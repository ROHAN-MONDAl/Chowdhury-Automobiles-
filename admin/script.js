$(document).ready(function () {

    let currentStep = 1;
    let updateWizardUI = function () { };


    // ==========================
    // 1. UI & NOTIFICATIONS
    // ==========================

    // Hide Loader
    setTimeout(function () {
        $('#loader').fadeOut(500);
    }, 500);

    // Auto-hide alerts after 3 seconds
    $(".global-success-msg, .global-error-msg, .global-info-msg, .global-warning-msg")
        .delay(3000)
        .fadeOut(500);

    // ==========================
    // Leads search toggle
    // =========================
    const searchInput = document.getElementById("leadSearchInput");
    const table = document.getElementById("leadsTable");
    const rows = table.querySelectorAll("tbody tr.lead-item");
    const noResultsMsg = document.getElementById("noResultsMsg");

    searchInput.addEventListener("keyup", function () {
        const searchValue = this.value.toLowerCase().trim();
        let visibleCount = 0;

        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();

            if (rowText.includes(searchValue)) {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        });

        // Show/Hide "No results" message
        noResultsMsg.style.display = visibleCount === 0 ? "block" : "none";
    });

    // ==========================
    // 2. AUTHENTICATION
    // ==========================

    $('#loginForm').on('submit', function (e) {
        e.preventDefault();
        window.location.href = 'dashboard.php';
    });

    $('#logoutBtn, #logoutIcon').on('click', function (e) {
        e.preventDefault();
        window.location.href = 'index.php';
    });

    // ==========================
    // 3. DATE CALCULATIONS (1 Year Auto-Fill)
    // ==========================

    // Helper function to handle +1 year logic
    function setOneYearExpiry(sourceInput, targetInput, textLabel) {
        $(sourceInput).on("change", function () {
            var date = new Date($(this).val());

            if (!isNaN(date.getTime())) {
                date.setFullYear(date.getFullYear() + 1);

                // Format to YYYY-MM-DD
                var formattedDate = date.toISOString().split('T')[0];

                $(targetInput).val(formattedDate);
                $(textLabel).text(" (1 Year)");
            }
        });
    }

    // Apply logic to both sets of dates
    setOneYearExpiry("#issueDate", "#expiryDate", "#expiryText");
    setOneYearExpiry("#startDate", "#endDate", "#durationText");

    // ==========================
    // PAYMENT CALCULATION LOGIC
    // ==========================
    $('#s_total, #s_paid').on('input', function () {
        // 1. Get values (Default to 0 if empty)
        let total = parseFloat($('#s_total').val()) || 0;
        let paid = parseFloat($('#s_paid').val()) || 0;

        // 2. Calculate Due
        let due = total - paid;

        // --- FIX FOR -0.00 ---
        // If due is essentially 0 (or -0), force it to positive 0
        if (Math.abs(due) < 0.001) {
            due = 0;
        }

        // 3. Update Due Field
        $('#s_due').val(due.toFixed(2));

        // 4. Show/Hide Reason Field Logic
        // If Due is strictly greater than 0.01 (allowing for tiny float variance), show reason
        if (due > 0.01) {
            $('#s_due_reason').removeClass('d-none');
        } else {
            // Hide it and clear the text if paid in full (or overpaid)
            $('#s_due_reason').addClass('d-none').val('');
        }
    });

    // ==========================
    // PAYMENT CALCULATION LOGIC
    // ==========================
    $('#s_total, #s_paid').on('input', function () {
        // 1. Get values (Default to 0 if empty)
        let total = parseFloat($('#s_total').val()) || 0;
        let paid = parseFloat($('#s_paid').val()) || 0;

        // 2. Calculate Due
        let due = total - paid;

        // 3. VALIDATION: Prevent Negative Due & -0
        if (due < 0) {
            due = 0;
        }

        // 4. Update Due Field
        $('#s_due').val(due.toFixed(2));

        // 5. Show/Hide Reason Field Logic
        // Only show if due is strictly positive (e.g. 0.01 or more)
        if (due > 0) {
            $('#s_due_reason').removeClass('d-none');
        } else {
            $('#s_due_reason').addClass('d-none').val('');
        }
    });

    // ==========================
    // PURCHASER PRICE CALCULATION
    // ==========================
    $('#p_total, #p_paid').on('input', function () {
        // 1. Get values
        let total = parseFloat($('#p_total').val()) || 0;
        let paid = parseFloat($('#p_paid').val()) || 0;

        // 2. Calculate Due
        let due = total - paid;

        // 3. VALIDATION: Prevent Negative Due & -0
        if (due < 0) {
            due = 0;
        }

        // 4. Update Due Field
        $('#p_due').val(due.toFixed(2));
    });
    // ==========================
    // 5. DELETE LEAD (AJAX)
    // ==========================

    // Using 'document' selector to handle dynamically loaded elements
    $(document).on('click', '.delete-lead', function (e) {
        e.preventDefault();
        var leadId = $(this).data('id');

        if (!confirm("Are you sure you want to delete this lead?")) return;

        $.ajax({
            url: 'delete_lead.php',
            type: 'POST',
            dataType: 'json',
            data: { delete_lead_id: leadId },
            success: function (res) {
                if (res.status === 'success') {
                    window.location.href = 'dashboard.php';
                } else {
                    alert(res.message || 'Delete failed');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Server error. Please try again.');
            }
        });
    });


    // ==========================
    // 6. GLOBAL FUNCTIONS
    // ==========================

    // Global Modal Handler
    window.openModal = function (id) {
        var modalElement = document.getElementById(id);
        if (!modalElement) return;

        var myModal = bootstrap.Modal.getOrCreateInstance(modalElement);

        // Special reset logic for Deal Modal
        if (id === 'dealModal') {
            $('#dealForm')[0].reset();           // Clear inputs
            $('input[name="vehicle_id"]').val(''); // Clear hidden ID

            // Reset Wizard steps if variable exists
            if (typeof currentStep !== 'undefined') {
                currentStep = 1;
                if (typeof updateWizardUI === 'function') updateWizardUI();

            }
        }
        myModal.show();
    };




    // ==========================
    // 7. WIZARD & UPLOAD LOGIC
    // ==========================

    // Configuration
    const form = document.getElementById('dealForm');

    // Only run if the form exists on this page
    if (form) {
        const actionInput = document.getElementById('formAction');
        const progressContainer = document.getElementById('uploadProgressContainer');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressPercent');
        const overlayTitle = document.getElementById('overlayTitle');
        const overlaySubtitle = document.getElementById('overlaySubtitle');
        const uploadLabel = document.getElementById('uploadLabel');

        const allActionButtons = [
            document.getElementById('btn-save-draft'),
            document.getElementById('btn-next'),
            document.getElementById('btn-finish')
        ];

        // Get current step
        currentStep = parseInt($('input[name="step"]').val()) || 1;




        // Wizard UI Logic
        // Wizard UI Logic
        updateWizardUI = function () {
            // Handle Sidebar Indicators
            $('.step-item').removeClass('step-active-blink');
            $('.step-item .step-circle').removeClass('bg-primary text-white').addClass('text-light');

            const $activeItem = $('.step-item[data-step="' + currentStep + '"]');
            $activeItem.addClass('step-active-blink');
            $activeItem.find('.step-circle').removeClass('text-light').addClass('bg-primary text-white');

            // Mobile Text
            $('#mobile-step-indicator').text('Step ' + currentStep);

            // ⭐ SHOW/HIDE WIZARD STEPS (THIS WAS MISSING!)
            $('.wizard-step').addClass('d-none');
            $('#step-' + currentStep).removeClass('d-none');

            // ⭐ SHOW NEXT OR FINISH BUTTON (THIS WAS MISSING!)
            if (currentStep === 4) {
                $('#btn-next').addClass('d-none');
                $('#btn-finish').removeClass('d-none').addClass('d-flex');
            } else {
                $('#btn-next').removeClass('d-none').addClass('d-flex');
                $('#btn-finish').addClass('d-none');
            }

            // Buttons Visibility
            if (currentStep === 1) {
                $('#prevBtn').hide();
            } else {
                $('#prevBtn').css('display', 'flex');
            }
        }
        // Initialize Wizard UI
        updateWizardUI();


        // Previous Button Logic
        // ==========================
        // FIXED PREVIOUS BUTTON LOGIC
        // ==========================
        // Use a flag to prevent double-clicks
        // Add this variable at the very top of your $(document).ready block
        let isNavigating = false;

        window.prevStep = function (e) {
            if (e) e.preventDefault();

            // 1. Prevent double-triggering if the user clicks too fast or if events overlap
            if (isNavigating) return false;

            // 2. Block if buttons are disabled (during upload)
            const btn = document.getElementById('prevBtn');
            if (btn && btn.disabled) return false;

            if (currentStep > 1) {
                isNavigating = true; // Set lock

                currentStep--; // Move back exactly one step

                // 3. Update Hidden Field
                $('input[name="step"]').val(currentStep);

                // 4. Update UI
                if (typeof updateWizardUI === 'function') {
                    updateWizardUI();
                }

                // 5. Sync URL
                const vehicleId = $('input[name="vehicle_id"]').val() || '';
                const newurl = window.location.pathname + `?step=${currentStep}&id=${vehicleId}`;
                window.history.pushState({ path: newurl }, '', newurl);

                // 6. Release lock after animation/transition finishes
                setTimeout(() => {
                    isNavigating = false;
                }, 300);
            }
        };


        // Image Preview Logic
        $('.photo-upload-box').on('click', function () {
            $(this).find('input[type="file"]').trigger('click');
        });

        $('.photo-upload-box input[type="file"]').on('click', function (e) {
            e.stopPropagation();
        });

        $('.photo-upload-box input[type="file"]').on('change', function () {
            const file = this.files[0];
            const $box = $(this).closest('.photo-upload-box');
            const $img = $box.find('img');
            const $input = $(this);

            if (file && file.type.match(/image.*/)) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const tempImg = new Image();
                    tempImg.src = e.target.result;
                    tempImg.onload = () => {
                        const canvas = document.createElement('canvas');
                        let width = tempImg.width, height = tempImg.height;
                        const MAX = 1000; // Resize to max 1000px width/height
                        if (width > height) { if (width > MAX) { height *= MAX / width; width = MAX; } }
                        else { if (height > MAX) { width *= MAX / height; height = MAX; } }
                        canvas.width = width; canvas.height = height;
                        canvas.getContext('2d').drawImage(tempImg, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            const compressedFile = new File([blob], file.name, { type: 'image/jpeg' });
                            $input.data('compressed', compressedFile); // Store for XHR
                            $img.attr('src', URL.createObjectURL(blob)).fadeIn(300);
                            $box.addClass('has-image').css('border-style', 'solid').find('i').hide();
                        }, 'image/jpeg', 0.7); // 70% Quality
                    };
                };
                reader.readAsDataURL(file);
            }
        });

        // -------------------------
        // A. VALIDATION LOGIC
        // -------------------------
        function validateStep1() {
            function check(selector, errorMsg) {
                const el = $(selector);
                if (el.attr('type') === 'radio') {
                    const name = el.attr('name');
                    if ($(`input[name="${name}"]:checked`).length === 0) {
                        showToast(errorMsg, 'danger');
                        return false;
                    }
                    return true;
                }
                if (!el.val() || el.val().trim() === '') {
                    showToast(errorMsg, 'danger');
                    el.addClass('is-invalid').focus();
                    el.off('input change').on('input change', function () { $(this).removeClass('is-invalid'); });
                    return false;
                }
                el.removeClass('is-invalid');
                return true;
            }

            // Core Fields
            if (!check('select[name="vehicle_type"]', "Mandatory: Select Vehicle Type")) return false;
            if (!check('input[name="name"]', "Mandatory: Bike Name")) return false;
            if (!check('input[name="vehicle_number"]', "Mandatory: Vehicle Number")) return false;
            if (!check('input[name="chassis_number"]', "Mandatory: Chassis Number")) return false;
            if (!check('input[name="engine_number"]', "Mandatory: Engine Number")) return false;

            // Photo 1 Mandatory Check
            const vid = $('input[name="vehicle_id"]').val();
            const photoInput = $('input[name="photo1"]');
            const photoBox = photoInput.closest('.photo-upload-box');

            if (!vid && (!photoInput[0].files || photoInput[0].files.length === 0)) {
                showToast("Mandatory: Upload Vehicle Photo (Photo 1)", 'danger');
                photoBox.addClass('border-danger');

                photoInput.one('change', function () {
                    if (this.files.length > 0) photoBox.removeClass('border-danger');
                });
                return false;
            }

            // Payment Logic
            const payType = $('input[name="payment_type"]:checked').val();
            if (!payType) { showToast("Select Payment Type", "danger"); return false; }
            if (payType === 'Cash') {
                if (!check('input[name="cash_price"]', "Mandatory: Enter Cash Price")) return false;
            } else {
                if ($('input[name="online_method"]:checked').length === 0) { showToast("Select Online Method", 'danger'); return false; }
                if (!check('input[name="online_transaction_id"]', "Enter Transaction ID")) return false;
                if (!check('input[name="online_price"]', "Enter Online Price")) return false;
            }
            return true;
        }

        // Minimum requirements to allow a Draft save
        function validateDraft() {
            // 1. Get Values
            const vehicleNumber = $('input[name="vehicle_number"]').val();
            const bikeName = $('input[name="name"]').val();
            const vehicleType = $('select[name="vehicle_type"]').val();

            // Photo 1 Check Logic
            const vid = $('input[name="vehicle_id"]').val();
            const photoInput = $('input[name="photo1"]');
            const photoBox = photoInput.closest('.photo-upload-box');

            // 2. Clear previous errors
            $('input[name="vehicle_number"], input[name="name"], select[name="vehicle_type"]').removeClass('is-invalid');
            photoBox.removeClass('border-danger');

            // 3. Validate Vehicle Number
            if (!vehicleNumber || vehicleNumber.trim() === "") {
                showToast("Draft Error: Vehicle Number is required.", "warning");
                $('input[name="vehicle_number"]').addClass('is-invalid').focus();
                return false;
            }

            // 4. Validate Bike Name
            if (!bikeName || bikeName.trim() === "") {
                showToast("Draft Error: Enter Bike Name.", "warning");
                $('input[name="name"]').addClass('is-invalid').focus();
                return false;
            }

            // 5. Validate Vehicle Type
            if (!vehicleType) {
                showToast("Draft Error: Select Vehicle Type.", "warning");
                $('select[name="vehicle_type"]').addClass('is-invalid').focus();
                return false;
            }

            // 6. Validate Photo 1 (Mandatory for Drafts too now)
            // If it's a NEW vehicle (no vid) AND no file selected
            if (!vid && (!photoInput[0].files || photoInput[0].files.length === 0)) {
                showToast("Draft Error: Vehicle Photo 1 is Mandatory.", "warning");
                photoBox.addClass('border-danger');

                // Scroll to photo if needed or focus nearby
                $('html, body').animate({
                    scrollTop: photoBox.offset().top - 100
                }, 500);

                // Auto-remove border
                photoInput.one('change', function () {
                    if (this.files.length > 0) photoBox.removeClass('border-danger');
                });
                return false;
            }

            return true;
        }
        // Toast Notification Function A-> B
        window.showToast = function (message, type = 'danger') {
            const toastEl = document.getElementById('validationToast');
            const msgEl = document.getElementById('toastMessage');

            // Set Message
            msgEl.innerText = message;

            // Set Color (Danger for errors, Success for drafts)
            toastEl.classList.remove('bg-danger', 'bg-success', 'bg-warning');
            toastEl.classList.add('bg-' + type);

            // Initialize and Show
            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
        };


        // -------------------------
        // B. BUTTON SETUP (Client-Side Nav -> Final Save)
        // -------------------------
        setupButton('btn-save-draft', 'save_draft', false);
        setupButton('btn-next', 'save_next', true);
        setupButton('btn-finish', 'finish', true);

        function setupButton(btnId, actionName, validate) {
            const btn = document.getElementById(btnId);
            if (!btn) return;

            btn.addEventListener('click', function (e) {
                e.preventDefault();

                // ============================================================
                // 1. NEXT BUTTON: (Client-Side Only)
                //    Validates Step 1, then simply moves to the next view.
                //    Does NOT save to DB yet.
                // ============================================================
                // Locate this section inside your setupButton function
                if (actionName === 'save_next') {

                    // ============================================================
                    // NEW LOGIC: Enforce "Save Draft" on Step 1
                    // ============================================================
                    if (currentStep === 1) {

                        // 1. Check if the Vehicle ID exists (Populated by a successful Draft Save)
                        const existingId = $('input[name="vehicle_id"]').val();

                        // 2. If no ID, stop navigation and alert the user
                        if (!existingId) {
                            showToast("⚠️ Action Required: Please click 'Save Draft' to create the record before proceeding.", "warning");

                            // Optional: Add a temporary visual cue to the Save Draft button
                            $('#btn-save-draft').addClass('btn-warning').removeClass('btn-secondary');
                            setTimeout(() => {
                                $('#btn-save-draft').removeClass('btn-warning').addClass('btn-secondary');
                            }, 2000);

                            return; // STOP EXECUTION HERE
                        }

                        // 3. Run Standard Validation
                        if (!validateStep1()) return;
                    }
                    // ============================================================

                    // B. Simple Navigation Logic (Existing Code)
                    if (currentStep < 4) {
                        currentStep++;
                        $('input[name="step"]').val(currentStep);
                        updateWizardUI();

                        const vid = $('input[name="vehicle_id"]').val();
                        const nextUrl = window.location.pathname + `?step=${currentStep}&id=${vid}`;
                        window.history.pushState({ path: nextUrl }, '', nextUrl);
                        return;
                    }
                }

                // ============================================================
                // 2. DRAFT BUTTON: (Optional Save)
                //    User manually asks to save progress.
                // ============================================================
                if (actionName === 'save_draft') {
                    if (!validateDraft()) return;
                    actionInput.value = actionName;
                    uploadData(btn, actionName);
                    return;
                }

                // ============================================================
                // 3. FINISH BUTTON: (The Big Save)
                //    This sends Step 1 + 2 + 3 + 4 data all at once.
                // ============================================================
                if (actionName === 'finish') {
                    // Check standard HTML5 validity
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    // Trigger the upload of the entire form
                    actionInput.value = actionName;
                    uploadData(btn, actionName);
                }
            });
        }
        // Helper to disable/enable all buttons
        function toggleButtons(disabledState) {
            // List all buttons that need to be locked during processing
            const allButtons = [
                document.getElementById('btn-save-draft'),
                document.getElementById('btn-next'),
                document.getElementById('btn-finish'),
                document.getElementById('prevBtn') // Added Back Button here
            ];

            allButtons.forEach(btn => {
                if (btn) {
                    btn.disabled = disabledState;
                    // Optional: Add visual feedback for being disabled
                    if (disabledState) {
                        btn.style.opacity = "0.6";
                        btn.style.cursor = "not-allowed";
                    } else {
                        btn.style.opacity = "1";
                        btn.style.cursor = "pointer";
                    }
                }
            });
        }

        // -------------------------
        // C. UPLOAD FUNCTION (ROBUST)
        // -------------------------
        function uploadData(clickedBtn, actionName) {
            // ------------------------------------------------
            // 1. PRE-VALIDATION: FILE SIZE CHECK (Max 5MB)
            // ------------------------------------------------
            let fileError = false;

            $('input[type="file"]').each(function () {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const fileSizeMB = file.size / 1024 / 1024;
                    const $box = $(this).closest('.photo-upload-box');

                    // Reset previous errors
                    $box.css('border', '');
                    $box.find('.file-error-tag').remove();

                    if (fileSizeMB > 5) {
                        fileError = true;
                        $box.css('border', '2px solid red');
                        $box.append('<small class="file-error-tag text-danger fw-bold d-block">File too large (>5MB)</small>');

                        // Auto-clear error when user picks a new file
                        $(this).one('change', function () {
                            $box.css('border', '');
                            $box.find('.file-error-tag').remove();
                        });
                    }
                }
            });

            if (fileError) {
                showToast("Upload Failed: One or more files are larger than 5MB.", "danger");
                return; // STOP EXECUTION HERE
            }

            // ------------------------------------------------
            // 2. PROCEED WITH UPLOAD
            // ------------------------------------------------
            const formData = new FormData(form);

            // ... (Rest of your existing uploadData logic: compression, xhr, etc.) ...
            $('.photo-upload-box input[type="file"]').each(function () {
                const comp = $(this).data('compressed');
                if (comp) formData.set($(this).attr('name'), comp);
            });

            const xhr = new XMLHttpRequest();
            toggleButtons(true);
            progressContainer.classList.remove('d-none');
            // ... continue with your existing UI reset code ...
            progressBar.classList.remove('bg-danger', 'bg-success', 'bg-warning');
            progressBar.classList.add('bg-primary');
            progressBar.style.width = '0%';
            progressText.innerText = '0%';
            overlayTitle.innerText = "Processing Data";
            overlaySubtitle.innerText = "Please wait while we upload...";

            // ... (Keep your existing slow network check, xhr.upload, xhr.onload logic) ...

            let slowNetworkCheck = setInterval(() => {
                if (xhr.readyState > 0 && xhr.readyState < 4) {
                    if (parseInt(progressText.innerText) < 100) {
                        overlaySubtitle.innerText = "Slow connection detected... still working.";
                        progressBar.classList.replace('bg-primary', 'bg-warning');
                    }
                }
            }, 10000);

            window.onbeforeunload = () => "Upload in progress...";

            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    const percent = Math.round((evt.loaded / evt.total) * 100);
                    progressBar.style.width = percent + "%";
                    progressText.innerText = percent + "%";
                    if (percent === 100) {
                        uploadLabel.innerText = "Processing on Server...";
                        overlaySubtitle.innerText = "Files sent! Finalizing...";
                        progressBar.classList.replace('bg-warning', 'bg-primary');
                    }
                }
            });

            xhr.addEventListener("load", function () {
                clearInterval(slowNetworkCheck);
                window.onbeforeunload = null;
                try {
                    const res = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && res.status === 'success') {
                        handleSuccess(res, actionName);
                    } else { showError(res.message || "Logic error.", res); }
                } catch (e) { showError("Server Error: Check PHP response."); }
            });

            xhr.onerror = () => { clearInterval(slowNetworkCheck); showError("Network connection lost."); };
            xhr.open("POST", "vehicle_form.php", true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.send(formData);
        }

        // Separate function for success to keep code clean
        function handleSuccess(res, actionName) {
            if (res.id) $('input[name="vehicle_id"]').val(res.id);

            if (actionName === 'save_next') {
                overlaySubtitle.innerText = "Step Saved Successfully!";

                currentStep++;
                $('input[name="step"]').val(currentStep);
                updateWizardUI();

                // NEW: Capture the generated ID and sync the URL bar
                const currentId = res.id || $('input[name="vehicle_id"]').val() || '';
                const nextUrl = window.location.pathname + `?step=${currentStep}&id=${currentId}`;
                window.history.pushState({ path: nextUrl }, '', nextUrl);

                setTimeout(() => {
                    progressContainer.classList.add('d-none');
                    toggleButtons(false);
                }, 1000);

            } else if (actionName === 'save_draft') {
                const currentId = res.id || $('input[name="vehicle_id"]').val() || '';
                const draftUrl = window.location.pathname + `?step=${currentStep}&id=${currentId}`;
                window.history.pushState({ path: draftUrl }, '', draftUrl);

                progressContainer.classList.add('d-none');
                toggleButtons(false);
                showToast("Draft Saved Successfully!", "success");
            } else {
                window.location.href = 'dashboard.php';
            }
        }

        // -------------------------
        // D. ERROR HANDLER
        // -------------------------
        function showError(msg, xhrResponse = null) {
            progressBar.classList.replace('bg-primary', 'bg-danger');
            progressBar.style.width = '100%';

            overlayTitle.innerText = "Error";
            overlayTitle.className = "fw-bold mb-0 text-danger";
            overlaySubtitle.innerText = msg;

            // --- NEW: Highlight large files if it's an overflow error ---
            if (xhrResponse && xhrResponse.error_type === 'overflow') {
                let largeFileFound = false;

                $('input[type="file"]').each(function () {
                    const file = this.files[0];
                    if (file && file.size > (5 * 1024 * 1024)) { // 5MB limit
                        largeFileFound = true;
                        const $box = $(this).closest('.photo-upload-box');

                        // Add red border and a specific error message
                        $box.css('border', '2px solid red');
                        if ($box.find('.file-error-tag').length === 0) {
                            $box.append('<small class="file-error-tag text-danger fw-bold">Too Large (>5MB)</small>');
                        }
                    }
                });

                if (largeFileFound) {
                    overlaySubtitle.innerText = "One or more images are over 5MB. Please replace the marked images.";
                }
            }

            setTimeout(() => {
                progressContainer.classList.add('d-none');
                toggleButtons(false);
                // We use the toast instead of an alert for a better UX
                showToast(msg, 'danger');
            }, 2000);
        }

    }

    // Alternative: Make the card clickable with proper routing
    function addVehicle() {
        window.location.href = 'vehicle_form.php?step=1';
    }

    const urlParams = new URLSearchParams(window.location.search);
    const step = urlParams.get('step');

    if (step) {
        openDealModal();
    }

    function openDealModal() {
        // Get current step and vehicle ID from URL if navigating back
        // 1. Check URL Parameters immediately on page load
        const urlParams = new URLSearchParams(window.location.search);
        const stepParam = urlParams.get('step');
        const idParam = urlParams.get('id');

        if (stepParam) {
            // We use a small timeout to ensure Bootstrap and the DOM are fully ready
            setTimeout(function () {
                triggerDealModal(stepParam, idParam);
            }, 100);
        }

        // 2. Define the trigger function
        function triggerDealModal(step, vehicleId) {
            const modalElement = document.getElementById('dealModal');
            if (!modalElement) return;

            // Update form hidden fields
            $('input[name="step"]').val(step);
            $('input[name="vehicle_id"]').val(vehicleId || '');

            // Sync the global currentStep variable used by your wizard logic
            currentStep = parseInt(step);

            // Update the UI (Show/Hide steps)
            if (typeof updateWizardUI === 'function') {
                updateWizardUI();
            }

            // Open the Bootstrap Modal
            var myModal = bootstrap.Modal.getOrCreateInstance(modalElement);
            myModal.show();
        };
    }
    
window.confirmDeleteVehicle = function () {
    console.log("Delete function triggered!"); // Debugging line
    const choice = confirm("⚠️ Are you sure you want to delete this vehicle?\n\nThis action cannot be undone.");
    return choice;
};


});