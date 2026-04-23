<?php
require_once 'config.php';
require_once 'functions.php';

// Get blog slug from URL
$slug = $_GET['slug'] ?? null;

if (!$slug) {
    header('Location: index.php');
    exit;
}

// Fetch blog by slug
$stmt = $pdo->prepare("SELECT b.*, c.name as category_name FROM blogs b LEFT JOIN categories c ON b.category_id = c.id WHERE b.slug = ? AND b.status = 'published'");
$stmt->execute([$slug]);
$blog = $stmt->fetch();

if (!$blog) {
    header('Location: 404.php');
    exit;
}

// Update view count
$pdo->prepare("UPDATE blogs SET view_count = view_count + 1 WHERE id = ?")->execute([$blog['id']]);

// Get recent blogs
$recentBlogs = getBlogs(null, 5);
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="author" content="India Day Trip">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <?php renderBlogSEOHead($blog); ?>

    <!-- Default Blog Article Schema if no custom schema -->
    <?php if (empty($blog['schema_markup'])): ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "<?php echo htmlspecialchars($blog['title']); ?>",
        "description": "<?php echo htmlspecialchars(substr(strip_tags($blog['content']), 0, 160)); ?>",
        "url": "https://indiadaytrip.com/blog/<?php echo $blog['slug']; ?>",
        "datePublished": "<?php echo $blog['created_at']; ?>",
        "dateModified": "<?php echo $blog['updated_at'] ?? $blog['created_at']; ?>",
        "author": {
            "@type": "Organization",
            "name": "India Day Trip"
        },
        "publisher": {
            "@type": "Organization",
            "name": "India Day Trip",
            "logo": {
                "@type": "ImageObject",
                "url": "https://indiadaytrip.com/assets/img/logo/logo-header.webp"
            }
        }
    }
    </script>
    <?php endif; ?>

    <?php include 'components/links.php'; ?>
</head>

