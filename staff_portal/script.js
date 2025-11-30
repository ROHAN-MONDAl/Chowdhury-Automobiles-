 $(document).ready(function () {
            // Navbar blur on scroll
            $(window).on('scroll', function () {
                if ($(this).scrollTop() > 50) {
                    $('.navbar').addClass('shadow-sm');
                } else {
                    $('.navbar').removeClass('shadow-sm');
                }
            });

            // Fade out loader once page is loaded
            $(window).on('load', function () {
                $('#loading-screen').fadeOut(500);
            });
        });