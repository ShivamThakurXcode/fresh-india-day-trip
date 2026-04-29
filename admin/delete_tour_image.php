<?php
session_start();
require_once '../config.php';
checkAdminLogin();

header('Content-Type: application/json');

try {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Security validation failed');
    }

    $imagePath = $_POST['image_path'] ?? '';
    $tourId = $_POST['tour_id'] ?? null;

    if (empty($imagePath)) {
        throw new Exception('Image path is required');
    }

    // Security: Validate the image path to prevent directory traversal
    $imagePath = str_replace('..', '', $imagePath);
    $imagePath = ltrim($imagePath, '/\\');
    
    // Handle both old format (with directory prefix) and new format (just filename)
    if (strpos($imagePath, 'tours-image/') === 0 || strpos($imagePath, 'tours-image\\') === 0) {
        $fullPath = '../assets/img/' . $imagePath;
    } else {
        // New format: just the filename, need to add directory
        if (strpos($imagePath, '/') !== false || strpos($imagePath, '\\') !== false) {
            throw new Exception('Invalid image path');
        }
        $fullPath = '../assets/img/tours-image/' . $imagePath;
    }

    // Check if file exists
    if (!file_exists($fullPath)) {
        throw new Exception('Image file not found');
    }

    // Verify it's actually an image file
    $imageInfo = @getimagesize($fullPath);
    if ($imageInfo === false) {
        throw new Exception('File is not a valid image');
    }

    // Delete the file
    if (!unlink($fullPath)) {
        throw new Exception('Failed to delete image file');
    }

    // If tour_id is provided, update the tour's images array in database
    if ($tourId) {
        $stmt = $pdo->prepare("SELECT images FROM tours WHERE id = ?");
        $stmt->execute([$tourId]);
        $tour = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tour) {
            $images = json_decode($tour['images'], true) ?: [];
            
            // Normalize imagePath for comparison (handle both old and new formats)
            $imagePathNormalized = str_replace('tours-image/', '', $imagePath);
            $imagePathNormalized = str_replace('tours-image\\', '', $imagePathNormalized);
            
            // Remove the image path from the array (handle both formats)
            $images = array_filter($images, function($img) use ($imagePathNormalized) {
                $imgNormalized = str_replace('tours-image/', '', $img);
                $imgNormalized = str_replace('tours-image\\', '', $imgNormalized);
                return $imgNormalized !== $imagePathNormalized;
            });
            
            // Re-index array
            $images = array_values($images);
            
            // Update database
            $stmt = $pdo->prepare("UPDATE tours SET images = ? WHERE id = ?");
            $stmt->execute([json_encode($images), $tourId]);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Image deleted successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
