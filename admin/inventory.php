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


        <div class="container-fluid pb-5">

            <?php
            // ============================================
            // 1. FILTER PARAMETERS
            // ============================================
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $year = isset($_GET['year']) ? trim($_GET['year']) : '';
            $rto = isset($_GET['rto']) ? trim($_GET['rto']) : '';
            $status = isset($_GET['status']) ? trim($_GET['status']) : '';
            $payment = isset($_GET['payment']) ? trim($_GET['payment']) : '';

            // ============================================
            // 2. BUILD DYNAMIC QUERY
            // ============================================
            $conditions = [];
            $params = [];
            $types = "";

            // --- RTO Filter (Checks inside 'vehicle_ot' table) ---
            if (!empty($rto)) {
                $conditions[] = "ot.ot_rto_name = ?";
                $params[] = $rto;
                $types .= "s";
            }

            // --- Year Filter (Checks inside 'vehicle' table) ---
            if (!empty($year)) {
                $conditions[] = "YEAR(v.register_date) = ?";
                $params[] = $year;
                $types .= "i";  // integer type for year
            }

            // --- Status/Payment Filters (Add these if needed) ---
            if (!empty($status)) {
                if ($status == 'available') {
                    $conditions[] = "v.sold_out = ?";
                    $params[] = 0;  // 0 = not sold
                } else if ($status == 'sold') {
                    $conditions[] = "v.sold_out = ?";
                    $params[] = 1;  // 1 = sold
                }
                $types .= "i";  // integer type
            }

            // Construct the WHERE clause
            $whereSQL = "";
            if (count($conditions) > 0) {
                $whereSQL = " WHERE " . implode(" AND ", $conditions);
            }

            // ============================================
            // 3. FETCH STATS (The Fixed Query)
            // ============================================
            $statsQuery = "SELECT 
    COUNT(DISTINCT v.id) as total,
    COUNT(DISTINCT CASE WHEN v.sold_out = 0 THEN v.id END) as available,
    COUNT(DISTINCT CASE WHEN v.sold_out = 1 THEN v.id END) as sold
