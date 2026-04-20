<?php
require_once 'config.php';

// Get tours for homepage
$popularTours = getTours(null, 6);
$sameDayTours = getTours('Same Day Tours', 4);
$tajMahalTours = getTours('Taj Mahal Tours', 4);
$goldenTriangleTours = getTours('Golden Triangle Tours', 8);

// Handle routing for detail pages
$type = $_GET['type'] ?? null;
$slug = $_GET['slug'] ?? null;

if ($type && $slug) {
    if ($type === 'tour') {
        // Fetch tour by slug
        $stmt = $pdo->prepare("SELECT t.*, c.name as category_name FROM tours t LEFT JOIN categories c ON t.category_id = c.id WHERE t.slug = ?");
        $stmt->execute([$slug]);
        $tour = $stmt->fetch();

        if ($tour) {
            // Update view count
            $pdo->prepare("UPDATE tours SET view_count = view_count + 1 WHERE id = ?")->execute([$tour['id']]);

            // Display tour detail page
            include 'tour-detail.php';
            exit;
        }
    }
    elseif ($type === 'blog') {
        // Fetch blog by slug
        $stmt = $pdo->prepare("SELECT * FROM blogs WHERE slug = ?");
        $stmt->execute([$slug]);
        $blog = $stmt->fetch();

        if ($blog) {
            // Update view count
            $pdo->prepare("UPDATE blogs SET view_count = view_count + 1 WHERE id = ?")->execute([$blog['id']]);

            // Display blog detail page
            include 'blog-detail.php';
            exit;
        }
    }

    // If not found, show 404
    include '404.php';
    exit;
}
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="author" content="India Day Trip">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <?php renderSEOHead('home'); ?>

    <!-- links  -->
    <?php include 'components/links.php'; ?>

</head>

