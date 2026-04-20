<?php require_once __DIR__ . '/../config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$contact_address = getSetting('contact_address') ?: 'Shop No. 2, Gupta Market, Tajganj, Agra, Uttar Pradesh, India';
$contact_email = getSetting('contact_email') ?: 'indiadaytrip@gmail.com';
$contact_mobile = getSetting('contact_mobile') ?: '+91 81260 52755';
$social_facebook = getSetting('social_facebook') ?: '#';
$social_twitter = getSetting('social_twitter') ?: '#';
$social_instagram = getSetting('social_instagram') ?: '#';
$phone_href = preg_replace('/[^0-9+]/', '', $contact_mobile);
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="author" content="India Day Trip">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <?php renderSEOHead('contact'); ?>
    <meta property="twitter:image" content="https://indiadaytrip.com/assets/img/bg/contact_bg_1.webp">

    <!-- Schema.org Structured Data for Contact Page -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ContactPage",
        "name": "Contact India Day Trip",
        "url": "https://indiadaytrip.com/contact/",
        "description": "Contact page for India Day Trip travel agency",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Shop No. 2, Gupta Market, Tajganj",
            "addressLocality": "Agra",
            "addressRegion": "Uttar Pradesh",
            "postalCode": "282001",
            "addressCountry": "IN"
        },
        "telephone": "+919897030802",
        "email": "indiadaytrip@gmail.com"
    }
    </script>

    <?php include '../components/links.php'; ?>
</head>

