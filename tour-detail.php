<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($tour)) {
    header('Location: index.php');
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
    <?php renderTourSEOHead($tour); ?>

    <?php include 'components/links.php'; ?>

    <style>
        /* Tour Detail Page Custom Styling */

        /* Hero Section Enhancements */
        .tour-hero {
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            position: relative;
        }

        .tour-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;

            z-index: 1;
        }

        .tour-hero .container {
            position: relative;
            z-index: 2;
        }

        .tour-badge {
            background: linear-gradient(45deg, #1CA8CB, #113D48);
            color: white;
            padding: 5px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .breadcumb-title {
            color: #113D48;
            font-size: 2rem !important;
            font-weight: 700;
            margin-bottom: 15px;

        }

        .hero-meta {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 30px;
            justify-content: start;
        }
        
        ul {
            list-style-type: none;
        }
        
        ol, ul {
            padding-left: 0px;
        }
.booking-form {
    padding: 22px 22px;
}
        .hero-meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hero-meta-item i {
            color: #1CA8CB;
            font-size: 20px;
        }

        /* Tour Details Section */
        .space-top {
            padding-top: 80px;
        }

        .space-extra-bottom {
            padding-bottom: 80px;
        }

        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Overview Section */
        .tour-highlight {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 30px;
            border-radius: 15px;
            margin-top: 20px;
            border-left: 5px solid #1CA8CB;
        }

        .tour-highlight h4 {
            color: #113D48;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .tour-highlight div {
            font-size: 1.2rem !important;
            line-height: 1.7;
            color: #333;
        }

        /* Gallery Section */
        .tour-gallery-single img,
        .tour-gallery-two img,
        .gallery-left img,
        .gallery-right img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .tour-gallery-single img:hover,
        .tour-gallery-two img:hover,
        .gallery-left img:hover,
        .gallery-right img:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .tour-gallery-single {
            margin-top: 20px;
        }

        .tour-gallery-two {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .tour-gallery-two img {
            width: 50%;
        }

        .tour-gallery-main {
            display: grid;
            grid-template-columns: 2fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 10px;
            margin-top: 20px;
            position: relative;
        }

        .gallery-img {
            width: 100%;
            height: 100%;
            min-height: 250px;
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .gallery-img:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .gallery-large {
            grid-column: 1;
            grid-row: 1 / 3;
            min-height: 520px;
        }

        .gallery-small:nth-child(2) {
            grid-column: 2;
            grid-row: 1;
            min-height: 250px;
        }

        .gallery-small:nth-child(3) {
            grid-column: 2;
            grid-row: 2;
            min-height: 250px;
        }

        .image-pill {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: white;
            border: 1px solid white;
            padding: 8px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
            z-index: 10;
        }

        .image-pill:hover {
            transform: scale(1.05);
        }

        .image-pill i {
            font-size: 14px;
        }

        .gallery-modal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            z-index: 800;
        }

        .gallery-modal-img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .gallery-modal-img:hover {
            transform: scale(1.05);
        }

        /* Highlights Section */
        .highlight-row {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }

        .highlight-row:hover {
            transform: translateY(-5px);
        }

        .highlight-icon {
            flex-shrink: 0;
            width: 60px;
            height: 60px;
            background: #0c2d62;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .highlight-content h4 {
            color: #113D48;
            font-size: 1.25rem;
            margin-bottom: 10px;
        }

        /* Tour Info List */
        .tour-info-list .info-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: background 0.3s ease;
        }

        .tour-info-list .info-item:hover {
            background: #e9ecef;
        }

        .info-icon {
            color: #1CA8CB;
            font-size: 24px;
        }

        .info-content h6 {
            color: #113D48;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        /* Itinerary Section */
        .itinerary-step {
            background: white;
            border-radius: 15px;
            padding: 25px 30px;
            margin-bottom: 30px;
            position: relative;
            border: 1px solid #1CA8CB;
        }

        .itinerary-step-number {
            display: none;
        }

        .itinerary-step h4 {
            color: #113D48;
            font-size: 1.3rem;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .itinerary-step p {
            color: #555;
            line-height: 1.7;
            margin: 0;
        }

        /* Inclusions/Exclusions */
        .inclusion-list li,
        .exclusion-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            position: relative;
            padding-left: 30px;
        }

        .inclusion-list li:last-child,
        .exclusion-list li:last-child {
            border-bottom: none;
        }

        .inclusion-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
            font-size: 18px;
        }

        .exclusion-list li::before {
            content: '✗';
            position: absolute;
            left: 0;
            color: #dc3545;
            font-weight: bold;
            font-size: 18px;
        }

        /* Related Tours */
        .related-tour-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            border: 0.5px solid #113d487a;
            height: 100%;
        }

        /* FAQ Section */
        .faq-section {
            margin-top: 40px;
        }
        
        .faq-section .sec-title {
            margin-bottom: 30px;
        }
        
        .faq-item {
            background: white;
            border: 1px solid #e8e8e8;
            border-radius: 12px;
            margin-bottom: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .faq-item:hover {
            border-color: #1CA8CB;
        }
        
        .faq-question {
            width: 100%;
            background: none;
            border: none;
            padding: 20px 25px;
            text-align: left;
            font-size: 1.1rem;
            font-weight: 600;
            color: #113D48;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .faq-question:hover {
            background: #f8f9fa;
        }
        
        .faq-question::after {
            content: '+';
            font-size: 1.5rem;
            color: #1CA8CB;
            transition: transform 0.3s ease;
        }
        
        .faq-item.active .faq-question::after {
            transform: rotate(45deg);
        }
        
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0 25px;
        }
        
        .faq-item.active .faq-answer {
            max-height: 500px;
            padding: 0 25px 20px;
        }
        
        .faq-answer p {
            color: #555;
            line-height: 1.7;
            margin: 0;
        }

        /* Full Width FAQ Section */
        .faq-section-full {
            background: #f8f9fa;
        }
        
        .faq-section-full .sec-title {
            font-size: 2rem;
            font-weight: 700;
            color: #113D48;
            margin-bottom: 30px;
        }
        
        .faq-section-full .title-area {
            margin-bottom: 40px;
        }

        .tour-img {
            position: relative;
            overflow: hidden;
            height: 200px;
        }

        .tour-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tour-content {
            padding: 20px;
        }

        .tour-title {
            color: #113D48;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .tour-destination {
            color: #6E7070;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .tour-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .tour-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #6E7070;
            font-size: 14px;
        }

        .tour-rating i {
            color: #FFB539;
        }

        /* Enhanced Typography */
        .sec-title {
            color: #113D48;
            font-weight: 700;
            font-size: 1.5rem !important;
            margin-bottom: 25px;
            position: relative;
            letter-spacing: -0.5px;
        }

        /* .sec-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(45deg, #1CA8CB, #113D48);
            border-radius: 2px;
        } */

        /* Button Styling to Match Theme */
        .th-btn {
            background-color: #113D48;
            border: none;
            color: #ffffff;
        }

        .th-btn:hover,
        .th-btn.active {
            background-color: #1CA8CB;
            color: #ffffff;
        }

        .th-btn:before {
            background-color: #1CA8CB;
        }

        .th-btn:hover:before {
            background-color: #113D48;
        }

        /* Improved Overview Section */
        .tour-highlight {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 30px;
            border-radius: 15px;
            margin-top: 20px;
            border-left: 5px solid #1CA8CB;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .tour-highlight h4 {
            color: #113D48;
            font-size: 1.5rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .tour-highlight div {
            font-size: 1.2rem !important;
            line-height: 1.7;
            color: #333;
        }

        /* Enhanced Gallery */
        .tour-gallery img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 15px;
            transition: all 0.4s ease;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .tour-gallery img:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        /* Better Highlights - Compact Version */
        .highlight-row {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            margin-bottom: 12px;
            border: 1px solid rgba(28, 168, 203, 0.08);
        }

        .highlight-row:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            border-color: rgba(28, 168, 203, 0.2);
        }

        .highlight-icon {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            background: #0c2d62;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            box-shadow: 0 3px 10px rgba(12, 45, 98, 0.25);
        }

        .highlight-content h4 {
            color: #113D48;
            font-size: 1.1rem;
            margin-bottom: 0;
            font-weight: 600;
            line-height: 1.4;
        }

        /* Enhanced Tour Info - Compact */
        .tour-info-list .info-item {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            border: 1px solid rgba(28, 168, 203, 0.08);
        }

        .tour-info-list .info-item:hover {
            background: linear-gradient(135deg, #ffffff, #f0f8ff);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border-color: rgba(28, 168, 203, 0.15);
        }

        .info-icon {
            color: #1CA8CB;
            font-size: 22px;
            background: rgba(28, 168, 203, 0.1);
            padding: 8px;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-content h6 {
            color: #113D48;
            font-size: 1rem;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .info-content p {
            font-size: 0.9rem;
            color: #6E7070;
            margin: 0;
        }

        /* Enhanced Inclusions/Exclusions - Compact */
        .inclusion-list li,
        .exclusion-list li {
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
            position: relative;
            padding-left: 30px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .inclusion-list li:last-child,
        .exclusion-list li:last-child {
            border-bottom: none;
        }

        .inclusion-list li:hover,
        .exclusion-list li:hover {
            background: rgba(28, 168, 203, 0.03);
            padding-left: 35px;
        }

        .inclusion-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
            font-size: 14px;
            background: rgba(40, 167, 69, 0.12);
            padding: 4px;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            top: 50%;
            transform: translateY(-50%);
        }

        .exclusion-list li::before {
            content: '✗';
            position: absolute;
            left: 0;
            color: #dc3545;
            font-weight: bold;
            font-size: 14px;
            background: rgba(220, 53, 69, 0.12);
            padding: 4px;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Better Related Tours */
        .related-tour-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            height: 100%;
        }

        .tour-img {
            position: relative;
            overflow: hidden;
            height: 220px;
        }

        .tour-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tour-content {
            padding: 25px;
        }

        .tour-title {
            color: #113D48;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .tour-destination {
            color: #6E7070;
            font-size: 14px;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .tour-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .tour-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #6E7070;
            font-size: 14px;
            font-weight: 500;
        }

        .tour-rating i {
            color: #FFB539;
        }


        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .breadcumb-title {
                font-size: 2.5rem;
            }

            .hero-meta {
                gap: 20px;
            }

            .tour-gallery-two {
                flex-direction: column;
                gap: 15px;
            }

            .tour-gallery-two img {
                width: 100%;
            }

            .tour-gallery-main {
                grid-template-columns: 1fr;
                grid-template-rows: auto;
                gap: 15px;
            }

            .gallery-large {
                grid-column: 1;
                grid-row: 1;
            }

            .gallery-small:nth-child(2) {
                grid-column: 1;
                grid-row: 2;
            }

            .gallery-small:nth-child(3) {
                grid-column: 1;
                grid-row: 3;
            }

            .gallery-modal-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 10px;
            }

            .highlight-row {
                flex-direction: column;
                text-align: center;
                padding: 15px;
            }

            .highlight-icon {
                align-self: center;
                width: 45px;
                height: 45px;
                font-size: 18px;
            }

            .highlight-content h4 {
                font-size: 1rem;
            }

            .itinerary-step {
                padding: 20px;
            }

            .sec-title {
                font-size: 1.4rem;
            }

            .tour-info-list .info-item {
                padding: 15px;
            }
        }

        @media (max-width: 576px) {
            .breadcumb-title {
                font-size: 2rem;
            }

            .hero-meta {
                flex-direction: column;
                gap: 15px;
            }

            .tour-gallery-two {
                flex-direction: column;
                gap: 15px;
            }

            .tour-gallery-two img {
                width: 100%;
            }

            .tour-gallery-main {
                grid-template-columns: 1fr;
                grid-template-rows: auto;
                gap: 15px;
            }

            .gallery-large {
                grid-column: 1;
                grid-row: 1;
            }

            .gallery-small:nth-child(2) {
                grid-column: 1;
                grid-row: 2;
            }

            .gallery-small:nth-child(3) {
                grid-column: 1;
                grid-row: 3;
            }

            .gallery-modal-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .highlight-row {
                padding: 15px;
            }

            .itinerary-step {
                padding: 20px;
            }

            .sec-title {
                font-size: 1.2rem;
            }

            .tour-info-list .info-item {
                padding: 15px;
            }

            .info-icon {
                font-size: 24px;
                padding: 8px;
            }

            .info-content h6 {
                font-size: 1.1rem;
            }
            
            .faq-question {
                font-size: 1rem;
                padding: 15px 20px;
            }
            
            .faq-answer {
                padding: 0 20px 15px;
            }
        }

        /* Modal Z-Index Fix */
        .modal {
            z-index: 10000;
            margin: auto auto;
        }

        /* Main Tour Slider Styles */
        .tour-main-slider {
            padding: 0;
        }

        .tour-slider-main {
            overflow: hidden;
        }

        .slider-image-wrapper {
            position: relative;
            width: 100%;
            object-fit: cover;
            overflow: hidden;
        }

        .slider-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .slider-image:hover {
            transform: scale(1.05);
        }

        .tour-slider-main .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            color: #113D48;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .tour-slider-main .slider-arrow:hover {
            background: #1CA8CB;
            color: white;
            transform: translateY(-50%) scale(1.1);
        }

        .tour-slider-main .slider-prev {
            left: 20px;
        }

        .tour-slider-main .slider-next {
            right: 20px;
        }

        /* Slider responsive */
        @media (max-width: 768px) {
            .slider-image-wrapper {
                height: 300px;
            }

            .tour-slider-main .slider-arrow {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .tour-slider-main .slider-prev {
                left: 10px;
            }

            .tour-slider-main .slider-next {
                right: 10px;
            }
        }

        /* Query Booking Form Styles - Sidebar */
        .sidebar-form-wrapper {
            margin-top: 0;
        }

        .booking-form-sidebar .booking-form-wrapper {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 25px;
            border-radius: 16px;
            border: 1px solid rgba(28, 168, 203, 0.15);
        }

        .booking-form-sidebar h4 {
            margin-bottom: 8px !important;
        }

        .booking-form-sidebar .form-control,
        .booking-form-sidebar .form-select {
            padding: 12px 15px;
            font-size: 0.95rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            background: #fff;
            transition: all 0.3s ease;
        }

        .booking-form-sidebar .form-control:focus,
        .booking-form-sidebar .form-select:focus {
            border-color: #1CA8CB;
            box-shadow: 0 0 0 0.2rem rgba(28, 168, 203, 0.15);
            outline: none;
        }

        .booking-form-sidebar .th-btn {
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .booking-form-sidebar .th-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(28, 168, 203, 0.3);
        }

        @media (max-width: 992px) {
            .sidebar-form-wrapper {
                position: relative !important;
                top: 0 !important;
                margin-top: 30px;
            }
        }

        .booking-form .form-group {
            margin-bottom: 0;
        }

        .booking-form .form-control {
            width: 100%;
            padding: 14px 16px 14px 48px;
            font-size: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            background: #fff;
            transition: all 0.3s ease;
            position: relative;
            height: 22px;
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
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 1rem;
            pointer-events: none;
            z-index: 5;
        }

        .booking-form textarea.form-control {
            height: auto;
            min-height: 90px;
            padding-top: 14px;
        }

        .booking-form .textarea-icon {
            top: 16px;
            transform: none;
        }

        .booking-form .form-label {
            display: block;
            font-weight: 600;
            color: #113D48;
            font-size: 0.9rem;
        }

        .booking-form .form-select {
            width: 100%;
            font-size: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            background: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            height: 32px;
        }

        .booking-form .form-select:focus {
            border-color: #1CA8CB;
            box-shadow: 0 0 0 0.25rem rgba(28, 168, 203, 0.15);
            outline: none;
        }

        .booking-form .btn-lg {
            padding: 16px 48px;
            font-size: 1.1rem;
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

        @media (max-width: 992px) {
            .booking-form-wrapper {
                padding: 30px;
            }
        }

        @media (max-width: 768px) {
            .booking-form .form-control {
                padding: 12px 12px 12px 42px;
                font-size: 0.95rem;
                height: 48px;
            }

            .booking-form .form-select {
                padding: 12px 12px;
                height: 48px;
            }

            .booking-form .input-icon {
                left: 14px;
                font-size: 0.9rem;
            }

            .booking-form-wrapper {
                padding: 20px;
            }

            .booking-form .btn-lg {
                width: 100%;
                text-align: center;
                padding: 14px 24px;
            }
        }

        .booking-form {
            margin-top: 0 !important;
        }

        /* Input Icon Styling */
        .booking-form .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 0.9rem;
            pointer-events: none;
            z-index: 5;
        }

        .booking-form .textarea-icon {
            top: 16px;
            transform: none;
        }

        .booking-form-sidebar .form-control {
            padding-left: 38px;
        }

        .booking-form-sidebar .form-select {
            padding-left: 38px;
        }

        .booking-form-sidebar textarea.form-control {
            padding-top: 14px;
        }
        .tour-overview {
            font-size: large;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <?php include 'components/preloader.php'; ?>
    <?php include 'components/sidebar.php'; ?>

    <?php include 'components/header.php'; ?>


    <main>

        <!-- Tour Details Section -->
        <section class=" fade-in">
            <div class="container">

                <!-- Tour Gallery (Full Width) -->
                <div class="mb-5 pb-3 border-bottom">
                    <?php
$images = json_decode($tour['images'], true) ?: [];
$imageCount = count($images);
if ($imageCount == 0) {
    echo '<p>No images available.</p>';
}
elseif ($imageCount == 1) {
    echo '<div class="tour-gallery-single">';
    echo '<img src="../assets/img/' . htmlspecialchars($images[0]) . '" alt="' . htmlspecialchars($tour['title']) . '">';
    echo '</div>';
}
elseif ($imageCount == 2) {
    echo '<div class="tour-gallery-two">';
    foreach ($images as $image) {
        echo '<img src="../assets/img/' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($tour['title']) . '">';
    }
    echo '</div>';
}
else {
    echo '<div class="tour-gallery-main">';
    echo '<div class="gallery-img gallery-large" style="background-image: url(\'../assets/img/' . htmlspecialchars($images[0]) . '\');"></div>';
    for ($i = 1; $i < min(3, $imageCount); $i++) {
        echo '<div class="gallery-img gallery-small" style="background-image: url(\'../assets/img/' . htmlspecialchars($images[$i]) . '\');"></div>';
    }
    if ($imageCount > 3) {
        $remaining = $imageCount - 3;
        echo '<button class="image-pill" data-bs-toggle="modal" data-bs-target="#galleryModal"><i class="fas fa-images"></i> +' . $remaining . '</button>';
    }
    echo '</div>';
}
?>
                </div>

                <!-- Main Content with Sidebar -->
                <div class="row g-4">
                    <div class="col-lg-8">
                        <h1 class="breadcumb-title  mb-5 text-black">
                            <?php echo htmlspecialchars($tour['title']); ?>
                        </h1>

                        <!-- Tour Hero Section -->
                        <section class="tour-hero pb-5 mt-5">
                            <div class="postion-relative ">

                                <div class="hero-meta">
                                    <div class="hero-meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span>
                                            <?php echo htmlspecialchars($tour['duration']); ?>
                                        </span>
                                    </div>
                                    <div class="hero-meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Pickup & Drop:
                                            <?php echo htmlspecialchars($tour['location']); ?>
                                        </span>
                                    </div>
                                    <div class="hero-meta-item">
                                        <i class="fas fa-users"></i>
                                        <span>Private Tour</span>
                                    </div>
                                    <div class="hero-meta-item">
                                        <i class="fas fa-star"></i>
                                        <span>
                                            <?php echo number_format($tour['rating'], 1); ?> (
                                            <?php echo $tour['reviews'] ?? '0'; ?> reviews)
                                        </span>
                                    </div>
                                    <?php if (!empty($tour['pricing'])): ?>
                                    <div class="hero-meta-item">
                                        <i class="fas fa-tag"></i>
                                        <span style="font-weight: bold; font-size: 1.2em; color: #1CA8CB;">
                                            ₹<?php echo number_format($tour['pricing'], 2); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </section>

                        <!-- Overview -->
                        <div class="mb-5 pb-3 border-top pt-5 border-bottom">
                            <h2 class="sec-title ">Tour Overview</h2>
                            <div class="tour-overview"><?php echo $tour['description']; ?></div>

                        </div>

                        <!-- Highlights -->
                        <div class="mb-5 pb-3 border-bottom">
                            <h2 class="sec-title mb-5">Tour Highlights</h2>
                            <?php
$highlights = json_decode($tour['highlights'] ?? '[]', true) ?: [];
$icons = ['fas fa-sun', 'fas fa-utensils', 'fas fa-landmark', 'fas fa-user-shield', 'fas fa-camera', 'fas fa-hotel'];
foreach ($highlights as $index => $highlight) {
    $icon = $icons[$index % count($icons)];
    echo '<div class="highlight-row mb-3">
                                        <div class="highlight-icon">
                                            <i class="' . $icon . '"></i>
                                        </div>
                                        <div class="highlight-content">
                                            <h4 class="box-title">' . htmlspecialchars($highlight['title'] ?? $highlight) . '</h4>
                                        </div>
                                    </div>';
}
?>
                        </div>

                        <!-- Inclusions/Exclusions -->
                        <div class="mb-5 pb-3 border-bottom">
                            <h3 class="sec-title mb-5">Inclusion Or Exclusion</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>
                                        Inclusions
                                    </h5>
                                    <ul class="inclusion-list">
                                        <?php
$included = json_decode($tour['included'] ?? '[]', true) ?: [];
foreach ($included as $item) {
    echo '<li>' . htmlspecialchars($item) . '</li>';
}
?>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-times-circle text-danger me-2"></i>
                                        Exclusions
                                    </h5>
                                    <ul class="exclusion-list">
                                        <?php
$excluded = json_decode($tour['excluded'] ?? '[]', true) ?: [];
foreach ($excluded as $item) {
    echo '<li>' . htmlspecialchars($item) . '</li>';
}
?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Itinerary -->
                        <div class="mb-5 pb-3 border-bottom">
                            <h3 class="sec-title mb-5">Tour Itinerary</h3>

                            <?php
$itinerary = json_decode($tour['itinerary'] ?? '[]', true) ?: [];
foreach ($itinerary as $index => $day) {
    $stepNum = $index + 1;
    $title = is_array($day) && isset($day['title']) ? $day['title'] : 'Step ' . $stepNum;
    $description = is_array($day) && isset($day['description']) ? $day['description'] : (is_array($day) && isset($day['points']) ? implode(' ', $day['points']) : $day);
    echo '<div class="itinerary-step">
                                        <h4>' . htmlspecialchars($title) . '</h4>
                                        <p>' . htmlspecialchars($description) . '</p>
                                    </div>';
}
?>
                        </div>
                    </div>

                    <!-- Sidebar with Booking Form -->
                    <div class="col-lg-4">
                        <div class="sidebar-form-wrapper" style="position: sticky; top: 100px;">
                            <form action="/api/mail.php" method="POST" class="booking-form ajax-contact">

                                <!-- Form type identifier -->
                                <input type="hidden" name="form_type" value="booking">
                                <!-- Anti-Spam: Honeypot field (hidden) - leave empty -->
                                <input type="text" name="website_url" class="honeypot" style="position:absolute; left:-9999px;" tabindex="-1" autocomplete="off">
                                <!-- Anti-Spam: CSRF Token -->
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
                                
                                <div class="row g-2">
                                    <div class="col-12">
                                        <div class="form-group mb-2 position-relative">
                                            <input type="text" class="form-control" name="first_name" id="first_name"
                                                placeholder="First Name *" required>
                                            <i class="fa-light fa-user input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-2 position-relative">
                                            <input type="text" class="form-control" name="last_name" id="last_name"
                                                placeholder="Last Name *" required>
                                            <i class="fa-light fa-user input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-2 position-relative">
                                            <input type="email" class="form-control" name="email" id="email"
                                                placeholder="Your Email *" required>
                                            <i class="fa-light fa-envelope input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-2 position-relative">
                                            <input type="tel" class="form-control" name="phone" id="phone"
                                                placeholder="Phone Number *" required>
                                            <i class="fa-light fa-phone input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-2 position-relative">
                                            <input type="text" class="form-control" name="tour_name" id="tour_name"
                                                value="<?php echo htmlspecialchars($tour['title']); ?>" readonly>
                                            <i class="fa-light fa-map-location-dot input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-2 position-relative">
                                            <input type="date" class="form-control" name="travel_date" id="travel_date"
                                                required>
                                            <i class="fa-light fa-calendar input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group mb-2 position-relative">
                                            <select name="adults" id="adults" class="form-select" required>
                                                <option value="">Adults</option>
                                                <option value="1">1</option>
                                                <option value="2" selected>2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6+</option>
                                            </select>
                                            <i class="fa-light fa-users input-icon"
                                                style="right: 12px; left: auto;"></i>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group mb-2 position-relative">
                                            <select name="children" id="children" class="form-select">
                                                <option value="0">Children</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                            </select>
                                            <i class="fa-light fa-child input-icon"
                                                style="right: 12px; left: auto;"></i>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-3 position-relative">
                                            <textarea name="special_requests" id="special_requests" rows="2"
                                                class="form-control" placeholder="Special Requests..."></textarea>
                                            <i class="fa-light fa-comment-dots input-icon textarea-icon"></i>
                                        </div>
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
                                        <div class="form-group mb-2">
                                            <label for="captcha_<?php echo $tour['id']; ?>" style="font-size:0.85rem;">Security Check <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text" style="background:#f8f9fa; font-size:0.9rem;"><?php echo $num1; ?> + <?php echo $num2; ?> = </span>
                                                <input type="number" class="form-control" id="captcha_<?php echo $tour['id']; ?>" name="captcha" required="" placeholder="Result" style="max-width:100px !important; padding: 2px 5px;">
                                                <input type="hidden" name="use_captcha" value="1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="th-btn style3 w-100 py-2">Get A Quote</button>
                                    </div>
                                </div>
                                <p class="form-messages mb-0 mt-2 text-center"></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section - Full Width -->
        <?php
        $tourFaqs = json_decode($tour['faq'] ?? '[]', true) ?: [];
        if (!empty($tourFaqs)):
        ?>
        <section class="faq-section-full py-5 bg-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="title-area text-center mb-5">
                            <h2 class="sec-title">Frequently Asked Questions</h2>
                        </div>
                        
                        <?php foreach ($tourFaqs as $faq): ?>
                        <div class="faq-item">
                            <button class="faq-question"><?php echo htmlspecialchars($faq['question'] ?? ''); ?></button>
                            <div class="faq-answer">
                                <p><?php echo htmlspecialchars($faq['answer'] ?? ''); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

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
                    
                    fetch('/api/mail.php', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json, text/plain, */*',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;

                        if (data.success) {
                            if (formMessages) {
                                formMessages.className = 'form-messages mb-0 mt-2 text-center text-success';
                                formMessages.textContent = data.message || 'Booking submitted successfully!';
                            }
                            bookingForm.reset();
                            location.reload();
                        } else {
                            if (formMessages) {
                                formMessages.className = 'form-messages mb-0 mt-2 text-center text-danger';
                                formMessages.textContent = data.error || 'Error submitting booking. Please try again.';
                            }
                        }
                    })
                    .catch(error => {
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                        if (formMessages) {
                            formMessages.className = 'form-messages mb-0 mt-2 text-center text-danger';
                            formMessages.textContent = 'Connection error. Please try again.';
                        }
                    });
                });
            }
        });
        </script>

        <!-- Related Tours -->
        <section class="space-top space-extra-bottom bg-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="title-area text-center mb-4">
                            <span class="sub-title" style="color:#113d48;font-weight:600;letter-spacing:1px;">Explore
                                More</span>
                            <h2 class="sec-title" style="font-size:2.1rem;font-weight:700; border: none;">
                                Related Tours
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="row gy-4">
                    <?php
$relatedTours = getTours($tour['category_name'], 3, $tour['id']);
foreach ($relatedTours as $relatedTour): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="related-tour-card">
                                <a href="../tour/<?php echo $relatedTour['slug']; ?>">
                                    <div class="tour-img">
                                        <img src="../assets/img/<?php echo json_decode($relatedTour['images'], true)[0] ?? 'default.webp'; ?>"
                                            alt="<?php echo htmlspecialchars($relatedTour['title']); ?>">
                                    </div>

                                    <div class="tour-content">
                                        <h3 class="tour-title">
                                            <?php echo htmlspecialchars($relatedTour['title']); ?>
                                        </h3>
                                        <p class="tour-destination">
                                            <?php echo htmlspecialchars($relatedTour['location']); ?>
                                        </p>
                                        <div class="tour-meta">
                                            <div class="tour-rating">
                                                <i class="fas fa-star"></i>
                                                <span><?php echo number_format($relatedTour['rating'], 1); ?>
                                                    (<?php echo $relatedTour['reviews'] ?? '0'; ?>+)</span>
                                            </div>
                                        </div>
                                        <a href="../tour/<?php echo $relatedTour['slug']; ?>" class="th-btn style3">View
                                            Details</a>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php
endforeach; ?>
                </div>
            </div>
        </section>

    </main>

    <?php include 'components/footer.php'; ?>

    <?php include 'components/script.php'; ?>

    <!-- Gallery Modal -->
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="galleryModalLabel">Tour Gallery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="gallery-modal-grid">
                        <?php
$images = json_decode($tour['images'], true) ?: [];
foreach ($images as $image) {
    echo '<img src="../assets/img/' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($tour['title']) . '" class="gallery-modal-img">';
}
?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Initialize Main Tour Slider -->
    <script>
        jQuery(document).ready(function ($) {
            // Initialize the main tour slider
            if (typeof Swiper !== 'undefined') {
                const tourSliderMain = new Swiper('#tourSliderMain', {
                    slidesPerView: 3,
                    spaceBetween: 10,
                    loop: true,
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl: '.slider-next',
                        prevEl: '.slider-prev',
                    },
                    breakpoints: {
                        0: {
                            slidesPerView: 1,
                        },
                        576: {
                            slidesPerView: 2,
                        },
                        992: {
                            slidesPerView: 3,
                        },
                        1200: {
                            slidesPerView: 3,
                        }
                    }
                });
            }
        });
        
        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', function() {
                const faqItem = this.parentElement;
                const isActive = faqItem.classList.contains('active');
                
                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                if (!isActive) {
                    faqItem.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>