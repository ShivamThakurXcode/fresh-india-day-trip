<?php require_once '../config.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="India Day Trip">
    <?php renderSEOHead('blog_listing'); ?>
    <meta property="twitter:url" content="https://indiadaytrip.com/blog/">
    <meta property="twitter:title" content="Blog - India Day Trip">
    <meta property="twitter:description"
        content="Read travel tips, guides, and stories about Taj Mahal Tours, Golden Triangle, and India travel. Expert advice from India Day Trip.">
    <meta property="twitter:image" content="https://indiadaytrip.com/assets/img/logo/logo-header.webp">
    <?php include '../components/links.php'; ?>
</head>

<body>
    <?php include '../components/header.php'; ?>

    <!-- Breadcrumb -->
    <div class="breadcumb-wrapper" data-bg-src="../assets/img/bg/breadcumb-bg.webp">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Blog</h1>
                <ul class="breadcumb-menu">
                    <li><a href="../index.php">Home</a></li>
                    <li>Blog</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Blog Area -->
    <section class="space">
        <div class="container">
            <div class="row">
                <?php
                // Get all published blogs
                $blogs = getBlogs(null, null, 'published');

                if (empty($blogs)): ?>
                    <div class="col-12">
                        <p class="text-center">No blog posts available yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($blogs as $blog): ?>
                        <?php echo renderBlogCard($blog, 'grid', '../blog-detail.php?slug='); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include '../components/footer.php'; ?>
    <?php include '../components/script.php'; ?>
</body>

</html>