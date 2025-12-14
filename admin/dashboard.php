<?php
// Include DB connection
require "db.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}
// 1. Get ID (safely handle if missing)
$id = $_SESSION['id'] ?? 0;

// 2. Run Query (Standard MySQLi style)
$query = $conn->prepare("SELECT user_id, email, role FROM users WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$u = $query->get_result()->fetch_assoc(); // Data is now in the $u array
?>

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
</head>


<body>

    <!-- LOADER -->
    <div id="loader"
        style="position:fixed; inset:0; background:rgba(242,242,247,0.98); z-index:9999; display:flex; justify-content:center; align-items:center; flex-direction:column;">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
        <div class="mt-3 fw-bold text-secondary" style="letter-spacing: 1px;">LOADING...</div>
    </div>

    <!-- Universal Messages -->
    <?php include "universal_message.php" ?>

    <!-- DASHBOARD SECTION -->
    <section id="dashboard-section" class="pb-5">
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
                <!-- Desktop Logout Button -->
                <a href="logout.php" class="btn logout-btn d-none d-md-inline-block">
                    <i class="ph-bold ph-sign-out me-1"></i> Log Out
                </a>

                <!-- Mobile Logout Icon -->
                <a href="logout.php" class="btn logout-icon d-inline-flex d-md-none">
                    <i class="ph-bold ph-sign-out fs-5"></i>
                </a>
            </div>
        </nav>


        <!-- DASHBOARD CONTENT CONTAINER -->
        <div class="container mt-5 p-2">
            <div class="d-flex justify-content-between align-items-end mb-4 mb-md-5">
                <div>
                    <h2 class="fw-bold mb-1 fs-3 fs-md-2">Overview</h2>
                    <p class="text-secondary mb-0 fw-medium small">Management Dashboard</p>
                </div>
            </div>

            <!-- INDICATOR CARDS (RESPONSIVE GRID) -->
            <div class="row g-3 g-md-4">

                <!-- Add Vehicle -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="app-card p-4 d-flex align-items-center" onclick="openModal('dealModal')">
                        <div class="card-icon-box bg-blue-grad me-3 rounded-circle 
                d-flex justify-content-center align-items-center p-4">
                            <i class="ph-bold ph-plus fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 fs-5">Add Vehicle</h5>
                            <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Quick Start</span>
                            <p class="text-secondary mb-0 small">Easily add a new vehicle to your inventory.</p>
                        </div>
                    </div>
                </div>


                <!-- Inventory -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="app-card p-4 d-flex align-items-center" onclick="window.location.href='inventory.php'">
                        <div class="card-icon-box bg-indigo-grad me-3 rounded-circle 
                    d-flex justify-content-center align-items-center p-4">
                            <i class="ph-bold ph-car fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 fs-5">Inventory</h5>
                            <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Search &
                                Details</span>
                            <p class="text-secondary mb-0 small">Browse stock and vehicle stats.</p>
                        </div>
                    </div>
                </div>

                <!-- Leads -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="app-card p-4 d-flex align-items-center" onclick="openModal('leadsListModal')">
                        <div class="card-icon-box bg-pink-grad me-3 rounded-circle 
                    d-flex justify-content-center align-items-center p-4">
                            <i class="ph-bold ph-users-three fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 fs-5">Leads</h5>
                            <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Manage List</span>
                            <p class="text-secondary mb-0 small">View and track potential clients.</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Add -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="app-card p-4 d-flex align-items-center" onclick="openModal('leadModal')">
                        <div class="card-icon-box bg-purple-grad me-3 rounded-circle 
                    d-flex justify-content-center align-items-center p-4">
                            <i class="ph-bold ph-user-plus fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 fs-5">Quick Add</h5>
                            <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Capture Lead</span>
                            <p class="text-secondary mb-0 small">Fast entry for new prospects.</p>
                        </div>
                    </div>
                </div>

                <!-- Update Profile -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="app-card p-4 d-flex align-items-center" onclick="openModal('profileConfigModal')">

                        <!-- Icon -->
                        <div class="card-icon-box bg-blue-grad me-3 rounded-circle 
            d-flex justify-content-center align-items-center p-4">
                            <i class="ph-bold ph-user-gear fs-3"></i>
                        </div>

                        <!-- Text Section -->
                        <div>
                            <h5 class="fw-bold mb-0 fs-5">Update Profile</h5>
                            <span class="text-secondary small fw-bold text-uppercase d-block mb-1">
                                Configuration
                            </span>
                            <p class="text-secondary mb-0 small">
                                Manage and update your profile details.
                            </p>
                        </div>

                    </div>
                </div>

                <!-- Empty spaces to ensure alignment -->
                <div class="d-none d-lg-block col-lg-3 col-xl-3"></div>
                <div class="d-none d-lg-block col-lg-3 col-xl-3"></div>
            </div>
        </div>
    </section>



    <!-- ADD vechical form -->
    <div class="modal fade" id="dealModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header border-0 pt-4 pb-2 d-flex align-items-center justify-content-between">

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

                    <!-- Right: Bootstrap Close Button (Perfectly Centered) -->
                    <button type="button" class="btn-close ms-3" data-bs-dismiss="modal"></button>

                </div>

                <?php
                // 1. GET PARAMETERS (Keep variable as integer)
                $open_modal = isset($_GET['modal']) && $_GET['modal'] == 'open';
                $current_step = isset($_GET['step']) ? (int) $_GET['step'] : 1;
                $row_id = isset($_GET['id']) ? $_GET['id'] : '';

                // 2. DEFINE STEPS (Rename variable to $wizard_steps)
                $wizard_steps = [
                    1 => 'Vehicle',
                    2 => 'Seller',
                    3 => 'Purchaser',
                    4 => 'Transfer'
                ];
                ?>

                <div class="wizard-nav d-flex justify-content-between flex-wrap gap-3 mt-3 px-2 text-center">
                    <?php foreach ($wizard_steps as $step_key => $label): ?>
                        <div class="step-item d-flex flex-column align-items-center flex-fill <?= ($step_key == $current_step) ? 'active' : '' ?> <?= ($step_key < $current_step) ? 'completed' : '' ?>"
                            data-step="<?= $step_key ?>">

                            <div class="step-circle d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 40px; height: 40px; transition: all 0.3s ease;">
                                <?= $step_key ?>
                            </div>
                            <div class="step-label small mt-1">
                                <?= $label ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="modal-body">
                    <form action="vehicle_form.php" id="dealForm" method="POST" class="app-form"
                        enctype="multipart/form-data" novalidate>

                        <input type="hidden" name="row_id" value="<?php echo $row_id; ?>">

                        <input type="hidden" name="current_step" value="<?php echo $current_step; ?>">

                        
                        <!-- STEP 1: VEHICLE -->
                        <div id="step-1" class="wizard-step">
                            <div class="card steps-id p-4 border-0 shadow-sm position-relative sold-wrapper rounded-4">
                                <div class="sold-stamp">SOLD OUT</div>
                                <div class="sold-overlay"></div>
                                <div>
                                    <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Vehicle Details</h6>
                                    <label class="mb-2">Vehicle Photos</label>
                                    <div class="row g-3 mb-4">
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <i class="ph-bold ph-camera fs-3 text-secondary"></i>
                                                <img src="">
                                                <input type="file" name="photo1" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <i class="ph-bold ph-camera fs-3 text-secondary"></i>
                                                <img src="">
                                                <input type="file" name="photo2" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <i class="ph-bold ph-camera fs-3 text-secondary"></i>
                                                <img src="">
                                                <input type="file" name="photo3" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <i class="ph-bold ph-camera fs-3 text-secondary"></i>
                                                <img src="">
                                                <input type="file" name="photo4" accept="image/*" hidden>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-12 col-md-6">
                                            <label for="vehicleType" class="form-label">Vehicle Type</label>
                                            <select id="vehicleType" name="vehicle_type" class="form-select fw-bold">
                                                <option selected disabled>Choose Vehicle Type</option>
                                                <option>Scooters</option>
                                                <option>Mopeds</option>
                                                <option>Dirt / Off-road Bikes</option>
                                                <option>Electric Bikes</option>
                                                <option>Cruiser Bikes</option>
                                                <option>Sport Bikes</option>
                                                <option>Touring Bikes</option>
                                                <option>Adventure / Dual-Sport Bikes</option>
                                                <option>Naked / Standard Bikes</option>
                                                <option>Cafe Racers</option>
                                                <option>Bobbers</option>
                                                <option>Choppers</option>
                                                <option>Pocket Bikes / Mini Bikes</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="fw-bold">Name</label>
                                            <input type="text" id="nameField" name="name" class="form-control"
                                                placeholder="Enter Name">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label>Vehicle Number</label>
                                            <input type="text" name="vehicle_number"
                                                class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000" value="WB ">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label>Register Date</label>
                                            <input type="date" name="register_date" class="form-control"
                                                value="2025-11-26">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Owner Serial</label>
                                            <select name="owner_serial" class="form-select">
                                                <option>1st</option>
                                                <option>2nd</option>
                                                <option>3rd</option>
                                                <option>4th</option>
                                                <option>5th</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Chassis Number</label>
                                            <input type="text" name="chassis_number"
                                                class="form-control text-uppercase">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Engine Number</label>
                                            <input type="text" name="engine_number" class="form-control text-uppercase">
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-12 col-md-6">
                                            <label class="fw-bold mb-2">Payment Type</label>
                                            <div class="d-flex gap-2 mb-3">
                                                <input type="radio" class="btn-check" name="payment_type" id="sp_cash"
                                                    value="cash" checked data-bs-toggle="collapse"
                                                    data-bs-target="#cashBox" aria-controls="cashBox">
                                                <label class="btn btn-outline-success" for="sp_cash">Cash</label>

                                                <input type="radio" class="btn-check" name="payment_type" id="sp_online"
                                                    value="online" data-bs-toggle="collapse" data-bs-target="#onlineBox"
                                                    aria-controls="onlineBox">
                                                <label class="btn btn-outline-primary" for="sp_online">Online</label>
                                            </div>

                                            <div id="payBoxes">
                                                <div id="cashBox" class="collapse show" data-bs-parent="#payBoxes">
                                                    <div class="p-3 mb-3 bg-white rounded-3 border shadow-sm">
                                                        <label class="fw-bold small mb-1">Bike Price</label>
                                                        <input type="number" name="cash_price"
                                                            class="form-control form-control-sm mb-3"
                                                            placeholder="Enter Amount">
                                                    </div>
                                                </div>

                                                <div id="onlineBox" class="collapse" data-bs-parent="#payBoxes">
                                                    <div class="p-3 mb-3 bg-white rounded-3 border shadow-sm">
                                                        <label class="fw-bold small mb-2">Select Online Method</label>
                                                        <div class="d-flex flex-wrap gap-3 mb-2">
                                                            <div class="form-check">
                                                                <input type="radio" class="form-check-input"
                                                                    name="online_method" id="om_gpay" value="gpay">
                                                                <label class="form-check-label small fw-bold"
                                                                    for="om_gpay">Google
                                                                    Pay</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio" class="form-check-input"
                                                                    name="online_method" id="om_paytm" value="paytm">
                                                                <label class="form-check-label small fw-bold"
                                                                    for="om_paytm">Paytm</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio" class="form-check-input"
                                                                    name="online_method" id="om_phonepe"
                                                                    value="phonepe">
                                                                <label class="form-check-label small fw-bold"
                                                                    for="om_phonepe">PhonePe</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio" class="form-check-input"
                                                                    name="online_method" id="om_bharatpe"
                                                                    value="bharatpe">
                                                                <label class="form-check-label small fw-bold"
                                                                    for="om_bharatpe">BharatPe</label>
                                                            </div>
                                                        </div>

                                                        <input type="text" name="upi_ref"
                                                            class="form-control form-control-sm mb-3 text-uppercase"
                                                            placeholder="Transaction / UPI Reference ID">

                                                        <label class="fw-bold small mb-1">Bike Price</label>
                                                        <div class="input-group">
                                                            <span
                                                                class="input-group-text bg-white border-end-0">₹</span>
                                                            <input type="number" name="online_price"
                                                                class="form-control border-start-0 ps-0"
                                                                placeholder="Enter Price" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-3 rounded-4 bg-light border">
                                        <label>Police Challan</label>
                                        <div class="d-flex gap-3 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="police_challan"
                                                    value="no" checked data-bs-toggle="collapse"
                                                    data-bs-target="#challan-section">
                                                <label class="form-check-label fw-bold">No</label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="police_challan"
                                                    value="yes" data-bs-toggle="collapse"
                                                    data-bs-target="#challan-section">
                                                <label class="form-check-label fw-bold">Yes</label>
                                            </div>
                                        </div>

                                        <div class="collapse mt-3" id="challan-section">
                                            <!-- Challan 1 -->
                                            <div class="border rounded p-2 mb-2 bg-white">
                                                <label class="fw-bold small">Challan 1</label>
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <input type="text" name="challan1_number"
                                                            class="form-control text-uppercase"
                                                            placeholder="Challan Number">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" name="challan1_amount" class="form-control"
                                                            placeholder="Amount">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="btn-group w-100 btn-group-sm">
                                                            <input type="radio" class="btn-check" name="challan1_status"
                                                                id="pen1" value="pending" checked>
                                                            <label class="btn btn-outline-danger"
                                                                for="pen1">Pending</label>

                                                            <input type="radio" class="btn-check" name="challan1_status"
                                                                id="paid1" value="paid">
                                                            <label class="btn btn-outline-success"
                                                                for="paid1">Paid</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Challan 2 -->
                                            <div class="border rounded p-2 mb-2 bg-white">
                                                <label class="fw-bold small">Challan 2</label>
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <input type="text" name="challan2_number"
                                                            class="form-control text-uppercase"
                                                            placeholder="Challan Number">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" name="challan2_amount" class="form-control"
                                                            placeholder="Amount">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="btn-group w-100 btn-group-sm">
                                                            <input type="radio" class="btn-check" name="challan2_status"
                                                                id="pen2" value="pending" checked>
                                                            <label class="btn btn-outline-danger"
                                                                for="pen2">Pending</label>

                                                            <input type="radio" class="btn-check" name="challan2_status"
                                                                id="paid2" value="paid">
                                                            <label class="btn btn-outline-success"
                                                                for="paid2">Paid</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Challan 3 -->
                                            <div class="border rounded p-2 mb-2 bg-white">
                                                <label class="fw-bold small">Challan 3</label>
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <input type="text" name="challan3_number"
                                                            class="form-control text-uppercase"
                                                            placeholder="Challan Number">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" name="challan3_amount" class="form-control"
                                                            placeholder="Amount">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="btn-group w-100 btn-group-sm">
                                                            <input type="radio" class="btn-check" name="challan3_status"
                                                                id="pen3" value="pending" checked>
                                                            <label class="btn btn-outline-danger"
                                                                for="pen3">Pending</label>

                                                            <input type="radio" class="btn-check" name="challan3_status"
                                                                id="paid3" value="paid">
                                                            <label class="btn btn-outline-success"
                                                                for="paid3">Paid</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                                <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between">
                                    <label class="form-check-label fw-bold text-danger mb-0" for="soldToggle">Mark as
                                        Sold Out</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="soldToggle" name="sold_out"
                                            style="width: 3em; height: 1.5em;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 2: SELLER -->
                        <div id="step-2" class="wizard-step d-none">
                            <div class="card steps-id border-0 p-4 shadow-sm rounded-4">
                                <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Seller Details</h6>

                                <!-- SELLER BASIC INFO -->
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-4">
                                        <label>Date</label>
                                        <input type="date" name="seller_date" class="form-control">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label>Vehicle No</label>
                                        <input type="text" name="seller_vehicle_number"
                                            class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000"
                                            value="WB ">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label>Bike Name</label>
                                        <input type="text" name="bike_name" class="form-control text-uppercase">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label>Chassis No</label>
                                        <input type="text" name="chassis_number" class="form-control text-uppercase">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label>Engine No</label>
                                        <input type="text" name="engine_number" class="form-control text-uppercase">
                                    </div>

                                    <div class="col-12">
                                        <label>Seller Name</label>
                                        <input type="text" name="seller_name" class="form-control text-uppercase">
                                    </div>

                                    <div class="col-12">
                                        <label>Address</label>
                                        <textarea name="seller_address" class="form-control text-uppercase"
                                            rows="2"></textarea>
                                    </div>
                                </div>

                                <!-- MOBILE NUMBERS -->
                                <label class="mb-2">Mobile Numbers</label>
                                <div class="row g-2 mb-3">
                                    <div class="col-12"><input type="tel" name="mobile1" class="form-control"
                                            placeholder="Mob 1"></div>
                                    <div class="col-12"><input type="tel" name="mobile2" class="form-control"
                                            placeholder="Mob 2"></div>
                                    <div class="col-12"><input type="tel" name="mobile3" class="form-control"
                                            placeholder="Mob 3"></div>
                                </div>

                                <!-- DOCUMENT UPLOADS -->
                                <div class="mb-3">
                                    <label class="mb-2">Purchaser Documents</label>
                                    <div class="row g-2">
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Aadhar Front</span>
                                                <img src="">
                                                <input type="file" name="aadhar_front" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Aadhar Back</span>
                                                <img src="">
                                                <input type="file" name="aadhar_back" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Voter Front</span>
                                                <img src="">
                                                <input type="file" name="voter_front" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Voter Back</span>
                                                <img src="">
                                                <input type="file" name="voter_back" accept="image/*" hidden>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PAPERS RECEIVED -->
                                <div class="bg-light p-3 rounded-4 border mb-3">
                                    <label class="mb-2 fw-bold">Papers Received</label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="papers_rc" class="form-check-input" id="pr_rc"
                                                data-bs-toggle="collapse" data-bs-target="#rcUploadBox">
                                            <label class="fw-bold" for="pr_rc">RC</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="papers_tax" class="form-check-input"
                                                id="pr_tax">
                                            <label class="fw-bold" for="pr_tax">Tax Token</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="papers_insurance" class="form-check-input"
                                                id="pr_ins">
                                            <label class="fw-bold" for="pr_ins">Insurance</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="papers_pucc" class="form-check-input"
                                                id="pr_puc">
                                            <label class="fw-bold" for="pr_puc">PUCC</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="papers_noc" class="form-check-input"
                                                id="pr_noc" data-bs-toggle="collapse" data-bs-target="#nocUploadBox">
                                            <label class="fw-bold" for="pr_noc">NOC</label>
                                        </div>
                                    </div>

                                    <!-- RC UPLOAD -->
                                    <div class="collapse mt-3" id="rcUploadBox">
                                        <label class="fw-bold small">RC Upload</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="border rounded p-2 text-center bg-white">
                                                    <small class="fw-bold d-block mb-1" style="font-size:10px">RC
                                                        FRONT</small>
                                                    <input type="file" name="rc_front"
                                                        class="form-control form-control-sm mt-1">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="border rounded p-2 text-center bg-white">
                                                    <small class="fw-bold d-block mb-1" style="font-size:10px">RC
                                                        BACK</small>
                                                    <input type="file" name="rc_back"
                                                        class="form-control form-control-sm mt-1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- NOC UPLOAD -->
                                    <div class="collapse mt-3" id="nocUploadBox">
                                        <label class="fw-bold small">NOC Status</label>
                                        <div class="d-flex justify-content-center">
                                            <div class="btn-group w-75 btn-group-sm mb-3 mx-auto" role="group">
                                                <input type="radio" name="noc_payment" class="btn-check" id="noc_paid"
                                                    value="paid" checked>
                                                <label class="btn btn-outline-success" for="noc_paid">Paid</label>

                                                <input type="radio" name="noc_payment" class="btn-check" id="noc_due"
                                                    value="due">
                                                <label class="btn btn-outline-danger" for="noc_due">Due</label>
                                            </div>
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="border rounded small-box text-center p-2">
                                                    <span class="small text-muted fw-bold">NOC Front</span>
                                                    <input type="file" name="noc_front"
                                                        class="form-control form-control-sm mt-1">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="border rounded small-box text-center p-2">
                                                    <span class="small text-muted fw-bold">NOC Back</span>
                                                    <input type="file" name="noc_back"
                                                        class="form-control form-control-sm mt-1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PAYMENT SECTION -->
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-6">
                                        <label class="fw-bold mb-2">Payment Type</label>
                                        <div class="d-flex gap-2 mb-3">
                                            <input type="radio" name="pay_type" class="btn-check" id="pay_cash"
                                                value="cash" checked data-bs-toggle="collapse"
                                                data-bs-target="#cashBox">
                                            <label class="btn btn-outline-success" for="pay_cash">Cash</label>

                                            <input type="radio" name="pay_type" class="btn-check" id="pay_online"
                                                value="online" data-bs-toggle="collapse" data-bs-target="#onlineBox">
                                            <label class="btn btn-outline-primary" for="pay_online">Online</label>
                                        </div>

                                        <div id="payAccordion">
                                            <div id="cashBox" class="collapse show" data-bs-parent="#payAccordion">
                                                <div class="p-3 bg-white border rounded shadow-sm">
                                                    <label class="fw-bold small mb-1">Price</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white border-end-0">₹</span>
                                                        <input type="number" name="cash_price"
                                                            class="form-control border-start-0 ps-0"
                                                            placeholder="Enter Price">
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="onlineBox" class="collapse" data-bs-parent="#payAccordion">
                                                <div class="p-3 bg-white border rounded shadow-sm">
                                                    <label class="fw-bold small mb-2">Online Method</label>
                                                    <div class="d-flex flex-wrap gap-3 mb-3">
                                                        <label class="form-check">
                                                            <input type="radio" name="online_method"
                                                                class="form-check-input" value="gpay">
                                                            <span class="form-check-label fw-bold">Google Pay</span>
                                                        </label>
                                                        <label class="form-check">
                                                            <input type="radio" name="online_method"
                                                                class="form-check-input" value="paytm">
                                                            <span class="form-check-label fw-bold">Paytm</span>
                                                        </label>
                                                        <label class="form-check">
                                                            <input type="radio" name="online_method"
                                                                class="form-check-input" value="phonepe">
                                                            <span class="form-check-label fw-bold">PhonePe</span>
                                                        </label>
                                                        <label class="form-check">
                                                            <input type="radio" name="online_method"
                                                                class="form-check-input" value="bharatpe">
                                                            <span class="form-check-label fw-bold">BharatPe</span>
                                                        </label>
                                                    </div>

                                                    <input type="text" name="upi_ref"
                                                        class="form-control form-control-sm mb-3 text-uppercase"
                                                        placeholder="Transaction / UPI Reference ID">

                                                    <label class="fw-bold small mb-1">Price</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white border-end-0">₹</span>
                                                        <input type="number" name="online_price"
                                                            class="form-control border-start-0 ps-0"
                                                            placeholder="Enter Price">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- OTHER FIELDS -->
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-6">
                                        <label>Exchange Showroom Name</label>
                                        <input type="text" name="showroom_name" class="form-control text-uppercase"
                                            placeholder="Showroom Name">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label>Staff Name</label>
                                        <input type="text" name="staff_name" class="form-control text-uppercase"
                                            placeholder="Staff Name">
                                    </div>
                                </div>

                                <!-- PAYMENT CALCULATION -->
                                <div class="bg-light p-3 rounded-4 border">
                                    <label class="text-primary">Payment Calculation</label>
                                    <div class="row g-2">
                                        <div class="col-12"><input type="number" name="total_amount"
                                                class="form-control" placeholder="Total" id="s_total"></div>
                                        <div class="col-12"><input type="number" name="paid_amount" class="form-control"
                                                placeholder="Paid" id="s_paid"></div>
                                        <div class="col-12"><input type="number" name="due_amount"
                                                class="form-control bg-white fw-bold text-danger" placeholder="Due"
                                                id="s_due" readonly></div>
                                        <div class="col-12"><input type="text" name="due_reason"
                                                class="form-control d-none mt-1" id="s_due_reason"
                                                placeholder="Reason for due amount..."></div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <!-- STEP 3: PURCHASER -->
                        <div id="step-3" class="wizard-step d-none">
                            <div class="card steps-id border-0 p-4 shadow-sm rounded-4">
                                <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Purchaser Details</h6>

                                <!-- Purchaser Basic Info -->
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-6">
                                        <label>Date</label>
                                        <input type="date" name="purchaser_date" class="form-control"
                                            value="2025-11-26">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label>Purchaser Name</label>
                                        <input type="text" name="purchaser_name" class="form-control text-uppercase">
                                    </div>
                                    <div class="col-12">
                                        <label>Address</label>
                                        <textarea name="purchaser_address" class="form-control text-uppercase"
                                            rows="2"></textarea>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label>Bike Name</label>
                                        <input type="text" name="purchaser_bike_name"
                                            class="form-control text-uppercase">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label>Vehicle No</label>
                                        <input type="text" name="purchaser_vehicle_no"
                                            class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000"
                                            value="WB ">
                                    </div>
                                </div>

                                <!-- Purchaser Paper Payment Fees -->
                                <div class="bg-light p-3 rounded-4 border mb-4">
                                    <label class="text-dark mb-3 d-block">Purchaser Paper Payment Fees</label>

                                    <!-- Name Transfer -->
                                    <div class="row g-2 align-items-end mb-3">
                                        <div class="col-12 col-md-4">
                                            <label>Transfer Amount</label>
                                            <input type="number" name="transfer_amount" class="form-control"
                                                placeholder="Amount">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Date</label>
                                            <input type="date" name="transfer_date" class="form-control"
                                                value="2025-11-26">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Status</label>
                                            <select name="transfer_status" class="form-select">
                                                <option value="paid">Paid</option>
                                                <option value="due">Due</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- HPA -->
                                    <div class="row g-2 align-items-end mb-3">
                                        <div class="col-12 col-md-4">
                                            <label>HPA</label>
                                            <input type="number" name="hpa_amount" class="form-control"
                                                placeholder="Amount">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Date</label>
                                            <input type="date" name="hpa_date" class="form-control" value="2025-11-26">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Status</label>
                                            <select name="hpa_status" class="form-select">
                                                <option value="paid">Paid</option>
                                                <option value="due">Due</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- HP -->
                                    <div class="row g-2 align-items-end mb-3">
                                        <div class="col-12 col-md-4">
                                            <label>HP</label>
                                            <input type="number" name="hp_amount" class="form-control"
                                                placeholder="Amount">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Date</label>
                                            <input type="date" name="hp_date" class="form-control" value="2025-11-26">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Status</label>
                                            <select name="hp_status" class="form-select">
                                                <option value="paid">Paid</option>
                                                <option value="due">Due</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Insurance -->
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-3">
                                            <label class="fw-bold">Insurance Name</label>
                                            <select name="insurance_name" class="form-control text-uppercase" required>
                                                <option value="">-- Select Insurance --</option>
                                                <option value="Tata AIG Insurance">Tata AIG Insurance</option>
                                                <option value="Bharti AXA">Bharti AXA</option>
                                                <option value="Bajaj Allianz">Bajaj Allianz</option>
                                                <option value="ICICI Lombard">ICICI Lombard</option>
                                                <option value="IFFCO Tokio">IFFCO Tokio</option>
                                                <option value="National Insurance">National Insurance</option>
                                                <option value="New India Assurance">New India Assurance</option>
                                                <option value="Oriental Insurance">Oriental Insurance</option>
                                                <option value="United India Insurance">United India Insurance</option>
                                                <option value="Reliance General Insurance">Reliance General Insurance
                                                </option>
                                                <option value="Royal Sundaram Insurance">Royal Sundaram Insurance
                                                </option>
                                                <option value="Chola MS Insurance">Chola MS Insurance</option>
                                                <option value="HDFC ERGO">HDFC ERGO</option>
                                                <option value="ECGC">ECGC</option>
                                                <option value="Agriculture Insurance Company of India (AIC)">Agriculture
                                                    Insurance Company of India (AIC)</option>
                                                <option value="Star Health Insurance">Star Health Insurance</option>
                                                <option value="Future Generali">Future Generali</option>
                                                <option value="Universal Sompo">Universal Sompo</option>
                                                <option value="Shriram General Insurance">Shriram General Insurance
                                                </option>
                                                <option value="Raheja QBE">Raheja QBE</option>
                                                <option value="SBI General Insurance">SBI General Insurance</option>
                                                <option value="Niva Bupa Health Insurance">Niva Bupa Health Insurance
                                                </option>
                                                <option value="L&T Insurance">L&T Insurance</option>
                                                <option value="Care Health Insurance">Care Health Insurance</option>
                                                <option value="Magma HDI">Magma HDI</option>
                                                <option value="Liberty General Insurance">Liberty General Insurance
                                                </option>
                                                <option value="Manipal Cigna">Manipal Cigna</option>
                                                <option value="Kotak General Insurance">Kotak General Insurance</option>
                                                <option value="Aditya Birla Capital Health Insurance">Aditya Birla
                                                    Capital Health Insurance</option>
                                                <option value="Digit Insurance">Digit Insurance</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="fw-bold">Payment Status</label>
                                            <select name="insurance_status" class="form-control" required>
                                                <option value="">-- Select Status --</option>
                                                <option value="paid">Paid</option>
                                                <option value="due">Due</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="fw-bold">Amount</label>
                                            <input type="number" name="insurance_amount" class="form-control"
                                                placeholder="Enter Amount" required>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="fw-bold">Issue Date</label>
                                            <input type="date" name="insurance_issue_date" class="form-control"
                                                value="2025-11-26" required>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="fw-bold">Expiry Date</label>
                                            <input type="date" name="insurance_expiry_date" class="form-control"
                                                readonly required>
                                        </div>

                                        <span class="fw-bold text-primary">Validity:<span
                                                id="expiryText">--</span></span>
                                    </div>
                                </div>

                                <!-- Price Breakdown -->
                                <div class="bg-light p-3 rounded-4 border mb-3">
                                    <label class="mb-2">Price Breakdown</label>
                                    <div class="row g-2 mb-3">
                                        <div class="col-12">
                                            <input type="number" name="total_price" id="p_total" class="form-control"
                                                placeholder="Total">
                                        </div>
                                        <div class="col-12">
                                            <input type="number" name="paid_price" id="p_paid" class="form-control"
                                                placeholder="Paid">
                                        </div>
                                        <div class="col-12">
                                            <input type="number" name="due_price" id="p_due"
                                                class="form-control bg-white" placeholder="Due" readonly>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_mode"
                                                id="rad_cash" value="cash" checked>
                                            <label class="fw-bold" for="rad_cash" data-bs-toggle="collapse"
                                                data-bs-target="#sec_cash" role="button">
                                                Cash
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_mode"
                                                id="rad_fin" value="finance">
                                            <label class="fw-bold" for="rad_fin" data-bs-toggle="collapse"
                                                data-bs-target="#sec_finance" role="button">
                                                Finance
                                            </label>
                                        </div>
                                    </div>

                                    <div id="payment_details_group">
                                        <div id="sec_cash" class="collapse show border-top pt-2 mt-2"
                                            data-bs-parent="#payment_details_group">
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <label>Amount</label>
                                                    <input type="number" name="cash_amount" class="form-control"
                                                        placeholder="Enter Amount">
                                                </div>
                                                <div class="col-12">
                                                    <label>Mobile Number 1</label>
                                                    <input type="tel" name="cash_mobile1" class="form-control"
                                                        placeholder="Enter Mobile Number">
                                                </div>
                                                <div class="col-12">
                                                    <label>Mobile Number 2</label>
                                                    <input type="tel" name="cash_mobile2" class="form-control"
                                                        placeholder="Enter Mobile Number">
                                                </div>
                                                <div class="col-12">
                                                    <label>Mobile Number 3</label>
                                                    <input type="tel" name="cash_mobile3" class="form-control"
                                                        placeholder="Enter Mobile Number">
                                                </div>
                                            </div>
                                        </div>

                                        <div id="sec_finance" class="collapse border-top pt-2 mt-2"
                                            data-bs-parent="#payment_details_group">
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <label>HPA With</label>
                                                    <input type="text" name="finance_company"
                                                        class="form-control text-uppercase"
                                                        placeholder="Finance Company">
                                                </div>
                                                <div class="col-12">
                                                    <label>Disburse Amount</label>
                                                    <div class="input-group">
                                                        <input type="number" name="finance_disburse_amount"
                                                            class="form-control" placeholder="Amt">
                                                        <select name="finance_disburse_status" class="form-select"
                                                            style="max-width:100px;">
                                                            <option value="paid">Paid</option>
                                                            <option value="due">Due</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label>Mobile Number 1</label>
                                                    <input type="tel" name="finance_mobile1" class="form-control"
                                                        placeholder="Mobile 1">
                                                </div>
                                                <div class="col-12">
                                                    <label>Mobile Number 2</label>
                                                    <input type="tel" name="finance_mobile2" class="form-control"
                                                        placeholder="Mobile 2">
                                                </div>
                                                <div class="col-12">
                                                    <label>Mobile Number 3</label>
                                                    <input type="tel" name="finance_mobile3" class="form-control"
                                                        placeholder="Mobile 3">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Purchaser Documents -->
                                <div class="mb-3">
                                    <label class="mb-2">Purchaser Documents</label>
                                    <div class="row g-2">
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Aadhar Front</span>
                                                <img src="">
                                                <input type="file" name="doc_aadhar_front" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Aadhar Back</span>
                                                <img src="">
                                                <input type="file" name="doc_aadhar_back" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Voter Front</span>
                                                <img src="">
                                                <input type="file" name="doc_voter_front" accept="image/*" hidden>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="photo-upload-box">
                                                <span class="small text-muted fw-bold">Voter Back</span>
                                                <img src="">
                                                <input type="file" name="doc_voter_back" accept="image/*" hidden>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="all_paid" id="all_paid">
                                    <label class="form-check-label fw-bold text-success" for="all_paid">Payment All
                                        Paid</label>
                                </div>
                            </div>

                        </div>

                        <!-- STEP 4: TRANSFER -->
                        <div id="step-4" class="wizard-step d-none">
                            <div class="card steps-id border-0 shadow-sm rounded-4">
                                <h6 class="fw-bold text-primary m-4 mb-3 text-uppercase ls-1">Ownership Transfer</h6>

                                <div class="p-3 border rounded-4 mb-4">
                                    <!-- Name Transfer -->
                                    <div class="row g-3 mb-3">
                                        <div class="col-12 col-md-4">
                                            <label>Name Transfer</label>
                                            <input type="text" class="form-control text-uppercase"
                                                placeholder="Enter Name" name="ot_name_transfer">
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <label>Vehicle Number</label>
                                            <input type="text" class="form-control fw-bold text-uppercase"
                                                placeholder="WB 00 AA 0000" value="WB " name="ot_vehicle_number">
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <label>RTO Name</label>
                                            <select class="form-select" name="ot_rto_name">
                                                <option>Bankura</option>
                                                <option>Bishnupur</option>
                                                <option>Durgapur</option>
                                                <option>Manbazar</option>
                                                <option>Suri</option>
                                                <option>Asansol</option>
                                                <option>Kalimpong</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Vendor Name -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-12 col-md-6">
                                            <label>Vendor Name</label>
                                            <input type="text" class="form-control text-uppercase"
                                                placeholder="Vendor Name" name="ot_vendor_name">
                                        </div>
                                    </div>

                                    <!-- Vendor Payments Section -->
                                    <div class="bg-light p-3 rounded-4 border mb-4">
                                        <label class="text-dark mb-3 d-block">Vendor Payment</label>

                                        <!-- Transfer Payment -->
                                        <div class="row g-2 align-items-end mb-3">
                                            <div class="col-12 col-md-4">
                                                <label>Transfer Amount</label>
                                                <input type="number" class="form-control" placeholder="Amount"
                                                    name="ot_transfer_amount">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Date</label>
                                                <input type="date" class="form-control" value="2025-11-26"
                                                    name="ot_transfer_date">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Status</label>
                                                <select class="form-select" name="ot_transfer_status">
                                                    <option>Paid</option>
                                                    <option>Due</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- HPA -->
                                        <div class="row g-2 align-items-end mb-3">
                                            <div class="col-12 col-md-4">
                                                <label>HPA</label>
                                                <input type="number" class="form-control" placeholder="Amount"
                                                    name="ot_hpa_amount">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Date</label>
                                                <input type="date" class="form-control" value="2025-11-26"
                                                    name="ot_hpa_date">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Status</label>
                                                <select class="form-select" name="ot_hpa_status">
                                                    <option>Paid</option>
                                                    <option>Due</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- HP -->
                                        <div class="row g-2 align-items-end mb-3">
                                            <div class="col-12 col-md-4">
                                                <label>HP</label>
                                                <input type="number" class="form-control" placeholder="Amount"
                                                    name="ot_hp_amount">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Date</label>
                                                <input type="date" class="form-control" value="2025-11-26"
                                                    name="ot_hp_date">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Status</label>
                                                <select class="form-select" name="ot_hp_status">
                                                    <option>Paid</option>
                                                    <option>Due</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Insurance -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-3">
                                                <label class="fw-bold">Insurance Name</label>
                                                <select class="form-control text-uppercase" required
                                                    name="ot_insurance_name">
                                                    <option value="">-- Select Insurance --</option>
                                                    <option value="Tata AIG Insurance">Tata AIG Insurance</option>
                                                    <option value="Bharti AXA">Bharti AXA</option>
                                                    <option value="Bajaj Allianz">Bajaj Allianz</option>
                                                    <option value="ICICI Lombard">ICICI Lombard</option>
                                                    <option value="IFFCO Tokio">IFFCO Tokio</option>
                                                    <option value="National Insurance">National Insurance</option>
                                                    <option value="New India Assurance">New India Assurance</option>
                                                    <option value="Oriental Insurance">Oriental Insurance</option>
                                                    <option value="United India Insurance">United India Insurance
                                                    </option>
                                                    <option value="Reliance General Insurance">Reliance General
                                                        Insurance</option>
                                                    <option value="Royal Sundaram Insurance">Royal Sundaram Insurance
                                                    </option>
                                                    <option value="Chola MS Insurance">Chola MS Insurance</option>
                                                    <option value="HDFC ERGO">HDFC ERGO</option>
                                                    <option value="ECGC">ECGC</option>
                                                    <option value="Agriculture Insurance Company of India (AIC)">
                                                        Agriculture Insurance Company of India (AIC)</option>
                                                    <option value="Star Health Insurance">Star Health Insurance</option>
                                                    <option value="Future Generali">Future Generali</option>
                                                    <option value="Universal Sompo">Universal Sompo</option>
                                                    <option value="Shriram General Insurance">Shriram General Insurance
                                                    </option>
                                                    <option value="Raheja QBE">Raheja QBE</option>
                                                    <option value="SBI General Insurance">SBI General Insurance</option>
                                                    <option value="Niva Bupa Health Insurance">Niva Bupa Health
                                                        Insurance</option>
                                                    <option value="L&T Insurance">L&T Insurance</option>
                                                    <option value="Care Health Insurance">Care Health Insurance</option>
                                                    <option value="Magma HDI">Magma HDI</option>
                                                    <option value="Liberty General Insurance">Liberty General Insurance
                                                    </option>
                                                    <option value="Manipal Cigna">Manipal Cigna</option>
                                                    <option value="Kotak General Insurance">Kotak General Insurance
                                                    </option>
                                                    <option value="Aditya Birla Capital Health Insurance">Aditya Birla
                                                        Capital Health Insurance</option>
                                                    <option value="Digit Insurance">Digit Insurance</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">Payment Status</label>
                                                <select class="form-control" required
                                                    name="ot_insurance_payment_status">
                                                    <option value="">-- Select Status --</option>
                                                    <option value="paid">Paid</option>
                                                    <option value="due">Due</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">Amount</label>
                                                <input type="number" class="form-control" placeholder="Enter Amount"
                                                    required name="ot_insurance_amount">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">Start Date</label>
                                                <input type="date" class="form-control" id="startDate"
                                                    value="2025-11-26" required name="ot_insurance_start_date">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">End Date</label>
                                                <input type="date" class="form-control" id="endDate" readonly required
                                                    name="ot_insurance_end_date">
                                            </div>

                                            <span class="fw-bold text-primary">Duration:<span
                                                    id="durationText">--</span></span>
                                        </div>
                                    </div>

                                    <!-- Purchaser & Seller Sign -->
                                    <div class="row g-4">
                                        <div class="col-12 col-md-6">
                                            <div class="border rounded-3">
                                                <label class="form-label fw-bold">Purchaser Sign</label>
                                                <div class="row g-3">
                                                    <div class="col-6">
                                                        <label class="form-label small">Status</label>
                                                        <select class="form-select" name="ot_purchaser_sign_status">
                                                            <option>Yes</option>
                                                            <option>No</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small">Date</label>
                                                        <input type="date" class="form-control" value="2025-11-26"
                                                            name="ot_purchaser_sign_date">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="border rounded-3">
                                                <label class="form-label fw-bold">Seller Sign</label>
                                                <div class="row g-3">
                                                    <div class="col-6">
                                                        <label class="form-label small">Status</label>
                                                        <select class="form-select" name="ot_seller_sign_status">
                                                            <option>Yes</option>
                                                            <option>No</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small">Date</label>
                                                        <input type="date" class="form-control" value="2025-11-26"
                                                            name="ot_seller_sign_date">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer d-flex align-items-center">
                            <?php if ($current_step < 4): ?>
                                <button type="submit" name="action" value="save_next" class="btn btn-primary fw-bold shadow-sm px-4 ms-auto">
                                    <i class="ph-bold ph-floppy-disk me-1"></i> Save & Next Step
                                </button>
                            <?php else: ?>
                                <button type="submit" name="action" value="finish" class="btn btn-success px-4 ms-auto text-white shadow-lg">
                                    Finish <i class="ph-bold ph-check-circle ms-1"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="modal-footer d-flex align-items-center">
                    <button type="button" class="btn btn-light border px-4 shadow-sm me-2" id="prevBtn"
                        style="display:none;">Back</button>
                    <button type="button" class="btn btn-primary px-4 ms-auto shadow-lg" id="nextBtn">Next</button>
                </div>
            </div>

        </div>
    </div>



    <!-- LEADS LIST MODAL -->
    <!-- Responsive: modal-fullscreen-sm-down for mobile view -->
    <div class="modal fade" id="leadsListModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold ms-2 fs-5 fs-md-4">Manage Leads</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3" id="leadsContainer">
                        <?php
                        $result = $conn->query("SELECT id, name, phone, bike_model, created_at FROM leads ORDER BY id DESC");
                        if ($result->num_rows > 0):
                            while ($lead = $result->fetch_assoc()):
                        ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card bg-primary bg-gradient text-white border-0 shadow h-100">
                                        <div class="card-body position-relative overflow-hidden">

                                            <i class="ph-bold ph-motorcycle position-absolute text-white opacity-25"
                                                style="font-size: 5rem; right: 20px; bottom: -18px;"></i>

                                            <div class="d-flex justify-content-between align-items-start position-relative z-1">
                                                <div>
                                                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($lead['name']) ?></h5>
                                                    <div class="opacity-75 small mb-3">
                                                        <i class="ph-bold ph-phone me-1"></i>
                                                        <?= htmlspecialchars($lead['phone']) ?>
                                                    </div>
                                                </div>
                                                <button
                                                    class="btn btn-white bg-white text-danger btn-sm rounded-5 shadow-sm delete-lead"
                                                    data-id="<?= $lead['id'] ?>">
                                                    <i class="ph-bold ph-trash"></i>
                                                </button>
                                            </div>

                                            <div class="mt-2 position-relative z-1">
                                                <span class="badge bg-white text-primary fw-bold me-2">
                                                    <?= htmlspecialchars($lead['bike_model']) ?>
                                                </span>
                                                <small class="opacity-75">
                                                    Added: <?= date('M d', strtotime($lead['created_at'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            endwhile;
                        else:
                            ?>
                            <div class="col-12">
                                <p class="text-center text-muted mb-0">No leads found.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LEAD MODAL -->
    <!-- Add Leads -->
    <div class="modal fade" id="leadModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Form Start -->
                <form method="POST" class="app-form card p-4 shadow-sm bg-white rounded" action="update_forms_data.php">
                    <h4 class="mb-3 fw-bold">Capture Lead</h4>

                    <div class="mb-3">
                        <label class="fw-bold">Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter full name"
                            pattern="^[A-Za-z\s]{2,50}$"
                            title="Name must contain only letters and spaces, 2-50 characters long." required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Phone</label>
                        <input type="tel" name="phone" class="form-control" placeholder="Enter phone number"
                            pattern="^\d{10}$" title="Phone number must be exactly 10 digits." required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Bike Model</label>
                        <input type="text" name="bike_model" class="form-control" placeholder="e.g. Splendor"
                            pattern="^[A-Za-z0-9\s\-]{2,30}$"
                            title="Bike model can include letters, numbers, spaces, and hyphens (2-30 characters)."
                            required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Save Lead</button>
                </form>
                <!-- Form End -->

            </div>
        </div>
    </div>

    <!-- Profile Configuration Modal -->
    <div class="modal fade" id="profileConfigModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-4">Edit Profile</h5>

                        <form method="POST" action="update_forms_data.php">
                            <input type="hidden" name="form_type" value="profile">

                            <div class="mb-3">
                                <label class="fw-bold small text-muted">Role</label>
                                <select class="form-select" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="admin" <?= ($u['role'] ?? '') == 'admin' ? 'selected' : '' ?>>ADMIN
                                    </option>
                                    <option value="user" <?= ($u['role'] ?? '') == 'user' ? 'selected' : '' ?>>USER
                                    </option>
                                    <option value="manager" <?= ($u['role'] ?? '') == 'manager' ? 'selected' : '' ?>>
                                        MANAGER</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold small text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?= $u['email'] ?? '' ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold small text-muted">Username / Login ID</label>
                                <input type="text" name="user_id" class="form-control"
                                    value="<?= $u['user_id'] ?? '' ?>" required>
                                <small class="text-muted">Used for login.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold small text-muted">New Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Optional">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold small text-muted">Confirm Password</label>
                                    <input type="password" name="confirmPassword" class="form-control"
                                        placeholder="Confirm">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">Save
                                Changes</button>
                        </form>

                    </div>
                </div>
                <!-- Form End -->

            </div>
        </div>
    </div>

</body>

</html>