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
                $sql = "SELECT * FROM stock_vehicle_details ORDER BY id DESC";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()):

                    // Check photo1 — if empty use default
                    $image = (!empty($row['photo1'])) ? $row['photo1'] : '../images/default.jpg';

                    // Determine badge text and color based on sold_out
                    if ($row['sold_out'] == 0) {
                        $statusText = "AVAILABLE";
                        $statusClass = "text-success";
                    } else {
                        $statusText = "SOLD OUT";
                        $statusClass = "text-danger";
                    }
                ?>

                    <div class="col">
                        <div class="card border-0 inventory-card h-100">

                            <div class="hover-card position-relative overflow-hidden">

                                <!-- image -->
                                <img src="../<?= $image; ?>"
                                    class="d-block w-100 h-100 object-fit-cover"
                                    loading="lazy" alt="Bike">


                                <div class="position-absolute top-0 end-0 p-3 z-2 mt-2">
                                    <span class="badge status-badge fw-bold bg-white shadow-sm rounded-pill px-3 py-2 <?= $statusClass ?>"
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
                                        <div class="fw-bold text-primary fs-4">₹ <?= number_format($row['cash_price']); ?></div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle fw-normal">
                                            <?= date('Y', strtotime($row['register_date'])); ?>
                                        </span>
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle fw-normal">
                                            <?= $row['owner_serial']; ?> Owner
                                        </span>
                                    </div>

                                    <div class="d-flex gap-2 mt-3">
                                        <a href="edit_inventory.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-dark fw-bold flex-grow-1 rounded-pill py-2">
                                            <i class="ph-bold ph-pencil-simple text-white me-1"></i> Edit
                                        </a>

                                        <button class="btn btn-sm btn-outline-dark fw-bold flex-grow-1 rounded-pill py-2"
                                            data-bs-toggle="modal" data-bs-target="#viewDealModal">
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- View Vehicle Modal -->
                    <div class="modal fade" id="viewDealModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen">
                            <div class="modal-content rounded-5 border-0 shadow-lg overflow-hidden">
                                <!-- Header -->
                                <div class="modal-header border-bottom bg-white px-4 py-3 sticky-top z-3">
                                    <div class="d-flex align-items-center gap-3 w-100">
                                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm border"
                                            style="width: 45px; height: 45px; overflow: hidden; padding: 2px;">
                                            <img src="../images/logo.jpeg" alt="Chowdhury Automobile" class="rounded-circle"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div class="lh-1">
                                            <h5 class="modal-title fw-bold text-dark mb-1">WB 12 AB 9999</h5>
                                            <div class="d-flex align-items-center gap-2 small text-muted">
                                                <span class="fw-bold text-uppercase">Royal Enfield Classic 350</span>
                                                <i class="ph-fill ph-dot text-muted" style="font-size: 8px;"></i>
                                                <span
                                                    class="badge bg-danger text-white border border-danger-subtle rounded-pill">Sold
                                                    Out</span>
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
                                                    data-bs-toggle="collapse" data-bs-target="#collapseVehicle">
                                                    <i class="ph-bold ph-moped me-2 text-primary fs-5"></i> Vehicle Details
                                                </button>
                                            </h2>
                                            <div id="collapseVehicle" class="accordion-collapse collapse show"
                                                data-bs-parent="#dealDetailsAccordion">
                                                <div class="accordion-body bg-white p-4 border-top">

                                                    <!-- Photos -->
                                                    <h6 class="fw-bold text-muted small text-uppercase mb-3">Vehicle Photos
                                                    </h6>
                                                    <div class="row g-3 mb-4">
                                                        <div class="col-6 col-md-3">
                                                            <div
                                                                class="ratio ratio-1x1 rounded-4 overflow-hidden border mb-2 bg-light">
                                                                <img src="https://images.carandbike.com/cms/articles/3201199/Royal_Enfield_Hunter_350_1_2022_08_05_T03_41_40_503_Z_6ab6dc0960.png"
                                                                    class="object-fit-cover">
                                                            </div>
                                                            <button class="btn btn-sm btn-dark rounded-pill w-100 fw-bold py-1"
                                                                style="font-size: 11px;">Download</button>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div
                                                                class="ratio ratio-1x1 rounded-4 overflow-hidden border mb-2 bg-light">
                                                                <img src="https://images.carandbike.com/cms/articles/3201199/Royal_Enfield_Hunter_350_1_2022_08_05_T03_41_40_503_Z_6ab6dc0960.png"
                                                                    class="object-fit-cover">
                                                            </div>
                                                            <button class="btn btn-sm btn-dark rounded-pill w-100 fw-bold py-1"
                                                                style="font-size: 11px;">Download</button>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div
                                                                class="ratio ratio-1x1 rounded-4 overflow-hidden border mb-2 bg-light">
                                                                <img src="https://images.carandbike.com/cms/articles/3201199/Royal_Enfield_Hunter_350_1_2022_08_05_T03_41_40_503_Z_6ab6dc0960.png"
                                                                    class="object-fit-cover">
                                                            </div>
                                                            <button class="btn btn-sm btn-dark rounded-pill w-100 fw-bold py-1"
                                                                style="font-size: 11px;">Download</button>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div
                                                                class="ratio ratio-1x1 rounded-4 overflow-hidden border mb-2 bg-light">
                                                                <img src="https://images.carandbike.com/cms/articles/3201199/Royal_Enfield_Hunter_350_1_2022_08_05_T03_41_40_503_Z_6ab6dc0960.png"
                                                                    class="object-fit-cover">
                                                            </div>
                                                            <button class="btn btn-sm btn-dark rounded-pill w-100 fw-bold py-1"
                                                                style="font-size: 11px;">Download</button>
                                                        </div>
                                                    </div>

                                                    <!-- Basic Info -->
                                                    <div class="p-3 bg-light rounded-4 border mb-3">
                                                        <div class="row g-3">
                                                            <div class="col-6 col-md-4">
                                                                <small class="text-muted text-uppercase fw-bold"
                                                                    style="font-size: 10px;">Register Date</small>
                                                                <div class="fw-bold text-dark">2023-05-15</div>
                                                            </div>
                                                            <div class="col-6 col-md-4">
                                                                <small class="text-muted text-uppercase fw-bold"
                                                                    style="font-size: 10px;">Vehicle Type</small>
                                                                <div class="fw-bold text-dark">Motorcycle</div>
                                                            </div>

                                                            <div class="col-6 col-md-4">
                                                                <small class="text-muted text-uppercase fw-bold"
                                                                    style="font-size: 10px;">Owner Serial</small>
                                                                <div class="fw-bold text-dark">1st Owner</div>
                                                            </div>
                                                            <div class="col-12 col-md-12">
                                                                <small class="text-muted text-uppercase fw-bold"
                                                                    style="font-size: 10px;">Bike Name</small>
                                                                <div class="fw-bold text-dark">Pulsar 125</div>
                                                            </div>
                                                            <div class="col-12 col-md-12">
                                                                <small class="text-muted text-uppercase fw-bold"
                                                                    style="font-size: 10px;">Vehicle Number</small>
                                                                <div class="fw-bold text-dark">WB 12 AB 9999</div>
                                                            </div>
                                                            <div class="col-12 col-md-12">
                                                                <small class="text-muted text-uppercase fw-bold"
                                                                    style="font-size: 10px;">Chassis Number</small>
                                                                <div class="fw-bold text-dark font-monospace">
                                                                    ME3J5F5F9LC01234
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-md-12">
                                                                <small class="text-muted text-uppercase fw-bold"
                                                                    style="font-size: 10px;">Engine Number</small>
                                                                <div class="fw-bold text-dark font-monospace">J5F5F9LC09988
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Price & Payment Mode -->
                                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                                        <div>
                                                            <small class="text-muted text-uppercase fw-bold">Payment
                                                                Mode</small>
                                                            <div class="fw-bold text-primary"><i
                                                                    class="ph-bold ph-google-logo me-1"></i> Google Pay
                                                            </div>
                                                        </div>
                                                        <div class="text-end">
                                                            <small class="text-muted text-uppercase fw-bold">Price</small>
                                                            <div class="fs-4 fw-bold text-dark">₹ 1,85,000</div>
                                                        </div>
                                                    </div>

                                                    <!-- Transaction ID / UPI ID -->
                                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                                        <div>
                                                            <small class="text-muted text-uppercase fw-bold">Transaction
                                                                ID</small>
                                                            <div class="fw-bold text-primary">TXN123456789</div>
                                                        </div>
                                                        <div class="text-end">
                                                            <small class="text-muted text-uppercase fw-bold">UPI ID</small>
                                                            <div class="fw-bold text-dark">dummy@upi</div>
                                                        </div>
                                                    </div>


                                                    <!-- Police Challan Table -->
                                                    <div class="border rounded-4 overflow-scroll">
                                                        <div
                                                            class="bg-light px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                                            <h6 class="fw-bold text-danger mb-0 small text-uppercase">Police
                                                                Challan Details</h6>
                                                            <span class="badge bg-danger">Yes</span>
                                                        </div>
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
                                                                <tr class="border-bottom">
                                                                    <td>1</td>
                                                                    <td class="font-monospace small">WB/KOL/2023/11</td>
                                                                    <td class="fw-bold">₹500</td>
                                                                    <td><span
                                                                            class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
                                                                    </td>
                                                                </tr>
                                                                <tr class="border-bottom">
                                                                    <td>2</td>
                                                                    <td class="font-monospace small">WB/HOW/2023/45</td>
                                                                    <td class="fw-bold">₹1000</td>
                                                                    <td><span
                                                                            class="badge bg-danger-subtle text-danger border border-danger-subtle">Pending</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>3</td>
                                                                    <td class="font-monospace small">--</td>
                                                                    <td class="fw-bold">--</td>
                                                                    <td>--</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- ==========================
                             STEP 2: SELLER DETAILS
                             ========================== -->
                                        <div class="accordion-item rounded-4 shadow-sm border-0 mb-3 overflow-hidden">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed fw-bold text-uppercase text-dark py-3"
                                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeller">
                                                    <i class="ph-bold ph-user-circle me-2 text-primary fs-5"></i> Seller
                                                    Details
                                                </button>
                                            </h2>
                                            <div id="collapseSeller" class="accordion-collapse collapse"
                                                data-bs-parent="#dealDetailsAccordion">
                                                <div class="accordion-body bg-white p-4 border-top">

                                                    <div class="d-flex justify-content-between mb-3">
                                                        <div>
                                                            <small class="text-muted text-uppercase fw-bold">Seller
                                                                Name</small>
                                                            <div class="fs-5 fw-bold text-dark">Rahul Roy</div>
                                                        </div>
                                                        <div class="text-end">
                                                            <small class="text-muted text-uppercase fw-bold">Date</small>
                                                            <div class="fw-bold text-dark">2025-11-26</div>
                                                        </div>
                                                    </div>

                                                    <!-- Vehicle Info -->
                                                    <div class="row g-2 mb-3">
                                                        <div class="col-12">
                                                            <small class="text-muted fw-bold" style="font-size:10px;">VEHICLE
                                                                NO</small>
                                                            <div class="fw-bold text-dark small">WB 02 AD 5555</div>
                                                        </div>
                                                        <div class="col-12">
                                                            <small class="text-muted fw-bold" style="font-size:10px;">BIKE
                                                                NAME</small>
                                                            <div class="fw-bold text-dark small">Pulsar 150</div>
                                                        </div>
                                                        <div class="col-12">
                                                            <small class="text-muted fw-bold" style="font-size:10px;">CHASSIS
                                                                NO</small>
                                                            <div class="fw-bold text-dark font-monospace small">MD2A123...
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <small class="text-muted fw-bold" style="font-size:10px;">ENGINE
                                                                NO</small>
                                                            <div class="fw-bold text-dark font-monospace small">DHK88...
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Contact -->
                                                    <div class="bg-light p-3 rounded-3 mb-3 border">
                                                        <small class="text-muted fw-bold">Address</small>
                                                        <div class="mb-2">123, G.T. Road, Howrah - 711101</div>
                                                        <div class="d-flex gap-3 flex-wrap">
                                                            <a href="tel:9876543210"
                                                                class="badge bg-white text-dark border text-decoration-none">
                                                                <i class="ph-fill ph-phone"></i> 9876543210
                                                            </a>
                                                            <a href="tel:8765432109"
                                                                class="badge bg-white text-dark border text-decoration-none">
                                                                <i class="ph-fill ph-phone"></i> 8765432109
                                                            </a>
                                                            <span class="badge bg-white text-dark border text-muted opacity-50">
                                                                <i class="ph-fill ph-phone"></i> --
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Documents -->
                                                    <h6 class="fw-bold text-muted small text-uppercase mb-3">Purchaser
                                                        Documents
                                                    </h6>

                                                    <div class="row g-2">

                                                        <!-- AADHAR FRONT -->
                                                        <div class="col-6 col-md-3" style="object-fit: cover;">
                                                            <div class="border rounded p-2 text-center bg-white">
                                                                <small class="fw-bold d-block mb-1" style="font-size:10px">AADHAR
                                                                    FRONT</small>

                                                                <!-- Square Preview -->
                                                                <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden">
                                                                    <img src="your-image-path/aadhar-front.jpg"
                                                                        class="w-100 h-100 object-fit-cover">
                                                                </div>

                                                                <!-- View (opens image in new tab) -->
                                                                <a href="your-image-path/aadhar-front.jpg" target="_blank"
                                                                    class="btn btn-dark btn-sm w-100 py-0" style="font-size:10px">
                                                                    View
                                                                </a>

                                                                <!-- Download -->
                                                                <a href="your-image-path/aadhar-front.jpg" download
                                                                    class="btn btn-secondary btn-sm w-100 mt-1 py-0"
                                                                    style="font-size:10px">
                                                                    Download
                                                                </a>
                                                            </div>
                                                        </div>

                                                        <!-- AADHAR BACK -->
                                                        <div class="col-6 col-md-3">
                                                            <div class="border rounded p-2 text-center bg-white">
                                                                <small class="fw-bold d-block mb-1" style="font-size:10px">AADHAR
                                                                    BACK</small>

                                                                <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden">
                                                                    <img src="your-image-path/aadhar-back.jpg"
                                                                        class="w-100 h-100 object-fit-cover">
                                                                </div>

                                                                <a href="your-image-path/aadhar-back.jpg" target="_blank"
                                                                    class="btn btn-dark btn-sm w-100 py-0"
                                                                    style="font-size:10px">View</a>

                                                                <a href="your-image-path/aadhar-back.jpg" download
                                                                    class="btn btn-secondary btn-sm w-100 mt-1 py-0"
                                                                    style="font-size:10px">Download</a>
                                                            </div>
                                                        </div>

                                                        <!-- VOTER FRONT -->
                                                        <div class="col-6 col-md-3">
                                                            <div class="border rounded p-2 text-center bg-white">
                                                                <small class="fw-bold d-block mb-1" style="font-size:10px">VOTER
                                                                    FRONT</small>

                                                                <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden">
                                                                    <img src="your-image-path/voter-front.jpg"
                                                                        class="w-100 h-100 object-fit-cover">
                                                                </div>

                                                                <a href="your-image-path/voter-front.jpg" target="_blank"
                                                                    class="btn btn-dark btn-sm w-100 py-0"
                                                                    style="font-size:10px">View</a>

                                                                <a href="your-image-path/voter-front.jpg" download
                                                                    class="btn btn-secondary btn-sm w-100 mt-1 py-0"
                                                                    style="font-size:10px">Download</a>
                                                            </div>
                                                        </div>

                                                        <!-- VOTER BACK -->
                                                        <div class="col-6 col-md-3">
                                                            <div class="border rounded p-2 text-center bg-white">
                                                                <small class="fw-bold d-block mb-1" style="font-size:10px">VOTER
                                                                    BACK</small>

                                                                <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden">
                                                                    <img src="your-image-path/voter-back.jpg"
                                                                        class="w-100 h-100 object-fit-cover">
                                                                </div>

                                                                <a href="your-image-path/voter-back.jpg" target="_blank"
                                                                    class="btn btn-dark btn-sm w-100 py-0"
                                                                    style="font-size:10px">View</a>

                                                                <a href="your-image-path/voter-back.jpg" download
                                                                    class="btn btn-secondary btn-sm w-100 mt-1 py-0"
                                                                    style="font-size:10px">Download</a>
                                                            </div>
                                                        </div>

                                                    </div>


                                                    <!-- Papers & Challan -->
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-12">
                                                            <label class="small text-muted fw-bold mb-1">PAPERS
                                                                RECEIVED</label>
                                                            <div class="d-flex flex-wrap gap-2">
                                                                <span
                                                                    class="badge bg-primary-subtle text-primary border border-primary-subtle">RC</span>
                                                                <span
                                                                    class="badge bg-primary-subtle text-primary border border-primary-subtle">Tax
                                                                    Token</span>
                                                                <span
                                                                    class="badge bg-light text-muted border text-decoration-line-through">Insurance</span>
                                                                <span
                                                                    class="badge bg-primary-subtle text-primary border border-primary-subtle">PUCC</span>
                                                                <span
                                                                    class="badge bg-primary-subtle text-primary border border-primary-subtle">NOC</span>
                                                            </div>
                                                        </div>
                                                        <!-- <div class="col-12">
                                                    <div
                                                        class="p-2 border rounded bg-warning-subtle text-warning-emphasis">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="small fw-bold"><i
                                                                    class="ph-bold ph-warning-circle me-1"></i> Seller
                                                                Challan</span>
                                                            <span class="badge bg-warning text-dark">Yes</span>
                                                        </div>
                                                        <div class="small mt-1"><strong>No:</strong> WB/KOL/999/2024
                                                        </div>
                                                    </div>
                                                </div> -->
                                                    </div>

                                                    <!-- NOC -->
                                                    <div class="border rounded-4 p-3 mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <small class="fw-bold text-uppercase text-muted">NOC
                                                                Details</small>
                                                            <span class="badge bg-success">Paid</span>
                                                        </div>

                                                        <div class="row g-2">

                                                            <!-- NOC FRONT -->
                                                            <div class="col-6">
                                                                <div class="border rounded p-2 text-center bg-white">

                                                                    <small class="fw-bold d-block mb-1" style="font-size:10px">NOC
                                                                        FRONT</small>

                                                                    <!-- Small square preview -->
                                                                    <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden"
                                                                        style="width:50px; height:50px; margin:auto;">
                                                                        <img src="your-image-path/noc-front.jpg" class="w-100 h-100"
                                                                            style="object-fit:cover;">
                                                                    </div>

                                                                    <!-- View -->
                                                                    <a href="your-image-path/noc-front.jpg" target="_blank"
                                                                        class="btn btn-outline-dark btn-sm w-100 py-0"
                                                                        style="font-size:10px">
                                                                        View
                                                                    </a>

                                                                    <!-- Download -->
                                                                    <a href="your-image-path/noc-front.jpg" download
                                                                        class="btn btn-dark btn-sm w-100 mt-1 py-0"
                                                                        style="font-size:10px">
                                                                        Download
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <!-- NOC BACK -->
                                                            <div class="col-6">
                                                                <div class="border rounded p-2 text-center bg-white">

                                                                    <small class="fw-bold d-block mb-1" style="font-size:10px">NOC
                                                                        BACK</small>

                                                                    <!-- Small square preview -->
                                                                    <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden"
                                                                        style="width:50px; height:50px; margin:auto;">
                                                                        <img src="your-image-path/noc-back.jpg" class="w-100 h-100"
                                                                            style="object-fit:cover;">
                                                                    </div>

                                                                    <!-- View -->
                                                                    <a href="your-image-path/noc-back.jpg" target="_blank"
                                                                        class="btn btn-outline-dark btn-sm w-100 py-0"
                                                                        style="font-size:10px">
                                                                        View
                                                                    </a>

                                                                    <!-- Download -->
                                                                    <a href="your-image-path/noc-back.jpg" download
                                                                        class="btn btn-dark btn-sm w-100 mt-1 py-0"
                                                                        style="font-size:10px">
                                                                        Download
                                                                    </a>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <!-- RC -->
                                                    <div class="border rounded-4 p-3 mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <small class="fw-bold text-uppercase text-muted">RC
                                                                Details</small>
                                                            <!-- <span class="badge bg-success">Paid</span> -->
                                                        </div>

                                                        <div class="row g-2">

                                                            <!-- RC FRONT -->
                                                            <div class="col-6">
                                                                <div class="border rounded p-2 text-center bg-white">

                                                                    <small class="fw-bold d-block mb-1" style="font-size:10px">RC
                                                                        FRONT</small>

                                                                    <!-- Small square preview -->
                                                                    <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden"
                                                                        style="width:50px; height:50px; margin:auto;">
                                                                        <img src="your-image-path/rc-front.jpg" class="w-100 h-100"
                                                                            style="object-fit:cover;">
                                                                    </div>

                                                                    <!-- View -->
                                                                    <a href="your-image-path/rc-front.jpg" target="_blank"
                                                                        class="btn btn-outline-dark btn-sm w-100 py-0"
                                                                        style="font-size:10px">
                                                                        View
                                                                    </a>

                                                                    <!-- Download -->
                                                                    <a href="your-image-path/rc-front.jpg" download
                                                                        class="btn btn-dark btn-sm w-100 mt-1 py-0"
                                                                        style="font-size:10px">
                                                                        Download
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <!-- RC BACK -->
                                                            <div class="col-6">
                                                                <div class="border rounded p-2 text-center bg-white">

                                                                    <small class="fw-bold d-block mb-1" style="font-size:10px">RC
                                                                        BACK</small>

                                                                    <!-- Small square preview -->
                                                                    <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden"
                                                                        style="width:50px; height:50px; margin:auto;">
                                                                        <img src="your-image-path/rc-back.jpg" class="w-100 h-100"
                                                                            style="object-fit:cover;">
                                                                    </div>

                                                                    <!-- View -->
                                                                    <a href="your-image-path/rc-back.jpg" target="_blank"
                                                                        class="btn btn-outline-dark btn-sm w-100 py-0"
                                                                        style="font-size:10px">
                                                                        View
                                                                    </a>

                                                                    <!-- Download -->
                                                                    <a href="your-image-path/rc-back.jpg" download
                                                                        class="btn btn-dark btn-sm w-100 mt-1 py-0"
                                                                        style="font-size:10px">
                                                                        Download
                                                                    </a>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>



                                                    <!-- Payment Info -->
                                                    <div class="bg-light border rounded-4 p-3 mb-3">
                                                        <h6 class="fw-bold small mb-2">Payment Details</h6>
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="small text-muted">Type:</span>
                                                            <span class="fw-bold text-dark">Online (PhonePe)</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-3">
                                                            <span class="small text-muted">Txn ID:</span>
                                                            <span class="fw-bold font-monospace small">T230515123456</span>
                                                        </div>

                                                        <div class="d-flex text-center border rounded overflow-hidden bg-white">
                                                            <div class="flex-fill p-2 border-end">
                                                                <div class="small text-muted fw-bold" style="font-size:10px">
                                                                    TOTAL</div>
                                                                <div class="fw-bold">₹50,000</div>
                                                            </div>
                                                            <div class="flex-fill p-2 border-end bg-success-subtle text-success">
                                                                <div class="small fw-bold" style="font-size:10px">PAID</div>
                                                                <div class="fw-bold">₹30,000</div>
                                                            </div>
                                                            <div class="flex-fill p-2 bg-danger-subtle text-danger">
                                                                <div class="small fw-bold" style="font-size:10px">DUE</div>
                                                                <div class="fw-bold">₹20,000</div>
                                                            </div>
                                                        </div>
                                                        <div class="small text-danger mt-2 fst-italic"><i
                                                                class="ph-bold ph-info me-1"></i> Pending RC transfer</div>
                                                    </div>

                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <small class="text-muted fw-bold"
                                                                style="font-size:10px">SHOWROOM</small>
                                                            <div class="fw-bold small">Speedy Wheels</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted fw-bold" style="font-size:10px">STAFF</small>
                                                            <div class="fw-bold small">Amit Das</div>
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
                                                <button class="accordion-button collapsed fw-bold text-uppercase text-dark py-3"
                                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseBuyer">
                                                    <i class="ph-bold ph-shopping-bag me-2 text-primary fs-5"></i> Purchaser
                                                    Details
                                                </button>
                                            </h2>
                                            <div id="collapseBuyer" class="accordion-collapse collapse"
                                                data-bs-parent="#dealDetailsAccordion">
                                                <div class="accordion-body bg-white p-4 border-top">

                                                    <!-- Basic Info -->
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-12">
                                                            <small class="text-muted text-uppercase fw-bold">Purchaser
                                                                Name</small>
                                                            <div class="fs-5 fw-bold text-dark">Sneha Gupta</div>
                                                            <small class="text-muted">Date: 2025-11-26</small>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="bg-light p-3 rounded border">
                                                                <small class="text-muted text-uppercase fw-bold">Address</small>
                                                                <div>Flat 4B, Green Heights, Kolkata - 700054</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted fw-bold" style="font-size:10px">BIKE
                                                                NAME</small>
                                                            <div class="fw-bold text-dark">Classic 350</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted fw-bold" style="font-size:10px">VEHICLE
                                                                NO</small>
                                                            <div class="fw-bold text-dark">WB 12 AB 9999</div>
                                                        </div>
                                                        <!-- <div class="col-12">
                                                    <small class="text-muted fw-bold" style="font-size:10px">BUYER
                                                        NAME</small>
                                                    <div class="fw-bold text-dark">Sneha Gupta (Self)</div>
                                                </div> -->
                                                    </div>

                                                    <!-- Buyer Payment Table -->
                                                    <div class="border rounded-4 overflow-hidden mb-4">
                                                        <div class="bg-light px-3 py-2 border-bottom">
                                                            <h6 class="fw-bold mb-0 small text-uppercase">Buyer Payment Fees
                                                            </h6>
                                                        </div>
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
                                                                <tr>
                                                                    <td class="text-start ps-3 small fw-bold">Transfer</td>
                                                                    <td>₹2,500</td>
                                                                    <td class="small text-muted">Nov 20</td>
                                                                    <td><span
                                                                            class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-start ps-3 small fw-bold">HPA</td>
                                                                    <td>₹1,500</td>
                                                                    <td class="small text-muted">Nov 22</td>
                                                                    <td><span
                                                                            class="badge bg-danger-subtle text-danger border border-danger-subtle">Due</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-start ps-3 small fw-bold">HP</td>
                                                                    <td>₹500</td>
                                                                    <td class="small text-muted">Nov 22</td>
                                                                    <td><span
                                                                            class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <!-- Insurance Details (New Field) -->
                                                    <div class="p-3 border rounded-4 bg-light mb-4 position-relative">
                                                        <span
                                                            class="position-absolute top-0 start-50 translate-middle badge bg-dark text-white border-light border">
                                                            Insurance Details
                                                        </span>

                                                        <div class="row g-2 mt-1">

                                                            <!-- Provider -->
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="small text-muted">Provider:</span>
                                                                    <span class="fw-bold">Tata AIG Insurance</span>
                                                                </div>
                                                            </div>

                                                            <!-- Policy Number -->
                                                            <!-- <div class="col-12">
                                                        <div class="d-flex justify-content-between">
                                                            <span class="small text-muted">Policy No:</span>
                                                            <span class="fw-bold font-monospace">3005/A/123456</span>
                                                        </div>
                                                    </div> -->

                                                            <!-- Payment Status -->
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="small text-muted">Payment Status:</span>
                                                                    <span class="fw-bold text-success">PAID</span>
                                                                </div>
                                                            </div>

                                                            <!-- Amount -->
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="small text-muted">Amount:</span>
                                                                    <span class="fw-bold">₹ 1,800</span>
                                                                </div>
                                                            </div>

                                                            <!-- Today Date -->
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="small text-muted">Issued On:</span>
                                                                    <span class="fw-bold">2025-11-26</span>
                                                                </div>
                                                            </div>

                                                            <!-- Expiry Date -->
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="small text-muted">Expiry Date:</span>
                                                                    <span class="fw-bold text-danger">2026-11-26</span>
                                                                </div>
                                                            </div>

                                                            <!-- Validity Text -->
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="small text-muted">Validity:</span>
                                                                    <span class="fw-bold text-primary">1 Year</span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>


                                                    <!-- Finance Section (HPA) -->
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
                                                                <div class="fw-bold text-dark">Bajaj Finance</div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <small class="text-primary-emphasis fw-bold"
                                                                            style="font-size:10px">DISBURSED AMT</small>
                                                                        <div class="fw-bold text-dark">₹1,00,000</div>
                                                                    </div>
                                                                    <span class="badge bg-success">Paid</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 mt-2 pt-2 border-top border-primary-subtle">
                                                                <small class="text-primary-emphasis fw-bold d-block mb-1"
                                                                    style="font-size:12px">REGISTERED MOBILES</small>
                                                                <div class="d-flex gap-3 flex-wrap">
                                                                    <a href="tel:9876543210"
                                                                        class="badge bg-white text-dark border text-decoration-none">
                                                                        <i class="ph-fill ph-phone"></i> 9876543210
                                                                    </a>
                                                                    <a href="tel:8765432109"
                                                                        class="badge bg-white text-dark border text-decoration-none">
                                                                        <i class="ph-fill ph-phone"></i> 8765432109
                                                                    </a>
                                                                    <span
                                                                        class="badge bg-white text-dark border text-muted opacity-50">
                                                                        <i class="ph-fill ph-phone"></i> --
                                                                    </span>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Price Breakdown -->
                                                    <div class="d-flex text-center border rounded overflow-hidden bg-white mb-3">
                                                        <div class="flex-fill p-2 border-end">
                                                            <div class="small text-muted fw-bold" style="font-size:10px">
                                                                TOTAL
                                                            </div>
                                                            <div class="fw-bold">₹1,85,000</div>
                                                        </div>
                                                        <div class="flex-fill p-2 border-end bg-success-subtle text-success">
                                                            <div class="small fw-bold" style="font-size:10px">PAID</div>
                                                            <div class="fw-bold">₹85,000</div>
                                                        </div>
                                                        <div class="flex-fill p-2 bg-danger-subtle text-danger">
                                                            <div class="small fw-bold" style="font-size:10px">DUE</div>
                                                            <div class="fw-bold">₹1,00,000</div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label class="small text-muted fw-bold">PAYMENT ALL PAID?</label>
                                                        <div class="fw-bold text-danger">No</div>
                                                    </div>

                                                    <!-- Documents -->
                                                    <h6 class="fw-bold text-muted small text-uppercase mb-3">Purchaser
                                                        Documents
                                                    </h6>

                                                    <div class="row g-2">

                                                        <!-- AADHAR FRONT -->
                                                        <div class="col-6 col-md-3" style="object-fit: cover;">
                                                            <div class="border rounded p-2 text-center bg-white">
                                                                <small class="fw-bold d-block mb-1" style="font-size:10px">AADHAR
                                                                    FRONT</small>

                                                                <!-- Square Preview -->
                                                                <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden">
                                                                    <img src="your-image-path/aadhar-front.jpg"
                                                                        class="w-100 h-100 object-fit-cover">
                                                                </div>

                                                                <!-- View (opens image in new tab) -->
                                                                <a href="your-image-path/aadhar-front.jpg" target="_blank"
                                                                    class="btn btn-dark btn-sm w-100 py-0" style="font-size:10px">
                                                                    View
                                                                </a>

                                                                <!-- Download -->
                                                                <a href="your-image-path/aadhar-front.jpg" download
                                                                    class="btn btn-secondary btn-sm w-100 mt-1 py-0"
                                                                    style="font-size:10px">
                                                                    Download
                                                                </a>
                                                            </div>
                                                        </div>

                                                        <!-- AADHAR BACK -->
                                                        <div class="col-6 col-md-3">
                                                            <div class="border rounded p-2 text-center bg-white">
                                                                <small class="fw-bold d-block mb-1" style="font-size:10px">AADHAR
                                                                    BACK</small>

                                                                <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden">
                                                                    <img src="your-image-path/aadhar-back.jpg"
                                                                        class="w-100 h-100 object-fit-cover">
                                                                </div>

                                                                <a href="your-image-path/aadhar-back.jpg" target="_blank"
                                                                    class="btn btn-dark btn-sm w-100 py-0"
                                                                    style="font-size:10px">View</a>

                                                                <a href="your-image-path/aadhar-back.jpg" download
                                                                    class="btn btn-secondary btn-sm w-100 mt-1 py-0"
                                                                    style="font-size:10px">Download</a>
                                                            </div>
                                                        </div>

                                                        <!-- VOTER FRONT -->
                                                        <div class="col-6 col-md-3">
                                                            <div class="border rounded p-2 text-center bg-white">
                                                                <small class="fw-bold d-block mb-1" style="font-size:10px">VOTER
                                                                    FRONT</small>

                                                                <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden">
                                                                    <img src="your-image-path/voter-front.jpg"
                                                                        class="w-100 h-100 object-fit-cover">
                                                                </div>

                                                                <a href="your-image-path/voter-front.jpg" target="_blank"
                                                                    class="btn btn-dark btn-sm w-100 py-0"
                                                                    style="font-size:10px">View</a>

                                                                <a href="your-image-path/voter-front.jpg" download
                                                                    class="btn btn-secondary btn-sm w-100 mt-1 py-0"
                                                                    style="font-size:10px">Download</a>
                                                            </div>
                                                        </div>

                                                        <!-- VOTER BACK -->
                                                        <div class="col-6 col-md-3">
                                                            <div class="border rounded p-2 text-center bg-white">
                                                                <small class="fw-bold d-block mb-1" style="font-size:10px">VOTER
                                                                    BACK</small>

                                                                <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden">
                                                                    <img src="your-image-path/voter-back.jpg"
                                                                        class="w-100 h-100 object-fit-cover">
                                                                </div>

                                                                <a href="your-image-path/voter-back.jpg" target="_blank"
                                                                    class="btn btn-dark btn-sm w-100 py-0"
                                                                    style="font-size:10px">View</a>

                                                                <a href="your-image-path/voter-back.jpg" download
                                                                    class="btn btn-secondary btn-sm w-100 mt-1 py-0"
                                                                    style="font-size:10px">Download</a>
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- ==========================
                             STEP 4: OWNERSHIP TRANSFER
                             ========================== -->
                                        <div class="accordion-item rounded-4 shadow-sm border-0 mb-3 overflow-hidden">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed fw-bold text-uppercase text-dark py-3"
                                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseTransfer">
                                                    <i class="ph-bold ph-arrows-left-right me-2 text-primary fs-5"></i>
                                                    Ownership Transfer
                                                </button>
                                            </h2>
                                            <div id="collapseTransfer" class="accordion-collapse collapse"
                                                data-bs-parent="#dealDetailsAccordion">
                                                <div class="accordion-body bg-white p-4 border-top">

                                                    <!-- Basic Transfer Info -->
                                                    <div class="row g-3 mb-4">
                                                        <div class="col-12 col-md-6">
                                                            <small class="text-muted fw-bold text-uppercase"
                                                                style="font-size:10px">Transfer Name To</small>
                                                            <div class="fw-bold text-dark">Sneha Gupta</div>
                                                        </div>
                                                        <div class="col-6 col-md-6">
                                                            <small class="text-muted fw-bold text-uppercase"
                                                                style="font-size:10px">Vehicle Number</small>
                                                            <div class="fw-bold text-dark">WB 12 AB 9999</div>
                                                        </div>
                                                        <div class="col-6 col-md-6">
                                                            <small class="text-muted fw-bold text-uppercase"
                                                                style="font-size:10px">RTO
                                                                Location</small>
                                                            <div class="fw-bold text-dark">Asansol</div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <small class="text-muted fw-bold text-uppercase"
                                                                style="font-size:10px">Vendor Name</small>
                                                            <div class="fw-bold text-primary">RTO Services Pvt Ltd</div>
                                                        </div>
                                                    </div>

                                                    <!-- Vendor Payment Table -->
                                                    <div class="border rounded-4 overflow-hidden mb-4">
                                                        <div class="bg-light px-3 py-2 border-bottom">
                                                            <h6 class="fw-bold mb-0 small text-uppercase">Vendor Payment
                                                                Details
                                                            </h6>
                                                        </div>
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
                                                                <tr>
                                                                    <td class="text-start ps-3 small fw-bold">Transfer</td>
                                                                    <td>₹1,200</td>
                                                                    <td class="small text-muted">Nov 28</td>
                                                                    <td><span
                                                                            class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-start ps-3 small fw-bold">HPA</td>
                                                                    <td>₹500</td>
                                                                    <td class="small text-muted">Nov 28</td>
                                                                    <td><span
                                                                            class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-start ps-3 small fw-bold">HP</td>
                                                                    <td>₹200</td>
                                                                    <td class="small text-muted">--</td>
                                                                    <td><span
                                                                            class="badge bg-danger-subtle text-danger border border-danger-subtle">Due</span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <!-- Insurance Details (New Field) -->
                                                    <div class="p-3 border rounded-4 bg-light mb-4 position-relative">
                                                        <span
                                                            class="position-absolute top-0 start-50 translate-middle badge bg-dark text-white border-light border">
                                                            Insurance Details
                                                        </span>

                                                        <div class="row g-2 mt-1">

                                                            <!-- Provider -->
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="small text-muted">Provider:</span>
                                                                    <span class="fw-bold">Tata AIG Insurance</span>
                                                                </div>
                                                            </div>

                                                            <!-- Policy Number -->
                                                            <!-- <div class="col-12">
                                                        <div class="d-flex justify-content-between">
                                                            <span class="small text-muted">Policy No:</span>
                                                            <span class="fw-bold font-monospace">3005/A/123456</span>
                                                        </div>
                                                    </div> -->

                                                            <!-- Payment Status -->
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="small text-muted">Payment Status:</span>
                                                                    <span class="fw-bold text-success">PAID</span>
                                                                </div>
                                                            </div>

                                                            <!-- Amount -->
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="small text-muted">Amount:</span>
                                                                    <span class="fw-bold">₹ 1,800</span>
                                                                </div>
                                                            </div>

                                                            <!-- Today Date -->
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="small text-muted">Issued On:</span>
                                                                    <span class="fw-bold">2025-11-26</span>
                                                                </div>
                                                            </div>

                                                            <!-- Expiry Date -->
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="small text-muted">Expiry Date:</span>
                                                                    <span class="fw-bold text-danger">2026-11-26</span>
                                                                </div>
                                                            </div>

                                                            <!-- Validity Text -->
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="small text-muted">Validity:</span>
                                                                    <span class="fw-bold text-primary">1 Year</span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <!-- Signatures -->
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <div class="p-3 border rounded-3 text-center bg-light h-100">
                                                                <i class="ph-fill ph-pen-nib text-primary mb-2 fs-5"></i>
                                                                <div class="small fw-bold text-muted mb-1">Purchaser</div>
                                                                <span class="badge bg-success mb-1">Signed</span>
                                                                <div class="small text-muted" style="font-size:10px">
                                                                    2025-11-26
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="p-3 border rounded-3 text-center bg-light h-100">
                                                                <i class="ph-fill ph-pen-nib text-primary mb-2 fs-5"></i>
                                                                <div class="small fw-bold text-muted mb-1">Seller</div>
                                                                <span class="badge bg-success mb-1">Signed</span>
                                                                <div class="small text-muted" style="font-size:10px">
                                                                    2025-11-26
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer border-top bg-white px-4 py-3">
                                    <button type="button" class="btn btn-light border fw-bold rounded-pill px-4 shadow-sm w-100"
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