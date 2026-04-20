<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

session_start();
require_once '../config.php';

const GALLERY_API_WRITE_WINDOW = 60;
const GALLERY_API_WRITE_MAX = 30;

function apiError($message, $status = 400) {
    http_response_code($status);
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
    exit;
}

function requireAdminApiAuth() {
    if (!isset($_SESSION['admin_id'])) {
        apiError('Unauthorized. Please login to continue.', 401);
    }
}

function requireWriteCsrfToken() {
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
    if (!verifyCSRFToken($token)) {
        apiError('Invalid CSRF token.', 403);
    }
}

function checkGalleryWriteRateLimit() {
    $rateFile = '../rate_limit.json';
    $now = time();
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $adminId = $_SESSION['admin_id'] ?? 'guest';
    $key = 'gallery_api_write|' . $adminId . '|' . $clientIp;

    $data = [];
    if (file_exists($rateFile)) {
        $json = @file_get_contents($rateFile);
        if ($json) {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }
    }

    foreach ($data as $k => $entry) {
        if (!isset($entry['time']) || $entry['time'] < ($now - GALLERY_API_WRITE_WINDOW)) {
            unset($data[$k]);
        }
    }

    if (!isset($data[$key])) {
        $data[$key] = ['count' => 0, 'time' => $now];
    }

    if ($data[$key]['count'] >= GALLERY_API_WRITE_MAX) {
        @file_put_contents($rateFile, json_encode($data));
        apiError('Too many write requests. Please wait and try again.', 429);
    }

    $data[$key]['count']++;
    $data[$key]['time'] = $now;
    @file_put_contents($rateFile, json_encode($data));
}

function saveWebpFromBase64($croppedImageData) {
    if (strlen($croppedImageData) > (6 * 1024 * 1024)) {
        throw new Exception('Cropped image payload too large.');
    }

    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImageData));
    if ($imageData === false || empty($imageData)) {
        throw new Exception('Invalid image data.');
    }

    $targetDir = '../assets/img/gallery/';
    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
        throw new Exception('Failed to create image directory.');
    }

    $filename = time() . '_' . uniqid() . '.webp';
    $target = $targetDir . $filename;

    $image = imagecreatefromstring($imageData);
    if ($image === false) {
        throw new Exception('Invalid image data.');
    }

    $quality = 80;
    $saved = imagewebp($image, $target, $quality);
    imagedestroy($image);
    if (!$saved) {
        throw new Exception('Failed to save image.');
    }

    $fileSize = filesize($target);
    while ($fileSize > 100 * 1024 && $quality > 30) {
        $quality -= 10;
        $image = imagecreatefromwebp($target);
        if ($image === false) {
            break;
        }
        imagewebp($image, $target, $quality);
        imagedestroy($image);
        $fileSize = filesize($target);
    }

    return $filename;
}

function saveUploadedGalleryImage($file) {
    $validation = validateFileUpload($file, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], 2 * 1024 * 1024);
    if (!$validation['valid']) {
        throw new Exception('Invalid file: ' . $validation['error']);
    }

    $targetDir = '../assets/img/gallery/';
    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
        throw new Exception('Failed to create image directory.');
    }

    $filename = time() . '_' . uniqid() . '.' . $validation['extension'];
    $target = $targetDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new Exception('Failed to upload image.');
    }

    return $filename;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$allowedMethods = ['GET', 'POST', 'DELETE', 'OPTIONS'];
if (!in_array($method, $allowedMethods, true)) {
    apiError('Method not allowed.', 405);
}

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    if ($method === 'GET') {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = (int)($_GET['per_page'] ?? 50);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $countStmt = $pdo->query('SELECT COUNT(*) FROM gallery_images');
        $total = (int)$countStmt->fetchColumn();

        $stmt = $pdo->prepare('SELECT * FROM gallery_images ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($images as &$image) {
            $image['url'] = '../assets/img/gallery/' . $image['filename'];
            $image['thumbnail'] = '../assets/img/gallery/' . $image['filename'];
        }

        echo json_encode([
            'success' => true,
            'data' => $images,
            'count' => count($images),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage
        ]);
        exit;
    }

    requireAdminApiAuth();
    requireWriteCsrfToken();
    checkGalleryWriteRateLimit();

    if ($method === 'POST') {
        $altText = trim($_POST['alt_text'] ?? '');
        $filename = '';

        if (!empty($_POST['cropped_image'])) {
            $filename = saveWebpFromBase64($_POST['cropped_image']);
        } elseif (!empty($_FILES['image']['name'])) {
            $filename = saveUploadedGalleryImage($_FILES['image']);
        }

        if ($filename === '') {
            throw new Exception('Image file is required.');
        }

        $stmt = $pdo->prepare('INSERT INTO gallery_images (filename, alt_text) VALUES (?, ?)');
        $stmt->execute([$filename, $altText]);

        echo json_encode([
            'success' => true,
            'message' => 'Image added successfully!',
            'data' => [
                'id' => (int)$pdo->lastInsertId(),
                'filename' => $filename,
                'alt_text' => $altText,
                'url' => '../assets/img/gallery/' . $filename,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
        exit;
    }

    // DELETE supports either id in query or form body for easier admin calls.
    $id = (int)($_POST['id'] ?? ($_GET['id'] ?? 0));
    if ($id <= 0) {
        throw new Exception('Image ID is required.');
    }

    $stmt = $pdo->prepare('SELECT filename FROM gallery_images WHERE id = ?');
    $stmt->execute([$id]);
    $image = $stmt->fetch();
    if (!$image) {
        throw new Exception('Image not found.');
    }

    $filePath = '../assets/img/gallery/' . $image['filename'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    $stmt = $pdo->prepare('DELETE FROM gallery_images WHERE id = ?');
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Image deleted successfully!'
    ]);
} catch (Exception $e) {
    apiError('Error: ' . $e->getMessage(), 400);
}
?>