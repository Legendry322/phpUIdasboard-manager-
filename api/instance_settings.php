<?php
// Combined instance settings endpoint
// Returns theme, seo, sliders, contact_links for a given instance_id
require_once __DIR__ . '/../src/Database.php';
$pdo = Database::getConnection();

header('Content-Type: application/json; charset=utf-8');

$instance_id = isset($_GET['instance_id']) ? (int)$_GET['instance_id'] : null;
if (!$instance_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing instance_id']);
    exit;
}

try {
    // Theme
    $stmt = $pdo->prepare('SELECT * FROM app_theme WHERE instance_id = ? ORDER BY updated_at DESC, theme_id DESC LIMIT 1');
    $stmt->execute([$instance_id]);
    $theme = $stmt->fetch(PDO::FETCH_ASSOC);

    // SEO
    $stmt = $pdo->prepare('SELECT * FROM app_seo WHERE instance_id = ? ORDER BY updated_at DESC, seo_id DESC LIMIT 1');
    $stmt->execute([$instance_id]);
    $seo = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($seo && !empty($seo['meta_keywords'])) {
        $decoded = json_decode($seo['meta_keywords'], true);
        if (json_last_error() === JSON_ERROR_NONE) $seo['meta_keywords'] = $decoded;
    }

    // Sliders (active)
    $stmt = $pdo->prepare('SELECT slider_id, short_text, medium_text, img_url, link_url, display_order, is_active FROM app_slider WHERE instance_id = ? AND is_active = 1 ORDER BY display_order ASC, created_at ASC');
    $stmt->execute([$instance_id]);
    $sliders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Contact links (active)
    $stmt = $pdo->prepare('SELECT contact_link_id, name, address, type, address_value, display_order FROM app_contact_link WHERE instance_id = ? AND is_active = 1 ORDER BY display_order ASC, created_at ASC');
    $stmt->execute([$instance_id]);
    $contact_links = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [
        'theme' => $theme,
        'seo' => $seo,
        'sliders' => $sliders,
        'contact_links' => $contact_links,
    ];

    echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    error_log('instance_settings error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
