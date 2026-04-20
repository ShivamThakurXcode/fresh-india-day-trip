<?php
require_once 'config.php';

// Handle GET data for filters and sorting
$location = $_GET['location'] ?? null;
$tourType = $_GET['tourType'] ?? null;
$duration = $_GET['duration'] ?? null;
$search = $_GET['search'] ?? null;
$orderby = $_GET['orderby'] ?? null;
$rating = $_GET['rating'] ?? null;

// Map tourType to category
$categoryMap = [
    'taj-mahal' => 'Taj Mahal Tours',
    'same-day' => 'Same Day Tours',
    'golden-triangle' => 'Golden Triangle Tours',
    'rajasthan' => 'Rajasthan Tours',
];
$category = isset($categoryMap[$tourType]) ? $categoryMap[$tourType] : null;

// Get tours
$tours = getTours($category, null, null, $search, $orderby, $location, $duration, null, null, $rating);

// Build filter summary
$filters = [];
if ($location)
    $filters[] = "Location: " . ucfirst($location);
if ($category)
    $filters[] = "Type: $category";
if ($duration)
    $filters[] = "Duration: $duration Day" . ($duration > 1 ? 's' : '');
if ($search)
    $filters[] = "Search: $search";
if ($rating)
    $filters[] = "Rating: $rating+ stars";
$filterSummary = implode(', ', $filters);
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Search Tours - India Day Trip</title>
    <meta name="author" content="India Day Trip">
    <meta name="description"
        content="Search and find your perfect tour with India Day Trip. Find Taj Mahal tours, Golden Triangle tours, Same Day tours and more.">
    <meta name="keywords"
        content="Search tours India, Taj Mahal tours, Golden Triangle tours, Same Day tours, Rajasthan tours">
    <meta name="robots" content="INDEX,FOLLOW">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <link rel="canonical" href="https://indiadaytrip.com/search-tours/">

    <?php include 'components/links.php'; ?>
</head>

<body>
    <?php include 'components/preloader.php'; ?>
    <?php include 'components/sidebar.php'; ?>
    <?php include 'components/header.php'; ?>

    <div class="breadcumb-wrapper" data-bg-src="assets/img/bg/breadcumb-bg.webp">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Search Tours</h1>
                <ul class="breadcumb-menu">
                    <li><a href="index.php">Home</a></li>
                    <li>Search Tours</li>
                </ul>
            </div>
        </div>
    </div>

    <section class="space">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="sidebar top-10">
                        <h4 class="sidebar-title">Filter Tours</h4>
                        <form method="GET" action="search-tours.php">
                            <div class="filter-group mb-3">
                                <label class="form-label">Location</label>
                                <select name="location" class="form-select custom-select">
                                    <option value="">All Locations</option>
                                    <option value="agra" <?php echo($location == 'agra') ? 'selected' : ''; ?>>Agra
                                    </option>
                                    <option value="delhi" <?php echo($location == 'delhi') ? 'selected' : ''; ?>>Delhi
                                    </option>
                                    <option value="jaipur" <?php echo($location == 'jaipur') ? 'selected' : ''; ?>>Jaipur
                                    </option>
                                    <option value="varanasi" <?php echo($location == 'varanasi') ? 'selected' : ''; ?>>
                                        Varanasi</option>
                                    <option value="rajasthan" <?php echo($location == 'rajasthan') ? 'selected' : ''; ?>>
                                        Rajasthan</option>
                                    <option value="udaipur" <?php echo($location == 'udaipur') ? 'selected' : ''; ?>>
                                        Udaipur</option>
                                    <option value="amritsar" <?php echo($location == 'amritsar') ? 'selected' : ''; ?>>
                                        Amritsar</option>
                                    <option value="pushkar" <?php echo($location == 'pushkar') ? 'selected' : ''; ?>>
                                        Pushkar</option>
                                    <option value="ranthambore" <?php echo($location == 'ranthambore') ? 'selected' : ''; ?>>Ranthambore</option>
                                </select>
                            </div>
                            <div class="filter-group mb-3">
                                <label class="form-label">Tour Type</label>
                                <select name="tourType" class="form-select custom-select">
                                    <option value="">All Types</option>
                                    <option value="same-day" <?php echo($tourType == 'same-day') ? 'selected' : ''; ?>>
                                        Same Day Tour</option>
                                    <option value="taj-mahal" <?php echo($tourType == 'taj-mahal') ? 'selected' : ''; ?>>
                                        Taj Mahal Tour</option>
                                    <option value="golden-triangle" <?php echo($tourType == 'golden-triangle') ? 'selected' : ''; ?>>Golden Triangle</option>
                                    <option value="rajasthan" <?php echo($tourType == 'rajasthan') ? 'selected' : ''; ?>>
                                        Rajasthan Tour</option>
                                </select>
                            </div>
                            <div class="filter-group mb-3">
                                <label class="form-label">Duration</label>
                                <select name="duration" class="form-select custom-select">
                                    <option value="">Any Duration</option>
                                    <option value="1" <?php echo($duration == '1') ? 'selected' : ''; ?>>1 Day</option>
                                    <option value="2" <?php echo($duration == '2') ? 'selected' : ''; ?>>2 Days</option>
                                    <option value="3" <?php echo($duration == '3') ? 'selected' : ''; ?>>3 Days</option>
                                    <option value="4" <?php echo($duration == '4') ? 'selected' : ''; ?>>4 Days</option>
                                    <option value="5" <?php echo($duration == '5') ? 'selected' : ''; ?>>5 Days</option>
                                    <option value="6" <?php echo($duration == '6') ? 'selected' : ''; ?>>6 Days</option>
                                    <option value="7" <?php echo($duration == '7') ? 'selected' : ''; ?>>7 Days</option>
                                    <option value="8" <?php echo($duration == '8') ? 'selected' : ''; ?>>8 Days</option>
                                </select>
                            </div>
                            <div class="filter-group mb-3">
                                <label class="form-label">Minimum Rating</label>
                                <select name="rating" class="form-select custom-select">
                                    <option value="">Any Rating</option>
                                    <option value="1" <?php echo($rating == '1') ? 'selected' : ''; ?>>1+ Stars</option>
                                    <option value="2" <?php echo($rating == '2') ? 'selected' : ''; ?>>2+ Stars</option>
                                    <option value="3" <?php echo($rating == '3') ? 'selected' : ''; ?>>3+ Stars</option>
                                    <option value="4" <?php echo($rating == '4') ? 'selected' : ''; ?>>4+ Stars</option>
                                    <option value="5" <?php echo($rating == '5') ? 'selected' : ''; ?>>5 Stars</option>
                                </select>
                            </div>
                            <div class="filter-group mb-3">
                                <label class="form-label">Search by Name</label>
                                <input type="text" name="search" class="form-control" placeholder="Tour name..."
                                    value="<?php echo htmlspecialchars($search ?? ''); ?>">
                            </div>
                            <button type="submit" class="th-btn w-100">Apply Filters</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-9">
                    <?php if ($filterSummary): ?>
                        <div class="alert alert-info mb-4">
                            <strong>Applied Filters:</strong> <?php echo htmlspecialchars($filterSummary); ?>
                        </div>
                    <?php
