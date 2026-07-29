<?php
require_once __DIR__ . '/../src/Database.php';
$pdo = Database::getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $instance_id = isset($_GET['instance_id']) ? (int)$_GET['instance_id'] : 1;
    $stmt = $pdo->prepare('SELECT * FROM app_contact_link WHERE instance_id = ? ORDER BY display_order ASC, created_at ASC');
    $stmt->execute([$instance_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $rows]);
    exit;
}

// For POST and DELETE expect JSON payload
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

if ($method === 'POST') {
    // create or update
    $instance_id = isset($input['instance_id']) ? (int)$input['instance_id'] : 1;
    $id = isset($input['contact_link_id']) ? (int)$input['contact_link_id'] : null;
    $name = isset($input['name']) ? trim($input['name']) : '';
    $address = isset($input['address']) ? trim($input['address']) : null;
    $type = isset($input['type']) ? trim($input['type']) : 'web';
    $address_value = isset($input['address_value']) ? trim($input['address_value']) : '';
    $is_active = isset($input['is_active']) ? (int)(bool)$input['is_active'] : 0;

    // basic validation
    if ($name === '' || $address_value === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Name and Value are required']);
        exit;
    }
    $allowed = ['email','web','phone','social'];
    if (!in_array($type, $allowed)) $type = 'web';

    if ($id) {
        // update
        $stmt = $pdo->prepare('UPDATE app_contact_link SET name = ?, address = ?, type = ?, address_value = ?, is_active = ? WHERE contact_link_id = ? AND instance_id = ?');
        $stmt->execute([$name, $address, $type, $address_value, $is_active, $id, $instance_id]);
        echo json_encode(['success' => true, 'message' => 'Updated']);
        exit;
    } else {
        // determine max display_order
        $stmt = $pdo->prepare('SELECT COALESCE(MAX(display_order), -1) + 1 FROM app_contact_link WHERE instance_id = ?');
        $stmt->execute([$instance_id]);
        $nextOrder = (int)$stmt->fetchColumn();
        $insert = $pdo->prepare('INSERT INTO app_contact_link (instance_id, name, address, type, address_value, is_active, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $insert->execute([$instance_id, $name, $address, $type, $address_value, $is_active, $nextOrder]);
        $newId = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'message' => 'Created', 'contact_link_id' => (int)$newId]);
        exit;
    }
}

if ($method === 'DELETE') {
    $id = isset($input['contact_link_id']) ? (int)$input['contact_link_id'] : null;
    $instance_id = isset($input['instance_id']) ? (int)$input['instance_id'] : 1;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing id']);
        exit;
    }
    $del = $pdo->prepare('DELETE FROM app_contact_link WHERE contact_link_id = ? AND instance_id = ?');
    $del->execute([$id, $instance_id]);
    echo json_encode(['success' => true, 'message' => 'Deleted']);
    exit;
}

// Reorder action: POST with action=reorder in query and JSON { instance_id, order: [id1,id2,...] }
if ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'reorder') {
    if (!isset($input['instance_id']) || !isset($input['order']) || !is_array($input['order'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid payload']);
        exit;
    }
    $instance_id = (int)$input['instance_id'];
    $order = $input['order'];
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('UPDATE app_contact_link SET display_order = ? WHERE contact_link_id = ? AND instance_id = ?');
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
