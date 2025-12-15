<?php
require "db.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

// Get the vehicle ID from URL
$vehicle_id = intval($_GET['id']);

// Fetch vehicle data
$query = $conn->prepare("SELECT * FROM vehicle WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$vehicle = $query->get_result()->fetch_assoc();
?>


<!-- Inventory Dashboard Page for Chowdhury Automobile Management System -->
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8"> <!-- Character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- Responsive design for mobile -->

    <!-- Page Title -->
    <title>Chowdhury Automobile | Manager</title>
    <!-- Favicon Icon -->
    <link rel="icon" type="image/png" href="../images/logo.jpeg">
    <!-- ==================== Stylesheets ==================== -->

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- ==================== Scripts ==================== -->

    <!-- Icons (Phosphor Icons) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="script.js" defer></script>
    <style>
        /* Custom styles matching original design */
        .step-circle {
            width: 40px;
            height: 40px;
            border: 2px solid #dee2e6;
            font-weight: 600;
            font-size: 1rem;
        }

        .step-item.active .step-circle {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: white !important;
        }

        .step-item.active .step-label {
            color: #0d6efd !important;
            font-weight: 600 !important;
        }

        .photo-upload-box {
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 100px;
        }

        .photo-upload-box:hover {
            border-color: #0d6efd !important;
            background-color: #f8f9fa !important;
        }

        .photo-upload-box i {
            transition: all 0.3s ease;
        }

        .photo-upload-box:hover i {
            color: #0d6efd !important;
        }

        .ls-1 {
            letter-spacing: 1px;
        }

        /* Make form controls look consistent */
        .form-control,
        .form-select {
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
            padding: 0.5rem 0.75rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .wizard-nav {
                gap: 0.5rem !important;
            }

            .step-circle {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }

            .modal-footer .btn {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .d-flex.gap-2 {
                gap: 0.5rem !important;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 1rem !important;
            }

            .btn-group-sm {
                flex-wrap: wrap;
            }

            .btn-group-sm .btn {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
        }
    </style>

</head>


<body>
    <!-- Loading Spinner Section - Displays a loading animation with 'LOADING...' text while the page initializes -->
    <div id="loader"
        style="position:fixed; inset:0; background:rgba(242,242,247,0.98); z-index:9999; display:flex; justify-content:center; align-items:center; flex-direction:column;">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
        <div class="mt-3 fw-bold text-secondary" style="letter-spacing: 1px;">LOADING...</div>
    </div>

    <!-- Main Dashboard Section - Contains the navigation bar and overall layout structure -->
    <section id="dashboard-section">
        <!-- HEADER / NAVIGATION BAR -->
        <!-- Responsive: Sticky-top, shadows, and flex ensures horizontal layout adapts on small screens -->
        <nav class="navbar bg-white sticky-top shadow-sm rounded-4 py-3 mt-3">
            <div class="container-fluid d-flex flex-wrap align-items-center justify-content-between">

                <!-- Brand -->
                <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-1"
                        style="width: 48px; height: 48px; overflow: hidden; padding: 2px;">
                        <img src="../images/logo.jpeg" alt="Chowdhury Automobile Logo" class="rounded-circle"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>

                    <div class="d-flex flex-column lh-1">
                        <span class="fs-5 fw-bolder text-dark">CHOWDHURY</span>
                        <span class="text-secondary fw-bold text-uppercase"
                            style="font-size: 0.7rem; letter-spacing: 1.5px;">
                            Automobile
                        </span>
                    </div>
                </a>


                <!-- Desktop Log Out Button -->
                <button id="logoutBtn"
                    class="btn btn-light border fw-bold rounded-pill px-3 py-1 shadow-sm d-none d-md-inline-block">
                    Log Out
                </button>

                <!-- Mobile Log Out Icon -->
                <button id="logoutIcon"
                    class="btn btn-light border rounded-circle shadow-sm d-inline-flex d-md-none justify-content-center align-items-center p-2">
                    <i class="ph-bold ph-sign-out fs-5"></i>
                </button>

            </div>
        </nav>
    </section>

    <div class="container pb-5">

        <!-- Inventory Dashboard Page -->
        <div class="container-fluid py-3">

            <!-- Dashboard Header -->
            <div class="p-2 p-md-3 rounded-4 shadow-sm bg-white mb-3">
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">

                    <!-- Title and Status -->
                    <div>
                        <h2 class="fw-bold fs-5 mb-1">Inventory Dashboard</h2>
                        <p class="text-secondary small mb-0">
                            <i class="ph-fill ph-check-circle text-success me-1"></i> Active • Tracking vehicle data
                        </p>
                    </div>

                    <!-- Buttons: Back + Filters -->
                    <div class="d-flex gap-1 flex-wrap">
                        <button class="btn btn-secondary btn-sm rounded-pill px-3 py-1" onclick="history.back()">
                            <i class="ph-bold ph-arrow-left me-1"></i>Back
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Vehicle Data Grid - Displays inventory vehicles in a responsive card layout with availability status and action buttons -->
        <div class="position-relative" style="min-height: 400px;">
            <div class="row ">
                <div class="col">
                    <!-- Edit vehicle form -->
                    <div class="card border-0 shadow-lg rounded-4">
                        <input type="hidden" id="editVehicleNo">
                        <div>
                            <div class="card-header border-0 pt-4 pb-2 d-flex align-items-center justify-content-between bg-white">

                                <!-- Left Section: Icon + Title -->
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm border"
                                        style="width: 45px; height: 45px; overflow: hidden; padding: 2px;">
                                        <img src="../images/logo.jpeg" alt="Chowdhury Automobile" class="rounded-circle"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>

                                    <div class="d-flex flex-column lh-1">
                                        <span class="fs-5 fw-bolder text-dark">CHOWDHURY</span>
                                        <span class="text-secondary fw-bold text-uppercase"
                                            style="font-size: 0.75rem; letter-spacing: 1.5px;">
                                            Automobile
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Wizard -->
                            <div class="wizard-nav d-flex justify-content-between flex-wrap gap-3 mt-3 px-2 text-center">

                                <div class="step-item d-flex flex-column align-items-center flex-fill" data-step="1">
                                    <div class="step-circle d-flex align-items-center justify-content-center rounded-circle">
                                        1
                                    </div>
                                    <div class="step-label small mt-1">Vehicle</div>
                                </div>

                                <div class="step-item d-flex flex-column align-items-center flex-fill" data-step="2">
                                    <div class="step-circle d-flex align-items-center justify-content-center rounded-circle">
                                        2
                                    </div>
                                    <div class="step-label small mt-1">Seller</div>
                                </div>

                                <div class="step-item d-flex flex-column align-items-center flex-fill" data-step="3">
                                    <div class="step-circle d-flex align-items-center justify-content-center rounded-circle">
                                        3
                                    </div>
                                    <div class="step-label small mt-1">Purchaser</div>
                                </div>

                                <div class="step-item d-flex flex-column align-items-center flex-fill" data-step="4">
                                    <div class="step-circle d-flex align-items-center justify-content-center rounded-circle">
                                        4
                                    </div>
                                    <div class="step-label small mt-1">Transfer</div>
                                </div>

                            </div>
                            <div class="card-body p-4">

                                <?php
                                // ---------------------------
                                // 1. Get ID
                                // ---------------------------
                                $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                                $vehicle = [];

                                // ---------------------------
                                // 2. Ensure DB connection
                                // ---------------------------
                                if (!isset($conn)) {
                                    die("Database connection not found. Include db.php file.");
                                }

                                // ---------------------------
                                // 3. Fetch data using prepared statement
                                // ---------------------------
                                if ($id > 0) {
                                    $stmt = $conn->prepare("SELECT * FROM stock_vehicle_details WHERE id = ? LIMIT 1");
                                    $stmt->bind_param("i", $id);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result && $result->num_rows > 0) {
                                        $vehicle = $result->fetch_assoc();
                                    }
                                    $stmt->close();
                                }

                                // ---------------------------
                                // 4. Helper : SELECT tag
                                // ---------------------------
                                function isSelected($dbValue, $optionValue)
                                {
                                    return ($dbValue == $optionValue) ? "selected" : "";
                                }

                                // ---------------------------
                                // 5. Helper : Radio / Checkbox
                                // ---------------------------
                                function isChecked($dbValue, $checkValue)
                                {
                                    return ($dbValue == $checkValue) ? "checked" : "";
                                }

                                ?>


                                <form action="vechicle_update_form.php" id="dealForm" method="POST" class="app-form" enctype="multipart/form-data">
                                    <input type="hidden" name="row_id" value="<?= $id ?>">

                                    <!-- STEP 1: VEHICLE -->
                                    <div id="step-1" class="wizard-step">
                                        <div class="card steps-id p-4 border-0 shadow-sm position-relative sold-wrapper rounded-4">
                                            <div>

                                                <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Vehicle Details</h6>

                                                <!-- VEHICLE PHOTOS -->
                                                <label class="mb-2">Vehicle Photos</label>
                                                <div class="row g-3 mb-4">
                                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                                        <div class="col-6 col-md-3">
                                                            <div class="photo-upload-box">

                                                                <?php if (!empty($vehicle["photo$i"])): ?>
                                                                    <img src="../<?= $vehicle["photo$i"] ?>" class="d-block w-100 h-100 object-fit-cover rounded">
                                                                    <i class="ph-bold ph-camera fs-3 text-secondary d-none"></i>
                                                                <?php else: ?>
                                                                    <i class="ph-bold ph-camera fs-3 text-secondary"></i>
                                                                    <img src="" class="d-none">
                                                                <?php endif; ?>

                                                                <input type="file" name="photo<?= $i ?>" accept="image/*" hidden>
                                                            </div>
                                                        </div>
                                                    <?php endfor; ?>
                                                </div>

                                                <!-- VEHICLE FIELDS -->
                                                <div class="row g-3 mb-3">

                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label">Vehicle Type</label>
                                                        <select id="vehicleType" name="vehicle_type" class="form-select fw-bold">
                                                            <option disabled>Choose Vehicle Type</option>
                                                            <?php
                                                            $types = [
                                                                'Scooters',
                                                                'Mopeds',
                                                                'Dirt / Off-road Bikes',
                                                                'Electric Bikes',
                                                                'Cruiser Bikes',
                                                                'Sport Bikes',
                                                                'Touring Bikes',
                                                                'Adventure / Dual-Sport Bikes',
                                                                'Naked / Standard Bikes',
                                                                'Cafe Racers',
                                                                'Bobbers',
                                                                'Choppers',
                                                                'Pocket Bikes / Mini Bikes'
                                                            ];
                                                            foreach ($types as $t):
                                                            ?>
                                                                <option <?= isSelected($vehicle['vehicle_type'] ?? '', $t) ?>><?= $t ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-12 col-md-6">
                                                        <label class="fw-bold">Name</label>
                                                        <input type="text" name="name" class="form-control"
                                                            value="<?= $vehicle['name'] ?? '' ?>" placeholder="Enter Name">
                                                    </div>

                                                    <div class="col-12 col-md-6">
                                                        <label>Vehicle Number</label>
                                                        <input type="text" name="vehicle_number"
                                                            class="form-control fw-bold text-uppercase"
                                                            value="<?= $vehicle['vehicle_number'] ?? 'WB ' ?>">
                                                    </div>

                                                    <div class="col-12 col-md-6">
                                                        <label>Register Date</label>
                                                        <input type="date" name="register_date" class="form-control"
                                                            value="<?= $vehicle['register_date'] ?? date('Y-m-d') ?>">
                                                    </div>

                                                    <div class="col-12 col-md-4">
                                                        <label>Owner Serial</label>
                                                        <select name="owner_serial" class="form-select">
                                                            <?php
                                                            $owners = ['1st', '2nd', '3rd', '4th', '5th'];
                                                            foreach ($owners as $os):
                                                            ?>
                                                                <option <?= isSelected($vehicle['owner_serial'] ?? '', $os) ?>><?= $os ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-12 col-md-4">
                                                        <label>Chassis Number</label>
                                                        <input type="text" name="chassis_number" class="form-control text-uppercase"
                                                            value="<?= $vehicle['chassis_number'] ?? '' ?>">
                                                    </div>

                                                    <div class="col-12 col-md-4">
                                                        <label>Engine Number</label>
                                                        <input type="text" name="engine_number" class="form-control text-uppercase"
                                                            value="<?= $vehicle['engine_number'] ?? '' ?>">
                                                    </div>

                                                </div>

                                                <!-- PAYMENT SECTION -->
                                                <?php $pType = $vehicle['payment_type'] ?? 'Cash'; ?>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-12 col-md-6">
                                                        <label class="fw-bold mb-2">Payment Type</label>

                                                        <div class="d-flex gap-2 mb-3">
                                                            <input type="radio" class="btn-check" name="payment_type" id="sp_cash" value="Cash"
                                                                <?= isChecked($pType, 'Cash') ?> data-bs-toggle="collapse" data-bs-target="#cashBox">
                                                            <label class="btn btn-outline-success" for="sp_cash">Cash</label>

                                                            <input type="radio" class="btn-check" name="payment_type" id="sp_online" value="Online"
                                                                <?= isChecked($pType, 'Online') ?> data-bs-toggle="collapse" data-bs-target="#onlineBox">
                                                            <label class="btn btn-outline-primary" for="sp_online">Online</label>
                                                        </div>

                                                        <div id="payBoxes">

                                                            <!-- CASH BOX -->
                                                            <div id="cashBox" class="collapse <?= $pType == 'Cash' ? 'show' : '' ?>">
                                                                <div class="p-3 bg-white rounded shadow-sm">
                                                                    <label class="fw-bold small">Bike Price</label>
                                                                    <input type="number" step="0.01" name="cash_price"
                                                                        class="form-control form-control-sm"
                                                                        value="<?= $vehicle['cash_price'] ?? '' ?>">
                                                                </div>
                                                            </div>

                                                            <!-- ONLINE BOX -->
                                                            <div id="onlineBox" class="collapse <?= $pType == 'Online' ? 'show' : '' ?>">
                                                                <div class="p-3 bg-white rounded shadow-sm">

                                                                    <!-- METHODS -->
                                                                    <?php $methods = ['Google Pay', 'Paytm', 'PhonePe', 'BharatPe']; ?>
                                                                    <div class="mb-2">
                                                                        <?php foreach ($methods as $m):
                                                                            $idSafe = strtolower(str_replace(' ', '', $m));
                                                                        ?>
                                                                            <div class="form-check d-inline-block me-3">
                                                                                <input type="radio" class="form-check-input" name="online_method"
                                                                                    value="<?= $m ?>" id="om_<?= $idSafe ?>"
                                                                                    <?= isChecked($vehicle['online_method'] ?? '', $m) ?>>
                                                                                <label class="form-check-label small fw-bold" for="om_<?= $idSafe ?>"><?= $m ?></label>
                                                                            </div>
                                                                        <?php endforeach; ?>
                                                                    </div>

                                                                    <input type="text" name="online_transaction_id"
                                                                        class="form-control form-control-sm text-uppercase mb-3"
                                                                        placeholder="Transaction / UPI ID"
                                                                        value="<?= $vehicle['online_transaction_id'] ?? '' ?>">

                                                                    <label class="fw-bold small">Bike Price</label>
                                                                    <input type="number" step="0.01" name="online_price"
                                                                        class="form-control"
                                                                        value="<?= $vehicle['online_price'] ?? '' ?>">
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>
                                                </div>

                                                <!-- CHALLAN -->
                                                <?php $hasChallan = ($vehicle['police_challan'] ?? 'No') == 'Yes'; ?>

                                                <div class="p-3 rounded bg-light border">
                                                    <label>Police Challan</label>
                                                    <div class="d-flex gap-3 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="police_challan" value="No"
                                                                <?= isChecked($vehicle['police_challan'] ?? 'No', 'No') ?>
                                                                data-bs-toggle="collapse" data-bs-target="#challan-section">
                                                            <label class="form-check-label fw-bold">No</label>
                                                        </div>

                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="police_challan" value="Yes"
                                                                <?= isChecked($vehicle['police_challan'] ?? 'No', 'Yes') ?>
                                                                data-bs-toggle="collapse" data-bs-target="#challan-section">
                                                            <label class="form-check-label fw-bold">Yes</label>
                                                        </div>
                                                    </div>

                                                    <div id="challan-section" class="collapse <?= $hasChallan ? 'show' : '' ?>">

                                                        <?php for ($c = 1; $c <= 3; $c++): ?>
                                                            <div class="border rounded p-2 mb-2 bg-white">

                                                                <label class="fw-bold small">Challan <?= $c ?></label>

                                                                <div class="row g-2">

                                                                    <div class="col-md-4">
                                                                        <input type="text" name="challan<?= $c ?>_number"
                                                                            class="form-control text-uppercase"
                                                                            value="<?= $vehicle["challan{$c}_number"] ?? '' ?>">
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <input type="number" step="0.01" name="challan<?= $c ?>_amount"
                                                                            class="form-control"
                                                                            value="<?= $vehicle["challan{$c}_amount"] ?? '' ?>">
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <?php $cStatus = $vehicle["challan{$c}_status"] ?? 'Pending'; ?>
                                                                        <div class="btn-group w-100 btn-group-sm">

                                                                            <input type="radio" class="btn-check" name="challan<?= $c ?>_status"
                                                                                value="Pending" id="pen<?= $c ?>" <?= isChecked($cStatus, 'Pending') ?>>
                                                                            <label class="btn btn-outline-danger" for="pen<?= $c ?>">Pending</label>

                                                                            <input type="radio" class="btn-check" name="challan<?= $c ?>_status"
                                                                                value="Paid" id="paid<?= $c ?>" <?= isChecked($cStatus, 'Paid') ?>>
                                                                            <label class="btn btn-outline-success" for="paid<?= $c ?>">Paid</label>

                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        <?php endfor; ?>

                                                    </div>
                                                </div>

                                                <!-- SOLD OUT -->
                                                <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                                                    <label class="fw-bold text-danger">Mark as Sold Out</label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="sold_out" value="1"
                                                            style="width:3em;height:1.5em;"
                                                            <?= isChecked($vehicle['sold_out'] ?? 0, 1) ?>>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                    <!-- STEP 2: SELLER -->
                                    <div id="step-2" class="wizard-step d-none">
                                        <div class="card steps-id border-0 p-4 shadow-sm rounded-4">
                                            <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Seller Details</h6>

                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-4">
                                                    <label>Date</label>
                                                    <input type="date" name="seller_date" class="form-control"
                                                        value="<?= $vehicle['seller_date'] ?? date('Y-m-d') ?>">
                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <label>Vehicle No</label>
                                                    <input type="text" name="seller_vehicle_number" class="form-control fw-bold text-uppercase"
                                                        placeholder="WB 00 AA 0000"
                                                        value="<?= $vehicle['seller_vehicle_number'] ?? 'WB ' ?>">
                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <label>Bike Name</label>
                                                    <input type="text" name="seller_bike_name" class="form-control text-uppercase"
                                                        value="<?= $vehicle['seller_bike_name'] ?? '' ?>">
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <label>Chassis No</label>
                                                    <input type="text" name="seller_chassis_no" class="form-control text-uppercase"
                                                        value="<?= $vehicle['seller_chassis_no'] ?? '' ?>">
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <label>Engine No</label>
                                                    <input type="text" name="seller_engine_no" class="form-control text-uppercase"
                                                        value="<?= $vehicle['seller_engine_no'] ?? '' ?>">
                                                </div>

                                                <div class="col-12">
                                                    <label>Seller Name</label>
                                                    <input type="text" name="seller_name" class="form-control text-uppercase"
                                                        value="<?= $vehicle['seller_name'] ?? '' ?>">
                                                </div>

                                                <div class="col-12">
                                                    <label>Address</label>
                                                    <textarea name="seller_address" class="form-control text-uppercase" rows="2"><?= $vehicle['seller_address'] ?? '' ?></textarea>
                                                </div>
                                            </div>

                                            <label class="mb-2">Mobile Numbers</label>
                                            <div class="row g-2 mb-3">
                                                <div class="col-12">
                                                    <input type="tel" name="seller_mobile1" class="form-control" placeholder="Mob 1"
                                                        value="<?= $vehicle['seller_mobile1'] ?? '' ?>">
                                                </div>
                                                <div class="col-12">
                                                    <input type="tel" name="seller_mobile2" class="form-control" placeholder="Mob 2"
                                                        value="<?= $vehicle['seller_mobile2'] ?? '' ?>">
                                                </div>
                                                <div class="col-12">
                                                    <input type="tel" name="seller_mobile3" class="form-control" placeholder="Mob 3"
                                                        value="<?= $vehicle['seller_mobile3'] ?? '' ?>">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="mb-2">Seller Documents</label>
                                                <div class="row g-2">
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Aadhar Front</span>
                                                            <?php if (!empty($vehicle['doc_aadhar_front'])): ?>
                                                                <img src="<?= $vehicle['doc_aadhar_front'] ?>" class="d-block w-100 h-100 object-fit-cover rounded">
                                                            <?php else: ?>
                                                                <img src="" class="d-none">
                                                            <?php endif; ?>
                                                            <input type="file" name="doc_aadhar_front" accept="image/*" hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Aadhar Back</span>
                                                            <?php if (!empty($vehicle['doc_aadhar_back'])): ?>
                                                                <img src="<?= $vehicle['doc_aadhar_back'] ?>" class="d-block w-100 h-100 object-fit-cover rounded">
                                                            <?php else: ?>
                                                                <img src="" class="d-none">
                                                            <?php endif; ?>
                                                            <input type="file" name="doc_aadhar_back" accept="image/*" hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Voter Front</span>
                                                            <?php if (!empty($vehicle['doc_voter_front'])): ?>
                                                                <img src="<?= $vehicle['doc_voter_front'] ?>" class="d-block w-100 h-100 object-fit-cover rounded">
                                                            <?php else: ?>
                                                                <img src="" class="d-none">
                                                            <?php endif; ?>
                                                            <input type="file" name="doc_voter_front" accept="image/*" hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Voter Back</span>
                                                            <?php if (!empty($vehicle['doc_voter_back'])): ?>
                                                                <img src="<?= $vehicle['doc_voter_back'] ?>" class="d-block w-100 h-100 object-fit-cover rounded">
                                                            <?php else: ?>
                                                                <img src="" class="d-none">
                                                            <?php endif; ?>
                                                            <input type="file" name="doc_voter_back" accept="image/*" hidden>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bg-light p-3 rounded-4 border mb-3">
                                                <label class="mb-2 fw-bold">Papers Received</label>

                                                <?php
                                                // 1. Smart Logic for RC: Check if flag is 1 OR if image data exists
                                                $hasRC = ($vehicle['pr_rc'] ?? 0) == 1 || !empty($vehicle['rc_front']) || !empty($vehicle['rc_back']);

                                                // 2. Smart Logic for NOC: Check if flag is 1 OR if image data exists
                                                $hasNOC = ($vehicle['pr_noc'] ?? 0) == 1 || !empty($vehicle['noc_front']) || !empty($vehicle['noc_back']);
                                                ?>

                                                <div class="d-flex flex-wrap gap-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="pr_rc" value="1" id="pr_rc"
                                                            <?= $hasRC ? 'checked' : '' ?>
                                                            data-bs-toggle="collapse" data-bs-target="#rcUploadBox">
                                                        <label class="fw-bold" for="pr_rc">RC</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="pr_tax" value="1" id="pr_tax"
                                                            <?= ($vehicle['pr_tax'] ?? 0) == 1 ? 'checked' : '' ?>>
                                                        <label class="fw-bold" for="pr_tax">Tax Token</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="pr_insurance" value="1" id="pr_ins"
                                                            <?= ($vehicle['pr_insurance'] ?? 0) == 1 ? 'checked' : '' ?>>
                                                        <label class="fw-bold" for="pr_ins">Insurance</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="pr_pucct" value="1" id="pr_puc"
                                                            <?= ($vehicle['pr_pucct'] ?? 0) == 1 ? 'checked' : '' ?>>
                                                        <label class="fw-bold" for="pr_puc">PUCC</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="pr_noc" value="1" id="pr_noc"
                                                            <?= $hasNOC ? 'checked' : '' ?>
                                                            data-bs-toggle="collapse" data-bs-target="#nocUploadBox">
                                                        <label class="fw-bold" for="pr_noc">NOC</label>
                                                    </div>
                                                </div>

                                                <div class="collapse mt-3 <?= $hasRC ? 'show' : '' ?>" id="rcUploadBox">
                                                    <label class="fw-bold small">RC Upload</label>
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <div class="border rounded p-2 text-center bg-white position-relative">
                                                                <small class="fw-bold d-block mb-1" style="font-size:10px">RC FRONT</small>

                                                                <?php if (!empty($vehicle['rc_front'])): ?>
                                                                    <div class="mb-2">
                                                                        <img src="<?= $vehicle['rc_front'] ?>" class="img-fluid rounded border"
                                                                            style="max-height: 100px; width: 100%; object-fit: cover;">
                                                                    </div>
                                                                <?php endif; ?>

                                                                <input type="file" name="rc_front" class="form-control form-control-sm mt-1">
                                                            </div>
                                                        </div>

                                                        <div class="col-6">
                                                            <div class="border rounded p-2 text-center bg-white position-relative">
                                                                <small class="fw-bold d-block mb-1" style="font-size:10px">RC BACK</small>

                                                                <?php if (!empty($vehicle['rc_back'])): ?>
                                                                    <div class="mb-2">
                                                                        <img src="<?= $vehicle['rc_back'] ?>" class="img-fluid rounded border"
                                                                            style="max-height: 100px; width: 100%; object-fit: cover;">
                                                                    </div>
                                                                <?php endif; ?>

                                                                <input type="file" name="rc_back" class="form-control form-control-sm mt-1">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="collapse mt-3 <?= $hasNOC ? 'show' : '' ?>" id="nocUploadBox">
                                                    <label class="fw-bold small">NOC Status</label>

                                                    <div class="d-flex justify-content-center">
                                                        <div class="btn-group w-75 btn-group-sm mb-3 mx-auto" role="group">
                                                            <?php $nocStatus = $vehicle['noc_status'] ?? 'Paid'; ?>

                                                            <input type="radio" class="btn-check" name="noc_status" value="Paid" id="noc_paid"
                                                                <?= $nocStatus == 'Paid' ? 'checked' : '' ?>>
                                                            <label class="btn btn-outline-success" for="noc_paid">Paid</label>

                                                            <input type="radio" class="btn-check" name="noc_status" value="Due" id="noc_due"
                                                                <?= $nocStatus == 'Due' ? 'checked' : '' ?>>
                                                            <label class="btn btn-outline-danger" for="noc_due">Due</label>
                                                        </div>
                                                    </div>

                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <div class="border rounded small-box text-center p-2">
                                                                <span class="small text-muted fw-bold">NOC Front</span>

                                                                <?php if (!empty($vehicle['noc_front'])): ?>
                                                                    <div class="mb-2">
                                                                        <img src="<?= $vehicle['noc_front'] ?>" class="img-fluid rounded border"
                                                                            style="max-height: 100px; width: 100%; object-fit: cover;">
                                                                    </div>
                                                                <?php endif; ?>

                                                                <input type="file" name="noc_front" class="form-control form-control-sm mt-1">
                                                            </div>
                                                        </div>

                                                        <div class="col-6">
                                                            <div class="border rounded small-box text-center p-2">
                                                                <span class="small text-muted fw-bold">NOC Back</span>

                                                                <?php if (!empty($vehicle['noc_back'])): ?>
                                                                    <div class="mb-2">
                                                                        <img src="<?= $vehicle['noc_back'] ?>" class="img-fluid rounded border"
                                                                            style="max-height: 100px; width: 100%; object-fit: cover;">
                                                                    </div>
                                                                <?php endif; ?>

                                                                <input type="file" name="noc_back" class="form-control form-control-sm mt-1">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php $sPayType = $vehicle['seller_payment_type'] ?? 'Cash'; ?>
                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label class="fw-bold mb-2">Payment Type</label>
                                                    <div class="d-flex gap-2 mb-3">
                                                        <input type="radio" class="btn-check" name="seller_payment_type" value="Cash" id="sell_pay_cash"
                                                            <?= $sPayType == 'Cash' ? 'checked' : '' ?>
                                                            data-bs-toggle="collapse" data-bs-target="#sellerCashBox">
                                                        <label class="btn btn-outline-success" for="sell_pay_cash">Cash</label>

                                                        <input type="radio" class="btn-check" name="seller_payment_type" value="Online" id="sell_pay_online"
                                                            <?= $sPayType == 'Online' ? 'checked' : '' ?>
                                                            data-bs-toggle="collapse" data-bs-target="#sellerOnlineBox">
                                                        <label class="btn btn-outline-primary" for="sell_pay_online">Online</label>
                                                    </div>

                                                    <div id="sellerPayAccordion">
                                                        <div id="sellerCashBox" class="collapse <?= $sPayType == 'Cash' ? 'show' : '' ?>" data-bs-parent="#sellerPayAccordion">
                                                            <div class="p-3 bg-white border rounded shadow-sm">
                                                                <label class="fw-bold small mb-1">Price</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text bg-white border-end-0">₹</span>
                                                                    <input type="number" step="0.01" name="seller_cash_price" class="form-control border-start-0 ps-0"
                                                                        placeholder="Enter Price" value="<?= $vehicle['seller_cash_price'] ?? '' ?>">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div id="sellerOnlineBox" class="collapse <?= $sPayType == 'Online' ? 'show' : '' ?>" data-bs-parent="#sellerPayAccordion">
                                                            <div class="p-3 bg-white border rounded shadow-sm">
                                                                <label class="fw-bold small mb-2">Online Method</label>
                                                                <div class="d-flex flex-wrap gap-3 mb-3">
                                                                    <?php
                                                                    $sMethods = ['Google Pay', 'Paytm', 'PhonePe', 'BharatPe'];
                                                                    foreach ($sMethods as $sm):
                                                                    ?>
                                                                        <label class="form-check">
                                                                            <input type="radio" class="form-check-input" name="seller_online_method" value="<?= $sm ?>"
                                                                                <?= (isset($vehicle['seller_online_method']) && $vehicle['seller_online_method'] == $sm) ? 'checked' : '' ?>>
                                                                            <span class="form-check-label fw-bold"><?= $sm ?></span>
                                                                        </label>
                                                                    <?php endforeach; ?>
                                                                </div>

                                                                <input type="text" name="seller_online_transaction_id"
                                                                    class="form-control form-control-sm mb-3 text-uppercase"
                                                                    placeholder="Transaction / UPI Reference ID"
                                                                    value="<?= $vehicle['seller_online_transaction_id'] ?? '' ?>">

                                                                <label class="fw-bold small mb-1">Price</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text bg-white border-end-0">₹</span>
                                                                    <input type="number" step="0.01" name="seller_online_price"
                                                                        class="form-control border-start-0 ps-0"
                                                                        placeholder="Enter Price" value="<?= $vehicle['seller_online_price'] ?? '' ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label>Exchange Showroom Name</label>
                                                    <input type="text" name="exchange_showroom_name" class="form-control text-uppercase"
                                                        placeholder="Showroom Name" value="<?= $vehicle['exchange_showroom_name'] ?? '' ?>">
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <label>Staff Name</label>
                                                    <input type="text" name="staff_name" class="form-control text-uppercase"
                                                        placeholder="Staff Name" value="<?= $vehicle['staff_name'] ?? '' ?>">
                                                </div>
                                            </div>

                                            <div class="bg-light p-3 rounded-4 border">
                                                <label class="text-primary">Payment Calculation</label>
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <label class="small text-muted">Total Amount</label>
                                                        <input type="number" step="0.01" name="total_amount" class="form-control" placeholder="Total"
                                                            id="s_total" value="<?= $vehicle['total_amount'] ?? '' ?>">
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="small text-muted">Paid Amount</label>
                                                        <input type="number" step="0.01" name="paid_amount" class="form-control" placeholder="Paid"
                                                            id="s_paid" value="<?= $vehicle['paid_amount'] ?? '' ?>">
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="small text-muted">Due Amount</label>
                                                        <input type="number" step="0.01" name="due_amount" class="form-control bg-white fw-bold text-danger"
                                                            placeholder="Due" id="s_due" readonly
                                                            value="<?= $vehicle['due_amount'] ?? '' ?>">
                                                    </div>

                                                    <div class="col-12">
                                                        <input type="text" name="due_reason"
                                                            class="form-control mt-1 <?= empty($vehicle['due_amount']) || $vehicle['due_amount'] == 0 ? 'd-none' : '' ?>"
                                                            id="s_due_reason"
                                                            placeholder="Reason for due amount..."
                                                            value="<?= $vehicle['due_reason'] ?? '' ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- STEP 3: PURCHASER -->
                                    <div id="step-3" class="wizard-step d-none">
                                        <div class="card steps-id border-0 p-4 shadow-sm rounded-4">
                                            <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Purchaser Details</h6>

                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label>Date</label>
                                                    <input type="date" name="purchaser_date" class="form-control"
                                                        value="<?= $vehicle['purchaser_date'] ?? date('Y-m-d') ?>">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label>Purchaser Name</label>
                                                    <input type="text" name="purchaser_name" class="form-control text-uppercase"
                                                        value="<?= $vehicle['purchaser_name'] ?? '' ?>">
                                                </div>
                                                <div class="col-12">
                                                    <label>Address</label>
                                                    <textarea name="purchaser_address" class="form-control text-uppercase" rows="2"><?= $vehicle['purchaser_address'] ?? '' ?></textarea>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label>Bike Name</label>
                                                    <input type="text" name="purchaser_bike_name" class="form-control text-uppercase"
                                                        value="<?= $vehicle['purchaser_bike_name'] ?? '' ?>">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label>Vehicle No</label>
                                                    <input type="text" name="purchaser_vehicle_no" class="form-control fw-bold text-uppercase"
                                                        placeholder="WB 00 AA 0000"
                                                        value="<?= $vehicle['purchaser_vehicle_no'] ?? 'WB ' ?>">
                                                </div>
                                            </div>

                                            <div class="bg-light p-3 rounded-4 border mb-4">
                                                <label class="text-dark mb-3 d-block fw-bold">Purchaser Paper Payment Fees</label>

                                                <div class="row g-2 align-items-end mb-3">
                                                    <div class="col-12 col-md-4">
                                                        <label>Transfer Amount</label>
                                                        <input type="number" step="0.01" name="purchaser_transfer_amount" class="form-control" placeholder="Amount"
                                                            value="<?= $vehicle['purchaser_transfer_amount'] ?? '' ?>">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Date</label>
                                                        <input type="date" name="purchaser_transfer_date" class="form-control"
                                                            value="<?= $vehicle['purchaser_transfer_date'] ?? date('Y-m-d') ?>">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Status</label>
                                                        <select name="purchaser_transfer_status" class="form-select">
                                                            <option value="Paid" <?= ($vehicle['purchaser_transfer_status'] ?? '') == 'Paid' ? 'selected' : '' ?>>Paid</option>
                                                            <option value="Due" <?= ($vehicle['purchaser_transfer_status'] ?? '') == 'Due' ? 'selected' : '' ?>>Due</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row g-2 align-items-end mb-3">
                                                    <div class="col-12 col-md-4">
                                                        <label>HPA Amount</label>
                                                        <input type="number" step="0.01" name="purchaser_hpa_amount" class="form-control" placeholder="Amount"
                                                            value="<?= $vehicle['purchaser_hpa_amount'] ?? '' ?>">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Date</label>
                                                        <input type="date" name="purchaser_hpa_date" class="form-control"
                                                            value="<?= $vehicle['purchaser_hpa_date'] ?? date('Y-m-d') ?>">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Status</label>
                                                        <select name="purchaser_hpa_status" class="form-select">
                                                            <option value="Paid" <?= ($vehicle['purchaser_hpa_status'] ?? '') == 'Paid' ? 'selected' : '' ?>>Paid</option>
                                                            <option value="Due" <?= ($vehicle['purchaser_hpa_status'] ?? '') == 'Due' ? 'selected' : '' ?>>Due</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row g-2 align-items-end mb-3">
                                                    <div class="col-12 col-md-4">
                                                        <label>HP Amount</label>
                                                        <input type="number" step="0.01" name="purchaser_hp_amount" class="form-control" placeholder="Amount"
                                                            value="<?= $vehicle['purchaser_hp_amount'] ?? '' ?>">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Date</label>
                                                        <input type="date" name="purchaser_hp_date" class="form-control"
                                                            value="<?= $vehicle['purchaser_hp_date'] ?? date('Y-m-d') ?>">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Status</label>
                                                        <select name="purchaser_hp_status" class="form-select">
                                                            <option value="Paid" <?= ($vehicle['purchaser_hp_status'] ?? '') == 'Paid' ? 'selected' : '' ?>>Paid</option>
                                                            <option value="Due" <?= ($vehicle['purchaser_hp_status'] ?? '') == 'Due' ? 'selected' : '' ?>>Due</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Insurance Name</label>
                                                        <select name="purchaser_insurance_name" class="form-select text-uppercase">
                                                            <option value="">-- Select Insurance --</option>
                                                            <?php
                                                            $insurances = [
                                                                "Tata AIG Insurance",
                                                                "Bharti AXA",
                                                                "Bajaj Allianz",
                                                                "ICICI Lombard",
                                                                "IFFCO Tokio",
                                                                "National Insurance",
                                                                "New India Assurance",
                                                                "Oriental Insurance",
                                                                "United India Insurance",
                                                                "Reliance General Insurance",
                                                                "Royal Sundaram Insurance",
                                                                "Chola MS Insurance",
                                                                "HDFC ERGO",
                                                                "ECGC",
                                                                "Agriculture Insurance Company of India (AIC)",
                                                                "Star Health Insurance",
                                                                "Future Generali",
                                                                "Universal Sompo",
                                                                "Shriram General Insurance",
                                                                "Raheja QBE",
                                                                "SBI General Insurance",
                                                                "Niva Bupa Health Insurance",
                                                                "L&T Insurance",
                                                                "Care Health Insurance",
                                                                "Magma HDI",
                                                                "Liberty General Insurance",
                                                                "Manipal Cigna",
                                                                "Kotak General Insurance",
                                                                "Aditya Birla Capital Health Insurance",
                                                                "Digit Insurance"
                                                            ];

                                                            // 1. Get the saved value safely
                                                            $savedValue = trim($vehicle['purchaser_insurance_name'] ?? '');

                                                            foreach ($insurances as $ins) {
                                                                // 2. Case-Insensitive Comparison (Fixes the issue)
                                                                // This works even if DB has "TATA AIG" and list has "Tata Aig"
                                                                $isSelected = (strcasecmp($savedValue, $ins) == 0) ? 'selected' : '';

                                                                echo "<option value='$ins' $isSelected>$ins</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Payment Status</label>
                                                        <select name="purchaser_insurance_payment_status" class="form-control">
                                                            <option value="">-- Select Status --</option>
                                                            <option value="paid" <?= ($vehicle['purchaser_insurance_payment_status'] ?? '') == 'paid' ? 'selected' : '' ?>>Paid</option>
                                                            <option value="due" <?= ($vehicle['purchaser_insurance_payment_status'] ?? '') == 'due' ? 'selected' : '' ?>>Due</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Amount</label>
                                                        <input type="number" step="0.01" name="purchaser_insurance_amount" class="form-control" placeholder="Enter Amount"
                                                            value="<?= $vehicle['purchaser_insurance_amount'] ?? '' ?>">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Issue Date</label>
                                                        <input type="date" name="purchaser_insurance_issue_date" class="form-control" id="issueDate"
                                                            value="<?= $vehicle['purchaser_insurance_issue_date'] ?? date('Y-m-d') ?>">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Expiry Date</label>
                                                        <input type="date" name="purchaser_insurance_expiry_date" class="form-control" id="expiryDate"
                                                            value="<?= $vehicle['purchaser_insurance_expiry_date'] ?? '' ?>">
                                                    </div>

                                                    <!-- <div class="col-md-3 d-flex align-items-end">
                    <span class="fw-bold text-primary">Validity: <span id="expiryText">--</span></span>
                </div> -->
                                                </div>
                                            </div>

                                            <div class="bg-light p-3 rounded-4 border mb-3">
                                                <label class="mb-2 fw-bold">Price Breakdown</label>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-12">
                                                        <input type="number" step="0.01" name="purchaser_total" id="p_total" class="form-control" placeholder="Total"
                                                            value="<?= $vehicle['purchaser_total'] ?? '' ?>">
                                                    </div>
                                                    <div class="col-12">
                                                        <input type="number" step="0.01" name="purchaser_paid" id="p_paid" class="form-control" placeholder="Paid"
                                                            value="<?= $vehicle['purchaser_paid'] ?? '' ?>">
                                                    </div>
                                                    <div class="col-12">
                                                        <input type="number" step="0.01" name="purchaser_due" id="p_due" class="form-control bg-white fw-bold text-danger" placeholder="Due" readonly
                                                            value="<?= $vehicle['purchaser_due'] ?? '' ?>">
                                                    </div>
                                                </div>

                                                <?php $pMode = $vehicle['purchaser_payment_mode'] ?? 'Cash'; ?>
                                                <div class="d-flex gap-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="purchaser_payment_mode" value="Cash" id="pm_cash"
                                                            <?= $pMode == 'Cash' ? 'checked' : '' ?>
                                                            onclick="document.getElementById('hpa_sec').classList.add('d-none'); document.getElementById('cash_sec').classList.remove('d-none');">
                                                        <label class="fw-bold" for="pm_cash">Cash</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="purchaser_payment_mode" value="Finance" id="pm_fin"
                                                            <?= $pMode == 'Finance' ? 'checked' : '' ?>
                                                            onclick="document.getElementById('hpa_sec').classList.remove('d-none'); document.getElementById('cash_sec').classList.add('d-none');">
                                                        <label class="fw-bold" for="pm_fin">Finance</label>
                                                    </div>
                                                </div>

                                                <div id="cash_sec" class="<?= $pMode == 'Finance' ? 'd-none' : '' ?> border-top pt-2 mt-2">
                                                    <div class="row g-2">
                                                        <div class="col-12">
                                                            <label>Cash Amount</label>
                                                            <input type="number" step="0.01" name="purchaser_cash_amount" class="form-control" placeholder="Cash Amount"
                                                                value="<?= $vehicle['purchaser_cash_amount'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12">
                                                            <label>Mobile 1</label>
                                                            <input type="tel" name="purchaser_cash_mobile1" class="form-control" placeholder="Mobile 1"
                                                                value="<?= $vehicle['purchaser_cash_mobile1'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12">
                                                            <label>Mobile 2</label>
                                                            <input type="tel" name="purchaser_cash_mobile2" class="form-control" placeholder="Mobile 2"
                                                                value="<?= $vehicle['purchaser_cash_mobile2'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12">
                                                            <label>Mobile 3</label>
                                                            <input type="tel" name="purchaser_cash_mobile3" class="form-control" placeholder="Mobile 3"
                                                                value="<?= $vehicle['purchaser_cash_mobile3'] ?? '' ?>">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="hpa_sec" class="<?= $pMode == 'Cash' ? 'd-none' : '' ?> border-top pt-2 mt-2">
                                                    <div class="row g-2">
                                                        <div class="col-12">
                                                            <label>HPA With</label>
                                                            <input type="text" name="purchaser_fin_hpa_with" class="form-control text-uppercase" placeholder="Finance Company"
                                                                value="<?= $vehicle['purchaser_fin_hpa_with'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12">
                                                            <label>Disburse Amount</label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" name="purchaser_fin_disburse_amount" class="form-control" placeholder="Amt"
                                                                    value="<?= $vehicle['purchaser_fin_disburse_amount'] ?? '' ?>">
                                                                <select name="purchaser_fin_disburse_status" class="form-select" style="max-width:100px;">
                                                                    <option value="Paid" <?= ($vehicle['purchaser_fin_disburse_status'] ?? '') == 'Paid' ? 'selected' : '' ?>>Paid</option>
                                                                    <option value="Due" <?= ($vehicle['purchaser_fin_disburse_status'] ?? '') == 'Due' ? 'selected' : '' ?>>Due</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label>Mobile Number 1</label>
                                                            <input type="tel" name="purchaser_fin_mobile1" class="form-control" placeholder="Mobile 1"
                                                                value="<?= $vehicle['purchaser_fin_mobile1'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12">
                                                            <label>Mobile Number 2</label>
                                                            <input type="tel" name="purchaser_fin_mobile2" class="form-control" placeholder="Mobile 2"
                                                                value="<?= $vehicle['purchaser_fin_mobile2'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12">
                                                            <label>Mobile Number 3</label>
                                                            <input type="tel" name="purchaser_fin_mobile3" class="form-control" placeholder="Mobile 3"
                                                                value="<?= $vehicle['purchaser_fin_mobile3'] ?? '' ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="mb-2">Purchaser Documents</label>
                                                <div class="row g-2">
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Aadhar Front</span>
                                                            <?php if (!empty($vehicle['purchaser_doc_aadhar_front'])): ?>
                                                                <img src="<?= $vehicle['purchaser_doc_aadhar_front'] ?>" class="d-block w-100 h-100 object-fit-cover rounded">
                                                            <?php else: ?>
                                                                <img src="" class="d-none">
                                                            <?php endif; ?>
                                                            <input type="file" name="purchaser_doc_aadhar_front" accept="image/*" hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Aadhar Back</span>
                                                            <?php if (!empty($vehicle['purchaser_doc_aadhar_back'])): ?>
                                                                <img src="<?= $vehicle['purchaser_doc_aadhar_back'] ?>" class="d-block w-100 h-100 object-fit-cover rounded">
                                                            <?php else: ?>
                                                                <img src="" class="d-none">
                                                            <?php endif; ?>
                                                            <input type="file" name="purchaser_doc_aadhar_back" accept="image/*" hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Voter Front</span>
                                                            <?php if (!empty($vehicle['purchaser_doc_voter_front'])): ?>
                                                                <img src="<?= $vehicle['purchaser_doc_voter_front'] ?>" class="d-block w-100 h-100 object-fit-cover rounded">
                                                            <?php else: ?>
                                                                <img src="" class="d-none">
                                                            <?php endif; ?>
                                                            <input type="file" name="purchaser_doc_voter_front" accept="image/*" hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Voter Back</span>
                                                            <?php if (!empty($vehicle['purchaser_doc_voter_back'])): ?>
                                                                <img src="<?= $vehicle['purchaser_doc_voter_back'] ?>" class="d-block w-100 h-100 object-fit-cover rounded">
                                                            <?php else: ?>
                                                                <img src="" class="d-none">
                                                            <?php endif; ?>
                                                            <input type="file" name="purchaser_doc_voter_back" accept="image/*" hidden>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="purchaser_payment_all_paid" value="1" id="all_paid"
                                                    <?= ($vehicle['purchaser_payment_all_paid'] ?? 0) == 1 ? 'checked' : '' ?>>
                                                <label class="form-check-label fw-bold text-success" for="all_paid">Payment All Paid</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- STEP 4: TRANSFER -->
                                    <div id="step-4" class="wizard-step d-none">
                                        <div class="card steps-id border-0 shadow-sm rounded-4">
                                            <h6 class="fw-bold text-primary m-4 mb-3 text-uppercase ls-1">Ownership Transfer</h6>

                                            <div class="p-3 border rounded-4 mb-4">

                                                <div class="row g-3 mb-3">
                                                    <div class="col-12 col-md-4">
                                                        <label>Name Transfer</label>
                                                        <input type="text" name="ot_name_transfer" class="form-control text-uppercase"
                                                            placeholder="Enter Name" value="<?= $vehicle['ot_name_transfer'] ?? '' ?>">
                                                    </div>

                                                    <div class="col-12 col-md-4">
                                                        <label>Vehicle Number</label>
                                                        <input type="text" name="ot_vehicle_number" class="form-control fw-bold text-uppercase"
                                                            placeholder="WB 00 AA 0000" value="<?= $vehicle['ot_vehicle_number'] ?? 'WB ' ?>">
                                                    </div>

                                                    <div class="col-12 col-md-4">
                                                        <label>RTO Name</label>
                                                        <select name="ot_rto_name" class="form-select">
                                                            <option value="">-- Select RTO --</option>
                                                            <?php
                                                            $rtos = ["Bankura", "Bishnupur", "Durgapur", "Manbazar", "Suri", "Asansol", "Kalimpong"];
                                                            $saved_rto = $vehicle['ot_rto_name'] ?? '';

                                                            // Safety: If saved RTO is not in the list, show it anyway
                                                            if ($saved_rto != "" && !in_array($saved_rto, $rtos)) {
                                                                echo "<option value='$saved_rto' selected>$saved_rto</option>";
                                                            }

                                                            foreach ($rtos as $rto) {
                                                                $selected = ($saved_rto == $rto) ? 'selected' : '';
                                                                echo "<option value='$rto' $selected>$rto</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-4">
                                                    <div class="col-12 col-md-6">
                                                        <label>Vendor Name</label>
                                                        <input type="text" name="ot_vendor_name" class="form-control text-uppercase"
                                                            placeholder="Vendor Name" value="<?= $vehicle['ot_vendor_name'] ?? '' ?>">
                                                    </div>
                                                </div>

                                                <div class="bg-light p-3 rounded-4 border mb-4">
                                                    <label class="text-dark mb-3 d-block fw-bold">Vendor Payment & Fees</label>

                                                    <div class="row g-2 align-items-end mb-3">
                                                        <div class="col-12 col-md-4">
                                                            <label>Transfer Amount</label>
                                                            <input type="number" step="0.01" name="ot_transfer_amount" class="form-control" placeholder="Amount"
                                                                value="<?= $vehicle['ot_transfer_amount'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            <label>Date</label>
                                                            <input type="date" name="ot_transfer_date" class="form-control"
                                                                value="<?= $vehicle['ot_transfer_date'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            <label>Status</label>
                                                            <select name="ot_transfer_status" class="form-select">
                                                                <option value="Paid" <?= ($vehicle['ot_transfer_status'] ?? '') == 'Paid' ? 'selected' : '' ?>>Paid</option>
                                                                <option value="Due" <?= ($vehicle['ot_transfer_status'] ?? '') == 'Due' ? 'selected' : '' ?>>Due</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row g-2 align-items-end mb-3">
                                                        <div class="col-12 col-md-4">
                                                            <label>HPA Amount</label>
                                                            <input type="number" step="0.01" name="ot_hpa_amount" class="form-control" placeholder="Amount"
                                                                value="<?= $vehicle['ot_hpa_amount'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            <label>Date</label>
                                                            <input type="date" name="ot_hpa_date" class="form-control"
                                                                value="<?= $vehicle['ot_hpa_date'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            <label>Status</label>
                                                            <select name="ot_hpa_status" class="form-select">
                                                                <option value="Paid" <?= ($vehicle['ot_hpa_status'] ?? '') == 'Paid' ? 'selected' : '' ?>>Paid</option>
                                                                <option value="Due" <?= ($vehicle['ot_hpa_status'] ?? '') == 'Due' ? 'selected' : '' ?>>Due</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row g-2 align-items-end mb-3">
                                                        <div class="col-12 col-md-4">
                                                            <label>HP Amount</label>
                                                            <input type="number" step="0.01" name="ot_hp_amount" class="form-control" placeholder="Amount"
                                                                value="<?= $vehicle['ot_hp_amount'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            <label>Date</label>
                                                            <input type="date" name="ot_hp_date" class="form-control"
                                                                value="<?= $vehicle['ot_hp_date'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            <label>Status</label>
                                                            <select name="ot_hp_status" class="form-select">
                                                                <option value="Paid" <?= ($vehicle['ot_hp_status'] ?? '') == 'Paid' ? 'selected' : '' ?>>Paid</option>
                                                                <option value="Due" <?= ($vehicle['ot_hp_status'] ?? '') == 'Due' ? 'selected' : '' ?>>Due</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="bg-light p-3 rounded-4 border mb-4">
                                                    <label class="text-dark mb-3 d-block fw-bold">Insurance Details</label>
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="fw-bold">Insurance Name</label>
                                                            <input type="text" name="ot_insurance_name" class="form-control text-uppercase"
                                                                placeholder="Insurance Company" value="<?= $vehicle['ot_insurance_name'] ?? '' ?>">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="fw-bold">Payment Status</label>
                                                            <select name="ot_insurance_payment_status" class="form-select">
                                                                <option value="">-- Select --</option>
                                                                <option value="paid" <?= ($vehicle['ot_insurance_payment_status'] ?? '') == 'paid' ? 'selected' : '' ?>>Paid</option>
                                                                <option value="due" <?= ($vehicle['ot_insurance_payment_status'] ?? '') == 'due' ? 'selected' : '' ?>>Due</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="fw-bold">Amount</label>
                                                            <input type="number" step="0.01" name="ot_insurance_amount" class="form-control" placeholder="Amount"
                                                                value="<?= $vehicle['ot_insurance_amount'] ?? '' ?>">
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="fw-bold">Start Date</label>
                                                            <input type="date" name="ot_insurance_start_date" class="form-control"
                                                                value="<?= $vehicle['ot_insurance_start_date'] ?? '' ?>">
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="fw-bold">End Date</label>
                                                            <input type="date" name="ot_insurance_end_date" class="form-control"
                                                                value="<?= $vehicle['ot_insurance_end_date'] ?? '' ?>">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row g-4">
                                                    <div class="col-12 col-md-6">
                                                        <div class="border rounded-3 p-3">
                                                            <label class="form-label fw-bold">Purchaser Sign</label>
                                                            <div class="row g-3">
                                                                <div class="col-6">
                                                                    <label class="form-label small">Status</label>
                                                                    <select name="ot_purchaser_sign_status" class="form-select">
                                                                        <option value="No" <?= ($vehicle['ot_purchaser_sign_status'] ?? 'No') == 'No' ? 'selected' : '' ?>>No</option>
                                                                        <option value="Yes" <?= ($vehicle['ot_purchaser_sign_status'] ?? '') == 'Yes' ? 'selected' : '' ?>>Yes</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label small">Date</label>
                                                                    <input type="date" name="ot_purchaser_sign_date" class="form-control"
                                                                        value="<?= $vehicle['ot_purchaser_sign_date'] ?? '' ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-md-6">
                                                        <div class="border rounded-3 p-3">
                                                            <label class="form-label fw-bold">Seller Sign</label>
                                                            <div class="row g-3">
                                                                <div class="col-6">
                                                                    <label class="form-label small">Status</label>
                                                                    <select name="ot_seller_sign_status" class="form-select">
                                                                        <option value="No" <?= ($vehicle['ot_seller_sign_status'] ?? 'No') == 'No' ? 'selected' : '' ?>>No</option>
                                                                        <option value="Yes" <?= ($vehicle['ot_seller_sign_status'] ?? '') == 'Yes' ? 'selected' : '' ?>>Yes</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label small">Date</label>
                                                                    <input type="date" name="ot_seller_sign_date" class="form-control"
                                                                        value="<?= $vehicle['ot_seller_sign_date'] ?? '' ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>



                                    <!-- Form Footer Buttons -->
                                    <div class="modal-footer d-flex align-items-center mt-4 pt-3 border-top">
                                        <button type="button" class="btn btn-light border px-4 shadow-sm me-2" id="prevBtn"
                                            style="display:none;">
                                            <i class="bi bi-arrow-left me-2"></i> Back
                                        </button>

                                        <div class="ms-auto d-flex gap-2">
                                            <button type="submit" class="btn btn-success border fw-bold shadow-sm text-light"
                                                id="saveStepBtn">
                                                <i class="bi bi-floppy me-2"></i> Save
                                            </button>

                                            <button type="button" class="btn btn-primary px-4 shadow-lg" id="nextBtn">
                                                Next <i class="bi bi-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- END OF Vehicle Data Grid -->



    </div>
</body>

</html>