FROM vehicle v
LEFT JOIN vehicle_ot ot ON v.id = ot.vehicle_id" . $whereSQL;

            // 4. Prepare and Execute
            $stmt = $conn->prepare($statsQuery);

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $statsResult = $stmt->get_result();
            $stats = $statsResult->fetch_assoc();

            // 5. Assign Variables
            $totalVehicles = $stats['total'] ?? 0;
            $availableVehicles = $stats['available'] ?? 0;
            $soldVehicles = $stats['sold'] ?? 0;
            ?>


            <!-- Inventory Dashboard Page -->
            <div class="container-fluid px-2 py-2 mb-3">

                <form method="GET" action="">

                    <div style="position: -webkit-sticky; position: sticky; top: 0;">

                        <div class="bg-primary shadow-sm rounded-4 p-2" style="border: 1px solid rgba(0,0,0,0.05);">

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h2 class="fw-bold m-0 text-white ps-1" style="font-size: 1rem;">Inventory</h2>

                                <a href="dashboard.php" class="btn btn-light btn-sm rounded-pill border px-3 fw-bold text-muted"
                                    style="font-size: 0.75rem;">
                                    <i class="ph-bold ph-arrow-left me-1"></i>Back
                                </a>
                            </div>

                            <div class="d-flex gap-2 mb-2">
                                <div class="position-relative flex-grow-1">
                                    <i class="ph-bold ph-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-2 text-muted" style="font-size: 0.9rem;"></i>
                                    <input type="text" name="search" class="form-control form-control-sm bg-light border-0 fw-semibold"
                                        placeholder="Search..."
                                        value="<?= htmlspecialchars($search) ?>"
                                        style="padding-left: 32px; height: 38px; border-radius: 20px; font-size: 0.85rem;">
                                </div>

                                <button type="button" class="btn btn-dark rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#compactFilters"
                                    aria-expanded="false"
                                    style="width: 38px; height: 38px;">
                                    <i class="ph-bold ph-faders text-white" style="font-size: 1rem;"></i>
                                </button>
                            </div>

                            <div class="d-flex justify-content-center rounded-4 px-2 py-2 mx-0 text-center gap-3"
                                style="background: linear-gradient(90deg, #4facfe, #00f2fe);">

                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center shadow-sm"
                                        style="width: 32px; height: 32px; color: #212529;">
                                        <i class="ph-fill ph-car small"></i>
                                    </div>
                                    <div class="lh-1">
                                        <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?= $totalVehicles ?></div>
                                        <small class="text-muted fw-bold" style="font-size: 0.65rem;">Total</small>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-success d-flex align-items-center justify-content-center shadow-sm text-white"
                                        style="width: 32px; height: 32px;">
                                        <i class="ph-fill ph-check small"></i>
                                    </div>
                                    <div class="lh-1">
                                        <div class="fw-bold text-success" style="font-size: 0.9rem;"><?= $availableVehicles ?></div>
                                        <small class="text-success fw-bold" style="font-size: 0.65rem;">Ready</small>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center shadow-sm text-white"
                                        style="width: 32px; height: 32px;">
                                        <i class="ph-fill ph-tag small"></i>
                                    </div>
                                    <div class="lh-1">
                                        <div class="fw-bold text-danger" style="font-size: 0.9rem;"><?= $soldVehicles ?></div>
                                        <small class="text-danger fw-bold" style="font-size: 0.65rem;">Sold</small>
                                    </div>
                                </div>

                            </div>

                            <div class="collapse mt-2" id="compactFilters">
                                <div class="p-2 border-top">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="small fw-bold text-muted ps-1 mb-1" style="font-size: 0.65rem;">YEAR</label>
                                            <select name="year" class="form-select form-select-sm bg-light border-0" style="border-radius: 8px; font-size: 0.8rem;">
                                                <option value="">All Years</option>
                                                <?php
                                                $currentYear = date('Y');
                                                for ($y = $currentYear; $y >= 2010; $y--) {
                                                    $selected = ($year == $y) ? 'selected' : '';
                                                    echo "<option value='$y' $selected>$y</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="small fw-bold text-muted ps-1 mb-1" style="font-size: 0.65rem;">STATUS</label>
                                            <select name="status" class="form-select form-select-sm bg-light border-0" style="border-radius: 8px; font-size: 0.8rem;">
                                                <option value="">All Status</option>
                                                <option value="available" <?= ($status == 'available') ? 'selected' : '' ?>>Ready</option>
                                                <option value="sold" <?= ($status == 'sold') ? 'selected' : '' ?>>Sold</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="small fw-bold text-muted ps-1 mb-1" style="font-size: 0.65rem;">RTO</label>
                                            <select name="rto" class="form-select form-select-sm bg-light border-0" style="border-radius: 8px; font-size: 0.8rem;">
                                                <option value="">All RTOs</option>
                                                <?php
                                                $rtoList = ['Bankura', 'Bishnupur', 'Durgapur', 'Asansol']; // Add others as needed
                                                foreach ($rtoList as $rtoName) {
                                                    $selected = ($rto == $rtoName) ? 'selected' : '';
                                                    echo "<option value='$rtoName' $selected>$rtoName</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="small fw-bold text-muted ps-1 mb-1" style="font-size: 0.65rem;">PAYMENT</label>
                                            <select name="payment" class="form-select form-select-sm bg-light border-0" style="border-radius: 8px; font-size: 0.8rem;">
                                                <option value="">All Types</option>
                                                <option value="Cash" <?= ($payment == 'Cash') ? 'selected' : '' ?>>Cash</option>
                                                <option value="Online" <?= ($payment == 'Online') ? 'selected' : '' ?>>Online</option>
                                            </select>
                                        </div>

                                        <div class="col-12 mt-3 d-flex gap-2">
                                            <a href="?" class="btn btn-light btn-sm border w-50 rounded-pill fw-bold">Reset</a>
                                            <button class="btn btn-dark btn-sm w-50 rounded-pill fw-bold" type="submit">
                                                Apply Filters
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Vehicle Data Grid -->
            <div class="position-relative" style="min-height: 400px;">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

                    <?php
                    // ============================================
                    // BUILD DYNAMIC SQL QUERY WITH FILTERS
                    // ============================================
                    $sql = "SELECT 
            v.id as vehicle_prim_id, 
            v.*, 
            vs.*, 
            vp.*, 
            ot.*
        FROM vehicle v
        LEFT JOIN vehicle_seller vs ON v.id = vs.vehicle_id
        LEFT JOIN vehicle_purchaser vp ON v.id = vp.vehicle_id
        LEFT JOIN vehicle_ot ot ON v.id = ot.vehicle_id
        WHERE 1=1";

                    // Search Filter (Name, Vehicle Number, Engine, Chassis)
                    if (!empty($search)) {
                        $searchEscaped = $conn->real_escape_string($search);
                        $sql .= " AND (
                v.name LIKE '%$searchEscaped%' OR 
                v.vehicle_number LIKE '%$searchEscaped%' OR 
                v.engine_number LIKE '%$searchEscaped%' OR 
                v.chassis_number LIKE '%$searchEscaped%'
            )";
                    }

                    // Year Filter
                    if (!empty($year)) {
                        $yearEscaped = $conn->real_escape_string($year);
                        $sql .= " AND YEAR(v.register_date) = '$yearEscaped'";
                    }

                    // RTO Filter (assuming RTO is part of vehicle_number like WB-XX-XXXX)
                    // RTO Filter (Now checks the correct 'ot_rto_name' column in the joined table)
                    if (!empty($rto)) {
                        $rtoEscaped = $conn->real_escape_string($rto);
                        $sql .= " AND ot.ot_rto_name = '$rtoEscaped'";
                    }

                    // Status Filter
                    if ($status === 'available') {
                        $sql .= " AND v.sold_out = 0";
                    } elseif ($status === 'sold') {
                        $sql .= " AND v.sold_out = 1";
                    }

                    // Payment Type Filter
                    if (!empty($payment)) {
                        $paymentEscaped = $conn->real_escape_string($payment);
                        $sql .= " AND v.payment_type = '$paymentEscaped'";
                    }

                    $sql .= " ORDER BY v.id DESC";

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):

                            // Image Logic
                            if (!empty($row['photo1'])) {
                                $imageSrc = "../images/" . $row['photo1'];
                            } else {
                                $imageSrc = "../images/default.jpg";
                            }

                            // Status Logic
                            $isAvailable = ($row['sold_out'] == 0);
                            $statusText = $isAvailable ? "AVAILABLE" : "SOLD OUT";
                            $statusClass = $isAvailable ? "text-success" : "text-danger";

                            // Unique Modal ID
                            $modalID = "viewModal_" . $row['vehicle_prim_id'];

                            // Prevent Warnings
                            $row['pr_rc'] = $row['pr_rc'] ?? 0;
                            $row['pr_noc'] = $row['pr_noc'] ?? 0;
                            $row['seller_payment_type'] = $row['seller_payment_type'] ?? '';
                    ?>

                            <div class="col">


                                <div class="card border-0 inventory-card h-100">
                                    <div class="hover-card position-relative overflow-hidden">
                                        <img src="<?= $imageSrc; ?>" class="d-block w-100" loading="lazy" alt="Bike">

                                        <span class="badge status-badge <?= $statusClass ?>">
                                            <?php if ($statusText === 'Available'): ?>
                                                <i class="fa-solid fa-box"></i>
                                            <?php else: ?>
                                                <i class="fa-solid fa-box-archive"></i>
                                            <?php endif; ?>
                                            <?= $statusText ?>
                                        </span>

                                    </div>

                                    <div class="info-overlay">
                                        <div class="card-header-section">
                                            <div>
                                                <h6 class="fw-bold text-uppercase"><?= $row['vehicle_number']; ?></h6>
                                                <small><?= $row['name']; ?></small>
                                            </div>
                                            <div class="card-price">₹ <?= number_format($row['cash_price']); ?></div>
                                        </div>

                                        <div class="card-meta">
                                            <span class="badge"><?= date('Y', strtotime($row['register_date'])); ?></span>
                                            <span class="badge"><?= $row['owner_serial']; ?> Owner</span>
                                        </div>

                                        <div class="card-actions">
                                            <a href="edit_inventory.php?id=<?= $row['vehicle_prim_id']; ?>"
                                                class="btn action-btn btn-edit">
                                                <i class="ph-bold ph-pencil-simple"></i> Edit
                                            </a>

                                           <a href="delete_vehicle.php?id=<?= $row['vehicle_prim_id']; ?>" 
   class="btn action-btn btn-delete" 
   onclick="return confirm('⚠️ Are you sure you want to delete this vehicle?\n\nThis action cannot be undone.');">
   <i class="bi bi-trash"></i> Delete
