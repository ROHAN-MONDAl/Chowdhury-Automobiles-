$(document).ready(function () {

    // Navbar shadow on scroll
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
// 1. Toggle between Email and OTP steps
function toggleResetSteps() {
    const step1Div = document.getElementById('emailStep').parentElement; // Step 1 container
    const step2Div = document.getElementById('otpStep'); // Step 2 container

    const emailInput = document.getElementById('resetEmail'); // User-entered email
    const hiddenEmail = document.getElementById('hiddenEmail'); // Hidden input in OTP form

    if (step2Div.classList.contains('d-none')) {
        // SHOW OTP STEP
        if (emailInput.value.trim() === '') {
            alert("Please enter your email first.");
            emailInput.focus();
            return;
        }

        // Copy email to hidden input
        hiddenEmail.value = emailInput.value.trim();

        step1Div.style.display = 'none';
        step2Div.classList.remove('d-none');
    } else {
        // SHOW EMAIL STEP
        step2Div.classList.add('d-none');
        step1Div.style.display = 'block';
    }
}

// 2. Auto-focus the next OTP box when typing numbers
function moveToNext(elem, index) {
    if (elem.value.length >= 1) {
        const boxes = document.querySelectorAll('.otp-box');
        if (index < boxes.length - 1) {
            boxes[index + 1].focus();
        }
    }
}

// 3. Combine the 6 OTP boxes into one hidden input before submitting
function combineOtp() {
    const boxes = document.querySelectorAll('.otp-box');
    let otp = '';
    boxes.forEach(box => otp += box.value.trim()); // Trim spaces just in case
    document.getElementById('fullOtp').value = otp;
}

// 4. Optional: auto-submit when all boxes filled
document.querySelectorAll('.otp-box').forEach((box, idx) => {
    box.addEventListener('input', () => {
        moveToNext(box, idx);
        const boxes = document.querySelectorAll('.otp-box');
        if ([...boxes].every(b => b.value.length > 0)) {
            combineOtp();
            // Optionally, auto-submit:
            // document.getElementById('otpForm').submit();
        }
    });
});