<body>
    <?php include 'components/preloader.php'; ?>
    <?php include 'components/sidebar.php'; ?>

    <?php include 'components/header.php'; ?>

    <!-- Breadcrumb -->
    <div class="breadcumb-wrapper" data-bg-src="assets/img/bg/breadcumb-bg.webp">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title"><?php echo htmlspecialchars($blog['title']); ?></h1>
                <ul class="breadcumb-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="blog/index.php">Blog</a></li>
                    <li><?php echo htmlspecialchars($blog['title']); ?></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Blog Detail Section -->
    <section class="blog-detail-section space-top pb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <article class="blog-detail-content">
                        <?php if ($blog['featured_image']): ?>
                            <div class="blog-featured-image mb-5">
                                <img src="/assets/img/blog/<?php echo $blog['featured_image']; ?>"
                                    alt="<?php echo htmlspecialchars($blog['title']); ?>">
                            </div>
                        <?php endif; ?>

                        <header class="blog-header mb-4">
                            <h1 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h1>

                            <div class="blog-meta">
                                <span class="meta-item">
                                    <i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($blog['author']); ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('F j, Y', strtotime($blog['publication_date'])); ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <?php $wordCount = str_word_count(strip_tags($blog['content'])); echo ceil($wordCount / 200); ?> min read
                                </span>
                            </div>
                        </header>

                        <div class="blog-content">
                            <?php echo $blog['content']; ?>
                        </div>

                        <footer class="blog-footer mt-5 pt-5 border-top">
                            <?php if ($blog['tags']): ?>
                                <div class="blog-tags mb-4">
                                    <div class="tags-list">
                                        <?php
                                        $tags = json_decode($blog['tags'], true);
                                        foreach ($tags as $tag): ?>
                                            <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($blog['category_name'])): ?>
                                <div class="blog-category mb-4">
                                    <span class="category"><?php echo htmlspecialchars($blog['category_name']); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="blog-navigation">
                                <div class="nav-links">
                                    <?php
                                    // Get previous blog
                                    $prevStmt = $pdo->prepare("SELECT slug, title FROM blogs WHERE publication_date < ? AND status = 'published' ORDER BY publication_date DESC LIMIT 1");
                                    $prevStmt->execute([$blog['publication_date']]);
                                    $prevBlog = $prevStmt->fetch();
                                    
                                    // Get next blog
                                    $nextStmt = $pdo->prepare("SELECT slug, title FROM blogs WHERE publication_date > ? AND status = 'published' ORDER BY publication_date ASC LIMIT 1");
                                    $nextStmt->execute([$blog['publication_date']]);
                                    $nextBlog = $nextStmt->fetch();
                                    ?>
                                    
                                    <?php if ($prevBlog): ?>
                                        <div class="nav-link prev">
                                            <a href="blog/<?php echo $prevBlog['slug']; ?>">
                                                <i class="fas fa-arrow-left"></i>
                                                <span>Previous: <?php echo htmlspecialchars($prevBlog['title']); ?></span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($nextBlog): ?>
                                        <div class="nav-link next">
                                            <a href="blog/<?php echo $nextBlog['slug']; ?>">
                                                <span>Next: <?php echo htmlspecialchars($nextBlog['title']); ?></span>
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                                                    </footer>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* Blog Detail Page Styles - Clean & Minimal */
        .blog-detail-section {
            background: #ffffff;
            padding: 60px 0;
        }
        
        .blog-detail-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .blog-featured-image img {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .blog-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .blog-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
            font-size: 0.95rem;
        }
        
        .meta-item i {
            color: #1CA8CB;
        }
        
        .blog-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            margin-bottom: 40px;
        }
        
        .blog-content h2,
        .blog-content h3,
        .blog-content h4 {
            color: #2c3e50;
            margin-top: 40px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .blog-content p {
            margin-bottom: 25px;
        }
        
        .blog-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 30px 0;
        }
        
        .blog-content ul,
        .blog-content ol {
            margin-bottom: 25px;
            padding-left: 30px;
        }
        
        .blog-content li {
            margin-bottom: 10px;
        }
        
        .blog-content blockquote {
            border-left: 4px solid #1CA8CB;
            padding-left: 20px;
            margin: 30px 0;
            font-style: italic;
            color: #6c757d;
        }
        
        .tags-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .tag {
            background: #f8f9fa;
            color: #495057;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            border: 1px solid #e9ecef;
        }
        
        .category {
            background: #e3f2fd;
            color: #1976d2;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .blog-navigation {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e9ecef;
        }
        
        .nav-links {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .nav-link {
            flex: 1;
            max-width: 48%;
        }
        
        .nav-link a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            text-decoration: none;
            color: #495057;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .nav-link a:hover {
            background: #1CA8CB;
            color: #fff;
        }
        
        .nav-link.next a {
            flex-direction: row-reverse;
            text-align: right;
        }
        
        .back-to-blog .btn {
            padding: 12px 30px;
            font-weight: 500;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .back-to-blog .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(28, 168, 203, 0.3);
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .blog-detail-section {
                padding: 40px 0;
            }
            
            .blog-title {
                font-size: 2rem;
            }
            
            .blog-meta {
                gap: 15px;
            }
            
            .nav-links {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-link {
                max-width: 100%;
            }
            
            .nav-link.next a {
                flex-direction: row;
                text-align: left;
            }
        }
        
        @media (max-width: 768px) {
            .blog-detail-section {
                padding: 30px 0;
            }
            
            .blog-title {
                font-size: 1.8rem;
            }
            
            .blog-featured-image img {
                max-height: 300px;
            }
            
            .blog-meta {
                gap: 12px;
                font-size: 0.9rem;
            }
            
            .blog-content {
                font-size: 1rem;
            }
            
            .blog-content h2,
            .blog-content h3,
            .blog-content h4 {
                margin-top: 30px;
            }
        }
        
        @media (max-width: 576px) {
            .blog-title {
                font-size: 1.5rem;
            }
            
            .blog-meta {
                flex-direction: column;
                gap: 8px;
            }
            
            .nav-link a {
                padding: 12px;
                font-size: 0.85rem;
            }
        }
    </style>

    <?php include 'components/footer.php'; ?>

    <?php include 'components/script.php'; ?>
</body>

</html>