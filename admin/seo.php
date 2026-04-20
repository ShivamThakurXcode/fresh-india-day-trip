<?php
session_start();
require_once '../config.php';
checkAdminLogin();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // SECURITY: Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_message = 'Security validation failed. Please try again.';
    } else {
        $page_key = $_POST['page_key'] ?? '';
        if ($page_key) {
            $seo_data = [
                'page_title' => $_POST['page_title'] ?? '',
                'meta_description' => $_POST['meta_description'] ?? '',
                'meta_keywords' => $_POST['meta_keywords'] ?? '',
                'canonical_url' => $_POST['canonical_url'] ?? '',
                'robots_meta' => $_POST['robots_meta'] ?? 'INDEX,FOLLOW',
                'og_title' => $_POST['og_title'] ?? '',
                'og_description' => $_POST['og_description'] ?? '',
                'og_image' => $_POST['og_image'] ?? '',
                'og_type' => $_POST['og_type'] ?? 'website',
                'twitter_title' => $_POST['twitter_title'] ?? '',
                'twitter_description' => $_POST['twitter_description'] ?? '',
                'twitter_image' => $_POST['twitter_image'] ?? '',
                'twitter_card' => $_POST['twitter_card'] ?? 'summary_large_image',
                'schema_markup' => $_POST['schema_markup'] ?? ''
            ];

            if (setPageSEO($page_key, $seo_data)) {
                $success_message = "SEO settings updated successfully for page: " . htmlspecialchars($page_key);
            } else {
                $error_message = "Failed to update SEO settings.";
            }
        }
    }
}

// Get all page SEO data
$pages_seo = getAllPageSEO();