<body>

    <!-- prelaoder -->
    <?php include 'components/preloader.php'; ?>
    <!-- sidemenu -->
    <?php include 'components/sidebar.php'; ?>

    <!-- popup search box -->
    <div class="popup-search-box"><button class="searchClose"><i class="fal fa-times"></i></button>
        <form action="search-tours.php" method="GET"><input type="text" name="search" placeholder="What are you looking for?"> <button type="submit"><i
                    class="fal fa-search"></i></button></form>
    </div>
    <div class="th-menu-wrapper onepage-nav">
        <div class="th-menu-area text-center"><button class="th-menu-toggle"><i class="fal fa-times"></i></button>
            <div class="mobile-logo"><a href="index.php"><img src="assets/img/logo/logo-header.webp"
                        alt="India Day Trip"></a>
            </div>
            <div class="th-mobile-menu">
                <ul>
                    <li><a class="active" href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li class="menu-item-has-children"><a href="#">Tours</a>
                        <ul class="sub-menu">
                            <li><a href="same-day-tours.php">Same Day Tours</a></li>
                            <li><a href="taj-mahal-tours.php">Taj Mahal Tours</a></li>
                            <li><a href="golden-triangle-tours.php">Golden Triangle Tours</a></li>
                            <li><a href="agra-tours.php">Agra Tours</a></li>
                        </ul>
                    </li>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
        </div>
    </div>


    <!-- header  -->

    <?php include 'components/header.php'; ?>


    <div class="th-hero-wrapper hero-1" id="hero">
        <div class="swiper th-slider hero-slider-1 swiper-fade" id="heroSlide1"
            data-slider-options='{"effect":"fade","menu": ["","",""],"heroSlide1": {"swiper-container": {"pagination": {"el": ".swiper-pagination", "clickable": true }}}}'>
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="hero-inner">
                        <div class="th-hero-bg background-image"
                            style="background-image: url('assets/img/hero/hero-agra.webp');"></div>
                        <div class="container">
                            <div class="hero-style1">
                                <span class="sub-title style1 slideinup" data-ani="slideinup" data-ani-delay="0.2s">Get
                                    unforgettable pleasure with us</span>
                                <h1 class="hero-title slideinup" data-ani="slideinup" data-ani-delay="0.4s">Discover The
                                    Beauty of Agra</h1>
                                <div class="btn-group slideinup" data-ani="slideinup" data-ani-delay="0.6s">
                                    <a href="tour/index.php" class="th-btn th-icon">Explore Tours</a>
                                    <a href="about.php" class="th-btn style2 th-icon">Our Services</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="hero-inner">
                        <div class="th-hero-bg background-image"
                            style="background-image: url('assets/img/hero/hero-delhi.webp');"></div>
                        <div class="container">
                            <div class="hero-style1">
                                <span class="sub-title style1 slideinup" data-ani="slideinup" data-ani-delay="0.2s">Get
                                    unforgettable pleasure with us</span>
                                <h1 class="hero-title slideinup" data-ani="slideinup" data-ani-delay="0.4s">Explore The
                                    Heart of Delhi</h1>
                                <div class="btn-group slideinup" data-ani="slideinup" data-ani-delay="0.6s">
                                    <a href="tour/index.php" class="th-btn th-icon">Explore Tours</a>
                                    <a href="about.php" class="th-btn style2 th-icon">Our Services</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="hero-inner">
                        <div class="th-hero-bg background-image"
                            style="background-image: url('assets/img/hero/hero-jaipur.webp');"></div>
                        <div class="container">
                            <div class="hero-style1">
                                <span class="sub-title style1 slideinup" data-ani="slideinup" data-ani-delay="0.2s">Get
                                    unforgettable pleasure with us</span>
                                <h1 class="hero-title slideinup" data-ani="slideinup" data-ani-delay="0.4s">Experience
                                    The Pink City of Jaipur</h1>
                                <div class="btn-group slideinup" data-ani="slideinup" data-ani-delay="0.6s">
                                    <a href="tour/index.php" class="th-btn th-icon">Explore Tours</a>
                                    <a href="about.php" class="th-btn style2 th-icon">Our Services</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="th-swiper-custom">
                <button data-slider-prev="#heroSlide1" class="slider-arrow slider-prev">
                    <img src="assets/img/icon/right-arrow.svg" alt="">
                </button>
                <div
                    class="slider-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal">
                    <span class="swiper-pagination-bullet"></span>
                    <span class="swiper-pagination-bullet swiper-pagination-bullet-active"></span>
                    <span class="swiper-pagination-bullet"></span>
                </div>
                <button data-slider-next="#heroSlide1" class="slider-arrow slider-next">
                    <img src="assets/img/icon/left-arrow.svg" alt="">
                </button>
            </div>
            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
        </div>
    </div>

    <div class="booking-sec">
        <div class="container">
            <form action="search-tours.php" method="GET" class="booking-form" style="padding: 0px !important;" id="tourSearchForm">
                <div class="input-wrap">
                    <div class="row align-items-center justify-content-between">
                        <div class="form-group col-md-6 col-lg-auto">
                            <div class="icon"><i class="fa-light fa-route"></i></div>
                            <div class="search-input">
                                <label class="form-label">Location</label>
                                <select name="location" id="location" class="form-select">
                                    <option value="">All Locations</option>
                                    <option value="agra">Agra</option>
                                    <option value="delhi">Delhi</option>
                                    <option value="jaipur">Jaipur</option>
                                    <option value="varanasi">Varanasi</option>
                                    <option value="rajasthan">Rajasthan</option>
                                    <option value="udaipur">Udaipur</option>
                                    <option value="amritsar">Amritsar</option>
                                    <option value="pushkar">Pushkar</option>
                                    <option value="ranthambore">Ranthambore</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6 col-lg-auto">
                            <div class="icon">
                                <i class="fa-regular fa-person-hiking"></i>
                            </div>
                            <div class="search-input">
                                <label class="form-label">Tour Type</label>
                                <select name="tourType" id="tourType" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="same-day">Same Day Tour</option>
                                    <option value="taj-mahal">Taj Mahal Tour</option>
                                    <option value="golden-triangle">Golden Triangle</option>
                                    <option value="rajasthan">Rajasthan Tour</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6 col-lg-auto">
                            <div class="icon"><i class="fa-light fa-clock"></i></div>
                            <div class="search-input">
                                <label class="form-label">Duration</label>
                                <select name="duration" id="duration" class="form-select">
                                    <option value="">Any Duration</option>
                                    <option value="1">1 Day</option>
                                    <option value="2">2 Days</option>
                                    <option value="3">3 Days</option>
                                    <option value="4">4 Days</option>
                                    <option value="5">5 Days</option>
                                    <option value="6">6 Days</option>
                                    <option value="7">7 Days</option>
                                    <option value="8">8 Days</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6 col-lg-auto">
                            <div class="icon">
                                <i class="fa-light fa-search"></i>
                            </div>
                            <div class="search-input">
                                <label class="form-label">Search Tours</label>
                                <input type="text" name="search" id="search" class="form-control"
                                    placeholder="Search by tour name...">
                            </div>
                        </div>
                        <div class="form-btn col-md-12 col-lg-auto">
                            <button type="submit" class="th-btn">
                                <i class="fas fa-search"></i> Search Tours
                            </button>
                        </div>
                    </div>
                    <p class="form-messages mb-0 mt-3"></p>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Tour Card Fixed Height - Equal height for all cards */
        .tour-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .tour-card .tour-card_img {
            height: 250px;
            overflow: hidden;
        }

        .tour-card .tour-card_img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tour-card .tour-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .tour-card .box-title {
            min-height: 50px;
        }

        /* Booking Search Form Improvements */
        .booking-sec {

            padding: 40px 0;
            margin-top: -30px;
            position: relative;
            z-index: 100;
        }

        .booking-form .input-wrap {
            background: #ffffff;
            border-radius: 16px;
            padding: 25px 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .booking-form .form-group {
            margin-bottom: 0;
        }

        .booking-form .search-input {
            position: relative;
        }

        .booking-form .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #113D48;
            margin-bottom: 8px;
        }

        .booking-form .form-control,
        .booking-form .form-select {
            padding: 14px 18px;
            padding-left: 18px;
            font-size: 0.95rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            background: #fff;
            transition: all 0.3s ease;
            color: #333;
        }

        .booking-form .form-control:focus,
        .booking-form .form-select:focus {
            border-color: #1CA8CB;
            box-shadow: 0 0 0 0.2rem rgba(28, 168, 203, 0.15);
            outline: none;
        }

        .booking-form .form-control::placeholder {
            color: #999;
            opacity: 1;
        }

        .booking-form .icon {
            position: absolute;
            left: 10px;
            top: 38px;
            color: #1CA8CB;
            font-size: 1.1rem;
            z-index: 10;
        }

        .booking-form .icon+.search-input .form-control,
        .booking-form .icon+.search-input .form-select {
            padding-left: 40px;
        }

        .booking-form .th-btn {
            padding: 14px 30px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .booking-form .th-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(28, 168, 203, 0.4);
        }

        @media (max-width: 992px) {
            .booking-form .input-wrap {
                padding: 20px;
            }

            .booking-form .form-group {
                margin-bottom: 15px;
            }

            .booking-form .form-control,
            .booking-form .form-select {
                padding: 12px 14px;
            }

            .booking-form .icon {
                top: 36px;
            }

            .booking-form .form-btn {
                width: 100%;
            }

            .booking-form .th-btn {
                width: 100%;
            }
        }
    </style>

    <section class="category-area bg-top-center background-image"
        style="background-image: url('assets/img/bg/category_bg_1.webp');">
        <div class="container th-container">
            <div class="title-area text-center">
                <span class="sub-title">Wonderful Place For You</span>
                <h2 class="sec-title">Top Destinations</h2>
            </div>
            <div class="swiper th-slider has-shadow categorySlider background-image swiper-initialized swiper-horizontal swiper-backface-hidden"
                id="categorySlider1"
                data-slider-options='{"spaceBetween": "50","breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"1"},"768":{"slidesPerView":"2"},"992":{"slidesPerView":"3"},"1200":{"slidesPerView":"3"},"1400":{"slidesPerView":"5"}}}'
                style="background-image: url('assets/img/bg/category_bg_1.webp');">
                <div class="swiper-wrapper" id="swiper-wrapper-custom" aria-live="off"
                    style="transition-duration: 1000ms;">
                    <div class="swiper-slide" style="width: 236.8px; margin-right: 50px;" role="group"
                        aria-label="1 / 8" data-swiper-slide-index="0">
                        <div class="category-card single"
                            style="transform: translate(0px, 10px) rotate(-3deg); transform-origin: right top;">
                            <div class="box-img global-img">
                                <img src="assets/img/destination/d-delhi.webp" alt="Delhi">
                            </div>
                            <h3 class="box-title"><a href="tour/index.php">Delhi</a></h3>
                            <a class="line-btn" href="tour/index.php">See packages</a>
                        </div>
                    </div>
                    <div class="swiper-slide" style="width: 236.8px; margin-right: 50px;" role="group"
                        aria-label="2 / 8" data-swiper-slide-index="1">
                        <div class="category-card single"
                            style="transform: translate(0px, 0px) rotate(-1deg); transform-origin: right top;">
                            <div class="box-img global-img">
                                <img src="assets/img/destination/d-agra.webp" alt="Agra">
                            </div>
                            <h3 class="box-title"><a href="tour/index.php">Agra</a></h3>
                            <a class="line-btn" href="tour/index.php">See packages</a>
                        </div>
                    </div>
                    <div class="swiper-slide" style="width: 236.8px; margin-right: 50px;" role="group"
                        aria-label="3 / 8" data-swiper-slide-index="2">
                        <div class="category-card single"
                            style="transform: translate(0px, 0px) rotate(1deg); transform-origin: left top;">
                            <div class="box-img global-img">
                                <img src="assets/img/destination/d-jaipur.webp" alt="Jaipur">
                            </div>
                            <h3 class="box-title"><a href="tour/index.php">Jaipur</a></h3>
                            <a class="line-btn" href="tour/index.php">See packages</a>
                        </div>
                    </div>
                    <div class="swiper-slide" style="width: 236.8px; margin-right: 50px;" role="group"
                        aria-label="4 / 8" data-swiper-slide-index="3">
                        <div class="category-card single"
                            style="transform: translate(0px, 22px) rotate(4deg); transform-origin: left top;">
                            <div class="box-img global-img">
                                <img src="assets/img/destination/d-amritsar.webp" alt="Amritsar">
                            </div>
                            <h3 class="box-title"><a href="tour/index.php">Amritsar</a></h3>
                            <a class="line-btn" href="tour/index.php">See packages</a>
                        </div>
                    </div>
                    <div class="swiper-slide" style="width: 236.8px; margin-right: 50px;" role="group"
                        aria-label="5 / 8" data-swiper-slide-index="4">
                        <div class="category-card single"
                            style="transform: translate(0px, 49px) rotate(7deg); transform-origin: left top;">
                            <div class="box-img global-img">
                                <img src="assets/img/destination/d-ranthambore.webp" alt="Ranthambore">
                            </div>
                            <h3 class="box-title"><a href="tour/index.php">Ranthambore</a></h3>
                            <a class="line-btn" href="tour/index.php">See packages</a>
                        </div>
                    </div>
                    <div class="swiper-slide" style="width: 236.8px; margin-right: 50px;" role="group"
                        aria-label="6 / 8" data-swiper-slide-index="5">
                        <div class="category-card single"
                            style="transform: translate(0px, 89px) rotate(11deg); transform-origin: right top;">
                            <div class="box-img global-img">
                                <img src="assets/img/destination/d-varansi.webp" alt="Varanasi">
                            </div>
                            <h3 class="box-title"><a href="tour/index.php">Varanasi</a></h3>
                            <a class="line-btn" href="tour/index.php">See packages</a>
                        </div>
                    </div>
                    <div class="swiper-slide" style="width: 236.8px; margin-right: 50px;" role="group"
                        aria-label="7 / 8" data-swiper-slide-index="6">
                        <div class="category-card single"
                            style="transform: translate(0px, 62px) rotate(9deg); transform-origin: right top;">
                            <div class="box-img global-img">
                                <img src="assets/img/destination/d-amritsar.webp" alt="Amritsar">
                            </div>
                            <h3 class="box-title"><a href="tour/index.php">Amritsar</a></h3>
                            <a class="line-btn" href="tour/index.php">See packages</a>
                        </div>
                    </div>
                    <div class="swiper-slide" style="width: 236.8px; margin-right: 50px;" role="group"
                        aria-label="8 / 8" data-swiper-slide-index="7">
                        <div class="category-card single"
                            style="transform: translate(0px, 36px) rotate(6deg); transform-origin: right top;">
                            <div class="box-img global-img">
                                <img src="assets/img/destination/d-jaipur.webp" alt="Jaipur">
                            </div>
                            <h3 class="box-title"><a href="tour/index.php">Jaipur</a></h3>
                            <a class="line-btn" href="tour/index.php">See packages</a>
                        </div>
                    </div>
                </div>
                <div
                    class="slider-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal">
                    <span class="swiper-pagination-bullet swiper-pagination-bullet-active" aria-label="Go to Slide 01"
                        tabindex="0" aria-current="true"></span>
                    <span class="swiper-pagination-bullet" aria-label="Go to Slide 02" tabindex="0"></span>
                    <span class="swiper-pagination-bullet" aria-label="Go to Slide 03" tabindex="0"></span>
                    <span class="swiper-pagination-bullet" aria-label="Go to Slide 04" tabindex="0"></span>
                    <span class="swiper-pagination-bullet" aria-label="Go to Slide 05" tabindex="0"></span>
                    <span class="swiper-pagination-bullet" aria-label="Go to Slide 06" tabindex="0"></span>
                    <span class="swiper-pagination-bullet" aria-label="Go to Slide 07" tabindex="0"></span>
                    <span class="swiper-pagination-bullet" aria-label="Go to Slide 08" tabindex="0"></span>
                </div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div>
        </div>
    </section>

    <!-- Most Popular Tour -->
    <section class="tour-area position-relative bg-top-center overflow-hidden space background-image arrow-wrap"
        id="service-sec" style="background-image: url('assets/img/bg/tour_bg_1.webp');">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="title-area text-center">
                        <span class="sub-title">Best Place For You</span>
                        <h2 class="sec-title">Most Popular Tour</h2>
                        <p class="sec-text">Discover our most popular tours across India, featuring iconic destinations
                            like the Taj Mahal, Delhi, and the Golden Triangle.</p>
                    </div>
                </div>
            </div>
            <div class="slider-area tour-slider">
                <div class="swiper th-slider has-shadow slider-drag-wrap swiper-initialized swiper-horizontal swiper-backface-hidden"
                    id="popularToursSlider"
                    data-slider-options='{"spaceBetween":20,"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"1"},"768":{"slidesPerView":"2"},"992":{"slidesPerView":"2"},"1200":{"slidesPerView":"3"},"1300":{"slidesPerView":"4"}}}'>
                    <div class="swiper-wrapper">
                        <?php foreach ($popularTours as $tour): ?>
                            <div class="swiper-slide">
                                <?php echo renderTourCard($tour); ?>
                            </div>
                        <?php
