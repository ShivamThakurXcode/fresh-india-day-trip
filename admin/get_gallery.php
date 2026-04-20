<?php
session_start();
require_once '../config.php';
checkAdminLogin();

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT id, filename, alt_text FROM gallery_images WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $image = $stmt->fetch();
    if ($image) {
        echo json_encode($image);
    } else {
        echo json_encode(['error' => 'Image not found']);
    }
}
?>