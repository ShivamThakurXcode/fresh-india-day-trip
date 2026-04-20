<?php
require_once __DIR__ . '/../config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Book Now - India Day Trip | Reserve Your Agra Tour</title>
    <meta name="author" content="India Day Trip">
    <meta name="description"
        content="Book your tour with India Day Trip. Choose from Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours. Secure online booking for Agra, Delhi, and Jaipur adventures.">
    <meta name="keywords"
        content="Book tour India Day Trip, reserve Agra tours, online booking Taj Mahal, Golden Triangle tour booking">
    <meta name="robots" content="INDEX,FOLLOW">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <link rel="canonical" href="https://indiadaytrip.com/to_book/">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://indiadaytrip.com/to_book/">
    <meta property="og:title" content="Book Now - India Day Trip | Reserve Your Agra Tour">
    <meta property="og:description"
        content="Book your tour with India Day Trip. Choose from Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours. Secure online booking for Agra, Delhi, and Jaipur adventures.">
    <meta property="og:image" content="https://indiadaytrip.com/assets/img/hero/hero-agra.webp">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://indiadaytrip.com/to_book/">
    <meta property="twitter:title" content="Book Now - India Day Trip | Reserve Your Agra Tour">
    <meta property="twitter:description"
        content="Book your tour with India Day Trip. Choose from Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours. Secure online booking for Agra, Delhi, and Jaipur adventures.">
    <meta property="twitter:image" content="https://indiadaytrip.com/assets/img/hero/hero-agra.webp">

    <?php include '../components/links.php'; ?>
</head>