endforeach; ?>
                    </div>
                    <div class="th-swiper-custom">
                        <button data-slider-prev="#popularToursSlider" class="slider-arrow slider-prev">
                            <img src="assets/img/icon/right-arrow.svg" alt="">
                        </button>
                        <div
                            class="slider-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal">
                        </div>
                        <button data-slider-next="#popularToursSlider" class="slider-arrow slider-next">
                            <img src="assets/img/icon/left-arrow.svg" alt="">
                        </button>
                    </div>
                    <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Trending Tours -->
    <section class="tour-area3 position-relative bg-top-center overflow-hidden space background-image arrow-wrap"
        id="service-sec" style="background-image: url('assets/img/bg/tour_bg_1.webp');">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="title-area text-center">
                        <span class="sub-title">Based on user booking package.</span>
                        <h2 class="sec-title">Our Trending Tours </h2>
                    </div>
                </div>
            </div>
            <div class="nav nav-tabs tour-tabs" id="nav-tab" role="tablist">
                <button class="nav-link th-btn active" id="nav-step1-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-step1" type="button">
                    <img src="assets/img/icon/tour_icon_1.svg" alt="Taj Mahal Tour">Taj Mahal
                    Tour
                </button>
                <button class="nav-link th-btn" id="nav-step2-tab" data-bs-toggle="tab" data-bs-target="#nav-step2"
                    type="button">
                    <img src="assets/img/icon/tour_icon_2.svg" alt="Same Day Tour">Same Day
                    Tour
                </button>
                <button class="nav-link th-btn" id="nav-step3-tab" data-bs-toggle="tab" data-bs-target="#nav-step3"
                    type="button">
                    <img src="assets/img/icon/tour_icon_3.svg" alt="Golden Triangle Tour">Golden
                    Triangle Tour
                </button>
            </div>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade active show" id="nav-step1" role="tabpanel">
                    <div class="slider-area tour-slider slider-drag-wrap">
                        <div class="swiper th-slider has-shadow"
                            data-slider-options='{"spaceBetween":20,"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"1"},"768":{"slidesPerView":"2"},"992":{"slidesPerView":"3"},"1200":{"slidesPerView":"3"},"1300":{"slidesPerView":"4"}}}'>
                            <div class="swiper-wrapper">
                                <?php foreach ($tajMahalTours as $tour): ?>
                                    <div class="swiper-slide">
                                        <?php echo renderTourCard($tour); ?>
                                    </div>
                                <?php
