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

    // Step 1 → Step 2 (delegated event)
    $(document).on("click", "#sendResetBtn", function () {
        $("#modalTitle").text("Verify OTP");
        $("#emailStep").addClass("d-none");
        $("#otpStep").removeClass("d-none");
        $(".otp-input").first().focus();
    });

    // Auto move to next OTP input (delegated event)
    $(document).on("input", ".otp-input", function () {
        if (this.value.length === 1) {
            $(this).next(".otp-input").focus();
        }
    });

});





