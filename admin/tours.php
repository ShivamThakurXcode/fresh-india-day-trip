<?php
ob_start();
session_start();
require_once '../config.php';
checkAdminLogin();

 $message = '';
 
 if (isset($_SESSION['message'])) {
     $message = $_SESSION['message'];
     unset($_SESSION['message']);
 }
 
 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // SECURITY: Verify CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            throw new Exception('Security validation failed. Please try again.');
        }

        $id = $_POST['id'] ?? null;
        $title = trim($_POST['title']);
        $slug = trim($_POST['slug']);
        $description = trim($_POST['description']);
        $highlights = json_decode($_POST['highlights'] ?? '[]', true) ?: [];
        $included = json_decode($_POST['included'] ?? '[]', true) ?: [];
        $excluded = json_decode($_POST['excluded'] ?? '[]', true) ?: [];
        $itinerary = json_decode($_POST['itinerary'] ?? '[]', true) ?: [];
        $faq = json_decode($_POST['faq'] ?? '[]', true) ?: [];
        $pricing = $_POST['pricing'] ?: null;
        $duration = trim($_POST['duration']);
        $availability = (int)$_POST['availability'];
        $category_id = $_POST['category_id'] ?: null;
        $location = trim($_POST['location']);
        $meta_title = trim($_POST['meta_title'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');
        $meta_keywords = trim($_POST['meta_keywords'] ?? '');
        $schema_markup = trim($_POST['schemas'] ?? ($_POST['schema_markup'] ?? '[]'));

        // Generate slug if empty or auto-generate
        if (empty($slug)) {
            $slug = generateSlug($title, 'tours', $id);
        }

        $images = [];
        if (!empty($_POST['current_images'])) {
            $images = json_decode($_POST['current_images'], true) ?: [];
        }
        if (!empty($_FILES['images']['name'][0])) {
            $upload_dir = "../assets/img/tours-image/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                // SECURITY: Validate file upload thoroughly
                $file_array = [
                    'name' => $_FILES['images']['name'][$key],
                    'type' => $_FILES['images']['type'][$key],
                    'tmp_name' => $tmp_name,
                    'error' => $_FILES['images']['error'][$key],
                    'size' => $_FILES['images']['size'][$key]
                ];
                
                $validation = validateFileUpload($file_array, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], 5 * 1024 * 1024);
                if (!$validation['valid']) {
                    error_log('File validation failed: ' . $validation['error']);
                    continue;
                }
                
                $sanitized_name = sanitizeFilename($_FILES['images']['name'][$key]);
                $filename = time() . '_' . uniqid() . '.' . $validation['extension'];
                $target = $upload_dir . $filename;
                
                if (move_uploaded_file($tmp_name, $target)) {
                    $images[] = "tours-image/" . $filename;
                } else {
                    error_log("Failed to move uploaded file: " . $filename . " to " . $target);
                }
            }
        }
        $images_json = json_encode($images);

        $hasMetaTitle = (bool)$pdo->query("SHOW COLUMNS FROM tours LIKE 'meta_title'")->fetch();
        $hasMetaDescription = (bool)$pdo->query("SHOW COLUMNS FROM tours LIKE 'meta_description'")->fetch();
        $hasMetaKeywords = (bool)$pdo->query("SHOW COLUMNS FROM tours LIKE 'meta_keywords'")->fetch();
        $hasSchemaMarkup = (bool)$pdo->query("SHOW COLUMNS FROM tours LIKE 'schema_markup'")->fetch();
        $hasSchemas = (bool)$pdo->query("SHOW COLUMNS FROM tours LIKE 'schemas'")->fetch();
        $hasSchemaLegacy = (bool)$pdo->query("SHOW COLUMNS FROM tours LIKE 'schema'")->fetch();

        if ($id) {
            $query = "UPDATE tours SET title = ?, slug = ?, description = ?, highlights = ?, included = ?, excluded = ?, itinerary = ?, faq = ?, pricing = ?, duration = ?, availability = ?, category_id = ?, location = ?, images = ?";
            $params = [$title, $slug, $description, json_encode($highlights), json_encode($included), json_encode($excluded), json_encode($itinerary), json_encode($faq), $pricing, $duration, $availability, $category_id, $location, $images_json];

            if ($hasMetaTitle) {
                $query .= ", meta_title = ?";
                $params[] = $meta_title;
            }
            if ($hasMetaDescription) {
                $query .= ", meta_description = ?";
                $params[] = $meta_description;
            }
            if ($hasMetaKeywords) {
                $query .= ", meta_keywords = ?";
                $params[] = $meta_keywords;
            }
            if ($hasSchemaMarkup) {
                $query .= ", schema_markup = ?";
                $params[] = $schema_markup;
            }
            if ($hasSchemas) {
                $query .= ", `schemas` = ?";
                $params[] = $schema_markup;
            }
            if ($hasSchemaLegacy) {
                $query .= ", `schema` = ?";
                $params[] = $schema_markup;
            }

            $query .= " WHERE id = ?";
            $params[] = $id;

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $_SESSION['message'] = 'Tour updated successfully!';
        } else {
            $columns = ['title', 'slug', 'description', 'highlights', 'included', 'excluded', 'itinerary', 'faq', 'pricing', 'duration', 'availability', 'category_id', 'location', 'images'];
            $params = [$title, $slug, $description, json_encode($highlights), json_encode($included), json_encode($excluded), json_encode($itinerary), json_encode($faq), $pricing, $duration, $availability, $category_id, $location, $images_json];

            if ($hasMetaTitle) {
                $columns[] = 'meta_title';
                $params[] = $meta_title;
            }
            if ($hasMetaDescription) {
                $columns[] = 'meta_description';
                $params[] = $meta_description;
            }
            if ($hasMetaKeywords) {
                $columns[] = 'meta_keywords';
                $params[] = $meta_keywords;
            }
            if ($hasSchemaMarkup) {
                $columns[] = 'schema_markup';
                $params[] = $schema_markup;
            }
            if ($hasSchemas) {
                $columns[] = '`schemas`';
                $params[] = $schema_markup;
            }
            if ($hasSchemaLegacy) {
                $columns[] = '`schema`';
                $params[] = $schema_markup;
            }

            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $stmt = $pdo->prepare("INSERT INTO tours (" . implode(', ', $columns) . ") VALUES (" . $placeholders . ")");
            $stmt->execute($params);
            $_SESSION['message'] = 'Tour added successfully!';
        }
    } catch (Exception $e) {
        $_SESSION['message'] = 'Error: ' . $e->getMessage();
    }
    header('Location: tours.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        if (!verifyCSRFToken($_GET['csrf_token'] ?? '')) {
            throw new Exception('Security validation failed. Please try again.');
        }

        $stmt = $pdo->prepare("DELETE FROM tours WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = 'Tour deleted successfully!';
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
    <title>Tours Management - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/magnific-popup.min.css">
    <link rel="stylesheet" href="../assets/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <style>
        /* Color Switcher Enhancement */
        .color-switch-btns button {
            position: relative;
            transition: all 0.3s ease;
        }

        .color-switch-btns button.active {
            transform: scale(1.2);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            border: 2px solid #fff;
        }

        .color-switch-btns button:hover {
            transform: scale(1.1);
        }

        /* Tour Slider Section Header Styling */
        .tour-area .row.align-items-center {
            margin-bottom: 30px;
        }

        .tour-area .title-area .sec-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--title-color);
            margin-bottom: 0;
        }

        /* Tour Location Styling */
        .tour-location {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        @media (max-width: 767px) {
            .tour-area .row.align-items-center {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 15px;
            }

            .tour-area .row.align-items-center .col-auto:last-child {
                width: 100%;
            }

            .tour-area .row.align-items-center .col-auto:last-child .line-btn {
                width: 100%;
                text-align: center;
                display: inline-block;
            }

            .tour-area .title-area .sec-title {
                font-size: 24px;
            }
        }

        /* Modal Styles */
        .modal {
            z-index: 9999;
        }
        .modal-dialog {
            max-width: 80vw;
            height: 80vh;
            margin: auto auto;
            top: 5%;
        
        }
        
        .input-group-text{
          height: 100%;
        }
        .modal-content {
            height: 100%;
            overflow: hidden;
        }
        .modal-body {
            height: calc(100% - 120px); /* Adjust for header and footer */
            overflow-y: auto;
        }

        #descriptionEditor {
            min-height: 220px;
            background: #fff;
        }

        /* Fix alignment for form elements */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .input-group-text {
            min-width: 40px;
            justify-content: center;
        }

        .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-color: #ced4da;
            box-shadow: none;
        }

        .input-group .input-group-text {
            border-right: none;
        }

        /* Button alignment */
        .modal-footer {
            justify-content: flex-end;
            gap: 10px;
        }

        .modal-footer .btn {
            min-width: 100px;
        }

        /* Custom file upload */
        .custom-file-upload {
            position: relative;
            display: flex;
            align-items: center;
            min-height: 38px; /* Match form-control height */
        }

        .custom-file-upload label {
            cursor: pointer;
            margin: 0;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .custom-file-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        /* Fixed styles for highlights, included, excluded, and itinerary fields */
        .dynamic-field-list {
            margin-top: 10px;
        }
        
        .dynamic-field-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .dynamic-field-item .form-control {
            flex-grow: 1;
        }
        
        .dynamic-field-item .btn {
            margin-left: 10px;
        }
        
        .itinerary-day {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .itinerary-day-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .itinerary-day-title {
            flex-grow: 1;
        }
        
        .itinerary-point {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .itinerary-point .form-control {
            flex-grow: 1;
        }
        
        .itinerary-point .btn {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>
    <div class="main-content">
        <div class="content-wrapper">
            <div class="d-flex mb-3 justify-content-between align-items-center">
                <h1 class="page-title">Tours Management</h1>
                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>
                <button type="button" class="btn btn-primary mb-3" onclick="openAddModal()" aria-label="Add new tour">Add New Tour</button> </div>
         
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Images</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $currentPage = max(1, (int)($_GET['page'] ?? 1));
                        $perPage = 20;
                        $totalItems = (int)$pdo->query("SELECT COUNT(*) FROM tours")->fetchColumn();
                        $pagination = getPaginationInfo($currentPage, $totalItems, $perPage);

                        $stmt = $pdo->prepare("SELECT t.*, c.name as category_name FROM tours t LEFT JOIN categories c ON t.category_id = c.id ORDER BY t.created_at DESC LIMIT ? OFFSET ?");
                        $stmt->bindValue(1, (int)$pagination['per_page'], PDO::PARAM_INT);
                        $stmt->bindValue(2, (int)$pagination['offset'], PDO::PARAM_INT);
                        $stmt->execute();
                        $csrfToken = urlencode(generateCSRFToken());
                        while ($row = $stmt->fetch()) {
                            $truncatedTitle = strlen($row['title']) > 50 ? substr($row['title'], 0, 50) . '...' : $row['title'];
                            $truncatedLocation = strlen($row['location']) > 30 ? substr($row['location'], 0, 30) . '...' : $row['location'];
                            echo "<tr>
                                <td>{$truncatedTitle}</td>
                                <td>{$row['category_name']}</td>
                                <td>{$truncatedLocation}</td>
                                <td>";
                            $images = json_decode($row['images'], true);
                            if ($images && is_array($images)) {
                                foreach ($images as $img) {
                                    echo "<img src='../assets/img/{$img}' width='50' height='50' style='margin-right:5px; border-radius:5px;'>";
                                }
                            } else {
                                echo 'No images';
                            }
                            echo "</td>
                                <td>
                                    <a href='#' onclick='openEditModal(" . $row['id'] . "); return false;' class='btn btn-sm btn-warning'>Edit</a>
                                    <a href='?action=delete&id={$row['id']}&csrf_token={$csrfToken}' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete this tour?\")'>Delete</a>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
                <nav aria-label="Tours pagination">
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
    </div>

    <!-- Modal -->
    <div class="modal fade" id="tourModal" tabindex="-1" role="dialog" aria-labelledby="tourModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="tourModalLabel">Add New Tour</h5>
            <button type="button" class="close" onclick="$('#tourModal').modal('hide')" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="tourForm" method="POST" enctype="multipart/form-data" onsubmit="updateHiddenFields()">
              <input type="hidden" name="id" id="tourId">
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
              <input type="hidden" name="current_images" id="currentImagesInput">
              <input type="hidden" name="cropped_images" id="croppedImagesInput">
              <input type="hidden" name="highlights" id="highlightsInput">
              <input type="hidden" name="included" id="includedInput">
              <input type="hidden" name="excluded" id="excludedInput">
              <input type="hidden" name="itinerary" id="itineraryInput">
              <input type="hidden" name="faq" id="faqInput">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Title *</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                      </div>
                      <input type="text" name="title" class="form-control" required>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Slug *</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-link"></i></span>
                      </div>
                      <input type="text" name="slug" id="slug" class="form-control" required>
                    </div>
                    <small class="form-text text-muted">URL-friendly version of the title. Auto-generated if left empty.</small>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Description</label>
                                <div id="descriptionEditor"></div>
                                <textarea name="description" id="description" class="d-none"></textarea>
              </div>
              
              <!-- Tour Highlights Section -->
              <div class="form-group">
                <label>Tour Highlights</label>
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-star"></i></span>
                  </div>
                  <input type="text" id="highlightInput" class="form-control" placeholder="Enter highlight">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-primary" onclick="addHighlightFromInput()">Add</button>
                  </div>
                </div>
                <div id="highlightsList" class="dynamic-field-list"></div>
              </div>
              
              <!-- Included Section -->
              <div class="form-group">
                <label>Included</label>
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                  </div>
                  <input type="text" id="includedItemInput" class="form-control" placeholder="Enter included item">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-primary" onclick="addIncludedFromInput()">Add</button>
                  </div>
                </div>
                <div id="includedList" class="dynamic-field-list"></div>
              </div>
              
              <!-- Excluded Section -->
              <div class="form-group">
                <label>Excluded</label>
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-times-circle"></i></span>
                  </div>
                  <input type="text" id="excludedItemInput" class="form-control" placeholder="Enter excluded item">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-primary" onclick="addExcludedFromInput()">Add</button>
                  </div>
                </div>
                <div id="excludedList" class="dynamic-field-list"></div>
              </div>
              
              <!-- Itinerary Section -->
              <div class="form-group">
                <label>Itinerary</label>
                <div id="itineraryContainer"></div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addDay()">Add Day</button>
              </div>
              
              <!-- FAQ Section -->
              <div class="form-group">
                <label>Frequently Asked Questions</label>
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-question-circle"></i></span>
                  </div>
                  <input type="text" id="faqQuestionInput" class="form-control" placeholder="Enter question">
                </div>
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-answer"></i></span>
                  </div>
                  <textarea id="faqAnswerInput" class="form-control" placeholder="Enter answer" rows="2"></textarea>
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-primary" onclick="addFaqFromInput()">Add FAQ</button>
                  </div>
                </div>
                <div id="faqList" class="dynamic-field-list"></div>
              </div>
              
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Pricing</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                      </div>
                      <input type="number" step="0.01" name="pricing" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Duration</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                      </div>
                      <input type="text" name="duration" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Availability</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                      </div>
                      <input type="number" name="availability" class="form-control">
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Category</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-list"></i></span>
                      </div>
                      <select name="category_id" class="form-control">
                        <option value="">Select Category</option>
                        <?php
                        $cat_stmt = $pdo->query("SELECT * FROM categories WHERE type = 'tour'");
                        while ($cat = $cat_stmt->fetch()) {
                          echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Location</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                      </div>
                      <input type="text" name="location" class="form-control">
                    </div>
                  </div>
                </div>
               </div>
               <!-- SEO Settings Box -->
               <div class="seo-settings-box" style="border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; background-color: #f8f9fa; margin-bottom: 20px;">
                 <h6 style="margin-bottom: 15px; color: #495057; font-weight: 600;"><i class="fas fa-search" style="margin-right: 8px;"></i>SEO Settings</h6>
                 <div class="form-group" style="margin-bottom: 15px;">
                   <label style="font-weight: 500; color: #495057;">Meta Title</label>
                   <div class="input-group">
                     <div class="input-group-prepend">
                       <span class="input-group-text"><i class="fas fa-heading"></i></span>
                     </div>
                     <input type="text" name="meta_title" class="form-control" maxlength="60" placeholder="Custom page title for SEO">
                   </div>
                   <small class="form-text text-muted">60 characters max. Leave empty to use tour title.</small>
                 </div>
                 <div class="form-group" style="margin-bottom: 15px;">
                   <label style="font-weight: 500; color: #495057;">Meta Description</label>
                   <textarea name="meta_description" class="form-control" rows="3" maxlength="160" placeholder="Custom meta description for SEO"></textarea>
                   <small class="form-text text-muted">160 characters max. Leave empty to auto-generate from content.</small>
                 </div>
                 <div class="form-group" style="margin-bottom: 0;">
                   <label style="font-weight: 500; color: #495057;">Meta Keywords</label>
                   <div class="input-group">
                     <div class="input-group-prepend">
                       <span class="input-group-text"><i class="fas fa-key"></i></span>
                     </div>
                     <input type="text" name="meta_keywords" class="form-control" placeholder="keyword1, keyword2, keyword3">
                   </div>
                   <small class="form-text text-muted">Comma-separated keywords (optional).</small>
                  </div>
                </div>

                <!-- Schema Settings Box -->
                <div class="schema-settings-box" style="border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; background-color: #f8f9fa; margin-bottom: 20px;">
                  <h6 style="margin-bottom: 15px; color: #495057; font-weight: 600;"><i class="fas fa-code" style="margin-right: 8px;"></i>Schema Markup</h6>
                  <div id="schemaContainer">
                    <!-- Schema items will be added here dynamically -->
                  </div>
                  <div class="schema-actions" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSchema()">
                      <i class="fas fa-plus"></i> Add Schema
                    </button>
                    <div class="schema-templates" style="margin-top: 10px;">
                      <small class="text-muted">Quick templates:</small>
                      <button type="button" class="btn btn-outline-secondary btn-xs ml-2" onclick="addTourSchema()">Tour</button>
                      <button type="button" class="btn btn-outline-secondary btn-xs ml-1" onclick="addProductSchema()">Product</button>
                      <button type="button" class="btn btn-outline-secondary btn-xs ml-1" onclick="addOrganizationSchema()">Organization</button>
                    </div>
                  </div>
                  <input type="hidden" name="schemas" id="schemasInput">
                </div>

                <div class="form-group">
                  <label>Images (multiple allowed, max 5)</label>
                <div class="custom-file-upload">
                  <input type="file" name="images[]" multiple class="form-control-file d-none" id="imageInput" accept="image/*">
                  <label for="imageInput" class="btn btn-outline-primary btn-block d-flex align-items-center justify-content-center">
                    <i class="fas fa-images mr-2"></i> Choose Images
                  </label>
                </div>
                <div id="cropperContainer" style="display: none; margin-top: 10px;">
                  <img id="cropperImage" style="max-width: 100%; max-height: 400px;">
                  <div class="mt-2">
                    <button type="button" class="btn btn-success btn-sm" id="cropBtn">Crop & Add</button>
                    <button type="button" class="btn btn-secondary btn-sm" id="cancelCropBtn">Cancel</button>
                  </div>
                </div>
                <div id="currentImages"></div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="$('#tourModal').modal('hide')">Cancel</button>
            <button type="submit" form="tourForm" class="btn btn-success">Save Tour</button>
          </div>
        </div>
      </div>
    </div>

    <script src="../assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/swiper-bundle.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/jquery.magnific-popup.min.js"></script>
    <script src="../assets/js/jquery.counterup.min.js"></script>
    <script src="../assets/js/jquery-ui.min.js"></script>
    <script src="../assets/js/imagesloaded.pkgd.min.js"></script>
    <script src="../assets/js/isotope.pkgd.min.js"></script>
    <script src="../assets/js/gsap.min.js"></script>
    <script src="../assets/js/circle-progress.js"></script>
    <script src="../assets/js/matter.min.js"></script>
    <script src="../assets/js/matterjs-custom.js"></script>
    <script src="../assets/js/nice-select.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script>
        let tourEditor;

        function initTourEditor() {
            if (tourEditor) {
                return;
            }

            tourEditor = new Quill('#descriptionEditor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'code-block'],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });
        }

        function setTourEditorHtml(html) {
            initTourEditor();
            tourEditor.clipboard.dangerouslyPasteHTML(html || '');
            $('#description').val(html || '');
        }

        function syncTourEditorToField() {
            initTourEditor();
            $('#description').val(tourEditor.root.innerHTML);
        }

        function openAddModal() {
            $('#tourForm')[0].reset();
            $('#tourId').val('');
            currentImages = [];
            $('#currentImagesInput').val('[]');
            $('#currentImages').html('');
            $('#imageInput').val('');
            $('#tourModalLabel').text('Add New Tour');
            $('#tourModal').modal('show');
            highlights = [];
            included = [];
            excluded = [];
            itinerary = [];
            faq = [];
            renderHighlights();
            renderIncluded();
            renderExcluded();
            renderItinerary();
            renderFaq();
            setTourEditorHtml('');
        }

        // Initialize all arrays to prevent undefined errors
        var currentImages = [];
        var croppedImages = [];
        var highlights = [];
        var included = [];
        var excluded = [];
        var itinerary = [];
        var faq = [];

        function openEditModal(id) {
            $.get('get_tour.php?id=' + id, function(data) {
                try {
                    var response;
                    // Handle both string and object responses
                    if (typeof data === 'string') {
                        try {
                            response = JSON.parse(data);
                        } catch(e) {
                            alert('Error parsing server response: ' + e.message);
                            return;
                        }
                    } else {
                        response = data;
                    }
                    
                    if (response.success === false) {
                        alert(response.message || 'Tour not found');
                        return;
                    }
                    
                    if (!response.data) {
                        alert('No tour data received');
                        return;
                    }
                    
                    var tour = response.data;
                    
                    // Initialize all arrays to prevent undefined errors
                    highlights = [];
                    included = [];
                    excluded = [];
                    itinerary = [];
                    faq = [];
                    currentImages = [];
                    
                    // Set form values
                    $('#tourId').val(tour.id || '');
                    $('input[name="title"]').val(tour.title || '');
                    $('input[name="slug"]').val(tour.slug || '');
                    $('#description').val(tour.description || '');
                    $('input[name="pricing"]').val(tour.pricing || '');
                    $('input[name="duration"]').val(tour.duration || '');
                    $('input[name="availability"]').val(tour.availability || '');
                    $('select[name="category_id"]').val(tour.category_id || '');
                    $('input[name="location"]').val(tour.location || '');
                    $('input[name="meta_title"]').val(tour.meta_title || '');
                    $('textarea[name="meta_description"]').val(tour.meta_description || '');
                    $('input[name="meta_keywords"]').val(tour.meta_keywords || '');

                    // Load schemas
                    const savedSchemas = tour.schemas || tour.schema_markup || tour.schema || '';
                    if (savedSchemas) {
                        try {
                            const existingSchemas = JSON.parse(savedSchemas);
                            existingSchemas.forEach(function(schema) {
                                addSchema(schema.type || 'custom', schema.data);
                            });
                        } catch (e) {
                            console.warn('Failed to load existing schemas:', e);
                        }
                    }
                    
                    // Safely parse JSON fields with fallbacks
                    try {
                        highlights = JSON.parse(tour.highlights || '[]');
                        if (!Array.isArray(highlights)) highlights = [];
                    } catch(e) {
                        highlights = [];
                        console.warn('Failed to parse highlights:', e.message);
                    }
                    
                    try {
                        included = JSON.parse(tour.included || '[]');
                        if (!Array.isArray(included)) included = [];
                    } catch(e) {
                        included = [];
                        console.warn('Failed to parse included:', e.message);
                    }
                    
                    try {
                        excluded = JSON.parse(tour.excluded || '[]');
                        if (!Array.isArray(excluded)) excluded = [];
                    } catch(e) {
                        excluded = [];
                        console.warn('Failed to parse excluded:', e.message);
                    }
                    
                    try {
                        itinerary = JSON.parse(tour.itinerary || '[]');
                        if (!Array.isArray(itinerary)) itinerary = [];
                        // Normalize itinerary to array of objects
                        itinerary = itinerary.map(function(day) {
                            if (typeof day === 'string') {
                                return {title: day, points: []};
                            } else if (day && typeof day === 'object' && day.title) {
                                return day;
                            } else {
                                return {title: 'Day', points: []};
                            }
                        });
                    } catch(e) {
                        itinerary = [];
                        console.warn('Failed to parse itinerary:', e.message);
                    }
                    
                    try {
                        faq = JSON.parse(tour.faq || '[]');
                        if (!Array.isArray(faq)) faq = [];
                    } catch(e) {
                        faq = [];
                        console.warn('Failed to parse faq:', e.message);
                    }
                    
                    try {
                        currentImages = JSON.parse(tour.images || '[]');
                        if (!Array.isArray(currentImages)) currentImages = [];
                    } catch(e) {
                        currentImages = [];
                        console.warn('Failed to parse images:', e.message);
                    }
                    
                    // Render all sections
                    renderHighlights();
                    renderIncluded();
                    renderExcluded();
                    renderItinerary();
                    renderFaq();
                    
                    // Reset cropped images
                    croppedImages = [];
                    $('#currentImagesInput').val(JSON.stringify(currentImages));
                    $('#croppedImagesInput').val('[]');
                    renderCurrentImages();

                    $('#tourModalLabel').text('Edit Tour');
                    $('#tourModal').modal('show');
                    setTourEditorHtml(tour.description || '');
                } catch(e) {
                    console.error('Error in openEditModal:', e);
                    alert('Error processing tour data: ' + e.message);
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                alert('Error loading tour data. Please try again.');
            });
        }

        function renderCurrentImages() {
            var html = '';
            currentImages.forEach(function(img, index) {
                html += '<div class="d-inline-block mr-2 mb-2 position-relative">';
                html += '<img src="' + (img.startsWith('data:') ? img : '../assets/img/' + img) + '" width="100" height="100" style="object-fit: cover;" class="border rounded">';
                html += '<button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 0; right: 0;" onclick="deleteImage(' + index + ')">&times;</button>';
                html += '</div>';
            });
            $('#currentImages').html(html);
        }

        function deleteImage(index) {
            currentImages.splice(index, 1);
            $('#currentImagesInput').val(JSON.stringify(currentImages));
            renderCurrentImages();
        }

        // Highlights
        function renderHighlights() {
            var html = '';
            highlights.forEach(function(item, index) {
                html += '<div class="dynamic-field-item">';
                html += '<div class="input-group">';
                html += '<div class="input-group-prepend">';
                html += '<span class="input-group-text"><i class="fas fa-star"></i></span>';
                html += '</div>';
                html += '<input type="text" class="form-control" value="' + (item || '').replace(/"/g, '"') + '" onchange="updateHighlight(' + index + ', this.value)">';
                html += '<div class="input-group-append">';
                html += '<button type="button" class="btn btn-outline-danger" onclick="deleteHighlight(' + index + ')"><i class="fas fa-trash"></i></button>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });
            $('#highlightsList').html(html);
            $('#highlightsInput').val(JSON.stringify(highlights));
        }

        function addHighlightFromInput() {
            var value = $('#highlightInput').val().trim();
            if (value) {
                highlights.push(value);
                $('#highlightInput').val('');
                renderHighlights();
            }
        }
        
        function updateHighlight(index, value) {
            highlights[index] = value;
            $('#highlightsInput').val(JSON.stringify(highlights));
        }

        function deleteHighlight(index) {
            highlights.splice(index, 1);
            renderHighlights();
        }

        // Included
        function renderIncluded() {
            var html = '';
            included.forEach(function(item, index) {
                html += '<div class="dynamic-field-item">';
                html += '<div class="input-group">';
                html += '<div class="input-group-prepend">';
                html += '<span class="input-group-text"><i class="fas fa-check-circle"></i></span>';
                html += '</div>';
                html += '<input type="text" class="form-control" value="' + (item || '').replace(/"/g, '"') + '" onchange="updateIncluded(' + index + ', this.value)">';
                html += '<div class="input-group-append">';
                html += '<button type="button" class="btn btn-outline-danger" onclick="deleteIncluded(' + index + ')"><i class="fas fa-trash"></i></button>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });
            $('#includedList').html(html);
            $('#includedInput').val(JSON.stringify(included));
        }

        function addIncludedFromInput() {
            var value = $('#includedItemInput').val().trim();
            if (value) {
                included.push(value);
                $('#includedItemInput').val('');
                renderIncluded();
            }
        }
        
        function updateIncluded(index, value) {
            included[index] = value;
            $('#includedInput').val(JSON.stringify(included));
        }

        function deleteIncluded(index) {
            included.splice(index, 1);
            renderIncluded();
        }

        // Excluded
        function renderExcluded() {
            var html = '';
            excluded.forEach(function(item, index) {
                html += '<div class="dynamic-field-item">';
                html += '<div class="input-group">';
                html += '<div class="input-group-prepend">';
                html += '<span class="input-group-text"><i class="fas fa-times-circle"></i></span>';
                html += '</div>';
                html += '<input type="text" class="form-control" value="' + (item || '').replace(/"/g, '"') + '" onchange="updateExcluded(' + index + ', this.value)">';
                html += '<div class="input-group-append">';
                html += '<button type="button" class="btn btn-outline-danger" onclick="deleteExcluded(' + index + ')"><i class="fas fa-trash"></i></button>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });
            $('#excludedList').html(html);
            $('#excludedInput').val(JSON.stringify(excluded));
        }

        function addExcludedFromInput() {
            var value = $('#excludedItemInput').val().trim();
            if (value) {
                excluded.push(value);
                $('#excludedItemInput').val('');
                renderExcluded();
            }
        }
        
        function updateExcluded(index, value) {
            excluded[index] = value;
            $('#excludedInput').val(JSON.stringify(excluded));
        }

        function deleteExcluded(index) {
            excluded.splice(index, 1);
            renderExcluded();
        }

        // Itinerary
        function renderItinerary() {
            var html = '';
            itinerary.forEach(function(day, dayIndex) {
                html += '<div class="itinerary-day">';
                html += '<div class="itinerary-day-header">';
                html += '<div class="input-group itinerary-day-title">';
                html += '<div class="input-group-prepend">';
                html += '<span class="input-group-text"><i class="fas fa-calendar-day"></i></span>';
                html += '</div>';
                html += '<input type="text" class="form-control" placeholder="Day Title" value="' + (day.title || '').replace(/"/g, '"') + '" onchange="updateDayTitle(' + dayIndex + ', this.value)">';
                html += '<div class="input-group-append">';
                html += '<button type="button" class="btn btn-outline-danger" onclick="deleteDay(' + dayIndex + ')"><i class="fas fa-trash"></i></button>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '<div class="itinerary-points">';
                day.points.forEach(function(point, pointIndex) {
                    html += '<div class="itinerary-point">';
                    html += '<div class="input-group">';
                    html += '<div class="input-group-prepend">';
                    html += '<span class="input-group-text"><i class="fas fa-map-pin"></i></span>';
                    html += '</div>';
                    html += '<input type="text" class="form-control" value="' + (point || '').replace(/"/g, '"') + '" onchange="updatePoint(' + dayIndex + ', ' + pointIndex + ', this.value)">';
                    html += '<div class="input-group-append">';
                    html += '<button type="button" class="btn btn-outline-danger" onclick="deletePoint(' + dayIndex + ', ' + pointIndex + ')"><i class="fas fa-trash"></i></button>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                });
                html += '<button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addPoint(' + dayIndex + ')">Add Point</button>';
                html += '</div>';
                html += '</div>';
            });
            $('#itineraryContainer').html(html);
            $('#itineraryInput').val(JSON.stringify(itinerary));
        }

        function addDay() {
            itinerary.push({title: 'Day ' + (itinerary.length + 1), points: []});
            renderItinerary();
        }

        function updateDayTitle(dayIndex, value) {
            itinerary[dayIndex].title = value;
            $('#itineraryInput').val(JSON.stringify(itinerary));
        }

        function deleteDay(dayIndex) {
            itinerary.splice(dayIndex, 1);
            renderItinerary();
        }

        function addPoint(dayIndex) {
            itinerary[dayIndex].points.push('');
            renderItinerary();
        }

        function updatePoint(dayIndex, pointIndex, value) {
            itinerary[dayIndex].points[pointIndex] = value;
            $('#itineraryInput').val(JSON.stringify(itinerary));
        }

        function deletePoint(dayIndex, pointIndex) {
            itinerary[dayIndex].points.splice(pointIndex, 1);
            renderItinerary();
        }

        // FAQ Functions
        function renderFaq() {
            var html = '';
            faq.forEach(function(item, index) {
                html += '<div class="dynamic-field-item">';
                html += '<div class="input-group">';
                html += '<div class="input-group-prepend">';
                html += '<span class="input-group-text"><i class="fas fa-question-circle"></i></span>';
                html += '</div>';
                html += '<div class="form-control" style="background:#f8f9fa;">';
                html += '<strong>Q: ' + (item.question || '').replace(/"/g, '"') + '</strong><br>';
                html += '<span class="text-muted">A: ' + (item.answer || '').replace(/"/g, '"') + '</span>';
                html += '</div>';
                html += '<div class="input-group-append">';
                html += '<button type="button" class="btn btn-outline-danger" onclick="deleteFaq(' + index + ')"><i class="fas fa-trash"></i></button>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });
            $('#faqList').html(html);
            $('#faqInput').val(JSON.stringify(faq));
        }

        function addFaqFromInput() {
            var question = $('#faqQuestionInput').val().trim();
            var answer = $('#faqAnswerInput').val().trim();
            if (question && answer) {
                faq.push({question: question, answer: answer});
                $('#faqQuestionInput').val('');
                $('#faqAnswerInput').val('');
                renderFaq();
            }
        }

        function deleteFaq(index) {
            faq.splice(index, 1);
            renderFaq();
        }

        let cropper;
        let currentFile;

        $('#imageInput').on('change', function(e) {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                // Add previews for all selected files
                files.forEach(function(file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        currentImages.push(e.target.result);
                        renderCurrentImages();
                    };
                    reader.readAsDataURL(file);
                });
                // Crop the first file
                currentFile = files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#cropperImage').attr('src', e.target.result);
                    $('#cropperContainer').show();
                    if (cropper) {
                        cropper.destroy();
                    }
                    cropper = new Cropper($('#cropperImage')[0], {
                        aspectRatio: 16 / 9, // Adjust as needed for tour cards
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
                        autoCropArea: 0.8
                    });
                };
                reader.readAsDataURL(files[0]);
            }
        });

        $('#cropBtn').on('click', function() {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({
                    width: 600, // Reduced size for smaller file
                    height: 342 // Maintain aspect ratio
                });
                const dataURL = canvas.toDataURL('image/webp', 0.7);
                canvas.toBlob(function(blob) {
                    const timestamp = Date.now();
                    const croppedFile = new File([blob], 'cropped_' + timestamp + '_' + currentFile.name, { type: 'image/webp' });
                    const files = Array.from($('#imageInput')[0].files);
                    files[0] = croppedFile;
                    $('#imageInput')[0].files = new FileListItems(files);
                    // Replace the first preview with cropped
                    if (currentImages.length > 0) {
                        currentImages[0] = dataURL;
                    }
                    renderCurrentImages();
                    $('#cropperContainer').hide();
                    cropper.destroy();
                    cropper = null;
                }, 'image/webp', 0.7); // Quality 0.7 for smaller file size
            }
        });

        $('#cancelCropBtn').on('click', function() {
            $('#cropperContainer').hide();
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        // Helper for FileList
        function FileListItems(files) {
            const b = new ClipboardEvent("").clipboardData || new DataTransfer();
            for (let i = 0, len = files.length; i < len; i++) b.items.add(files[i]);
            return b.files;
        }

        // Auto-generate slug from title
        $(document).ready(function() {
            initTourEditor();

            $('input[name="title"]').on('input', function() {
                var title = $(this).val();
                var slug = title.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('input[name="slug"]').val(slug);
            });

        });

        // Enhanced Color Switcher Fix
        jQuery(document).ready(function($) {
            // Initialize color buttons with their colors
            $(".color-switch-btns button").each(function() {
                const $button = $(this);
                const color = $button.data("color");

                // Set the button's background color preview
                $button.css("--theme-color", color);
                $button.css("background-color", color);

                // Add click handler
                $button.on("click", function() {
                    const selectedColor = $(this).data("color");

                    // Update both theme-color and primary-color CSS variables
                    $(":root").css("--theme-color", selectedColor);
                    $(":root").css("--primary-color", selectedColor);

                    // Store in localStorage for persistence
                    localStorage.setItem("theme-color", selectedColor);

                    // Add active class to clicked button
                    $(".color-switch-btns button").removeClass("active");
                    $(this).addClass("active");
                });
            });

            // Load saved color from localStorage on page load
            const savedColor = localStorage.getItem("theme-color");
            if (savedColor) {
                $(":root").css("--theme-color", savedColor);
                $(":root").css("--primary-color", savedColor);

                // Mark the corresponding button as active
                $(".color-switch-btns button").each(function() {
                    if ($(this).data("color") === savedColor) {
                        $(this).addClass("active");
                    }
                });
            }

            // Toggle color scheme panel
            $(document).on("click", ".switchIcon", function() {
                $(".color-scheme-wrap").toggleClass("active");
            });
        });

        function updateHiddenFields() {
            syncTourEditorToField();
            $('#highlightsInput').val(JSON.stringify(highlights));
            $('#includedInput').val(JSON.stringify(included));
            $('#excludedInput').val(JSON.stringify(excluded));
            $('#itineraryInput').val(JSON.stringify(itinerary));
            return true;
        }

        // Schema functionality for tours
        let schemaCounter = 0;
        let schemas = [];

        function addSchema(type = 'custom', data = null) {
            const schemaId = schemaCounter++;
            let schemaHtml = '';

            if (type === 'tour') {
                schemaHtml = createTourSchema(schemaId);
            } else if (type === 'product') {
                schemaHtml = createProductSchema(schemaId);
            } else if (type === 'organization') {
                schemaHtml = createOrganizationSchema(schemaId);
            } else {
                schemaHtml = createCustomSchema(schemaId, data);
            }

            $('#schemaContainer').append(schemaHtml);
            if (data) {
                $(`#schemaType${schemaId}`).val(data.type || 'custom');
                $(`#schemaJson${schemaId}`).val(JSON.stringify(data, null, 2));
            }
            updateSchemasInput();
        }

        function createCustomSchema(id, data = null) {
            const jsonValue = data ? JSON.stringify(data, null, 2) : `{
  "@context": "https://schema.org",
  "@type": "TouristAttraction",
  "name": "${data?.title || 'Tour Name'}",
  "description": "${data?.description || 'Tour description'}",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "${data?.location || 'Location'}",
    "addressRegion": "Uttar Pradesh",
    "addressCountry": "IN"
  }
}`;
            return `
<div class="schema-item" id="schemaItem${id}" style="border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; margin-bottom: 10px; background: white;">
    <div class="schema-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <select id="schemaType${id}" class="form-control form-control-sm" style="width: 150px;" onchange="changeSchemaType(${id}, this.value)">
            <option value="custom">Custom JSON-LD</option>
            <option value="tour">Tour</option>
            <option value="product">Product</option>
            <option value="organization">Organization</option>
            <option value="event">Event</option>
            <option value="place">Place</option>
        </select>
        <button type="button" class="btn btn-danger btn-sm" onclick="removeSchema(${id})">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <textarea id="schemaJson${id}" class="form-control" rows="8" placeholder="Enter JSON-LD schema markup">${jsonValue}</textarea>
    <div class="schema-preview" style="margin-top: 10px;">
        <button type="button" class="btn btn-info btn-sm" onclick="validateSchema(${id})">
            <i class="fas fa-check"></i> Validate
        </button>
        <span id="validationResult${id}" style="margin-left: 10px; font-size: 12px;"></span>
    </div>
</div>`;
        }

        function createTourSchema(id) {
            return `
<div class="schema-item" id="schemaItem${id}" style="border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; margin-bottom: 10px; background: white;">
    <div class="schema-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <strong>Tour Schema</strong>
        <button type="button" class="btn btn-danger btn-sm" onclick="removeSchema(${id})">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Name</label>
                <input type="text" id="tourName${id}" class="form-control" placeholder="Tour name">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Description</label>
                <input type="text" id="tourDescription${id}" class="form-control" placeholder="Tour description">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Location</label>
                <input type="text" id="tourLocation${id}" class="form-control" placeholder="Tour location">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Duration</label>
                <input type="text" id="tourDuration${id}" class="form-control" placeholder="e.g., 2 days">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Price</label>
                <input type="text" id="tourPrice${id}" class="form-control" placeholder="Price (e.g., 500)">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Currency</label>
                <select id="tourCurrency${id}" class="form-control">
                    <option value="INR">INR</option>
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                </select>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-primary btn-sm" onclick="generateTourSchema(${id})">
        <i class="fas fa-magic"></i> Generate JSON-LD
    </button>
    <textarea id="schemaJson${id}" class="form-control mt-2" rows="10" style="display: none;"></textarea>
</div>`;
        }

        function createProductSchema(id) {
            return `
<div class="schema-item" id="schemaItem${id}" style="border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; margin-bottom: 10px; background: white;">
    <div class="schema-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <strong>Product Schema</strong>
        <button type="button" class="btn btn-danger btn-sm" onclick="removeSchema(${id})">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Name</label>
                <input type="text" id="productName${id}" class="form-control" placeholder="Product name">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Description</label>
                <input type="text" id="productDescription${id}" class="form-control" placeholder="Product description">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Price</label>
                <input type="number" step="0.01" id="productPrice${id}" class="form-control" placeholder="Price">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Currency</label>
                <select id="productCurrency${id}" class="form-control">
                    <option value="INR">INR</option>
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Availability</label>
                <select id="productAvailability${id}" class="form-control">
                    <option value="InStock">In Stock</option>
                    <option value="OutOfStock">Out of Stock</option>
                    <option value="PreOrder">Pre-order</option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Image URL</label>
        <input type="url" id="productImage${id}" class="form-control" placeholder="https://example.com/image.jpg">
    </div>
    <button type="button" class="btn btn-primary btn-sm" onclick="generateProductSchema(${id})">
        <i class="fas fa-magic"></i> Generate JSON-LD
    </button>
    <textarea id="schemaJson${id}" class="form-control mt-2" rows="8" style="display: none;"></textarea>
</div>`;
        }

        function createOrganizationSchema(id) {
            return `
<div class="schema-item" id="schemaItem${id}" style="border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; margin-bottom: 10px; background: white;">
    <div class="schema-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <strong>Organization Schema</strong>
        <button type="button" class="btn btn-danger btn-sm" onclick="removeSchema(${id})">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Name</label>
                <input type="text" id="orgName${id}" class="form-control" placeholder="Organization name" value="India Day Trip">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>URL</label>
                <input type="url" id="orgUrl${id}" class="form-control" placeholder="Website URL" value="https://indiadaytrip.com">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea id="orgDescription${id}" class="form-control" rows="2" placeholder="Organization description">India Day Trip offers authentic cultural experiences and guided tours across India's most iconic destinations.</textarea>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" id="orgPhone${id}" class="form-control" placeholder="+91-XXXXXXXXXX">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="orgEmail${id}" class="form-control" placeholder="indiadaytrip@gmail.com">
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-primary btn-sm" onclick="generateOrganizationSchema(${id})">
        <i class="fas fa-magic"></i> Generate JSON-LD
    </button>
    <textarea id="schemaJson${id}" class="form-control mt-2" rows="10" style="display: none;"></textarea>
</div>`;
        }

        function addTourSchema() {
            addSchema('tour');
        }

        function addProductSchema() {
            addSchema('product');
        }

        function addOrganizationSchema() {
            addSchema('organization');
        }

        function removeSchema(id) {
            $(`#schemaItem${id}`).remove();
            schemas = schemas.filter(s => s.id !== id);
            updateSchemasInput();
        }

        function validateSchema(id) {
            const jsonText = $(`#schemaJson${id}`).val();
            try {
                JSON.parse(jsonText);
                $(`#validationResult${id}`).html('<span style="color: green;">✓ Valid JSON</span>');
            } catch (e) {
                $(`#validationResult${id}`).html('<span style="color: red;">✗ Invalid JSON</span>');
            }
        }

        function generateTourSchema(id) {
            const name = $(`#tourName${id}`).val() || 'Tour Name';
            const description = $(`#tourDescription${id}`).val() || 'Tour description';
            const location = $(`#tourLocation${id}`).val() || 'Location';
            const duration = $(`#tourDuration${id}`).val() || '1 day';
            const price = $(`#tourPrice${id}`).val() || '0';
            const currency = $(`#tourCurrency${id}`).val() || 'INR';

            const schema = {
                "@context": "https://schema.org",
                "@type": "TouristTrip",
                "name": name,
                "description": description,
                "provider": {
                    "@type": "Organization",
                    "name": "India Day Trip"
                },
                "offers": {
                    "@type": "Offer",
                    "price": price,
                    "priceCurrency": currency,
                    "availability": "https://schema.org/InStock"
                },
                "touristType": "Cultural tourism",
                "duration": `P${duration.replace(/[^\d]/g, '')}D`
            };

            $(`#schemaJson${id}`).val(JSON.stringify(schema, null, 2)).show();
            updateSchemasInput();
        }

        function generateProductSchema(id) {
            const name = $(`#productName${id}`).val() || 'Product Name';
            const description = $(`#productDescription${id}`).val() || 'Product description';
            const price = $(`#productPrice${id}`).val() || '0';
            const currency = $(`#productCurrency${id}`).val() || 'INR';
            const availability = $(`#productAvailability${id}`).val() || 'InStock';
            const image = $(`#productImage${id}`).val() || '';

            const schema = {
                "@context": "https://schema.org",
                "@type": "Product",
                "name": name,
                "description": description,
                "offers": {
                    "@type": "Offer",
                    "price": price,
                    "priceCurrency": currency,
                    "availability": `https://schema.org/${availability}`
                }
            };

            if (image) {
                schema.image = image;
            }

            $(`#schemaJson${id}`).val(JSON.stringify(schema, null, 2)).show();
            updateSchemasInput();
        }

        function generateOrganizationSchema(id) {
            const name = $(`#orgName${id}`).val() || 'Organization Name';
            const url = $(`#orgUrl${id}`).val() || '';
            const description = $(`#orgDescription${id}`).val() || 'Organization description';
            const phone = $(`#orgPhone${id}`).val() || '';
            const email = $(`#orgEmail${id}`).val() || '';

            const schema = {
                "@context": "https://schema.org",
                "@type": "Organization",
                "name": name,
                "description": description,
                "url": url,
                "contactPoint": {
                    "@type": "ContactPoint",
                    "telephone": phone,
                    "email": email,
                    "contactType": "customer service"
                }
            };

            $(`#schemaJson${id}`).val(JSON.stringify(schema, null, 2)).show();
            updateSchemasInput();
        }

        function updateSchemasInput() {
            const schemasData = [];
            $('.schema-item').each(function() {
                const id = $(this).attr('id').replace('schemaItem', '');
                const jsonText = $(`#schemaJson${id}`).val();
                if (jsonText) {
                    try {
                        const schema = JSON.parse(jsonText);
                        schemasData.push({
                            id: id,
                            type: $(`#schemaType${id}`).val() || 'custom',
                            data: schema
                        });
                    } catch (e) {
                        console.warn('Invalid JSON in schema', id);
                    }
                }
            });
            $('#schemasInput').val(JSON.stringify(schemasData));
        }

        // Initialize schema functionality
        $(document).ready(function() {
            // Load existing schemas when editing
            if (typeof tour !== 'undefined' && tour.schemas) {
                try {
                    const existingSchemas = JSON.parse(tour.schemas);
                    existingSchemas.forEach(function(schema) {
                        addSchema(schema.type || 'custom', schema.data);
                    });
                } catch (e) {
                    console.warn('Failed to load existing schemas:', e);
                }
            }
        });
    </script>
</body>
</html>