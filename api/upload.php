<?php
// Simple upload handler for images
require_once __DIR__ . '/../src/Database.php';
$pdo = Database::getConnection();

$maxSize = 2 * 1024 * 1024; // 2MB
$allowed = ['image/jpeg','image/png','image/webp'];
$uploadDir = __DIR__ . '/../appimg/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (empty($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$file = $_FILES['file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Upload error']);
    exit;
}

if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File too large']);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
if (!in_array($mime, $allowed)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit;
}

$ext = '';
switch ($mime) {
    case 'image/jpeg': $ext = 'jpg'; break;
    case 'image/png': $ext = 'png'; break;
    case 'image/webp': $ext = 'webp'; break;
}

$prefix = isset($_POST['prefix']) ? preg_replace('/[^a-z0-9_\-]/i','',$_POST['prefix']) : 'img';
$timestamp = time();
$filename = sprintf('%s_%s.%s', $prefix, $timestamp, $ext);
$dest = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to move file']);
    exit;
}

// Return path relative to /appimg/
echo json_encode(['success' => true, 'url' => $filename]);