endforeach; ?>
                            </div>
                            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-step2" role="tabpanel">
                    <div class="slider-area tour-slider slider-drag-wrap">
                        <div class="swiper th-slider has-shadow"
                            data-slider-options='{"spaceBetween":20,"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"1"},"768":{"slidesPerView":"2"},"992":{"slidesPerView":"3"},"1200":{"slidesPerView":"3"},"1300":{"slidesPerView":"4"}}}'>
                            <div class="swiper-wrapper">
                                <?php foreach ($sameDayTours as $tour): ?>
                                    <div class="swiper-slide">
                                        <?php echo renderTourCard($tour); ?>
                                    </div>
                                <?php
endforeach; ?>
                            </div>
                            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-step3" role="tabpanel">
                    <div class="slider-area tour-slider slider-drag-wrap">
                        <div class="swiper th-slider has-shadow"
                            data-slider-options='{"spaceBetween":20,"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"1"},"768":{"slidesPerView":"2"},"992":{"slidesPerView":"3"},"1200":{"slidesPerView":"3"},"1300":{"slidesPerView":"4"}}}'>
                            <div class="swiper-wrapper">
                                <?php foreach ($goldenTriangleTours as $tour): ?>
                                    <div class="swiper-slide">
                                        <?php echo renderTourCard($tour); ?>
                                    </div>
                                <?php
