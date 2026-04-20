<?php
session_start();
require_once '../config.php';
checkAdminLogin();

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->execute([$id]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($blog);
?>