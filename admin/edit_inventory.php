<?php
// ======================================================
// 1. SILENT INITIALIZATION & HEADERS
// ======================================================
ob_start();

// Security Settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Include DB
require "db.php";

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Anti-Cache Headers (Prevent "Back" button after logout)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// ======================================================
// 2. THE "KILL SWITCH" HELPER (PRODUCTION MODE)
// ======================================================
function force_404_exit()
{
    // Silent fail: Destroy session and hide existence of page
    session_unset();
    session_destroy();
    header("Location: 404.php");
    exit();
}

// ======================================================
// 3. PHASE 1: USER AUTHENTICATION (Who are you?)
// ======================================================

// CHECK A: Is user logged in?
// FIX: We check 'id' because your debug showed this holds the numeric ID (23)
if (!isset($_SESSION["id"])) {
    force_404_exit();
}

// CHECK B: Session Hijacking (Browser Fingerprint)
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
} elseif ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    force_404_exit();
}

// CHECK C: Database Verification (User Role)
// FIX: Use ['id'] (23) instead of ['user_id'] ("Rohan766")
$session_db_id = $_SESSION['id'] ?? 0;

// Update query to find user by numeric ID
$user_query = $conn->prepare("SELECT id, user_id, role, email FROM users WHERE id = ? LIMIT 1");
$user_query->bind_param("i", $session_db_id);
$user_query->execute();
$u = $user_query->get_result()->fetch_assoc();
$user_query->close();

// If user missing or not Admin
if (!$u || $u['role'] !== 'admin') {
    if ($u) {
        // Log the breach attempt
        error_log("Security Breach: Non-admin " . ($u['user_id'] ?? 'unknown') . " tried to access Vehicle Editor.");
    }
    force_404_exit();
}

// ======================================================
// 4. PHASE 2: VEHICLE VALIDATION (What do you want?)
// ======================================================

// Get Vehicle ID safely from URL
$vehicle_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// CHECK D: Invalid ID format
if ($vehicle_id === 0) {
    force_404_exit();
}

// CHECK E: Does Vehicle Exist?
$veh_query = $conn->prepare("SELECT id, sold_out FROM vehicle WHERE id = ? LIMIT 1");
$veh_query->bind_param("i", $vehicle_id);
$veh_query->execute();
$result = $veh_query->get_result();
$vehicle_data = $result->fetch_assoc();
$veh_query->close();

