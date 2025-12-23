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
    const totalSteps = 4;

    // --- UI UPDATE FUNCTION ---

    function updateWizard() {
        // Update Sidebar Circles
        $('.step-item').each(function () {
            let stepNum = parseInt($(this).data('step'));
            let $circle = $(this).find('.step-circle');
            let $label = $(this).find('.step-label');

            $(this).removeClass('bg-primary-subtle border-primary');
            $circle.removeClass('bg-primary bg-success text-white shadow').addClass('bg-light text-secondary');
            $circle.html('<span class="small fw-bold">' + stepNum + '</span>');
            $label.removeClass('text-primary text-success fw-bolder').addClass('text-secondary');

            if (stepNum === currentStep) {
                $(this).addClass('bg-primary-subtle border-primary');
                $circle.removeClass('bg-light text-secondary').addClass('bg-primary text-white shadow');
                $label.removeClass('text-secondary').addClass('text-primary fw-bolder');
            } else if (stepNum < currentStep) {
                $circle.removeClass('bg-light text-secondary').addClass('bg-success text-white');
                $circle.html('<i class="ph-bold ph-check"></i>');
                $label.removeClass('text-secondary').addClass('text-success fw-bold');
            }
        });

        // Show/Hide Steps
        $('.wizard-step').addClass('d-none');
        $('#step-' + currentStep).removeClass('d-none').hide().fadeIn(300);

        // Update Mobile Title
        $('#mobile-step-indicator').text('Step ' + currentStep);

        // Handle Button Visibility
        if (currentStep === 1) $('#prevBtn').hide();
        else $('#prevBtn').show();

        if (currentStep === totalSteps) {
            $('#btn-next').addClass('d-none');
            $('#btn-finish').removeClass('d-none');
        } else {
            $('#btn-next').removeClass('d-none');
            $('#btn-finish').addClass('d-none');
        }
    }


    // --- 4. AJAX SAVE FUNCTION ---
    function saveData(actionType, btnElement = null) {

        // --- 1. SETUP LOADING UI ---
        let originalBtnText = '';
        let $btn = null;

        if (btnElement) {
            $btn = $(btnElement);
            originalBtnText = $btn.html(); // Save original text (e.g., "Next")

            // Disable button and show spinner (Bootstrap class)
            $btn.prop('disabled', true);
            $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

        }

        // 2. Get Form Data
        let formData = new FormData($('#dealForm')[0]);

        // 3. Append Manual Fields
        formData.append('step', currentStep);
        formData.append('action', actionType);

        // 4. Perform AJAX with Timeout
        $.ajax({
            url: 'vehicle_form.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            timeout: 5000, // Stop after 5 seconds
            success: function (response) {

                if (response.status === 'success') {
                    $('input[name="vehicle_id"]').val(response.id);

                    if (actionType === 'save_next') {
                        showGlobalToast(response.message, 'success');
                        if (currentStep < totalSteps) {
                            currentStep++;
                            updateWizard();
                        }
                    } else if (actionType === 'save_only') {
                        showGlobalToast(response.message, 'success');
                    } else if (actionType === 'finish') {
                        showGlobalToast("Deal Completed! Redirecting...", 'success');

                        // Don't reset button if redirecting, keeps the "Processing..." state
                        return;

                        setTimeout(function () {
                            window.location.href = 'dashboard.php';
                        }, 1500);
                    }

                } else {
                    showGlobalToast(response.message, 'error');
                }
            },
            error: function (xhr, status, error) {
                if (status === 'timeout') {
                    console.error("Save timed out (> 5s)");
                    showGlobalToast("Server taking too long. Redirecting...", 'error');
                    setTimeout(function () {
                        window.location.href = 'dashboard.php';
                    }, 1500);
                } else {
                    console.error("AJAX Error:", xhr.responseText);
                    showGlobalToast("System Error: Check console for details.", 'error');
                }
            },
            // --- 5. RESET LOADING UI (Runs on Success or Error) ---
            complete: function () {
                // Only reset if we are NOT redirecting (Finish action usually redirects)
                if ($btn && actionType !== 'finish') {
                    $btn.prop('disabled', false);
                    $btn.html(originalBtnText); // Restore original text
                }
            }
        });
    }

    // --- EVENT HANDLERS ---

    // Save Draft
    $('#btn-save-draft').off('click').on('click', function (e) {
        e.preventDefault();
        // Pass $(this) so we know which button to spin
        saveData('save_only', $(this));
    });

    // Next
    $('#btn-next').off('click').on('click', function (e) {
        e.preventDefault();
        saveData('save_next', $(this));
    });

    // Finish
    $('#btn-finish').off('click').on('click', function (e) {
        e.preventDefault();
        if (confirm("Are you sure you want to finish?")) {
            saveData('finish', $(this));
        }
    });

    // Previous (Usually doesn't need AJAX save, but if it did, you would pass $(this) here too)
    $('#prevBtn').off('click').on('click', function (e) {
        e.preventDefault();
        if (currentStep > 1) {
            currentStep--;
            updateWizard();
        }
    });


    // GLOBAL MESSAGE FOR FORM
    function showGlobalToast(message, type = 'success') {
        const toastElement = document.getElementById('liveToast');
        const toastBody = document.getElementById('toastMessage');

        // 1. Set Message
        toastBody.innerText = message;

        // 2. Set Color based on Type
        toastElement.classList.remove('text-bg-success', 'text-bg-danger', 'text-bg-warning');

        if (type === 'success') {
            toastElement.classList.add('text-bg-success'); // Green
        } else if (type === 'error') {
            toastElement.classList.add('text-bg-danger'); // Red
        } else {
            toastElement.classList.add('text-bg-warning'); // Yellow/Orange
        }

        // 3. Show Toast using Bootstrap 5 API
        const toast = new bootstrap.Toast(toastElement, { delay: 4000 }); // Disappears after 4 seconds
        toast.show();
    }


    // --- 6. GLOBAL MODAL HANDLER ---
    window.openModal = function (id) {
        var modalElement = document.getElementById(id);
        if (!modalElement) return;

        var myModal = bootstrap.Modal.getOrCreateInstance(modalElement);

        if (id === 'dealModal') {
            // Reset Wizard for New Entry
            currentStep = 1;
            updateWizard();
            $('input[name="vehicle_id"]').val(''); // Clear ID
            $('#dealForm')[0].reset(); // Clear Form
        }
        myModal.show();
    };

});

