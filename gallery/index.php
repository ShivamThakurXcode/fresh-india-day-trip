<?php require_once '../config.php'; ?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="author" content="India Day Trip">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <?php renderSEOHead('gallery'); ?>
    <meta property="twitter:image" content="https://indiadaytrip.com/assets/img/gallery/hg1.webp">

    <?php include '../components/links.php'; ?>
</head>

<body>
    <?php include '../components/preloader.php'; ?>
    <?php include '../components/sidebar.php'; ?>
    <?php include '../components/header.php'; ?>

    <div class="breadcumb-wrapper" data-bg-src="../assets/img/bg/breadcumb-bg.webp">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Our Gallery</h1>
                <ul class="breadcumb-menu">
                    <li><a href="../index.php">Home</a></li>
                    <li>Our Gallery</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="overflow-hidden space" id="gallery-sec">
        <div class="container-fuild">
            <div class="title-area mb-30 text-center">
                <span class="sub-title">Discover India</span>
                <h2 class="sec-title">Capturing Moments of a Lifetime</h2>
            </div>
            <div class="row gy-4 gallery-row4" id="gallery-container">
                <!-- Gallery images will be loaded here dynamically -->
            </div>
            <div id="loading-spinner" class="text-center my-4" style="display: none;">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div id="error-message" class="text-center text-danger my-4" style="display: none;">
                Failed to load gallery images. Please try again later.
            </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>


    <?php include '../components/script.php'; ?>

    <script>
        // Gallery loading functionality
        document.addEventListener('DOMContentLoaded', function () {
            loadGalleryImages();
        });

        function loadGalleryImages() {
            const container = document.getElementById('gallery-container');
            const loadingSpinner = document.getElementById('loading-spinner');
            const errorMessage = document.getElementById('error-message');

            // Show loading spinner
            loadingSpinner.style.display = 'block';
            errorMessage.style.display = 'none';
            container.innerHTML = '';

            fetch('../api/gallery.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    loadingSpinner.style.display = 'none';

                    if (data.success && data.data.length > 0) {
                        data.data.forEach(image => {
                            const galleryItem = createGalleryItem(image);
                            container.appendChild(galleryItem);
                        });
                    } else {
                        container.innerHTML = '<div class="col-12 text-center"><p>No gallery images available at the moment.</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading gallery:', error);
                    loadingSpinner.style.display = 'none';
                    errorMessage.style.display = 'block';
                    container.innerHTML = '<div class="col-12 text-center"><p>Unable to load gallery. Please check your internet connection and try again.</p></div>';
                });
        }

        function createGalleryItem(image) {
            const colDiv = document.createElement('div');
            colDiv.className = 'col-auto';

            const galleryBox = document.createElement('div');
            galleryBox.className = 'gallery-box style5';

            const galleryImg = document.createElement('div');
            galleryImg.className = 'gallery-img global-img';

            const img = document.createElement('img');
            img.src = image.url;
            img.alt = image.alt_text || image.title || 'Gallery Image';
            img.loading = 'lazy'; // Lazy loading for performance

            const link = document.createElement('a');
            link.href = image.url;
            link.className = 'icon-btn popup-image';
            link.setAttribute('data-gallery', 'gallery');
            link.innerHTML = '<i class="fal fa-magnifying-glass-plus"></i>';

            galleryImg.appendChild(img);
            galleryImg.appendChild(link);
            galleryBox.appendChild(galleryImg);
            colDiv.appendChild(galleryBox);

            return colDiv;
        }
    </script>
</body>

</html>