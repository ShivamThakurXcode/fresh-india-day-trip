<script src="../assets/js/vendor/jquery-3.6.0.min.js"></script>
<script src="../assets/js/swiper-bundle.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/jquery.magnific-popup.min.js"></script>
<script src="../assets/js/jquery.counterup.min.js"></script>
<script src="../assets/js/jquery-ui.min.js"></script>
<script src="../assets/js/imagesloaded.pkgd.min.js"></script>
<script src="../assets/js/isotope.pkgd.min.js"></script>
<script src="../assets/js/gsap.min.js"></script>
<script src="../assets/js/circle-progress.js"></script>
<script src="../assets/js/matter.min.js"></script>
<script src="../assets/js/matterjs-custom.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-nice-select@1.1.0/js/jquery.nice-select.min.js"></script>
<script src="../assets/js/main.js"></script>
<script>
    // Enhanced Color Switcher Fix
    jQuery(document).ready(function($) {
        // Initialize nice-select
        setTimeout(function() {
            $('.nice-select').niceSelect();
        }, 100);

        // Initialize color buttons with their colors
        $(".color-switch-btns button").each(function() {
            const $button = $(this);
            const color = $button.data("color");

            // Set the button's background color preview
            $button.css("--theme-color", color);
            $button.css("background-color", color);

            // Add click handler
            $button.on("click", function() {
                const selectedColor = $(this).data("color");

                // Update both theme-color and primary-color CSS variables
                $(":root").css("--theme-color", selectedColor);
                $(":root").css("--primary-color", selectedColor);

                // Store in localStorage for persistence
                localStorage.setItem("theme-color", selectedColor);

                // Add active class to clicked button
                $(".color-switch-btns button").removeClass("active");
                $(this).addClass("active");
            });
        });

        // Load saved color from localStorage on page load
        const savedColor = localStorage.getItem("theme-color");
        if (savedColor) {
            $(":root").css("--theme-color", savedColor);
            $(":root").css("--primary-color", savedColor);

            // Mark the corresponding button as active
            $(".color-switch-btns button").each(function() {
                if ($(this).data("color") === savedColor) {
                    $(this).addClass("active");
                }
            });
        }

        // Toggle color scheme panel
        $(document).on("click", ".switchIcon", function() {
            $(".color-scheme-wrap").toggleClass("active");
        });
    });
</script>