 $(document).ready(function () {
            // --- LOADER ---
            setTimeout(function () {
                $('#loader').fadeOut(500);
            }, 500);

            // --- LOGIN FLOW ---
            $('#loginForm').on('submit', function (e) {
                e.preventDefault();
                window.location.href = 'dashboard.html';
            });

            $('#logoutBtn').on('click', function () {
                window.location.href = 'index.html';
            });

            // --- MODAL HANDLERS ---
            window.openModal = function (id) {
                var myModal = new bootstrap.Modal(document.getElementById(id));
                if (id === 'dealModal') resetWizard();
                myModal.show();
            };

            window.viewVehicleDetails = function () {
                var myModal = new bootstrap.Modal(document.getElementById('vehicleDetailsModal'));
                myModal.show();
            };

            // --- WIZARD LOGIC ---
            let currentStep = 1;
            const totalSteps = 4; // Removed Step 5

            window.jumpStep = function (step) {
                currentStep = step;
                updateWizardUI();
            };

            window.resetWizard = function () {
                currentStep = 1;
                updateWizardUI();
            };

            function updateWizardUI() {
                // Hide/Show Steps
                $('.wizard-step').addClass('d-none');
                $('#step-' + currentStep).removeClass('d-none');

                // Update Indicators
                $('.step-item').removeClass('active completed');
                $('.step-item').each(function () {
                    let step = $(this).data('step');
                    if (step == currentStep) $(this).addClass('active');
                    else if (step < currentStep) $(this).addClass('completed');
                });

                // Buttons
                if (currentStep === 1) $('#prevBtn').hide();
                else $('#prevBtn').show();

                if (currentStep === totalSteps) {
                    $('#nextBtn').hide();
                    $('#finishBtn').show();
                } else {
                    $('#nextBtn').show();
                    $('#finishBtn').hide();
                }
            }

            $('#nextBtn').click(function () {
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateWizardUI();
                }
            });

            $('#prevBtn').click(function () {
                if (currentStep > 1) {
                    currentStep--;
                    updateWizardUI();
                }
            });

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

            $('#finishBtn').click(function () {
                alert("Data Saved Successfully!");
                bootstrap.Modal.getInstance(document.getElementById('dealModal')).hide();
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

            // --- INVENTORY SEARCH FILTER ---
            // Search across all text in the row to cover vehicle details
            $('#invSearch, #invFilter').on('input change', function () {
                let searchVal = $('#invSearch').val().toLowerCase();
                let statusFilter = $('#invFilter').val();

                $('.inv-row').each(function () {
                    let rowText = $(this).text().toLowerCase(); // Includes Vehicle No, Model, Year, Price
                    let rowStatus = $(this).data('status');

                    let matchSearch = rowText.indexOf(searchVal) > -1;
                    let matchFilter = (statusFilter === 'all') || (rowStatus === statusFilter);

                    if (matchSearch && matchFilter) $(this).show();
                    else $(this).hide();
                });
            });

            // Delete Lead Action
            $(document).on('click', '.delete-lead', function () {
                if (confirm('Are you sure you want to delete this lead?')) {
                    $(this).closest('.lead-item').remove();
                }
            });
        });