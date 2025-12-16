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
            // Using try-catch logic to handle empty results gracefully
            $s_res = $conn->query("SELECT * FROM vehicle_seller WHERE vehicle_id = $id");
            $seller = ($s_res->num_rows > 0) ? $s_res->fetch_assoc() : [];

            // C. PURCHASER DATA (Step 3)
            $p_res = $conn->query("SELECT * FROM vehicle_purchaser WHERE vehicle_id = $id");
            $purchaser = ($p_res->num_rows > 0) ? $p_res->fetch_assoc() : [];

            // D. TRANSFER / OT DATA (Step 4)
            $ot_res = $conn->query("SELECT * FROM vehicle_ot WHERE vehicle_id = $id");
            $ot = ($ot_res->num_rows > 0) ? $ot_res->fetch_assoc() : [];

            // --- HELPER VARIABLES FOR UI ---
            // Ensure default keys exist to prevent "Undefined array key" warnings in HTML
            $vehicle['sold_out'] = $vehicle['sold_out'] ?? 0;
            $seller['seller_payment_type'] = $seller['seller_payment_type'] ?? 'Cash';
            $purchaser['purchaser_payment_all_paid'] = $purchaser['purchaser_payment_all_paid'] ?? 0;

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

                                                    <input type="file" id="file_<?= $key ?>" name="<?= $key ?>" accept="image/*" hidden onchange="previewImage(this, '<?= $key ?>')">
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
                                                <input type="text" id="nameField" name="name" class="form-control" placeholder="Enter Name" value="<?= $vehicle['name'] ?? '' ?>">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label>Vehicle Number</label>
                                                <input type="text" name="vehicle_number" class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000" value="<?= $vehicle['vehicle_number'] ?? '' ?>">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label>Register Date</label>
                                                <input type="date" name="register_date" class="form-control" value="<?= $vehicle['register_date'] ?? '' ?>">
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
                                                <input type="text" name="chassis_number" class="form-control text-uppercase" value="<?= $vehicle['chassis_number'] ?? '' ?>">
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Engine Number</label>
                                                <input type="text" name="engine_number" class="form-control text-uppercase" value="<?= $vehicle['engine_number'] ?? '' ?>">
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label class="fw-bold mb-2">Payment Type</label>
                                                <div class="d-flex gap-2 mb-3">
                                                    <input type="radio" class="btn-check" name="payment_type" id="sp_cash" value="Cash" <?= ($vehicle['payment_type'] == 'Cash') ? 'checked' : '' ?> data-bs-toggle="collapse" data-bs-target="#cashBox">
                                                    <label class="btn btn-outline-success" for="sp_cash">Cash</label>

                                                    <input type="radio" class="btn-check" name="payment_type" id="sp_online" value="Online" <?= ($vehicle['payment_type'] == 'Online') ? 'checked' : '' ?> data-bs-toggle="collapse" data-bs-target="#onlineBox">
                                                    <label class="btn btn-outline-primary" for="sp_online">Online</label>
                                                </div>

                                                <div id="payBoxes">
                                                    <div id="cashBox" class="collapse <?= ($vehicle['payment_type'] == 'Cash') ? 'show' : '' ?>" data-bs-parent="#payBoxes">
                                                        <div class="p-3 mb-3 bg-white rounded-3 border shadow-sm">
                                                            <label class="fw-bold small mb-1">Bike Price</label>
                                                            <input type="number" name="cash_price" class="form-control form-control-sm mb-3" placeholder="Enter Amount" value="<?= $vehicle['cash_price'] ?? '' ?>">
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
                                                                        <input type="radio" class="form-check-input" name="online_method" id="<?= $mId ?>" value="<?= $mName ?>" <?= $checked ?>>
                                                                        <label class="form-check-label small fw-bold" for="<?= $mId ?>"><?= $mName ?></label>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>

                                                            <input type="text" name="online_transaction_id" class="form-control form-control-sm mb-3 text-uppercase" placeholder="Transaction / UPI Reference ID" value="<?= $vehicle['online_transaction_id'] ?? '' ?>">

                                                            <label class="fw-bold small mb-1">Bike Price</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white border-end-0">₹</span>
                                                                <input type="number" name="online_price" class="form-control border-start-0 ps-0" placeholder="Enter Price" value="<?= $vehicle['online_price'] ?? '' ?>">
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
                                                    <input class="form-check-input" type="radio" name="police_challan" value="No" <?= ($vehicle['police_challan'] == 'No') ? 'checked' : '' ?> data-bs-toggle="collapse" data-bs-target="#challan-section">
                                                    <label class="form-check-label fw-bold">No</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="police_challan" value="Yes" <?= ($vehicle['police_challan'] == 'Yes') ? 'checked' : '' ?> data-bs-toggle="collapse" data-bs-target="#challan-section">
                                                    <label class="form-check-label fw-bold">Yes</label>
                                                </div>
                                            </div>

                                            <div class="collapse mt-3 <?= ($vehicle['police_challan'] == 'Yes') ? 'show' : '' ?>" id="challan-section">
                                                <?php for ($i = 1; $i <= 3; $i++): ?>
                                                    <div class="border rounded p-2 mb-2 bg-white">
                                                        <label class="fw-bold small">Challan <?= $i ?></label>
                                                        <div class="row g-2">
                                                            <div class="col-md-4">
                                                                <input type="text" name="challan<?= $i ?>_number" class="form-control text-uppercase" placeholder="Challan Number" value="<?= $vehicle["challan{$i}_number"] ?? '' ?>">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="number" name="challan<?= $i ?>_amount" class="form-control" placeholder="Amount" value="<?= $vehicle["challan{$i}_amount"] ?? '' ?>">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="btn-group w-100 btn-group-sm">
                                                                    <input type="radio" class="btn-check" name="challan<?= $i ?>_status" id="pen<?= $i ?>" value="Pending" <?= ($vehicle["challan{$i}_status"] == 'Pending') ? 'checked' : '' ?>>
                                                                    <label class="btn btn-outline-danger" for="pen<?= $i ?>">Pending</label>

                                                                    <input type="radio" class="btn-check" name="challan<?= $i ?>_status" id="paid<?= $i ?>" value="Paid" <?= ($vehicle["challan{$i}_status"] == 'Paid') ? 'checked' : '' ?>>
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
                                                <input class="form-check-input" type="checkbox" id="soldToggle" name="sold_out" value="1" <?= ($vehicle['sold_out'] == 1) ? 'checked' : '' ?> style="width: 3em; height: 1.5em;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 2nd step -->
                            <div id="step-2" class="wizard-step d-none">
                                <div class="card steps-id border-0 p-4 shadow-sm rounded-4">
                                    <h6 class="fw-bold text-primary mb-3 text-uppercase ls-1">Seller Details</h6>

                                    <div class="row g-3 mb-3">
                                        <div class="col-12 col-md-4">
                                            <label>Date</label>
                                            <input type="date" name="seller_date" class="form-control" value="<?= $vehicle['seller_date'] ?? '' ?>">
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <label>Vehicle No</label>
                                            <input type="text" name="seller_vehicle_number"
                                                class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000"
                                                value="<?= !empty($vehicle['seller_vehicle_number']) ? $vehicle['seller_vehicle_number'] : 'WB ' ?>">
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
                                                    <input type="radio" name="noc_status" class="btn-check" id="noc_paid" value="paid"
                                                        <?= (!isset($vehicle['noc_status']) || $vehicle['noc_status'] == 'paid') ? 'checked' : '' ?>>
                                                    <label class="btn btn-outline-success" for="noc_paid">Paid</label>

                                                    <input type="radio" name="noc_status" class="btn-check" id="noc_due" value="due"
                                                        <?= (isset($vehicle['noc_status']) && $vehicle['noc_status'] == 'due') ? 'checked' : '' ?>>
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
                                                <input type="radio" name="seller_payment_type" class="btn-check" id="pay_cash" value="cash"
                                                    <?= (!isset($vehicle['seller_payment_type']) || $vehicle['seller_payment_type'] == 'cash') ? 'checked' : '' ?>
                                                    data-bs-toggle="collapse" data-bs-target="#cashBox">
                                                <label class="btn btn-outline-success" for="pay_cash">Cash</label>

                                                <input type="radio" name="seller_payment_type" class="btn-check" id="pay_online" value="online"
                                                    <?= (isset($vehicle['seller_payment_type']) && $vehicle['seller_payment_type'] == 'online') ? 'checked' : '' ?>
                                                    data-bs-toggle="collapse" data-bs-target="#onlineBox">
                                                <label class="btn btn-outline-primary" for="pay_online">Online</label>
                                            </div>

                                            <div id="payAccordion">
                                                <div id="cashBox" class="collapse <?= (!isset($vehicle['seller_payment_type']) || $vehicle['seller_payment_type'] == 'cash') ? 'show' : '' ?>" data-bs-parent="#payAccordion">
                                                    <div class="p-3 bg-white border rounded shadow-sm">
                                                        <label class="fw-bold small mb-1">Price</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-white border-end-0">₹</span>
                                                            <input type="number" name="seller_cash_price" class="form-control border-start-0 ps-0" placeholder="Enter Price" value="<?= $vehicle['seller_cash_price'] ?? '' ?>">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="onlineBox" class="collapse <?= (isset($vehicle['seller_payment_type']) && $vehicle['seller_payment_type'] == 'online') ? 'show' : '' ?>" data-bs-parent="#payAccordion">
                                                    <div class="p-3 bg-white border rounded shadow-sm">
                                                        <label class="fw-bold small mb-2">Online Method</label>
                                                        <div class="d-flex flex-wrap gap-3 mb-3">
                                                            <?php
                                                            $methods = ['gpay' => 'Google Pay', 'paytm' => 'Paytm', 'phonepe' => 'PhonePe', 'bharatpe' => 'BharatPe'];
                                                            foreach ($methods as $val => $label):
                                                            ?>
                                                                <label class="form-check">
                                                                    <input type="radio" name="seller_online_method" class="form-check-input" value="<?= $val ?>"
                                                                        <?= (isset($vehicle['seller_online_method']) && $vehicle['seller_online_method'] == $val) ? 'checked' : '' ?>>
                                                                    <span class="form-check-label fw-bold"><?= $label ?></span>
                                                                </label>
                                                            <?php endforeach; ?>
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
                                            <input type="date" name="purchaser_date" class="form-control" value="2025-11-26">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label>Purchaser Name</label>
                                            <input type="text" name="purchaser_name" class="form-control text-uppercase">
                                        </div>
                                        <div class="col-12">
                                            <label>Address</label>
                                            <textarea name="purchaser_address" class="form-control text-uppercase" rows="2"></textarea>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label>Bike Name</label>
                                            <input type="text" name="purchaser_bike_name" class="form-control text-uppercase">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label>Vehicle No</label>
                                            <input type="text" name="purchaser_vehicle_no" class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000" value="WB ">
                                        </div>
                                    </div>

                                    <div class="bg-light p-3 rounded-4 border mb-4">
                                        <label class="text-dark mb-3 d-block">Purchaser Paper Payment Fees</label>

                                        <div class="row g-2 align-items-end mb-3">
                                            <div class="col-12 col-md-4">
                                                <label>Transfer Amount</label>
                                                <input type="number" name="purchaser_transfer_amount" class="form-control" placeholder="Amount">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Date</label>
                                                <input type="date" name="purchaser_transfer_date" class="form-control" value="2025-11-26">
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
                                                <input type="number" name="purchaser_hpa_amount" class="form-control" placeholder="Amount">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Date</label>
                                                <input type="date" name="purchaser_hpa_date" class="form-control" value="2025-11-26">
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
                                                <input type="number" name="purchaser_hp_amount" class="form-control" placeholder="Amount">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label>Date</label>
                                                <input type="date" name="purchaser_hp_date" class="form-control" value="2025-11-26">
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
                                                <select name="purchaser_insurance_name" class="form-control text-uppercase" required>
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
                                                    <option value="Reliance General Insurance">Reliance General Insurance</option>
                                                    <option value="Royal Sundaram Insurance">Royal Sundaram Insurance</option>
                                                    <option value="Chola MS Insurance">Chola MS Insurance</option>
                                                    <option value="HDFC ERGO">HDFC ERGO</option>
                                                    <option value="ECGC">ECGC</option>
                                                    <option value="Agriculture Insurance Company of India (AIC)">Agriculture Insurance Company of India (AIC)</option>
                                                    <option value="Star Health Insurance">Star Health Insurance</option>
                                                    <option value="Future Generali">Future Generali</option>
                                                    <option value="Universal Sompo">Universal Sompo</option>
                                                    <option value="Shriram General Insurance">Shriram General Insurance</option>
                                                    <option value="Raheja QBE">Raheja QBE</option>
                                                    <option value="SBI General Insurance">SBI General Insurance</option>
                                                    <option value="Niva Bupa Health Insurance">Niva Bupa Health Insurance</option>
                                                    <option value="L&T Insurance">L&T Insurance</option>
                                                    <option value="Care Health Insurance">Care Health Insurance</option>
                                                    <option value="Magma HDI">Magma HDI</option>
                                                    <option value="Liberty General Insurance">Liberty General Insurance</option>
                                                    <option value="Manipal Cigna">Manipal Cigna</option>
                                                    <option value="Kotak General Insurance">Kotak General Insurance</option>
                                                    <option value="Aditya Birla Capital Health Insurance">Aditya Birla Capital Health Insurance</option>
                                                    <option value="Digit Insurance">Digit Insurance</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">Payment Status</label>
                                                <select name="purchaser_insurance_payment_status" class="form-control" required>
                                                    <option value="">-- Select Status --</option>
                                                    <option value="paid">Paid</option>
                                                    <option value="due">Due</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">Amount</label>
                                                <input type="number" name="purchaser_insurance_amount" class="form-control" placeholder="Enter Amount" required>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">Issue Date</label>
                                                <input type="date" name="purchaser_insurance_issue_date" class="form-control" id="issueDate" value="2025-11-26" required>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="fw-bold">Expiry Date</label>
                                                <input type="date" name="purchaser_insurance_expiry_date" class="form-control" id="expiryDate" readonly required>
                                            </div>

                                            <span class="fw-bold text-primary">Validity:<span id="expiryText">--</span></span>
                                        </div>
                                    </div>

                                    <div class="bg-light p-3 rounded-4 border mb-3">
                                        <label class="mb-2">Price Breakdown</label>
                                        <div class="row g-2 mb-3">
                                            <div class="col-12">
                                                <input type="number" name="purchaser_total" id="p_total" class="form-control" placeholder="Total">
                                            </div>
                                            <div class="col-12">
                                                <input type="number" name="purchaser_paid" id="p_paid" class="form-control" placeholder="Paid">
                                            </div>
                                            <div class="col-12">
                                                <input type="number" name="purchaser_due" id="p_due" class="form-control bg-white" placeholder="Due" readonly>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-3 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="purchaser_payment_mode" id="rad_cash" value="cash" checked>
                                                <label class="fw-bold" for="rad_cash" data-bs-toggle="collapse" data-bs-target="#sec_cash" role="button">
                                                    Cash
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="purchaser_payment_mode" id="rad_fin" value="finance">
                                                <label class="fw-bold" for="rad_fin" data-bs-toggle="collapse" data-bs-target="#sec_finance" role="button">
                                                    Finance
                                                </label>
                                            </div>
                                        </div>

                                        <div id="payment_details_group">
                                            <div id="sec_cash" class="collapse show border-top pt-2 mt-2" data-bs-parent="#payment_details_group">
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <label>Amount</label>
                                                        <input type="number" name="purchaser_cash_amount" class="form-control" placeholder="Enter Amount">
                                                    </div>
                                                    <div class="col-12">
                                                        <label>Mobile Number 1</label>
                                                        <input type="tel" name="purchaser_cash_mobile1" class="form-control" placeholder="Enter Mobile Number">
                                                    </div>
                                                    <div class="col-12">
                                                        <label>Mobile Number 2</label>
                                                        <input type="tel" name="purchaser_cash_mobile2" class="form-control" placeholder="Enter Mobile Number">
                                                    </div>
                                                    <div class="col-12">
                                                        <label>Mobile Number 3</label>
                                                        <input type="tel" name="purchaser_cash_mobile3" class="form-control" placeholder="Enter Mobile Number">
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="sec_finance" class="collapse border-top pt-2 mt-2" data-bs-parent="#payment_details_group">
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <label>HPA With</label>
                                                        <input type="text" name="purchaser_fin_hpa_with" class="form-control text-uppercase" placeholder="Finance Company">
                                                    </div>
                                                    <div class="col-12">
                                                        <label>Disburse Amount</label>
                                                        <div class="input-group">
                                                            <input type="number" name="purchaser_fin_disburse_amount" class="form-control" placeholder="Amt">
                                                            <select name="purchaser_fin_disburse_status" class="form-select" style="max-width:100px;">
                                                                <option value="paid">Paid</option>
                                                                <option value="due">Due</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <label>Mobile Number 1</label>
                                                        <input type="tel" name="purchaser_fin_mobile1" class="form-control" placeholder="Mobile 1">
                                                    </div>
                                                    <div class="col-12">
                                                        <label>Mobile Number 2</label>
                                                        <input type="tel" name="purchaser_fin_mobile2" class="form-control" placeholder="Mobile 2">
                                                    </div>
                                                    <div class="col-12">
                                                        <label>Mobile Number 3</label>
                                                        <input type="tel" name="purchaser_fin_mobile3" class="form-control" placeholder="Mobile 3">
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
                                                    <input type="file" name="purchaser_doc_aadhar_front" accept="image/*" hidden>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="photo-upload-box">
                                                    <span class="small text-muted fw-bold">Aadhar Back</span>
                                                    <img src="">
                                                    <input type="file" name="purchaser_doc_aadhar_back" accept="image/*" hidden>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="photo-upload-box">
                                                    <span class="small text-muted fw-bold">Voter Front</span>
                                                    <img src="">
                                                    <input type="file" name="purchaser_doc_voter_front" accept="image/*" hidden>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="photo-upload-box">
                                                    <span class="small text-muted fw-bold">Voter Back</span>
                                                    <img src="">
                                                    <input type="file" name="purchaser_doc_voter_back" accept="image/*" hidden>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="purchaser_payment_all_paid" id="all_paid">
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
                                                <input type="text" class="form-control text-uppercase" placeholder="Enter Name" name="ot_name_transfer">
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Vehicle Number</label>
                                                <input type="text" class="form-control fw-bold text-uppercase" placeholder="WB 00 AA 0000" value="WB " name="ot_vehicle_number">
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
                                                <input type="text" class="form-control text-uppercase" placeholder="Vendor Name" name="ot_vendor_name">
                                            </div>
                                        </div>

                                        <div class="bg-light p-3 rounded-4 border mb-4">
                                            <label class="text-dark mb-3 d-block">Vendor Payment</label>

                                            <div class="row g-2 align-items-end mb-3">
                                                <div class="col-12 col-md-4">
                                                    <label>Transfer Amount</label>
                                                    <input type="number" class="form-control" placeholder="Amount" name="ot_transfer_amount">
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <label>Date</label>
                                                    <input type="date" class="form-control" value="2025-11-26" name="ot_transfer_date">
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
                                                    <input type="number" class="form-control" placeholder="Amount" name="ot_hpa_amount">
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <label>Date</label>
                                                    <input type="date" class="form-control" value="2025-11-26" name="ot_hpa_date">
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
                                                    <input type="number" class="form-control" placeholder="Amount" name="ot_hp_amount">
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <label>Date</label>
                                                    <input type="date" class="form-control" value="2025-11-26" name="ot_hp_date">
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
                                                    <select class="form-control text-uppercase" required name="ot_insurance_name">
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
                                                        <option value="Reliance General Insurance">Reliance General Insurance</option>
                                                        <option value="Royal Sundaram Insurance">Royal Sundaram Insurance</option>
                                                        <option value="Chola MS Insurance">Chola MS Insurance</option>
                                                        <option value="HDFC ERGO">HDFC ERGO</option>
                                                        <option value="ECGC">ECGC</option>
                                                        <option value="Agriculture Insurance Company of India (AIC)">Agriculture Insurance Company of India (AIC)</option>
                                                        <option value="Star Health Insurance">Star Health Insurance</option>
                                                        <option value="Future Generali">Future Generali</option>
                                                        <option value="Universal Sompo">Universal Sompo</option>
                                                        <option value="Shriram General Insurance">Shriram General Insurance</option>
                                                        <option value="Raheja QBE">Raheja QBE</option>
                                                        <option value="SBI General Insurance">SBI General Insurance</option>
                                                        <option value="Niva Bupa Health Insurance">Niva Bupa Health Insurance</option>
                                                        <option value="L&T Insurance">L&T Insurance</option>
                                                        <option value="Care Health Insurance">Care Health Insurance</option>
                                                        <option value="Magma HDI">Magma HDI</option>
                                                        <option value="Liberty General Insurance">Liberty General Insurance</option>
                                                        <option value="Manipal Cigna">Manipal Cigna</option>
                                                        <option value="Kotak General Insurance">Kotak General Insurance</option>
                                                        <option value="Aditya Birla Capital Health Insurance">Aditya Birla Capital Health Insurance</option>
                                                        <option value="Digit Insurance">Digit Insurance</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="fw-bold">Payment Status</label>
                                                    <select class="form-control" required name="ot_insurance_payment_status">
                                                        <option value="">-- Select Status --</option>
                                                        <option value="paid">Paid</option>
                                                        <option value="due">Due</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="fw-bold">Amount</label>
                                                    <input type="number" class="form-control" placeholder="Enter Amount" required name="ot_insurance_amount">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="fw-bold">Start Date</label>
                                                    <input type="date" class="form-control" id="startDate" value="2025-11-26" required name="ot_insurance_start_date">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="fw-bold">End Date</label>
                                                    <input type="date" class="form-control" id="endDate" readonly required name="ot_insurance_end_date">
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
                                                                <option>Yes</option>
                                                                <option>No</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label small">Date</label>
                                                            <input type="date" class="form-control" value="2025-11-26" name="ot_purchaser_sign_date">
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
                                                            <input type="date" class="form-control" value="2025-11-26" name="ot_seller_sign_date">
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
                                <span class="d-none d-sm-inline">Save Draft</span>
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
    </script>
</body>

</html>