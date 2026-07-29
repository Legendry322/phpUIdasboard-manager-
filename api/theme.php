<?php
// API to get/save theme per instance_id
require_once __DIR__ . '/../src/Database.php';
$pdo = Database::getConnection();

// Allow both GET (fetch) and POST (save)
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $instance_id = isset($_GET['instance_id']) ? (int)$_GET['instance_id'] : 1;
    $stmt = $pdo->prepare('SELECT * FROM app_theme WHERE instance_id = ? ORDER BY updated_at DESC, theme_id DESC LIMIT 1');
    $stmt->execute([$instance_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $row]);
    exit;
}

if ($method === 'POST') {
    // Expect JSON
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
        exit;
    }

    $instance_id = isset($input['instance_id']) ? (int)$input['instance_id'] : 1;
    $allowedFonts = ['Arial','Roboto','Open Sans','Lato','Poppins','Montserrat','Inter'];

    $background_color = preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $input['background_color'] ?? '') ? $input['background_color'] : '#ffffff';
    $box_color = preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $input['box_color'] ?? '') ? $input['box_color'] : '#f0f0f0';
    $header_color = preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $input['header_color'] ?? '') ? $input['header_color'] : '#333333';
    $footer_color = preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $input['footer_color'] ?? '') ? $input['footer_color'] : '#222222';
    $site_color = preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $input['site_color'] ?? '') ? $input['site_color'] : '#007bff';
    $hover_text_color = preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $input['hover_text_color'] ?? '') ? $input['hover_text_color'] : '#ffffff';
    $side_banner_color = preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $input['side_banner_color'] ?? '') ? $input['side_banner_color'] : '#e0e0e0';
    $font_family = in_array($input['font_family'] ?? '', $allowedFonts) ? $input['font_family'] : 'Arial';

    // Upsert: check existing
    $stmt = $pdo->prepare('SELECT theme_id FROM app_theme WHERE instance_id = ? LIMIT 1');
    $stmt->execute([$instance_id]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        $update = $pdo->prepare('UPDATE app_theme SET background_color = ?, box_color = ?, header_color = ?, footer_color = ?, site_color = ?, hover_text_color = ?, side_banner_color = ?, font_family = ?, updated_at = CURRENT_TIMESTAMP() WHERE instance_id = ?');
        $update->execute([$background_color,$box_color,$header_color,$footer_color,$site_color,$hover_text_color,$side_banner_color,$font_family,$instance_id]);
    } else {
        $insert = $pdo->prepare('INSERT INTO app_theme (instance_id, background_color, box_color, header_color, footer_color, site_color, hover_text_color, side_banner_color, font_family) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $insert->execute([$instance_id,$background_color,$box_color,$header_color,$footer_color,$site_color,$hover_text_color,$side_banner_color,$font_family]);
    }

    echo json_encode(['success' => true, 'message' => 'Theme saved', 'instance_id' => $instance_id]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