endif; ?>

                    <?php if (empty($tours)): ?>
                        <div class="text-center">
                            <h3>No tours found matching your criteria.</h3>
                            <p>Try adjusting your search filters.</p>
                            <a href="index.php" class="th-btn">Back to Home</a>
                        </div>
                    <?php
else: ?>
                        <div class="th-sort-bar">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-4">
                                    <div class="search-form-area">
                                        <p>Showing <?php echo count($tours); ?> result(s)</p>
                                    </div>
                                </div>
                                <div class="col-md-auto">
                                    <div class="sorting-filter-wrap">
                                        <form class="woocommerce-ordering" method="get">
                                            <input type="hidden" name="location"
                                                value="<?php echo htmlspecialchars($location ?? ''); ?>">
                                            <input type="hidden" name="tourType"
                                                value="<?php echo htmlspecialchars($tourType ?? ''); ?>">
                                            <input type="hidden" name="duration"
                                                value="<?php echo htmlspecialchars($duration ?? ''); ?>">
                                            <input type="hidden" name="search"
                                                value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                            <input type="hidden" name="rating"
                                                value="<?php echo htmlspecialchars($rating ?? ''); ?>">
                                            <select name="orderby" class="orderby" aria-label="tour order"
                                                onchange="this.form.submit()">
                                                <option value="menu_order" <?php echo($orderby == 'menu_order' || !$orderby) ? 'selected' : ''; ?>>Default Sorting</option>
                                                <option value="popularity" <?php echo($orderby == 'popularity') ? 'selected' : ''; ?>>Sort by popularity</option>
                                                <option value="rating" <?php echo($orderby == 'rating') ? 'selected' : ''; ?>>Sort by average rating</option>
                                                <option value="date" <?php echo($orderby == 'date') ? 'selected' : ''; ?>>
                                                    Sort by latest</option>
                                                <option value="price" <?php echo($orderby == 'price') ? 'selected' : ''; ?>>
                                                    Sort by price: low to high</option>
                                                <option value="price-desc" <?php echo($orderby == 'price-desc') ? 'selected' : ''; ?>>Sort by price: high to low</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row gy-24 gx-24">
                            <?php foreach ($tours as $tour): ?>
                                <div class="col-xxl-4 col-lg-4 col-md-6 mb-4"><?php echo renderTourCard($tour); ?></div>
                            <?php
    endforeach; ?>
                        </div>
                    <?php
endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>

    <?php include 'components/script.php'; ?>
</body>

</html>