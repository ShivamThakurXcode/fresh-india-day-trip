<?php
$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: ../blog/index.php');
    exit;
}

header('Location: ../blog-detail.php?slug=' . urlencode($slug), true, 301);
exit;
