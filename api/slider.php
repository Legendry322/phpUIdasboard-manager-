<?php
require_once __DIR__ . '/../src/Database.php';
$pdo = Database::getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $instance_id = isset($_GET['instance_id']) ? (int)$_GET['instance_id'] : 1;
    $stmt = $pdo->prepare('SELECT * FROM app_slider WHERE instance_id = ? ORDER BY display_order ASC, created_at ASC');
    $stmt->execute([$instance_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $rows]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

if ($method === 'POST') {
    $instance_id = isset($input['instance_id']) ? (int)$input['instance_id'] : 1;
    $id = isset($input['slider_id']) ? (int)$input['slider_id'] : null;
    $short_text = isset($input['short_text']) ? trim($input['short_text']) : null;
    $medium_text = isset($input['medium_text']) ? trim($input['medium_text']) : null;
    $link_url = isset($input['link_url']) ? trim($input['link_url']) : null;
    $img_url = isset($input['img_url']) ? trim($input['img_url']) : null;
    $is_active = isset($input['is_active']) ? (int)(bool)$input['is_active'] : 0;

    if ($id) {
        $stmt = $pdo->prepare('UPDATE app_slider SET short_text = ?, medium_text = ?, img_url = ?, link_url = ?, is_active = ? WHERE slider_id = ? AND instance_id = ?');
        $stmt->execute([$short_text, $medium_text, $img_url, $link_url, $is_active, $id, $instance_id]);
        echo json_encode(['success' => true, 'message' => 'Updated']);
        exit;
    } else {
        $stmt = $pdo->prepare('SELECT COALESCE(MAX(display_order), -1) + 1 FROM app_slider WHERE instance_id = ?');
        $stmt->execute([$instance_id]);
        $next = (int)$stmt->fetchColumn();
        $insert = $pdo->prepare('INSERT INTO app_slider (instance_id, short_text, medium_text, img_url, link_url, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $insert->execute([$instance_id, $short_text, $medium_text, $img_url, $link_url, $next, $is_active]);
        $newId = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'message' => 'Created', 'slider_id' => (int)$newId]);
        exit;
    }
}

if ($method === 'DELETE') {
    $id = isset($input['slider_id']) ? (int)$input['slider_id'] : null;
    $instance_id = isset($input['instance_id']) ? (int)$input['instance_id'] : 1;
    if (!$id) { http_response_code(400); echo json_encode(['success' => false, 'message' => 'Missing id']); exit; }
    // optionally unlink image
    $stmt = $pdo->prepare('SELECT img_url FROM app_slider WHERE slider_id = ? AND instance_id = ? LIMIT 1');
    $stmt->execute([$id, $instance_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && !empty($row['img_url'])) {
        $path = __DIR__ . '/../appimg/' . $row['img_url'];
        if (file_exists($path)) @unlink($path);
    }
    $del = $pdo->prepare('DELETE FROM app_slider WHERE slider_id = ? AND instance_id = ?');
    $del->execute([$id, $instance_id]);
    echo json_encode(['success' => true, 'message' => 'Deleted']);
    exit;
}

// Reorder
if ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'reorder') {
    if (!isset($input['instance_id']) || !isset($input['order']) || !is_array($input['order'])) { http_response_code(400); echo json_encode(['success' => false, 'message' => 'Invalid payload']); exit; }
    $instance_id = (int)$input['instance_id'];
    $order = $input['order'];
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('UPDATE app_slider SET display_order = ? WHERE slider_id = ? AND instance_id = ?');
        foreach ($order as $idx => $id) {
            $stmt->execute([(int)$idx, (int)$id, $instance_id]);
        }
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Reordered']);
        exit;
    } catch (Throwable $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Reorder failed']);
        exit;
    }
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
