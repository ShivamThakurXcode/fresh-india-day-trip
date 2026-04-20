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
        $content = $_POST['content']; // Keep as-is for WYSIWYG editor content
        $excerpt = trim($_POST['excerpt']);
        $author = trim($_POST['author']);
        $publication_date = $_POST['publication_date'];
        $status = $_POST['status'];
        $tags = array_map('trim', explode(',', $_POST['tags']));
        $tags_json = json_encode($tags);
        $category_id = $_POST['category_id'] ?? null;
        $meta_title = trim($_POST['meta_title'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');
        $meta_keywords = trim($_POST['meta_keywords'] ?? '');
        $schema_markup = trim($_POST['schemas'] ?? ($_POST['schema_markup'] ?? '[]'));

        // Generate slug if empty
        if (empty($slug)) {
            $slug = generateSlug($title, 'blogs', $id);
        }

        $featured_image = '';
        if (!empty($_FILES['featured_image']['name'])) {
            // SECURITY: Validate file upload thoroughly
            $validation = validateFileUpload($_FILES['featured_image'], ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], 2 * 1024 * 1024);
            if (!$validation['valid']) {
                throw new Exception('Invalid image: ' . $validation['error']);
            }
            
            $sanitized_name = sanitizeFilename($_FILES['featured_image']['name']);
            $filename = time() . '_' . uniqid() . '.' . $validation['extension'];
            $target = "../assets/img/blog/" . $filename;
            
            // Create directory if needed
            if (!is_dir('../assets/img/blog/')) {
                mkdir('../assets/img/blog/', 0755, true);
            }
            
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
                $featured_image = $filename;
            } else {
                throw new Exception('Failed to upload image. Please check file permissions.');
            }
        }

        $hasMetaTitle = (bool)$pdo->query("SHOW COLUMNS FROM blogs LIKE 'meta_title'")->fetch();
        $hasMetaDescription = (bool)$pdo->query("SHOW COLUMNS FROM blogs LIKE 'meta_description'")->fetch();
        $hasMetaKeywords = (bool)$pdo->query("SHOW COLUMNS FROM blogs LIKE 'meta_keywords'")->fetch();
        $hasSchemaMarkup = (bool)$pdo->query("SHOW COLUMNS FROM blogs LIKE 'schema_markup'")->fetch();
        $hasSchemas = (bool)$pdo->query("SHOW COLUMNS FROM blogs LIKE 'schemas'")->fetch();
        $hasSchemaLegacy = (bool)$pdo->query("SHOW COLUMNS FROM blogs LIKE 'schema'")->fetch();

        if ($id) {
            $query = "UPDATE blogs SET title = ?, slug = ?, content = ?, excerpt = ?, author = ?, publication_date = ?, status = ?, tags = ?, category_id = ?";
            $params = [$title, $slug, $content, $excerpt, $author, $publication_date, $status, $tags_json, $category_id];

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

            if ($featured_image) {
                $query .= ", featured_image = ?";
                $params[] = $featured_image;
            }
            $query .= " WHERE id = ?";
            $params[] = $id;
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $_SESSION['message'] = 'Blog updated successfully!';
        } else {
            $columns = ['title', 'slug', 'content', 'excerpt', 'author', 'publication_date', 'status', 'tags', 'category_id', 'featured_image'];
            $params = [$title, $slug, $content, $excerpt, $author, $publication_date, $status, $tags_json, $category_id, $featured_image];

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
            $stmt = $pdo->prepare("INSERT INTO blogs (" . implode(', ', $columns) . ") VALUES (" . $placeholders . ")");
            $stmt->execute($params);
            $_SESSION['message'] = 'Blog added successfully!';
        }
    } catch (Exception $e) {
        $_SESSION['message'] = 'Error: ' . $e->getMessage();
    }
    header('Location: blogs.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        if (!verifyCSRFToken($_GET['csrf_token'] ?? '')) {
            throw new Exception('Security validation failed. Please try again.');
        }

        $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $_SESSION['message'] = 'Blog deleted successfully!';
    } catch (Exception $e) {
        $_SESSION['message'] = 'Error: ' . $e->getMessage();
    }
    header('Location: blogs.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs Management - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/magnific-popup.min.css">
    <link rel="stylesheet" href="../assets/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <style>
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
        .modal-content {
            height: 100%;
            overflow: hidden;
        }
        .modal-body {
            height: calc(100% - 120px);
            overflow-y: auto;
        }
        #contentEditor {
            min-height: 220px;
            background: #fff;
        }
        .input-group-text{
            height: 100%;
        }
    </style>
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
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>
    <div class="main-content">
        <div class="content-wrapper">
            <div class="d-flex mb-3 justify-content-between align-items-center">
                <h1 class="page-title">Blogs Management</h1>
                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>
                <button type="button" class="btn btn-primary mb-3" onclick="openAddModal()" aria-label="Add new blog">Add New Blog</button>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Publication Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $currentPage = max(1, (int)($_GET['page'] ?? 1));
                    $perPage = 20;
                    $totalItems = (int)$pdo->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
                    $pagination = getPaginationInfo($currentPage, $totalItems, $perPage);

                    $stmt = $pdo->prepare("SELECT b.*, c.name as category_name FROM blogs b LEFT JOIN categories c ON b.category_id = c.id ORDER BY b.created_at DESC LIMIT ? OFFSET ?");
                    $stmt->bindValue(1, (int)$pagination['per_page'], PDO::PARAM_INT);
                    $stmt->bindValue(2, (int)$pagination['offset'], PDO::PARAM_INT);
                    $stmt->execute();
                    $csrfToken = urlencode(generateCSRFToken());
                    while ($row = $stmt->fetch()) {
                        $truncatedTitle = strlen($row['title']) > 50 ? substr($row['title'], 0, 50) . '...' : $row['title'];
                        echo "<tr>
                            <td>{$truncatedTitle}</td>
                            <td>{$row['author']}</td>
                            <td>{$row['category_name']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['publication_date']}</td>
                            <td>
                                <a href='#' onclick='openEditModal({$row['id']}); return false;' class='btn btn-sm btn-warning'>Edit</a>
                                <a href='?action=delete&id={$row['id']}&csrf_token={$csrfToken}' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete this blog?\")'>Delete</a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
            <nav aria-label="Blogs pagination">
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

            <!-- Modal -->
            <div class="modal fade" id="blogModal" tabindex="-1" role="dialog" aria-labelledby="blogModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="blogModalLabel">Add New Blog</h5>
                            <button type="button" class="close" onclick="$('#blogModal').modal('hide')" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="blogForm" method="POST" enctype="multipart/form-data" onsubmit="updateForm()">
                                <input type="hidden" name="id" id="blogId">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                <div class="form-group">
                                    <label>Title *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                        </div>
                                        <input type="text" name="title" class="form-control" required>
                                    </div>
                                </div>
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
                                <div class="form-group">
                                    <label>Content *</label>
                                    <div id="contentEditor"></div>
                                    <textarea name="content" id="content" class="d-none"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Excerpt</label>
                                    <textarea name="excerpt" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Author</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                </div>
                                                <input type="text" name="author" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Publication Date</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                </div>
                                                <input type="date" name="publication_date" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-toggle-on"></i></span>
                                                </div>
                                                <select name="status" class="form-control">
                                                    <option value="draft">Draft</option>
                                                    <option value="published">Published</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
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
                                                    $cat_stmt = $pdo->query("SELECT * FROM categories WHERE type = 'blog'");
                                                    while ($cat = $cat_stmt->fetch()) {
                                                        echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 <div class="form-group">
                                     <label>Tags (comma separated)</label>
                                     <div class="input-group">
                                         <div class="input-group-prepend">
                                             <span class="input-group-text"><i class="fas fa-tags"></i></span>
                                         </div>
                                         <input type="text" name="tags" class="form-control">
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
                                         <small class="form-text text-muted">60 characters max. Leave empty to use blog title.</small>
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
                                             <button type="button" class="btn btn-outline-secondary btn-xs ml-2" onclick="addArticleSchema()">Article</button>
                                             <button type="button" class="btn btn-outline-secondary btn-xs ml-1" onclick="addBreadcrumbSchema()">Breadcrumb</button>
                                             <button type="button" class="btn btn-outline-secondary btn-xs ml-1" onclick="addFAQSchema()">FAQ</button>
                                         </div>
                                     </div>
                                     <input type="hidden" name="schemas" id="schemasInput">
                                 </div>

                                 <div class="form-group">
                                     <label>Featured Image</label>
                                     <input type="file" name="featured_image" class="form-control">
                                     <div id="currentImage"></div>
                                 </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="$('#blogModal').modal('hide')">Cancel</button>
                            <button type="submit" form="blogForm" class="btn btn-success">Save Blog</button>
                        </div>
                    </div>
                </div>
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
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script>
        let blogEditor;

        function initBlogEditor() {
            if (blogEditor) {
                return;
            }

            blogEditor = new Quill('#contentEditor', {
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

        function setBlogEditorHtml(html) {
            initBlogEditor();
            blogEditor.clipboard.dangerouslyPasteHTML(html || '');
            $('#content').val(html || '');
        }

        function syncBlogEditorToField() {
            initBlogEditor();
            $('#content').val(blogEditor.root.innerHTML);
        }

        function openAddModal() {
            $('#blogForm')[0].reset();
            $('#blogId').val('');
            $('#currentImage').html('');
            $('#blogModalLabel').text('Add New Blog');
            $('#blogModal').modal('show');
            setBlogEditorHtml('');
            $('input[name="publication_date"]').val(new Date().toISOString().split('T')[0]);
            $('select[name="status"]').val('draft');
        }

        function openEditModal(id) {
            $.get('get_blog.php?id=' + id, function(data) {
                try {
                    var blog = JSON.parse(data);
                    $('#blogId').val(blog.id);
                    $('input[name="title"]').val(blog.title);
                    $('input[name="slug"]').val(blog.slug);
                    $('#content').val(blog.content || '');
                    $('textarea[name="excerpt"]').val(blog.excerpt);
                    $('input[name="author"]').val(blog.author);
                    $('input[name="publication_date"]').val(blog.publication_date);
                    $('select[name="status"]').val(blog.status);
                    $('select[name="category_id"]').val(blog.category_id);
                    $('input[name="tags"]').val(blog.tags ? JSON.parse(blog.tags).join(', ') : '');
                    $('input[name="meta_title"]').val(blog.meta_title || '');
                    $('textarea[name="meta_description"]').val(blog.meta_description || '');
                    $('input[name="meta_keywords"]').val(blog.meta_keywords || '');

                    // Load schemas
                    const savedSchemas = blog.schemas || blog.schema_markup || blog.schema || '';
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
                    if (blog.featured_image) {
                        $('#currentImage').html('<img src="../assets/img/blog/' + blog.featured_image + '" width="100" class="mt-2 border rounded">');
                    } else {
                        $('#currentImage').html('');
                    }
                    $('#blogModalLabel').text('Edit Blog');
                    $('#blogModal').modal('show');
                    setBlogEditorHtml(blog.content || '');
                } catch(e) {
                    alert('Error parsing blog data: ' + e.message);
                }
            }).fail(function() {
                alert('Error loading blog data.');
            });
        }

        function updateForm() {
            syncBlogEditorToField();
            return true;
        }

        // Auto-generate slug from title
        $(document).ready(function() {
            initBlogEditor();

            $('input[name="title"]').on('input', function() {
                var title = $(this).val();
                var slug = title.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('input[name="slug"]').val(slug);
            });

            // Image preview on change
            $('input[name="featured_image"]').on('change', function() {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#currentImage').html('<img src="' + e.target.result + '" width="100" class="mt-2 border rounded"> <p>New image selected</p>');
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#currentImage').html('');
                }
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

        // Schema functionality
        let schemaCounter = 0;
        let schemas = [];

        function addSchema(type = 'custom', data = null) {
            const schemaId = schemaCounter++;
            let schemaHtml = '';

            if (type === 'article') {
                schemaHtml = createArticleSchema(schemaId);
            } else if (type === 'breadcrumb') {
                schemaHtml = createBreadcrumbSchema(schemaId);
            } else if (type === 'faq') {
                schemaHtml = createFAQSchema(schemaId);
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
  "@type": "Article",
  "headline": "${data?.title || 'Article Title'}",
  "description": "${data?.description || 'Article description'}",
  "author": {
    "@type": "Person",
    "name": "${data?.author || 'Author Name'}"
  },
  "datePublished": "${data?.datePublished || new Date().toISOString().split('T')[0]}",
  "publisher": {
    "@type": "Organization",
    "name": "India Day Trip"
  }
}`;
            return `
<div class="schema-item" id="schemaItem${id}" style="border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; margin-bottom: 10px; background: white;">
    <div class="schema-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <select id="schemaType${id}" class="form-control form-control-sm" style="width: 150px;" onchange="changeSchemaType(${id}, this.value)">
            <option value="custom">Custom JSON-LD</option>
            <option value="article">Article</option>
            <option value="breadcrumb">Breadcrumb</option>
            <option value="faq">FAQ</option>
            <option value="organization">Organization</option>
            <option value="website">Website</option>
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

        function createArticleSchema(id) {
            const currentDate = new Date().toISOString().split('T')[0];
            return `
<div class="schema-item" id="schemaItem${id}" style="border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; margin-bottom: 10px; background: white;">
    <div class="schema-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <strong>Article Schema</strong>
        <button type="button" class="btn btn-danger btn-sm" onclick="removeSchema(${id})">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Headline</label>
                <input type="text" id="articleHeadline${id}" class="form-control" placeholder="Article headline">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Author</label>
                <input type="text" id="articleAuthor${id}" class="form-control" placeholder="Author name">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea id="articleDescription${id}" class="form-control" rows="2" placeholder="Article description"></textarea>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Publication Date</label>
                <input type="date" id="articleDate${id}" class="form-control" value="${currentDate}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Image URL</label>
                <input type="url" id="articleImage${id}" class="form-control" placeholder="https://example.com/image.jpg">
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-primary btn-sm" onclick="generateArticleSchema(${id})">
        <i class="fas fa-magic"></i> Generate JSON-LD
    </button>
    <textarea id="schemaJson${id}" class="form-control mt-2" rows="8" style="display: none;"></textarea>
</div>`;
        }

        function createBreadcrumbSchema(id) {
            return `
<div class="schema-item" id="schemaItem${id}" style="border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; margin-bottom: 10px; background: white;">
    <div class="schema-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <strong>Breadcrumb Schema</strong>
        <button type="button" class="btn btn-danger btn-sm" onclick="removeSchema(${id})">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div id="breadcrumbItems${id}">
        <div class="breadcrumb-item" style="display: flex; align-items: center; margin-bottom: 10px;">
            <input type="text" class="form-control form-control-sm" placeholder="Name" style="margin-right: 10px; flex: 1;">
            <input type="url" class="form-control form-control-sm" placeholder="URL" style="margin-right: 10px; flex: 2;">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeBreadcrumbItem(this)">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addBreadcrumbItem(${id})">
        <i class="fas fa-plus"></i> Add Item
    </button>
    <button type="button" class="btn btn-primary btn-sm ml-2" onclick="generateBreadcrumbSchema(${id})">
        <i class="fas fa-magic"></i> Generate JSON-LD
    </button>
    <textarea id="schemaJson${id}" class="form-control mt-2" rows="6" style="display: none;"></textarea>
</div>`;
        }

        function createFAQSchema(id) {
            return `
<div class="schema-item" id="schemaItem${id}" style="border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; margin-bottom: 10px; background: white;">
    <div class="schema-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <strong>FAQ Schema</strong>
        <button type="button" class="btn btn-danger btn-sm" onclick="removeSchema(${id})">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div id="faqItems${id}">
        <div class="faq-item" style="border: 1px solid #f0f0f0; border-radius: 4px; padding: 10px; margin-bottom: 10px;">
            <input type="text" class="form-control form-control-sm mb-2" placeholder="Question">
            <textarea class="form-control form-control-sm" rows="2" placeholder="Answer"></textarea>
            <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeFaqItem(this)">
                <i class="fas fa-minus"></i> Remove
            </button>
        </div>
    </div>
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addFaqItem(${id})">
        <i class="fas fa-plus"></i> Add FAQ
    </button>
    <button type="button" class="btn btn-primary btn-sm ml-2" onclick="generateFAQSchema(${id})">
        <i class="fas fa-magic"></i> Generate JSON-LD
    </button>
    <textarea id="schemaJson${id}" class="form-control mt-2" rows="8" style="display: none;"></textarea>
</div>`;
        }

        function addArticleSchema() {
            addSchema('article');
        }

        function addBreadcrumbSchema() {
            addSchema('breadcrumb');
        }

        function addFAQSchema() {
            addSchema('faq');
        }

        function removeSchema(id) {
            $(`#schemaItem${id}`).remove();
            schemas = schemas.filter(s => s.id !== id);
            updateSchemasInput();
        }

        function changeSchemaType(id, type) {
            // This could be enhanced to convert between schema types
            console.log('Schema type changed to:', type);
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

        function generateArticleSchema(id) {
            const headline = $(`#articleHeadline${id}`).val() || 'Article Headline';
            const author = $(`#articleAuthor${id}`).val() || 'Author Name';
            const description = $(`#articleDescription${id}`).val() || 'Article description';
            const date = $(`#articleDate${id}`).val() || new Date().toISOString().split('T')[0];
            const image = $(`#articleImage${id}`).val() || '';

            const schema = {
                "@context": "https://schema.org",
                "@type": "Article",
                "headline": headline,
                "description": description,
                "author": {
                    "@type": "Person",
                    "name": author
                },
                "datePublished": date,
                "publisher": {
                    "@type": "Organization",
                    "name": "India Day Trip"
                }
            };

            if (image) {
                schema.image = image;
            }

            $(`#schemaJson${id}`).val(JSON.stringify(schema, null, 2)).show();
            updateSchemasInput();
        }

        function addBreadcrumbItem(id) {
            const itemHtml = `
<div class="breadcrumb-item" style="display: flex; align-items: center; margin-bottom: 10px;">
    <input type="text" class="form-control form-control-sm" placeholder="Name" style="margin-right: 10px; flex: 1;">
    <input type="url" class="form-control form-control-sm" placeholder="URL" style="margin-right: 10px; flex: 2;">
    <button type="button" class="btn btn-danger btn-sm" onclick="removeBreadcrumbItem(this)">
        <i class="fas fa-minus"></i>
    </button>
</div>`;
            $(`#breadcrumbItems${id}`).append(itemHtml);
        }

        function removeBreadcrumbItem(button) {
            $(button).closest('.breadcrumb-item').remove();
        }

        function generateBreadcrumbSchema(id) {
            const items = [];
            $(`#breadcrumbItems${id} .breadcrumb-item`).each(function(index) {
                const name = $(this).find('input[type="text"]').val();
                const url = $(this).find('input[type="url"]').val();
                if (name && url) {
                    items.push({
                        "@type": "ListItem",
                        "position": index + 1,
                        "name": name,
                        "item": url
                    });
                }
            });

            const schema = {
                "@context": "https://schema.org",
                "@type": "BreadcrumbList",
                "itemListElement": items
            };

            $(`#schemaJson${id}`).val(JSON.stringify(schema, null, 2)).show();
            updateSchemasInput();
        }

        function addFaqItem(id) {
            const itemHtml = `
<div class="faq-item" style="border: 1px solid #f0f0f0; border-radius: 4px; padding: 10px; margin-bottom: 10px;">
    <input type="text" class="form-control form-control-sm mb-2" placeholder="Question">
    <textarea class="form-control form-control-sm" rows="2" placeholder="Answer"></textarea>
    <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeFaqItem(this)">
        <i class="fas fa-minus"></i> Remove
    </button>
</div>`;
            $(`#faqItems${id}`).append(itemHtml);
        }

        function removeFaqItem(button) {
            $(button).closest('.faq-item').remove();
        }

        function generateFAQSchema(id) {
            const faqs = [];
            $(`#faqItems${id} .faq-item`).each(function() {
                const question = $(this).find('input[type="text"]').val();
                const answer = $(this).find('textarea').val();
                if (question && answer) {
                    faqs.push({
                        "@type": "Question",
                        "name": question,
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": answer
                        }
                    });
                }
            });

            const schema = {
                "@context": "https://schema.org",
                "@type": "FAQPage",
                "mainEntity": faqs
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
            if (typeof blog !== 'undefined' && blog.schemas) {
                try {
                    const existingSchemas = JSON.parse(blog.schemas);
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