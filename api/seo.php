<?php
// API to get/save SEO per instance_id
require_once __DIR__ . '/../src/Database.php';
$pdo = Database::getConnection();

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    $instance_id = isset($_GET['instance_id']) ? (int)$_GET['instance_id'] : 1;
    $stmt = $pdo->prepare('SELECT * FROM app_seo WHERE instance_id = ? ORDER BY updated_at DESC, seo_id DESC LIMIT 1');
    $stmt->execute([$instance_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && $row['meta_keywords']) {
        // try decode
        $decoded = json_decode($row['meta_keywords'], true);
        if (json_last_error() === JSON_ERROR_NONE) $row['meta_keywords'] = $decoded;
    }
    echo json_encode(['success' => true, 'data' => $row]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
        exit;
    }
    $instance_id = isset($input['instance_id']) ? (int)$input['instance_id'] : 1;
    $meta_title = isset($input['meta_title']) ? substr(trim($input['meta_title']), 0, 255) : null;
    $meta_description = isset($input['meta_description']) ? trim($input['meta_description']) : null;
    $meta_keywords = $input['meta_keywords'] ?? null; // can be array or comma string
    $logo_image_url = isset($input['logo_image_url']) ? trim($input['logo_image_url']) : null;
    $og_image_url = isset($input['og_image_url']) ? trim($input['og_image_url']) : null;

    // normalize keywords into JSON
    if (is_string($meta_keywords)) {
        // split by comma
        $parts = array_filter(array_map('trim', explode(',', $meta_keywords)));
        $meta_keywords_arr = array_values($parts);
    } elseif (is_array($meta_keywords)) {
        $meta_keywords_arr = array_values(array_filter(array_map('trim', $meta_keywords)));
    } else {
        $meta_keywords_arr = null;
    }

    $meta_keywords_json = $meta_keywords_arr ? json_encode($meta_keywords_arr, JSON_UNESCAPED_UNICODE) : null;

    // upsert
    $stmt = $pdo->prepare('SELECT seo_id FROM app_seo WHERE instance_id = ? LIMIT 1');
    $stmt->execute([$instance_id]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        $update = $pdo->prepare('UPDATE app_seo SET meta_title = ?, meta_description = ?, meta_keywords = ?, logo_image_url = ?, og_image_url = ?, updated_at = CURRENT_TIMESTAMP() WHERE instance_id = ?');
        $update->execute([$meta_title, $meta_description, $meta_keywords_json, $logo_image_url, $og_image_url, $instance_id]);
    } else {
        $insert = $pdo->prepare('INSERT INTO app_seo (instance_id, meta_title, meta_description, meta_keywords, logo_image_url, og_image_url) VALUES (?, ?, ?, ?, ?, ?)');
        $insert->execute([$instance_id, $meta_title, $meta_description, $meta_keywords_json, $logo_image_url, $og_image_url]);
    }

    echo json_encode(['success' => true, 'message' => 'SEO saved', 'instance_id' => $instance_id]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