// ==========================
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
    e.preventDefault();

    var leadId = $(this).data('id');

    if (!confirm("Are you sure you want to delete this lead?")) return;

    $.ajax({
        url: 'delete_lead.php',
        type: 'POST',
        dataType: 'json', // 🚨 force JSON
        data: { delete_lead_id: leadId },
        success: function (res) {
            console.log(res); // 🔍 DEBUG

            if (res.status === 'success') {
                window.location.href = 'dashboard.php';
            } else {
                alert(res.message || 'Delete failed');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText); // 🔥 THIS WILL SHOW ERROR
            alert('Server error. Please try again.');
        }
    });
});



function confirmDeleteVehicle() {
    return confirm("⚠️ Are you sure you want to delete this vehicle?\n\nThis action cannot be undone.");
}





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
// ==========================
// LEAD SEARCH FUNCTIONALITY
// ==========================
$(document).ready(function () {
    $("#leadSearchInput").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        var hasVisibleItems = false;

        // Loop through all rows with class 'lead-item'
        $("#leadsTable .lead-item").filter(function () {
            var isMatch = $(this).text().toLowerCase().indexOf(value) > -1;
            $(this).toggle(isMatch);
            if (isMatch) hasVisibleItems = true;
        });

        // Show "No Results" message if nothing matches
        if (!hasVisibleItems && value.length > 0) {
            $("#noResultsMsg").show();
        } else {
            $("#noResultsMsg").hide();
        }
    });
});