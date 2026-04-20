<?php require_once '../config.php'; ?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="author" content="India Day Trip">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <?php renderSEOHead('golden_triangle_tours'); ?>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://indiadaytrip.com/golden-triangle-tours/">
    <meta property="og:title" content="Golden Triangle Tours - India Day Trip | Delhi Agra Jaipur Tours">
    <meta property="og:description"
        content="Explore the Golden Triangle of India with our comprehensive tours covering Delhi, Agra, and Jaipur. Experience the best of India's heritage and culture.">
    <meta property="og:image" content="https://indiadaytrip.com/assets/img/destination/d-delhi.webp">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://indiadaytrip.com/golden-triangle-tours/">
    <meta property="twitter:title" content="Golden Triangle Tours - India Day Trip | Delhi Agra Jaipur Tours">
    <meta property="twitter:description"
        content="Explore the Golden Triangle of India with our comprehensive tours covering Delhi, Agra, and Jaipur. Experience the best of India's heritage and culture.">
    <meta property="twitter:image" content="https://indiadaytrip.com/assets/img/destination/d-delhi.webp">

    <?php
require_once '../config.php';
$search = $_GET['s'] ?? null;
$orderby = $_GET['orderby'] ?? null;
$categoryTours = getTours('Golden Triangle Tours', null, null, $search, $orderby);
?>

    <?php include '../components/links.php'; ?>
</head>

<body>
    <?php include '../components/preloader.php'; ?>
    <?php include '../components/sidebar.php'; ?>
    <?php include '../components/header.php'; ?>

    <div class="breadcumb-wrapper" data-bg-src="../assets/img/bg/breadcumb-bg.webp">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Golden Triangle Tours</h1>
                <ul class="breadcumb-menu">
                    <li><a href="../index.php">Home</a></li>
                    <li>Golden Triangle Tours</li>
                </ul>
            </div>
        </div>
    </div>
    <section class="space">
        <div class="container">
            <div class="th-sort-bar">
                <div class="row justify-content-between align-items-center">
                    <div class="col-md-4">
                        <div class="search-form-area">
                            <form class="search-form" method="get">
                                <input type="text" name="s" placeholder="Search Golden Triangle Tours"
                                    value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                <input type="hidden" name="orderby"
                                    value="<?php echo htmlspecialchars($orderby ?? ''); ?>">
                                <button type="submit">
                                    <i class="fa-light fa-magnifying-glass"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-auto">
                        <div class="sorting-filter-wrap">

                            <form class="woocommerce-ordering" method="get">
                                <input type="hidden" name="s" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                <select name="orderby" class="orderby" aria-label="destination order"
                                    onchange="this.form.submit()">
                                    <option value="menu_order" <?php echo($orderby == 'menu_order' || !$orderby) ? 'selected' : ''; ?>>Default Sorting</option>
                                    <option value="popularity" <?php echo($orderby == 'popularity') ? 'selected' : ''; ?>>Sort by popularity</option>
                                    <option value="rating" <?php echo($orderby == 'rating') ? 'selected' : ''; ?>>Sort by
                                        average rating</option>
                                    <option value="date" <?php echo($orderby == 'date') ? 'selected' : ''; ?>>Sort by
                                        latest</option>
                                    <option value="price" <?php echo($orderby == 'price') ? 'selected' : ''; ?>>Sort by
                                        price: low to high</option>
                                    <option value="price-desc" <?php echo($orderby == 'price-desc') ? 'selected' : ''; ?>>Sort by price: high to low</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade active show" id="tab-grid" role="tabpanel"
                            aria-labelledby="tab-tour-grid">
                            <div class="row gy-24 gx-24">
                                <?php foreach ($categoryTours as $tour): ?>
                                    <div class="col-xxl-4 col-lg-4 col-md-6 mb-4"><?php echo renderTourCard($tour); ?></div>
                                <?php
endforeach; ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-list" role="tabpanel" aria-labelledby="tab-tour-list">
                            <div class="row gy-30">
                                <?php foreach ($categoryTours as $tour): ?>
                                    <div class="col-12"><?php echo renderTourCard($tour); ?></div>
                                <?php
endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="shape-mockup shape1 d-none d-xxl-block" data-bottom="7%" data-right="-8%">
                <img src="../assets/img/shape/shape_1.webp" alt="shape">
            </div>
            <div class="shape-mockup shape2 d-none d-xl-block" data-bottom="1%" data-right="-7%">
                <img src="../assets/img/shape/shape_2.webp" alt="shape">
            </div>
            <div class="shape-mockup shape3 d-none d-xxl-block" data-bottom="-2%" data-right="-12%">
                <img src="../assets/img/shape/shape_3.webp" alt="shape">
            </div>
        </div>
    </section>

    <?php include '../components/footer.php'; ?>

    <?php include '../components/script.php'; ?>
</body>

</html>