endforeach; ?>
                            </div>
                            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose India Day Trip -->
    <div class="feature-area overflow-hidden" id="feature-area">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="title-area text-center"><span class="sub-title style1">Why Choose India Day Trip</span>
                        <h2 class="sec-title">Premium Travel Experience</h2>
                        <p class="feature-text">India Day Trip offers exceptional travel experiences with expert guides,
                            comfortable transportation, and personalized itineraries.</p>
                    </div>
                </div>
            </div>
            <div class="row gy-4 justify-content-center">
                <div class="col-md-6 col-xl-3">
                    <div class="feature-item th-ani">
                        <div class="feature-item_icon"><img src="assets/img/icon/feature_1_1.svg" alt="icon"></div>
                        <div class="media-body">
                            <h3 class="box-title">Expert Guides</h3>
                            <p class="feature-item_text">Knowledgeable local guides who bring India's rich history and
                                culture to life.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="feature-item th-ani">
                        <div class="feature-item_icon"><img src="assets/img/icon/feature_1_2.svg" alt="icon"></div>
                        <div class="media-body">
                            <h3 class="box-title">Comfortable Transport</h3>
                            <p class="feature-item_text">Air-conditioned vehicles with professional drivers for a smooth
                                journey.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="feature-item th-ani">
                        <div class="feature-item_icon"><img src="assets/img/icon/feature_1_3.svg" alt="icon"></div>
                        <div class="media-body">
                            <h3 class="box-title">Flexible Itineraries</h3>
                            <p class="feature-item_text">Customizable, flexible tour plans. Tailored options. Fit your
                                schedule.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="feature-item th-ani">
                        <div class="feature-item_icon"><img src="assets/img/icon/feature_1_4.svg" alt="icon"></div>
                        <div class="media-body">
                            <h3 class="box-title">24/7 Support</h3>
                            <p class="feature-item_text">Round-the-clock assistance to ensure a hassle-free travel
                                experience.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- About India Day Trip -->
    <div class="about-area position-relative overflow-hidden space" id="about-sec">
        <div class="container">
            <div class="row gy-4">
                <div class="col-xl-6">
                    <div class="img-box15 pe-xl-4">
                        <div class="img1 global-img"><img src="assets/img/home/about-1.webp" alt="About India Day Trip">
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="ps-xl-4">
                        <div class="title-area mb-20"><span class="sub-title style1">About India Day Trip</span>
                            <h2 class="sec-title mb-20">Your Trusted Travel Partner in Agra</h2>
                            <p class="sec-text2 mb-50">India Day Trip is an Agra-based tour and travel company
                                specializing in Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours. With years
                                of experience, we provide exceptional travel experiences across India's most iconic
                                destinations.</p>
                        </div>
                        <div class="about-item-wrap style2">
                            <div class="about-item style4">
                                <div class="about-item_img"><img src="assets/img/icon/about_2_1.svg" alt=""></div>
                                <div class="about-item_centent">
                                    <h5 class="box-title">Hassle-Free Booking</h5>
                                    <p class="about-item_text">Easy online booking process with secure payment options
                                        and instant confirmation.</p>
                                </div>
                            </div>
                            <div class="about-item style4">
                                <div class="about-item_img"><img src="assets/img/icon/about_2_2.svg" alt=""></div>
                                <div class="about-item_centent">
                                    <h5 class="box-title">Cultural Immersion</h5>
                                    <p class="about-item_text">Authentic experiences that connect you with India's rich
                                        heritage and traditions.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Same Day Tours Slider -->
    <section class="tour-area position-relative overflow-hidden pt-5 pb-5">
        <div class="container">
            <div class="mb-30 text-center text-md-start">
                <div class="row align-items-center justify-content-between">
                    <div class="col-md-7">
                        <div class="title-area mb-md-0">
                            <h3 class="sec-title">Same Day Tours</h3>
                        </div>
                    </div>
                    <div class="col-md-auto">
                        <a href="/same-day-tours/index.php" class="th-btn style4 th-icon">See All Tours</a>
                    </div>
                </div>
            </div>
            <div class="slider-area tour-slider">
                <div class="swiper th-slider has-shadow slider-drag-wrap" id="sameDayToursSlider"
                    data-slider-options='{"spaceBetween":20,"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"1"},"768":{"slidesPerView":"2"},"992":{"slidesPerView":"2"},"1200":{"slidesPerView":"3"},"1300":{"slidesPerView":"4"}}}'>
                    <div class="swiper-wrapper">
                        <?php foreach ($sameDayTours as $tour): ?>
                            <div class="swiper-slide">
                                <?php echo renderTourCard($tour); ?>
                            </div>
                        <?php
endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Taj Mahal Tours Slider -->
    <section class="tour-area position-relative overflow-hidden pb-5">
        <div class="container">
            <div class="mb-30 text-center text-md-start">
                <div class="row align-items-center justify-content-between">
                    <div class="col-md-7">
                        <div class="title-area mb-md-0">
                            <h3 class="sec-title">Taj Mahal Tours</h3>
                        </div>
                    </div>
                    <div class="col-md-auto">
                        <a href="/taj-mahal-tours/index.php" class="th-btn style4 th-icon">See All Tours</a>
                    </div>
                </div>
            </div>
            <div class="slider-area tour-slider">
                <div class="swiper th-slider has-shadow slider-drag-wrap" id="tajMahalToursSlider"
                    data-slider-options='{"spaceBetween":20,"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"1"},"768":{"slidesPerView":"2"},"992":{"slidesPerView":"2"},"1200":{"slidesPerView":"3"},"1300":{"slidesPerView":"4"}}}'>
                    <div class="swiper-wrapper">
                        <?php foreach ($tajMahalTours as $tour): ?>
                            <div class="swiper-slide">
                                <?php echo renderTourCard($tour); ?>
                            </div>
                        <?php
endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Golden Triangle Tours Slider -->
    <section class="tour-area position-relative overflow-hidden space-bottom">
        <div class="container">
            <div class="mb-30 text-center text-md-start">
                <div class="row align-items-center justify-content-between">
                    <div class="col-md-7">
                        <div class="title-area mb-md-0">
                            <h3 class="sec-title">Golden Triangle Tours</h3>
                        </div>
                    </div>
                    <div class="col-md-auto">
                        <a href="/golden-triangle-tours/index.php" class="th-btn style4 th-icon">See All Tours</a>
                    </div>
                </div>
            </div>
            <div class="slider-area tour-slider">
                <div class="swiper th-slider has-shadow slider-drag-wrap" id="goldenTriangleToursSlider"
                    data-slider-options='{"spaceBetween":20,"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"1"},"768":{"slidesPerView":"2"},"992":{"slidesPerView":"2"},"1200":{"slidesPerView":"3"},"1300":{"slidesPerView":"4"}}}'>
                    <div class="swiper-wrapper">
                        <?php foreach ($goldenTriangleTours as $tour): ?>
                            <div class="swiper-slide">
                                <?php echo renderTourCard($tour); ?>
                            </div>
                        <?php
endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Travel Gallery -->
    <div class="gallery-area">
        <div class="container th-container shape-mockup-wrap">
            <div class="title-area text-center"><span class="sub-title">Capture Your Memories</span>
                <h2 class="sec-title">Travel Gallery</h2>
            </div>
            <div class="row gy-10 gx-10 justify-content-center align-items-center">
                <div class="col-md-6 col-lg-2">
                    <div class="gallery-card">
                        <div class="box-img global-img"><a href="assets/img/gallery/hg1.webp" class="popup-image">
                                <div class="icon-btn"><i class="fal fa-magnifying-glass-plus"></i></div><img
                                    src="assets/img/gallery/hg1.webp" alt="Taj Mahal">
                            </a></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-2">
                    <div class="gallery-card">
                        <div class="box-img global-img"><a href="assets/img/gallery/hg2.webp" class="popup-image">
                                <div class="icon-btn"><i class="fal fa-magnifying-glass-plus"></i></div><img
                                    src="assets/img/gallery/hg2.webp" alt="Agra Fort">
                            </a></div>
                    </div>
                    <div class="gallery-card">
                        <div class="box-img global-img"><a href="assets/img/gallery/hg3.webp" class="popup-image">
                                <div class="icon-btn"><i class="fal fa-magnifying-glass-plus"></i></div><img
                                    src="assets/img/gallery/hg3.webp" alt="Amber Fort">
                            </a></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-2">
                    <div class="gallery-card">
                        <div class="box-img global-img"><a href="assets/img/gallery/hg4.webp" class="popup-image">
                                <div class="icon-btn"><i class="fal fa-magnifying-glass-plus"></i></div><img
                                    src="assets/img/gallery/hg4.webp" alt="Qutub Minar">
                            </a></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-2">
                    <div class="gallery-card">
                        <div class="box-img global-img"><a href="assets/img/gallery/hg5.webp" class="popup-image">
                                <div class="icon-btn"><i class="fal fa-magnifying-glass-plus"></i></div><img
                                    src="assets/img/gallery/hg5.webp" alt="India Gate">
                            </a></div>
                    </div>
                    <div class="gallery-card">
                        <div class="box-img global-img"><a href="assets/img/gallery/hg6.webp" class="popup-image">
                                <div class="icon-btn"><i class="fal fa-magnifying-glass-plus"></i></div><img
                                    src="assets/img/gallery/hg6.webp" alt="Hawa Mahal">
                            </a></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-2">
                    <div class="gallery-card">
                        <div class="box-img global-img"><a href="assets/img/gallery/hg7.webp" class="popup-image">
                                <div class="icon-btn"><i class="fal fa-magnifying-glass-plus"></i></div><img
                                    src="assets/img/gallery/hg7.webp" alt="City Palace">
                            </a></div>
                    </div>
                </div>
            </div>
            <div class="shape-mockup d-none d-xl-block" style="top: -25%; left: 0%;"><img
                    src="assets/img/shape/line.webp" alt="shape"></div>
            <div class="shape-mockup movingX d-none d-xl-block" style="top: 30%; left: -3%;"><img class="gmovingX"
                    src="assets/img/shape/shape_4.webp" alt="shape"></div>
        </div>
    </div>


    <section class="testi-area overflow-hidden space-top pb-5" id="testi-sec">
        <div class="container-fluid p-0">
            <div class="title-area mb-20 text-center"><span class="sub-title text-anime-style-2">Testimonial</span>
                <h2 class="sec-title text-anime-style-3">What Client Say About us</h2>
            </div>
            <div class="slider-area">
                <div class="swiper th-slider testiSlider1 has-shadow" id="testiSlider1"
                    data-slider-options='{"breakpoints":{"0":{"slidesPerView":1},"767":{"slidesPerView":"2","centeredSlides":"true"},"992":{"slidesPerView":"2","centeredSlides":"true"},"1200":{"slidesPerView":"2.5","centeredSlides":"true"},"1400":{"slidesPerView":"2.5","centeredSlides":"true"}}}'>
                    <div class="swiper-wrapper">
                        <!-- Review 1: Giovanni G - Bipin Kumar -->
                        <div class="swiper-slide">
                            <div class="testi-card">
                                <div class="testi-card_wrapper">
                                    <div class="testi-card_profile">
                                        <div class="testi-card_avater"><img src="assets/img/testimonial/testi_1_1.webp"
                                                alt="testimonial"></div>
                                        <div class="media-body">
                                            <h3 class="box-title">Giovanni G</h3><span class="testi-card_desig">Solo
                                                traveller</span>
                                        </div>
                                    </div>
                                    <div class="testi-card_review"><i class="fa-solid fa-star"></i> <i
                                            class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i
                                            class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i></div>
                                </div>
                                <p class="testi-card_text">"Bipin Kumar is an amazing driver. Very professional and very
                                    good and lovely person. I felt always safe and it is now a new Indian friend of
                                    mine. I suggest you to travel with him. Great experience!!!"</p>
                                <div class="testi-card-quote"><img src="assets/img/icon/testi-quote.svg" alt="img">
                                </div>
                            </div>
                        </div>
                        <!-- Review 2: Iqram S - Golden Triangle Tour -->
                        <div class="swiper-slide">
                            <div class="testi-card">
                                <div class="testi-card_wrapper">
                                    <div class="testi-card_profile">
                                        <div class="testi-card_avater"><img src="assets/img/testimonial/testi_1_2.webp"
                                                alt="testimonial"></div>
                                        <div class="media-body">
                                            <h3 class="box-title">Iqram S</h3><span class="testi-card_desig">Narsingdi,
                                                Bangladesh</span>
                                        </div>
                                    </div>
                                    <div class="testi-card_review"><i class="fa-solid fa-star"></i> <i
                                            class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i
                                            class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i></div>
                                </div>
                                <p class="testi-card_text">"The 4-day Golden Triangle Tour was absolutely perfect.
                                    Everything from the pick-up in Delhi to the drop-off was smooth and stress-free. The
                                    guides were very knowledgeable, and we learned so much about India's history. The
                                    Taj Mahal, Amber Fort, and Jaipur's palaces were breathtaking. Highly recommended!"
                                </p>
                                <div class="testi-card-quote"><img src="assets/img/icon/testi-quote.svg" alt="img">
                                </div>
                            </div>
                        </div>
                        <!-- Review 3: Sabrina A - Omveer driver -->
                        <div class="swiper-slide">
                            <div class="testi-card">
                                <div class="testi-card_wrapper">
                                    <div class="testi-card_profile">
                                        <div class="testi-card_avater"><img src="assets/img/testimonial/testi_1_1.webp"
                                                alt="testimonial"></div>
                                        <div class="media-body">
                                            <h3 class="box-title">Sabrina A</h3><span
                                                class="testi-card_desig">Traveller</span>
                                        </div>
                                    </div>
                                    <div class="testi-card_review"><i class="fa-solid fa-star"></i> <i
                                            class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i
                                            class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i></div>
                                </div>
                                <p class="testi-card_text">"A very well organized tour, followed on every occasion but
                                    also left free to explore. Omveer our driver was very helpful, polite and kind,
                                    always water available, I highly recommend it to everyone."</p>
                                <div class="testi-card-quote"><img src="assets/img/icon/testi-quote.svg" alt="img">
                                </div>
                            </div>
                        </div>
                        <!-- Review 4: Ale - Omveer driver -->
                        <div class="swiper-slide">
                            <div class="testi-card">
                                <div class="testi-card_wrapper">
                                    <div class="testi-card_profile">
                                        <div class="testi-card_avater"><img src="assets/img/testimonial/testi_1_2.webp"
                                                alt="testimonial"></div>
                                        <div class="media-body">
                                            <h3 class="box-title">Ale</h3><span
                                                class="testi-card_desig">Traveller</span>
                                        </div>
                                    </div>
                                    <div class="testi-card_review"><i class="fa-solid fa-star"></i> <i
                                            class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i
                                            class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i></div>
                                </div>
                                <p class="testi-card_text">"The tour was well organized and, despite some small
                                    misunderstanding, the tour operator has always responded with availability and
                                    professionalism, satisfying our every request. The selected hotels were beautiful,
                                    comfortable and in great locations. The local guides proved to be prepared and
                                    competent, enriching the experience with interesting and detailed explanations.
                                    Special thanks go to our driver Omveer, who accompanied us throughout the trip with
                                    kindness, punctuality and great professionalism: the best driver we could wish for!"
                                </p>
                                <div class="testi-card-quote"><img src="assets/img/icon/testi-quote.svg" alt="img">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="slider-pagination"></div>
                </div>
            </div>
        </div>
        <div class="shape-mockup d-none d-xl-block" data-bottom="-2%" data-right="0%"><img
                src="assets/img/shape/line2.webp" alt="shape"></div>
        <div class="shape-mockup movingX d-none d-xl-block" data-top="30%" data-left="5%"><img
                src="assets/img/shape/shape_7.webp" alt="shape"></div>
    </section>

    <section class="overflow-hidden space-bottom pt-5">
        <div class="container">
            <div class="row justify-content-lg-between justify-content-center align-items-end">
                <div class="col-lg">
                    <div class="title-area text-center text-lg-start"><span class="sub-title">Travel Tips &
                            Insights</span>
                        <h2 class="sec-title">Latest from Our Blog</h2>
                    </div>
                </div>
                <div class="col-lg-auto d-none d-lg-block">
                    <div class="sec-btn"><a href="/blog/index.php" class="th-btn style4 th-icon">See More Articles</a>
                    </div>
                </div>
            </div>
            <div class="row gx-24 gy-30">
                <div class="col-xl-8">
                    <?php if (isset($latestBlogs[0])): ?>
                        <div class="blog-grid2 style2 th-ani">
                            <div class="blog-img global-img"><img
                                    src="assets/img/blog/<?php echo $latestBlogs[0]['featured_image'] ?: 'blog-tour.webp'; ?>"
                                    alt="<?php echo htmlspecialchars($latestBlogs[0]['title']); ?>"></div>
                            <div class="blog-grid2_content">
                                <div class="blog-meta">
                                    <span><?php echo date('M d, Y', strtotime($latestBlogs[0]['publication_date'])); ?></span>
                                    <span><?php $wordCount = str_word_count(strip_tags($latestBlogs[0]['content']));
    echo ceil($wordCount / 200); ?>
                                        min read</span>
                                </div>
                                <h3 class="box-title"><a
                                        href="?type=blog&slug=<?php echo $latestBlogs[0]['slug']; ?>"><?php echo htmlspecialchars($latestBlogs[0]['title']); ?></a>
                                </h3><a href="?type=blog&slug=<?php echo $latestBlogs[0]['slug']; ?>"
                                    class="th-btn style4 th-icon">Read
                                    More</a>
                            </div>
                        </div>
                    <?php
