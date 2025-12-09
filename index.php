<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Inventory | Chowdhury Automobile</title>

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

    <section class="py-5 bg-white">
        <div class="container">

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

                <!-- VEHICLE 1 -->
                <div class="col">
                    <div class="product-card">
                        <span class="badge-status bg-available">Available</span>

                        <div class="product-img-wrapper">
                            <img src="https://www.jawayezdimotorcycles.com/cdn/shop/files/JY_Walkthrough_video_Website_Thumbnail_1_Hero_IMG_Roadster_Desktop_1.png?v=1757934480&width=2880"
                                alt="Bike">
                        </div>

                        <div class="p-4 pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-secondary fw-bold text-uppercase" style="font-size: 0.7rem;">
                                    sport bike
                                </small>
                            </div>

                            <h5 class="fw-bold mb-1">Hero Splendor Plus</h5>
                            <p class="text-muted small fw-medium mb-3">WB 20 AB 1234</p>

                            <div class="d-flex gap-2 mb-3">
                                <span class="spec-chip">Register Year: 2025</span>
                                <span class="spec-chip">1st Owner</span>
                            </div>

                            <div class="d-flex align-items-end justify-content-between border-top pt-3">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Price</small>
                                    <span class="fw-bold fs-5">₹75,000</span>
                                </div>

                                <button class="btn btn-dark rounded-circle" style="width: 40px; height: 40px;"
                                    data-bs-toggle="modal" data-bs-target="#viewDealModal">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VEHICLE 2 -->
                <div class="col">
                    <div class="product-card" style="opacity: 0.9;">
                        <span class="badge-status bg-sold">Sold Out</span>

                        <div class="product-img-wrapper" style="filter: grayscale(100%);">
                            <img src="https://www.drivio.in/_next/image?url=https%3A%2F%2Fcdn.drivio.in%2Fblog%2Fblog-1734089144775.webp&w=1280&q=75"
                                alt="Bike">
                        </div>

                        <div class="p-4 pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-secondary fw-bold text-uppercase" style="font-size: 0.7rem;">
                                    Scooter
                                </small>
                            </div>

                            <h5 class="fw-bold mb-1">Hero Splendor Plus</h5>
                            <p class="text-muted small fw-medium mb-3">WB 20 AB 1234</p>

                            <div class="d-flex gap-2 mb-3">
                                <span class="spec-chip">Register Year: 2025</span>
                                <span class="spec-chip">1st Owner</span>
                            </div>

                            <div class="d-flex align-items-end justify-content-between border-top pt-3">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Price</small>
                                    <span class="fw-bold fs-5">₹75,000</span>
                                </div>

                                <button class="btn btn-dark rounded-circle" style="width: 40px; height: 40px;"
                                    data-bs-toggle="modal" data-bs-target="#viewDealModal">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- More vehicle cards can be added here following the same structure -->

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
    <div class="modal fade" id="viewDealModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen">
            <div class="modal-content rounded-5 border-0 shadow-lg overflow-hidden">


                <!-- Header -->
                <div class="modal-header border-bottom bg-white px-4 py-3 sticky-top z-3">
                    <div class="d-flex align-items-center gap-3 w-100">
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm border"
                            style="width: 45px; height: 45px; overflow: hidden; padding: 2px;">
                            <img src="images/logo.jpeg" alt="Chowdhury Automobile" class="rounded-circle"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="lh-1">
                            <h5 class="modal-title fw-bold text-dark mb-1">WB 12 AB 9999</h5>
                            <div class="d-flex align-items-center gap-2 small text-muted">
                                <span class="fw-bold text-uppercase">Royal Enfield Classic 350</span>
                                <i class="ph-fill ph-dot text-muted" style="font-size: 8px;"></i>
                                <span class="badge bg-danger text-white border border-danger-subtle rounded-pill">Sold
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
                                            <div class="ratio ratio-1x1 rounded-4 overflow-hidden border mb-2 bg-light">
                                                <img src="https://images.carandbike.com/cms/articles/3201199/Royal_Enfield_Hunter_350_1_2022_08_05_T03_41_40_503_Z_6ab6dc0960.png"
                                                    class="object-fit-cover">
                                            </div>
                                            <button class="btn btn-sm btn-dark rounded-pill w-100 fw-bold py-1"
                                                style="font-size: 11px;">Download</button>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="ratio ratio-1x1 rounded-4 overflow-hidden border mb-2 bg-light">
                                                <img src="https://images.carandbike.com/cms/articles/3201199/Royal_Enfield_Hunter_350_1_2022_08_05_T03_41_40_503_Z_6ab6dc0960.png"
                                                    class="object-fit-cover">
                                            </div>
                                            <button class="btn btn-sm btn-dark rounded-pill w-100 fw-bold py-1"
                                                style="font-size: 11px;">Download</button>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="ratio ratio-1x1 rounded-4 overflow-hidden border mb-2 bg-light">
                                                <img src="https://images.carandbike.com/cms/articles/3201199/Royal_Enfield_Hunter_350_1_2022_08_05_T03_41_40_503_Z_6ab6dc0960.png"
                                                    class="object-fit-cover">
                                            </div>
                                            <button class="btn btn-sm btn-dark rounded-pill w-100 fw-bold py-1"
                                                style="font-size: 11px;">Download</button>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="ratio ratio-1x1 rounded-4 overflow-hidden border mb-2 bg-light">
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
                                            <small class="text-muted fw-bold" style="font-size:10px">SHOWROOM</small>
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
                                                    <span class="badge bg-white text-dark border text-muted opacity-50">
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
                                            <small class="text-muted fw-bold text-uppercase" style="font-size:10px">RTO
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
                        <a href="#" class="btn btn-dark rounded-circle" style="width: 40px; height: 40px;"><i
                                class="fa-brands fa-whatsapp mt-1"></i></a>
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
                            <span class="small text-secondary">123, G.T. Road, Howrah - 711101, West Bengal.</span>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <a href="tel:+919876543210" class="text-decoration-none">
                                <span class="fw-bold text-white small d-block">Call Us</span>
                                <span class="small text-secondary">+91 98765 43210</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="overflow-hidden rounded-4 border border-secondary border-opacity-25"
                        style="height: 100%; min-height: 250px;">
                        <iframe class="footer-map-frame"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14736.313495863266!2d88.3308930740924!3d22.57615024479383!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a0277bd76922b03%3A0x6b4478206d9101d2!2sHowrah%2C%20West%20Bengal!5e0!3m2!1sen!2sin!4v1701358327495!5m2!1sen!2sin"
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