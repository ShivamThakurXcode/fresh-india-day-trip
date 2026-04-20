<?php
// Calculate base path based on including file location
$script_path = dirname($_SERVER['SCRIPT_NAME']);
$depth = substr_count($script_path, '/');
if ($depth > 0) {
    $base_path = str_repeat('../', $depth);
} else {
    $base_path = '';
}
$current_page = $_SERVER['SCRIPT_NAME'];

$contact_address = getSetting('contact_address') ?: 'Shop No. 2, Gupta Market, Tajganj, Agra';
$contact_email = getSetting('contact_email') ?: 'indiadaytrip@gmail.com';
$contact_mobile = getSetting('contact_mobile') ?: '+91 81260 52755';
$social_facebook = getSetting('social_facebook') ?: 'https://www.facebook.com/indiadaytrip';
$social_twitter = getSetting('social_twitter') ?: 'https://www.twitter.com/indiadaytrip';
$social_instagram = getSetting('social_instagram') ?: 'https://www.instagram.com/indiadaytrip';

$phone_href = preg_replace('/[^0-9+]/', '', $contact_mobile);
?>
<header class="th-header header-layout1 header-layout2">
    <div class="header-top d-none d-lg-block" style="background-color: #0c2d62;">
        <div class="container th-container">
            <div class="row justify-content-center justify-content-lg-between align-items-center">
                <div class="col-auto d-none d-md-block">
                    <div class="header-links">
                        <ul>
                            <li class="d-none d-xl-inline-block"><i class="fa-sharp fa-regular fa-location-dot" style="color: #ffc107;"></i>
                                <span class="text-white"><?php echo htmlspecialchars($contact_address); ?></span>
                            </li>
                            <li class="d-none d-xl-inline-block"><i class="fa-regular fa-clock" style="color: #ffc107;"></i> <span class="text-white">Daily:
                                    8.00 am - 8.00 pm</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="header-top-right d-none d-lg-flex align-items-center">
                        <div class="contact-info d-none d-xl-flex align-items-center gap-3 me-4">
                            <a href="tel:<?php echo htmlspecialchars($phone_href); ?>" class="contact-item d-flex align-items-center gap-2 text-white text-decoration-none">
                                <i class="fas fa-phone-alt" style="color: #ffc107;"></i>
                                <span><?php echo htmlspecialchars($contact_mobile); ?></span>
                            </a>
                            <a href="mailto:<?php echo htmlspecialchars($contact_email); ?>" class="contact-item d-flex align-items-center gap-2 text-white text-decoration-none">
                                <i class="fas fa-envelope" style="color: #ffc107;"></i>
                                <span><?php echo htmlspecialchars($contact_email); ?></span>
                            </a>
                        </div>
                        <div class="social-links d-flex align-items-center ">
                            <a href="<?php echo htmlspecialchars($social_facebook); ?>" target="_blank" class="d-flex align-items-center justify-content-center text-white social-icon" style="width: 36px; height: 36px; background-color: rgba(255,255,255,0.1); border-radius: 50%; transition: all 0.3s ease;">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="<?php echo htmlspecialchars($social_twitter); ?>" target="_blank" class="d-flex align-items-center justify-content-center text-white social-icon" style="width: 36px; height: 36px; background-color: rgba(255,255,255,0.1); border-radius: 50%; transition: all 0.3s ease;">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="<?php echo htmlspecialchars($social_instagram); ?>" target="_blank" class="d-flex align-items-center justify-content-center text-white social-icon" style="width: 36px; height: 36px; background-color: rgba(255,255,255,0.1); border-radius: 50%; transition: all 0.3s ease;">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://www.linkedin.com" target="_blank" class="d-flex align-items-center justify-content-center text-white social-icon" style="width: 36px; height: 36px; background-color: rgba(255,255,255,0.1); border-radius: 50%; transition: all 0.3s ease;">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sticky-wrapper">
        <div class="menu-area" data-bg-src="<?php echo $base_path; ?>../assets/img/bg/line-pattern.webp">
            <div class="container th-container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto">
                        <div class="header-logo"><a href="<?php echo $base_path; ?>index.php"><img src="<?php echo $base_path; ?>../assets/img/logo/logo-header.webp"
                                    alt="India Day Trip" style="height: 60px; width: auto;"></a></div>
                    </div>
                    <div class="col-auto">
                        <nav class="main-menu d-none d-xl-inline-block">
                            <ul>
                                <li><a class="<?php echo ($current_page == '/index.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>../index.php">Home</a></li>
                                <li><a class="<?php echo ($current_page == '../about/index.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>../about/index.php">About</a></li>
                                <li class="menu-item-has-children"><a class="<?php echo (in_array($current_page, ['/tour/index.php', '/same-day-tours/index.php', '/taj-mahal-tours/index.php', '/golden-triangle-tours/index.php', '/rajasthan-tour-packages/index.php'])) ? 'active' : ''; ?>" href="#">Tours</a>
                                    <ul class="sub-menu">
                                        <li><a class="<?php echo ($current_page == '/tour/index.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>../tour/index.php">All Tours</a></li>
                                        <li><a class="<?php echo ($current_page == '/same-day-tours/index.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>../same-day-tours/index.php">Same Day Tours</a></li>
                                        <li><a class="<?php echo ($current_page == '/taj-mahal-tours/index.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>../taj-mahal-tours/index.php">Taj Mahal Tours</a></li>
                                        <li><a class="<?php echo ($current_page == '/golden-triangle-tours/index.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>../golden-triangle-tours/index.php">Golden Triangle Tours</a></li>
                                        <li><a class="<?php echo ($current_page == '/rajasthan-tour-packages/index.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>../rajasthan-tour-packages/index.php">Rajasthan Tours</a></li>
                                    </ul>
                                </li>
                                <li><a class="<?php echo ($current_page == '/search-tours.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>../search-tours.php">Search Tours</a></li>
                                <li><a class="<?php echo (strpos($current_page, '/blog/') === 0) ? 'active' : ''; ?>" href="<?php echo $base_path; ?>../blog/index.php">Blog</a></li>
                                <li><a class="<?php echo ($current_page == '/contact/index.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>../contact/index.php">Contact Us</a></li>
                            </ul>
                        </nav><button type="button" class="th-menu-toggle d-block d-xl-none"><i
                                class="far fa-bars"></i></button>
                    </div>
                    <div class="col-auto d-none d-xl-block">
                        <div class="header-button"><a href="<?php echo $base_path; ?>/to_book/index.php" class="th-btn style3 th-icon">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </header>
    
    <?php include 'fixed-buttons.php'; ?>