$(document).ready(function () {

    // --- 1. LOADER ---
    setTimeout(function () {
        $('#loader').fadeOut(500);
    }, 500);


    // --- 2. LOGIN FLOW ---
    $('#loginForm').on('submit', function (e) {
        e.preventDefault();
        window.location.href = 'dashboard.php';
    });

    $('#logoutBtn, #logoutIcon').on('click', function () {
        window.location.href = 'index.php';
    });


    // --- 3. WIZARD VARIABLES & LOGIC ---
    let currentStep = 1;
    // Calculate total steps based on how many .step-item divs (circles) exist
    // Default to 4 if for some reason the length is 0 (e.g. loaded dynamically)
    const totalSteps = $('.step-item').length || 4;

    // Function to update UI based on currentStep
    function updateWizard() {
        // 1. Update Navigation Circles
        $('.step-item').each(function () {
            let stepNum = parseInt($(this).data('step'));
            $(this).removeClass('active completed');

            if (stepNum === currentStep) {
                $(this).addClass('active');
            } else if (stepNum < currentStep) {
                $(this).addClass('completed');
            }
        });

        // 2. Show specific Step Content
        // First, hide ALL steps by adding d-none class
        $('.wizard-step').addClass('d-none');
        
        // Then, show only the CURRENT step using its ID (e.g., #step-1)
        // We use .hide().fadeIn() to force the fade animation
        $('#step-' + currentStep).removeClass('d-none').hide().fadeIn(300);

        // 3. Handle Buttons

        // --- BACK BUTTON LOGIC ---
        if (currentStep === 1) {
            $('#prevBtn').hide(); // Completely hide on Step 1
        } else {
            $('#prevBtn').show(); // Show on Step 2, 3, 4
        }

        // --- NEXT / SUBMIT BUTTON LOGIC ---
        if (currentStep === totalSteps) {
            $('#nextBtn').addClass('d-none');      // Hide Next button
            $('#submitBtn').removeClass('d-none'); // Show Submit button
        } else {
            $('#nextBtn').removeClass('d-none');   // Show Next button
            $('#submitBtn').addClass('d-none');    // Hide Submit button
        }
    }

    // Function to Reset Wizard (Used when Modal Opens)
    function resetWizard() {
        currentStep = 1;
        updateWizard();
        // Optional: Reset form inputs if needed
        // $('#wizardForm')[0].reset(); 
    }


    // --- 4. MODAL HANDLERS ---
    // Assigned to window so it can be called from HTML onclick attributes
    window.openModal = function (id) {
        var modalElement = document.getElementById(id);
        
        if (!modalElement) return; // Safety check

        // Use getOrCreateInstance to prevent duplicate instance errors
        var myModal = bootstrap.Modal.getOrCreateInstance(modalElement);

        // If opening the deal modal, reset the wizard to step 1
        if (id === 'dealModal') {
            resetWizard();
        }

        myModal.show();
    };


    // --- 5. WIZARD EVENTS ---

    // Next Button Click
    $('#nextBtn').click(function () {
        if (currentStep < totalSteps) {
            // Optional: Validation can go here
            // if( !validateStep(currentStep) ) return;

            currentStep++;
            updateWizard();
        }
    });

    // Back Button Click
    $('#prevBtn').click(function () {
        if (currentStep > 1) {
            currentStep--;
            updateWizard();
        }
    });

    // Initialize the wizard view on page load
    updateWizard();

});





// Form Submission
$('#saveStepBtn').click(function () {
    let btn = $(this);
    let originalContent = btn.html();
    btn.html('<i class="ph-bold ph-check me-1"></i> Saved');
    btn.addClass('btn-success text-white').removeClass('btn-light text-primary');

    setTimeout(function () {
        btn.html(originalContent);
        btn.removeClass('btn-success text-white').addClass('btn-light text-primary');
    }, 1500);
});

$('.step-item').click(function () {
    let step = $(this).data('step');
    if (step <= totalSteps) jumpStep(step); // Prevent jumping to non-existent step 5
});

// --- LOGIC: STEP 1 (Vehicle) ---
$('#soldToggle').change(function () {
    if ($(this).is(':checked')) $('#step1-card').addClass('is-sold');
    else $('#step1-card').removeClass('is-sold');
});

$('input[name="p_chal"]').change(function () {
    if ($('#pc_yes').is(':checked')) $('#challan-inputs').removeClass('d-none');
    else $('#challan-inputs').addClass('d-none');
});

// --- LOGIC: STEP 2 (Seller) ---
$('input[name="s_pay"]').change(function () {
    if ($('#sp_fin').is(':checked')) $('#seller-fin-options').removeClass('d-none');
    else $('#seller-fin-options').addClass('d-none');
});

