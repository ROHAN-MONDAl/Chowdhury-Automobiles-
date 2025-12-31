<?php
// 1. SILENT INITIALIZATION
ob_start(); // Buffer output

// --- FIX: SETTINGS MUST GO FIRST ---
// These must run BEFORE session_start() (which is likely inside db.php)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// 2. INCLUDE DB
require "db.php";

// 3. START SESSION (If db.php didn't start it)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Anti-Cache Headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// ======================================================
// 2. THE "KILL SWITCH" FUNCTION
// ======================================================
function force_404_exit()
{
    session_unset();
    session_destroy();
    header("Location: 404.php");
    exit();
}

// ======================================================
// 3. SECURITY CHECKS
// ======================================================

// CHECK A: Is user logged in?
if (!isset($_SESSION["user_id"])) {
    force_404_exit();
}

// CHECK B: Session Hijacking (Browser Fingerprint)
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
} elseif ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    force_404_exit();
}

// CHECK C: Database Verification
$id = $_SESSION['id'] ?? 0;

$query = $conn->prepare("SELECT user_id, email, role FROM users WHERE id = ? LIMIT 1");
$query->bind_param("i", $id);
$query->execute();
$u = $query->get_result()->fetch_assoc();

// If user does not exist
if (!$u) {
    force_404_exit();
}

