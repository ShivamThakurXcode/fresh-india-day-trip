<?php
// Calculate base path based on including file location
$script_path = dirname($_SERVER['SCRIPT_NAME']);
$depth = substr_count($script_path, '/');
if ($depth > 0) {
    $base_path = str_repeat('../', $depth);
} else {
    $base_path = '';
}

$contact_address = getSetting('contact_address') ?: 'Shop No. 2, Gupta Market, Tajganj, Agra, Uttar Pradesh, India';
$contact_email = getSetting('contact_email') ?: 'indiadaytrip@gmail.com';
$contact_mobile = getSetting('contact_mobile') ?: '+91 81260 52755';
$social_facebook = getSetting('social_facebook') ?: 'https://www.facebook.com/indiadaytrip';
$social_twitter = getSetting('social_twitter') ?: 'https://www.twitter.com/indiadaytrip';
$social_instagram = getSetting('social_instagram') ?: 'https://www.instagram.com/indiadaytrip';

$phone_href = preg_replace('/[^0-9+]/', '', $contact_mobile);
$whatsapp_number = preg_replace('/\D/', '', $contact_mobile);
?>
<footer class="footer-wrapper bg-title footer-layout2 ">
    <div class="widget-area ">
        <div class="container">
            <div class="row g-4 g-lg-5 justify-content-between">
                <!-- Column 1: Logo, About & Social Links -->
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                    <div class="widget footer-widget ">
                        <div class="th-widget-about">
                            <div class="about-logo mb-3">
                                <a href="<?php echo $base_path; ?>index.php">
                                    <img src="../assets/img/logo/logo-footer.webp" alt="India Day Trip" class="img-fluid" style="height: 70px; width: auto;">
                                </a>
                            </div>
                           <p class="about-text mb-4">
                                Agra’s premier travel company for unforgettable Taj Mahal and Golden Triangle tours. We deliver world-class service, carefully crafted itineraries, and expert local guidance to ensure a smooth and luxurious travel experience. Trusted by travelers for years, we are committed to excellence, comfort, and creating remarkable memories at every destination.
                            </p>

                            <div class="th-social">
                                <a href="<?php echo htmlspecialchars($social_facebook); ?>" aria-label="Facebook" class="me-2"><i
                                        class="fab fa-facebook-f"></i></a>
                                <a href="<?php echo htmlspecialchars($social_twitter); ?>" aria-label="Twitter" class="me-2"><i
                                        class="fab fa-twitter"></i></a>
                                <a href="https://wa.me/<?php echo htmlspecialchars($whatsapp_number); ?>" aria-label="WhatsApp" class="me-2"><i
                                        class="fab fa-whatsapp"></i></a>
                                <a href="<?php echo htmlspecialchars($social_instagram); ?>" aria-label="Instagram"><i
                                        class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6">
                    <div class="widget widget_nav_menu footer-widget ">
                        <h3 class="widget_title">Quick Links</h3>
                        <div class="menu-all-pages-container">
                            <ul class="menu">
                                <li><a href="<?php echo $base_path; ?>index.php">Home</a></li>
                                <li><a href="<?php echo $base_path; ?>about/index.php">About</a></li>
                                <li><a href="<?php echo $base_path; ?>gallery/index.php">Gallery</a></li>
                                <li><a href="<?php echo $base_path; ?>contact/index.php">Contact</a></li>
                                <li><a href="<?php echo $base_path; ?>to_book/index.php">Book Trip</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Column 3: Tours -->
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-6">
                    <div class="widget widget_nav_menu footer-widget ">
                        <h3 class="widget_title">Tours</h3>
                        <div class="menu-all-pages-container">
                            <ul class="menu">
                                <li><a href="<?php echo $base_path; ?>tour/index.php">All Tours</a></li>
                                <li><a href="<?php echo $base_path; ?>same-day-tours/index.php">Same Day Tours</a></li>
                                <li><a href="<?php echo $base_path; ?>taj-mahal-tours/index.php">Taj Mahal Tours</a></li>
                                <li><a href="<?php echo $base_path; ?>golden-triangle-tours/index.php">Golden Triangle Tours</a></li>
                                <li><a href="<?php echo $base_path; ?>rajasthan-tour-packages/index.php">Rajasthan Tours</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Column 4: Contact Information -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                    <div class="widget footer-widget h-100">
                        <h3 class="widget_title">Get In Touch</h3>
                        <div class="">
                            <div class="info-box_text mb-3 d-flex align-items-center">
                                <div class="icon me-3 flex-shrink-0"><img src="<?php echo $base_path; ?>../assets/img/icon/location-dot.svg" alt="Location"></div>
                                <div class="details">
                                    <p class="mb-0"><?php echo htmlspecialchars($contact_address); ?></p>
                                </div>
                            </div>
                            <div class="info-box_text mb-3 d-flex align-items-center">
                                <div class="icon me-3 flex-shrink-0"><img src="<?php echo $base_path; ?>../assets/img/icon/phone.svg" alt="Phone"></div>
                                <div class="details">
                                    <p class="mb-0"><a href="tel:<?php echo htmlspecialchars($phone_href); ?>" class="info-box_link"><?php echo htmlspecialchars($contact_mobile); ?></a></p>
                                </div>
                            </div>
                            <div class="info-box_text mb-3 d-flex align-items-center">
                                <div class="icon me-3 flex-shrink-0"><img src="<?php echo $base_path; ?>../assets/img/icon/envelope.svg" alt="Email"></div>
                                <div class="details">
                                    <p class="mb-0"><a href="mailto:<?php echo htmlspecialchars($contact_email); ?>"
                                            class="info-box_link"><?php echo htmlspecialchars($contact_email); ?></a></p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Payment Methods and Trust Badges -->
    <div class="payment-trust-wrap  py-3">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-4 col-md-4">
                    <div class="payment-methods text-start mb-3 mb-lg-0">
                        <h6 class="text-white mb-3">Accepted Payment Methods</h6>
                        <div class="d-flex justify-content-start align-items-center gap-3">
                            <i class="fab fa-cc-visa fa-2x text-white" title="Visa"></i>
                            <i class="fab fa-cc-mastercard fa-2x text-white" title="Mastercard"></i>
                            <i class="fab fa-cc-amex fa-2x text-white" title="American Express"></i>
                            <i class="fab fa-cc-discover fa-2x text-white" title="Discover"></i>
                            <i class="fab fa-cc-paypal fa-2x text-white" title="PayPal"></i>
                            <i class="fab fa-google-pay fa-2x text-white" title="Google Pay"></i>
                            <i class="fab fa-cc-apple-pay fa-2x text-white" title="Apple Pay"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="trust-badges text-center">
                        <div class="d-flex justify-content-center align-items-center gap-3">
                            <img src="<?php echo $base_path; ?>../assets/img/icon/tripadvisor.png" alt="TripAdvisor" style="width: auto;">
                            <span class="text-white text-start">Trusted by <br> Travelers Worldwide</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="payment-on-arrival text-center">
                        <div class="" >
                            <img src="<?php echo $base_path; ?>../assets/img/icon/payment-cash.png" alt="Payment on Arrival" style=" width: auto; margin-bottom: 5px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-wrap">
        <div class="container">
            <div class="row align-items-center g-3">
                <div class="col-lg-4 col-md-12 text-center text-md-start">
                    <p class="copyright-text mb-0">Copyright 2025 <a href="<?php echo $base_path; ?>index.php" style="color: orange;">India Day Trip</a>. All Rights
                        Reserved.</p>
                </div>
                <div class="col-lg-4 col-md-12 text-center">
                    <p class="copyright-text mb-0">Developed by <a href="https://denexiasolution.com" target="_blank" style="color: orange;">Denexia It Solution</a></p>
                </div>
                <div class="col-lg-4 col-md-12 text-center text-md-end">
                    <div class="footer-links">
                        <a href="<?php echo $base_path; ?>privacy-policy/index.php" style="color: orange;">Privacy Policy</a> |
                        <a href="<?php echo $base_path; ?>terms-conditions/index.php" style="color: orange;">Terms & Conditions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>