<body>
    <?php include '../components/preloader.php'; ?>
    <?php include '../components/sidebar.php'; ?>
    <?php include '../components/header.php'; ?>

    <div class="breadcumb-wrapper" data-bg-src="../assets/img/bg/breadcumb-bg.webp">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Book Now</h1>
                <ul class="breadcumb-menu">
                    <li><a href="../index.php">Home</a></li>
                    <li>Book Now</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="space">
        <div class="container">
            <div class="title-area text-center mb-5 pb-5">
                <span class="sub-title">Book Your Tour</span>
                <h2 class="sec-title">Reserve Your Adventure</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form action="../api/mail.php" method="POST" class="booking-form ajax-contact">
                        <!-- Form type identifier -->
                        <input type="hidden" name="form_type" value="booking">
                        <!-- Anti-Spam: Honeypot field (hidden) - leave empty -->
                        <input type="text" name="website_url" class="honeypot" style="position:absolute; left:-9999px;" tabindex="-1" autocomplete="off">
                        <!-- Anti-Spam: CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                        <div class="row">
                            <div class="col-md-6 form-group position-relative">
                                <input type="text" class="form-control form-control-lg" name="first_name"
                                    id="first_name" placeholder="First Name *" required>
                                <i class="fa-light fa-user input-icon"></i>
                            </div>
                            <div class="col-md-6 form-group position-relative">
                                <input type="text" class="form-control form-control-lg" name="last_name" id="last_name"
                                    placeholder="Last Name *" required>
                                <i class="fa-light fa-user input-icon"></i>
                            </div>
                            <div class="col-md-6 form-group position-relative">
                                <input type="email" class="form-control form-control-lg" name="email" id="email"
                                    placeholder="Your Email *" required>
                                <i class="fa-light fa-envelope input-icon"></i>
                            </div>
                            <div class="col-md-6 form-group position-relative">
                                <input type="tel" class="form-control form-control-lg" name="phone" id="phone"
                                    placeholder="Phone Number *" required>
                                <i class="fa-light fa-phone input-icon"></i>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label">Select Tour Type *</label>
                                <select name="tour_type" id="tour_type" class="form-select form-select-lg" required>
                                    <option value="">Choose Tour Type</option>
                                    <option value="Same Day Tours">Same Day Tours</option>
                                    <option value="Taj Mahal Tours">Taj Mahal Tours</option>
                                    <option value="Golden Triangle Tours">Golden Triangle Tours</option>
                                    <option value="Agra Tours">Agra Tours</option>
                                    <option value="Delhi Tours">Delhi Tours</option>
                                    <option value="Jaipur Tours">Jaipur Tours</option>
                                    <option value="Rajasthan Tours">Rajasthan Tours</option>
                                    <option value="Varanasi Tours">Varanasi Tours</option>
                                    <option value="Custom Tour">Custom Tour</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group position-relative">
                                <input type="date" class="form-control form-control-lg" name="travel_date"
                                    id="travel_date" required>
                                <i class="fa-light fa-calendar input-icon"></i>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label">Number of Adults *</label>
                                <select name="adults" id="adults" class="form-select form-select-lg" required>
                                    <option value="">Select Adults</option>
                                    <option value="1">1 Adult</option>
                                    <option value="2">2 Adults</option>
                                    <option value="3">3 Adults</option>
                                    <option value="4">4 Adults</option>
                                    <option value="5">5 Adults</option>
                                    <option value="6">6 Adults</option>
                                    <option value="7">7 Adults</option>
                                    <option value="8">8+ Adults</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label">Number of Children</label>
                                <select name="children" id="children" class="form-select form-select-lg">
                                    <option value="0">No Children</option>
                                    <option value="1">1 Child</option>
                                    <option value="2">2 Children</option>
                                    <option value="3">3 Children</option>
                                    <option value="4">4 Children</option>
                                    <option value="5">5 Children</option>
                                </select>
                            </div>
                            <div class="col-12 form-group position-relative">
                                <textarea name="special_requests" id="special_requests" cols="30" rows="4"
                                    class="form-control form-control-lg"
                                    placeholder="Special Requests or Additional Information"></textarea>
                                <i class="fa-light fa-comment-dots input-icon textarea-icon"></i>
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
                            <div class="col-12 form-group">
                                <label class="form-label">Security Check <span class="text-danger">*</span></label>
                                <div class="input-group" style="max-width: 200px;">
                                    <span class="input-group-text" style="background:#f8f9fa;"><?php echo $num1; ?> + <?php echo $num2; ?> = </span>
                                    <input type="number" class="form-control form-control-lg" name="captcha" required="" placeholder="?" style="max-width:80px !important; padding: 2px 5px;">
                                    <input type="hidden" name="use_captcha" value="1">
                                </div>
                            </div>
                            <div class="form-btn col-12 mt-4 text-center">
                                <button type="submit" class="th-btn style3 btn-lg px-5 py-3">Book Now <i
                                        class="fa-light fa-paper-plane ms-2"></i></button>
                            </div>
                        </div>
                        <p class="form-messages mb-0 mt-3"></p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var bookingForm = document.querySelector('.booking-form');
        if (bookingForm) {
            bookingForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                var formMessages = bookingForm.querySelector('.form-messages');
                var submitBtn = bookingForm.querySelector('button[type="submit"]');
                var originalBtnText = submitBtn.innerHTML;
                submitBtn.innerHTML = 'Sending...';
                submitBtn.disabled = true;
                
                var formData = new FormData(bookingForm);
                
                fetch('../api/mail.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                    
                    if (data.includes('Thank you') || data.includes('confirmation') || data.includes('Confirmation') || data.includes('success')) {
                        if (formMessages) {
                            formMessages.className = 'form-messages mb-0 mt-3 text-success';
                            formMessages.textContent = data;
                        }
                        bookingForm.reset();
                        location.reload();
                    } else if (data.includes('error') || data.includes('Error') || data.includes('Sorry')) {
                        if (formMessages) {
                            formMessages.className = 'form-messages mb-0 mt-3 text-danger';
                            formMessages.textContent = data;
                        }
                    } else {
                        if (formMessages) {
                            formMessages.className = 'form-messages mb-0 mt-3 text-danger';
                            formMessages.textContent = data;
                        }
                    }
                })
                .catch(error => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                    if (formMessages) {
                        formMessages.className = 'form-messages mb-0 mt-3 text-danger';
                        formMessages.textContent = 'Connection error. Please try again.';
                    }
                });
            });
        }
    });
    </script>

    <style>
        /* Booking Form Improvements */
        .booking-form .form-group {
            margin-bottom: 1.5rem;
        }

        .booking-form .form-control {
            width: 100%;
            padding: 16px 18px 16px 54px;
            font-size: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            background: #fff;
            transition: all 0.3s ease;
            position: relative;
            min-height: 54px;
        }

        .booking-form .form-control:focus {
            border-color: #1CA8CB;
            box-shadow: 0 0 0 0.25rem rgba(28, 168, 203, 0.15);
            outline: none;
        }

        .booking-form .form-control::placeholder {
            color: #999;
            opacity: 1;
            font-weight: 500;
        }

        .booking-form .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-48%);
            color: #6c757d;
            font-size: 1.25rem;
            line-height: 1;
            pointer-events: none;
            z-index: 5;
        }

        .booking-form .textarea-icon {
            top: 22px;
            transform: none;
        }

        .booking-form .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #113D48;
            font-size: 0.95rem;
        }

        .booking-form .form-select {
            width: 100%;
            padding: 16px 18px 16px 18px;
            font-size: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            background: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 54px;
        }

        .booking-form .form-select:focus {
            border-color: #1CA8CB;
            box-shadow: 0 0 0 0.25rem rgba(28, 168, 203, 0.15);
            outline: none;
        }

        .booking-form .btn-lg {
            padding: 16px 40px;
            font-size: 1.15rem;
            font-weight: 600;
        }

        .booking-form .th-btn {
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .booking-form .th-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(28, 168, 203, 0.35);
        }

        @media (max-width: 768px) {
            .booking-form .form-control {
                padding: 14px 14px 14px 50px;
                font-size: 0.95rem;
                min-height: 50px;
            }

            .booking-form .form-select {
                padding: 14px 14px;
                min-height: 50px;
            }

            .booking-form .input-icon {
                left: 16px;
                font-size: 1rem;
            }
        }
    </style>

    <?php include '../components/footer.php'; ?>


    <?php include '../components/script.php'; ?>
</body>

</html>