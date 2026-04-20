<?php
session_start();
require_once '../config.php';
checkAdminLogin();

 $message = '';

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // SECURITY: Verify CSRF token
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Security validation failed. Please try again.');
            }

            if (isset($_POST['action']) && $_POST['action'] == 'bulk_delete') {
                $ids = $_POST['ids'] ?? [];
                if (is_string($ids)) {
                    $ids = json_decode($ids, true);
                }

                if (!is_array($ids)) {
                    throw new Exception('Invalid IDs');
                }

                foreach ($ids as $id) {
                    $stmt = $pdo->prepare("SELECT filename FROM gallery_images WHERE id = ?");
                    $stmt->execute([$id]);
                    $image = $stmt->fetch();
                    if ($image) {
                        $file_path = "../assets/img/gallery/" . $image['filename'];
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                        $stmt = $pdo->prepare("DELETE FROM gallery_images WHERE id = ?");
                        $stmt->execute([$id]);
                    }
                }

                echo json_encode(['success' => true, 'message' => 'Images deleted successfully!']);
                exit;
            }

            $id = $_POST['id'] ?? null;
            $alt_text = trim($_POST['alt_text']);
            $filename = '';
            $cropped_image_data = $_POST['cropped_image'] ?? null;
            
            if (!empty($_FILES['image']['name']) || $cropped_image_data) {
                if ($cropped_image_data) {
                    // Handle cropped image data
                    // Clean the base64 data
                    $image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $cropped_image_data));
                    if ($image_data === false || empty($image_data)) {
                        throw new Exception('Failed to decode base64 image data. Data length: ' . strlen($cropped_image_data));
                    }
                    
                    $filename = time() . '_' . uniqid() . '.webp';
                    $target = "../assets/img/gallery/" . $filename;
                    
                    // Create directory if it doesn't exist
                    $gallery_dir = "../assets/img/gallery/";
                    if (!is_dir($gallery_dir)) {
                        if (!mkdir($gallery_dir, 0755, true)) {
                            throw new Exception('Failed to create gallery directory: ' . $gallery_dir);
                        }
                    }
                    
                    // Check if directory is writable
                    if (!is_writable($gallery_dir)) {
                        throw new Exception('Gallery directory is not writable: ' . $gallery_dir);
                    }
                    
                    // Create image from string data
                    $image = imagecreatefromstring($image_data);
                    if ($image === false) {
                        throw new Exception('Invalid image data. Could not create image resource. Data length: ' . strlen($image_data));
                    }
                    
                    // Save as WebP with initial quality
                    $quality = 80;
                    $success = imagewebp($image, $target, $quality);
                    imagedestroy($image);
                    
                    if (!$success) {
                        throw new Exception('Failed to save WebP image. Check file permissions and directory exists: ' . $target);
                    }
                    
                    // Verify file was created
                    if (!file_exists($target)) {
                        throw new Exception('WebP file was not created at: ' . $target);
                    }
                    
                    // Check file size and reduce quality if needed (max 100KB)
                    $file_size = filesize($target);
                    while ($file_size > 100 * 1024 && $quality > 30) {
                        $quality -= 10;
                        $image = imagecreatefromwebp($target);
                        if ($image !== false) {
                            imagewebp($image, $target, $quality);
                            imagedestroy($image);
                            $file_size = filesize($target);
                        }
                    }
                    
                    // If still too large, resize the image
                    if ($file_size > 100 * 1024) {
                        $image = imagecreatefromwebp($target);
                        if ($image !== false) {
                            $width = imagesx($image);
                            $height = imagesy($image);
                            
                            // Calculate new dimensions (reduce by 20% each iteration)
                            while ($file_size > 100 * 1024 && $width > 200 && $height > 200) {
                                $new_width = intval($width * 0.8);
                                $new_height = intval($height * 0.8);
                                
                                $new_image = imagecreatetruecolor($new_width, $new_height);
                                imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                                
                                imagewebp($new_image, $target, 70);
                                imagedestroy($new_image);
                                
                                $file_size = filesize($target);
                                $width = $new_width;
                                $height = $new_height;
                            }
                            imagedestroy($image);
                        }
                    }
                    
                } else {
                    // Handle regular file upload
                    if (!isset($_FILES['image'])) {
                        throw new Exception('File upload payload is missing.');
                    }

                    $validation = validateFileUpload($_FILES['image'], ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], 2 * 1024 * 1024);
                    if (!$validation['valid']) {
                        throw new Exception('Invalid image: ' . $validation['error']);
                    }
                    
                    // Check initial file size (should be less than 2MB for processing)
                    if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                        throw new Exception('File too large for processing. Please upload an image smaller than 2MB.');
                    }
                    
                    // Create directory if it doesn't exist
                    if (!is_dir("../assets/img/gallery/")) {
                        mkdir("../assets/img/gallery/", 0755, true);
                    }
                    
                    $filename = time() . '_' . uniqid() . '.webp';
                    $target = "../assets/img/gallery/" . $filename;
                    
                    // Load the image based on its type
                    $ext = $validation['extension'];
                    switch ($ext) {
                        case 'jpeg':
                        case 'jpg':
                            $image = imagecreatefromjpeg($_FILES['image']['tmp_name']);
                            break;
                        case 'png':
                            $image = imagecreatefrompng($_FILES['image']['tmp_name']);
                            break;
                        case 'gif':
                            $image = imagecreatefromgif($_FILES['image']['tmp_name']);
                            break;
                        case 'webp':
                            $image = imagecreatefromwebp($_FILES['image']['tmp_name']);
                            break;
                        default:
                            throw new Exception('Unsupported image format.');
                    }
                    
                    if ($image === false) {
                        throw new Exception('Failed to load image for conversion.');
                    }
                    
                    // Save as WebP with initial quality
                    $quality = 80;
                    $success = imagewebp($image, $target, $quality);
                    imagedestroy($image);
                    
                    if (!$success) {
                        throw new Exception('Failed to convert image to WebP.');
                    }
                    
                    // Check file size and reduce quality if needed (max 100KB)
                    $file_size = filesize($target);
                    while ($file_size > 100 * 1024 && $quality > 30) {
                        $quality -= 10;
                        $image = imagecreatefromwebp($target);
                        if ($image !== false) {
                            imagewebp($image, $target, $quality);
                            imagedestroy($image);
                            $file_size = filesize($target);
                        }
                    }
                    
                    // If still too large, resize the image
                    if ($file_size > 100 * 1024) {
                        $image = imagecreatefromwebp($target);
                        if ($image !== false) {
                            $width = imagesx($image);
                            $height = imagesy($image);
                            
                            // Calculate new dimensions (reduce by 20% each iteration)
                            while ($file_size > 100 * 1024 && $width > 200 && $height > 200) {
                                $new_width = intval($width * 0.8);
                                $new_height = intval($height * 0.8);
                                
                                $new_image = imagecreatetruecolor($new_width, $new_height);
                                imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                                
                                imagewebp($new_image, $target, 70);
                                imagedestroy($new_image);
                                
                                $file_size = filesize($target);
                                $width = $new_width;
                                $height = $new_height;
                            }
                            imagedestroy($image);
                        }
                    }
                    
                    // Final check
                    if (filesize($target) > 100 * 1024) {
                        unlink($target);
                        throw new Exception('Unable to compress image to under 100KB. Please try a smaller image.');
                    }
                }
            } else if (!$id) {
                throw new Exception('Image file is required for new images.');
            }
            
            if ($id) {
                $query = "UPDATE gallery_images SET alt_text = ?";
                $params = [$alt_text];
                if ($filename) {
                    // Get old filename to delete it
                    $stmt = $pdo->prepare("SELECT filename FROM gallery_images WHERE id = ?");
                    $stmt->execute([$id]);
                    $old_image = $stmt->fetch();
                    
                    if ($old_image && file_exists("../assets/img/gallery/" . $old_image['filename'])) {
                        unlink("../assets/img/gallery/" . $old_image['filename']);
                    }
                    
                    $query .= ", filename = ?";
                    $params[] = $filename;
                }
                $query .= " WHERE id = ?";
                $params[] = $id;
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $message = 'Image updated successfully!';
            } else {
                if (!$filename) {
                    throw new Exception('Image file is required');
                }
                $stmt = $pdo->prepare("INSERT INTO gallery_images (filename, alt_text) VALUES (?, ?)");
                $stmt->execute([$filename, $alt_text]);
                $message = 'Image added successfully!';
            }
            echo json_encode(['success' => true, 'message' => $message]);
        } catch (Exception $e) {
            error_log('Gallery upload error: ' . $e->getMessage());
            error_log('POST data: ' . print_r($_POST, true));
            error_log('FILES data: ' . print_r($_FILES, true));
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        if (!verifyCSRFToken($_GET['csrf_token'] ?? '')) {
            throw new Exception('Security validation failed. Please try again.');
        }

        $stmt = $pdo->prepare("SELECT filename FROM gallery_images WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $image = $stmt->fetch();
        if ($image) {
            $file_path = "../assets/img/gallery/" . $image['filename'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $stmt = $pdo->prepare("DELETE FROM gallery_images WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $message = 'Image deleted successfully!';
        } else {
            $message = 'Image not found!';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <style>
        .modal {
            z-index: 9999;
        }
        .modal-dialog {
            max-width: 80vw;
            height: 80vh;
            margin: auto auto;
            top: 5%;
        }
        .modal-content {
            height: 100%;
            overflow: hidden;
        }
        .modal-body {
            height: calc(100% - 120px);
            overflow-y: auto;
        }
        .input-group-text{
          height: 100%;
        }
        #dropZone {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            position: relative;
            background-color: #f8f9fa;
        }
        #dropZone:hover {
            border-color: #007bff;
            background-color: rgba(0, 123, 255, 0.05);
        }
        #dropZone.dragover {
            border-color: #007bff;
            background-color: rgba(0, 123, 255, 0.1);
        }
        #dropZone input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }
        #dropZone .dropzone-content {
            position: relative;
            z-index: 1;
            pointer-events: none; /* Allows clicks to pass through to the input */
        }
        #cropperContainer {
            margin-top: 10px;
        }
        #cropperImage {
            max-width: 100%;
            max-height: 400px;
        }
        .btn-group-crop {
            margin-top: 10px;
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        .btn-group-crop .btn {
            flex-grow: 1;
        }
        .current-image-container {
            margin-top: 10px;
            text-align: center;
        }
        .current-image-container img {
            max-width: 200px;
            max-height: 150px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .current-image-container p {
            margin-top: 5px;
            font-size: 0.9em;
            color: #666;
        }
        .file-size-info {
            font-size: 0.8em;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>
    <div class="main-content">
        <div class="content-wrapper">
            <div class="d-flex mb-3 justify-content-between align-items-center">
                <h1 class="page-title">Gallery Management</h1>
                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>
                <button type="button" class="btn btn-primary mb-3" onclick="openAddModal()" aria-label="Add new image">Add New Image</button>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" id="search" placeholder="Search images..." class="form-control">
                </div>
                <div class="col-md-6 text-right">
                    <button id="bulkDelete" class="btn btn-danger">Delete Selected</button>
                </div>
            </div>
            <table class="table table-striped">
                 <thead>
                     <tr>
                         <th><input type="checkbox" id="selectAll"></th>
                         <th>Image Preview</th>
                         <th>Alt Text</th>
                         <th>File Size</th>
                         <th>Actions</th>
                     </tr>
                 </thead>
                 <tbody>
                     <?php
                     $currentPage = max(1, (int)($_GET['page'] ?? 1));
                     $perPage = 24;
                     $totalItems = (int)$pdo->query("SELECT COUNT(*) FROM gallery_images")->fetchColumn();
                     $pagination = getPaginationInfo($currentPage, $totalItems, $perPage);

                     $stmt = $pdo->prepare("SELECT id, filename, alt_text FROM gallery_images ORDER BY created_at DESC LIMIT ? OFFSET ?");
                     $stmt->bindValue(1, (int)$pagination['per_page'], PDO::PARAM_INT);
                     $stmt->bindValue(2, (int)$pagination['offset'], PDO::PARAM_INT);
                     $stmt->execute();
                     $csrfToken = urlencode(generateCSRFToken());
                     while ($row = $stmt->fetch()) {
                         $file_path = "../assets/img/gallery/" . $row['filename'];
                         $file_size = file_exists($file_path) ? number_format(filesize($file_path) / 1024, 2) . ' KB' : 'N/A';
                         echo "<tr>
                             <td><input type='checkbox' class='bulk-check' data-id='{$row['id']}'></td>
                             <td><img src='../assets/img/gallery/{$row['filename']}' width='100' alt='{$row['alt_text']}'></td>
                             <td>" . ($row['alt_text'] ? htmlspecialchars($row['alt_text']) : '<span class="text-muted">No alt text</span>') . "</td>
                             <td>" . $file_size . "</td>
                             <td>
                                 <a href='#' onclick='openEditModal({$row['id']})' class='btn btn-sm btn-warning'>Edit</a>
                                 <a href='?action=delete&id={$row['id']}&csrf_token={$csrfToken}' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete this image?\")'>Delete</a>
                             </td>
                         </tr>";
                     }
                     ?>
                 </tbody>
             </table>
            <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
            <nav aria-label="Gallery pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo !$pagination['has_previous'] ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo max(1, $pagination['current_page'] - 1); ?>">Previous</a>
                    </li>
                    <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                        <li class="page-item <?php echo $p === (int)$pagination['current_page'] ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo !$pagination['has_next'] ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo min($pagination['total_pages'], $pagination['current_page'] + 1); ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="galleryModal" tabindex="-1" role="dialog" aria-labelledby="galleryModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="galleryModalLabel">Add New Image</h5>
            <button type="button" class="close" onclick="$('#galleryModal').modal('hide')" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
             <form id="galleryForm" method="POST" enctype="multipart/form-data">
               <input type="hidden" name="id" id="imageId">
               <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
               <div class="form-group">
                 <label>Image Alt Text</label>
                 <div class="input-group">
                   <div class="input-group-prepend">
                     <span class="input-group-text"><i class="fas fa-text"></i></span>
                   </div>
                   <input type="text" name="alt_text" class="form-control" placeholder="Enter alt text for accessibility">
                 </div>
               </div>
               <div class="form-group">
                 <label>Image *</label>
                 <div id="dropZone">
                     <input type="file" name="image" accept="image/*">
                     <div class="dropzone-content">
                         <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                         <p>Drag and drop image here or click to select</p>
                         <p class="text-muted small">Supported formats: JPEG, PNG, GIF, WebP (Max size: 2MB, will be compressed to 100KB WebP)</p>
                     </div>
                 </div>
                 <div id="currentImage" class="current-image-container"></div>
                 <div id="cropperContainer" style="display:none;">
                     <img id="cropperImage">
                     <div class="btn-group-crop">
                         <button type="button" class="btn btn-sm btn-secondary" onclick="setAspectRatio(1)">Square (1:1)</button>
                         <button type="button" class="btn btn-sm btn-secondary" onclick="setAspectRatio(16/9)">Banner (16:9)</button>
                         <button type="button" class="btn btn-sm btn-secondary" onclick="setAspectRatio(4/3)">Standard (4:3)</button>
                         <button type="button" class="btn btn-sm btn-secondary" onclick="setAspectRatio(NaN)">Free</button>
                         <button type="button" class="btn btn-sm btn-success" id="cropAndAddBtn">Crop & Add</button>
                         <button type="button" class="btn btn-sm btn-warning" id="resetCropBtn">Reset</button>
                     </div>
                     <div class="file-size-info" id="croppedSizeInfo"></div>
                 </div>
               </div>
             </form>
           </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="$('#galleryModal').modal('hide')">Cancel</button>
            <button type="submit" form="galleryForm" class="btn btn-success" id="saveImageBtn">Save Image</button>
          </div>
        </div>
      </div>
    </div>

    <script src="../assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script>
        var cropper;
        var currentFile;
        
        function openAddModal() {
            $('#galleryForm')[0].reset();
            $('#imageId').val('');
            $('#currentImage').html('');
            $('#cropperContainer').hide();
            if (cropper) cropper.destroy();
            $('#galleryModalLabel').text('Add New Image');
            $('#galleryModal').modal('show');
        }

        function openEditModal(id) {
            $.get('get_gallery.php?id=' + id, function(data) {
                var image = JSON.parse(data);
                $('#imageId').val(image.id);
                $('input[name="alt_text"]').val(image.alt_text || '');
                $('#currentImage').html('<img src="../assets/img/gallery/' + image.filename + '" alt="' + (image.alt_text || '') + '"><p>Current image (click "Crop & Add" to replace with new image)</p>');
                $('#cropperContainer').hide();
                if (cropper) cropper.destroy();
                $('#galleryModalLabel').text('Edit Image');
                $('#galleryModal').modal('show');
            });
        }

        $('#galleryForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            
            // Handle cropped image
            if (cropper && cropper.getCroppedCanvas()) {
                try {
                    var croppedCanvas = cropper.getCroppedCanvas({
                        width: 800,
                        height: 600,
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high'
                    });
                    
                    // Convert to WebP format with 80% quality
                    var croppedImageData = croppedCanvas.toDataURL('image/webp', 0.8);
                    formData.set('cropped_image', croppedImageData);
                    
                    // Remove the original file input if it exists
                    formData.delete('image');
                    
                    console.log('Cropped image data prepared:', croppedImageData.substring(0, 50) + '...');
                } catch (error) {
                    console.error('Error processing cropped image:', error);
                    alert('Error processing cropped image. Please try again.');
                    return;
                }
            } else if ($('input[name="image"]')[0].files.length > 0) {
                // Use original file if no cropping was done
                console.log('Using original file upload');
            } else if (!$('#imageId').val()) {
                // New image but no file selected
                alert('Please select an image to upload');
                return;
            }
            
            sendForm(formData);
        });
        
        function sendForm(formData) {
            // Show loading state
            var submitBtn = $('#saveImageBtn');
            var originalText = submitBtn.text();
            submitBtn.prop('disabled', true).text('Saving...');
            
            $.ajax({
                url: 'gallery.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response && response.success) {
                        alert(response.message);
                        $('#galleryModal').modal('hide');
                        location.reload();
                    } else {
                        console.error('Invalid response:', response);
                        alert('Server returned invalid response. Please check the console for details.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    
                    // Try to parse error response
                    try {
                        var errorResponse = JSON.parse(xhr.responseText);
                        alert('Error: ' + errorResponse.message);
                    } catch (e) {
                        alert('Error occurred while saving. Please check the console for details.');
                    }
                },
                complete: function() {
                    // Restore button state
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function estimateCroppedSize(canvas, quality = 0.8) {
            // Rough estimation of WebP file size
            const dataURL = canvas.toDataURL('image/webp', quality);
            const base64 = dataURL.replace(/^data:image\/webp;base64,/, '');
            const fileSize = base64.length * 0.75; // Base64 is roughly 33% larger than binary
            return fileSize;
        }

        $(document).ready(function() {
            // Direct change event on the file input
            $('input[name="image"]').on('change', function() {
                var file = this.files[0];
                if (file) {
                    // Check file size (2MB limit for processing)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File is too large for processing. Maximum size is 2MB. The image will be compressed to 100KB WebP format.');
                        $(this).val('');
                        return;
                    }
                    
                    currentFile = file;
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#cropperImage').attr('src', e.target.result);
                        $('#cropperContainer').show();
                        if (cropper) cropper.destroy();
                        
                        // Initialize cropper with proper settings
                        cropper = new Cropper(document.getElementById('cropperImage'), {
                            aspectRatio: 1,
                            viewMode: 1,
                            responsive: true,
                            restore: false,
                            checkCrossOrigin: false,
                            checkOrientation: false,
                            modal: true,
                            guides: true,
                            center: true,
                            highlight: false,
                            background: false,
                            autoCrop: true,
                            autoCropArea: 0.8,
                            movable: true,
                            rotatable: true,
                            scalable: true,
                            zoomable: true,
                            zoomOnTouch: true,
                            zoomOnWheel: true,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            minCropBoxWidth: 100,
                            minCropBoxHeight: 100,
                            ready: function() {
                                // Cropper is ready
                                console.log('Cropper initialized successfully');
                            },
                            crop: function(event) {
                                // Update estimated file size when crop changes
                                var canvas = cropper.getCroppedCanvas({
                                    width: 800,
                                    height: 600,
                                    imageSmoothingEnabled: true,
                                    imageSmoothingQuality: 'high'
                                });
                                if (canvas) {
                                    var estimatedSize = estimateCroppedSize(canvas, 0.8);
                                    $('#croppedSizeInfo').text('Estimated size: ' + formatFileSize(estimatedSize) + ' (will be compressed to max 100KB)');
                                }
                            }
                        });
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#cropperContainer').hide();
                    if (cropper) cropper.destroy();
                }
            });

            // Crop and Add button functionality
            $('#cropAndAddBtn').click(function() {
                if (cropper) {
                    try {
                        var croppedCanvas = cropper.getCroppedCanvas({
                            width: 800,
                            height: 600,
                            imageSmoothingEnabled: true,
                            imageSmoothingQuality: 'high'
                        });
                        
                        // Convert to WebP format with 80% quality
                        var croppedImageData = croppedCanvas.toDataURL('image/webp', 0.8);
                        
                        // Display cropped image
                        $('#currentImage').html('<img src="' + croppedImageData + '" alt="Cropped image"><p>Cropped image (will be saved as WebP, max 100KB)</p>');
                        
                        // Hide cropper
                        $('#cropperContainer').hide();
                        
                        // Create a blob from the cropped image and set it as the file input
                        croppedCanvas.toBlob(function(blob) {
                            var file = new File([blob], currentFile.name.replace(/\.[^/.]+$/, ".webp"), {type: "image/webp"});
                            var dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            document.querySelector('input[name="image"]').files = dataTransfer.files;
                        }, 'image/webp', 0.8);
                        
                    } catch (error) {
                        console.error('Error processing cropped image:', error);
                        alert('Error processing cropped image. Please try again.');
                    }
                }
            });

            // Reset crop button functionality
            $('#resetCropBtn').click(function() {
                if (cropper) {
                    cropper.reset();
                }
            });

            // Drag and drop functionality
            $('#dropZone').on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            });

            $('#dropZone').on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
            });

            $('#dropZone').on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
                var files = e.originalEvent.dataTransfer.files;
                if (files.length) {
                    // Check file size (2MB limit for processing)
                    if (files[0].size > 2 * 1024 * 1024) {
                        alert('File is too large for processing. Maximum size is 2MB. The image will be compressed to 100KB WebP format.');
                        return;
                    }
                    
                    $('input[name="image"]')[0].files = files;
                    $('input[name="image"]').trigger('change');
                }
            });

            // No need for click handler since the input covers the entire drop zone

            $('#search').on('input', function() {
                var query = $(this).val().toLowerCase();
                $('tbody tr').each(function() {
                    var text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(query));
                });
            });

            $('#selectAll').change(function() {
                $('.bulk-check').prop('checked', this.checked);
            });

            $('#bulkDelete').click(function() {
                var ids = [];
                $('.bulk-check:checked').each(function() {
                    ids.push($(this).data('id'));
                });
                if (ids.length && confirm('Delete selected images?')) {
                    // Use AJAX to delete multiple images
                    $.ajax({
                        url: 'gallery.php',
                        type: 'POST',
                        data: {
                            action: 'bulk_delete',
                            ids: JSON.stringify(ids),
                            csrf_token: $('input[name="csrf_token"]').first().val()
                        },
                        success: function(response) {
                            if (response && response.success) {
                                alert(response.message);
                                location.reload();
                            } else {
                                console.error('Invalid response:', response);
                                alert('Server returned invalid response.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', {
                                status: status,
                                error: error,
                                response: xhr.responseText
                            });
                            alert('Error occurred while deleting images.');
                        }
                    });
                }
            });
        });

        function setAspectRatio(ratio) {
            if (cropper) {
                cropper.setAspectRatio(ratio);
            }
        }
    </script>
</body>
</html>