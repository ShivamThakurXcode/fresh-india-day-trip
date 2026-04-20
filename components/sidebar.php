<?php
// Calculate base path based on including file location
$script_path = dirname($_SERVER['SCRIPT_NAME']);
$depth = substr_count($script_path, '/');
if ($depth > 0) {
    $base_path = str_repeat('../', $depth);
} else {
    $base_path = '';
}

$contact_address = getSetting('contact_address') ?: 'Shop No. 2, Gupta Market, Tajganj, Agra';
$contact_email = getSetting('contact_email') ?: 'indiadaytrip@gmail.com';
$contact_mobile = getSetting('contact_mobile') ?: '+91 81260 52755';
$social_facebook = getSetting('social_facebook') ?: 'https://www.facebook.com/indiadaytrip';
$social_twitter = getSetting('social_twitter') ?: 'https://www.twitter.com/indiadaytrip';
$social_instagram = getSetting('social_instagram') ?: 'https://www.instagram.com/indiadaytrip';

$phone_href = preg_replace('/[^0-9+]/', '', $contact_mobile);
?>
<div class="sidemenu-wrapper sidemenu-info">
    <div class="sidemenu-content"><button class="closeButton sideMenuCls"><i class="far fa-times"></i></button>
        <div class="widget">
            <div class="th-widget-about">
                <div class="about-logo"><a href="<?php echo $base_path; ?>index.php"><img src="<?php echo $base_path; ?>assets/img/logo/logo-header.webp"
                            alt="India Day Trip" style="height: 50px; width: auto;"></a>
                </div>
                <p class="about-text">India Day Trip is an Agra-based tour and travel company specializing in Same
                    Day Tours, Taj Mahal Tours, and Golden Triangle Tours.</p>
                <div class="th-social"><a href="<?php echo htmlspecialchars($social_facebook); ?>"><i class="fab fa-facebook-f"></i></a> <a
                    href="<?php echo htmlspecialchars($social_twitter); ?>"><i class="fab fa-twitter"></i></a> <a
                        href="https://www.linkedin.com/"><i class="fab fa-linkedin-in"></i></a> <a
                    href="<?php echo htmlspecialchars($social_instagram); ?>"><i class="fab fa-instagram"></i></a></div>
            </div>
        </div>
        <div class="widget">
            <h3 class="widget_title">Recent Posts</h3>
            <div class="recent-post-wrap">
                <div class="recent-post">
                    <div class="media-img"><a href="<?php echo $base_path; ?>blog-detail.php?slug=best-time-to-visit-taj-mahal"><img
                                src="<?php echo $base_path; ?>assets/img/blog/recent-post-1-1.webp" alt="Blog Image"></a></div>
                    <div class="media-body">
                        <div class="recent-post-meta"><a href="<?php echo $base_path; ?>blog/"><i class="far fa-calendar"></i>Sep 09,
                                2024</a></div>
                        <h4 class="post-title"><a class="text-inherit" href="<?php echo $base_path; ?>blog-detail.php?slug=best-time-to-visit-taj-mahal">Best Time to Visit
                                Taj Mahal</a></h4>
                    </div>
                </div>
                <div class="recent-post">
                    <div class="media-img"><a href="<?php echo $base_path; ?>blog-detail.php?slug=perfect-5-day-golden-triangle-itinerary"><img
                                src="<?php echo $base_path; ?>assets/img/blog/recent-post-1-2.webp" alt="Blog Image"></a></div>
                    <div class="media-body">
                        <div class="recent-post-meta"><a href="<?php echo $base_path; ?>blog/"><i class="far fa-calendar"></i>Sep 10,
                                2024</a></div>
                        <h4 class="post-title"><a class="text-inherit" href="<?php echo $base_path; ?>blog-detail.php?slug=perfect-5-day-golden-triangle-itinerary">Golden Triangle
                                Itinerary Guide</a></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget">
            <h3 class="widget_title">Get In Touch</h3>
            <div class="th-widget-contact">
                <div class="info-box_text">
                    <div class="icon"><img src="<?php echo $base_path; ?>assets/img/icon/phone.svg" alt="img"></div>
                    <div class="details">
                        <p><a href="tel:<?php echo htmlspecialchars($phone_href); ?>" class="info-box_link"><?php echo htmlspecialchars($contact_mobile); ?></a></p>
                    </div>
                </div>
                <div class="info-box_text">
                    <div class="icon"><img src="<?php echo $base_path; ?>assets/img/icon/envelope.svg" alt="img"></div>
                    <div class="details">
                        <p><a href="mailto:<?php echo htmlspecialchars($contact_email); ?>" class="info-box_link"><?php echo htmlspecialchars($contact_email); ?></a>
                        </p>
                    </div>
                </div>
                <div class="info-box_text">
                    <div class="icon"><img src="<?php echo $base_path; ?>assets/img/icon/location-dot.svg" alt="img"></div>
                    <div class="details">
                        <p><?php echo htmlspecialchars($contact_address); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>