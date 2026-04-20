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

    <!-- Blog Detail Section -->
    <section class="space-top space-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <article class="blog-post">
                        <?php if ($blog['featured_image']): ?>
                            <div class="blog-featured-image">
                                <img src="assets/img/blog/<?php echo $blog['featured_image']; ?>"
                                    alt="<?php echo htmlspecialchars($blog['title']); ?>" class="img-fluid">
                            </div>
                        <?php endif; ?>

                        <header class="blog-header">
                            <h1 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h1>

                            <div class="blog-meta">
                                <span class="author"><i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($blog['author']); ?></span>
                                <span class="date"><i class="fas fa-calendar"></i>
                                    <?php echo date('F j, Y', strtotime($blog['publication_date'])); ?></span>
                                <span class="views"><i class="fas fa-eye"></i> <?php echo $blog['view_count']; ?>
                                    views</span>
                            </div>
                        </header>

                        <div class="blog-content">
                            <?php echo $blog['content']; ?>
                        </div>

                        <?php if ($blog['tags']): ?>
                            <div class="blog-tags">
                                <h4>Tags:</h4>
                                <?php
                                $tags = json_decode($blog['tags'], true);
                                foreach ($tags as $tag): ?>
                                    <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($blog['category_name'])): ?>
                            <div class="blog-categories">
                                <h4>Category:</h4>
                                <span class="category"><?php echo htmlspecialchars($blog['category_name']); ?></span>
                            </div>
                        <?php endif; ?>
                    </article>
                </div>

                <div class="col-lg-4">
                    <div class="blog-sidebar">
                        <div class="author-info">
                            <h4>About the Author</h4>
                            <p><strong><?php echo htmlspecialchars($blog['author']); ?></strong></p>
                            <p>Travel enthusiast and content creator at India Day Trip.</p>
                        </div>

                        <div class="recent-posts">
                            <h4>Recent Posts</h4>
                            <?php
                            $stmt = $pdo->query("SELECT title, slug FROM blogs WHERE id != {$blog['id']} ORDER BY created_at DESC LIMIT 5");
                            while ($recent = $stmt->fetch()): ?>
                                <div class="recent-post-item">
                                    <a
                                        href="blog/<?php echo $recent['slug']; ?>"><?php echo htmlspecialchars($recent['title']); ?></a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>

    <?php include 'components/script.php'; ?>
</body>

</html>