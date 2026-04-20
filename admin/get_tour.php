<?php
session_start();
require_once '../config.php';
checkAdminLogin();

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ?");
$stmt->execute([$id]);
$tour = $stmt->fetch(PDO::FETCH_ASSOC);

if ($tour) {
    echo json_encode(['success' => true, 'data' => $tour]);
} else {
    echo json_encode(['success' => false, 'message' => 'Tour not found']);
}
?>