else: ?>
                        <div class="blog-grid2 style2 th-ani">
                            <div class="blog-img global-img"><img src="assets/img/blog/blog-tour.webp" alt="No blog yet">
                            </div>
                            <div class="blog-grid2_content">
                                <div class="blog-meta"><span>No blog yet</span></div>
                                <h3 class="box-title">No blog yet</h3>
                            </div>
                        </div>
                    <?php
endif; ?>
                    <?php if (isset($latestBlogs[1])): ?>
                        <div class="blog-grid2 th-ani style2 mt-24">
                            <div class="blog-img global-img"><img
                                    src="assets/img/blog/<?php echo $latestBlogs[1]['featured_image'] ?: 'blog-tour.webp'; ?>"
                                    alt="<?php echo htmlspecialchars($latestBlogs[1]['title']); ?>"></div>
                            <div class="blog-grid2_content">
                                <div class="blog-meta">
                                    <span><?php echo date('M d, Y', strtotime($latestBlogs[1]['publication_date'])); ?></span>
                                    <span><?php $wordCount = str_word_count(strip_tags($latestBlogs[1]['content']));
    echo ceil($wordCount / 200); ?>
                                        min read</span>
                                </div>
                                <h3 class="box-title"><a
                                        href="?type=blog&slug=<?php echo $latestBlogs[1]['slug']; ?>"><?php echo htmlspecialchars($latestBlogs[1]['title']); ?></a>
                                </h3><a href="?type=blog&slug=<?php echo $latestBlogs[1]['slug']; ?>"
                                    class="th-btn style4 th-icon">Read
                                    More</a>
                            </div>
                        </div>
                    <?php
