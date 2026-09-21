<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/functions.php';

function checkApiAuth(): bool {
    $apiKey = defined('API_KEY') ? API_KEY : 'linearlearn_secret_key_2026';
    $headers = getallheaders();
    $providedKey = $headers['X-API-Key'] ?? ($headers['x-api-key'] ?? null);

    if ($providedKey && hash_equals($apiKey, $providedKey)) return true;

    $authHeader = $headers['Authorization'] ?? ($headers['authorization'] ?? '');
    if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        if (hash_equals($apiKey, trim($matches[1]))) return true;
    }

    $user = $_SERVER['PHP_AUTH_USER'] ?? null;
    $pass = $_SERVER['PHP_AUTH_PW'] ?? null;
    if ($user && $pass && loginAdmin($user, $pass)) return true;

    if (!empty($_GET['api_key']) && hash_equals($apiKey, $_GET['api_key'])) return true;
    if (isLoggedIn()) return true;

    return false;
}

if (!checkApiAuth()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$file = $_FILES['image'] ?? ($_FILES['file'] ?? null);

if (!$file || empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No image file uploaded or upload error']);
    exit;
}

$allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $allowedMimes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid file type. Allowed: jpg, png, webp, gif, svg']);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$originalName = pathinfo($file['name'], PATHINFO_FILENAME);
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) ?: 'jpg';
$cleanName = slugify($originalName) ?: 'image';
$filename = $cleanName . '-' . time() . '-' . mt_rand(100, 999) . '.' . $ext;
$targetPath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save file']);
    exit;
}

$relativeUrl = 'uploads/' . $filename;
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8080');

echo json_encode([
    'success'  => true,
    'url'      => $relativeUrl,
    'full_url' => $baseUrl . '/' . $relativeUrl,
    'filename' => $filename,
    'size'     => filesize($targetPath)
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
