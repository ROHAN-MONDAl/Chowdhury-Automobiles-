<?php
include 'admin/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Inventory | Chowdhury Automobile</title>
    <!-- Favicon Icon -->
    <link rel="icon" type="image/png" class="border-5 rounded-5" href="images/logo.jpeg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="script.js"></script>

</head>

<body>

    <!-- Loading Screen -->
    <div id="loading-screen">
        <div class="loader-content">
            <video class="bike-video" autoplay muted loop playsinline>
                <source src="images/Motorcycle.mp4" type="video/mp4">
            </video>
            <h2>Chowdhury</h2>
            <span class="text-secondary fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 1.5px;">
                Automobile
            </span>
        </div>
    </div>



    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-1"
                    style="width: 48px; height: 48px; overflow: hidden; padding: 2px;">
                    <img src="images/logo.jpeg" alt="Chowdhury Automobile Logo" class="rounded-circle"
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

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa-solid fa-bars fs-4"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-3 align-items-center">
                    <li class="nav-item"><a class="nav-link fw-medium text-dark" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold text-dark active" href="#inventory">Inventory</a>
                    </li>
                    <li class="nav-item">
                        <a href="admin/index.php"
                            class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-secondary">
                            <i class="fa-solid fa-user-shield me-1"></i> Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <section class="inventory-header  mt-5 pt-5" id="inventory">
        <div class="container text-center">
            <h1 class="fw-black mb-4 display-5" style="font-weight: 800; letter-spacing: -1px;">
                Browse Inventory
            </h1>

            <div class="hero-search-container mb-4 mx-auto position-relative">
                <i class="fa-solid fa-magnifying-glass hero-search-icon"></i>
                <input type="text" class="hero-search-input" placeholder="Try 'Royal Enfield' or 'Activa'...">

                <button class="btn btn-dark rounded-circle position-absolute top-50 end-0 translate-middle-y me-2"
                    style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

            <div class="d-md-none mb-3">
                <select class="form-select" id="mobileFilterSelect">
                    <option value="all" selected>All Vehicles</option>
                    <option value="scooters">Scooters</option>
                    <option value="mopeds">Mopeds</option>
                    <option value="Dirt / off-road bikes">Dirt / off-road bikes</option>
                    <option value="electric">Electric Bikes</option>
                    <option value="cruiser">Cruiser Bikes</option>
                    <option value="sport">Sport Bikes</option>
                    <option value="touring">Touring Bikes</option>
                    <option value="adventure">Adventure / Dual-Sport Bikes</option>
                    <option value="naked">Naked / Standard Bikes</option>
                    <option value="cafe">Cafe Racers</option>
                    <option value="bobbers">Bobbers</option>
                    <option value="choppers">Choppers</option>
                    <option value="pocket">Pocket / Mini Bikes</option>
                </select>
            </div>

            <div class="d-none d-md-flex justify-content-center gap-2 flex-wrap">
                <div class="filter-chip active">All Vehicles</div>
                <div class="filter-chip"><i class="ri-e-bike-2-fill"></i> Scooters</div>
                <div class="filter-chip"><i class="fa-solid fa-bicycle"></i> Mopeds</div>
                <div class="filter-chip"><i class="fa-solid fa-mountain"></i> Dirt / off-road bikes</div>
                <div class="filter-chip"><i class="fa-solid fa-bolt"></i> Electric Bikes</div>
                <div class="filter-chip"><i class="fa-solid fa-road"></i> Cruiser Bikes</div>
                <div class="filter-chip"><i class="fa-solid fa-tachometer-alt"></i> Sport Bikes</div>
                <div class="filter-chip"><i class="fa-solid fa-helmet-safety"></i> Touring Bikes</div>
                <div class="filter-chip"><i class="fa-solid fa-mountain"></i> Adventure / Dual-Sport Bikes</div>
                <div class="filter-chip"><i class="fa-solid fa-circle-notch"></i> Naked / Standard Bikes</div>
                <div class="filter-chip"><i class="fa-solid fa-cafe"></i> Cafe Racers</div>
                <div class="filter-chip"><i class="fa-solid fa-star"></i> Bobbers</div>
                <div class="filter-chip"><i class="fa-solid fa-fire"></i> Choppers</div>
                <div class="filter-chip"><i class="fa-solid fa-bolt-lightning"></i> Pocket / Mini Bikes</div>
            </div>


            <p class="text-muted mt-3 small fw-bold">
                Showing <span class="vehicle-count"></span>
            </p>

        </div>
    </section>

    <section class="py-1">
        <div class="container">

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
                if (!empty($rto)) {
                    $rtoEscaped = $conn->real_escape_string($rto);
                    $sql .= " AND v.vehicle_number LIKE '%$rtoEscaped%'";
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
                            $imageSrc = "images/" . $row['photo1'];
                        } else {
                            $imageSrc = "images/default.jpg";
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
                        <!-- VEHICLE 1 -->
                        <div class="col">
                            <div class="product-card bikedekho-dark">

                                <!-- STATUS -->
                                <span class="badge-status <?= ($row['sold_out'] == 0) ? 'bg-available' : 'bg-sold'; ?>">
                                    <?= ($row['sold_out'] == 0) ? 'Available' : 'Sold Out'; ?>
                                </span>

                                <!-- IMAGE -->
                                <div class="bike-img">
                                    <img src="<?= !empty($row['photo1']) ? 'images/' . $row['photo1'] : 'images/default.jpg'; ?>"
                                        alt="Bike">
                                </div>

                                <!-- BODY -->
                                <div class="bike-body">

                                    <!-- VEHICLE TYPE -->
                                    <span class="bike-type fw-bold mb-1 d-inline-block">
                                        <?= htmlspecialchars($row['vehicle_type'] ?? 'Bike'); ?>
                                    </span>

                                    <!-- TITLE -->
                                    <h6 class="bike-title mb-1">
                                        <?= htmlspecialchars($row['model_name'] ?? $row['name']); ?>
                                    </h6>

                                    <!-- VEHICLE NUMBER -->
                                    <p class="bike-number mb-1">
                                        <?= htmlspecialchars($row['vehicle_number']); ?>
                                    </p>

                                    <!-- META INFO -->
                                    <div class="bike-meta mb-2">
                                        <?= date('Y', strtotime($row['register_date'])); ?> •
                                        <?= (int) $row['owner_serial']; ?> Owner
                                    </div>

                                    <!-- PRICE + ACTION -->
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <span class="bike-price">
                                            ₹<?= number_format($row['cash_price']); ?>
                                        </span>

                                        <button class="btn btn-dark btn-sm rounded-pill" data-bs-toggle="modal"
                                            data-bs-target="#<?= $modalID; ?>">
                                            View <i class="fa-solid fa-arrow-right ms-1"></i>
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>


            </div>

            <!-- PAGINATION (EMPTY — JS will build pages here) -->
            <div class="d-flex justify-content-center mt-5">
                <nav aria-label="Page navigation">
                    <ul class="pagination"></ul>
                </nav>
            </div>

        </div>
    </section>



    <!-- Vehicle Details View Modal - Accordion-based modal to view comprehensive vehicle and deal
              information including photos, specs, and transaction details -->
    <div class="modal fade" id="<?= $modalID; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen">
            <div class="modal-content rounded-0 border-0 h-100">
                <!-- Header -->
                <div class="modal-header border-bottom bg-white px-4 py-3 sticky-top z-3">
                    <div class="d-flex align-items-center gap-3 w-100">
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm border"
                            style="width: 45px; height: 45px; overflow: hidden; padding: 2px;">
                            <img src="images/logo.jpeg" alt="Chowdhury Automobile" class="rounded-circle"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="lh-1">
                            <h5 class="modal-title fw-bold text-dark mb-1">
                                <?php echo htmlspecialchars($row['vehicle_number']); ?></h5>
                            <div class="d-flex align-items-center gap-2 small text-muted">
                                <span
                                    class="fw-bold text-uppercase"><?php echo htmlspecialchars($row['name']); ?></span>
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
                                                $imgSrc = "images/" . $row[$photoKey];
                                        ?>
                                                <div class="col-6 col-md-3">
                                                    <div class="ratio ratio-1x1 rounded-4 overflow-hidden border mb-2 bg-light">
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
                                            <div class="col-6 col-md-4">
                                                <small class="text-muted text-uppercase fw-bold"
                                                    style="font-size: 10px;">Register Date</small>
                                                <div class="fw-bold text-dark">
                                                    <?= date('d-M-Y', strtotime($row['register_date'])); ?></div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <small class="text-muted text-uppercase fw-bold"
                                                    style="font-size: 10px;">Vehicle Type</small>
                                                <div class="fw-bold text-dark"><?= $row['vehicle_type']; ?></div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <small class="text-muted text-uppercase fw-bold"
                                                    style="font-size: 10px;">Owner Serial</small>
                                                <div class="fw-bold text-dark"><?= $row['owner_serial']; ?></div>
                                            </div>
                                            <div class="col-12 col-md-12">
                                                <small class="text-muted text-uppercase fw-bold"
                                                    style="font-size: 10px;">Bike Name</small>
                                                <div class="fw-bold text-dark"><?= $row['name']; ?></div>
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
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div>
                                            <small class="text-muted text-uppercase fw-bold">Payment Mode</small>
                                            <div class="fw-bold text-primary">
                                                <i class="ph-bold <?= $isOnline ? 'ph-globe' : 'ph-money'; ?> me-1"></i>
                                                <?= $method; ?>
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
                                        <div
                                            class="bg-light px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                            <h6 class="fw-bold text-danger mb-0 small text-uppercase">Police Challan
                                                Details</h6>
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
                                                                    <td class="font-monospace small"><?= $cNum; ?></td>
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
                                <button class="accordion-button collapsed fw-bold text-uppercase text-dark py-3"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseSeller_<?= $row['id']; ?>">
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
                                            <div class="fw-bold text-dark small text-uppercase">
                                                <?= $row['seller_vehicle_number']; ?></div>
                                        </div>
                                        <div class="col-12">
                                            <small class="text-muted fw-bold" style="font-size:10px;">BIKE NAME</small>
                                            <div class="fw-bold text-dark small"><?= $row['seller_bike_name']; ?></div>
                                        </div>
                                        <div class="col-12">
                                            <small class="text-muted fw-bold" style="font-size:10px;">CHASSIS NO</small>
                                            <div class="fw-bold text-dark font-monospace small">
                                                <?= $row['seller_chassis_no']; ?></div>
                                        </div>
                                        <div class="col-12">
                                            <small class="text-muted fw-bold" style="font-size:10px;">ENGINE NO</small>
                                            <div class="fw-bold text-dark font-monospace small">
                                                <?= $row['seller_engine_no']; ?></div>
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
                                                    <a href="tel:<?= $mob; ?>"
                                                        class="badge bg-white text-dark border text-decoration-none py-2">
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
                                            'doc_aadhar_back' => 'AADHAR BACK',
                                            'doc_voter_front' => 'VOTER FRONT',
                                            'doc_voter_back' => 'VOTER BACK'
                                        ];

                                        foreach ($docs as $col => $label):
                                            if (!empty($row[$col])):
                                                $imgSrc = "images/" . $row[$col];
                                        ?>
                                                <div class="col-6 col-md-3">
                                                    <div class="border rounded p-2 text-center bg-white h-100">
                                                        <small class="fw-bold d-block mb-1"
                                                            style="font-size:10px"><?= $label; ?></small>
                                                        <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden">
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
                                            <label class="small text-muted fw-bold mb-1">PAPERS RECEIVED</label>
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
                                        <div class="border rounded-3 p-3 mb-3 bg-white">
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                                <span class="fw-bold text-dark">NOC Status</span>
                                                <span
                                                    class="badge <?= ($row['noc_status'] == 'Paid') ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> border border-current">
                                                    <?= $row['noc_status']; ?>
                                                </span>
                                            </div>

                                            <div class="d-flex flex-column gap-3">
                                                <?php
                                                $nocDocs = ['noc_front' => 'Front Side', 'noc_back' => 'Back Side'];
                                                foreach ($nocDocs as $col => $label):
                                                    if (!empty($row[$col])):
                                                        $imgSrc = "images/" . $row[$col];
                                                ?>
                                                        <div class="d-flex align-items-center border rounded p-2">
                                                            <div class="flex-shrink-0">
                                                                <a href="<?= $imgSrc; ?>" target="_blank">
                                                                    <img src="<?= $imgSrc; ?>"
                                                                        class="rounded border object-fit-cover"
                                                                        style="width: 60px; height: 60px;">
                                                                </a>
                                                            </div>

                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-1 text-secondary" style="font-size: 0.85rem;">
                                                                    <?= $label; ?></h6>
                                                                <div class="d-flex gap-2">
                                                                    <a href="<?= $imgSrc; ?>" target="_blank"
                                                                        class="text-decoration-none" style="font-size: 0.8rem;">
                                                                        View
                                                                    </a>
                                                                    <span class="text-muted">|</span>
                                                                    <a href="<?= $imgSrc; ?>" download
                                                                        class="fw-bold text-dark text-decoration-none"
                                                                        style="font-size: 0.8rem;">
                                                                        Download
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                <?php
                                                    endif;
                                                endforeach;
                                                ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($row['rc_front']) || !empty($row['rc_back'])): ?>
                                        <div class="border rounded-3 p-3 mb-3 bg-white">
                                            <div class="mb-3 border-bottom pb-2">
                                                <span class="fw-bold text-dark">RC Details</span>
                                            </div>

                                            <div class="d-flex flex-column gap-3">
                                                <?php
                                                $rcDocs = ['rc_front' => 'RC Front Side', 'rc_back' => 'RC Back Side'];
                                                foreach ($rcDocs as $col => $label):
                                                    if (!empty($row[$col])):
                                                        $imgSrc = "images/" . $row[$col];
                                                ?>
                                                        <div class="d-flex align-items-center border rounded p-2">
                                                            <div class="flex-shrink-0">
                                                                <div class="ratio ratio-1x1 rounded border overflow-hidden"
                                                                    style="width: 60px;">
                                                                    <img src="<?= $imgSrc; ?>" class="object-fit-cover">
                                                                </div>
                                                            </div>

                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-1 text-secondary" style="font-size: 0.85rem;">
                                                                    <?= $label; ?></h6>
                                                                <div class="d-flex gap-2">
                                                                    <a href="<?= $imgSrc; ?>" target="_blank"
                                                                        class="text-decoration-none small text-primary">
                                                                        <i class="bi bi-eye"></i> View
                                                                    </a>
                                                                    <span class="text-muted small">|</span>
                                                                    <a href="<?= $imgSrc; ?>" download
                                                                        class="text-decoration-none small fw-bold text-dark">
                                                                        <i class="bi bi-download"></i> Download
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                <?php
                                                    endif;
                                                endforeach;
                                                ?>
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

                                        <div class="d-flex text-center border rounded overflow-hidden bg-white">
                                            <div class="flex-fill p-2 border-end">
                                                <div class="small text-muted fw-bold" style="font-size:10px">TOTAL</div>
                                                <div class="fw-bold">₹<?= number_format($row['total_amount'], 0); ?>
                                                </div>
                                            </div>
                                            <div class="flex-fill p-2 border-end bg-success-subtle text-success">
                                                <div class="small fw-bold" style="font-size:10px">PAID</div>
                                                <div class="fw-bold">₹<?= number_format($row['paid_amount'], 0); ?>
                                                </div>
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
                                            <div class="fw-bold small"><?= $row['exchange_showroom_name'] ?? '-'; ?>
                                            </div>
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
                        <div class="accordion-item rounded-4 shadow-sm border-0 mb-3 overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-uppercase text-dark py-3"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapsePurchaser_<?= $row['id']; ?>">
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
                                            <small class="text-muted">Date:
                                                <?= date('d-M-Y', strtotime($row['purchaser_date'])); ?></small>
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
                                            <div class="fw-bold text-dark text-uppercase">
                                                <?= $row['purchaser_vehicle_no']; ?></div>
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
                                                        'HPA' => ['amt' => 'purchaser_hpa_amount', 'date' => 'purchaser_hpa_date', 'status' => 'purchaser_hpa_status'],
                                                        'HP' => ['amt' => 'purchaser_hp_amount', 'date' => 'purchaser_hp_date', 'status' => 'purchaser_hp_status'],
                                                    ];
                                                    foreach ($fees as $label => $cols):
                                                        if ($row[$cols['amt']] > 0): // Only show if amount exists
                                                    ?>
                                                            <tr>
                                                                <td class="text-start ps-3 small fw-bold"><?= $label; ?></td>
                                                                <td>₹<?= number_format($row[$cols['amt']]); ?></td>
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
                                            <span
                                                class="position-absolute top-0 start-50 translate-middle badge bg-dark text-white border-light border">
                                                Insurance Details
                                            </span>
                                            <div class="row g-2 mt-1">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="small text-muted">Provider:</span>
                                                        <span
                                                            class="fw-bold"><?= $row['purchaser_insurance_name']; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="small text-muted">Payment Status:</span>
                                                        <span
                                                            class="fw-bold <?= ($row['purchaser_insurance_payment_status'] == 'paid') ? 'text-success' : 'text-danger'; ?>">
                                                            <?= strtoupper($row['purchaser_insurance_payment_status']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="small text-muted">Amount:</span>
                                                        <span class="fw-bold">₹
                                                            <?= number_format($row['purchaser_insurance_amount']); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="small text-muted">Issued On:</span>
                                                        <span
                                                            class="fw-bold"><?= $row['purchaser_insurance_issue_date']; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="small text-muted">Expiry Date:</span>
                                                        <span
                                                            class="fw-bold text-danger"><?= $row['purchaser_insurance_expiry_date']; ?></span>
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
                                            <div
                                                class="d-flex justify-content-between align-items-center border-bottom border-primary-subtle pb-2 mb-2">
                                                <span class="badge bg-primary">Finance Mode</span>
                                                <small class="fw-bold text-primary">HPA Active</small>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <small class="text-primary-emphasis fw-bold"
                                                        style="font-size:10px">FINANCE COMPANY</small>
                                                    <div class="fw-bold text-dark"><?= $row['purchaser_fin_hpa_with']; ?>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between align-items-center">
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
                                                <div class="col-12 mt-2 pt-2 border-top border-primary-subtle">
                                                    <small class="text-primary-emphasis fw-bold d-block mb-1"
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
                                            'purchaser_doc_aadhar_back' => 'AADHAR BACK',
                                            'purchaser_doc_voter_front' => 'VOTER FRONT',
                                            'purchaser_doc_voter_back' => 'VOTER BACK'
                                        ];
                                        foreach ($pDocs as $col => $label):
                                            if (!empty($row[$col])):
                                                $imgSrc = "images/" . $row[$col];
                                        ?>
                                                <div class="col-6 col-md-3">
                                                    <div class="border rounded p-2 text-center bg-white h-100">
                                                        <small class="fw-bold d-block mb-1"
                                                            style="font-size:10px"><?= $label; ?></small>
                                                        <div class="ratio ratio-1x1 mb-1 border rounded overflow-hidden">
                                                            <img src="<?= $imgSrc; ?>" class="w-100 h-100 object-fit-cover">
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
                                <button class="accordion-button collapsed fw-bold text-uppercase text-dark py-3"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTransfer_<?= $row['id']; ?>">
                                    <i class="ph-bold ph-arrows-left-right me-2 text-primary fs-5"></i>
                                    Ownership Transfer
                                </button>
                            </h2>
                            <div id="collapseTransfer_<?= $row['id']; ?>" class="accordion-collapse collapse"
                                data-bs-parent="#dealDetailsAccordion">
                                <div class="accordion-body bg-white p-4 border-top">

                                    <div class="row g-3 mb-4">
                                        <div class="col-12 col-md-6">
                                            <small class="text-muted fw-bold text-uppercase"
                                                style="font-size:10px">Transfer Name To</small>
                                            <div class="fw-bold text-dark"><?= $row['ot_name_transfer'] ?? '-'; ?></div>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <small class="text-muted fw-bold text-uppercase"
                                                style="font-size:10px">Vehicle Number</small>
                                            <div class="fw-bold text-dark text-uppercase">
                                                <?= $row['ot_vehicle_number'] ?? '-'; ?></div>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <small class="text-muted fw-bold text-uppercase" style="font-size:10px">RTO
                                                Location</small>
                                            <div class="fw-bold text-dark"><?= $row['ot_rto_name'] ?? '-'; ?></div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <small class="text-muted fw-bold text-uppercase"
                                                style="font-size:10px">Vendor Name</small>
                                            <div class="fw-bold text-primary"><?= $row['ot_vendor_name'] ?? '-'; ?>
                                            </div>
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
                                                        'HPA' => ['amt' => 'ot_hpa_amount', 'date' => 'ot_hpa_date', 'status' => 'ot_hpa_status'],
                                                        'HP' => ['amt' => 'ot_hp_amount', 'date' => 'ot_hp_date', 'status' => 'ot_hp_status'],
                                                    ];

                                                    foreach ($otFees as $label => $cols):
                                                        if (($row[$cols['amt']] ?? 0) > 0): // Only show if amount > 0
                                                    ?>
                                                            <tr>
                                                                <td class="text-start ps-3 small fw-bold"><?= $label; ?></td>
                                                                <td>₹<?= number_format($row[$cols['amt']]); ?></td>
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
                                                            <td colspan="4" class="text-muted small py-2">No vendor payments
                                                                recorded.</td>
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
                                            <span
                                                class="position-absolute top-0 start-50 translate-middle badge bg-dark text-white border-light border">
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
                                                        <span
                                                            class="fw-bold <?= ($row['ot_insurance_payment_status'] == 'paid') ? 'text-success' : 'text-danger'; ?>">
                                                            <?= strtoupper($row['ot_insurance_payment_status']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="small text-muted">Amount:</span>
                                                        <span class="fw-bold">₹
                                                            <?= number_format($row['ot_insurance_amount']); ?></span>
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
                                                        <span
                                                            class="fw-bold text-danger"><?= $row['ot_insurance_end_date']; ?></span>
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
                    <button type="button" class="btn btn-light border fw-bold rounded-pill px-4 shadow-sm w-100"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


<?php
                    endwhile;
                else:
?>
<div class="col-12">
    <div class="alert alert-info text-center">
        <i class="ph-bold ph-info fs-1 d-block mb-2"></i>
        <h5>No vehicles found</h5>
        <p class="mb-0">Try adjusting your filters or <a href="?">reset them</a> to see all vehicles.</p>
    </div>
</div>
<?php endif; ?>

<footer id="contact" class="section-footer py-5">
    <div class="container py-4">
        <div class="row g-5">

            <div class="col-lg-4">
                <a class="navbar-brand d-flex align-items-center gap-2" href="staffs_portal.php">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-1"
                        style="width: 48px; height: 48px; overflow: hidden; padding: 2px;">
                        <img src="images/logo.jpeg" alt="Chowdhury Automobile Logo" class="rounded-circle"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>

                    <div class="d-flex flex-column lh-1">
                        <span class="fs-5 fw-bolder text-white">CHOWDHURY</span>
                        <span class="text-secondary fw-bold text-uppercase"
                            style="font-size: 0.7rem; letter-spacing: 1.5px;">
                            Automobile
                        </span>
                    </div>
                </a>


                <p class="text-secondary small mt-2 mb-4 pe-lg-4">
                    West Bengal's most trusted pre-owned two-wheeler dealership. We ensure every ride you take is
                    safe, legal, and reliable.
                </p>
                <div class="d-flex gap-3">
                    <a href="https://wa.me/917047649289" target="_blank" class="btn btn-dark rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                    <a href="#" class="btn btn-dark rounded-circle" style="width: 40px; height: 40px;"><i
                            class="fa-brands fa-facebook-f mt-1"></i></a>
                    <a href="#" class="btn btn-dark rounded-circle" style="width: 40px; height: 40px;"><i
                            class="fa-brands fa-instagram mt-1"></i></a>
                </div>
            </div>

            <div class="col-lg-3">
                <h6 class="fw-bold mb-4">Get in Touch</h6>
                <div class="d-flex gap-3 mb-4">
                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <span class="fw-bold small d-block">Showroom</span>
                        <span class="small text-secondary">Beliatore (Opposite of WBSEDCL office),pin :- 722203.</span>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <div>
                        <a href="tel:+919332072223" class="text-decoration-none">
                            <span class="fw-bold text-white small d-block">Call Us</span>
                            <span class="small text-secondary">+91 93320 72223</span>
                        </a>
                        <a href="tel:+917047649289" class="text-decoration-none">
                            <span class="fw-bold text-white small d-block">Call Us</span>
                            <span class="small text-secondary">+91 70476 49289</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="overflow-hidden rounded-4 border border-secondary border-opacity-25"
                    style="height: 100%; min-height: 250px;">
                    <iframe class="footer-map-frame"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3663.9166233335636!2d87.21558441011682!3d23.318783705123142!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39f7a1a94a63818b%3A0xc439f4a9fb186f34!2sBeliatore%20CCC(WBSEDCL)!5e0!3m2!1sen!2sin!4v1766005607136!5m2!1sen!2sin"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>

        <div class="border-top border-secondary border-opacity-25 mt-5 pt-4 text-center small text-secondary">
            © 2025 Chowdhury Automobile. All rights reserved. | Developed by <a href="https://web2infinity.com/"
                style="text-decoration: none; color: white;">Web2Infinity</a>
        </div>

    </div>
</footer>


</html>