$('input[name="noc_stat"]').change(function () {
    if ($('#noc_rec').is(':checked')) {
        $('#noc-photos').removeClass('d-none');
        $('#noc-alert').addClass('d-none');
    } else {
        $('#noc-photos').addClass('d-none');
        $('#noc-alert').removeClass('d-none');
    }
});

$('input[name="s_chal"]').change(function () {
    if ($('#sc_yes').is(':checked')) {
        $('#s_chal_inp').removeClass('d-none');
        $('#s_chal_ok').addClass('d-none');
    } else {
        $('#s_chal_inp').addClass('d-none');
        $('#s_chal_ok').removeClass('d-none');
    }
});

$('#s_total, #s_paid').on('input', function () {
    let total = parseFloat($('#s_total').val()) || 0;
    let paid = parseFloat($('#s_paid').val()) || 0;
    let due = total - paid;
    $('#s_due').val(due);
    if (due > 0) $('#s_due_reason').removeClass('d-none');
    else $('#s_due_reason').addClass('d-none');
});

// --- LOGIC: STEP 3 (Purchaser) ---
$('#p_total, #p_paid').on('input', function () {
    let total = parseFloat($('#p_total').val()) || 0;
    let paid = parseFloat($('#p_paid').val()) || 0;
    let due = total - paid;
    $('#p_due').val(due);
});

$('input[name="p_mode"]').change(function () {
    if ($('#pm_fin').is(':checked')) $('#hpa_sec').removeClass('d-none');
    else $('#hpa_sec').addClass('d-none');
});

// --- IMAGE UPLOAD PREVIEW ---
$(document).on('click', '.photo-upload-box', function (e) {
    if (e.target.tagName !== 'INPUT') {
        $(this).find('input[type="file"]').trigger('click');
    }
});

$(document).on('change', '.photo-upload-box input[type="file"]', function () {
    if (this.files && this.files[0]) {
        let reader = new FileReader();
        let $box = $(this).closest('.photo-upload-box');

        reader.onload = function (e) {
            $box.find('img').attr('src', e.target.result);
            $box.addClass('has-image');
        };

        reader.readAsDataURL(this.files[0]);
    }
});

// Delete Lead Action
$('.delete-lead').on('click', function (e) {
    e.preventDefault(); // Prevent default button behavior
    var leadId = $(this).data('id');

    if (confirm("Are you sure you want to delete this lead?")) {
        $.post('delete_lead.php', { delete_lead_id: leadId }, function (response) {
            // Remove the lead card from DOM
            $('#lead-' + leadId).remove();
        }).fail(function () {
            alert('Failed to delete lead. Please try again.');
        });
    }
});



// ==========================
// ISSUE DATE → EXPIRY DATE
// ==========================
$("#issueDate").on("change", function () {

    // Get selected issue date
    let issueDate = new Date($(this).val());

    // Boundary check: ensure selected date is valid
    if (!isNaN(issueDate)) {

        // Add 1 year to issue date
        let expiryDate = new Date(issueDate);
        expiryDate.setFullYear(issueDate.getFullYear() + 1);

        // Format date into yyyy-mm-dd (HTML date input format)
        let year = expiryDate.getFullYear();
        let month = String(expiryDate.getMonth() + 1).padStart(2, '0');
        let day = String(expiryDate.getDate()).padStart(2, '0');

        let formattedDate = `${year}-${month}-${day}`;

        // Set calculated expiry date in input field
        $("#expiryDate").val(formattedDate);

        // Update validity text
        $("#expiryText").text(" (1 Year)");
    }
});


// ==========================
// START DATE → END DATE
// ==========================
$("#startDate").on("change", function () {

    // Get selected start date
    let start = new Date($(this).val());

    // Boundary check: ensure selected date is valid
    if (!isNaN(start)) {

        // Add 1 year to start date
        let end = new Date(start);
        end.setFullYear(start.getFullYear() + 1);

        // Format date into yyyy-mm-dd
        let year = end.getFullYear();
        let month = String(end.getMonth() + 1).padStart(2, '0');
        let day = String(end.getDate()).padStart(2, '0');

        let formatted = `${year}-${month}-${day}`;

        // Set calculated end date in input
        $("#endDate").val(formatted);

        // Update duration text
        $("#durationText").text(" (1 Year)");
    }
});

// Fade out the success toast after 3 seconds
$(".global-success-msg, .global-error-msg, .global-info-msg, .global-warning-msg")
    .delay(3000) // show for 3 seconds
    .fadeOut(500);