// CHECK D: The "Admin Only" Gate
if ($u['role'] !== 'admin') {
    error_log("Security Breach: Non-admin user " . $u['email'] . " tried to access Admin Panel.");
    force_404_exit();
}

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
    <!-- global Messages -->
    <?php include_once "global_message.php" ?>

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
                            <h5 class="fw-bold mb-0 fs-5"> Profile</h5>
                            <span class="text-secondary small fw-bold text-uppercase d-block mb-1">
                                Configuration
                            </span>
                            <p class="text-secondary mb-0 small">
                                Manage and update your profile details.
                            </p>
                        </div>

                    </div>
                </div>

                <!-- Add Staff and Managers -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="app-card p-4 d-flex align-items-center" style="cursor: pointer;" data-bs-toggle="modal"
                        data-bs-target="#addUserModal">

                        <div class="card-icon-box bg-green-grad me-3 rounded-circle 
                    d-flex justify-content-center align-items-center p-4">
                            <i class="ph-bold ph-user-plus fs-3"></i>
                        </div>

                        <div>
                            <h5 class="fw-bold mb-0 fs-5">Add Staff</h5>
                            <span class="text-secondary small fw-bold text-uppercase d-block mb-1">
                                Create & Assign
                            </span>
                            <p class="text-secondary mb-0 small">Add Manager, Staff, or User credentials.</p>
                        </div>
                    </div>
                </div>

                <!-- View Team -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="app-card p-4 d-flex align-items-center" style="cursor: pointer;" data-bs-toggle="modal"
                        data-bs-target="#viewUsersModal">

                        <div class="card-icon-box bg-primary bg-opacity-10 text-primary me-3 rounded-circle 
                    d-flex justify-content-center align-items-center p-4">
                            <i class="ph-bold ph-users-three fs-3"></i>
                        </div>

                        <div>
                            <h5 class="fw-bold mb-0 fs-5">View Team</h5>
                            <span class="text-secondary small fw-bold text-uppercase d-block mb-1">
                                Manage Access
                            </span>
                            <p class="text-secondary mb-0 small">View credentials and delete users.</p>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </section>




    <!-- ADD vechical form -->
    <div class="modal fade" id="dealModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content border-0">

                <?php
                // PHP: Define Steps Array
                $wizard_steps = [
                    1 => 'Vehicle',
                    2 => 'Seller',
                    3 => 'Purchaser',
                    4 => 'Transfer'
                ];
                ?>

                <div class="row g-0 h-100">

                    <div class="col-lg-2 d-none d-lg-flex flex-column border-end h-100 shadow-sm z-3 position-relative sidebar-gradient">

                        <div class="p-4 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle border p-1 shadow-sm" style="width: 48px; height: 48px; background: rgba(255,255,255,0.5);">
                                    <img src="../images/logo.jpeg" alt="Logo" class="rounded-circle w-100 h-100 object-fit-cover">
                                </div>
                                <div class="d-flex flex-column lh-1">
                                    <span class="fw-bolder text-dark tracking-tight">CHOWDHURY</span>
                                    <span class="text-primary small fw-bold">AUTOMOBILE</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 overflow-y-auto flex-grow-1">
                            <div class="d-flex flex-column gap-2">
                                <?php foreach ($wizard_steps as $step_key => $label): ?>
                                    <div class="step-item d-flex align-items-center gap-3 p-3 rounded-3 border-start border-4 border-transparent"
                                        data-step="<?= $step_key ?>" style="cursor: pointer; transition: all 0.2s ease;">

                                        <div class="step-circle border rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 32px; height: 32px;">
                                            <span class="small fw-bold"><?= $step_key ?></span>
                                        </div>

                                        <div class="d-flex flex-column">
                                            <span class="step-label text-dark fw-medium" style="font-size: 0.95rem;"><?= $label ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-10 h-100 d-flex flex-column position-relative">

                        <!-- Upload Progress Container -->
                        <div id="uploadProgressContainer" class="d-none"
                            style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 320px; z-index: 10000; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 0 50px rgba(0,0,0,0.5);">

                            <div class="text-center mb-3">
                                <h5 id="overlayTitle" class="fw-bold mb-0">Processing Data</h5>
                                <small id="overlaySubtitle" class="text-muted">Please wait...</small>
                            </div>

                            <div class="d-flex justify-content-between mb-1">
                                <span id="uploadLabel" class="fw-bold" style="font-size: 0.9rem;">Uploading</span>
                                <span id="progressPercent" class="fw-bold" style="font-size: 0.9rem;">0%</span>
                            </div>

                            <div class="progress" style="height: 12px; border-radius: 6px;">
                                <div id="progressBar"
                                    class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                    role="progressbar"
                                    style="width: 0%">
                                </div>
                            </div>
                        </div>

                        <!-- Toast Notification -->
                        <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3"
                            style="z-index: 11000;">

                            <div id="validationToast"
                                class="toast align-items-center text-white bg-danger border-0"
                                role="alert"
                                aria-live="assertive"
                                aria-atomic="true">

                                <div class="d-flex">
                                    <div class="toast-body d-flex align-items-center gap-2">
                                        <i class="ph-bold ph-warning-circle fs-5"></i>
                                        <span id="toastMessage">Please fill all mandatory fields.</span>
                                    </div>

                                    <button type="button"
                                        class="btn-close btn-close-white me-2 m-auto"
                                        data-bs-dismiss="toast"
                                        aria-label="Close">
                                    </button>
                                </div>
                            </div>
                        </div>



                        <form action="vehicle_form.php" id="dealForm" method="POST" class="d-flex flex-column h-100" enctype="multipart/form-data" novalidate>

                            <input type="hidden" name="step" value="<?= isset($_GET['step']) ? $_GET['step'] : 1 ?>">
                            <input type="hidden" name="vehicle_id" value="<?= isset($_GET['id']) ? $_GET['id'] : '' ?>">
                            <input type="hidden" name="action" id="formAction" value="save_next">

                            <div class="position-sticky top-0 border-bottom shadow-sm z-3">
                                <div class="container-fluid">
                                    <div class="d-flex align-items-center justify-content-between py-2 px-2 px-md-4 gap-2">

                                        <!-- Page Title (Desktop) -->
                                        <h5 class="m-0 fw-semibold text-dark d-none d-lg-block">
                                            Chowdhury Automobile
                                        </h5>

                                        <!-- Step Indicator (Mobile) -->
                                        <div class="fw-semibold text-primary d-lg-none">
                                            <span id="mobile-step-indicator">Step 1</span>
                                        </div>

                                        <!-- Close / Back to Dashboard -->
                                        <a href="dashboard.php" class="text-decoration-none">
                                            <button type="button"
                                                class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center shadow-sm p-0"
                                                style="width: 40px; height: 40px;"
                                                aria-label="Close">
                                                <i class="ph-bold ph-x fs-5 text-white"></i>
                                            </button>
                                        </a>



                                    </div>
                                </div>
                            </div>


                            <div class="flex-grow-1 overflow-y-auto p-3 p-md-5" style="background-color:gray;">
                                <div class="container-fluid" style="max-width: 900px;">
                                    <!-- 1st step -->
                                    <div id="step-1" class="wizard-step fade-in-animation">
                                        <div class="card  steps-id p-4 border-0 shadow-sm position-relative sold-wrapper rounded-4">
                                            <div class="sold-stamp">SOLD OUT</div>
                                            <div class="sold-overlay"></div>
                                            <div>
                                                <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Vehicle
                                                    Details</h6>
                                                <label class="mb-2">Vehicle Photos</label>
                                                <div class="row g-3 mb-4">
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <i class="ph-bold ph-camera fs-3 text-secondary"></i>
                                                            <img src="">
                                                            <input type="file" name="photo1" accept="image/*"
                                                                hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <i class="ph-bold ph-camera fs-3 text-secondary"></i>
                                                            <img src="">
                                                            <input type="file" name="photo2" accept="image/*"
                                                                hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <i class="ph-bold ph-camera fs-3 text-secondary"></i>
                                                            <img src="">
                                                            <input type="file" name="photo3" accept="image/*"
                                                                hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <i class="ph-bold ph-camera fs-3 text-secondary"></i>
                                                            <img src="">
                                                            <input type="file" name="photo4" accept="image/*"
                                                                hidden>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-12 col-md-6">
                                                        <label for="vehicleType" class="form-label">Vehicle Type</label>
                                                        <select id="vehicleType" name="vehicle_type"
                                                            class="form-select fw-bold">
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
                                                        <label class="fw-bold">Bike Name</label>
                                                        <input type="text" id="nameField" name="name"
                                                            class="form-control" placeholder="Enter Name">
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label>Vehicle Number</label>
                                                        <input type="text" name="vehicle_number"
                                                            class="form-control fw-bold text-uppercase"
                                                            placeholder="WB 00 AA 0000" value="WB ">
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label>Register Date</label>
                                                        <input type="date" name="register_date"
                                                            class="form-control" value="2025-11-26">
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
                                                        <input type="text" name="engine_number"
                                                            class="form-control text-uppercase">
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-12 col-md-6">
                                                        <label class="fw-bold mb-2">Payment Type</label>
                                                        <div class="d-flex gap-2 mb-3">
                                                            <input type="radio" class="btn-check"
                                                                name="payment_type" id="sp_cash" value="Cash" checked
                                                                data-bs-toggle="collapse" data-bs-target="#cashBox"
                                                                aria-controls="cashBox">
                                                            <label class="btn btn-outline-success"
                                                                for="sp_cash">Cash</label>

                                                            <input type="radio" class="btn-check"
                                                                name="payment_type" id="sp_online" value="Online"
                                                                data-bs-toggle="collapse" data-bs-target="#onlineBox"
                                                                aria-controls="onlineBox">
                                                            <label class="btn btn-outline-primary"
                                                                for="sp_online">Online</label>
                                                        </div>

                                                        <div id="payBoxes">
                                                            <div id="cashBox" class="collapse show"
                                                                data-bs-parent="#payBoxes">
                                                                <div
                                                                    class="p-3 mb-3 bg-white rounded-3 border shadow-sm">
                                                                    <label class="fw-bold small mb-1">Bike Price</label>
                                                                    <input type="number" name="cash_price"
                                                                        class="form-control form-control-sm mb-3"
                                                                        placeholder="Enter Amount">
                                                                </div>
                                                            </div>

                                                            <div id="onlineBox" class="collapse"
                                                                data-bs-parent="#payBoxes">
                                                                <div
                                                                    class="p-3 mb-3 bg-white rounded-3 border shadow-sm">
                                                                    <label class="fw-bold small mb-2">Select Online
                                                                        Method</label>
                                                                    <div class="d-flex flex-wrap gap-3 mb-2">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input"
                                                                                name="online_method" id="om_gpay"
                                                                                value="Google Pay">
                                                                            <label
                                                                                class="form-check-label small fw-bold"
                                                                                for="om_gpay">Google Pay</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input"
                                                                                name="online_method" id="om_paytm"
                                                                                value="Paytm">
                                                                            <label
                                                                                class="form-check-label small fw-bold"
                                                                                for="om_paytm">Paytm</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input"
                                                                                name="online_method" id="om_phonepe"
                                                                                value="PhonePe">
                                                                            <label
                                                                                class="form-check-label small fw-bold"
                                                                                for="om_phonepe">PhonePe</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input"
                                                                                name="online_method" id="om_bharatpe"
                                                                                value="BharatPe">
                                                                            <label
                                                                                class="form-check-label small fw-bold"
                                                                                for="om_bharatpe">BharatPe</label>
                                                                        </div>
                                                                    </div>

                                                                    <input type="text"
                                                                        name="online_transaction_id"
                                                                        class="form-control form-control-sm mb-3 text-uppercase"
                                                                        placeholder="Transaction / UPI Reference ID">

                                                                    <label class="fw-bold small mb-1">Bike Price</label>
                                                                    <div class="input-group">
                                                                        <span
                                                                            class="input-group-text bg-white border-end-0">₹</span>
                                                                        <input type="number"
                                                                            name="online_price"
                                                                            class="form-control border-start-0 ps-0"
                                                                            placeholder="Enter Price">
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
                                                            <input class="form-check-input" type="radio"
                                                                name="police_challan" value="No" checked
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#challan-section">
                                                            <label class="form-check-label fw-bold">No</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="police_challan" value="Yes"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#challan-section">
                                                            <label class="form-check-label fw-bold">Yes</label>
                                                        </div>
                                                    </div>

                                                    <div class="collapse mt-3" id="challan-section">
                                                        <div class="border rounded p-2 mb-2 bg-white">
                                                            <label class="fw-bold small">Challan 1</label>
                                                            <div class="row g-2">
                                                                <div class="col-md-4">
                                                                    <input type="text" name="challan1_number"
                                                                        class="form-control text-uppercase"
                                                                        placeholder="Challan Number">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input type="number" name="challan1_amount"
                                                                        class="form-control" placeholder="Amount">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="btn-group w-100 btn-group-sm">
                                                                        <input type="radio" class="btn-check"
                                                                            name="challan1_status" id="pen1"
                                                                            value="Pending" checked>
                                                                        <label class="btn btn-outline-danger"
                                                                            for="pen1">Pending</label>

                                                                        <input type="radio" class="btn-check"
                                                                            name="challan1_status" id="paid1"
                                                                            value="Paid">
                                                                        <label class="btn btn-outline-success"
                                                                            for="paid1">Paid</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="border rounded p-2 mb-2 bg-white">
                                                            <label class="fw-bold small">Challan 2</label>
                                                            <div class="row g-2">
                                                                <div class="col-md-4">
                                                                    <input type="text" name="challan2_number"
                                                                        class="form-control text-uppercase"
                                                                        placeholder="Challan Number">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input type="number" name="challan2_amount"
                                                                        class="form-control" placeholder="Amount">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="btn-group w-100 btn-group-sm">
                                                                        <input type="radio" class="btn-check"
                                                                            name="challan2_status" id="pen2"
                                                                            value="Pending" checked>
                                                                        <label class="btn btn-outline-danger"
                                                                            for="pen2">Pending</label>

                                                                        <input type="radio" class="btn-check"
                                                                            name="challan2_status" id="paid2"
                                                                            value="Paid">
                                                                        <label class="btn btn-outline-success"
                                                                            for="paid2">Paid</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="border rounded p-2 mb-2 bg-white">
                                                            <label class="fw-bold small">Challan 3</label>
                                                            <div class="row g-2">
                                                                <div class="col-md-4">
                                                                    <input type="text" name="challan3_number"
                                                                        class="form-control text-uppercase"
                                                                        placeholder="Challan Number">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input type="number" name="challan3_amount"
                                                                        class="form-control" placeholder="Amount">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="btn-group w-100 btn-group-sm">
                                                                        <input type="radio" class="btn-check"
                                                                            name="challan3_status" id="pen3"
                                                                            value="Pending" checked>
                                                                        <label class="btn btn-outline-danger"
                                                                            for="pen3">Pending</label>

                                                                        <input type="radio" class="btn-check"
                                                                            name="challan3_status" id="paid3"
                                                                            value="Paid">
                                                                        <label class="btn btn-outline-success"
                                                                            for="paid3">Paid</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between">
                                                    <label class="form-check-label fw-bold text-danger mb-0"
                                                        for="soldToggle">Mark as Sold Out</label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="soldToggle" name="sold_out" value="1"
                                                            style="width: 3em; height: 1.5em;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 2nd step -->
                                    <div id="step-2" class="wizard-step d-none">
                                        <div class="card steps-id border-0 p-4 shadow-sm rounded-4">
                                            <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Seller Details
                                            </h6>

                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-4">
                                                    <label>Date</label>
                                                    <input type="date" name="seller_date" class="form-control">
                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <label>Vehicle No</label>
                                                    <input type="text" name="seller_vehicle_number"
                                                        class="form-control fw-bold text-uppercase"
                                                        placeholder="WB 00 AA 0000" value="WB ">
                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <label>Bike Name</label>
                                                    <input type="text" name="seller_bike_name"
                                                        class="form-control text-uppercase">
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <label>Chassis No</label>
                                                    <input type="text" name="seller_chassis_no"
                                                        class="form-control text-uppercase">
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <label>Engine No</label>
                                                    <input type="text" name="seller_engine_no"
                                                        class="form-control text-uppercase">
                                                </div>

                                                <div class="col-12">
                                                    <label>Seller Name</label>
                                                    <input type="text" name="seller_name"
                                                        class="form-control text-uppercase">
                                                </div>

                                                <div class="col-12">
                                                    <label>Address</label>
                                                    <textarea name="seller_address" class="form-control text-uppercase"
                                                        rows="2"></textarea>
                                                </div>
                                            </div>

                                            <label class="mb-2">Mobile Numbers</label>
                                            <div class="row g-2 mb-3">
                                                <div class="col-12"><input type="tel" name="seller_mobile1"
                                                        class="form-control" placeholder="Mob 1"></div>
                                                <div class="col-12"><input type="tel" name="seller_mobile2"
                                                        class="form-control" placeholder="Mob 2"></div>
                                                <div class="col-12"><input type="tel" name="seller_mobile3"
                                                        class="form-control" placeholder="Mob 3"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="mb-2">Purchaser Documents (In Seller Step)</label>
                                                <div class="row g-2">
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Aadhar Front</span>
                                                            <img src="">
                                                            <input type="file" name="doc_aadhar_front" accept="image/*"
                                                                hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Aadhar Back</span>
                                                            <img src="">
                                                            <input type="file" name="doc_aadhar_back" accept="image/*"
                                                                hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Voter Front</span>
                                                            <img src="">
                                                            <input type="file" name="doc_voter_front" accept="image/*"
                                                                hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Voter Back</span>
                                                            <img src="">
                                                            <input type="file" name="doc_voter_back" accept="image/*"
                                                                hidden>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bg-light p-3 rounded-4 border mb-3">
                                                <label class="mb-2 fw-bold">Papers Received</label>
                                                <div class="d-flex flex-wrap gap-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" name="pr_rc" class="form-check-input"
                                                            id="pr_rc" data-bs-toggle="collapse"
                                                            data-bs-target="#rcUploadBox">
                                                        <label class="fw-bold" for="pr_rc">RC</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="pr_tax" class="form-check-input"
                                                            id="pr_tax">
                                                        <label class="fw-bold" for="pr_tax">Tax Token</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="pr_insurance"
                                                            class="form-check-input" id="pr_ins">
                                                        <label class="fw-bold" for="pr_ins">Insurance</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="pr_pucc" class="form-check-input"
                                                            id="pr_puc">
                                                        <label class="fw-bold" for="pr_puc">PUCC</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="pr_noc" class="form-check-input"
                                                            id="pr_noc" data-bs-toggle="collapse"
                                                            data-bs-target="#nocUploadBox">
                                                        <label class="fw-bold" for="pr_noc">NOC</label>
                                                    </div>
                                                </div>

                                                <div class="collapse mt-3" id="rcUploadBox">
                                                    <label class="fw-bold small">RC Upload</label>
                                                    <div class="row g-2">
                                                        <div class="col-12">
                                                            <div class="border rounded p-2 text-center bg-white">
                                                                <small class="fw-bold d-block mb-1"
                                                                    style="font-size:10px">RC FRONT</small>
                                                                <input type="file" name="rc_front"
                                                                    class="form-control form-control-sm mt-1">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="border rounded p-2 text-center bg-white">
                                                                <small class="fw-bold d-block mb-1"
                                                                    style="font-size:10px">RC BACK</small>
                                                                <input type="file" name="rc_back"
                                                                    class="form-control form-control-sm mt-1">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="collapse mt-3" id="nocUploadBox">
                                                    <label class="fw-bold small">NOC Status</label>
                                                    <div class="d-flex justify-content-center">
                                                        <div class="btn-group w-75 btn-group-sm mb-3 mx-auto"
                                                            role="group">
                                                            <input type="radio" name="noc_status" class="btn-check"
                                                                id="noc_paid" value="paid" checked>
                                                            <label class="btn btn-outline-success"
                                                                for="noc_paid">Paid</label>

                                                            <input type="radio" name="noc_status" class="btn-check"
                                                                id="noc_due" value="due">
                                                            <label class="btn btn-outline-danger"
                                                                for="noc_due">Due</label>
                                                        </div>
                                                    </div>

                                                    <div class="row g-2">
                                                        <div class="col-12">
                                                            <div class="border rounded small-box text-center p-2">
                                                                <span class="small text-muted fw-bold">NOC Front</span>
                                                                <input type="file" name="noc_front"
                                                                    class="form-control form-control-sm mt-1">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="border rounded small-box text-center p-2">
                                                                <span class="small text-muted fw-bold">NOC Back</span>
                                                                <input type="file" name="noc_back"
                                                                    class="form-control form-control-sm mt-1">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label class="fw-bold mb-2">Payment Type</label>
                                                    <div class="d-flex gap-2 mb-3">
                                                        <input type="radio" name="seller_payment_type" class="btn-check"
                                                            id="pay_cash" value="cash" checked data-bs-toggle="collapse"
                                                            data-bs-target="#cashBox">
                                                        <label class="btn btn-outline-success"
                                                            for="pay_cash">Cash</label>

                                                        <input type="radio" name="seller_payment_type" class="btn-check"
                                                            id="pay_online" value="online" data-bs-toggle="collapse"
                                                            data-bs-target="#onlineBox">
                                                        <label class="btn btn-outline-primary"
                                                            for="pay_online">Online</label>
                                                    </div>

                                                    <div id="payAccordion">
                                                        <div id="cashBox" class="collapse show"
                                                            data-bs-parent="#payAccordion">
                                                            <div class="p-3 bg-white border rounded shadow-sm">
                                                                <label class="fw-bold small mb-1">Price</label>
                                                                <div class="input-group">
                                                                    <span
                                                                        class="input-group-text bg-white border-end-0">₹</span>
                                                                    <input type="number" name="seller_cash_price"
                                                                        class="form-control border-start-0 ps-0"
                                                                        placeholder="Enter Price">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div id="onlineBox" class="collapse"
                                                            data-bs-parent="#payAccordion">
                                                            <div class="p-3 bg-white border rounded shadow-sm">
                                                                <label class="fw-bold small mb-2">Online Method</label>
                                                                <div class="d-flex flex-wrap gap-3 mb-3">
                                                                    <label class="form-check">
                                                                        <input type="radio" name="seller_online_method"
                                                                            class="form-check-input" value="Google Pay">
                                                                        <span class="form-check-label fw-bold">Google
                                                                            Pay</span>
                                                                    </label>
                                                                    <label class="form-check">
                                                                        <input type="radio" name="seller_online_method"
                                                                            class="form-check-input" value="paytm">
                                                                        <span
                                                                            class="form-check-label fw-bold">Paytm</span>
                                                                    </label>
                                                                    <label class="form-check">
                                                                        <input type="radio" name="seller_online_method"
                                                                            class="form-check-input" value="phonepe">
                                                                        <span
                                                                            class="form-check-label fw-bold">PhonePe</span>
                                                                    </label>
                                                                    <label class="form-check">
                                                                        <input type="radio" name="seller_online_method"
                                                                            class="form-check-input" value="bharatpe">
                                                                        <span
                                                                            class="form-check-label fw-bold">BharatPe</span>
                                                                    </label>
                                                                </div>

                                                                <input type="text" name="seller_online_transaction_id"
                                                                    class="form-control form-control-sm mb-3 text-uppercase"
                                                                    placeholder="Transaction / UPI Reference ID">

                                                                <label class="fw-bold small mb-1">Price</label>
                                                                <div class="input-group">
                                                                    <span
                                                                        class="input-group-text bg-white border-end-0">₹</span>
                                                                    <input type="number" name="seller_online_price"
                                                                        class="form-control border-start-0 ps-0"
                                                                        placeholder="Enter Price">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label>Exchange Showroom Name</label>
                                                    <input type="text" name="exchange_showroom_name"
                                                        class="form-control text-uppercase" placeholder="Showroom Name">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label>Staff Name</label>
                                                    <input type="text" name="staff_name"
                                                        class="form-control text-uppercase" placeholder="Staff Name">
                                                </div>
                                            </div>

                                            <div class="bg-light p-3 rounded-4 border">
                                                <label class="text-primary">Payment Calculation</label>
                                                <div class="row g-2">
                                                    <div class="col-12"><input type="number" name="total_amount"
                                                            class="form-control" placeholder="Total" id="s_total"></div>
                                                    <div class="col-12"><input type="number" name="paid_amount"
                                                            class="form-control" placeholder="Paid" id="s_paid"></div>
                                                    <div class="col-12"><input type="number" name="due_amount"
                                                            class="form-control bg-white fw-bold text-danger"
                                                            placeholder="Due" id="s_due" readonly></div>
                                                    <div class="col-12"><input type="text" name="due_reason"
                                                            class="form-control d-none mt-1" id="s_due_reason"
                                                            placeholder="Reason for due amount..."></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 3rd step -->
                                    <div id="step-3" class="wizard-step d-none">
                                        <div class="card steps-id border-0 p-4 shadow-sm rounded-4">
                                            <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Purchaser Details
                                            </h6>

                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label>Date</label>
                                                    <input type="date" name="purchaser_date" class="form-control"
                                                        value="2025-11-26">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label>Purchaser Name</label>
                                                    <input type="text" name="purchaser_name"
                                                        class="form-control text-uppercase">
                                                </div>
                                                <div class="col-12">
                                                    <label>Address</label>
                                                    <textarea name="purchaser_address"
                                                        class="form-control text-uppercase" rows="2"></textarea>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label>Bike Name</label>
                                                    <input type="text" name="purchaser_bike_name"
                                                        class="form-control text-uppercase">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label>Vehicle No</label>
                                                    <input type="text" name="purchaser_vehicle_no"
                                                        class="form-control fw-bold text-uppercase"
                                                        placeholder="WB 00 AA 0000" value="WB ">
                                                </div>
                                            </div>

                                            <div class="bg-light p-3 rounded-4 border mb-4">
                                                <label class="text-dark mb-3 d-block">Purchaser Paper Payment
                                                    Fees</label>

                                                <div class="row g-2 align-items-end mb-3">
                                                    <div class="col-12 col-md-4">
                                                        <label>Transfer Amount</label>
                                                        <input type="number" name="purchaser_transfer_amount"
                                                            class="form-control" placeholder="Amount">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Date</label>
                                                        <input type="date" name="purchaser_transfer_date"
                                                            class="form-control" value="2025-11-26">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Status</label>
                                                        <select name="purchaser_transfer_status" class="form-select">
                                                            <option value="paid">Paid</option>
                                                            <option value="due">Due</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row g-2 align-items-end mb-3">
                                                    <div class="col-12 col-md-4">
                                                        <label>HPA</label>
                                                        <input type="number" name="purchaser_hpa_amount"
                                                            class="form-control" placeholder="Amount">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Date</label>
                                                        <input type="date" name="purchaser_hpa_date"
                                                            class="form-control" value="2025-11-26">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Status</label>
                                                        <select name="purchaser_hpa_status" class="form-select">
                                                            <option value="paid">Paid</option>
                                                            <option value="due">Due</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row g-2 align-items-end mb-3">
                                                    <div class="col-12 col-md-4">
                                                        <label>HP</label>
                                                        <input type="number" name="purchaser_hp_amount"
                                                            class="form-control" placeholder="Amount">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Date</label>
                                                        <input type="date" name="purchaser_hp_date" class="form-control"
                                                            value="2025-11-26">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Status</label>
                                                        <select name="purchaser_hp_status" class="form-select">
                                                            <option value="paid">Paid</option>
                                                            <option value="due">Due</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Insurance Name</label>
                                                        <select name="purchaser_insurance_name"
                                                            class="form-control text-uppercase">
                                                            <option value="">-- Select Insurance --</option>
                                                            <option value="Tata AIG Insurance">Tata AIG Insurance
                                                            </option>
                                                            <option value="Bharti AXA">Bharti AXA</option>
                                                            <option value="Bajaj Allianz">Bajaj Allianz</option>
                                                            <option value="ICICI Lombard">ICICI Lombard</option>
                                                            <option value="IFFCO Tokio">IFFCO Tokio</option>
                                                            <option value="National Insurance">National Insurance
                                                            </option>
                                                            <option value="New India Assurance">New India Assurance
                                                            </option>
                                                            <option value="Oriental Insurance">Oriental Insurance
                                                            </option>
                                                            <option value="United India Insurance">United India
                                                                Insurance</option>
                                                            <option value="Reliance General Insurance">Reliance General
                                                                Insurance</option>
                                                            <option value="Royal Sundaram Insurance">Royal Sundaram
                                                                Insurance</option>
                                                            <option value="Chola MS Insurance">Chola MS Insurance
                                                            </option>
                                                            <option value="HDFC ERGO">HDFC ERGO</option>
                                                            <option value="ECGC">ECGC</option>
                                                            <option
                                                                value="Agriculture Insurance Company of India (AIC)">
                                                                Agriculture Insurance Company of India (AIC)</option>
                                                            <option value="Star Health Insurance">Star Health Insurance
                                                            </option>
                                                            <option value="Future Generali">Future Generali</option>
                                                            <option value="Universal Sompo">Universal Sompo</option>
                                                            <option value="Shriram General Insurance">Shriram General
                                                                Insurance</option>
                                                            <option value="Raheja QBE">Raheja QBE</option>
                                                            <option value="SBI General Insurance">SBI General Insurance
                                                            </option>
                                                            <option value="Niva Bupa Health Insurance">Niva Bupa Health
                                                                Insurance</option>
                                                            <option value="L&T Insurance">L&T Insurance</option>
                                                            <option value="Care Health Insurance">Care Health Insurance
                                                            </option>
                                                            <option value="Magma HDI">Magma HDI</option>
                                                            <option value="Liberty General Insurance">Liberty General
                                                                Insurance</option>
                                                            <option value="Manipal Cigna">Manipal Cigna</option>
                                                            <option value="Kotak General Insurance">Kotak General
                                                                Insurance</option>
                                                            <option value="Aditya Birla Capital Health Insurance">Aditya
                                                                Birla Capital Health Insurance</option>
                                                            <option value="Digit Insurance">Digit Insurance</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Payment Status</label>
                                                        <select name="purchaser_insurance_payment_status"
                                                            class="form-control">
                                                            <option value="">-- Select Status --</option>
                                                            <option value="paid">Paid</option>
                                                            <option value="due">Due</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Amount</label>
                                                        <input type="number" name="purchaser_insurance_amount"
                                                            class="form-control" placeholder="Enter Amount">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Issue Date</label>
                                                        <input type="date" name="purchaser_insurance_issue_date"
                                                            class="form-control" id="issueDate" value="2025-11-26">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Expiry Date</label>
                                                        <input type="date" name="purchaser_insurance_expiry_date"
                                                            class="form-control" id="expiryDate" readonly>
                                                    </div>

                                                    <span class="fw-bold text-primary">Validity:<span
                                                            id="expiryText">--</span></span>
                                                </div>
                                            </div>

                                            <div class="bg-light p-3 rounded-4 border mb-3">
                                                <label class="mb-2">Price Breakdown</label>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-12">
                                                        <input type="number" name="purchaser_total" id="p_total"
                                                            class="form-control" placeholder="Total">
                                                    </div>
                                                    <div class="col-12">
                                                        <input type="number" name="purchaser_paid" id="p_paid"
                                                            class="form-control" placeholder="Paid">
                                                    </div>
                                                    <div class="col-12">
                                                        <input type="number" name="purchaser_due" id="p_due"
                                                            class="form-control bg-white" placeholder="Due" readonly>
                                                    </div>
                                                </div>

                                                <div class="d-flex gap-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="purchaser_payment_mode" id="rad_cash" value="cash"
                                                            checked>
                                                        <label class="fw-bold" for="rad_cash" data-bs-toggle="collapse"
                                                            data-bs-target="#sec_cash" role="button">
                                                            Cash
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="purchaser_payment_mode" id="rad_fin" value="finance">
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
                                                                <input type="number" name="purchaser_cash_amount"
                                                                    class="form-control" placeholder="Enter Amount">
                                                            </div>
                                                            <div class="col-12">
                                                                <label>Mobile Number 1</label>
                                                                <input type="tel" name="purchaser_cash_mobile1"
                                                                    class="form-control"
                                                                    placeholder="Enter Mobile Number">
                                                            </div>
                                                            <div class="col-12">
                                                                <label>Mobile Number 2</label>
                                                                <input type="tel" name="purchaser_cash_mobile2"
                                                                    class="form-control"
                                                                    placeholder="Enter Mobile Number">
                                                            </div>
                                                            <div class="col-12">
                                                                <label>Mobile Number 3</label>
                                                                <input type="tel" name="purchaser_cash_mobile3"
                                                                    class="form-control"
                                                                    placeholder="Enter Mobile Number">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="sec_finance" class="collapse border-top pt-2 mt-2"
                                                        data-bs-parent="#payment_details_group">
                                                        <div class="row g-2">
                                                            <div class="col-12">
                                                                <label>HPA With</label>
                                                                <input type="text" name="purchaser_fin_hpa_with"
                                                                    class="form-control text-uppercase"
                                                                    placeholder="Finance Company">
                                                            </div>
                                                            <div class="col-12">
                                                                <label>Disburse Amount</label>
                                                                <div class="input-group">
                                                                    <input type="number"
                                                                        name="purchaser_fin_disburse_amount"
                                                                        class="form-control" placeholder="Amt">
                                                                    <select name="purchaser_fin_disburse_status"
                                                                        class="form-select" style="max-width:100px;">
                                                                        <option value="paid">Paid</option>
                                                                        <option value="due">Due</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <label>Mobile Number 1</label>
                                                                <input type="tel" name="purchaser_fin_mobile1"
                                                                    class="form-control" placeholder="Mobile 1">
                                                            </div>
                                                            <div class="col-12">
                                                                <label>Mobile Number 2</label>
                                                                <input type="tel" name="purchaser_fin_mobile2"
                                                                    class="form-control" placeholder="Mobile 2">
                                                            </div>
                                                            <div class="col-12">
                                                                <label>Mobile Number 3</label>
                                                                <input type="tel" name="purchaser_fin_mobile3"
                                                                    class="form-control" placeholder="Mobile 3">
                                                            </div>
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
                                                            <img src="">
                                                            <input type="file" name="purchaser_doc_aadhar_front"
                                                                accept="image/*" hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Aadhar Back</span>
                                                            <img src="">
                                                            <input type="file" name="purchaser_doc_aadhar_back"
                                                                accept="image/*" hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Voter Front</span>
                                                            <img src="">
                                                            <input type="file" name="purchaser_doc_voter_front"
                                                                accept="image/*" hidden>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3">
                                                        <div class="photo-upload-box">
                                                            <span class="small text-muted fw-bold">Voter Back</span>
                                                            <img src="">
                                                            <input type="file" name="purchaser_doc_voter_back"
                                                                accept="image/*" hidden>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="purchaser_payment_all_paid" id="all_paid">
                                                <label class="form-check-label fw-bold text-success"
                                                    for="all_paid">Payment All Paid</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 4rd step -->
                                    <div id="step-4" class="wizard-step d-none">
                                        <div class="card steps-id border-0 shadow-sm rounded-4">
                                            <h6 class="fw-bold text-primary m-4 mb-3 text-uppercase ls-1">Ownership
                                                Transfer</h6>

                                            <div class="p-3 border rounded-4 mb-4">
                                                <div class="row g-3 mb-3">
                                                    <div class="col-12 col-md-4">
                                                        <label>Name Transfer</label>
                                                        <input type="text" class="form-control text-uppercase"
                                                            placeholder="Enter Name" name="ot_name_transfer">
                                                    </div>

                                                    <div class="col-12 col-md-4">
                                                        <label>Vehicle Number</label>
                                                        <input type="text" class="form-control fw-bold text-uppercase"
                                                            placeholder="WB 00 AA 0000" value="WB "
                                                            name="ot_vehicle_number">
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

                                                <div class="row g-3 mb-4">
                                                    <div class="col-12 col-md-6">
                                                        <label>Vendor Name</label>
                                                        <input type="text" class="form-control text-uppercase"
                                                            placeholder="Vendor Name" name="ot_vendor_name">
                                                    </div>
                                                </div>

                                                <div class="bg-light p-3 rounded-4 border mb-4">
                                                    <label class="text-dark mb-3 d-block">Vendor Payment</label>

                                                    <div class="row g-2 align-items-end mb-3">
                                                        <div class="col-12 col-md-4">
                                                            <label>Transfer Amount</label>
                                                            <input type="number" class="form-control"
                                                                placeholder="Amount" name="ot_transfer_amount">
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

                                                    <div class="row g-2 align-items-end mb-3">
                                                        <div class="col-12 col-md-4">
                                                            <label>HPA</label>
                                                            <input type="number" class="form-control"
                                                                placeholder="Amount" name="ot_hpa_amount">
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

                                                    <div class="row g-2 align-items-end mb-3">
                                                        <div class="col-12 col-md-4">
                                                            <label>HP</label>
                                                            <input type="number" class="form-control"
                                                                placeholder="Amount" name="ot_hp_amount">
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

                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-3">
                                                            <label class="fw-bold">Insurance Name</label>
                                                            <select class="form-control text-uppercase"
                                                                name="ot_insurance_name">
                                                                <option value="">-- Select Insurance --</option>
                                                                <option value="Tata AIG Insurance">Tata AIG Insurance
                                                                </option>
                                                                <option value="Bharti AXA">Bharti AXA</option>
                                                                <option value="Bajaj Allianz">Bajaj Allianz</option>
                                                                <option value="ICICI Lombard">ICICI Lombard</option>
                                                                <option value="IFFCO Tokio">IFFCO Tokio</option>
                                                                <option value="National Insurance">National Insurance
                                                                </option>
                                                                <option value="New India Assurance">New India Assurance
                                                                </option>
                                                                <option value="Oriental Insurance">Oriental Insurance
                                                                </option>
                                                                <option value="United India Insurance">United India
                                                                    Insurance</option>
                                                                <option value="Reliance General Insurance">Reliance
                                                                    General Insurance</option>
                                                                <option value="Royal Sundaram Insurance">Royal Sundaram
                                                                    Insurance</option>
                                                                <option value="Chola MS Insurance">Chola MS Insurance
                                                                </option>
                                                                <option value="HDFC ERGO">HDFC ERGO</option>
                                                                <option value="ECGC">ECGC</option>
                                                                <option
                                                                    value="Agriculture Insurance Company of India (AIC)">
                                                                    Agriculture Insurance Company of India (AIC)
                                                                </option>
                                                                <option value="Star Health Insurance">Star Health
                                                                    Insurance</option>
                                                                <option value="Future Generali">Future Generali</option>
                                                                <option value="Universal Sompo">Universal Sompo</option>
                                                                <option value="Shriram General Insurance">Shriram
                                                                    General Insurance</option>
                                                                <option value="Raheja QBE">Raheja QBE</option>
                                                                <option value="SBI General Insurance">SBI General
                                                                    Insurance</option>
                                                                <option value="Niva Bupa Health Insurance">Niva Bupa
                                                                    Health Insurance</option>
                                                                <option value="L&T Insurance">L&T Insurance</option>
                                                                <option value="Care Health Insurance">Care Health
                                                                    Insurance</option>
                                                                <option value="Magma HDI">Magma HDI</option>
                                                                <option value="Liberty General Insurance">Liberty
                                                                    General Insurance</option>
                                                                <option value="Manipal Cigna">Manipal Cigna</option>
                                                                <option value="Kotak General Insurance">Kotak General
                                                                    Insurance</option>
                                                                <option value="Aditya Birla Capital Health Insurance">
                                                                    Aditya Birla Capital Health Insurance</option>
                                                                <option value="Digit Insurance">Digit Insurance</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="fw-bold">Payment Status</label>
                                                            <select class="form-control"
                                                                name="ot_insurance_payment_status">
                                                                <option value="">-- Select Status --</option>
                                                                <option value="paid">Paid</option>
                                                                <option value="due">Due</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="fw-bold">Amount</label>
                                                            <input type="number" class="form-control"
                                                                placeholder="Enter Amount"
                                                                name="ot_insurance_amount">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="fw-bold">Start Date</label>
                                                            <input type="date" class="form-control" id="startDate"
                                                                value="2025-11-26"
                                                                name="ot_insurance_start_date">
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="fw-bold">End Date</label>
                                                            <input type="date" class="form-control" id="endDate"
                                                                readonly name="ot_insurance_end_date">
                                                        </div>

                                                        <span class="fw-bold text-primary">Duration:<span
                                                                id="durationText">--</span></span>
                                                    </div>
                                                </div>

                                                <div class="row g-4">
                                                    <div class="col-12 col-md-6">
                                                        <div class="border rounded-3">
                                                            <label class="form-label fw-bold">Purchaser Sign</label>
                                                            <div class="row g-3">
                                                                <div class="col-6">
                                                                    <label class="form-label small">Status</label>
                                                                    <select class="form-select"
                                                                        name="ot_purchaser_sign_status">
                                                                        <option>Yes</option>
                                                                        <option>No</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label small">Date</label>
                                                                    <input type="date" class="form-control"
                                                                        value="2025-11-26"
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
                                                                    <select class="form-select"
                                                                        name="ot_seller_sign_status">
                                                                        <option>Yes</option>
                                                                        <option>No</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label small">Date</label>
                                                                    <input type="date" class="form-control"
                                                                        value="2025-11-26" name="ot_seller_sign_date">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-dark border-top position-sticky bottom-0 w-100 shadow"
                                style="z-index:1030;">

                                <div class="container-fluid py-2">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

                                        <!-- Back Button -->
                                        <button type="button"
                                            id="prevBtn"
                                            onclick="prevStep(event)"
                                            class="btn btn-outline-light btn-sm d-flex align-items-center gap-1">
                                            <i class="ph-bold ph-arrow-left"></i>
                                            <span class="d-none d-md-inline">Back</span>
                                        </button>

                                        <!-- Right Actions -->
                                        <div class="d-flex align-items-center gap-2 ms-auto">

                                            <button type="button"
                                                id="btn-save-draft"
                                                class="btn btn-outline-warning btn-sm fw-semibold d-flex align-items-center gap-1">
                                                <i class="ph-bold ph-floppy-disk"></i>
                                                <span class="d-none d-sm-inline">Save Draft</span>
                                            </button>

                                            <button type="button"
                                                id="btn-next"
                                                class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1">
                                                <span class="d-none d-sm-inline">Next</span>
                                                <i class="ph-bold ph-caret-right"></i>
                                            </button>
                                            <button type="button"
                                                id="btn-finish"
                                                class="btn btn-success btn-sm fw-semibold d-none d-flex align-items-center gap-1">
                                                <i class="ph-bold ph-check-circle"></i>
                                                <span>Finish</span>
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>


                    </div>
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
                    <!-- Search bar -->
                    <div class="mb-3">
                        <input type="text" id="leadSearchInput" class="form-control form-control-sm"
                            placeholder="Search leads...">
                    </div>

                    <!-- Leads Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle text-center" id="leadsTable">
                            <thead class="table-primary text-primary">
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>WhatsApp</th>
                                    <th>Model</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $conn->query("SELECT id, name, phone, bike_model, created_at FROM leads ORDER BY id DESC");
                                if ($result->num_rows > 0):
                                    while ($lead = $result->fetch_assoc()):
                                ?>
                                        <tr class="lead-item">
                                            <td class="text-nowrap"><?= date('M d', strtotime($lead['created_at'])) ?></td>
                                            <td class="text-nowrap"><?= htmlspecialchars($lead['name']) ?></td>
                                            <td class="text-nowrap">
                                                <a href="tel:<?= htmlspecialchars($lead['phone']) ?>"
                                                    class="btn btn-light btn-sm d-flex align-items-center gap-1">
                                                    <i class="ph-bold ph-phone"></i> <?= htmlspecialchars($lead['phone']) ?>
                                                </a>
                                            </td>
                                            <td class="text-nowrap">
                                                <a href="https://wa.me/<?= preg_replace('/\D/', '', $lead['phone']) ?>"
                                                    target="_blank"
                                                    class="btn btn-success btn-sm d-flex align-items-center gap-1 text-white">
                                                    <i class="ph-bold ph-whatsapp-logo"></i>
                                                    <?= htmlspecialchars($lead['phone']) ?>
                                                </a>
                                            </td>
                                            <td class="text-nowrap"><?= htmlspecialchars($lead['bike_model']) ?></td>
                                            <td class="text-nowrap">
                                                <button class="btn btn-danger btn-sm delete-lead" data-id="<?= $lead['id'] ?>">
                                                    <i class="ph-bold ph-trash"></i>
                                                </button>
                                            </td>
                                        </tr>

                                    <?php
                                    endwhile;
                                else:
                                    ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No leads found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- No results message -->
                    <div id="noResultsMsg" class="text-center text-muted mt-3" style="display: none;">
                        No leads found.
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
                            title="Name must contain only letters and spaces, 2-50 characters long.">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Phone</label>
                        <input type="tel" name="phone" class="form-control" placeholder="Enter phone number"
                            pattern="^\d{10}$" title="Phone number must be exactly 10 digits.">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Bike Model</label>
                        <input type="text" name="bike_model" class="form-control" placeholder="e.g. Splendor"
                            pattern="^[A-Za-z0-9\s\-]{2,15}$"
                            title="Bike model can include letters, numbers, spaces, and hyphens (2-15 characters).">
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
                                <select class="form-select" name="role">
                                    <option value="">Select Role</option>
                                    <option value="admin" <?= ($u['role'] ?? '') == 'admin' ? 'selected' : '' ?>>ADMIN
                                    </option>
                                    <!-- <option value="user" <?= ($u['role'] ?? '') == 'user' ? 'selected' : '' ?>>USER
                                    </option>
                                    <option value="manager" <?= ($u['role'] ?? '') == 'manager' ? 'selected' : '' ?>>
                                        MANAGER</option> -->
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold small text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?= $u['email'] ?? '' ?>">
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold small text-muted">user_id / Login ID</label>
                                <input type="text" name="user_id" class="form-control"
                                    value="<?= $u['user_id'] ?? '' ?>">
                                <small class="text-muted">Used for login / User id.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold small text-muted">New Password</label>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Add a new Password">
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

    <!-- Assign MANAGER & STAFF Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-transparent shadow-none">

                <div class="card shadow-sm">

                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Add New Staff / Manager</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="card-body">

                        <form method="POST" action="update_forms_data.php">
                            <input type="hidden" name="form_type" value="create_user">

                            <div class="mb-3">
                                <label class="fw-bold small text-muted">Assign Role</label>
                                <select class="form-select" name="role">
                                    <option value="" selected disabled>Select Role</option>
                                    <option value="manager">MANAGER</option>
                                    <option value="staff">STAFF</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold small text-muted">Full Name</label>
                                <input type="text" name="full_name" class="form-control" placeholder="e.g. John Doe">
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold small text-muted">user_id / Login ID</label>
                                <input type="text" name="user_id" class="form-control" placeholder="Create a login ID">
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="fw-bold small text-muted">Password</label>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Min 8 chars, 1 Upper, 1 Lower, 1 Symbol">
                                    <small class="text-muted" style="font-size: 0.8em;">Must contain A-Z, a-z, 0-9, and
                                        a symbol.</small>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2">
                                <i class="bi bi-person-plus-fill me-2"></i> Create Account
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Team -->
    <div class="modal fade" id="viewUsersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg modal-fullscreen-sm-down">
            <div class="modal-content bg-light border-0">

                <div class="modal-header bg-white shadow-sm" style="z-index: 1050;">
                    <div>
                        <h5 class="modal-title fw-bold text-dark">User Management</h5>
                        <p class="text-muted small mb-0">Manage access and roles</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-3 p-md-4">

                    <div class="row g-3">
                        <?php
                        // SQL Query
                        $sql = "
                    (SELECT id, user_id, full_name, role, password_hash, 'managers' as source_table FROM managers)
                    UNION ALL
                    (SELECT id, user_id, full_name, role, password_hash, 'staff' as source_table FROM staff)
                    ORDER BY role ASC, id DESC
                    ";

                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Variables
                                $id = $row['id'];
                                $name = $row['full_name'];
                                $userId = $row['user_id'];
                                $role = $row['role'];
                                $tableName = $row['source_table'];

                                // Design Logic
                                if ($role == 'manager') {
                                    $borderClass = 'border-primary';
                                    $bgIcon = 'bg-primary-subtle text-primary';
                                    $roleBadge = 'text-bg-primary';
                                } else {
                                    $borderClass = 'border-success';
                                    $bgIcon = 'bg-success-subtle text-success';
                                    $roleBadge = 'text-bg-success';
                                }
                        ?>

                                <div class="col-12 col-md-6">
                                    <div
                                        class="card h-100 border-0 shadow-sm border-start border-4 <?php echo $borderClass; ?>">
                                        <div class="card-body d-flex align-items-center position-relative p-3">

                                            <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center me-3 <?php echo $bgIcon; ?>"
                                                style="width: 50px; height: 50px;">
                                                <i class="ph-fill ph-user fs-4"></i>
                                            </div>
                                            <div class="flex-grow-1 text-start">
                                                <h6 class="fw-bold mb-1 text-truncate">
                                                    <?php echo htmlspecialchars($name); ?>
                                                </h6>
                                                <div class="mb-1">
                                                    <small class="fw-bold text-muted" style="font-size: 0.75rem;">
                                                        ID: <?php echo htmlspecialchars($userId); ?>
                                                    </small>
                                                </div>
                                                <span class="badge <?php echo $roleBadge; ?> rounded-pill text-uppercase fw-bold"
                                                    style="font-size: 0.65rem;">
                                                    <?php echo htmlspecialchars($role); ?>
                                                </span>
                                            </div>


                                            <div class="flex-shrink-0 ms-2">
                                                <a href="delete_ms.php?id=<?php echo $id; ?>&table=<?php echo $tableName; ?>"
                                                    class="btn btn-light bg-white border text-danger shadow-sm rounded-3 p-2 d-flex align-items-center justify-content-center"
                                                    onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($name); ?>?');"
                                                    title="Delete User">
                                                    <i class="ph-bold ph-trash fs-5"></i>
                                                </a>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                        <?php
                            }
                        } else {
                            // Empty State
                            echo '
                        <div class="col-12 text-center py-5">
                            <div class="mb-3 text-muted opacity-50"><i class="ph-duotone ph-users fs-1"></i></div>
                            <h6 class="text-muted">No managers or staff found.</h6>
                        </div>';
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>