// If vehicle does not exist in DB, kill the page
if (!$vehicle_data) {
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

</head>

<body style="height: 100dvh; overflow: hidden; padding: 0%;">

    <div class="container-fluid p-0 h-100">

        <div class="card border-0 h-100 rounded-0 overflow-hidden">

            <div id="loader"
                style="position:fixed; inset:0; background:rgba(242,242,247,0.98); z-index:9999; display:flex; justify-content:center; align-items:center; flex-direction:column;">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                <div class="mt-3 fw-bold text-secondary" style="letter-spacing: 1px;">LOADING...</div>
            </div>

            <?php
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

                    <?php
                    // ====================================================
                    // 1. FETCH DATA FROM ALL TABLES
                    // ====================================================

                    // A. MASTER VEHICLE DATA (Step 1)
                    // FIX: Changed $id to $vehicle_id
                    $stmt = $conn->prepare("SELECT * FROM vehicle WHERE id = ?");
                    $stmt->bind_param("i", $vehicle_id);
                    $stmt->execute();
                    $vehicle = $stmt->get_result()->fetch_assoc();

                    // Stop if the main vehicle doesn't exist
                    if (!$vehicle) {
                        die("<div class='alert alert-danger m-4'>❌ Error: Vehicle ID #$vehicle_id not found. <a href='inventory.php'>Go Back</a></div>");
                    }

                    // B. SELLER DATA (Step 2)
                    $stmt = $conn->prepare("SELECT * FROM vehicle_seller WHERE vehicle_id = ?");
                    $stmt->bind_param("i", $vehicle_id);
                    $stmt->execute();
                    $sellerData = $stmt->get_result()->fetch_assoc();

                    // ⭐ MERGE: Add seller data into the main $vehicle array
                    if ($sellerData) {
                        $vehicle = array_merge($vehicle, $sellerData);
                        // CRITICAL FIX: Ensure the ID remains the Vehicle ID, not the Seller ID
                        $vehicle['id'] = $vehicle_id;
                    }

                    // C. PURCHASER DATA (Step 3)
                    $stmt = $conn->prepare("SELECT * FROM vehicle_purchaser WHERE vehicle_id = ?");
                    $stmt->bind_param("i", $vehicle_id);
                    $stmt->execute();
                    $purchaserData = $stmt->get_result()->fetch_assoc();

                    // ⭐ MERGE: Add purchaser data into the main $vehicle array
                    if ($purchaserData) {
                        $vehicle = array_merge($vehicle, $purchaserData);
                        $vehicle['id'] = $vehicle_id; // Keep Vehicle ID safe
                    }

                    // D. TRANSFER / OT DATA (Step 4)
                    $stmt = $conn->prepare("SELECT * FROM vehicle_ot WHERE vehicle_id = ?");
                    $stmt->bind_param("i", $vehicle_id);
                    $stmt->execute();
                    $otData = $stmt->get_result()->fetch_assoc();

                    // ⭐ MERGE: Add OT data into the main $vehicle array
                    if ($otData) {
                        $vehicle = array_merge($vehicle, $otData);
                        $vehicle['id'] = $vehicle_id; // Keep Vehicle ID safe
                    }

                    // ====================================================
                    // 2. SET DEFAULTS (To prevent "Undefined key" errors)
                    // ====================================================

                    // Step 1 Defaults
                    $vehicle['sold_out'] = $vehicle['sold_out'] ?? 0;

                    // Step 2 Defaults (Seller)
                    $vehicle['seller_payment_type']  = $vehicle['seller_payment_type'] ?? 'Cash'; // Default to Cash
                    $vehicle['seller_online_method'] = $vehicle['seller_online_method'] ?? '';
                    $vehicle['noc_status']           = $vehicle['noc_status'] ?? 'Paid';

                    // Step 3 Defaults (Purchaser)
                    $vehicle['purchaser_payment_all_paid'] = $vehicle['purchaser_payment_all_paid'] ?? 0;
                    $vehicle['purchaser_payment_mode']     = $vehicle['purchaser_payment_mode'] ?? 'Cash';

                    // Step 4 Defaults (OT)
                    $vehicle['ot_transfer_status'] = $vehicle['ot_transfer_status'] ?? 'Due';

                    ?>


                    <!-- Upload Progress Container -->
<div id="uploadProgressContainer" class="d-none" 
     style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">

    <div style="width: 320px; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 0 50px rgba(0,0,0,0.5);">
        
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





                    <form action="vehicle_update_form.php" id="dealForm" method="POST" class="d-flex flex-column h-100" enctype="multipart/form-data" novalidate>

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

                                    <!-- Close / Back to inventory.php -->
                                    <a href="inventory.php" class="text-decoration-none">
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

                        <div class="alert alert-warning d-flex align-items-center gap-2 py-1 px-2 mb-2 small" role="alert">
                            <i class="bi bi-info-circle-fill"></i>
                            <span>
                                <strong>Notice:</strong>
                                Please press <b>Save Draft</b>, otherwise your data will <b>NOT</b> be saved.
                            </span>
                        </div>

                        <div class="flex-grow-1 overflow-y-auto p-3 p-md-5  custom-scroll">
                            <div class="container-fluid" style="max-width: 900px;">

                                <!-- 1st step -->
                                <div id="step-1" class="wizard-step fade-in-animation" style="padding-bottom: 100px;">
                                    <div class="card steps-id border-0 shadow-sm position-relative sold-wrapper rounded-4 p-3 p-md-4" style="padding-bottom: 100px;">

                                        <?php if (isset($vehicle['sold_out']) && $vehicle['sold_out'] == 1): ?>
                                            <div class="sold-stamp">SOLD OUT</div>
                                            <div class="sold-overlay"></div>
                                        <?php endif; ?>

                                        <div>
                                            <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Vehicle Details</h6>
                                            <label class="mb-2">Vehicle Photos</label>

                                            <div class="row g-3 mb-4">
                                                <?php
                                                $photoFields = ['photo1', 'photo2', 'photo3', 'photo4'];
                                                foreach ($photoFields as $key):

                                                    // Logic: Use DB image if exists, otherwise use default
                                                    if (!empty($vehicle[$key])) {
                                                        $imgSrc = "../images/" . $vehicle[$key];
                                                        $isDefault = false;
                                                    } else {
                                                        $imgSrc = "../images/default.jpg";
                                                        $isDefault = true;
                                                    }
                                                ?>
                                                    <div class="col-6 col-md-3">
                                                        <label for="file_<?= $key ?>" class="photo-upload-box position-relative d-block" style="cursor: pointer;">

                                                            <?php if ($isDefault): ?>
                                                                <i id="icon_<?= $key ?>" class="ph-bold ph-camera fs-3 text-secondary position-absolute top-50 start-50 translate-middle" style="z-index: 5;"></i>
                                                            <?php endif; ?>

                                                            <img id="preview_<?= $key ?>" src="<?= $imgSrc ?>" class="w-100 h-100 object-fit-cover rounded-3" style="display:block; min-height: 100px; background: #f8f9fa; border: 1px solid #ddd;">

                                                        </label>

                                                        <input required type="file" id="file_<?= $key ?>" name="<?= $key ?>" accept="image/*" hidden onchange="previewImage(this, '<?= $key ?>')">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label for="vehicleType" class="form-label">Vehicle Type</label>
                                                    <select id="vehicleType" name="vehicle_type" class="form-select fw-bold">
                                                        <option disabled selected>Choose Vehicle Type</option>
                                                        <?php
                                                        $types = ["Scooters", "Mopeds", "Dirt / Off-road Bikes", "Electric Bikes", "Cruiser Bikes", "Sport Bikes", "Touring Bikes", "Adventure / Dual-Sport Bikes", "Naked / Standard Bikes", "Cafe Racers", "Bobbers", "Choppers", "Pocket Bikes / Mini Bikes"];
                                                        foreach ($types as $t) {
                                                            $sel = ($vehicle['vehicle_type'] == $t) ? 'selected' : '';
                                                            echo "<option $sel>$t</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label class="fw-bold">Name</label>
                                                    <input required type="text" id="nameField" name="name" class="form-control" placeholder="Enter Name" value="<?= $vehicle['name'] ?? '' ?>">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label>Vehicle Number</label>
                                                    <input required type="text" name="vehicle_number" class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000" value="<?= $vehicle['vehicle_number'] ?? '' ?>">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label>Register Date</label>
                                                    <input required type="date" name="register_date" class="form-control" value="<?= $vehicle['register_date'] ?? '' ?>">
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <label>Owner Serial</label>
                                                    <select name="owner_serial" class="form-select">
                                                        <?php
                                                        $owners = ["1st", "2nd", "3rd", "4th", "5th"];
                                                        foreach ($owners as $o) {
                                                            $sel = ($vehicle['owner_serial'] == $o) ? 'selected' : '';
                                                            echo "<option $sel>$o</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <label>Chassis Number</label>
                                                    <input required type="text" name="chassis_number" class="form-control text-uppercase" value="<?= $vehicle['chassis_number'] ?? '' ?>">
                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <label>Engine Number</label>
                                                    <input required type="text" name="engine_number" class="form-control text-uppercase" value="<?= $vehicle['engine_number'] ?? '' ?>">
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label class="fw-bold mb-2">Payment Type</label>
                                                    <div class="d-flex gap-2 mb-3">
                                                        <input required type="radio" class="btn-check" name="payment_type" id="sp_cash" value="Cash" <?= ($vehicle['payment_type'] == 'Cash') ? 'checked' : '' ?> data-bs-toggle="collapse" data-bs-target="#cashBox">
                                                        <label class="btn btn-outline-success" for="sp_cash">Cash</label>

                                                        <input required type="radio" class="btn-check" name="payment_type" id="sp_online" value="Online" <?= ($vehicle['payment_type'] == 'Online') ? 'checked' : '' ?> data-bs-toggle="collapse" data-bs-target="#onlineBox">
                                                        <label class="btn btn-outline-primary" for="sp_online">Online</label>
                                                    </div>

                                                    <div id="payBoxes">
                                                        <div id="cashBox" class="collapse <?= ($vehicle['payment_type'] == 'Cash') ? 'show' : '' ?>" data-bs-parent="#payBoxes">
                                                            <div class="p-3 mb-3 bg-white rounded-3 border shadow-sm">
                                                                <label class="fw-bold small mb-1">Bike Price</label>
                                                                <input required type="number" name="cash_price" class="form-control form-control-sm mb-3" placeholder="Enter Amount" value="<?= $vehicle['cash_price'] ?? '' ?>">
                                                            </div>
                                                        </div>

                                                        <div id="onlineBox" class="collapse <?= ($vehicle['payment_type'] == 'Online') ? 'show' : '' ?>" data-bs-parent="#payBoxes">
                                                            <div class="p-3 mb-3 bg-white rounded-3 border shadow-sm">
                                                                <label class="fw-bold small mb-2">Select Online Method</label>
                                                                <div class="d-flex flex-wrap gap-3 mb-2">
                                                                    <?php
                                                                    $methods = ["Google Pay" => "om_gpay", "Paytm" => "om_paytm", "PhonePe" => "om_phonepe", "BharatPe" => "om_bharatpe"];
                                                                    foreach ($methods as $mName => $mId):
                                                                        $checked = ($vehicle['online_method'] == $mName) ? 'checked' : '';
                                                                    ?>
                                                                        <div class="form-check">
                                                                            <input required type="radio" class="form-check-input" name="online_method" id="<?= $mId ?>" value="<?= $mName ?>" <?= $checked ?>>
                                                                            <label class="form-check-label small fw-bold" for="<?= $mId ?>"><?= $mName ?></label>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>

                                                                <input required type="text" name="online_transaction_id" class="form-control form-control-sm mb-3 text-uppercase" placeholder="Transaction / UPI Reference ID" value="<?= $vehicle['online_transaction_id'] ?? '' ?>">

                                                                <label class="fw-bold small mb-1">Bike Price</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text bg-white border-end-0">₹</span>
                                                                    <input required type="number" name="online_price" class="form-control border-start-0 ps-0" placeholder="Enter Price" value="<?= $vehicle['online_price'] ?? '' ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="p-3 rounded-4  border">
                                                <label>Police Challan</label>
                                                <div class="d-flex gap-3 mb-2">
                                                    <div class="form-check">
                                                        <input required class="form-check-input" type="radio" name="police_challan" value="No" <?= ($vehicle['police_challan'] == 'No') ? 'checked' : '' ?> data-bs-toggle="collapse" data-bs-target="#challan-section">
                                                        <label class="form-check-label fw-bold">No</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input required class="form-check-input" type="radio" name="police_challan" value="Yes" <?= ($vehicle['police_challan'] == 'Yes') ? 'checked' : '' ?> data-bs-toggle="collapse" data-bs-target="#challan-section">
                                                        <label class="form-check-label fw-bold">Yes</label>
                                                    </div>
                                                </div>

                                                <div class="collapse mt-3 <?= ($vehicle['police_challan'] == 'Yes') ? 'show' : '' ?>" id="challan-section">
                                                    <?php for ($i = 1; $i <= 3; $i++): ?>
                                                        <div class="border rounded p-2 mb-2 bg-white">
                                                            <label class="fw-bold small">Challan <?= $i ?></label>
                                                            <div class="row g-2">
                                                                <div class="col-md-4">
                                                                    <input required type="text" name="challan<?= $i ?>_number" class="form-control text-uppercase" placeholder="Challan Number" value="<?= $vehicle["challan{$i}_number"] ?? '' ?>">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input required type="number" name="challan<?= $i ?>_amount" class="form-control" placeholder="Amount" value="<?= $vehicle["challan{$i}_amount"] ?? '' ?>">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="btn-group w-100 btn-group-sm">
                                                                        <input required type="radio" class="btn-check" name="challan<?= $i ?>_status" id="pen<?= $i ?>" value="Pending" <?= ($vehicle["challan{$i}_status"] == 'Pending') ? 'checked' : '' ?>>
                                                                        <label class="btn btn-outline-danger" for="pen<?= $i ?>">Pending</label>

                                                                        <input required type="radio" class="btn-check" name="challan<?= $i ?>_status" id="paid<?= $i ?>" value="Paid" <?= ($vehicle["challan{$i}_status"] == 'Paid') ? 'checked' : '' ?>>
                                                                        <label class="btn btn-outline-success" for="paid<?= $i ?>">Paid</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>

                                            <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between">
                                                <label class="form-check-label fw-bold text-danger mb-0" for="soldToggle">Mark as Sold Out</label>
                                                <div class="form-check form-switch">
                                                    <input required class="form-check-input" type="checkbox" id="soldToggle" name="sold_out" value="1" <?= ($vehicle['sold_out'] == 1) ? 'checked' : '' ?> style="width: 3em; height: 1.5em;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- 2nd step -->
                                <div id="step-2" class="wizard-step d-none" style="padding-bottom: 100px;">
                                    <div class="card steps-id border-0 shadow-sm position-relative sold-wrapper rounded-4 p-3 p-md-4" style="padding-bottom: 100px;">
                                        <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Seller Details</h6>

                                        <input type="hidden" name="vehicle_id" value="<?= $id ?>">

                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-4">
                                                <label>Date</label>
                                                <input type="date" name="seller_date" class="form-control" value="<?= $vehicle['seller_date'] ?? '' ?>">
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Vehicle No</label>
                                                <input type="text" name="seller_vehicle_number"
                                                    class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000"
                                                    value="<?= $vehicle['seller_vehicle_number'] ?? '' ?>">
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Bike Name</label>
                                                <input type="text" name="seller_bike_name" class="form-control text-uppercase" value="<?= $vehicle['seller_bike_name'] ?? '' ?>">
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label>Chassis No</label>
                                                <input type="text" name="seller_chassis_no" class="form-control text-uppercase" value="<?= $vehicle['seller_chassis_no'] ?? '' ?>">
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label>Engine No</label>
                                                <input type="text" name="seller_engine_no" class="form-control text-uppercase" value="<?= $vehicle['seller_engine_no'] ?? '' ?>">
                                            </div>

                                            <div class="col-12">
                                                <label>Seller Name</label>
                                                <input type="text" name="seller_name" class="form-control text-uppercase" value="<?= $vehicle['seller_name'] ?? '' ?>">
                                            </div>

                                            <div class="col-12">
                                                <label>Address</label>
                                                <textarea name="seller_address" class="form-control text-uppercase" rows="2"><?= $vehicle['seller_address'] ?? '' ?></textarea>
                                            </div>
                                        </div>

                                        <label class="mb-2">Mobile Numbers</label>
                                        <div class="row g-2 mb-3">
                                            <div class="col-12"><input type="tel" name="seller_mobile1" class="form-control" placeholder="Mob 1" value="<?= $vehicle['seller_mobile1'] ?? '' ?>"></div>
                                            <div class="col-12"><input type="tel" name="seller_mobile2" class="form-control" placeholder="Mob 2" value="<?= $vehicle['seller_mobile2'] ?? '' ?>"></div>
                                            <div class="col-12"><input type="tel" name="seller_mobile3" class="form-control" placeholder="Mob 3" value="<?= $vehicle['seller_mobile3'] ?? '' ?>"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="mb-2 fw-bold text-dark">Purchaser Documents (In Seller Step)</label>
                                            <div class="row g-3">
                                                <?php
                                                $docs = [
                                                    'doc_aadhar_front' => 'Aadhar Front',
                                                    'doc_aadhar_back'  => 'Aadhar Back',
                                                    'doc_voter_front'  => 'Voter Front',
                                                    'doc_voter_back'   => 'Voter Back'
                                                ];

                                                foreach ($docs as $key => $label):
                                                    // 1. Get Filename & Path
                                                    $dbFile = !empty($vehicle[$key]) ? $vehicle[$key] : "";
                                                    $fullPath = !empty($dbFile) ? "../images/" . $dbFile : "#";

                                                    // 2. Format Filename for Display (Truncate if too long)
                                                    $displayName = !empty($dbFile) ? $dbFile : "No file uploaded";
                                                    if (strlen($displayName) > 15) {
                                                        $displayName = substr($displayName, 0, 12) . "...";
                                                    }

                                                    // 3. Button Status & Icons
                                                    $viewClass = !empty($dbFile) ? "btn-outline-primary" : "btn-outline-secondary disabled";
                                                    $statusColor = !empty($dbFile) ? "text-success" : "text-muted";
                                                    $icon = !empty($dbFile) ? "ph-file-image" : "ph-file";
                                                ?>
                                                    <div class="col-12 col-md-3">
                                                        <div class="card h-100 shadow-sm border-0 ">
                                                            <div class="card-body p-2 d-flex flex-column">

                                                                <span class="small fw-bold text-secondary mb-2"><?= $label ?></span>

                                                                <div class="bg-white border rounded p-2 mb-2 d-flex align-items-center gap-2 flex-grow-1">
                                                                    <i class="ph-bold <?= $icon ?> fs-4 <?= $statusColor ?>"></i>
                                                                    <span id="filename_<?= $key ?>" class="small fw-bold text-truncate" style="max-width: 100%;" title="<?= $dbFile ?>">
                                                                        <?= $displayName ?>
                                                                    </span>
                                                                </div>

                                                                <div class="d-flex gap-2">
                                                                    <button type="button" class="btn btn-sm btn-dark flex-grow-1 d-flex align-items-center justify-content-center gap-1" onclick="document.getElementById('<?= $key ?>').click();">
                                                                        <i class="ph-bold ph-upload-simple"></i>
                                                                        <span class="small">Upload</span>
                                                                    </button>

                                                                    <a href="<?= $fullPath ?>" target="_blank" id="view_<?= $key ?>" class="btn btn-sm <?= $viewClass ?>">
                                                                        <i class="ph-bold ph-eye"></i>
                                                                    </a>
                                                                </div>

                                                                <input type="file" id="<?= $key ?>" name="<?= $key ?>" accept="image/*" hidden onchange="updateFileName(this)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class=" p-3 rounded-4 border mb-3">
                                            <label class="mb-2 fw-bold">Papers Received</label>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="form-check">
                                                    <input type="checkbox" name="pr_rc" class="form-check-input" id="pr_rc"
                                                        <?= (isset($vehicle['pr_rc']) && $vehicle['pr_rc'] == 1) ? 'checked' : '' ?>
                                                        data-bs-toggle="collapse" data-bs-target="#rcUploadBox">
                                                    <label class="fw-bold" for="pr_rc">RC</label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" name="pr_tax" class="form-check-input" id="pr_tax"
                                                        <?= (isset($vehicle['pr_tax']) && $vehicle['pr_tax'] == 1) ? 'checked' : '' ?>>
                                                    <label class="fw-bold" for="pr_tax">Tax Token</label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" name="pr_insurance" class="form-check-input" id="pr_ins"
                                                        <?= (isset($vehicle['pr_insurance']) && $vehicle['pr_insurance'] == 1) ? 'checked' : '' ?>>
                                                    <label class="fw-bold" for="pr_ins">Insurance</label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" name="pr_pucc" class="form-check-input" id="pr_puc"
                                                        <?= (isset($vehicle['pr_pucc']) && $vehicle['pr_pucc'] == 1) ? 'checked' : '' ?>>
                                                    <label class="fw-bold" for="pr_puc">PUCC</label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" name="pr_noc" class="form-check-input" id="pr_noc"
                                                        <?= (isset($vehicle['pr_noc']) && $vehicle['pr_noc'] == 1) ? 'checked' : '' ?>
                                                        data-bs-toggle="collapse" data-bs-target="#nocUploadBox">
                                                    <label class="fw-bold" for="pr_noc">NOC</label>
                                                </div>
                                            </div>

                                            <div class="collapse mt-3 <?= (isset($vehicle['pr_rc']) && $vehicle['pr_rc'] == 1) ? 'show' : '' ?>" id="rcUploadBox">
                                                <label class="mb-2 fw-bold text-dark">RC Upload</label>
                                                <div class="row g-3">
                                                    <?php
                                                    $rcs = ['rc_front' => 'RC FRONT', 'rc_back' => 'RC BACK'];

                                                    foreach ($rcs as $key => $label):
                                                        // 1. Get Filename & Path
                                                        $dbFile = !empty($vehicle[$key]) ? $vehicle[$key] : "";
                                                        $fullPath = !empty($dbFile) ? "../images/" . $dbFile : "#";

                                                        // 2. Format Filename for Display (Truncate if too long)
                                                        $displayName = !empty($dbFile) ? $dbFile : "No file uploaded";
                                                        if (strlen($displayName) > 15) {
                                                            $displayName = substr($displayName, 0, 12) . "...";
                                                        }

                                                        // 3. Button Status & Icons
                                                        $viewClass = !empty($dbFile) ? "btn-outline-primary" : "btn-outline-secondary disabled";
                                                        $statusColor = !empty($dbFile) ? "text-success" : "text-muted";
                                                        $icon = !empty($dbFile) ? "ph-file-image" : "ph-file";
                                                    ?>
                                                        <div class="col-12 col-md-3">
                                                            <div class="card h-100 shadow-sm border-0 ">
                                                                <div class="card-body p-2 d-flex flex-column">

                                                                    <span class="small fw-bold text-secondary mb-2"><?= $label ?></span>

                                                                    <div class="bg-white border rounded p-2 mb-2 d-flex align-items-center gap-2 flex-grow-1">
                                                                        <i class="ph-bold <?= $icon ?> fs-4 <?= $statusColor ?>"></i>
                                                                        <span id="filename_<?= $key ?>" class="small fw-bold text-truncate" style="max-width: 100%;" title="<?= $dbFile ?>">
                                                                            <?= $displayName ?>
                                                                        </span>
                                                                    </div>

                                                                    <div class="d-flex gap-2">
                                                                        <button type="button" class="btn btn-sm btn-dark flex-grow-1 d-flex align-items-center justify-content-center gap-1" onclick="document.getElementById('<?= $key ?>').click();">
                                                                            <i class="ph-bold ph-upload-simple"></i>
                                                                            <span class="small">Upload</span>
                                                                        </button>

                                                                        <a href="<?= $fullPath ?>" target="_blank" id="view_<?= $key ?>" class="btn btn-sm <?= $viewClass ?>">
                                                                            <i class="ph-bold ph-eye"></i>
                                                                        </a>
                                                                    </div>

                                                                    <input type="file" id="<?= $key ?>" name="<?= $key ?>" accept="image/*" hidden onchange="updateFileName(this)">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <div class="collapse mt-3 <?= (isset($vehicle['pr_noc']) && $vehicle['pr_noc'] == 1) ? 'show' : '' ?>" id="nocUploadBox">
                                                <label class="fw-bold small">NOC Status</label>
                                                <div class="d-flex justify-content-center">
                                                    <div class="btn-group w-75 btn-group-sm mb-3 mx-auto" role="group">
                                                        <input type="radio" name="noc_status" class="btn-check" id="noc_paid" value="Paid"
                                                            <?= ($vehicle['noc_status'] == 'Paid') ? 'checked' : '' ?>>
                                                        <label class="btn btn-outline-success" for="noc_paid">Paid</label>

                                                        <input type="radio" name="noc_status" class="btn-check" id="noc_due" value="Due"
                                                            <?= ($vehicle['noc_status'] == 'Due') ? 'checked' : '' ?>>
                                                        <label class="btn btn-outline-danger" for="noc_due">Due</label>
                                                    </div>
                                                </div>

                                                <div class="row g-3">
                                                    <?php
                                                    $nocs = ['noc_front' => 'NOC Front', 'noc_back' => 'NOC Back'];

                                                    foreach ($nocs as $key => $label):
                                                        // 1. Get Filename & Path
                                                        $dbFile = !empty($vehicle[$key]) ? $vehicle[$key] : "";
                                                        $fullPath = !empty($dbFile) ? "../images/" . $dbFile : "#";

                                                        // 2. Format Filename for Display (Truncate if too long)
                                                        $displayName = !empty($dbFile) ? $dbFile : "No file uploaded";
                                                        if (strlen($displayName) > 15) {
                                                            $displayName = substr($displayName, 0, 12) . "...";
                                                        }

                                                        // 3. Button Status & Icons
                                                        $viewClass = !empty($dbFile) ? "btn-outline-primary" : "btn-outline-secondary disabled";
                                                        $statusColor = !empty($dbFile) ? "text-success" : "text-muted";
                                                        $icon = !empty($dbFile) ? "ph-file-image" : "ph-file";
                                                    ?>
                                                        <div class="col-12 col-md-3">
                                                            <div class="card h-100 shadow-sm border-0 ">
                                                                <div class="card-body p-2 d-flex flex-column">

                                                                    <span class="small fw-bold text-secondary mb-2"><?= $label ?></span>

                                                                    <div class="bg-white border rounded p-2 mb-2 d-flex align-items-center gap-2 flex-grow-1">
                                                                        <i class="ph-bold <?= $icon ?> fs-4 <?= $statusColor ?>"></i>
                                                                        <span id="filename_<?= $key ?>" class="small fw-bold text-truncate" style="max-width: 100%;" title="<?= $dbFile ?>">
                                                                            <?= $displayName ?>
                                                                        </span>
                                                                    </div>

                                                                    <div class="d-flex gap-2">
                                                                        <button type="button" class="btn btn-sm btn-dark flex-grow-1 d-flex align-items-center justify-content-center gap-1" onclick="document.getElementById('<?= $key ?>').click();">
                                                                            <i class="ph-bold ph-upload-simple"></i>
                                                                            <span class="small">Upload</span>
                                                                        </button>

                                                                        <a href="<?= $fullPath ?>" target="_blank" id="view_<?= $key ?>" class="btn btn-sm <?= $viewClass ?>">
                                                                            <i class="ph-bold ph-eye"></i>
                                                                        </a>
                                                                    </div>

                                                                    <input type="file" id="<?= $key ?>" name="<?= $key ?>" accept="image/*" hidden onchange="updateFileName(this)">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label class="fw-bold mb-2">Payment Type</label>
                                                <div class="d-flex gap-2 mb-3">
                                                    <input type="radio" name="seller_payment_type" class="btn-check" id="pay_cash" value="Cash"
                                                        <?= ($vehicle['seller_payment_type'] == 'Cash') ? 'checked' : '' ?>
                                                        data-bs-toggle="collapse" data-bs-target="#cashBox">
                                                    <label class="btn btn-outline-success" for="pay_cash">Cash</label>

                                                    <input type="radio" name="seller_payment_type" class="btn-check" id="pay_online" value="Online"
                                                        <?= ($vehicle['seller_payment_type'] == 'Online') ? 'checked' : '' ?>
                                                        data-bs-toggle="collapse" data-bs-target="#onlineBox">
                                                    <label class="btn btn-outline-primary" for="pay_online">Online</label>
                                                </div>

                                                <div id="payAccordion">
                                                    <div id="cashBox" class="collapse <?= ($vehicle['seller_payment_type'] == 'Cash') ? 'show' : '' ?>" data-bs-parent="#payAccordion">
                                                        <div class="p-3 bg-white border rounded shadow-sm">
                                                            <label class="fw-bold small mb-1">Price</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white border-end-0">₹</span>
                                                                <input type="number" name="seller_cash_price" class="form-control border-start-0 ps-0" placeholder="Enter Price" value="<?= $vehicle['seller_cash_price'] ?? '' ?>">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="onlineBox" class="collapse <?= ($vehicle['seller_payment_type'] == 'Online') ? 'show' : '' ?>" data-bs-parent="#payAccordion">
                                                        <div class="p-3 bg-white border rounded shadow-sm">
                                                            <label class="fw-bold small mb-2">Online Method</label>
                                                            <div class="d-flex flex-wrap gap-3 mb-3">
                                                                <label class="form-check">
                                                                    <input type="radio" name="seller_online_method" class="form-check-input" value="Google Pay"
                                                                        <?= ($vehicle['seller_online_method'] == 'Google Pay') ? 'checked' : '' ?>>
                                                                    <span class="form-check-label fw-bold">Google Pay</span>
                                                                </label>
                                                                <label class="form-check">
                                                                    <input type="radio" name="seller_online_method" class="form-check-input" value="Paytm"
                                                                        <?= ($vehicle['seller_online_method'] == 'Paytm') ? 'checked' : '' ?>>
                                                                    <span class="form-check-label fw-bold">Paytm</span>
                                                                </label>
                                                                <label class="form-check">
                                                                    <input type="radio" name="seller_online_method" class="form-check-input" value="PhonePe"
                                                                        <?= ($vehicle['seller_online_method'] == 'PhonePe') ? 'checked' : '' ?>>
                                                                    <span class="form-check-label fw-bold">PhonePe</span>
                                                                </label>
                                                                <label class="form-check">
                                                                    <input type="radio" name="seller_online_method" class="form-check-input" value="BharatPe"
                                                                        <?= ($vehicle['seller_online_method'] == 'BharatPe') ? 'checked' : '' ?>>
                                                                    <span class="form-check-label fw-bold">BharatPe</span>
                                                                </label>
                                                            </div>

                                                            <input type="text" name="seller_online_transaction_id" class="form-control form-control-sm mb-3 text-uppercase" placeholder="Transaction / UPI Reference ID" value="<?= $vehicle['seller_online_transaction_id'] ?? '' ?>">

                                                            <label class="fw-bold small mb-1">Price</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white border-end-0">₹</span>
                                                                <input type="number" name="seller_online_price" class="form-control border-start-0 ps-0" placeholder="Enter Price" value="<?= $vehicle['seller_online_price'] ?? '' ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label>Exchange Showroom Name</label>
                                                <input type="text" name="exchange_showroom_name" class="form-control text-uppercase" placeholder="Showroom Name" value="<?= $vehicle['exchange_showroom_name'] ?? '' ?>">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label>Staff Name</label>
                                                <input type="text" name="staff_name" class="form-control text-uppercase" placeholder="Staff Name" value="<?= $vehicle['staff_name'] ?? '' ?>">
                                            </div>
                                        </div>

                                        <div class="bg-white p-4 rounded-4 border shadow-sm">
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 rounded-pill">
                                                    Payment Calculation
                                                </span>
                                            </div>

                                            <div class="row g-3">

                                                <!-- Total Amount -->
                                                <div class="col-12">
                                                    <label class="form-label text-primary fw-semibold mb-1">Total Amount</label>
                                                    <input type="number"
                                                        name="total_amount"
                                                        class="form-control border-primary"
                                                        placeholder="Enter total amount"
                                                        id="s_total"
                                                        value="<?= $vehicle['total_amount'] ?? '' ?>">
                                                </div>

                                                <!-- Paid Amount -->
                                                <div class="col-12">
                                                    <label class="form-label text-success fw-semibold mb-1">Paid Amount</label>
                                                    <input type="number"
                                                        name="paid_amount"
                                                        class="form-control border-success"
                                                        placeholder="Enter paid amount"
                                                        id="s_paid"
                                                        value="<?= $vehicle['paid_amount'] ?? '' ?>">
                                                </div>

                                                <!-- Due Amount -->
                                                <div class="col-12">
                                                    <label class="form-label text-danger fw-semibold mb-1">Due Amount</label>
                                                    <input type="number"
                                                        name="due_amount"
                                                        class="form-control bg-danger-subtle fw-bold text-danger border-danger"
                                                        placeholder="Due amount"
                                                        id="s_due"
                                                        readonly
                                                        value="<?= $vehicle['due_amount'] ?? '' ?>">
                                                </div>

                                                <!-- Due Reason -->
                                                <div class="col-12">
                                                    <input type="text"
                                                        name="due_reason"
                                                        class="form-control mt-1 <?= (!empty($vehicle['due_amount']) && $vehicle['due_amount'] > 0) ? '' : 'd-none' ?>"
                                                        id="s_due_reason"
                                                        placeholder="Reason for due amount..."
                                                        value="<?= $vehicle['due_reason'] ?? '' ?>">
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- 3rd step -->
                                <div id="step-3" class="wizard-step d-none" style="padding-bottom: 100px;">
                                    <div class="card steps-id border-0 shadow-sm position-relative sold-wrapper rounded-4 p-3 p-md-4" style="padding-bottom: 100px;">
                                        <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Purchaser Details</h6>

                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label>Date</label>
                                                <input type="date" name="purchaser_date" class="form-control" value="<?= $vehicle['purchaser_date'] ?? date('Y-m-d') ?>">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label>Purchaser Name</label>
                                                <input type="text" name="purchaser_name" class="form-control text-uppercase" value="<?= $vehicle['purchaser_name'] ?? '' ?>">
                                            </div>
                                            <div class="col-12">
                                                <label>Address</label>
                                                <textarea name="purchaser_address" class="form-control text-uppercase" rows="2"><?= $vehicle['purchaser_address'] ?? '' ?></textarea>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label>Bike Name</label>
                                                <input type="text" name="purchaser_bike_name" class="form-control text-uppercase"
                                                    value="<?= !empty($vehicle['purchaser_bike_name']) ? $vehicle['purchaser_bike_name'] : ($vehicle['name'] ?? '') ?>">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label>Vehicle No</label>
                                                <input type="text" name="purchaser_vehicle_no" class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000"
                                                    value="<?= !empty($vehicle['purchaser_vehicle_no']) ? $vehicle['purchaser_vehicle_no'] : ($vehicle['vehicle_number'] ?? 'WB ') ?>">
                                            </div>
                                        </div>

                                        <div class=" p-3 rounded-4 border mb-4">
                                            <label class="text-dark mb-3 d-block">Purchaser Paper Payment Fees</label>

                                            <div class="row g-2 align-items-end mb-3">
                                                <div class="col-12 col-md-4">
                                                    <label>Transfer Amount</label>
                                                    <input type="number" name="purchaser_transfer_amount" class="form-control" placeholder="Amount" value="<?= $vehicle['purchaser_transfer_amount'] ?? '' ?>">
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <label>Date</label>
                                                    <input type="date" name="purchaser_transfer_date" class="form-control" value="<?= $vehicle['purchaser_transfer_date'] ?? '' ?>">
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <label>Status</label>
                                                    <select name="purchaser_transfer_status" class="form-select">
                                                        <option value="Paid" <?= (isset($vehicle['purchaser_transfer_status']) && $vehicle['purchaser_transfer_status'] == 'Paid') ? 'selected' : '' ?>>Paid</option>
                                                        <option value="Due" <?= (isset($vehicle['purchaser_transfer_status']) && $vehicle['purchaser_transfer_status'] == 'Due') ? 'selected' : '' ?>>Due</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row g-2 align-items-end mb-3">
                                                <div class="col-12 col-md-4">
                                                    <label>HPA</label>
                                                    <input type="number" name="purchaser_hpa_amount" class="form-control" placeholder="Amount" value="<?= $vehicle['purchaser_hpa_amount'] ?? '' ?>">
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <label>Date</label>
                                                    <input type="date" name="purchaser_hpa_date" class="form-control" value="<?= $vehicle['purchaser_hpa_date'] ?? '' ?>">
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <label>Status</label>
                                                    <select name="purchaser_hpa_status" class="form-select">
                                                        <option value="Paid" <?= (isset($vehicle['purchaser_hpa_status']) && $vehicle['purchaser_hpa_status'] == 'Paid') ? 'selected' : '' ?>>Paid</option>
                                                        <option value="Due" <?= (isset($vehicle['purchaser_hpa_status']) && $vehicle['purchaser_hpa_status'] == 'Due') ? 'selected' : '' ?>>Due</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row g-2 align-items-end mb-3">
                                                <div class="col-12 col-md-4">
                                                    <label>HP</label>
                                                    <input type="number" name="purchaser_hp_amount" class="form-control" placeholder="Amount" value="<?= $vehicle['purchaser_hp_amount'] ?? '' ?>">
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <label>Date</label>
                                                    <input type="date" name="purchaser_hp_date" class="form-control" value="<?= $vehicle['purchaser_hp_date'] ?? '' ?>">
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <label>Status</label>
                                                    <select name="purchaser_hp_status" class="form-select">
                                                        <option value="Paid" <?= (isset($vehicle['purchaser_hp_status']) && $vehicle['purchaser_hp_status'] == 'Paid') ? 'selected' : '' ?>>Paid</option>
                                                        <option value="Due" <?= (isset($vehicle['purchaser_hp_status']) && $vehicle['purchaser_hp_status'] == 'Due') ? 'selected' : '' ?>>Due</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-md-3">
                                                    <label class="fw-bold">Insurance Name</label>
                                                    <select name="purchaser_insurance_name" class="form-control text-uppercase" >
                                                        <option value="">-- Select Insurance --</option>
                                                        <?php
                                                        $ins_companies = [
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
                                                        foreach ($ins_companies as $ins) {
                                                            $selected = (isset($vehicle['purchaser_insurance_name']) && $vehicle['purchaser_insurance_name'] == $ins) ? 'selected' : '';
                                                            echo "<option value='$ins' $selected>$ins</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="fw-bold">Payment Status</label>
                                                    <select name="purchaser_insurance_payment_status" class="form-control" >
                                                        <option value="">-- Select Status --</option>
                                                        <option value="paid" <?= (isset($vehicle['purchaser_insurance_payment_status']) && $vehicle['purchaser_insurance_payment_status'] == 'paid') ? 'selected' : '' ?>>Paid</option>
                                                        <option value="due" <?= (isset($vehicle['purchaser_insurance_payment_status']) && $vehicle['purchaser_insurance_payment_status'] == 'due') ? 'selected' : '' ?>>Due</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="fw-bold">Amount</label>
                                                    <input type="number" name="purchaser_insurance_amount" class="form-control" placeholder="Enter Amount"  value="<?= $vehicle['purchaser_insurance_amount'] ?? '' ?>">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="fw-bold">Issue Date</label>
                                                    <input type="date" name="purchaser_insurance_issue_date" class="form-control" id="issueDate"  value="<?= $vehicle['purchaser_insurance_issue_date'] ?? '' ?>">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="fw-bold">Expiry Date</label>
                                                    <input type="date" name="purchaser_insurance_expiry_date" class="form-control" id="expiryDate" readonly  value="<?= $vehicle['purchaser_insurance_expiry_date'] ?? '' ?>">
                                                </div>

                                                <span class="fw-bold text-primary">Validity:<span id="expiryText">--</span></span>
                                            </div>
                                        </div>

                                        <div class=" p-3 rounded-4 border mb-3">
                                            <label class="mb-2 fw-bold">Price Breakdown</label>
                                            <div class="bg-white p-4 rounded-4 border shadow-sm mb-3">

                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="badge bg-info-subtle text-info fw-semibold px-3 py-2 rounded-pill">
                                                        Purchaser Payment
                                                    </span>
                                                </div>

                                                <div class="row g-3">

                                                    <!-- Total -->
                                                    <div class="col-12">
                                                        <label class="form-label text-primary fw-semibold mb-1">Total Amount</label>
                                                        <input type="number"
                                                            name="purchaser_total"
                                                            id="p_total"
                                                            class="form-control border-primary"
                                                            placeholder="Enter total amount"
                                                            value="<?= $vehicle['purchaser_total'] ?? '' ?>">
                                                    </div>

                                                    <!-- Paid -->
                                                    <div class="col-12">
                                                        <label class="form-label text-success fw-semibold mb-1">Paid Amount</label>
                                                        <input type="number"
                                                            name="purchaser_paid"
                                                            id="p_paid"
                                                            class="form-control border-success"
                                                            placeholder="Enter paid amount"
                                                            value="<?= $vehicle['purchaser_paid'] ?? '' ?>">
                                                    </div>

                                                    <!-- Due -->
                                                    <div class="col-12">
                                                        <label class="form-label text-danger fw-semibold mb-1">Due Amount</label>
                                                        <input type="number"
                                                            name="purchaser_due"
                                                            id="p_due"
                                                            class="form-control bg-danger-subtle fw-bold text-danger border-danger"
                                                            placeholder="Due amount"
                                                            readonly
                                                            value="<?= $vehicle['purchaser_due'] ?? '' ?>">
                                                    </div>

                                                </div>
                                            </div>


                                            <div class="d-flex gap-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="purchaser_payment_mode" id="rad_cash" value="Cash"
                                                        <?= (!isset($vehicle['purchaser_payment_mode']) || $vehicle['purchaser_payment_mode'] == 'Cash') ? 'checked' : '' ?>>
                                                    <label class="fw-bold" for="rad_cash" data-bs-toggle="collapse" data-bs-target="#sec_cash" role="button">
                                                        Cash
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="purchaser_payment_mode" id="rad_fin" value="Finance"
                                                        <?= (isset($vehicle['purchaser_payment_mode']) && $vehicle['purchaser_payment_mode'] == 'Finance') ? 'checked' : '' ?>>
                                                    <label class="fw-bold" for="rad_fin" data-bs-toggle="collapse" data-bs-target="#sec_finance" role="button">
                                                        Finance
                                                    </label>
                                                </div>
                                            </div>

                                            <div id="payment_details_group">
                                                <div id="sec_cash" class="collapse <?= (!isset($vehicle['purchaser_payment_mode']) || $vehicle['purchaser_payment_mode'] == 'Cash') ? 'show' : '' ?> border-top pt-2 mt-2" data-bs-parent="#payment_details_group">
                                                    <div class="row g-2">
                                                        <div class="col-12">
                                                            <label>Amount</label>
                                                            <input type="number" name="purchaser_cash_amount" class="form-control" placeholder="Enter Amount" value="<?= $vehicle['purchaser_cash_amount'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12">
                                                            <label>Mobile Number 1</label>
                                                            <input type="tel" name="purchaser_cash_mobile1" class="form-control" placeholder="Enter Mobile Number" value="<?= $vehicle['purchaser_cash_mobile1'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12">
                                                            <label>Mobile Number 2</label>
                                                            <input type="tel" name="purchaser_cash_mobile2" class="form-control" placeholder="Enter Mobile Number" value="<?= $vehicle['purchaser_cash_mobile2'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12">
                                                            <label>Mobile Number 3</label>
                                                            <input type="tel" name="purchaser_cash_mobile3" class="form-control" placeholder="Enter Mobile Number" value="<?= $vehicle['purchaser_cash_mobile3'] ?? '' ?>">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="sec_finance" class="collapse <?= (isset($vehicle['purchaser_payment_mode']) && $vehicle['purchaser_payment_mode'] == 'Finance') ? 'show' : '' ?> border-top pt-2 mt-2" data-bs-parent="#payment_details_group">
                                                    <div class="row g-2">
                                                        <div class="col-12">
                                                            <label>HPA With</label>
                                                            <input type="text" name="purchaser_fin_hpa_with" class="form-control text-uppercase" placeholder="Finance Company" value="<?= $vehicle['purchaser_fin_hpa_with'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12">
                                                            <label>Disburse Amount</label>
                                                            <div class="input-group">
                                                                <input type="number" name="purchaser_fin_disburse_amount" class="form-control" placeholder="Amt" value="<?= $vehicle['purchaser_fin_disburse_amount'] ?? '' ?>">
                                                                <select name="purchaser_fin_disburse_status" class="form-select" style="max-width:100px;">
                                                                    <option value="Paid" <?= (isset($vehicle['purchaser_fin_disburse_status']) && $vehicle['purchaser_fin_disburse_status'] == 'Paid') ? 'selected' : '' ?>>Paid</option>
                                                                    <option value="Due" <?= (isset($vehicle['purchaser_fin_disburse_status']) && $vehicle['purchaser_fin_disburse_status'] == 'Due') ? 'selected' : '' ?>>Due</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label>Mobile Number 1</label>
                                                            <input type="tel" name="purchaser_fin_mobile1" class="form-control" placeholder="Mobile 1" value="<?= $vehicle['purchaser_fin_mobile1'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12">
                                                            <label>Mobile Number 2</label>
                                                            <input type="tel" name="purchaser_fin_mobile2" class="form-control" placeholder="Mobile 2" value="<?= $vehicle['purchaser_fin_mobile2'] ?? '' ?>">
                                                        </div>
                                                        <div class="col-12">
                                                            <label>Mobile Number 3</label>
                                                            <input type="tel" name="purchaser_fin_mobile3" class="form-control" placeholder="Mobile 3" value="<?= $vehicle['purchaser_fin_mobile3'] ?? '' ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="mb-2 fw-bold text-dark">Purchaser Documents</label>
                                            <div class="row g-3">
                                                <?php
                                                $p_docs = [
                                                    'purchaser_doc_aadhar_front' => 'Aadhar Front',
                                                    'purchaser_doc_aadhar_back'  => 'Aadhar Back',
                                                    'purchaser_doc_voter_front'  => 'Voter Front',
                                                    'purchaser_doc_voter_back'   => 'Voter Back'
                                                ];

                                                foreach ($p_docs as $key => $label):
                                                    // 1. Get Filename & Path
                                                    $dbFile = !empty($vehicle[$key]) ? $vehicle[$key] : "";
                                                    $fullPath = !empty($dbFile) ? "../images/" . $dbFile : "#";

                                                    // 2. Format Filename for Display (Truncate if too long)
                                                    $displayName = !empty($dbFile) ? $dbFile : "No file uploaded";
                                                    if (strlen($displayName) > 15) {
                                                        $displayName = substr($displayName, 0, 12) . "...";
                                                    }

                                                    // 3. Button Status
                                                    $viewClass = !empty($dbFile) ? "btn-outline-primary" : "btn-outline-secondary disabled";
                                                    $statusColor = !empty($dbFile) ? "text-success" : "text-muted";
                                                    $icon = !empty($dbFile) ? "ph-file-image" : "ph-file";
                                                ?>
                                                    <div class="col-12 col-md-3">
                                                        <div class="card h-100 shadow-sm border-0 ">
                                                            <div class="card-body p-2 d-flex flex-column">

                                                                <span class="small fw-bold text-secondary mb-2"><?= $label ?></span>

                                                                <div class="bg-white border rounded p-2 mb-2 d-flex align-items-center gap-2 flex-grow-1">
                                                                    <i class="ph-bold <?= $icon ?> fs-4 <?= $statusColor ?>"></i>
                                                                    <span id="filename_<?= $key ?>" class="small fw-bold text-truncate" style="max-width: 100%;" title="<?= $dbFile ?>">
                                                                        <?= $displayName ?>
                                                                    </span>
                                                                </div>

                                                                <div class="d-flex gap-2">
                                                                    <button type="button" class="btn btn-sm btn-dark flex-grow-1 d-flex align-items-center justify-content-center gap-1" onclick="document.getElementById('<?= $key ?>').click();">
                                                                        <i class="ph-bold ph-upload-simple"></i>
                                                                        <span class="small">Upload</span>
                                                                    </button>

                                                                    <a href="<?= $fullPath ?>" target="_blank" id="view_<?= $key ?>" class="btn btn-sm <?= $viewClass ?>">
                                                                        <i class="ph-bold ph-eye"></i>
                                                                    </a>
                                                                </div>

                                                                <input type="file" id="<?= $key ?>" name="<?= $key ?>" accept="image/*" hidden onchange="updateFileName(this)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>


                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="purchaser_payment_all_paid" id="all_paid" value="1"
                                                <?= (isset($vehicle['purchaser_payment_all_paid']) && $vehicle['purchaser_payment_all_paid'] == 1) ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-bold text-success" for="all_paid">Payment All Paid</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- 4rd step -->
                                <div id="step-4" class="wizard-step d-none" style="padding-bottom: 100px;">
                                    <div class="card steps-id border-0 shadow-sm position-relative sold-wrapper rounded-4 " style="padding-bottom: 100px;">
                                        <h6 class="fw-bold text-primary m-4 mb-3 text-uppercase ls-1">Ownership Transfer</h6>

                                        <div class="border rounded-4 mb-4" style="padding: 10px;">
                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-4">
                                                    <label>Name Transfer</label>
                                                    <input type="text" class="form-control text-uppercase" placeholder="Enter Name" name="ot_name_transfer" value="<?= $vehicle['ot_name_transfer'] ?? '' ?>">
                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <label>Vehicle Number</label>
                                                    <input type="text" class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000" name="ot_vehicle_number"
                                                        value="<?= !empty($vehicle['ot_vehicle_number']) ? $vehicle['ot_vehicle_number'] : ($vehicle['vehicle_number'] ?? 'WB ') ?>">
                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <label>RTO Name</label>
                                                    <select class="form-select" name="ot_rto_name">
                                                        <?php
                                                        $rtos = ["Bankura", "Bishnupur", "Durgapur", "Manbazar", "Suri", "Asansol", "Kalimpong"];
                                                        foreach ($rtos as $rto) {
                                                            $selected = (isset($vehicle['ot_rto_name']) && $vehicle['ot_rto_name'] == $rto) ? 'selected' : '';
                                                            echo "<option $selected>$rto</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-4">
                                                <div class="col-12 col-md-6">
                                                    <label>Vendor Name</label>
                                                    <input type="text" class="form-control text-uppercase" placeholder="Vendor Name" name="ot_vendor_name" value="<?= $vehicle['ot_vendor_name'] ?? '' ?>">
                                                </div>
                                            </div>

                                            <div class=" p-3 rounded-4 border mb-4">
                                                <label class="text-dark mb-3 d-block">Vendor Payment</label>

                                                <div class="row g-2 align-items-end mb-3">
                                                    <div class="col-12 col-md-4">
                                                        <label>Transfer Amount</label>
                                                        <input type="number" class="form-control" placeholder="Amount" name="ot_transfer_amount" value="<?= $vehicle['ot_transfer_amount'] ?? '' ?>">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Date</label>
                                                        <input type="date" class="form-control" name="ot_transfer_date" value="<?= $vehicle['ot_transfer_date'] ?? date('Y-m-d') ?>">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Status</label>
                                                        <select class="form-select" name="ot_transfer_status">
                                                            <option value="Paid" <?= (isset($vehicle['ot_transfer_status']) && $vehicle['ot_transfer_status'] == 'Paid') ? 'selected' : '' ?>>Paid</option>
                                                            <option value="Due" <?= (isset($vehicle['ot_transfer_status']) && $vehicle['ot_transfer_status'] == 'Due') ? 'selected' : '' ?>>Due</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row g-2 align-items-end mb-3">
                                                    <div class="col-12 col-md-4">
                                                        <label>HPA</label>
                                                        <input type="number" class="form-control" placeholder="Amount" name="ot_hpa_amount" value="<?= $vehicle['ot_hpa_amount'] ?? '' ?>">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Date</label>
                                                        <input type="date" class="form-control" name="ot_hpa_date" value="<?= $vehicle['ot_hpa_date'] ?? date('Y-m-d') ?>">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Status</label>
                                                        <select class="form-select" name="ot_hpa_status">
                                                            <option value="Paid" <?= (isset($vehicle['ot_hpa_status']) && $vehicle['ot_hpa_status'] == 'Paid') ? 'selected' : '' ?>>Paid</option>
                                                            <option value="Due" <?= (isset($vehicle['ot_hpa_status']) && $vehicle['ot_hpa_status'] == 'Due') ? 'selected' : '' ?>>Due</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row g-2 align-items-end mb-3">
                                                    <div class="col-12 col-md-4">
                                                        <label>HP</label>
                                                        <input type="number" class="form-control" placeholder="Amount" name="ot_hp_amount" value="<?= $vehicle['ot_hp_amount'] ?? '' ?>">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Date</label>
                                                        <input type="date" class="form-control" name="ot_hp_date" value="<?= $vehicle['ot_hp_date'] ?? date('Y-m-d') ?>">
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label>Status</label>
                                                        <select class="form-select" name="ot_hp_status">
                                                            <option value="Paid" <?= (isset($vehicle['ot_hp_status']) && $vehicle['ot_hp_status'] == 'Paid') ? 'selected' : '' ?>>Paid</option>
                                                            <option value="Due" <?= (isset($vehicle['ot_hp_status']) && $vehicle['ot_hp_status'] == 'Due') ? 'selected' : '' ?>>Due</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Insurance Name</label>
                                                        <select class="form-control text-uppercase"  name="ot_insurance_name">
                                                            <option value="">-- Select Insurance --</option>
                                                            <?php
                                                            $ins_companies = [
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
                                                            foreach ($ins_companies as $ins) {
                                                                $selected = (isset($vehicle['ot_insurance_name']) && $vehicle['ot_insurance_name'] == $ins) ? 'selected' : '';
                                                                echo "<option value='$ins' $selected>$ins</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Payment Status</label>
                                                        <select class="form-control"  name="ot_insurance_payment_status">
                                                            <option value="">-- Select Status --</option>
                                                            <option value="paid" <?= (isset($vehicle['ot_insurance_payment_status']) && $vehicle['ot_insurance_payment_status'] == 'paid') ? 'selected' : '' ?>>Paid</option>
                                                            <option value="due" <?= (isset($vehicle['ot_insurance_payment_status']) && $vehicle['ot_insurance_payment_status'] == 'due') ? 'selected' : '' ?>>Due</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Amount</label>
                                                        <input type="number" class="form-control" placeholder="Enter Amount"  name="ot_insurance_amount" value="<?= $vehicle['ot_insurance_amount'] ?? '' ?>">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">Start Date</label>
                                                        <input type="date" class="form-control" id="startDate"  name="ot_insurance_start_date" value="<?= $vehicle['ot_insurance_start_date'] ?? date('Y-m-d') ?>">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="fw-bold">End Date</label>
                                                        <input type="date" class="form-control" id="endDate" readonly  name="ot_insurance_end_date" value="<?= $vehicle['ot_insurance_end_date'] ?? '' ?>">
                                                    </div>

                                                    <span class="fw-bold text-primary">Duration:<span id="durationText">--</span></span>
                                                </div>
                                            </div>

                                            <div class="row g-4">
                                                <div class="col-12 col-md-6">
                                                    <div class="border rounded-3">
                                                        <label class="form-label fw-bold">Purchaser Sign</label>
                                                        <div class="row g-3">
                                                            <div class="col-6">
                                                                <label class="form-label small">Status</label>
                                                                <select class="form-select" name="ot_purchaser_sign_status">
                                                                    <option value="Yes" <?= (isset($vehicle['ot_purchaser_sign_status']) && $vehicle['ot_purchaser_sign_status'] == 'Yes') ? 'selected' : '' ?>>Yes</option>
                                                                    <option value="No" <?= (!isset($vehicle['ot_purchaser_sign_status']) || $vehicle['ot_purchaser_sign_status'] == 'No') ? 'selected' : '' ?>>No</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label small">Date</label>
                                                                <input type="date" class="form-control" name="ot_purchaser_sign_date" value="<?= $vehicle['ot_purchaser_sign_date'] ?? date('Y-m-d') ?>">
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
                                                                    <option value="Yes" <?= (isset($vehicle['ot_seller_sign_status']) && $vehicle['ot_seller_sign_status'] == 'Yes') ? 'selected' : '' ?>>Yes</option>
                                                                    <option value="No" <?= (!isset($vehicle['ot_seller_sign_status']) || $vehicle['ot_seller_sign_status'] == 'No') ? 'selected' : '' ?>>No</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label small">Date</label>
                                                                <input type="date" class="form-control" name="ot_seller_sign_date" value="<?= $vehicle['ot_seller_sign_date'] ?? date('Y-m-d') ?>">
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


                      <div class="bg-dark border-top position-fixed bottom-0 start-0 w-100 shadow"
     style="z-index:1030;">

    <div class="container-fluid py-2">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

            <button type="button"
                    id="prevBtn"
                    onclick="prevStep(event)"
                    class="btn btn-outline-light btn-sm d-flex align-items-center gap-1">
                <i class="ph-bold ph-arrow-left"></i>
                <span class="d-none d-md-inline">Back</span>
            </button>

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

    <script>
        $(document).ready(function() {

            // ==========================================
            // 1. INITIALIZATION & CONFIGURATION
            // ==========================================
            let currentStep = 1;
            const totalSteps = 4;
            const form = document.getElementById('dealForm');

            // Get ID safely (PHP renders it into the hidden input)
            const vehicleId = $('input[name="vehicle_id"]').val();

            // Hide Page Loader
            setTimeout(function() {
                $('#loader').fadeOut(500);
            }, 500);

            // Initialize State from URL (if page refreshed)
            const urlParams = new URLSearchParams(window.location.search);
            const urlStep = urlParams.get('step');
            if (urlStep) currentStep = parseInt(urlStep);

            // Initial UI Update
            updateWizardUI();

            // Trigger calculations immediately (to fill Due amounts based on fetched DB data)
            $('#s_total, #p_total').trigger('input');

            // Safety Check
            if (!vehicleId || vehicleId == 0) {
                showToast("CRITICAL ERROR: Vehicle ID is missing. You cannot save.", "danger");
                toggleButtons(true);
            }

            // ==========================================
            // 2. IMAGE HANDLING (Compression & Previews)
            // ==========================================

            // Trigger file input when clicking the label/box
            $('.photo-upload-box').on('click', function(e) {
                if (e.target.tagName !== 'INPUT') {
                    e.preventDefault();
                    const inputId = $(this).attr('for');
                    if (inputId) $('#' + inputId).trigger('click');
                }
            });

            // Handle File Selection
            $('input[type="file"]').on('change', function() {
                const file = this.files[0];
                const rawId = this.id; // e.g. file_photo1 or doc_aadhar
                const key = rawId.replace('file_', ''); // Strip prefix for matching preview IDs
                const $input = $(this);

                if (!file) return;

                // A. Image Compression (Step 1 & Docs)
                if (file.type.match(/image.*/)) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const tempImg = new Image();
                        tempImg.src = e.target.result;
                        tempImg.onload = () => {
                            // Resize Logic
                            const canvas = document.createElement('canvas');
                            let width = tempImg.width,
                                height = tempImg.height;
                            const MAX = 1200;
                            if (width > height) {
                                if (width > MAX) {
                                    height *= MAX / width;
                                    width = MAX;
                                }
                            } else {
                                if (height > MAX) {
                                    width *= MAX / height;
                                    height = MAX;
                                }
                            }
                            canvas.width = width;
                            canvas.height = height;
                            canvas.getContext('2d').drawImage(tempImg, 0, 0, width, height);

                            // Compress to JPEG 70%
                            canvas.toBlob((blob) => {
                                const compressedFile = new File([blob], file.name, {
                                    type: 'image/jpeg'
                                });
                                $input.data('compressed', compressedFile); // Store for Upload

                                // Update Visual Preview (Step 1)
                                const $previewImg = $(`#preview_${key}`);
                                const $icon = $(`#icon_${key}`);
                                const $box = $previewImg.closest('.photo-upload-box');

                                if ($previewImg.length) {
                                    $previewImg.attr('src', URL.createObjectURL(blob)).show();
                                    $icon.hide();
                                    $box.addClass('has-image').css('border-style', 'solid');
                                }
                            }, 'image/jpeg', 0.7);
                        };
                    };
                    reader.readAsDataURL(file);
                }

                // B. Text & Icon Updates (Steps 2-4)
                const $nameSpan = $(`#filename_${key}`);
                const $viewBtn = $(`#view_${key}`);
                const $fileIcon = $(`#file_icon_${key}`);

                if ($nameSpan.length) {
                    let name = file.name;
                    if (name.length > 20) name = name.substring(0, 15) + "...";
                    $nameSpan.text(name).removeClass('text-muted').addClass('text-dark fw-bold');
                }

                if ($viewBtn.length) {
                    const url = URL.createObjectURL(file);
                    $viewBtn.attr('href', url).attr('target', '_blank');
                    $viewBtn.removeClass('btn-outline-secondary disabled').addClass('btn-outline-primary');
                    $viewBtn.css('pointer-events', 'auto');
                    $viewBtn.find('i').removeClass('text-muted').addClass('text-primary');
                }

                if ($fileIcon.length) {
                    $fileIcon.removeClass('ph-file text-muted').addClass('ph-file-image text-success');
                }
            });

            // ==========================================
            // 3. WIZARD NAVIGATION UI
            // ==========================================
            function updateWizardUI() {
                // Sidebar & Mobile
                $('.step-item').removeClass('step-active-blink border-primary');
                $('.step-item .step-circle').removeClass('bg-primary text-white').addClass(' text-white');

                const $activeItem = $('.step-item[data-step="' + currentStep + '"]');
                $activeItem.addClass('step-active-blink border-primary ');
                $activeItem.find('.step-circle').removeClass(' text-white').addClass('bg-primary text-white');

                $('#mobile-step-indicator').text('Step ' + currentStep);

                // Show/Hide Steps
                $('.wizard-step').addClass('d-none').removeClass('fade-in-animation');
                $('#step-' + currentStep).removeClass('d-none').addClass('fade-in-animation');

                // Buttons
                if (currentStep === 4) {
                    $('#btn-next').addClass('d-none');
                    $('#btn-finish').removeClass('d-none').addClass('d-flex');
                } else {
                    $('#btn-next').removeClass('d-none').addClass('d-flex');
                    $('#btn-finish').addClass('d-none');
                }

                if (currentStep === 1) $('#prevBtn').addClass('d-none');
                else $('#prevBtn').removeClass('d-none');

                // Sync hidden input
                $('input[name="step"]').val(currentStep);
            }

            // Previous Button
            window.prevStep = function(e) {
                if (e) e.preventDefault();
                if (currentStep > 1) {
                    currentStep--;
                    updateWizardUI();
                    updateUrl();
                    window.scrollTo(0, 0);
                }
            };

            function updateUrl() {
                const newUrl = window.location.pathname + `?step=${currentStep}&id=${vehicleId}`;
                window.history.pushState({
                    path: newUrl
                }, '', newUrl);
            }

            // ==========================================
            // 4. SUBMISSION LOGIC (AJAX + VALIDATION)
            // ==========================================

            // Listeners
            $('#btn-save-draft').click(function(e) {
                e.preventDefault();
                handleSubmission('save_draft');
            });
            $('#btn-next').click(function(e) {
                e.preventDefault();
                handleSubmission('save_next');
            });
            $('#btn-finish').click(function(e) {
                e.preventDefault();
                handleSubmission('finish');
            });

            function handleSubmission(actionName) {

                // 1. Validate (Strict for Step 1, Generic for others)
                // Skip validation only if saving a draft
                if (actionName !== 'save_draft') {
                    if (!validateStep(currentStep)) return;
                }

                // 2. Pre-Upload File Size Check
                let fileError = false;
                $('input[type="file"]').each(function() {
                    if (this.files.length > 0 && this.files[0].size > (5 * 1024 * 1024)) {
                        fileError = true;
                        $(this).closest('.photo-upload-box, .card-body').css('border', '2px solid red');
                    }
                });
                if (fileError) {
                    showToast("Upload Error: Files must be less than 5MB", "danger");
                    return;
                }

                // 3. Prepare Form Data
                const formData = new FormData(form);
                formData.append('formAction', actionName);
                formData.append('step_number', currentStep);
                formData.append('vehicle_id', vehicleId);

                // Replace files with Compressed versions if available
                $('input[type="file"]').each(function() {
                    const comp = $(this).data('compressed');
                    if (comp) formData.set($(this).attr('name'), comp);
                });

                // 4. AJAX Upload with Progress
                const xhr = new XMLHttpRequest();

                // UI Setup
                toggleButtons(true);
                $('#uploadProgressContainer').removeClass('d-none');
                $('#progressBar').width('0%').removeClass('bg-danger bg-warning').addClass('bg-primary');
                $('#progressPercent').text('0%');
                $('#overlayTitle').text(actionName === 'save_draft' ? 'Saving Draft' : 'Processing');
                $('#overlaySubtitle').text('Uploading data...');

                // Slow Network Detection
                let slowNetCheck = setInterval(() => {
                    if (xhr.readyState > 0 && xhr.readyState < 4) {
                        $('#overlaySubtitle').text('Slow connection detected... still working.');
                        $('#progressBar').addClass('bg-warning');
                    }
                }, 8000);

                // Progress Handler
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        const percent = Math.round((evt.loaded / evt.total) * 100);
                        $('#progressBar').width(percent + '%');
                        $('#progressPercent').text(percent + '%');
                    }
                });

                // Response Handler
                xhr.addEventListener("load", function() {
                    clearInterval(slowNetCheck);
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (xhr.status === 200 && res.status === 'success') {
                            handleSuccess(res, actionName);
                        } else {
                            showError(res.message || "Unknown server error");
                        }
                    } catch (e) {
                        showError("Invalid server response.");
                        console.log(xhr.responseText);
                    }
                });

                xhr.onerror = function() {
                    clearInterval(slowNetCheck);
                    showError("Network Connection Failed");
                };

                // Send
                xhr.open("POST", "vehicle_update_form.php", true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send(formData);
            }

            // ==========================================
            // 5. VALIDATION HELPER (STRICT)
            // ==========================================
            function validateStep(step) {
                let isValid = true;
                $('.is-invalid').removeClass('is-invalid');
                $('.border-danger').removeClass('border-danger');

                // --- STEP 1 STRICT CHECK ---
                if (step === 1) {
                    // A. Core Text Fields
                    const fields = ['vehicle_number', 'name', 'chassis_number', 'engine_number'];
                    fields.forEach(function(name) {
                        var $el = $('input[name="' + name + '"]');
                        if (!$el.val() || $el.val().trim() === '') {
                            $el.addClass('is-invalid');
                            isValid = false;
                        }
                    });

                    // B. Vehicle Photo 1 (File Input)
                    // Logic: Invalid if (No New File Selected) AND (Server Image is Default/Empty)
                    var $photoInput = $('#file_photo1');
                    var $preview = $('#preview_photo1');
                    var currentSrc = $preview.attr('src');

                    // Check if user has NOT selected a file AND the current image is the default/empty
                    if ($photoInput[0].files.length === 0 && (currentSrc.includes('default.jpg') || currentSrc === '' || currentSrc === '#')) {
                        $photoInput.closest('.photo-upload-box').css('border', '2px solid #dc3545');
                        isValid = false;
                    }

                    // C. Payment Logic
                    var payType = $('input[name="payment_type"]:checked').val();
                    if (!payType) {
                        // If no payment type selected
                        $('label[for="sp_cash"], label[for="sp_online"]').addClass('border-danger');
                        isValid = false;
                    } else {
                        $('label[for="sp_cash"], label[for="sp_online"]').removeClass('border-danger');

                        if (payType === 'Cash') {
                            // Check Bike Price (Cash)
                            var $cashPrice = $('input[name="cash_price"]');
                            if (!$cashPrice.val()) {
                                $cashPrice.addClass('is-invalid');
                                isValid = false;
                            }
                        } else if (payType === 'Online') {
                            // Check Online Method
                            if ($('input[name="online_method"]:checked').length === 0) {
                                $('#onlineBox .form-check-label').addClass('text-danger');
                                isValid = false;
                            } else {
                                $('#onlineBox .form-check-label').removeClass('text-danger');
                            }
                            // Check Transaction ID & Online Price
                            var $txnId = $('input[name="online_transaction_id"]');
                            var $onlinePrice = $('input[name="online_price"]');
                            if (!$txnId.val()) {
                                $txnId.addClass('is-invalid');
                                isValid = false;
                            }
                            if (!$onlinePrice.val()) {
                                $onlinePrice.addClass('is-invalid');
                                isValid = false;
                            }
                        }
                    }
                }

                // --- OTHER STEPS (GENERIC) ---
                else {
                    $(`#step-${step} [required]`).each(function() {
                        if ($(this).is(':radio')) {
                            const name = $(this).attr('name');
                            if ($(`input[name="${name}"]:checked`).length === 0) isValid = false;
                        } else if (!$(this).val() || $(this).val().trim() === '') {
                            $(this).addClass('is-invalid');
                            isValid = false;
                        }
                    });
                }

                if (!isValid) showToast("Please fill all required fields highlighted in red.", "warning");
                return isValid;
            }

            // ==========================================
            // 6. CALCULATIONS & HELPERS
            // ==========================================

            // Handle Success
            function handleSuccess(res, actionName) {
                showToast("Saved Successfully!", "success");

                if (actionName === 'save_next') {
                    if (currentStep < totalSteps) {
                        currentStep++;
                        updateWizardUI();
                        updateUrl();
                        window.scrollTo(0, 0);
                    }
                } else if (actionName === 'finish') {
                    window.location.href = 'inventory.php';
                }

                setTimeout(() => {
                    $('#uploadProgressContainer').addClass('d-none');
                    toggleButtons(false);
                }, 800);
            }

            // Handle Error
            function showError(msg) {
                showToast(msg, "danger");
                $('#overlayTitle').text("Error");
                $('#overlaySubtitle').text(msg);
                $('#progressBar').removeClass('bg-primary').addClass('bg-danger');

                setTimeout(() => {
                    $('#uploadProgressContainer').addClass('d-none');
                    toggleButtons(false);
                }, 2000);
            }

            // Toggle Buttons
            function toggleButtons(state) {
                const ids = ['btn-save-draft', 'btn-next', 'btn-finish', 'prevBtn'];
                ids.forEach(id => {
                    const btn = document.getElementById(id);
                    if (btn) btn.disabled = state;
                });
            }

            // Show Toast
            function showToast(msg, type) {
                const toastEl = document.getElementById('validationToast');
                const msgEl = document.getElementById('toastMessage');
                if (!toastEl) {
                    alert(msg);
                    return;
                }

                msgEl.innerText = msg;
                toastEl.className = `toast align-items-center text-white border-0 bg-${type === 'warning' ? 'warning' : (type === 'success' ? 'success' : 'danger')}`;
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 3000
                });
                toast.show();
            }

            // Date Auto-Fill
            function setOneYearExpiry(source, target, label) {
                $(source).on("change", function() {
                    let date = new Date($(this).val());
                    if (!isNaN(date.getTime())) {
                        date.setFullYear(date.getFullYear() + 1);
                        $(target).val(date.toISOString().split('T')[0]);
                        $(label).text(" (1 Year)");
                    }
                });
            }
            setOneYearExpiry("#issueDate", "#expiryDate", "#expiryText");
            setOneYearExpiry("#startDate", "#endDate", "#durationText");

            // Seller Payment Logic (-0 fix)
            $('#s_total, #s_paid').on('input', function() {
                let total = parseFloat($('#s_total').val()) || 0;
                let paid = parseFloat($('#s_paid').val()) || 0;
                let due = total - paid;

                if (Math.abs(due) < 0.001) due = 0; // Fix -0.00

                $('#s_due').val(due.toFixed(2));
                if (due > 0.01) $('#s_due_reason').removeClass('d-none');
                else $('#s_due_reason').addClass('d-none').val('');
            });

            // Purchaser Payment Logic
            $('#p_total, #p_paid').on('input', function() {
                let total = parseFloat($('#p_total').val()) || 0;
                let paid = parseFloat($('#p_paid').val()) || 0;
                let due = total - paid;
                if (Math.abs(due) < 0.001) due = 0;
                $('#p_due').val(due.toFixed(2));
            });

        });
    </script>
</body>

</html>