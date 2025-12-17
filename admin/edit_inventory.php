<?php
// 1. DATABASE CONNECTION
require "db.php";

// 2. SECURITY: Check Login & ID
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

// Get ID safely (default to 0 if missing)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    die("❌ Error: Invalid or missing Vehicle ID.");
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

<body class="vh-100 d-flex flex-column overflow-hidden">

    <!-- global Messages -->
    <?php include_once "global_message.php" ?>


    <nav class="navbar bg-white border-bottom shadow-sm py-3 flex-shrink-0">
        <div class="container-fluid d-flex flex-wrap align-items-center justify-content-between">
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

            <button id="logoutBtn"
                class="btn btn-light border fw-bold rounded-pill px-3 py-1 shadow-sm d-none d-md-inline-block">
                Log Out
            </button>

            <button id="logoutIcon"
                class="btn btn-light border rounded-circle shadow-sm d-inline-flex d-md-none justify-content-center align-items-center p-2">
                <i class="ph-bold ph-sign-out fs-5"></i>
            </button>
        </div>
    </nav>
    <div class="flex-grow-1 overflow-hidden">
        <div class="row g-0 h-100">
            <?php
            // PHP: Define Steps Array
            $wizard_steps = [
                1 => 'Vehicle',
                2 => 'Seller',
                3 => 'Purchaser',
                4 => 'Transfer'
            ];
            ?>
            <div class="col-lg-2 d-none d-lg-flex flex-column bg-white border-end h-100 shadow-sm z-3 position-relative">

                <div class="p-3 overflow-y-auto flex-grow-1 bg-white">
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($wizard_steps as $step_key => $label):
                            // Check if this is the first step to set initial "Active" styling
                            $isActive = ($step_key == 1);
                        ?>

                            <div id="sidebar-item-<?= $step_key ?>"
                                class="step-item d-flex align-items-center gap-3 p-3 rounded-3 border-start border-4 hover-bg-light <?= $isActive ? 'border-primary bg-light' : 'border-transparent' ?>"
                                data-step="<?= $step_key ?>"
                                onclick="goToStep(<?= $step_key ?>)"
                                style="cursor: pointer; transition: all 0.2s ease;">

                                <div id="sidebar-circle-<?= $step_key ?>"
                                    class="step-circle border rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 <?= $isActive ? 'bg-primary text-white' : 'bg-light text-secondary' ?>"
                                    style="width: 32px; height: 32px;">
                                    <span class="small fw-bold"><?= $step_key ?></span>
                                </div>

                                <div class="d-flex flex-column">
                                    <span id="sidebar-text-<?= $step_key ?>"
                                        class="step-label fw-medium <?= $isActive ? 'text-primary fw-bold' : 'text-secondary' ?>"
                                        style="font-size: 0.95rem;">
                                        <?= $label ?>
                                    </span>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Universal Toast Messages -->
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1055;">
                <div id="liveToast"
                    class="toast align-items-center border-0 shadow"
                    role="alert"
                    aria-live="assertive"
                    aria-atomic="true">

                    <div class="d-flex">
                        <div class="toast-body fw-semibold" id="toastMessage"></div>

                        <button type="button"
                            class="btn-close me-2 m-auto"
                            data-bs-dismiss="toast"
                            aria-label="Close">
                        </button>
                    </div>
                </div>
            </div>


            <?php
            // ====================================================
            // 1. FETCH DATA FROM ALL TABLES
            // ====================================================

            // A. MASTER VEHICLE DATA (Step 1)
            $stmt = $conn->prepare("SELECT * FROM vehicle WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $vehicle = $stmt->get_result()->fetch_assoc();

            // Stop if the main vehicle doesn't exist
            if (!$vehicle) {
                die("<div class='alert alert-danger m-4'>❌ Error: Vehicle ID #$id not found. <a href='inventory.php'>Go Back</a></div>");
            }

            // B. SELLER DATA (Step 2)
            $stmt = $conn->prepare("SELECT * FROM vehicle_seller WHERE vehicle_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $sellerData = $stmt->get_result()->fetch_assoc();

            // ⭐ MERGE: Add seller data into the main $vehicle array
            if ($sellerData) {
                $vehicle = array_merge($vehicle, $sellerData);
            }

            // C. PURCHASER DATA (Step 3)
            $stmt = $conn->prepare("SELECT * FROM vehicle_purchaser WHERE vehicle_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $purchaserData = $stmt->get_result()->fetch_assoc();

            // ⭐ MERGE: Add purchaser data into the main $vehicle array
            if ($purchaserData) {
                $vehicle = array_merge($vehicle, $purchaserData);
            }

            // D. TRANSFER / OT DATA (Step 4)
            $stmt = $conn->prepare("SELECT * FROM vehicle_ot WHERE vehicle_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $otData = $stmt->get_result()->fetch_assoc();

            // ⭐ MERGE: Add OT data into the main $vehicle array
            if ($otData) {
                $vehicle = array_merge($vehicle, $otData);
            }

            // ====================================================
            // 2. SET DEFAULTS (To prevent "Undefined key" errors)
            // ====================================================

            // Step 1 Defaults
            $vehicle['sold_out'] = $vehicle['sold_out'] ?? 0;

            // Step 2 Defaults (Seller)
            $vehicle['seller_payment_type']  = $vehicle['seller_payment_type'] ?? 'cash'; // Default to Cash
            $vehicle['seller_online_method'] = $vehicle['seller_online_method'] ?? '';
            $vehicle['noc_status']           = $vehicle['noc_status'] ?? 'paid';

            // Step 3 Defaults (Purchaser)
            $vehicle['purchaser_payment_all_paid'] = $vehicle['purchaser_payment_all_paid'] ?? 0;
            $vehicle['purchaser_payment_mode']     = $vehicle['purchaser_payment_mode'] ?? 'Cash';

            // Step 4 Defaults (OT)
            $vehicle['ot_transfer_status'] = $vehicle['ot_transfer_status'] ?? 'Due';

            ?>


            <div class="col-12 col-lg-10 h-100 d-flex flex-column bg-light position-relative">
                <form id="updateForm" class="d-flex flex-column h-100" enctype="multipart/form-data">

                    <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['id']; ?>">

                    <input type="hidden" name="action" id="action_input">

                    <div class="d-lg-none bg-white px-4 py-3 border-bottom shadow-sm z-2">
                        <div class="d-flex justify-content-between align-items-end mb-1">
                            <span class="text-primary fw-bold text-uppercase small ls-1">Current Step</span>
                            <span class="text-muted small fw-bold"><span id="mobile-current-step">1</span>/4</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: 25%" id="mobile-progress-bar"></div>
                        </div>
                        <h5 class="fw-bold mt-2 mb-0 text-dark" id="mobile-step-title">Vehicle Details</h5>
                    </div>

                    <div class="flex-grow-1 overflow-y-auto p-3 p-md-5">
                        <div class="container-fluid p-0" style="max-width: 1000px; margin: 0 auto;">

                            <!-- 1st step -->
                            <div id="step-1" class="wizard-step fade-in-animation">
                                <div class="card steps-id p-4 border-0 shadow-sm position-relative sold-wrapper rounded-4">

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

                                                    <input  required type="file" id="file_<?= $key ?>" name="<?= $key ?>" accept="image/*" hidden onchange="previewImage(this, '<?= $key ?>')">
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
                                                <input  required type="text" id="nameField" name="name" class="form-control" placeholder="Enter Name" value="<?= $vehicle['name'] ?? '' ?>">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label>Vehicle Number</label>
                                                <input  required type="text" name="vehicle_number" class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000" value="<?= $vehicle['vehicle_number'] ?? '' ?>">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label>Register Date</label>
                                                <input  required type="date" name="register_date" class="form-control" value="<?= $vehicle['register_date'] ?? '' ?>">
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
                                                <input  required type="text" name="chassis_number" class="form-control text-uppercase" value="<?= $vehicle['chassis_number'] ?? '' ?>">
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Engine Number</label>
                                                <input  required type="text" name="engine_number" class="form-control text-uppercase" value="<?= $vehicle['engine_number'] ?? '' ?>">
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label class="fw-bold mb-2">Payment Type</label>
                                                <div class="d-flex gap-2 mb-3">
                                                    <input  required type="radio" class="btn-check" name="payment_type" id="sp_cash" value="Cash" <?= ($vehicle['payment_type'] == 'Cash') ? 'checked' : '' ?> data-bs-toggle="collapse" data-bs-target="#cashBox">
                                                    <label class="btn btn-outline-success" for="sp_cash">Cash</label>

                                                    <input  required type="radio" class="btn-check" name="payment_type" id="sp_online" value="Online" <?= ($vehicle['payment_type'] == 'Online') ? 'checked' : '' ?> data-bs-toggle="collapse" data-bs-target="#onlineBox">
                                                    <label class="btn btn-outline-primary" for="sp_online">Online</label>
                                                </div>

                                                <div id="payBoxes">
                                                    <div id="cashBox" class="collapse <?= ($vehicle['payment_type'] == 'Cash') ? 'show' : '' ?>" data-bs-parent="#payBoxes">
                                                        <div class="p-3 mb-3 bg-white rounded-3 border shadow-sm">
                                                            <label class="fw-bold small mb-1">Bike Price</label>
                                                            <input  required type="number" name="cash_price" class="form-control form-control-sm mb-3" placeholder="Enter Amount" value="<?= $vehicle['cash_price'] ?? '' ?>">
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
                                                                        <input  required type="radio" class="form-check-input"  name="online_method" id="<?= $mId ?>" value="<?= $mName ?>" <?= $checked ?>>
                                                                        <label class="form-check-label small fw-bold" for="<?= $mId ?>"><?= $mName ?></label>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>

                                                            <input  required type="text" name="online_transaction_id" class="form-control form-control-sm mb-3 text-uppercase" placeholder="Transaction / UPI Reference ID" value="<?= $vehicle['online_transaction_id'] ?? '' ?>">

                                                            <label class="fw-bold small mb-1">Bike Price</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white border-end-0">₹</span>
                                                                <input  required type="number" name="online_price" class="form-control border-start-0 ps-0" placeholder="Enter Price" value="<?= $vehicle['online_price'] ?? '' ?>">
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
                                                    <input  required class="form-check-input"  type="radio" name="police_challan" value="No" <?= ($vehicle['police_challan'] == 'No') ? 'checked' : '' ?> data-bs-toggle="collapse" data-bs-target="#challan-section">
                                                    <label class="form-check-label fw-bold">No</label>
                                                </div>
                                                <div class="form-check">
                                                    <input  required class="form-check-input"  type="radio" name="police_challan" value="Yes" <?= ($vehicle['police_challan'] == 'Yes') ? 'checked' : '' ?> data-bs-toggle="collapse" data-bs-target="#challan-section">
                                                    <label class="form-check-label fw-bold">Yes</label>
                                                </div>
                                            </div>

                                            <div class="collapse mt-3 <?= ($vehicle['police_challan'] == 'Yes') ? 'show' : '' ?>" id="challan-section">
                                                <?php for ($i = 1; $i <= 3; $i++): ?>
                                                    <div class="border rounded p-2 mb-2 bg-white">
                                                        <label class="fw-bold small">Challan <?= $i ?></label>
                                                        <div class="row g-2">
                                                            <div class="col-md-4">
                                                                <input  required type="text" name="challan<?= $i ?>_number" class="form-control text-uppercase" placeholder="Challan Number" value="<?= $vehicle["challan{$i}_number"] ?? '' ?>">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input  required type="number" name="challan<?= $i ?>_amount" class="form-control" placeholder="Amount" value="<?= $vehicle["challan{$i}_amount"] ?? '' ?>">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="btn-group w-100 btn-group-sm">
                                                                    <input  required type="radio" class="btn-check" name="challan<?= $i ?>_status" id="pen<?= $i ?>" value="Pending" <?= ($vehicle["challan{$i}_status"] == 'Pending') ? 'checked' : '' ?>>
                                                                    <label class="btn btn-outline-danger" for="pen<?= $i ?>">Pending</label>

                                                                    <input  required type="radio" class="btn-check" name="challan<?= $i ?>_status" id="paid<?= $i ?>" value="Paid" <?= ($vehicle["challan{$i}_status"] == 'Paid') ? 'checked' : '' ?>>
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
                                                <input  required class="form-check-input" type="checkbox" id="soldToggle" name="sold_out" value="1" <?= ($vehicle['sold_out'] == 1) ? 'checked' : '' ?> style="width: 3em; height: 1.5em;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 2nd step -->
                            <div id="step-2" class="wizard-step d-none">
                                <div class="card steps-id border-0 p-4 shadow-sm rounded-4">
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
                                        <label class="mb-2">Purchaser Documents (In Seller Step)</label>
                                        <div class="row g-2">
                                            <?php
                                            $docs = [
                                                'doc_aadhar_front' => 'Aadhar Front',
                                                'doc_aadhar_back' => 'Aadhar Back',
                                                'doc_voter_front' => 'Voter Front',
                                                'doc_voter_back' => 'Voter Back'
                                            ];
                                            foreach ($docs as $key => $label):
                                                $src = !empty($vehicle[$key]) ? "../images/" . $vehicle[$key] : "";
                                                $display = !empty($src) ? "block" : "none";
                                            ?>
                                                <div class="col-6 col-md-3">
                                                    <div class="photo-upload-box position-relative" style="cursor: pointer;" onclick="document.getElementById('<?= $key ?>').click();">
                                                        <span class="small text-muted fw-bold"><?= $label ?></span>
                                                        <img id="preview_<?= $key ?>" src="<?= $src ?>" class="w-100 mt-1 rounded border" style="height: 80px; object-fit: cover; display: <?= $display ?>; background: #f8f9fa;">
                                                        <?php if (empty($src)): ?>
                                                            <div id="icon_<?= $key ?>" class="text-center py-3 text-secondary"><i class="ph-bold ph-camera fs-3"></i></div>
                                                        <?php endif; ?>
                                                        <input type="file" id="<?= $key ?>" name="<?= $key ?>" accept="image/*" hidden onchange="previewImage(this)">
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="bg-light p-3 rounded-4 border mb-3">
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
                                            <label class="fw-bold small">RC Upload</label>
                                            <div class="row g-2">
                                                <?php
                                                $rcs = ['rc_front' => 'RC FRONT', 'rc_back' => 'RC BACK'];
                                                foreach ($rcs as $k => $l):
                                                    $src = !empty($vehicle[$k]) ? "../images/" . $vehicle[$k] : "";
                                                ?>
                                                    <div class="col-6">
                                                        <div class="border rounded p-2 text-center bg-white" style="cursor: pointer;" onclick="document.getElementById('<?= $k ?>').click();">
                                                            <small class="fw-bold d-block mb-1" style="font-size:10px"><?= $l ?></small>
                                                            <?php if ($src): ?><img src="<?= $src ?>" class="w-100 mb-1" style="height:50px; object-fit: contain;"><?php endif; ?>
                                                            <input type="file" id="<?= $k ?>" name="<?= $k ?>" class="form-control form-control-sm mt-1">
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

                                            <div class="row g-2">
                                                <?php
                                                $nocs = ['noc_front' => 'NOC Front', 'noc_back' => 'NOC Back'];
                                                foreach ($nocs as $k => $l):
                                                    $src = !empty($vehicle[$k]) ? "../images/" . $vehicle[$k] : "";
                                                ?>
                                                    <div class="col-6">
                                                        <div class="border rounded small-box text-center p-2" style="cursor: pointer;" onclick="document.getElementById('<?= $k ?>').click();">
                                                            <span class="small text-muted fw-bold"><?= $l ?></span>
                                                            <?php if ($src): ?><img src="<?= $src ?>" class="w-100 mb-1" style="height:50px; object-fit: contain;"><?php endif; ?>
                                                            <input type="file" id="<?= $k ?>" name="<?= $k ?>" class="form-control form-control-sm mt-1">
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

                                    <div class="bg-light p-3 rounded-4 border">
                                        <label class="text-primary">Payment Calculation</label>
                                        <div class="row g-2">
                                            <div class="col-12"><input type="number" name="total_amount" class="form-control" placeholder="Total" id="s_total" value="<?= $vehicle['total_amount'] ?? '' ?>"></div>
                                            <div class="col-12"><input type="number" name="paid_amount" class="form-control" placeholder="Paid" id="s_paid" value="<?= $vehicle['paid_amount'] ?? '' ?>"></div>
                                            <div class="col-12"><input type="number" name="due_amount" class="form-control bg-white fw-bold text-danger" placeholder="Due" id="s_due" readonly value="<?= $vehicle['due_amount'] ?? '' ?>"></div>
                                            <div class="col-12">
                                                <input type="text" name="due_reason" class="form-control <?= (!empty($vehicle['due_amount']) && $vehicle['due_amount'] > 0) ? '' : 'd-none' ?> mt-1" id="s_due_reason" placeholder="Reason for due amount..." value="<?= $vehicle['due_reason'] ?? '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 3rd step -->
                            <div id="step-3" class="wizard-step d-none">
                                <div class="card steps-id border-0 p-4 shadow-sm rounded-4">
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

                                    <div class="bg-light p-3 rounded-4 border mb-4">
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
                                                <select name="purchaser_insurance_name" class="form-control text-uppercase" required>
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
                                                <select name="purchaser_insurance_payment_status" class="form-control" required>
                                                    <option value="">-- Select Status --</option>
                                                    <option value="paid" <?= (isset($vehicle['purchaser_insurance_payment_status']) && $vehicle['purchaser_insurance_payment_status'] == 'paid') ? 'selected' : '' ?>>Paid</option>
                                                    <option value="due" <?= (isset($vehicle['purchaser_insurance_payment_status']) && $vehicle['purchaser_insurance_payment_status'] == 'due') ? 'selected' : '' ?>>Due</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">Amount</label>
                                                <input type="number" name="purchaser_insurance_amount" class="form-control" placeholder="Enter Amount" required value="<?= $vehicle['purchaser_insurance_amount'] ?? '' ?>">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">Issue Date</label>
                                                <input type="date" name="purchaser_insurance_issue_date" class="form-control" id="issueDate" required value="<?= $vehicle['purchaser_insurance_issue_date'] ?? '' ?>">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">Expiry Date</label>
                                                <input type="date" name="purchaser_insurance_expiry_date" class="form-control" id="expiryDate" readonly required value="<?= $vehicle['purchaser_insurance_expiry_date'] ?? '' ?>">
                                            </div>

                                            <span class="fw-bold text-primary">Validity:<span id="expiryText">--</span></span>
                                        </div>
                                    </div>

                                    <div class="bg-light p-3 rounded-4 border mb-3">
                                        <label class="mb-2 fw-bold">Price Breakdown</label>
                                        <div class="row g-2 mb-3">
                                            <div class="col-12">
                                                <input type="number" name="purchaser_total" id="p_total" class="form-control" placeholder="Total" value="<?= $vehicle['purchaser_total'] ?? '' ?>">
                                            </div>
                                            <div class="col-12">
                                                <input type="number" name="purchaser_paid" id="p_paid" class="form-control" placeholder="Paid" value="<?= $vehicle['purchaser_paid'] ?? '' ?>">
                                            </div>
                                            <div class="col-12">
                                                <input type="number" name="purchaser_due" id="p_due" class="form-control bg-white" placeholder="Due" readonly value="<?= $vehicle['purchaser_due'] ?? '' ?>">
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
                                                <div class="col-6 col-md-3">
                                                    <div class="card h-100 shadow-sm border-0 bg-light">
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
                            <div id="step-4" class="wizard-step d-none">
                                <div class="card steps-id border-0 shadow-sm rounded-4">
                                    <h6 class="fw-bold text-primary m-4 mb-3 text-uppercase ls-1">Ownership Transfer</h6>

                                    <div class="p-3 border rounded-4 mb-4">
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

                                        <div class="bg-light p-3 rounded-4 border mb-4">
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
                                                    <select class="form-control text-uppercase" required name="ot_insurance_name">
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
                                                    <select class="form-control" required name="ot_insurance_payment_status">
                                                        <option value="">-- Select Status --</option>
                                                        <option value="paid" <?= (isset($vehicle['ot_insurance_payment_status']) && $vehicle['ot_insurance_payment_status'] == 'paid') ? 'selected' : '' ?>>Paid</option>
                                                        <option value="due" <?= (isset($vehicle['ot_insurance_payment_status']) && $vehicle['ot_insurance_payment_status'] == 'due') ? 'selected' : '' ?>>Due</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="fw-bold">Amount</label>
                                                    <input type="number" class="form-control" placeholder="Enter Amount" required name="ot_insurance_amount" value="<?= $vehicle['ot_insurance_amount'] ?? '' ?>">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="fw-bold">Start Date</label>
                                                    <input type="date" class="form-control" id="startDate" required name="ot_insurance_start_date" value="<?= $vehicle['ot_insurance_start_date'] ?? date('Y-m-d') ?>">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="fw-bold">End Date</label>
                                                    <input type="date" class="form-control" id="endDate" readonly required name="ot_insurance_end_date" value="<?= $vehicle['ot_insurance_end_date'] ?? '' ?>">
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

                    <div class="d-flex align-items-center gap-3">
                        <!-- Back Button (LEFT) -->
                        <button type="button"
                            class="btn btn-outline-primary px-4 py-2 fw-bold"
                            id="prevBtn"
                            style="display:none;"
                            onclick="prevStep()">
                            <i class="ph-bold ph-arrow-left me-2"></i>Back
                        </button>
                        <!-- Spacer pushes next buttons to right -->
                        <div class="ms-auto d-flex gap-2">
                            <button type="button"
                                class="btn btn-warning px-4"
                                onclick="submitAjax('save_only')">


                                <span>Draft <i class="ph-bold ph-floppy-disk me-2"></i></span>
                            </button>
                            <button type="button"
                                id="btn-next"
                                class="btn btn-primary px-4"
                                onclick="submitAjax('save_next')">
                                Save & Next <i class="ph-bold ph-caret-right ms-2"></i>
                            </button>
                            <button type="button"
                                id="btn-finish"
                                class="btn btn-success px-4 d-none"
                                onclick="submitAjax('finish')">
                                Finish <i class="ph-bold ph-check-circle ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        var currentStep = 1;
        var totalSteps = 4;
        var vehicleId = "<?= $id ?>";

        $(document).ready(function() {
            updateUI();

            // Auto-Calculate Due Amounts (Seller)
            $('#s_total, #s_paid').on('input', function() {
                var total = parseFloat($('#s_total').val()) || 0;
                var paid = parseFloat($('#s_paid').val()) || 0;
                $('#s_due').val(total - paid);
            });

            // Auto-Calculate Due Amounts (Purchaser)
            $('#p_total, #p_paid').on('input', function() {
                var total = parseFloat($('#p_total').val()) || 0;
                var paid = parseFloat($('#p_paid').val()) || 0;
                $('#p_due').val(total - paid);
            });
        });

        // 1. Image Preview
        window.previewImage = function(input, key) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview_' + key).attr('src', e.target.result).show();
                    $('#icon_' + key).hide();
                }
                reader.readAsDataURL(input.files[0]);
            }
        };

        // 2. Navigation Logic
        window.nextStep = function() {
            if (currentStep < totalSteps) goToStep(currentStep + 1);
        };

        window.prevStep = function() {
            if (currentStep > 1) goToStep(currentStep - 1);
        };

        window.goToStep = function(stepNumber) {
            // Hide current
            $('#step-' + currentStep).addClass('d-none').removeClass('fade-in-animation');

            // Show new
            currentStep = stepNumber;
            $('#step-' + currentStep).removeClass('d-none').addClass('fade-in-animation');

            updateUI();
        };

        function updateUI() {
            // Button visibility
            $('#prevBtn').toggle(currentStep > 1);
            if (currentStep === totalSteps) {
                $('#btn-next').addClass('d-none');
                $('#btn-finish').removeClass('d-none');
            } else {
                $('#btn-next').removeClass('d-none');
                $('#btn-finish').addClass('d-none');
            }

            // Mobile Progress
            var percentage = (currentStep / totalSteps) * 100;
            $('#mobile-progress-bar').css('width', percentage + '%');
            $('#mobile-current-step').text(currentStep);

            // Desktop Sidebar
            $('.step-item').removeClass('bg-light border-primary').addClass('border-transparent');
            $('.step-circle').removeClass('bg-primary text-white').addClass('bg-light text-secondary');
            $('.step-label').removeClass('text-primary fw-bold').addClass('text-secondary');

            var $active = $('#sidebar-item-' + currentStep);
            $active.addClass('bg-light border-primary').removeClass('border-transparent');
            $active.find('.step-circle').addClass('bg-primary text-white').removeClass('bg-light text-secondary');
            $active.find('.step-label').addClass('text-primary fw-bold').removeClass('text-secondary');
        }

        // 3. AJAX Logic
        window.submitAjax = function(actionType) {
            var form = $('#updateForm')[0];
            var formData = new FormData(form);

            formData.append('action', actionType);
            formData.append('step_number', currentStep);

            var $btn = (currentStep === totalSteps) ? $('#btn-finish') : $('#btn-next');
            var originalText = $btn.html();
            $btn.html('<span class="spinner-border spinner-border-sm"></span> Processing...').prop('disabled', true);

            $.ajax({
                url: 'vechicle_update_form.php', // Ensure this file exists on server
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'success') {
                        if (actionType === 'save_only') {
                            alert("Draft Saved!");
                        } else if (actionType === 'save_next') {
                            nextStep();
                        } else if (actionType === 'finish') {
                            alert("Vehicle updated successfully!");
                            window.location.href = "inventory.php";
                        }
                    } else {
                        alert("Error: " + data.message);
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert("System Error. Check console for details.");
                },
                complete: function() {
                    $btn.html(originalText).prop('disabled', false);
                }
            });
        };

        // ==========================
        // ISSUE DATE → EXPIRY DATE
        // ==========================
        $("#issueDate").on("change", function() {

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
        $("#startDate").on("change", function() {

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

        function updateFileName(input) {
            if (input.files && input.files[0]) {
                var file = input.files[0];
                var elementId = input.id; // e.g., purchaser_doc_aadhar_front

                // 1. Update the text display
                var nameSpan = document.getElementById('filename_' + elementId);
                if (nameSpan) {
                    // Truncate name if long
                    var displayName = file.name;
                    if (displayName.length > 15) {
                        displayName = displayName.substring(0, 12) + "...";
                    }
                    nameSpan.innerText = displayName;
                    nameSpan.classList.remove('text-muted');
                    nameSpan.classList.add('text-success');
                }

                // 2. Enable the View button with a temporary blob URL
                var viewBtn = document.getElementById('view_' + elementId);
                if (viewBtn) {
                    var blobUrl = URL.createObjectURL(file);
                    viewBtn.href = blobUrl;
                    viewBtn.classList.remove('btn-outline-secondary', 'disabled');
                    viewBtn.classList.add('btn-outline-primary');
                }
            }
        }
    </script>
</body>

</html>