else: ?>
                        <div class="blog-grid2 th-ani style2 mt-24">
                            <div class="blog-img global-img"><img src="assets/img/blog/blog-tour.webp" alt="No blog yet">
                            </div>
                            <div class="blog-grid2_content">
                                <div class="blog-meta"><span>No blog yet</span></div>
                                <h3 class="box-title">No blog yet</h3>
                            </div>
                        </div>
                    <?php
endif; ?>
                </div>
                <div class="col-xl-4">
                    <?php if (isset($latestBlogs[2])): ?>
                        <div class="blog-grid2 th-ani">
                            <div class="blog-img global-img"><img
                                    src="assets/img/blog/<?php echo $latestBlogs[2]['featured_image'] ?: 'blog-tour.webp'; ?>"
                                    alt="<?php echo htmlspecialchars($latestBlogs[2]['title']); ?>"></div>
                            <div class="blog-grid2_content">
                                <div class="blog-meta">
                                    <span><?php echo date('M d, Y', strtotime($latestBlogs[2]['publication_date'])); ?></span>
                                    <span><?php $wordCount = str_word_count(strip_tags($latestBlogs[2]['content']));
    echo ceil($wordCount / 200); ?>
                                        min read</span>
                                </div>
                                <h3 class="box-title">
                                    <a
                                        href="?type=blog&slug=<?php echo $latestBlogs[2]['slug']; ?>"><?php echo htmlspecialchars($latestBlogs[2]['title']); ?></a>
                                </h3><a href="?type=blog&slug=<?php echo $latestBlogs[2]['slug']; ?>"
                                    class="th-btn style4 th-icon">Read
                                    More</a>
                            </div>
                        </div>
                    <?php
else: ?>
                        <div class="blog-grid2 th-ani">
                            <div class="blog-img global-img"><img src="assets/img/blog/blog-tour.webp" alt="No blog yet">
                            </div>
                            <div class="blog-grid2_content">
                                <div class="blog-meta"><span>No blog yet</span></div>
                                <h3 class="box-title">No blog yet</h3>
                            </div>
                        </div>
                    <?php
endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Hotel & Travel Partners Section -->
    <section class="partners-area overflow-hidden py-5 bg-light">
        <div class="container">
            <!-- Hotel Partners -->
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h3 class="sec-title mb-4">Our Hotel Partners</h3>
                    <p class="sec-text mb-4">We collaborate with the finest hotels to ensure your stay is comfortable
                    </p>
                </div>
            </div>
            <div class="row justify-content-center mb-5">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-center align-items-center gap-4 gap-lg-5"
                        id="hotel-partners">
                        <div class="partner-box text-center">
                            <img src="assets/img/hotels/taj.png" alt="Taj Hotels" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/hotels/hilton.png" alt="Hilton Hotels" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/hotels/fairmount.png" alt="Fairmount Hotels" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/hotels/holiday.png" alt="Holiday Inn" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/hotels/ramada.png" alt="Ramada Hotels" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/hotels/redission.png" alt="Radisson Hotels" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/hotels/itc holel.png" alt="ITC Hotels" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/hotels/marrite.png" alt="Marriott Hotels" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/hotels/novetl.png" alt="Novotel Hotels" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/hotels/sarovar.png" alt="Sarovar Hotels" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/hotels/shanrila.png" alt="Shangri-La Hotels" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/hotels/trident.png" alt="Trident Hotels" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Travel Partners -->
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h3 class="sec-title mb-4">Our Travel Partners</h3>
                    <p class="sec-text mb-4">We work with trusted travel companies to provide the best experiences</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-center align-items-center gap-4 gap-lg-5"
                        id="travel-partners">
                        <div class="partner-box text-center">
                            <img src="assets/img/traveller-partner/viator.png" alt="Viator" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/traveller-partner/tripadvisor.png" alt="TripAdvisor" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/traveller-partner/expedia.png" alt="Expedia" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/traveller-partner/lonely.png" alt="Lonely Planet" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/traveller-partner/civi.png" alt="Civi" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                        <div class="partner-box text-center">
                            <img src="assets/img/traveller-partner/tour-radar.png" alt="Tour Radar" class="img-fluid"
                                style="height: 80px; width: auto;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- footer -->

    <?php include 'components/footer.php'; ?>

        

    <!-- script  -->

    <?php include 'components/script.php'; ?>

</body>

</html>