// Page key to edit (from GET parameter)
$edit_page = $_GET['edit'] ?? null;
$current_seo = null;
if ($edit_page) {
    $current_seo = getPageSEO($edit_page);
    if (!$current_seo) {
        $current_seo = [
            'page_key' => $edit_page,
            'page_title' => '',
            'meta_description' => '',
            'meta_keywords' => '',
            'canonical_url' => '',
            'robots_meta' => 'INDEX,FOLLOW',
            'og_title' => '',
            'og_description' => '',
            'og_image' => '',
            'og_type' => 'website',
            'twitter_title' => '',
            'twitter_description' => '',
            'twitter_image' => '',
            'twitter_card' => 'summary_large_image',
            'schema_markup' => ''
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Management - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/magnific-popup.min.css">
    <link rel="stylesheet" href="../assets/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .seo-table th, .seo-table td {
            vertical-align: middle;
        }
        .seo-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .seo-status.complete {
            background-color: #d4edda;
            color: #155724;
        }
        .seo-status.incomplete {
            background-color: #f8d7da;
            color: #721c24;
        }
        .seo-form .form-group {
            margin-bottom: 15px;
        }
        .seo-form label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .schema-preview {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            font-family: monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>
    <div class="main-content">
        <div class="content-wrapper">
            <h2 class="page-title">SEO Management</h2>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Open Page Key</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-9">
                            <label for="edit" class="form-label">Page Key</label>
                            <input type="text" class="form-control" id="edit" name="edit" placeholder="home, contact, tour_listing, blog_listing" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Open</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($edit_page): ?>
                <!-- Edit Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Edit SEO for: <?php echo htmlspecialchars($edit_page); ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="seo-form">
                            <input type="hidden" name="page_key" value="<?php echo htmlspecialchars($edit_page); ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="page_title">Page Title</label>
                                        <input type="text" class="form-control" id="page_title" name="page_title"
                                               value="<?php echo htmlspecialchars($current_seo['page_title'] ?? ''); ?>"
                                               placeholder="Enter page title">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="canonical_url">Canonical URL</label>
                                        <input type="url" class="form-control" id="canonical_url" name="canonical_url"
                                               value="<?php echo htmlspecialchars($current_seo['canonical_url'] ?? ''); ?>"
                                               placeholder="https://example.com/page">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="meta_description">Meta Description</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="3"
                                          placeholder="Enter meta description"><?php echo htmlspecialchars($current_seo['meta_description'] ?? ''); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="meta_keywords">Meta Keywords</label>
                                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                       value="<?php echo htmlspecialchars($current_seo['meta_keywords'] ?? ''); ?>"
                                       placeholder="keyword1, keyword2, keyword3">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="robots_meta">Robots Meta</label>
                                        <select class="form-control" id="robots_meta" name="robots_meta">
                                            <option value="INDEX,FOLLOW" <?php echo ($current_seo['robots_meta'] ?? '') === 'INDEX,FOLLOW' ? 'selected' : ''; ?>>INDEX,FOLLOW</option>
                                            <option value="NOINDEX,FOLLOW" <?php echo ($current_seo['robots_meta'] ?? '') === 'NOINDEX,FOLLOW' ? 'selected' : ''; ?>>NOINDEX,FOLLOW</option>
                                            <option value="INDEX,NOFOLLOW" <?php echo ($current_seo['robots_meta'] ?? '') === 'INDEX,NOFOLLOW' ? 'selected' : ''; ?>>INDEX,NOFOLLOW</option>
                                            <option value="NOINDEX,NOFOLLOW" <?php echo ($current_seo['robots_meta'] ?? '') === 'NOINDEX,NOFOLLOW' ? 'selected' : ''; ?>>NOINDEX,NOFOLLOW</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="og_type">Open Graph Type</label>
                                        <select class="form-control" id="og_type" name="og_type">
                                            <option value="website" <?php echo ($current_seo['og_type'] ?? '') === 'website' ? 'selected' : ''; ?>>Website</option>
                                            <option value="article" <?php echo ($current_seo['og_type'] ?? '') === 'article' ? 'selected' : ''; ?>>Article</option>
                                            <option value="product" <?php echo ($current_seo['og_type'] ?? '') === 'product' ? 'selected' : ''; ?>>Product</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <h6 class="mt-4 mb-3">Open Graph Meta Tags</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="og_title">OG Title</label>
                                        <input type="text" class="form-control" id="og_title" name="og_title"
                                               value="<?php echo htmlspecialchars($current_seo['og_title'] ?? ''); ?>"
                                               placeholder="Open Graph title">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="og_image">OG Image URL</label>
                                        <input type="url" class="form-control" id="og_image" name="og_image"
                                               value="<?php echo htmlspecialchars($current_seo['og_image'] ?? ''); ?>"
                                               placeholder="https://example.com/image.jpg">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="og_description">OG Description</label>
                                <textarea class="form-control" id="og_description" name="og_description" rows="2"
                                          placeholder="Open Graph description"><?php echo htmlspecialchars($current_seo['og_description'] ?? ''); ?></textarea>
                            </div>

                            <h6 class="mt-4 mb-3">Twitter Meta Tags</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="twitter_card">Twitter Card Type</label>
                                        <select class="form-control" id="twitter_card" name="twitter_card">
                                            <option value="summary_large_image" <?php echo ($current_seo['twitter_card'] ?? '') === 'summary_large_image' ? 'selected' : ''; ?>>Summary Large Image</option>
                                            <option value="summary" <?php echo ($current_seo['twitter_card'] ?? '') === 'summary' ? 'selected' : ''; ?>>Summary</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="twitter_image">Twitter Image URL</label>
                                        <input type="url" class="form-control" id="twitter_image" name="twitter_image"
                                               value="<?php echo htmlspecialchars($current_seo['twitter_image'] ?? ''); ?>"
                                               placeholder="https://example.com/image.jpg">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="twitter_title">Twitter Title</label>
                                        <input type="text" class="form-control" id="twitter_title" name="twitter_title"
                                               value="<?php echo htmlspecialchars($current_seo['twitter_title'] ?? ''); ?>"
                                               placeholder="Twitter title">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="twitter_description">Twitter Description</label>
                                        <input type="text" class="form-control" id="twitter_description" name="twitter_description"
                                               value="<?php echo htmlspecialchars($current_seo['twitter_description'] ?? ''); ?>"
                                               placeholder="Twitter description">
                                    </div>
                                </div>
                            </div>

                            <h6 class="mt-4 mb-3">Schema.org Structured Data (JSON-LD)</h6>
                            <div class="form-group">
                                <label for="schema_markup">Schema Markup (JSON)</label>
                                <textarea class="form-control" id="schema_markup" name="schema_markup" rows="8"
                                          placeholder='{"@context": "https://schema.org", "@type": "WebPage", ...}'><?php echo htmlspecialchars($current_seo['schema_markup'] ?? ''); ?></textarea>
                                <small class="form-text text-muted">Enter valid JSON-LD structured data for rich snippets</small>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Update SEO Settings</button>
                                <a href="seo.php" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Pages Overview Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Page SEO Overview</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped seo-table">
                            <thead>
                                <tr>
                                    <th>Page Key</th>
                                    <th>Page Title</th>
                                    <th>Meta Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pages_seo as $page): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($page['page_key']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($page['page_title'] ?? '', 0, 50)); ?><?php echo strlen($page['page_title'] ?? '') > 50 ? '...' : ''; ?></td>
                                        <td><?php echo htmlspecialchars(substr($page['meta_description'] ?? '', 0, 50)); ?><?php echo strlen($page['meta_description'] ?? '') > 50 ? '...' : ''; ?></td>
                                        <td>
                                            <?php
                                            $has_title = !empty($page['page_title']);
                                            $has_desc = !empty($page['meta_description']);
                                            $has_og = !empty($page['og_title']) && !empty($page['og_description']);
                                            $status = $has_title && $has_desc && $has_og ? 'complete' : 'incomplete';
                                            ?>
                                            <span class="seo-status <?php echo $status; ?>">
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="seo.php?edit=<?php echo urlencode($page['page_key']); ?>" class="btn btn-sm btn-primary">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($pages_seo)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No SEO data found. Please run the database migration.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        // Auto-fill OG and Twitter fields if empty
        document.getElementById('page_title')?.addEventListener('input', function() {
            const title = this.value;
            if (!document.getElementById('og_title').value) {
                document.getElementById('og_title').value = title;
            }
            if (!document.getElementById('twitter_title').value) {
                document.getElementById('twitter_title').value = title;
            }
        });

        document.getElementById('meta_description')?.addEventListener('input', function() {
            const desc = this.value;
            if (!document.getElementById('og_description').value) {
                document.getElementById('og_description').value = desc;
            }
            if (!document.getElementById('twitter_description').value) {
                document.getElementById('twitter_description').value = desc.substring(0, 200);
            }
        });
    </script>
</body>
</html>