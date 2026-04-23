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
            <div class="row gx-24 gy-30">
                <?php
                // Get all published blogs
                $blogs = getBlogs(null, null, 'published');

                if (empty($blogs)): ?>
                    <div class="col-12">
                        <p class="text-center">No blog posts available yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($blogs as $blog): ?>
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <div class="blog-grid2 th-ani">
                                <div class="blog-img global-img">
                                    <img src="../assets/img/blog/<?php echo $blog['featured_image'] ?: 'blog-tour.webp'; ?>" 
                                         alt="<?php echo htmlspecialchars($blog['title']); ?>">
                                </div>
                                <div class="blog-grid2_content">
                                    <div class="blog-meta">
                                        <span><?php echo date('M d, Y', strtotime($blog['publication_date'])); ?></span>
                                        <span><?php $wordCount = str_word_count(strip_tags($blog['content']));
    echo ceil($wordCount / 200); ?> min read</span>
                                    </div>
                                    <h3 class="box-title">
                                        <a href="<?php echo $blog['slug']; ?>">
                                            <?php echo htmlspecialchars($blog['title']); ?>
                                        </a>
                                    </h3>
                                    <a href="<?php echo $blog['slug']; ?>" 
                                       class="th-btn style4 th-icon">Read More</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include '../components/footer.php'; ?>
    <?php include '../components/script.php'; ?>
</body>

</html>