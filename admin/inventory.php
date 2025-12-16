<?php
// Include DB connection
require "db.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

// 2. Run Query (Standard MySQLi style)
$query = $conn->prepare("SELECT user_id, email, role FROM users WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$u = $query->get_result()->fetch_assoc(); // Data is now in the $u array
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
</head>


<body>
    <!-- Loading Spinner Section - Displays a loading animation with 'LOADING...' text while the page initializes -->
    <div id="loader"
        style="position:fixed; inset:0; background:rgba(242,242,247,0.98); z-index:9999; display:flex; justify-content:center; align-items:center; flex-direction:column;">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
        <div class="mt-3 fw-bold text-secondary" style="letter-spacing: 1px;">LOADING...</div>
    </div>

    <!-- global Messages -->
    <?php include_once "global_message.php" ?>

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
                    <!-- Inventory Stats -->
                    <div class="d-flex justify-content-center text-center flex-nowrap overflow-auto"
                        style="gap:0.8rem;">

                        <!-- Total -->
                        <div class="d-flex flex-column align-items-center">
                            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width:40px; height:40px; font-weight:bold; font-size:0.9rem;">
                                17
                            </div>
                            <small class="mt-1 text-muted">Total</small>
                        </div>

                        <!-- Available -->
                        <div class="d-flex flex-column align-items-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width:40px; height:40px; font-weight:bold; font-size:0.9rem;">
                                12
                            </div>
                            <small class="mt-1 text-muted">Available</small>
                        </div>

                        <!-- Sold -->
                        <div class="d-flex flex-column align-items-center">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width:40px; height:40px; font-weight:bold; font-size:0.9rem;">
                                5
                            </div>
                            <small class="mt-1 text-muted">Sold</small>
                        </div>
                    </div>

                    <!-- Buttons: Back + Filters -->
                    <div class="d-flex gap-1 flex-wrap">
                        <button class="btn btn-secondary btn-sm rounded-pill px-3 py-1" onclick="history.back()">
                            <i class="ph-bold ph-arrow-left me-1"></i>Back
                        </button>
                        <button class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1" data-bs-toggle="collapse"
                            data-bs-target="#mobileFilters" aria-expanded="false">
                            <i class="ph-bold ph-faders me-1"></i>Filters
                        </button>
                    </div>

                </div>
            </div>

            <!-- Mobile Filter Dropdown (Collapsible) -->
            <div class="collapse" id="mobileFilters">
                <div class="card card-body p-2 p-md-3 mb-3 shadow-sm bg-white rounded-4">
                    <div class="row g-2">

                        <div class="col-12 col-sm-6 col-md-4">
                            <label class="form-label small text-muted fw-bold">Search</label>
                            <input type="text" class="form-control form-control-sm" placeholder="Name, Bike, Engine...">
                        </div>

                        <div class="col-6 col-sm-6 col-md-2">
                            <label class="form-label small text-muted fw-bold">Year</label>
                            <select class="form-select form-select-sm">
                                <option value="">All Years</option>
                                <option>2025</option>
                                <option>2024</option>
                                <option>2023</option>
                                <option>2022</option>
                                <option>2021</option>
                                <option>2020</option>
                                <option>2019</option>
                                <option>2018</option>
                                <option>2017</option>
                                <option>2016</option>
                                <!-- Add more years as needed -->
                            </select>
                        </div>


                        <div class="col-6 col-sm-6 col-md-2">
                            <label class="form-label small text-muted fw-bold">RTO</label>
                            <select class="form-select form-select-sm">
                                <option>All RTOs</option>
                                <option>Bankura</option>
                                <option>Bishnupur</option>
                                <option>Durgapur</option>
                                <option>Manbazar</option>
                                <option>Suri</option>
                                <option>Asansol</option>
                                <option>Kalimpong</option>
                            </select>
                        </div>

                        <div class="col-6 col-sm-6 col-md-2">
                            <label class="form-label small text-muted fw-bold">Status</label>
                            <select class="form-select form-select-sm">
                                <option>All</option>
                                <option class="text-success fw-bold">Available</option>
                                <option class="text-danger fw-bold">Sold Out</option>
                            </select>
                        </div>

                        <div class="col-6 col-sm-6 col-md-2">
                            <label class="form-label small text-muted fw-bold">Payment</label>
                            <select class="form-select form-select-sm">
                                <option>All</option>
                                <option class="text-success fw-bold">Cash</option>
                                <option class="text-primary fw-bold">Online</option>
                            </select>
                        </div>

                    </div>

                    <div class="mt-2 text-end">
                        <button class="btn btn-sm btn-secondary me-2" type="button" data-bs-toggle="collapse"
                            data-bs-target="#mobileFilters">Close</button>
                        <button class="btn btn-sm btn-danger" type="button">Apply</button>
                    </div>
                </div>
            </div>
        </div>




        <!-- Vehicle Data Grid - Displays inventory vehicles in a responsive card layout with availability status and action buttons -->
        <div class="position-relative" style="min-height: 400px;">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">


                <?php
                // 1. SQL Query (Joins all tables and selects ALL columns)
                // ⭐ FIX: We must use vs.* and vp.* to get paper details, payments, etc.
                $sql = "SELECT 
            v.id as vehicle_prim_id, 
            v.*,
            v.*, 
            vs.*, 
            vp.*, 
            ot.*
        FROM vehicle v
        LEFT JOIN vehicle_seller vs ON v.id = vs.vehicle_id
        LEFT JOIN vehicle_purchaser vp ON v.id = vp.vehicle_id
        LEFT JOIN vehicle_ot ot ON v.id = ot.vehicle_id
        ORDER BY v.id DESC";

                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()):

                    // ⭐ IMAGE LOGIC: Fix path issues
                    if (!empty($row['photo1'])) {
                        $imageSrc = "../images/" . $row['photo1'];
                    } else {
                        $imageSrc = "../images/default.jpg";
                    }

                    // ⭐ STATUS LOGIC
                    $isAvailable = ($row['sold_out'] == 0);
                    $statusText = $isAvailable ? "AVAILABLE" : "SOLD OUT";
                    $statusClass = $isAvailable ? "text-success" : "text-danger";

                    // ⭐ UNIQUE MODAL ID
                    $modalID = "viewModal_" . $row['id'];

                    // ⭐ PREVENT WARNINGS: Set defaults for keys that might be null
                    $row['pr_rc'] = $row['pr_rc'] ?? 0;
                    $row['pr_noc'] = $row['pr_noc'] ?? 0;
                    $row['seller_payment_type'] = $row['seller_payment_type'] ?? '';
                ?>

                    <div class="col">
                        <div class="card border-0 inventory-card h-100">
                            <div class="hover-card position-relative overflow-hidden">

                                <img src="<?= $imageSrc; ?>" class="d-block w-100 h-100 object-fit-cover" loading="lazy"
                                    style="height: 300px !important;" alt="Bike">

                                <div class="position-absolute top-0 end-0 p-3 z-2 mt-2">
                                    <span
                                        class="badge status-badge <?= $statusClass ?> fw-bold bg-white shadow-sm rounded-pill px-3 py-2"
                                        style="font-size: 11px; letter-spacing: 0.5px;">
                                        <i class="ph-fill ph-circle me-1"></i> <?= $statusText ?>
                                    </span>
                                </div>

                                <div class="info-overlay d-flex flex-column gap-2">
                                    <div class="d-flex justify-content-between align-items-end">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark fs-5"><?= $row['vehicle_number']; ?></h6>
                                            <small class="text-muted"><?= $row['name']; ?></small>
                                        </div>
                                        <div class="fw-bold text-primary fs-4">₹ <?= number_format($row['cash_price']); ?>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        <span
                                            class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle fw-normal">
                                            <?= date('Y', strtotime($row['register_date'])); ?>
                                        </span>
                                        <span
                                            class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle fw-normal">
                                            <?= $row['owner_serial']; ?> Owner
                                        </span>
                                    </div>

                                    <div class="d-flex gap-2 mt-3 align-items-center">

                                        <!-- Edit -->
                                        <a href="edit_inventory.php?id=<?= $row['vehicle_prim_id']; ?>"
                                            class="btn btn-sm btn-dark fw-bold flex-grow-1 rounded-pill py-2 text-center">
                                            <i class="ph-bold ph-pencil-simple text-white me-1"></i> Edit
                                        </a>

                                        <!-- Delete -->
                                        <a href="delete_vehicle.php?id=<?= $row['vehicle_prim_id']; ?>"
                                            class="btn btn-sm btn-danger fw-bold flex-grow-1 rounded-pill py-2 text-center"
                                            onclick="return confirmDeleteVehicle();">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </a>

                                        <!-- View -->
                                        <button type="button"
                                            class="btn btn-sm btn-outline-dark fw-bold flex-grow-1 rounded-pill py-2 text-center"
                                            data-bs-toggle="modal"
                                            data-bs-target="#<?= $modalID; ?>">
                                            View
                                        </button>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>


                    <!-- View Vehicle Modal -->
                    <div class="modal fade p-0" id="<?= $modalID; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen m-0">
                            <div class="modal-content rounded-0 border-0 h-100">
                                <!-- Header -->
                                <div class="modal-header border-bottom bg-white px-4 py-3 sticky-top z-3">
                                    <div class="d-flex align-items-center gap-3 w-100">
                                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm border"
                                            style="width: 45px; height: 45px; overflow: hidden; padding: 2px;">
                                            <img src="../images/logo.jpeg" alt="Chowdhury Automobile" class="rounded-circle"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div class="lh-1">
                                            <h5 class="modal-title fw-bold text-dark mb-1"><?php echo htmlspecialchars($row['vehicle_number']); ?></h5>
                                            <div class="d-flex align-items-center gap-2 small text-muted">
                                                <span class="fw-bold text-uppercase"><?php echo htmlspecialchars($row['name']); ?></span>
                                                <i class="ph-fill ph-dot text-muted" style="font-size: 8px;"></i>
                                                <span
                                                    class="badge <?php echo $row['sold_out'] ? 'bg-danger' : 'bg-success'; ?> text-white border border-danger-subtle rounded-pill"><?php echo $row['sold_out'] ? 'Sold Out' : 'Available'; ?></span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close ms-auto bg-light rounded-circle p-2"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                </div>

                                <div class="modal-body p-0 bg-light">
                                    <div class="accordion accordion-flush p-3" id="dealDetailsAccordion">

                                        <!-- ==========================
                             STEP 1: VEHICLE DETAILS
                             ========================== -->
                                        <div class="accordion-item rounded-4 shadow-sm border-0 mb-3 overflow-hidden">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button fw-bold text-uppercase text-dark py-3" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseVehicle_<?= $row['id']; ?>">
                                                    <i class="ph-bold ph-moped me-2 text-primary fs-5"></i> Vehicle Details
                                                </button>
                                            </h2>
                                            <div id="collapseVehicle_<?= $row['id']; ?>" class="accordion-collapse collapse show"
                                                data-bs-parent="#dealDetailsAccordion">
                                                <div class="accordion-body bg-white p-4 border-top">

                                                    <h6 class="fw-bold text-muted small text-uppercase mb-3">Vehicle Photos</h6>
                                                    <div class="row g-3 mb-4">
                                                        <?php
                                                        // Check all 4 photo columns
                                                        for ($i = 1; $i <= 4; $i++):
                                                            $photoKey = "photo" . $i;
                                                            if (!empty($row[$photoKey])):
                                                                $imgSrc = "../images/" . $row[$photoKey];
                                                        ?>
                                                                <div class="col-6 col-md-3">
                                                                    <div class="ratio ratio-1x1 rounded-4 overflow-hidden border mb-2 bg-light">
                                                                        <img src="<?= $imgSrc; ?>" class="object-fit-cover">
                                                                    </div>
                                                                    <a href="<?= $imgSrc; ?>" download class="btn btn-sm btn-dark rounded-pill w-100 fw-bold py-1" style="font-size: 11px;">Download</a>
                                                                </div>
                                                        <?php endif;
                                                        endfor; ?>

                                                        <?php if (empty($row['photo1']) && empty($row['photo2']) && empty($row['photo3']) && empty($row['photo4'])): ?>
                                                            <div class="col-12 text-muted small">No photos uploaded.</div>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="p-3 bg-light rounded-4 border mb-3">
                                                        <div class="row g-3">
                                                            <div class="col-6 col-md-4">
                                                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Register Date</small>
                                                                <div class="fw-bold text-dark"><?= date('d-M-Y', strtotime($row['register_date'])); ?></div>
                                                            </div>
                                                            <div class="col-6 col-md-4">
                                                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Vehicle Type</small>
                                                                <div class="fw-bold text-dark"><?= $row['vehicle_type']; ?></div>
                                                            </div>
                                                            <div class="col-6 col-md-4">
                                                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Owner Serial</small>
                                                                <div class="fw-bold text-dark"><?= $row['owner_serial']; ?></div>
                                                            </div>
                                                            <div class="col-12 col-md-12">
                                                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Bike Name</small>
                                                                <div class="fw-bold text-dark"><?= $row['name']; ?></div>
                                                            </div>
                                                            <div class="col-12 col-md-12">
                                                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Vehicle Number</small>
                                                                <div class="fw-bold text-dark text-uppercase"><?= $row['vehicle_number']; ?></div>
                                                            </div>
                                                            <div class="col-12 col-md-12">
                                                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Chassis Number</small>
                                                                <div class="fw-bold text-dark font-monospace"><?= $row['chassis_number']; ?></div>
                                                            </div>
                                                            <div class="col-12 col-md-12">
                                                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Engine Number</small>
                                                                <div class="fw-bold text-dark font-monospace"><?= $row['engine_number']; ?></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <?php
                                                    $isOnline = ($row['payment_type'] == 'Online');
                                                    $price = $isOnline ? $row['online_price'] : $row['cash_price'];
                                                    $method = $isOnline ? $row['online_method'] : 'Cash Payment';
                                                    $txnId = $isOnline ? $row['online_transaction_id'] : 'N/A';
                                                    ?>
                                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                                        <div>
                                                            <small class="text-muted text-uppercase fw-bold">Payment Mode</small>
                                                            <div class="fw-bold text-primary">
                                                                <i class="ph-bold <?= $isOnline ? 'ph-globe' : 'ph-money'; ?> me-1"></i> <?= $method; ?>
                                                            </div>
                                                        </div>
                                                        <div class="text-end">
                                                            <small class="text-muted text-uppercase fw-bold">Price</small>
                                                            <div class="fs-4 fw-bold text-dark">₹ <?= number_format($price, 2); ?></div>
                                                        </div>
                                                    </div>

                                                    <?php if ($isOnline): ?>
                                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                                            <div>
                                                                <small class="text-muted text-uppercase fw-bold">Transaction ID</small>
                                                                <div class="fw-bold text-primary text-break"><?= $txnId; ?></div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="border rounded-4 overflow-hidden">
                                                        <div class="bg-light px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                                            <h6 class="fw-bold text-danger mb-0 small text-uppercase">Police Challan Details</h6>
                                                            <span class="badge <?= ($row['police_challan'] == 'Yes') ? 'bg-danger' : 'bg-success'; ?>">
                                                                <?= $row['police_challan']; ?>
                                                            </span>
                                                        </div>

                                                        <?php if ($row['police_challan'] == 'Yes'): ?>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm align-middle mb-0 text-center">
                                                                    <thead>
                                                                        <tr class="text-muted small">
                                                                            <th class="py-2">#</th>
                                                                            <th class="py-2">Challan No</th>
                                                                            <th class="py-2">Amt</th>
                                                                            <th class="py-2">Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        // Loop through challan 1 to 3
                                                                        for ($c = 1; $c <= 3; $c++):
                                                                            $cNum = $row['challan' . $c . '_number'];
                                                                            $cAmt = $row['challan' . $c . '_amount'];
                                                                            $cStatus = $row['challan' . $c . '_status'];

                                                                            // Only display row if Challan Number exists
                                                                            if (!empty($cNum)):
                                                                        ?>
                                                                                <tr class="border-bottom">
                                                                                    <td><?= $c; ?></td>
                                                                                    <td class="font-monospace small"><?= $cNum; ?></td>
                                                                                    <td class="fw-bold">₹<?= $cAmt; ?></td>
                                                                                    <td>
                                                                                        <?php if ($cStatus == 'Paid'): ?>
                                                                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
                                                                                        <?php else: ?>
                                                                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Pending</span>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                </tr>
                                                                        <?php endif;
                                                                        endfor; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="p-3 text-center text-muted small">No Challans Reported.</div>
                                                        <?php endif; ?>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- ==========================
                             STEP 2: SELLER DETAILS
                             ========================== -->
                                        <div class="accordion-item rounded-4 shadow-sm border-0 mb-3 overflow-hidden">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed fw-bold text-uppercase text-dark py-3" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseSeller_<?= $row['id']; ?>">
                                                    <i class="ph-bold ph-user-circle me-2 text-primary fs-5"></i> Seller Details
                                                </button>
                                            </h2>
                                            <div id="collapseSeller_<?= $row['id']; ?>" class="accordion-collapse collapse"
                                                data-bs-parent="#dealDetailsAccordion">
                                                <div class="accordion-body bg-white p-4 border-top">

                                                    <div class="d-flex justify-content-between mb-3">
                                                        <div>
                                                            <small class="text-muted text-uppercase fw-bold">Seller Name</small>
                                                            <div class="fs-5 fw-bold text-dark"><?= $row['seller_name']; ?></div>
                                                        </div>
                                                        <div class="text-end">
                                                            <small class="text-muted text-uppercase fw-bold">Date</small>
                                                            <div class="fw-bold text-dark">
                                                                <?= (!empty($row['seller_date'])) ? date('Y-m-d', strtotime($row['seller_date'])) : '-'; ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row g-2 mb-3">
                                                        <div class="col-12">
                                                            <small class="text-muted fw-bold" style="font-size:10px;">VEHICLE NO</small>
                                                            <div class="fw-bold text-dark small text-uppercase"><?= $row['seller_vehicle_number']; ?></div>
                                                        </div>
                                                        <div class="col-12">
                                                            <small class="text-muted fw-bold" style="font-size:10px;">BIKE NAME</small>
                                                            <div class="fw-bold text-dark small"><?= $row['seller_bike_name']; ?></div>
                                                        </div>
                                                        <div class="col-12">
                                                            <small class="text-muted fw-bold" style="font-size:10px;">CHASSIS NO</small>
                                                            <div class="fw-bold text-dark font-monospace small"><?= $row['seller_chassis_no']; ?></div>
                                                        </div>
                                                        <div class="col-12">
                                                            <small class="text-muted fw-bold" style="font-size:10px;">ENGINE NO</small>
                                                            <div class="fw-bold text-dark font-monospace small"><?= $row['seller_engine_no']; ?></div>
                                                        </div>
                                                    </div>

                                                    <div class="bg-light p-3 rounded-3 mb-3 border">
                                                        <small class="text-muted fw-bold">Address</small>
                                                        <div class="mb-2"><?= $row['seller_address']; ?></div>
                                                        <div class="d-flex gap-2 flex-wrap">
                                                            <?php
                                                            // Loop through mobile 1, 2, 3
                                                            for ($m = 1; $m <= 3; $m++):
                                                                $mob = $row['seller_mobile' . $m];
                                                                if (!empty($mob)):
                                                            ?>
                                                                    <a href="tel:<?= $mob; ?>" class="badge bg-white text-dark border text-decoration-none py-2">
                                                                        <i class="ph-fill ph-phone me-1"></i> <?= $mob; ?>
                                                                    </a>
                                                            <?php endif;
                                                            endfor; ?>
                                                        </div>
                                                    </div>

                                                    <h6 class="fw-bold text-muted small text-uppercase mb-3">Seller Documents</h6>
                                                    <div class="row g-2">
                                                        <?php
                                                        // Array to map DB columns to Display Labels
                                                        $docs = [
                                                            'doc_aadhar_front' => 'AADHAR FRONT',
                                                            'doc_aadhar_back'  => 'AADHAR BACK',
                                                            'doc_voter_front'  => 'VOTER FRONT',
                                                            'doc_voter_back'   => 'VOTER BACK'
                                                        ];

                                                        foreach ($docs as $col => $label):
                                                            if (!empty($row[$col])):
                                                                $imgSrc = "../images/" . $row[$col];
                                                        ?>
                                                                <div class="col-6 col-md-3">
                                                                    <div class="border rounded p-2 text-center bg-white h-100">
                                                                        <small class="fw-bold d-block mb-1" style="font-size:10px"><?= $label; ?></small>
                                                                        <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden">
                                                                            <img src="<?= $imgSrc; ?>" class="object-fit-cover">
                                                                        </div>
                                                                        <a href="<?= $imgSrc; ?>" target="_blank" class="btn btn-dark btn-sm w-100 py-0 mb-1" style="font-size:10px">View</a>
                                                                        <a href="<?= $imgSrc; ?>" download class="btn btn-secondary btn-sm w-100 py-0" style="font-size:10px">Download</a>
                                                                    </div>
                                                                </div>
                                                        <?php endif;
                                                        endforeach; ?>
                                                    </div>

                                                    <div class="row g-3 mb-3 mt-2">
                                                        <div class="col-12">
                                                            <label class="small text-muted fw-bold mb-1">PAPERS RECEIVED</label>
                                                            <div class="d-flex flex-wrap gap-2">
                                                                <span class="badge <?= ($row['pr_rc'] == 1) ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border text-decoration-line-through'; ?>">RC</span>
                                                                <span class="badge <?= ($row['pr_tax'] == 1) ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border text-decoration-line-through'; ?>">Tax Token</span>
                                                                <span class="badge <?= ($row['pr_insurance'] == 1) ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border text-decoration-line-through'; ?>">Insurance</span>
                                                                <span class="badge <?= ($row['pr_pucc'] == 1) ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border text-decoration-line-through'; ?>">PUCC</span>
                                                                <span class="badge <?= ($row['pr_noc'] == 1) ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border text-decoration-line-through'; ?>">NOC</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <?php if (!empty($row['noc_front']) || !empty($row['noc_back'])): ?>
                                                        <div class="border rounded-4 p-3 mb-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <small class="fw-bold text-uppercase text-muted">NOC Details</small>
                                                                <span class="badge <?= ($row['noc_status'] == 'Paid') ? 'bg-success' : 'bg-danger'; ?>"><?= $row['noc_status']; ?></span>
                                                            </div>
                                                            <div class="row g-2">
                                                                <?php
                                                                $nocDocs = ['noc_front' => 'NOC FRONT', 'noc_back' => 'NOC BACK'];
                                                                foreach ($nocDocs as $col => $label):
                                                                    if (!empty($row[$col])): $imgSrc = "../images/" . $row[$col];
                                                                ?>
                                                                        <div class="col-6">
                                                                            <div class="border rounded p-2 text-center bg-white">
                                                                                <small class="fw-bold d-block mb-1" style="font-size:10px"><?= $label; ?></small>
                                                                                <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden" style="width:50px; height:50px; margin:auto;">
                                                                                    <img src="<?= $imgSrc; ?>" class="object-fit-cover">
                                                                                </div>
                                                                                <a href="<?= $imgSrc; ?>" target="_blank" class="btn btn-outline-dark btn-sm w-100 py-0 mb-1" style="font-size:10px">View</a>
                                                                                <a href="<?= $imgSrc; ?>" download class="btn btn-dark btn-sm w-100 py-0" style="font-size:10px">Download</a>
                                                                            </div>
                                                                        </div>
                                                                <?php endif;
                                                                endforeach; ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($row['rc_front']) || !empty($row['rc_back'])): ?>
                                                        <div class="border rounded-4 p-3 mb-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <small class="fw-bold text-uppercase text-muted">RC Details</small>
                                                            </div>
                                                            <div class="row g-2">
                                                                <?php
                                                                $rcDocs = ['rc_front' => 'RC FRONT', 'rc_back' => 'RC BACK'];
                                                                foreach ($rcDocs as $col => $label):
                                                                    if (!empty($row[$col])): $imgSrc = "../images/" . $row[$col];
                                                                ?>
                                                                        <div class="col-6">
                                                                            <div class="border rounded p-2 text-center bg-white">
                                                                                <small class="fw-bold d-block mb-1" style="font-size:10px"><?= $label; ?></small>
                                                                                <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden" style="width:50px; height:50px; margin:auto;">
                                                                                    <img src="<?= $imgSrc; ?>" class="object-fit-cover">
                                                                                </div>
                                                                                <a href="<?= $imgSrc; ?>" target="_blank" class="btn btn-outline-dark btn-sm w-100 py-0 mb-1" style="font-size:10px">View</a>
                                                                                <a href="<?= $imgSrc; ?>" download class="btn btn-dark btn-sm w-100 py-0" style="font-size:10px">Download</a>
                                                                            </div>
                                                                        </div>
                                                                <?php endif;
                                                                endforeach; ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="bg-light border rounded-4 p-3 mb-3">
                                                        <h6 class="fw-bold small mb-2">Payment Details</h6>

                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="small text-muted">Type:</span>
                                                            <span class="fw-bold text-dark">
                                                                <?php if ($row['seller_payment_type'] == 'Online'): ?>
                                                                    Online (<?= $row['seller_online_method']; ?>)
                                                                <?php else: ?>
                                                                    Cash
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>

                                                        <?php if ($row['seller_payment_type'] == 'Online'): ?>
                                                            <div class="d-flex justify-content-between mb-3">
                                                                <span class="small text-muted">Txn ID:</span>
                                                                <span class="fw-bold font-monospace small text-break"><?= $row['seller_online_transaction_id']; ?></span>
                                                            </div>
                                                        <?php endif; ?>

                                                        <div class="d-flex text-center border rounded overflow-hidden bg-white">
                                                            <div class="flex-fill p-2 border-end">
                                                                <div class="small text-muted fw-bold" style="font-size:10px">TOTAL</div>
                                                                <div class="fw-bold">₹<?= number_format($row['total_amount'], 0); ?></div>
                                                            </div>
                                                            <div class="flex-fill p-2 border-end bg-success-subtle text-success">
                                                                <div class="small fw-bold" style="font-size:10px">PAID</div>
                                                                <div class="fw-bold">₹<?= number_format($row['paid_amount'], 0); ?></div>
                                                            </div>
                                                            <div class="flex-fill p-2 bg-danger-subtle text-danger">
                                                                <div class="small fw-bold" style="font-size:10px">DUE</div>
                                                                <div class="fw-bold">₹<?= number_format($row['due_amount'], 0); ?></div>
                                                            </div>
                                                        </div>

                                                        <?php if ($row['due_amount'] > 0 && !empty($row['due_reason'])): ?>
                                                            <div class="small text-danger mt-2 fst-italic">
                                                                <i class="ph-bold ph-info me-1"></i> <?= $row['due_reason']; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <small class="text-muted fw-bold" style="font-size:10px">SHOWROOM</small>
                                                            <div class="fw-bold small"><?= $row['exchange_showroom_name'] ?? '-'; ?></div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted fw-bold" style="font-size:10px">STAFF</small>
                                                            <div class="fw-bold small"><?= $row['staff_name'] ?? '-'; ?></div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- ==========================
                             STEP 3: PURCHASER DETAILS
                             ========================== -->
                                        <?php if ($row['sold_out'] == 1): // Only show if vehicle is sold 
                                        ?>
                                            <div class="accordion-item rounded-4 shadow-sm border-0 mb-3 overflow-hidden">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed fw-bold text-uppercase text-dark py-3"
                                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapsePurchaser_<?= $row['id']; ?>">
                                                        <i class="ph-bold ph-shopping-bag me-2 text-primary fs-5"></i> Purchaser Details
                                                    </button>
                                                </h2>
                                                <div id="collapsePurchaser_<?= $row['id']; ?>" class="accordion-collapse collapse"
                                                    data-bs-parent="#dealDetailsAccordion">
                                                    <div class="accordion-body bg-white p-4 border-top">

                                                        <div class="row g-3 mb-3">
                                                            <div class="col-12">
                                                                <small class="text-muted text-uppercase fw-bold">Purchaser Name</small>
                                                                <div class="fs-5 fw-bold text-dark"><?= $row['purchaser_name']; ?></div>
                                                                <small class="text-muted">Date: <?= date('d-M-Y', strtotime($row['purchaser_date'])); ?></small>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="bg-light p-3 rounded border">
                                                                    <small class="text-muted text-uppercase fw-bold">Address</small>
                                                                    <div><?= $row['purchaser_address']; ?></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted fw-bold" style="font-size:10px;">BIKE NAME</small>
                                                                <div class="fw-bold text-dark"><?= $row['purchaser_bike_name']; ?></div>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted fw-bold" style="font-size:10px;">VEHICLE NO</small>
                                                                <div class="fw-bold text-dark text-uppercase"><?= $row['purchaser_vehicle_no']; ?></div>
                                                            </div>
                                                        </div>

                                                        <div class="border rounded-4 overflow-hidden mb-4">
                                                            <div class="bg-light px-3 py-2 border-bottom">
                                                                <h6 class="fw-bold mb-0 small text-uppercase">Buyer Payment Fees</h6>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm align-middle mb-0 text-center">
                                                                    <thead>
                                                                        <tr class="text-muted small">
                                                                            <th class="py-2 text-start ps-3">Type</th>
                                                                            <th class="py-2">Amt</th>
                                                                            <th class="py-2">Date</th>
                                                                            <th class="py-2">Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        $fees = [
                                                                            'Transfer' => ['amt' => 'purchaser_transfer_amount', 'date' => 'purchaser_transfer_date', 'status' => 'purchaser_transfer_status'],
                                                                            'HPA'      => ['amt' => 'purchaser_hpa_amount',      'date' => 'purchaser_hpa_date',      'status' => 'purchaser_hpa_status'],
                                                                            'HP'       => ['amt' => 'purchaser_hp_amount',       'date' => 'purchaser_hp_date',       'status' => 'purchaser_hp_status'],
                                                                        ];
                                                                        foreach ($fees as $label => $cols):
                                                                            if ($row[$cols['amt']] > 0): // Only show if amount exists
                                                                        ?>
                                                                                <tr>
                                                                                    <td class="text-start ps-3 small fw-bold"><?= $label; ?></td>
                                                                                    <td>₹<?= number_format($row[$cols['amt']]); ?></td>
                                                                                    <td class="small text-muted"><?= (!empty($row[$cols['date']])) ? date('M d', strtotime($row[$cols['date']])) : '-'; ?></td>
                                                                                    <td>
                                                                                        <?php if ($row[$cols['status']] == 'Paid'): ?>
                                                                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
                                                                                        <?php else: ?>
                                                                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Due</span>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                </tr>
                                                                        <?php endif;
                                                                        endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                        <?php if (!empty($row['purchaser_insurance_name'])):
                                                            // Calculate Validity
                                                            $insStart = new DateTime($row['purchaser_insurance_issue_date']);
                                                            $insEnd   = new DateTime($row['purchaser_insurance_expiry_date']);
                                                            $interval = $insStart->diff($insEnd);
                                                            $validity = $interval->y . " Year" . ($interval->y > 1 ? 's' : '');
                                                        ?>
                                                            <div class="p-3 border rounded-4 bg-light mb-4 position-relative">
                                                                <span class="position-absolute top-0 start-50 translate-middle badge bg-dark text-white border-light border">
                                                                    Insurance Details
                                                                </span>
                                                                <div class="row g-2 mt-1">
                                                                    <div class="col-12">
                                                                        <div class="d-flex justify-content-between">
                                                                            <span class="small text-muted">Provider:</span>
                                                                            <span class="fw-bold"><?= $row['purchaser_insurance_name']; ?></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="d-flex justify-content-between">
                                                                            <span class="small text-muted">Payment Status:</span>
                                                                            <span class="fw-bold <?= ($row['purchaser_insurance_payment_status'] == 'paid') ? 'text-success' : 'text-danger'; ?>">
                                                                                <?= strtoupper($row['purchaser_insurance_payment_status']); ?>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="d-flex justify-content-between">
                                                                            <span class="small text-muted">Amount:</span>
                                                                            <span class="fw-bold">₹ <?= number_format($row['purchaser_insurance_amount']); ?></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="d-flex justify-content-between">
                                                                            <span class="small text-muted">Issued On:</span>
                                                                            <span class="fw-bold"><?= $row['purchaser_insurance_issue_date']; ?></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="d-flex justify-content-between">
                                                                            <span class="small text-muted">Expiry Date:</span>
                                                                            <span class="fw-bold text-danger"><?= $row['purchaser_insurance_expiry_date']; ?></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="d-flex justify-content-between">
                                                                            <span class="small text-muted">Validity:</span>
                                                                            <span class="fw-bold text-primary"><?= $validity; ?></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if ($row['purchaser_payment_mode'] == 'Finance'): ?>
                                                            <div class="alert alert-primary border-primary mb-3">
                                                                <div class="d-flex justify-content-between align-items-center border-bottom border-primary-subtle pb-2 mb-2">
                                                                    <span class="badge bg-primary">Finance Mode</span>
                                                                    <small class="fw-bold text-primary">HPA Active</small>
                                                                </div>
                                                                <div class="row g-2">
                                                                    <div class="col-12">
                                                                        <small class="text-primary-emphasis fw-bold" style="font-size:10px">FINANCE COMPANY</small>
                                                                        <div class="fw-bold text-dark"><?= $row['purchaser_fin_hpa_with']; ?></div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <div>
                                                                                <small class="text-primary-emphasis fw-bold" style="font-size:10px">DISBURSED AMT</small>
                                                                                <div class="fw-bold text-dark">₹<?= number_format($row['purchaser_fin_disburse_amount']); ?></div>
                                                                            </div>
                                                                            <span class="badge <?= ($row['purchaser_fin_disburse_status'] == 'Paid') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                                                                <?= $row['purchaser_fin_disburse_status']; ?>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 mt-2 pt-2 border-top border-primary-subtle">
                                                                        <small class="text-primary-emphasis fw-bold d-block mb-1" style="font-size:12px">REGISTERED MOBILES</small>
                                                                        <div class="d-flex gap-3 flex-wrap">
                                                                            <?php for ($m = 1; $m <= 3; $m++): $pmob = $row['purchaser_fin_mobile' . $m];
                                                                                if (!empty($pmob)): ?>
                                                                                    <a href="tel:<?= $pmob; ?>" class="badge bg-white text-dark border text-decoration-none">
                                                                                        <i class="ph-fill ph-phone"></i> <?= $pmob; ?>
                                                                                    </a>
                                                                            <?php endif;
                                                                            endfor; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>

                                                        <div class="d-flex text-center border rounded overflow-hidden bg-white mb-3">
                                                            <div class="flex-fill p-2 border-end">
                                                                <div class="small text-muted fw-bold" style="font-size:10px">TOTAL</div>
                                                                <div class="fw-bold">₹<?= number_format($row['purchaser_total']); ?></div>
                                                            </div>
                                                            <div class="flex-fill p-2 border-end bg-success-subtle text-success">
                                                                <div class="small fw-bold" style="font-size:10px">PAID</div>
                                                                <div class="fw-bold">₹<?= number_format($row['purchaser_paid']); ?></div>
                                                            </div>
                                                            <div class="flex-fill p-2 bg-danger-subtle text-danger">
                                                                <div class="small fw-bold" style="font-size:10px">DUE</div>
                                                                <div class="fw-bold">₹<?= number_format($row['purchaser_due']); ?></div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-4">
                                                            <label class="small text-muted fw-bold">PAYMENT ALL PAID?</label>
                                                            <?php if ($row['purchaser_payment_all_paid'] == 1): ?>
                                                                <div class="fw-bold text-success">Yes, All Clear</div>
                                                            <?php else: ?>
                                                                <div class="fw-bold text-danger">No, Payment Pending</div>
                                                            <?php endif; ?>
                                                        </div>

                                                        <h6 class="fw-bold text-muted small text-uppercase mb-3">Purchaser Documents</h6>
                                                        <div class="row g-2">
                                                            <?php
                                                            $pDocs = [
                                                                'purchaser_doc_aadhar_front' => 'AADHAR FRONT',
                                                                'purchaser_doc_aadhar_back'  => 'AADHAR BACK',
                                                                'purchaser_doc_voter_front'  => 'VOTER FRONT',
                                                                'purchaser_doc_voter_back'   => 'VOTER BACK'
                                                            ];
                                                            foreach ($pDocs as $col => $label):
                                                                if (!empty($row[$col])): $imgSrc = "../images/" . $row[$col];
                                                            ?>
                                                                    <div class="col-6 col-md-3">
                                                                        <div class="border rounded p-2 text-center bg-white h-100">
                                                                            <small class="fw-bold d-block mb-1" style="font-size:10px"><?= $label; ?></small>
                                                                            <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden">
                                                                                <img src="<?= $imgSrc; ?>" class="w-100 h-100 object-fit-cover">
                                                                            </div>
                                                                            <a href="<?= $imgSrc; ?>" target="_blank" class="btn btn-dark btn-sm w-100 py-0 mb-1" style="font-size:10px">View</a>
                                                                            <a href="<?= $imgSrc; ?>" download class="btn btn-secondary btn-sm w-100 py-0" style="font-size:10px">Download</a>
                                                                        </div>
                                                                    </div>
                                                            <?php endif;
                                                            endforeach; ?>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- ==========================
                             STEP 4: OWNERSHIP TRANSFER
                             ========================== -->
                                        <div class="accordion-item rounded-4 shadow-sm border-0 mb-3 overflow-hidden">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed fw-bold text-uppercase text-dark py-3"
                                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseTransfer_<?= $row['id']; ?>">
                                                    <i class="ph-bold ph-arrows-left-right me-2 text-primary fs-5"></i>
                                                    Ownership Transfer
                                                </button>
                                            </h2>
                                            <div id="collapseTransfer_<?= $row['id']; ?>" class="accordion-collapse collapse"
                                                data-bs-parent="#dealDetailsAccordion">
                                                <div class="accordion-body bg-white p-4 border-top">

                                                    <div class="row g-3 mb-4">
                                                        <div class="col-12 col-md-6">
                                                            <small class="text-muted fw-bold text-uppercase" style="font-size:10px">Transfer Name To</small>
                                                            <div class="fw-bold text-dark"><?= $row['ot_name_transfer'] ?? '-'; ?></div>
                                                        </div>
                                                        <div class="col-6 col-md-6">
                                                            <small class="text-muted fw-bold text-uppercase" style="font-size:10px">Vehicle Number</small>
                                                            <div class="fw-bold text-dark text-uppercase"><?= $row['ot_vehicle_number'] ?? '-'; ?></div>
                                                        </div>
                                                        <div class="col-6 col-md-6">
                                                            <small class="text-muted fw-bold text-uppercase" style="font-size:10px">RTO Location</small>
                                                            <div class="fw-bold text-dark"><?= $row['ot_rto_name'] ?? '-'; ?></div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <small class="text-muted fw-bold text-uppercase" style="font-size:10px">Vendor Name</small>
                                                            <div class="fw-bold text-primary"><?= $row['ot_vendor_name'] ?? '-'; ?></div>
                                                        </div>
                                                    </div>

                                                    <div class="border rounded-4 overflow-hidden mb-4">
                                                        <div class="bg-light px-3 py-2 border-bottom">
                                                            <h6 class="fw-bold mb-0 small text-uppercase">Vendor Payment Details</h6>
                                                        </div>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm align-middle mb-0 text-center">
                                                                <thead>
                                                                    <tr class="text-muted small">
                                                                        <th class="py-2 text-start ps-3">Type</th>
                                                                        <th class="py-2">Amt</th>
                                                                        <th class="py-2">Date</th>
                                                                        <th class="py-2">Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    // Define rows mapping
                                                                    $otFees = [
                                                                        'Transfer' => ['amt' => 'ot_transfer_amount', 'date' => 'ot_transfer_date', 'status' => 'ot_transfer_status'],
                                                                        'HPA'      => ['amt' => 'ot_hpa_amount',      'date' => 'ot_hpa_date',      'status' => 'ot_hpa_status'],
                                                                        'HP'       => ['amt' => 'ot_hp_amount',       'date' => 'ot_hp_date',       'status' => 'ot_hp_status'],
                                                                    ];

                                                                    foreach ($otFees as $label => $cols):
                                                                        if (($row[$cols['amt']] ?? 0) > 0): // Only show if amount > 0
                                                                    ?>
                                                                            <tr>
                                                                                <td class="text-start ps-3 small fw-bold"><?= $label; ?></td>
                                                                                <td>₹<?= number_format($row[$cols['amt']]); ?></td>
                                                                                <td class="small text-muted"><?= (!empty($row[$cols['date']])) ? date('M d', strtotime($row[$cols['date']])) : '-'; ?></td>
                                                                                <td>
                                                                                    <?php if ($row[$cols['status']] == 'Paid'): ?>
                                                                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
                                                                                    <?php else: ?>
                                                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Due</span>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                            </tr>
                                                                    <?php endif;
                                                                    endforeach; ?>

                                                                    <?php if (($row['ot_transfer_amount'] + $row['ot_hpa_amount'] + $row['ot_hp_amount']) == 0): ?>
                                                                        <tr>
                                                                            <td colspan="4" class="text-muted small py-2">No vendor payments recorded.</td>
                                                                        </tr>
                                                                    <?php endif; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    <?php if (!empty($row['ot_insurance_name'])):
                                                        // Calculate Validity
                                                        $otInsStart = new DateTime($row['ot_insurance_start_date']);
                                                        $otInsEnd   = new DateTime($row['ot_insurance_end_date']);
                                                        $otInterval = $otInsStart->diff($otInsEnd);
                                                        $otValidity = $otInterval->y . " Year" . ($otInterval->y > 1 ? 's' : '');
                                                    ?>
                                                        <div class="p-3 border rounded-4 bg-light mb-4 position-relative">
                                                            <span class="position-absolute top-0 start-50 translate-middle badge bg-dark text-white border-light border">
                                                                Insurance Details
                                                            </span>

                                                            <div class="row g-2 mt-1">
                                                                <div class="col-12">
                                                                    <div class="d-flex justify-content-between">
                                                                        <span class="small text-muted">Provider:</span>
                                                                        <span class="fw-bold"><?= $row['ot_insurance_name']; ?></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="d-flex justify-content-between">
                                                                        <span class="small text-muted">Payment Status:</span>
                                                                        <span class="fw-bold <?= ($row['ot_insurance_payment_status'] == 'paid') ? 'text-success' : 'text-danger'; ?>">
                                                                            <?= strtoupper($row['ot_insurance_payment_status']); ?>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="d-flex justify-content-between">
                                                                        <span class="small text-muted">Amount:</span>
                                                                        <span class="fw-bold">₹ <?= number_format($row['ot_insurance_amount']); ?></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="d-flex justify-content-between">
                                                                        <span class="small text-muted">Issued On:</span>
                                                                        <span class="fw-bold"><?= $row['ot_insurance_start_date']; ?></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="d-flex justify-content-between">
                                                                        <span class="small text-muted">Expiry Date:</span>
                                                                        <span class="fw-bold text-danger"><?= $row['ot_insurance_end_date']; ?></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="d-flex justify-content-between">
                                                                        <span class="small text-muted">Validity:</span>
                                                                        <span class="fw-bold text-primary"><?= $otValidity; ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <div class="p-3 border rounded-3 text-center bg-light h-100">
                                                                <i class="ph-fill ph-pen-nib text-primary mb-2 fs-5"></i>
                                                                <div class="small fw-bold text-muted mb-1">Purchaser</div>

                                                                <?php if ($row['ot_purchaser_sign_status'] == 'Yes'): ?>
                                                                    <span class="badge bg-success mb-1">Signed</span>
                                                                    <div class="small text-muted" style="font-size:10px">
                                                                        <?= date('d-M-Y', strtotime($row['ot_purchaser_sign_date'])); ?>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <span class="badge bg-warning text-dark mb-1">Pending</span>
                                                                    <div class="small text-muted" style="font-size:10px">--</div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>

                                                        <div class="col-6">
                                                            <div class="p-3 border rounded-3 text-center bg-light h-100">
                                                                <i class="ph-fill ph-pen-nib text-primary mb-2 fs-5"></i>
                                                                <div class="small fw-bold text-muted mb-1">Seller</div>

                                                                <?php if ($row['ot_seller_sign_status'] == 'Yes'): ?>
                                                                    <span class="badge bg-success mb-1">Signed</span>
                                                                    <div class="small text-muted" style="font-size:10px">
                                                                        <?= date('d-M-Y', strtotime($row['ot_seller_sign_date'])); ?>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <span class="badge bg-warning text-dark mb-1">Pending</span>
                                                                    <div class="small text-muted" style="font-size:10px">--</div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>

                                <div class="modal-footer border-top bg-white px-4 py-3">
                                    <button type="button"
                                        class="btn btn-light border fw-bold rounded-pill px-4 shadow-sm w-100"
                                        data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>







            </div>
        </div>
        <!-- END OF Vehicle Data Grid -->



    </div>
    <script>

    </script>
</body>

</html>