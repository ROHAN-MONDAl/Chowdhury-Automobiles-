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
    // 4. SEARCH FUNCTIONALITY
    // ==========================

    $("#leadSearchInput").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        var hasVisibleItems = false;

        $("#leadsTable .lead-item").filter(function () {
            var isMatch = $(this).text().toLowerCase().indexOf(value) > -1;
            $(this).toggle(isMatch);
            if (isMatch) hasVisibleItems = true;
        });

        // Toggle "No Results" message
        if (!hasVisibleItems && value.length > 0) {
            $("#noResultsMsg").show();
        } else {
            $("#noResultsMsg").hide();
        }
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

    // Vehicle Delete Confirmation
    window.confirmDeleteVehicle = function () {
        return confirm("⚠️ Are you sure you want to delete this vehicle?\n\nThis action cannot be undone.");
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
        window.prevStep = function (e) {
            if (e) e.preventDefault();

            // Block navigation if data is processing
            const btn = document.getElementById('prevBtn');
            if (btn && btn.disabled) return false;

            if (currentStep > 1) {
                currentStep--;
                $('input[name="step"]').val(currentStep);
                updateWizardUI();

                // SYNC URL: keep the ID if it exists, otherwise leave empty
                const vehicleId = $('input[name="vehicle_id"]').val() || '';
                const newurl = window.location.pathname + `?step=${currentStep}&id=${vehicleId}`;
                window.history.pushState({ path: newurl }, '', newurl);
            }
        };

        $('#prevBtn').on('click', function (e) {
            window.prevStep(e);
        });

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
                // Special handling for Radio Groups
                if (el.attr('type') === 'radio') {
                    const name = el.attr('name');
                    if ($(`input[name="${name}"]:checked`).length === 0) {
                        showToast(errorMsg, 'danger');
                        return false;
                    }
                    return true;
                }

                // Standard handling for Input/Select
                if (!el.val() || el.val().trim() === '') {
                    showToast(errorMsg, 'danger'); // Show modern toast
                    el.addClass('is-invalid');    // Add red border
                    el.focus();

                    // Auto-remove red border when user starts typing/fixing
                    el.off('input change').on('input change', function () {
                        $(this).removeClass('is-invalid');
                    });
                    return false;
                }
                el.removeClass('is-invalid');
                return true;
            }

            // A. Core Fields
            if (!check('select[name="vehicle_type"]', "Mandatory: Select Vehicle Type")) return false;
            if (!check('input[name="name"]', "Mandatory: Bike Name")) return false;
            if (!check('input[name="vehicle_number"]', "Mandatory: Vehicle Number")) return false;
            if (!check('input[name="chassis_number"]', "Mandatory: Chassis Number")) return false;
            if (!check('input[name="engine_number"]', "Mandatory: Engine Number")) return false;

            // B. Photo Check (Only for new vehicles)
            const vid = $('input[name="vehicle_id"]').val();
            const photoInput = $('input[name="photo1"]');
            if (!vid && (!photoInput.val() || photoInput[0].files.length === 0)) {
                showToast("Mandatory: Upload Vehicle Photo", 'danger');
                photoInput.closest('.photo-upload-box').addClass('border-danger');
                return false;
            }

            // C. Payment Logic
            const payType = $('input[name="payment_type"]:checked').val();
            if (payType === 'Cash') {
                if (!check('input[name="cash_price"]', "Mandatory: Enter Cash Price")) return false;
            } else {
                // Check if radio button for online method is picked
                if ($('input[name="online_method"]:checked').length === 0) {
                    showToast("Mandatory: Select Online Payment Method", 'danger');
                    return false;
                }
                if (!check('input[name="online_transaction_id"]', "Mandatory: Enter Transaction ID")) return false;
                if (!check('input[name="online_price"]', "Mandatory: Enter Online Price")) return false;
            }

            return true; // Everything is valid
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
        // B. BUTTON SETUP
        // -------------------------
        setupButton('btn-save-draft', 'save_draft', true); // Draft = No Validation
        setupButton('btn-next', 'save_next', true);  // Next = Validate
        setupButton('btn-finish', 'finish', true);  // Finish = Validate

        function setupButton(btnId, actionName, validate) {
            const btn = document.getElementById(btnId);
            if (!btn) return;

            btn.addEventListener('click', function (e) {
                e.preventDefault();

                // --- NEW: STEP 1 INSTANT JUMP (No Server Call) ---
                if (currentStep === 1 && actionName === 'save_next') {
                    if (validate && !validateStep1()) return;

                    currentStep = 2;
                    $('input[name="step"]').val(currentStep);
                    updateWizardUI();

                    // URL Sync for Jump
                    const jumpUrl = window.location.pathname + `?step=2&id=`;
                    window.history.pushState({ path: jumpUrl }, '', jumpUrl);

                    showToast("Step 1 Validated. Enter Seller Details.", "info");
                    return;
                }

                // --- Normal Save Logic for other steps ---
                if (validate && !form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                actionInput.value = actionName;
                uploadData(btn, actionName);
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
            const formData = new FormData(form);

            // Swap files with compressed versions if stored in jQuery data
            $('.photo-upload-box input[type="file"]').each(function () {
                const comp = $(this).data('compressed');
                if (comp) formData.set($(this).attr('name'), comp);
            });

            const xhr = new XMLHttpRequest();

            // 1. Increase Timeout to 5 minutes (300,000 ms)
            xhr.timeout = 300000;

            // 2. RESET UI STATE (Removes the "stuck" error look)
            toggleButtons(true);
            progressContainer.classList.remove('d-none');
            progressBar.classList.remove('bg-danger', 'bg-success'); // Clear previous colors
            progressBar.classList.add('bg-primary'); // Set back to blue
            progressBar.style.width = '0%';
            progressText.innerText = '0%';
            overlayTitle.innerText = "Processing Data";
            overlayTitle.className = "fw-bold mb-0 text-dark";
            overlaySubtitle.innerText = "Please wait while we upload...";

            // Prevent accidental tab close
            window.onbeforeunload = () => "Data is processing. Are you sure you want to leave?";

            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    const percent = Math.round((evt.loaded / evt.total) * 100);
                    progressBar.style.width = percent + "%";
                    progressText.innerText = percent + "%";

                    // Helpful text for slow networks
                    if (percent === 100) {
                        uploadLabel.innerText = "Processing on Server...";
                        overlaySubtitle.innerText = "Files sent! Waiting for database to save...";
                    }
                }
            });

            xhr.addEventListener("load", function () {
                window.onbeforeunload = null;
                try {
                    if (!xhr.responseText) throw new Error("Empty response");
                    const res = JSON.parse(xhr.responseText);

                    if (xhr.status === 200 && res.status === 'success') {

                        // 1. Capture ID: Priority to server response, fallback to existing input
                        let currentId = res.id || $('input[name="vehicle_id"]').val() || '';

                        if (res.id) {
                            $('input[name="vehicle_id"]').val(res.id);
                        }

                        if (actionName === 'save_next') {
                            // Increment step
                            currentStep++;
                            $('input[name="step"]').val(currentStep);

                            // Update UI
                            updateWizardUI();

                            // 2. SYNC URL BAR
                            // This ensures Step 3 shows the ID (e.g., ?step=3&id=242)
                            const newurl = window.location.pathname + `?step=${currentStep}&id=${currentId}`;
                            window.history.pushState({ path: newurl }, '', newurl);

                            progressContainer.classList.add('d-none');
                            toggleButtons(false);
                            showToast("Step " + (currentStep - 1) + " Saved!", "success");

                        } else if (actionName === 'save_draft') {
                            // Update URL for Draft so the ID appears in the address bar immediately
                            const draftUrl = window.location.pathname + `?step=${currentStep}&id=${currentId}`;
                            window.history.pushState({ path: draftUrl }, '', draftUrl);

                            progressContainer.classList.add('d-none');
                            toggleButtons(false);
                            showToast("Draft Saved!", "success");
                        } else {
                            window.location.href = 'dashboard.php';
                        }
                    } else {
                        showError(res.message || "Logic error occurred.");
                    }
                } catch (e) {
                    showError("Server Error: Check PHP response.");
                }
            });

            // 3. ACTUAL NETWORK ERROR CHECK
            xhr.onerror = function () {
                window.onbeforeunload = null;
                // Only show "Network Lost" if xhr.status is 0 (browser could not reach server)
                if (xhr.status === 0) {
                    showError("Network connection lost. Check your internet.");
                } else {
                    showError("An unexpected error occurred (Code: " + xhr.status + ")");
                }
            };

            xhr.ontimeout = function () {
                window.onbeforeunload = null;
                showError("Upload timed out due to slow connection. Try again.");
            };

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
        function showError(msg) {
            // UI Red Bar
            progressBar.classList.remove('bg-primary');
            progressBar.classList.add('bg-danger');
            progressBar.style.width = '100%';

            overlayTitle.innerText = "Error";
            overlayTitle.className = "fw-bold mb-0 text-danger";
            overlaySubtitle.innerText = msg;

            // UNLOCK BUTTONS after 1.5 seconds so user can fix and retry
            setTimeout(() => {
                alert(msg);
                progressContainer.classList.add('d-none');
                toggleButtons(false);
            }, 1500);
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
    }// End if(form)

});