</a>

                                            <button type="button" class="btn action-btn btn-view" data-bs-toggle="modal"
                                                data-bs-target="#<?= $modalID; ?>">
                                                <i class="ph-bold ph-eye"></i> View
                                            </button>
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
                                                    <img src="../images/logo.jpeg" alt="Chowdhury Automobile"
                                                        class="rounded-circle"
                                                        style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                                <div class="lh-1 mb-2">
                                                    <h5 class="modal-title fw-bold text-dark text-uppercase mb-1">
                                                        <?= htmlspecialchars($row['vehicle_number']); ?>
                                                    </h5>
                                                    <div class="d-flex align-items-center gap-2 mb-1 small text-muted">
                                                        <span class="fw-bold text-uppercase"><?= htmlspecialchars($row['name']); ?></span>
                                                        <i class="ph-fill ph-dot text-muted" style="font-size: 8px;"></i>
                                                    </div>
                                                    <span class="badge <?= $row['sold_out'] ? 'bg-danger' : 'bg-success'; ?> text-white border border-danger-subtle rounded-pill">
                                                        <?= $row['sold_out'] ? 'Sold Out' : 'Available'; ?>
                                                    </span>
                                                </div>

                                                <button type="button" class="btn-close ms-auto bg-light rounded-circle p-2"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                        </div>

                                        <div class="modal-body p-0 overflow-auto h-100 text-uppercase text-wrap">
                                            <div class="accordion accordion-flush p-3" id="dealDetailsAccordion">


                                                <!-- ==========================
                                                                            STEP 1: VEHICLE DETAILS
                                                                            ========================== -->
                                                <div class="accordion-item rounded-4 shadow-sm border-0 mb-3 overflow-hidden">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button fw-bold text-uppercase text-dark py-3"
                                                            type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#collapseVehicle_<?= $row['id']; ?>">
                                                            <i class="ph-bold ph-moped me-2 text-primary fs-5"></i> Vehicle
                                                            Details
                                                        </button>
                                                    </h2>
                                                    <div id="collapseVehicle_<?= $row['id']; ?>"
                                                        class="accordion-collapse collapse show"
                                                        data-bs-parent="#dealDetailsAccordion">
                                                        <div class="accordion-body bg-white p-4 border-top">

                                                            <h6 class="fw-bold text-muted small text-uppercase mb-3">Vehicle
                                                                Photos</h6>
                                                            <div class="row g-3 mb-4">
                                                                <?php
                                                                // Check all 4 photo columns
                                                                for ($i = 1; $i <= 4; $i++):
                                                                    $photoKey = "photo" . $i;
                                                                    if (!empty($row[$photoKey])):
                                                                        $imgSrc = "../images/" . $row[$photoKey];
                                                                ?>
                                                                        <div class="col-6 col-md-3">
                                                                            <div
                                                                                class="ratio ratio-1x1 rounded-4 overflow-hidden border mb-2 bg-light">
                                                                                <img src="<?= $imgSrc; ?>" class="object-fit-cover">
                                                                            </div>
                                                                            <a href="<?= $imgSrc; ?>" download
                                                                                class="btn btn-sm btn-dark rounded-pill w-100 fw-bold py-1"
                                                                                style="font-size: 11px;">Download</a>
                                                                        </div>
                                                                <?php endif;
                                                                endfor; ?>

                                                                <?php if (empty($row['photo1']) && empty($row['photo2']) && empty($row['photo3']) && empty($row['photo4'])): ?>
                                                                    <div class="col-12 text-muted small">No photos uploaded.</div>
                                                                <?php endif; ?>
                                                            </div>

                                                            <div class="p-3 bg-light rounded-4 border mb-3">
                                                                <div class="row g-3">
                                                                    <div class="col-12 col-md-4">
                                                                        <small class="text-muted text-uppercase fw-bold"
                                                                            style="font-size: 10px;">Register Date</small>
                                                                        <div class="fw-bold text-dark">
                                                                            <?= date('d-M-Y', strtotime($row['register_date'])); ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-md-4">
                                                                        <small class="text-muted text-uppercase fw-bold"
                                                                            style="font-size: 10px;">Vehicle Type</small>
                                                                        <div class="fw-bold text-dark">
                                                                            <?= $row['vehicle_type']; ?></div>
                                                                    </div>

                                                                    <div class="col-6 col-md-4">
                                                                        <small class="text-muted text-uppercase fw-bold"
                                                                            style="font-size: 10px;">Owner Serial</small>
                                                                        <div class="fw-bold text-dark">
                                                                            <?= $row['owner_serial']; ?></div>
                                                                    </div>
                                                                    <div class="col-12 col-md-12">
                                                                        <small class="text-muted text-uppercase fw-bold"
                                                                            style="font-size: 10px;">Bike Name</small>
                                                                        <div class="fw-bold text-dark"><?= $row['name']; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-md-12">
                                                                        <small class="text-muted text-uppercase fw-bold"
                                                                            style="font-size: 10px;">Vehicle Number</small>
                                                                        <div class="fw-bold text-dark text-uppercase">
                                                                            <?= $row['vehicle_number']; ?></div>
                                                                    </div>
                                                                    <div class="col-12 col-md-12">
                                                                        <small class="text-muted text-uppercase fw-bold"
                                                                            style="font-size: 10px;">Chassis Number</small>
                                                                        <div class="fw-bold text-dark font-monospace">
                                                                            <?= $row['chassis_number']; ?></div>
                                                                    </div>
                                                                    <div class="col-12 col-md-12">
                                                                        <small class="text-muted text-uppercase fw-bold"
                                                                            style="font-size: 10px;">Engine Number</small>
                                                                        <div class="fw-bold text-dark font-monospace">
                                                                            <?= $row['engine_number']; ?></div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <?php
                                                            $isOnline = ($row['payment_type'] == 'Online');
                                                            $price = $isOnline ? $row['online_price'] : $row['cash_price'];
                                                            $method = $isOnline ? $row['online_method'] : 'Cash Payment';
                                                            $txnId = $isOnline ? $row['online_transaction_id'] : 'N/A';
                                                            ?>
                                                            <div class="d-flex flex-column mb-4">
                                                                <div class="mb-3">
                                                                    <small class="text-muted text-uppercase fw-bold">Payment Mode</small>
                                                                    <div class="fw-bold text-primary">
                                                                        <i class="ph-bold <?= $isOnline ? 'ph-globe' : 'ph-money'; ?> me-1"></i>
                                                                        <?= $method; ?>
                                                                    </div>
                                                                </div>

                                                                <div>
                                                                    <small class="text-muted text-uppercase fw-bold">Price</small>
                                                                    <div class="fs-4 fw-bold text-dark">
                                                                        ₹ <?= number_format($price, 2); ?>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <?php if ($isOnline): ?>
                                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                                    <div>
                                                                        <small class="text-muted text-uppercase fw-bold">Transaction
                                                                            ID</small>
                                                                        <div class="fw-bold text-primary text-break"><?= $txnId; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                            <div class="border rounded-4 overflow-hidden">
                                                                <div
                                                                    class="bg-light px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                                                    <h6 class="fw-bold text-danger mb-0 small text-uppercase">
                                                                        Police Challan</h6>
                                                                    <span
                                                                        class="badge <?= ($row['police_challan'] == 'Yes') ? 'bg-danger' : 'bg-success'; ?>">
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
                                                                                            <td class="font-monospace small"><?= $cNum; ?>
                                                                                            </td>
                                                                                            <td class="fw-bold">₹<?= $cAmt; ?></td>
                                                                                            <td>
                                                                                                <?php if ($cStatus == 'Paid'): ?>
                                                                                                    <span
                                                                                                        class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
                                                                                                <?php else: ?>
                                                                                                    <span
                                                                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle">Pending</span>
                                                                                                <?php endif; ?>
                                                                                            </td>
                                                                                        </tr>
                                                                                <?php endif;
                                                                                endfor; ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="p-3 text-center text-muted small">No Challans
                                                                        Reported.</div>
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
                                                        <button
                                                            class="accordion-button collapsed fw-bold text-uppercase text-dark py-3"
                                                            type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#collapseSeller_<?= $row['id']; ?>">
                                                            <i class="ph-bold ph-user-circle me-2 text-primary fs-5"></i> Seller
                                                            Details
                                                        </button>
                                                    </h2>
                                                    <div id="collapseSeller_<?= $row['id']; ?>"
                                                        class="accordion-collapse collapse"
                                                        data-bs-parent="#dealDetailsAccordion">
                                                        <div class="accordion-body bg-white p-4 border-top">

                                                            <div class="d-flex flex-column mb-3">
                                                                <div class="mb-2">
                                                                    <small class="text-muted text-uppercase fw-bold">Seller Name</small>
                                                                    <div class="fs-5 fw-bold text-dark">
                                                                        <?= $row['seller_name']; ?>
                                                                    </div>
                                                                </div>

                                                                <div>
                                                                    <small class="text-muted text-uppercase fw-bold">Date</small>
                                                                    <div class="fw-bold text-dark">
                                                                        <?= (!empty($row['seller_date'])) ? date('Y-m-d', strtotime($row['seller_date'])) : '-'; ?>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <div class="row g-2 mb-3">
                                                                <div class="col-12">
                                                                    <small class="text-muted fw-bold"
                                                                        style="font-size:10px;">VEHICLE NO</small>
                                                                    <div class="fw-bold text-dark small text-uppercase">
                                                                        <?= $row['seller_vehicle_number']; ?></div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <small class="text-muted fw-bold"
                                                                        style="font-size:10px;">BIKE NAME</small>
                                                                    <div class="fw-bold text-dark small">
                                                                        <?= $row['seller_bike_name']; ?></div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <small class="text-muted fw-bold"
                                                                        style="font-size:10px;">CHASSIS NO</small>
                                                                    <div class="fw-bold text-dark font-monospace small">
                                                                        <?= $row['seller_chassis_no']; ?></div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <small class="text-muted fw-bold"
                                                                        style="font-size:10px;">ENGINE NO</small>
                                                                    <div class="fw-bold text-dark font-monospace small">
                                                                        <?= $row['seller_engine_no']; ?></div>
                                                                </div>
                                                            </div>

                                                            <div class="bg-light p-3 rounded-3 mb-3 border">
                                                                <small class="text-muted fw-bold">Address</small>
                                                                <div class="mb-2 text-wrap text-break"><?= $row['seller_address']; ?></div>
                                                                <div class="d-flex gap-2 flex-wrap">
                                                                    <?php
                                                                    // Loop through mobile 1, 2, 3
                                                                    for ($m = 1; $m <= 3; $m++):
                                                                        $mob = $row['seller_mobile' . $m];
                                                                        if (!empty($mob)):
                                                                    ?>
                                                                            <a href="tel:<?= $mob; ?>"
                                                                                class="badge bg-white text-dark border text-decoration-none py-2">
                                                                                <i class="ph-fill ph-phone me-1"></i> <?= $mob; ?>
                                                                            </a>
                                                                    <?php endif;
                                                                    endfor; ?>
                                                                </div>
                                                            </div>

                                                            <h6 class="fw-bold text-muted small text-uppercase mb-3">Seller
                                                                Documents</h6>
                                                            <div class="row g-2">
                                                                <?php
                                                                // Array to map DB columns to Display Labels
                                                                $docs = [
                                                                    'doc_aadhar_front' => 'AADHAR FRONT',
                                                                    'doc_aadhar_back' => 'AADHAR BACK',
                                                                    'doc_voter_front' => 'VOTER FRONT',
                                                                    'doc_voter_back' => 'VOTER BACK'
                                                                ];

                                                                foreach ($docs as $col => $label):
                                                                    if (!empty($row[$col])):
                                                                        $imgSrc = "../images/" . $row[$col];
                                                                ?>
                                                                        <div class="col-6 col-md-3">
                                                                            <div class="border rounded p-2 text-center bg-white h-100">
                                                                                <small class="fw-bold d-block mb-1"
                                                                                    style="font-size:10px"><?= $label; ?></small>
                                                                                <div
                                                                                    class="ratio ratio-1x1 mb-1 border rounded overflow-hidden">
                                                                                    <img src="<?= $imgSrc; ?>" class="object-fit-cover">
                                                                                </div>
                                                                                <a href="<?= $imgSrc; ?>" target="_blank"
                                                                                    class="btn btn-dark btn-sm w-100 py-0 mb-1"
                                                                                    style="font-size:10px">View</a>
                                                                                <a href="<?= $imgSrc; ?>" download
                                                                                    class="btn btn-secondary btn-sm w-100 py-0"
                                                                                    style="font-size:10px">Download</a>
                                                                            </div>
                                                                        </div>
                                                                <?php endif;
                                                                endforeach; ?>
                                                            </div>

                                                            <div class="row g-3 mb-3 mt-2">
                                                                <div class="col-12">
                                                                    <label class="small text-muted fw-bold mb-1">PAPERS
                                                                        RECEIVED</label>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        <span
                                                                            class="badge <?= ($row['pr_rc'] == 1) ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border text-decoration-line-through'; ?>">RC</span>
                                                                        <span
                                                                            class="badge <?= ($row['pr_tax'] == 1) ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border text-decoration-line-through'; ?>">Tax
                                                                            Token</span>
                                                                        <span
                                                                            class="badge <?= ($row['pr_insurance'] == 1) ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border text-decoration-line-through'; ?>">Insurance</span>
                                                                        <span
                                                                            class="badge <?= ($row['pr_pucc'] == 1) ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border text-decoration-line-through'; ?>">PUCC</span>
                                                                        <span
                                                                            class="badge <?= ($row['pr_noc'] == 1) ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border text-decoration-line-through'; ?>">NOC</span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <?php if (!empty($row['noc_front']) || !empty($row['noc_back'])): ?>
                                                                <div class="border rounded-4 p-3 mb-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <small class="fw-bold text-uppercase text-muted">NOC
                                                                            Details</small>
                                                                        <span
                                                                            class="badge <?= ($row['noc_status'] == 'Paid') ? 'bg-success' : 'bg-danger'; ?>"><?= $row['noc_status']; ?></span>
                                                                    </div>
                                                                    <div class="row g-2">
                                                                        <?php
                                                                        $nocDocs = ['noc_front' => 'NOC FRONT', 'noc_back' => 'NOC BACK'];
                                                                        foreach ($nocDocs as $col => $label):
                                                                            if (!empty($row[$col])):
                                                                                $imgSrc = "../images/" . $row[$col];
                                                                        ?>
                                                                                <div class="col-6">
                                                                                    <div class="border rounded p-2 text-center bg-white">
                                                                                        <small class="fw-bold d-block mb-1"
                                                                                            style="font-size:10px"><?= $label; ?></small>
                                                                                        <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden"
                                                                                            style="width:50px; height:50px; margin:auto;">
                                                                                            <img src="<?= $imgSrc; ?>"
                                                                                                class="object-fit-cover">
                                                                                        </div>
                                                                                        <a href="<?= $imgSrc; ?>" target="_blank"
                                                                                            class="btn btn-outline-dark btn-sm w-100 py-0 mb-1"
                                                                                            style="font-size:10px">View</a>
                                                                                        <a href="<?= $imgSrc; ?>" download
                                                                                            class="btn btn-dark btn-sm w-100 py-0"
                                                                                            style="font-size:10px">Download</a>
                                                                                    </div>
                                                                                </div>
                                                                        <?php endif;
                                                                        endforeach; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php if (!empty($row['rc_front']) || !empty($row['rc_back'])): ?>
                                                                <div class="border rounded-4 p-3 mb-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <small class="fw-bold text-uppercase text-muted">RC
                                                                            Details</small>
                                                                    </div>
                                                                    <div class="row g-2">
                                                                        <?php
                                                                        $rcDocs = ['rc_front' => 'RC FRONT', 'rc_back' => 'RC BACK'];
                                                                        foreach ($rcDocs as $col => $label):
                                                                            if (!empty($row[$col])):
                                                                                $imgSrc = "../images/" . $row[$col];
                                                                        ?>
                                                                                <div class="col-6">
                                                                                    <div class="border rounded p-2 text-center bg-white">
                                                                                        <small class="fw-bold d-block mb-1"
                                                                                            style="font-size:10px"><?= $label; ?></small>
                                                                                        <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden"
                                                                                            style="width:50px; height:50px; margin:auto;">
                                                                                            <img src="<?= $imgSrc; ?>"
                                                                                                class="object-fit-cover">
                                                                                        </div>
                                                                                        <a href="<?= $imgSrc; ?>" target="_blank"
                                                                                            class="btn btn-outline-dark btn-sm w-100 py-0 mb-1"
                                                                                            style="font-size:10px">View</a>
                                                                                        <a href="<?= $imgSrc; ?>" download
                                                                                            class="btn btn-dark btn-sm w-100 py-0"
                                                                                            style="font-size:10px">Download</a>
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
                                                                        <span
                                                                            class="fw-bold font-monospace small text-break"><?= $row['seller_online_transaction_id']; ?></span>
                                                                    </div>
                                                                <?php endif; ?>

                                                                <div class="d-flex flex-column flex-md-row text-center border rounded overflow-hidden bg-white">
                                                                    <div class="flex-fill p-2 border-bottom border-md-end mb-2 mb-md-0">
                                                                        <div class="small text-muted fw-bold" style="font-size:10px">TOTAL</div>
                                                                        <div class="fw-bold">
                                                                            ₹<?= number_format($row['total_amount'], 0); ?>
                                                                        </div>
                                                                    </div>

                                                                    <div class="flex-fill p-2 border-bottom border-md-end bg-success-subtle text-success mb-2 mb-md-0">
                                                                        <div class="small fw-bold" style="font-size:10px">PAID</div>
                                                                        <div class="fw-bold">
                                                                            ₹<?= number_format($row['paid_amount'], 0); ?>
                                                                        </div>
                                                                    </div>

                                                                    <div class="flex-fill p-2 bg-danger-subtle text-danger">
                                                                        <div class="small fw-bold" style="font-size:10px">DUE</div>
                                                                        <div class="fw-bold">
                                                                            ₹<?= number_format($row['due_amount'], 0); ?>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                                <?php if ($row['due_amount'] > 0 && !empty($row['due_reason'])): ?>
                                                                    <div class="small text-danger mt-2 fst-italic">
                                                                        Remark <i class="ph-bold ph-info me-1"></i> :
                                                                        <?= $row['due_reason']; ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>

                                                            <div class="row g-2">
                                                                <div class="col-12">
                                                                    <small class="text-muted fw-bold"
                                                                        style="font-size:10px">SHOWROOM</small>
                                                                    <div class="fw-bold small">
                                                                        <?= $row['exchange_showroom_name'] ?? '-'; ?></div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <small class="text-muted fw-bold"
                                                                        style="font-size:10px">STAFF</small>
                                                                    <div class="fw-bold small"><?= $row['staff_name'] ?? '-'; ?>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- ==========================
                                                    STEP 3: PURCHASER DETAILS
                                                    ========================== -->
                                                <div class="accordion-item rounded-4 shadow-sm border-0 mb-3 overflow-hidden">
                                                    <h2 class="accordion-header">
                                                        <button
                                                            class="accordion-button collapsed fw-bold text-uppercase text-dark py-3"
                                                            type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#collapsePurchaser_<?= $row['id']; ?>">
                                                            <i class="ph-bold ph-shopping-bag me-2 text-primary fs-5"></i>
                                                            Purchaser Details
                                                        </button>
                                                    </h2>
                                                    <div id="collapsePurchaser_<?= $row['id']; ?>"
                                                        class="accordion-collapse collapse"
                                                        data-bs-parent="#dealDetailsAccordion">
                                                        <div class="accordion-body bg-white p-4 border-top">

                                                            <div class="row g-3 mb-3">
                                                                <div class="col-12">
                                                                    <small class="text-muted text-uppercase fw-bold">Purchaser
                                                                        Name</small>
                                                                    <div class="fs-5 fw-bold text-dark">
                                                                        <?= $row['purchaser_name']; ?></div>
                                                                    <small class="text-muted">Date:
                                                                        <?= date('d-M-Y', strtotime($row['purchaser_date'])); ?></small>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="bg-light p-3 rounded border">
                                                                        <small
                                                                            class="text-muted text-uppercase fw-bold">Address</small>
                                                                        <div><?= $row['purchaser_address']; ?></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <small class="text-muted fw-bold"
                                                                        style="font-size:10px;">BIKE NAME</small>
                                                                    <div class="fw-bold text-dark">
                                                                        <?= $row['purchaser_bike_name']; ?></div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <small class="text-muted fw-bold"
                                                                        style="font-size:10px;">VEHICLE NO</small>
                                                                    <div class="fw-bold text-dark text-uppercase">
                                                                        <?= $row['purchaser_vehicle_no']; ?></div>
                                                                </div>
                                                            </div>

                                                            <div class="border rounded-4 overflow-hidden mb-4">
                                                                <div class="bg-light px-3 py-2 border-bottom">
                                                                    <h6 class="fw-bold mb-0 small text-uppercase">Buyer Payment
                                                                        Fees</h6>
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
                                                                                'HPA' => ['amt' => 'purchaser_hpa_amount', 'date' => 'purchaser_hpa_date', 'status' => 'purchaser_hpa_status'],
                                                                                'HP' => ['amt' => 'purchaser_hp_amount', 'date' => 'purchaser_hp_date', 'status' => 'purchaser_hp_status'],
                                                                            ];
                                                                            foreach ($fees as $label => $cols):
                                                                                if ($row[$cols['amt']] > 0): // Only show if amount exists
                                                                            ?>
                                                                                    <tr>
                                                                                        <td class="text-start ps-3 small fw-bold">
                                                                                            <?= $label; ?></td>
                                                                                        <td>₹<?= number_format($row[$cols['amt']]); ?>
                                                                                        </td>
                                                                                        <td class="small text-muted">
                                                                                            <?= (!empty($row[$cols['date']])) ? date('M d', strtotime($row[$cols['date']])) : '-'; ?>
                                                                                        </td>
                                                                                        <td>
                                                                                            <?php if ($row[$cols['status']] == 'Paid'): ?>
                                                                                                <span
                                                                                                    class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
                                                                                            <?php else: ?>
                                                                                                <span
                                                                                                    class="badge bg-danger-subtle text-danger border border-danger-subtle">Due</span>
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
                                                                $insEnd = new DateTime($row['purchaser_insurance_expiry_date']);
                                                                $interval = $insStart->diff($insEnd);
                                                                $validity = $interval->y . " Year" . ($interval->y > 1 ? 's' : '');
                                                            ?>
                                                                <div class="p-3 border rounded-4 bg-light mb-4 position-relative">
                                                                    <span class="position-absolute top-0 start-50 translate-middle badge bg-dark text-white border-light border">
                                                                        Insurance Details
                                                                    </span>

                                                                    <div class="table-responsive mt-3">
                                                                        <table class="table table-borderless mb-0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <th class="small text-muted">Provider:</th>
                                                                                    <td class="fw-bold"><?= $row['purchaser_insurance_name']; ?></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th class="small text-muted">Payment Status:</th>
                                                                                    <td class="fw-bold <?= ($row['purchaser_insurance_payment_status'] == 'paid') ? 'text-success' : 'text-danger'; ?>">
                                                                                        <?= strtoupper($row['purchaser_insurance_payment_status']); ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th class="small text-muted">Amount:</th>
                                                                                    <td class="fw-bold">₹<?= number_format($row['purchaser_insurance_amount']); ?></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th class="small text-muted">Issued On:</th>
                                                                                    <td class="fw-bold"><?= $row['purchaser_insurance_issue_date']; ?></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th class="small text-muted">Expiry Date:</th>
                                                                                    <td class="fw-bold text-danger"><?= $row['purchaser_insurance_expiry_date']; ?></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th class="small text-muted">Validity:</th>
                                                                                    <td class="fw-bold text-primary"><?= $validity; ?></td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>

                                                            <?php endif; ?>

                                                            <?php if ($row['purchaser_payment_mode'] == 'Finance'): ?>
                                                                <div class="alert alert-primary border-primary mb-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center border-bottom border-primary-subtle pb-2 mb-2">
                                                                        <span class="badge bg-primary">Finance Mode</span>
                                                                        <small class="fw-bold text-primary">HPA Active</small>
                                                                    </div>
                                                                    <div class="row g-2">
                                                                        <div class="col-12">
                                                                            <small class="text-primary-emphasis fw-bold"
                                                                                style="font-size:10px">FINANCE COMPANY</small>
                                                                            <div class="fw-bold text-dark">
                                                                                <?= $row['purchaser_fin_hpa_with']; ?></div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div
                                                                                class="d-flex justify-content-between align-items-center">
                                                                                <div>
                                                                                    <small class="text-primary-emphasis fw-bold"
                                                                                        style="font-size:10px">DISBURSED AMT</small>
                                                                                    <div class="fw-bold text-dark">
                                                                                        ₹<?= number_format($row['purchaser_fin_disburse_amount']); ?>
                                                                                    </div>
                                                                                </div>
                                                                                <span
                                                                                    class="badge <?= ($row['purchaser_fin_disburse_status'] == 'Paid') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                                                                    <?= $row['purchaser_fin_disburse_status']; ?>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="col-12 mt-2 pt-2 border-top border-primary-subtle">
                                                                            <small
                                                                                class="text-primary-emphasis fw-bold d-block mb-1"
                                                                                style="font-size:12px">REGISTERED MOBILES</small>
                                                                            <div class="d-flex gap-3 flex-wrap">
                                                                                <?php for ($m = 1; $m <= 3; $m++):
                                                                                    $pmob = $row['purchaser_fin_mobile' . $m];
                                                                                    if (!empty($pmob)): ?>
                                                                                        <a href="tel:<?= $pmob; ?>"
                                                                                            class="badge bg-white text-dark border text-decoration-none">
                                                                                            <i class="ph-fill ph-phone"></i> <?= $pmob; ?>
                                                                                        </a>
                                                                                <?php endif;
                                                                                endfor; ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                            <div class="d-flex flex-column flex-md-row text-center border rounded overflow-hidden bg-white mb-3">
                                                                <div class="flex-fill p-2 border-bottom border-md-end mb-2 mb-md-0">
                                                                    <div class="small text-muted fw-bold" style="font-size:10px">TOTAL</div>
                                                                    <div class="fw-bold">
                                                                        ₹<?= number_format($row['purchaser_total']); ?>
                                                                    </div>
                                                                </div>

                                                                <div class="flex-fill p-2 border-bottom border-md-end bg-success-subtle text-success mb-2 mb-md-0">
                                                                    <div class="small fw-bold" style="font-size:10px">PAID</div>
                                                                    <div class="fw-bold">
                                                                        ₹<?= number_format($row['purchaser_paid']); ?>
                                                                    </div>
                                                                </div>

                                                                <div class="flex-fill p-2 bg-danger-subtle text-danger">
                                                                    <div class="small fw-bold" style="font-size:10px">DUE</div>
                                                                    <div class="fw-bold">
                                                                        ₹<?= number_format($row['purchaser_due']); ?>
                                                                    </div>
                                                                </div>
                                                            </div>



                                                            <div class="mb-4">
                                                                <label class="small text-muted fw-bold">PAYMENT ALL
                                                                    PAID?</label>
                                                                <?php if ($row['purchaser_payment_all_paid'] == 1): ?>
                                                                    <div class="fw-bold text-success">Yes, All Clear</div>
                                                                <?php else: ?>
                                                                    <div class="fw-bold text-danger">No, Payment Pending</div>
                                                                <?php endif; ?>
                                                            </div>

                                                            <h6 class="fw-bold text-muted small text-uppercase mb-3">Purchaser
                                                                Documents</h6>
                                                            <div class="row g-2">
                                                                <?php
                                                                $pDocs = [
                                                                    'purchaser_doc_aadhar_front' => 'AADHAR FRONT',
                                                                    'purchaser_doc_aadhar_back' => 'AADHAR BACK',
                                                                    'purchaser_doc_voter_front' => 'VOTER FRONT',
                                                                    'purchaser_doc_voter_back' => 'VOTER BACK'
                                                                ];
                                                                foreach ($pDocs as $col => $label):
                                                                    if (!empty($row[$col])):
                                                                        $imgSrc = "../images/" . $row[$col];
                                                                ?>
                                                                        <div class="col-6 col-md-3">
                                                                            <div class="border rounded p-2 text-center bg-white h-100">
                                                                                <small class="fw-bold d-block mb-1"
                                                                                    style="font-size:10px"><?= $label; ?></small>
                                                                                <div
                                                                                    class="ratio ratio-1x1 mb-1 border rounded overflow-hidden">
                                                                                    <img src="<?= $imgSrc; ?>"
                                                                                        class="w-100 h-100 object-fit-cover">
                                                                                </div>
                                                                                <a href="<?= $imgSrc; ?>" target="_blank"
                                                                                    class="btn btn-dark btn-sm w-100 py-0 mb-1"
                                                                                    style="font-size:10px">View</a>
                                                                                <a href="<?= $imgSrc; ?>" download
                                                                                    class="btn btn-secondary btn-sm w-100 py-0"
                                                                                    style="font-size:10px">Download</a>
                                                                            </div>
                                                                        </div>
                                                                <?php endif;
                                                                endforeach; ?>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- ==========================
                                                        STEP 4: OWNERSHIP TRANSFER
                                                        ========================== -->
                                                <div class="accordion-item rounded-4 shadow-sm border-0 mb-3 overflow-hidden">
                                                    <h2 class="accordion-header">
                                                        <button
                                                            class="accordion-button collapsed fw-bold text-uppercase text-dark py-3"
                                                            type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#collapseTransfer_<?= $row['id']; ?>">
                                                            <i class="ph-bold ph-arrows-left-right me-2 text-primary fs-5"></i>
                                                            Ownership Transfer
                                                        </button>
                                                    </h2>
                                                    <div id="collapseTransfer_<?= $row['id']; ?>"
                                                        class="accordion-collapse collapse"
                                                        data-bs-parent="#dealDetailsAccordion">
                                                        <div class="accordion-body bg-white p-4 border-top">

                                                            <div class="row g-3 mb-4">
                                                                <div class="col-12 col-md-6">
                                                                    <small class="text-muted fw-bold text-uppercase"
                                                                        style="font-size:10px">Transfer Name To</small>
                                                                    <div class="fw-bold text-dark">
                                                                        <?= $row['ot_name_transfer'] ?? '-'; ?></div>
                                                                </div>
                                                                <div class="col-6 col-md-6">
                                                                    <small class="text-muted fw-bold text-uppercase"
                                                                        style="font-size:10px">Vehicle Number</small>
                                                                    <div class="fw-bold text-dark text-uppercase">
                                                                        <?= $row['ot_vehicle_number'] ?? '-'; ?></div>
                                                                </div>
                                                                <div class="col-6 col-md-6">
                                                                    <small class="text-muted fw-bold text-uppercase"
                                                                        style="font-size:10px">RTO Location</small>
                                                                    <div class="fw-bold text-dark">
                                                                        <?= $row['ot_rto_name'] ?? '-'; ?></div>
                                                                </div>
                                                                <div class="col-12 col-md-6">
                                                                    <small class="text-muted fw-bold text-uppercase"
                                                                        style="font-size:10px">Vendor Name</small>
                                                                    <div class="fw-bold text-primary">
                                                                        <?= $row['ot_vendor_name'] ?? '-'; ?></div>
                                                                </div>
                                                            </div>

                                                            <div class="border rounded-4 overflow-hidden mb-4">
                                                                <div class="bg-light px-3 py-2 border-bottom">
                                                                    <h6 class="fw-bold mb-0 small text-uppercase">Vendor Payment
                                                                        Details</h6>
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
                                                                                'HPA' => ['amt' => 'ot_hpa_amount', 'date' => 'ot_hpa_date', 'status' => 'ot_hpa_status'],
                                                                                'HP' => ['amt' => 'ot_hp_amount', 'date' => 'ot_hp_date', 'status' => 'ot_hp_status'],
                                                                            ];

                                                                            foreach ($otFees as $label => $cols):
                                                                                if (($row[$cols['amt']] ?? 0) > 0): // Only show if amount > 0
                                                                            ?>
                                                                                    <tr>
                                                                                        <td class="text-start ps-3 small fw-bold">
                                                                                            <?= $label; ?></td>
                                                                                        <td>₹<?= number_format($row[$cols['amt']]); ?>
                                                                                        </td>
                                                                                        <td class="small text-muted">
                                                                                            <?= (!empty($row[$cols['date']])) ? date('M d', strtotime($row[$cols['date']])) : '-'; ?>
                                                                                        </td>
                                                                                        <td>
                                                                                            <?php if ($row[$cols['status']] == 'Paid'): ?>
                                                                                                <span
                                                                                                    class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
                                                                                            <?php else: ?>
                                                                                                <span
                                                                                                    class="badge bg-danger-subtle text-danger border border-danger-subtle">Due</span>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                    </tr>
                                                                            <?php endif;
                                                                            endforeach; ?>

                                                                            <?php if (($row['ot_transfer_amount'] + $row['ot_hpa_amount'] + $row['ot_hp_amount']) == 0): ?>
                                                                                <tr>
                                                                                    <td colspan="4" class="text-muted small py-2">No
                                                                                        vendor payments recorded.</td>
                                                                                </tr>
                                                                            <?php endif; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>

                                                            <?php if (!empty($row['ot_insurance_name'])):
                                                                // Calculate Validity
                                                                $otInsStart = new DateTime($row['ot_insurance_start_date']);
                                                                $otInsEnd = new DateTime($row['ot_insurance_end_date']);
                                                                $otInterval = $otInsStart->diff($otInsEnd);
                                                                $otValidity = $otInterval->y . " Year" . ($otInterval->y > 1 ? 's' : '');
                                                            ?>
                                                                <div class="p-3 border rounded-4 bg-light mb-4 position-relative">
                                                                    <span class="position-absolute top-0 start-50 translate-middle badge bg-dark text-white border-light border">
                                                                        Insurance Details
                                                                    </span>

                                                                    <div class="table-responsive mt-3">
                                                                        <table class="table table-borderless mb-0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <th class="small text-muted">Provider:</th>
                                                                                    <td class="fw-bold"><?= $row['ot_insurance_name']; ?></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th class="small text-muted">Payment Status:</th>
                                                                                    <td class="fw-bold <?= ($row['ot_insurance_payment_status'] == 'paid') ? 'text-success' : 'text-danger'; ?>">
                                                                                        <?= strtoupper($row['ot_insurance_payment_status']); ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th class="small text-muted">Amount:</th>
                                                                                    <td class="fw-bold">₹<?= number_format($row['ot_insurance_amount']); ?></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th class="small text-muted">Issued On:</th>
                                                                                    <td class="fw-bold"><?= $row['ot_insurance_start_date']; ?></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th class="small text-muted">Expiry Date:</th>
                                                                                    <td class="fw-bold text-danger"><?= $row['ot_insurance_end_date']; ?></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th class="small text-muted">Validity:</th>
                                                                                    <td class="fw-bold text-primary"><?= $otValidity; ?></td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>

                                                            <?php endif; ?>

                                                            <div class="row g-2">
                                                                <div class="col-6">
                                                                    <div
                                                                        class="p-3 border rounded-3 text-center bg-light h-100">
                                                                        <i
                                                                            class="ph-fill ph-pen-nib text-primary mb-2 fs-5"></i>
                                                                        <div class="small fw-bold text-muted mb-1">Purchaser
                                                                        </div>

                                                                        <?php if ($row['ot_purchaser_sign_status'] == 'Yes'): ?>
                                                                            <span class="badge bg-success mb-1">Signed</span>
                                                                            <div class="small text-muted" style="font-size:10px">
                                                                                <?= date('d-M-Y', strtotime($row['ot_purchaser_sign_date'])); ?>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <span
                                                                                class="badge bg-warning text-dark mb-1">Pending</span>
                                                                            <div class="small text-muted" style="font-size:10px">--
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>

                                                                <div class="col-6">
                                                                    <div
                                                                        class="p-3 border rounded-3 text-center bg-light h-100">
                                                                        <i
                                                                            class="ph-fill ph-pen-nib text-primary mb-2 fs-5"></i>
                                                                        <div class="small fw-bold text-muted mb-1">Seller</div>

                                                                        <?php if ($row['ot_seller_sign_status'] == 'Yes'): ?>
                                                                            <span class="badge bg-success mb-1">Signed</span>
                                                                            <div class="small text-muted" style="font-size:10px">
                                                                                <?= date('d-M-Y', strtotime($row['ot_seller_sign_date'])); ?>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <span
                                                                                class="badge bg-warning text-dark mb-1">Pending</span>
                                                                            <div class="small text-muted" style="font-size:10px">--
                                                                            </div>
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



                        <?php
                        endwhile;
                    else:
                        ?>
                        <div class="col-12 col-lg-12 mx-auto">
                            <div class="border border-2 border-dashed rounded-3 p-5 text-center">
                                <i class="ph-bold ph-car fs-1 text-secondary mb-3 d-block"></i>

                                <h5 class="text-secondary">No vehicles match your search</h5>

                                <div class="mt-3">
                                    <a href="?" class="btn btn-outline-secondary btn-sm">
                                        Clear Filters
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </section>
</body>

</html>