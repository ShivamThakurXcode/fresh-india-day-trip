<link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicons/favicon,webp">
<link rel="shortcut icon" type="image/png" href="assets/img/favicons/favicon,webp">

<link rel="manifest" href="assets/img/favicons/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="theme-color" content="#ffffff">
<link rel="preconnect" href="https://fonts.googleapis.com/">
<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
<link rel="preconnect" href="https://fonts.googleapis.com/">
<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&amp;family=Manrope:wght@200..800&amp;family=Montez&amp;display=swap"
    rel="stylesheet">
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/fontawesome.min.css">
<link rel="stylesheet" href="../assets/css/magnific-popup.min.css">
<link rel="stylesheet" href="../assets/css/swiper-bundle.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-nice-select@1.1.0/css/nice-select.css">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
    /* Color Switcher Enhancement */
    .color-switch-btns button {
        position: relative;
        transition: all 0.3s ease;
    }

    .color-switch-btns button.active {
        transform: scale(1.2);
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        border: 2px solid #fff;
    }

    .color-switch-btns button:hover {
        transform: scale(1.1);
    }

    /* Tour Slider Section Header Styling */
    .tour-area .row.align-items-center {
        margin-bottom: 30px;
    }

    .tour-area .title-area .sec-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--title-color);
        margin-bottom: 0;
    }

    /* Tour Location Styling */
    .tour-location {
        font-size: 14px;
        color: #666;
        margin-bottom: 5px;
    }

    @media (max-width: 767px) {
        .tour-area .row.align-items-center {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 15px;
        }

        .tour-area .row.align-items-center .col-auto:last-child {
            width: 100%;
        }

        .tour-area .row.align-items-center .col-auto:last-child .line-btn {
            width: 100%;
            text-align: center;
            display: inline-block;
        }

        .tour-area .title-area .sec-title {
            font-size: 24px;
        }
    }

    /* Custom Select Styles */
    .custom-select {
        width: 100%;
        height: 50px;
        padding: 10px 50px 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background: #fff;
        font-size: 14px;
        color: #333;
        cursor: pointer;
        appearance: none;
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 12px;
        z-index: 10;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
    .custom-select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0,123,255,0.5);
    }
    .custom-select option {
        padding: 10px;
        background: #fff;
        color: #333;
    }

    /* Sidebar Styles */
    .sidebar {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: sticky;
        top: 100px;
    }
    .sidebar-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #333;
    }
    .filter-group label {
        font-weight: 500;
        margin-bottom: 5px;
        display: block;
    }
    .filter-group .form-control,
    .filter-group .form-select {
        margin-bottom: 10px;
    }
</style>