<body>
    <?php include '../components/preloader.php'; ?>
    <?php include '../components/sidebar.php'; ?>
    <?php include '../components/header.php'; ?>

    <div class="breadcumb-wrapper" data-bg-src="../assets/img/bg/breadcumb-bg.webp">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Contact Us</h1>
                <ul class="breadcumb-menu">
                    <li><a href="../index.php">Home</a></li>
                    <li>Contact Us</li>
                </ul>
            </div>
        </div>
    </div>
    <style>
        .contact-info-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .contact-form-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .contact-cards-row {
            gap: 30px;
        }

        .contact-info-box h3 {
            color: #1CA8CB;
            margin-bottom: 20px;
        }

        .contact-info-box ul li {
            margin-bottom: 15px;
        }

        .contact-info-box ul li strong {
            color: #333;
        }

        .social-links a {
            display: inline-block;
            margin-right: 15px;
            font-size: 24px;
            color: #666;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: #1CA8CB;
        }

        .form-label {
            font-weight: 600;
            color: #333;
        }

        .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px 15px;
        }

        .form-control:focus {
            border-color: #1CA8CB;
            box-shadow: 0 0 0 0.2rem rgba(28, 168, 203, 0.25);
        }

        .th-btn {
            background: #0056b3;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .th-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        @media (max-width: 991px) {
            .contact-cards-row {
                flex-direction: column;
            }

            .contact-info-box,
            .contact-form-box {
                margin-bottom: 30px;
            }
        }
    </style>
    <!-- Contact Section -->
    <section class="contact-area space" style="background: #f7fafd;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="title-area text-center mb-5">
                        <span class="sub-title">Get in Touch</span>
                        <h2 class="sec-title">Contact India Day Trip</h2>
                        <p class="mt-2">Have questions, need a custom tour, or want to book your next adventure? Fill
                            out the form below or reach us directly. Our team is here to help you plan the perfect
                            journey!</p>
                    </div>
                </div>
            </div>
            <div class="row contact-cards-row mb-5">
                <div class="col-lg-5 d-flex">
                    <div class="contact-info-box w-100">
                        <h3 class="mb-4 text-center"><i class="fas fa-address-book me-2 "></i>Contact Information</h3>
                        <ul class="list-unstyled mb-4">
                            <li class="d-flex align-items-start">
                                <span class="me-3"><i class="fas fa-map-marker-alt "></i></span>
                                <span>
                                    <strong>Address:</strong><br>
                                    <?php echo htmlspecialchars($contact_address); ?>
                                </span>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-3"><i class="fas fa-envelope "></i></span>
                                <span>
                                    <strong>Email:</strong><br>
                                    <a href="mailto:<?php echo htmlspecialchars($contact_email); ?>"><?php echo htmlspecialchars($contact_email); ?></a>
                                </span>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-3"><i class="fas fa-phone-alt "></i></span>
                                <span>
                                    <strong>Phone:</strong><br>
                                    <a href="tel:<?php echo htmlspecialchars($phone_href); ?>"><?php echo htmlspecialchars($contact_mobile); ?></a>
                                </span>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-3"><i class="fas fa-clock "></i></span>
                                <span>
                                    <strong>Working Hours:</strong><br>
                                    Mon - Sun: 8:00 AM - 10:00 PM
                                </span>
                            </li>
                        </ul>
                        <div class="social-links mt-auto text-center">
                            <a href="<?php echo htmlspecialchars($social_facebook); ?>" class="me-2" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="<?php echo htmlspecialchars($social_instagram); ?>" class="me-2" title="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="<?php echo htmlspecialchars($social_twitter); ?>" class="me-2" title="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-flex">
                    <div class="contact-form-box w-100">

                        <form action="../api/mail.php" method="POST" class="row g-3" id="contactForm">
                            <input type="hidden" name="form_type" value="contact">
                            <!-- Anti-Spam: Honeypot field (hidden) - leave empty -->
                            <input type="text" name="website_url" class="honeypot" style="position:absolute; left:-9999px;" tabindex="-1" autocomplete="off">
                            <!-- Anti-Spam: CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                            <div class="col-md-6">
                                <label for="contactName" class="form-label">Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contactName" name="name" required=""
                                    placeholder="Your Name">
                            </div>
                            <div class="col-md-6">
                                <label for="contactEmail" class="form-label">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="contactEmail" name="email" required=""
                                    placeholder="you@email.com">
                            </div>
                            <div class="col-md-6">
                                <label for="contactPhone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="contactPhone" name="phone"
                                    placeholder="+91-XXXXXXXXXX">
                            </div>
                            <div class="col-md-6">
                                <label for="contactSubject" class="form-label">Subject <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contactSubject" name="subject" required=""
                                    placeholder="Subject">
                            </div>
                            <div class="col-12">
                                <label for="contactMessage" class="form-label">Message <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="contactMessage" name="message" rows="4" required=""
                                    placeholder="How can we help you?"></textarea>
                            </div>
                            <?php
                            // Generate simple math CAPTCHA with all needed session variables
                            $num1 = rand(1, 9);
                            $num2 = rand(1, 9);
                            $_SESSION['captcha_num1'] = $num1;
                            $_SESSION['captcha_num2'] = $num2;
                            $_SESSION['captcha_answer'] = $num1 + $num2;
                            // Track form generation time for bot detection
                            $_SESSION['form_time'] = time();
                            ?>
                            <div class="col-12">
                                <label for="captcha" class="form-label">Security Check <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f8f9fa;"><?php echo $num1; ?> + <?php echo $num2; ?> = </span>
                                    <input type="number" class="form-control" id="captcha" name="captcha" required="" placeholder="Enter result" style="max-width:120px;">
                                    <input type="hidden" name="use_captcha" value="1">
                                </div>
                            </div>
                            <div class="col-12 text-center mt-3">
                                <button type="submit" class="th-btn style3 th-icon px-5 py-2">Send Message</button>
                            </div>
                            <p class="form-messages mt-3"></p>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row mt-5 justify-content-center">
                <div class="col-lg-10">
                    <!-- Google Map Embed (optional, matches theme) -->
                    <div class="rounded shadow overflow-hidden" style="height: 350px;">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3559.726073289074!2d78.0395673150447!3d27.17501578301709!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x397471d7c2b2c7b1%3A0x2b2b2b2b2b2b2b2b!2sTaj%20Mahal!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade" title="Taj Mahal Location"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <?php include '../components/footer.php'; ?>



    <?php include '../components/script.php'; ?>

    <script>
        // Handle contact form submission via AJAX
        document.getElementById('contactForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const formMessages = form.nextElementSibling;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = 'Sending...';
            submitBtn.disabled = true;
            if (formMessages) {
                formMessages.className = 'form-messages mt-3';
                formMessages.textContent = '';
            }

            fetch('../api/mail.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;

                    if (data.includes('Thank you') || data.includes('confirmation') || data.includes('Confirmation') || data.includes('success')) {
                        // Show success
                        if (formMessages) {
                            formMessages.className = 'form-messages mt-3 text-success';
                            formMessages.textContent = data;
                        }
                        form.reset();
                        // Regenerate CAPTCHA after form reset
                        location.reload();
                    } else if (data.includes('error') || data.includes('Error') || data.includes('Sorry')) {
                        // Show error
                        if (formMessages) {
                            formMessages.className = 'form-messages mt-3 text-danger';
                            formMessages.textContent = data;
                        }
                    } else {
                        // Unknown response - show as error
                        if (formMessages) {
                            formMessages.className = 'form-messages mt-3 text-danger';
                            formMessages.textContent = data;
                        }
                    }
                })
                .catch(error => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;

                    if (formMessages) {
                        formMessages.className = 'form-messages mt-3 text-danger';
                        formMessages.textContent = 'Sorry, there was a connection error. Please try again.';
                    }
                });
        });

        // Check for success parameter in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === '1') {
            const successAlert = document.getElementById('contactSuccess');
            if (successAlert) {
                successAlert.classList.remove('d-none');
                // Scroll to the form
                successAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Hide after 5 seconds
                setTimeout(() => {
                    successAlert.classList.add('d-none');
                }, 5000);
            }
        }
    </script>
</body>

</html>