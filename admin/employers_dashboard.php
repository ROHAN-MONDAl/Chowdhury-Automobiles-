<?php
// 1. SILENT START
ob_start(); // Buffer output

// ======================================================
// FIX: SETTINGS MUST BE FIRST
// ======================================================
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// 2. CONNECT TO DB
require_once 'db.php';

// 3. ENSURE SESSION STARTED
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Anti-Cache Headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// ======================================================
// 4. THE "KILL SWITCH" FUNCTION
// ======================================================
function force_404_exit()
{
    // Optional: Log the attack
    if (isset($_SESSION['user_id'])) {
        error_log("Security Breach: User " . $_SESSION['user_id'] . " attempted unauthorized access.");
    }

    session_unset();
    session_destroy();
    header("Location: 404.php");
    exit();
}

// ======================================================
// 5. SECURITY CHECKS
// ======================================================

// CHECK A: Is User Logged In?
if (!isset($_SESSION['user_id'])) {
    force_404_exit();
}

// CHECK B: Session Hijacking
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
} elseif ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    force_404_exit();
}

// CHECK C: Database Verification
$userId = $_SESSION['user_id'];

// Check users table
$stmt = $conn->prepare("SELECT id, email, role FROM users WHERE user_id = ? LIMIT 1");
$stmt->bind_param("s", $userId);
$stmt->execute();
$user_check = $stmt->get_result()->fetch_assoc();

if (!$user_check) {
    force_404_exit();
}

// CHECK D: Role Enforcement (UPDATED)
$current_role = strtolower($user_check['role']);

// *** FIX: Allow BOTH 'manager' AND 'admin' ***
if ($current_role !== 'manager' && $current_role !== 'admin') {
    force_404_exit();
}

// Try to get Manager Profile
$stmt_profile = $conn->prepare("SELECT full_name FROM managers WHERE user_id = ?");
$stmt_profile->bind_param("s", $userId);
$stmt_profile->execute();
$profile_res = $stmt_profile->get_result()->fetch_assoc();

// If profile not found, fallback to email
$display_name = $profile_res ? htmlspecialchars($profile_res['full_name']) : htmlspecialchars($user_check['email']);

// *** FIX: Map the display name to the variable your HTML expects ***
$current_user = $display_name;

// HTML Content starts below...
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
    <link rel="stylesheet" href="employers.css">

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
    <script src="employers.js" defer></script>

</head>

<body>
    <div id="loader" style="position:fixed; inset:0; background:rgba(242,242,247,0.98); z-index:9999; display:flex; justify-content:center; align-items:center; flex-direction:column;">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
        <div class="mt-3 fw-bold text-secondary" style="letter-spacing: 1px;">LOADING...</div>
    </div>

    <?php include "universal_message.php" ?>
    <?php include_once "global_message.php" ?>


    <section id="dashboard-section" class="pb-5">

        <nav class="navbar fixed-top glass-nav px-2 px-md-3 py-2 py-md-3 animate-entry">
            <div class="container-fluid">

                <a class="navbar-brand d-flex align-items-center gap-2 me-auto" href="dashboard.php">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-1"
                        style="width: 42px; height: 42px; overflow: hidden; padding: 2px;">
                        <img src="../images/logo.jpeg" alt="Logo" class="rounded-circle"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>

                    <div class="d-flex flex-column lh-1">
                        <span class="fs-6 fs-md-5 fw-bolder text-dark">CHOWDHURY</span>
                        <span class="text-secondary fw-bold text-uppercase"
                            style="font-size: 0.65rem; letter-spacing: 1px;">
                            Automobile
                        </span>
                    </div>
                </a>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none"
                        id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                        <div class="d-none d-md-flex flex-column text-end lh-sm">
                            <span class="fw-bold text-dark fs-6"><?= $current_user ?></span>
                            <span class="text-muted" style="font-size: 0.7rem;"><?= $current_role ?></span>
                        </div>

                        <div class="avatar-circle shadow-sm d-flex align-items-center justify-content-center bg-primary text-white"
                            style="width: 40px; height: 40px; border-radius: 50%;">
                            <?= substr($current_user, 0, 1) ?>
                        </div>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-2" aria-labelledby="userDropdown">

                        <li class="d-md-none px-3 py-2">
                            <div class="fw-bold text-dark"><?= $current_user ?></div>
                            <div class="badge rounded-pill <?= $role_badge_class ?> text-uppercase" style="font-size: 0.65rem;">
                                <?= $current_role ?>
                            </div>
                        </li>
                        <li class="d-md-none">
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 rounded-2 text-danger py-2" href="employers_logout.php">
                                <i class="ph-bold ph-sign-out fs-5"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </nav>

        <div style="margin-top: 110px;"></div>

        <div class="container">

            <div class="row mb-5">
                <div class="col-12">
                    <div class="card border-0 rounded-4 hover-card overflow-hidden position-relative animate-entry delay-1">
                        <div class="card-body p-4 p-md-5 d-flex align-items-center justify-content-between position-relative z-2">
                            <div>
                                <h1 class="fw-bolder display-6 mb-2">
                                    Welcome back, <span class="text-gradient"><?= explode(' ', $current_user)[0] ?></span>!
                                </h1>
                                <p class="text-secondary mb-0 fs-5">Here's what's happening at the showroom today.</p>
                            </div>
                            <div class="d-none d-lg-block">
                                <div class="text-end opacity-50">
                                    <div class="h2 fw-bold mb-0 text-dark"><?= date('d') ?></div>
                                    <div class="text-uppercase fw-bold text-secondary"><?= date('M Y') ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="position-absolute top-0 end-0 h-100 w-50 bg-primary opacity-10 rounded-start-pill" style="filter: blur(80px); transform: translateX(20%);"></div>
                    </div>
                </div>
            </div>

            <h5 class="text-muted fw-bold text-uppercase small mb-4 ms-1 letter-spacing-2 animate-entry delay-2">Quick Actions</h5>

            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 hover-card cursor-pointer animate-entry delay-2" onclick="openModal('leadsListModal')">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="p-3 rounded-4 bg-danger bg-opacity-10 text-danger">
                                    <i class="ph-bold ph-users-three fs-3"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0"><i class="ph-bold ph-dots-three text-muted"></i></button>
                                </div>
                            </div>
                            <h4 class="fw-bold mb-1">View Leads</h4>
                            <p class="text-muted small mb-3">Check active customer inquiries.</p>
                            <div class="d-flex align-items-center text-danger fw-bold small">
                                <span>Open List</span>
                                <i class="ph-bold ph-arrow-right ms-2"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 hover-card cursor-pointer animate-entry delay-3" onclick="openModal('leadModal')">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="p-3 rounded-4 bg-primary bg-opacity-10 text-primary">
                                    <i class="ph-bold ph-plus fs-3"></i>
                                </div>
                                <span class="badge bg-primary rounded-pill">New</span>
                            </div>
                            <h4 class="fw-bold mb-1">Capture Lead</h4>
                            <p class="text-muted small mb-3">Add a new customer walk-in.</p>
                            <div class="d-flex align-items-center text-primary fw-bold small">
                                <span>Add Now</span>
                                <i class="ph-bold ph-arrow-right ms-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="leadsListModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex flex-column align-items-start bg-white rounded-top-4">
                    <div class="d-flex justify-content-between w-100 align-items-center mb-3">
                        <h4 class="modal-title fw-bold fs-4">Manage Leads</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="input-group w-100 mb-2">
                        <span class="input-group-text bg-light border-0 ps-3">
                            <i class="ph-bold ph-magnifying-glass text-secondary fs-5"></i>
                        </span>
                        <input type="text" id="leadSearchInput" class="form-control bg-light border-0 py-2"
                            placeholder="Search by name, phone, or bike model..." autocomplete="off">
                    </div>
                </div>

                <div class="modal-body p-4 bg-light">
                    <div class="row g-3" id="leadsContainer">
                        <?php
                        $leadQuery = "SELECT id, name, phone, bike_model, created_at FROM leads ORDER BY id DESC";
                        $leadResult = $conn->query($leadQuery);

                        if ($leadResult && $leadResult->num_rows > 0):
                            while ($lead = $leadResult->fetch_assoc()):
                                $wa_phone = preg_replace('/[^0-9]/', '', $lead['phone']);
                        ?>
                                <div class="col-md-6 col-lg-4 lead-item">
                                    <div class="card bg-primary bg-gradient text-white border-0 shadow h-100 rounded-4 position-relative overflow-hidden hover-card">
                                        <div class="card-body p-4 position-relative z-2">
                                            <div class="mb-3">
                                                <h5 class="fw-bold mb-1 fs-5 searchable-name"><?= htmlspecialchars($lead['name']) ?></h5>
                                                <div class="text-white opacity-75 small searchable-phone">
                                                    <?= htmlspecialchars($lead['phone']) ?>
                                                </div>
                                            </div>

                                            <div class="d-flex gap-2 mb-4">
                                                <a href="tel:<?= htmlspecialchars($lead['phone']) ?>"
                                                    class="btn btn-light text-primary fw-bold flex-grow-1 d-flex align-items-center justify-content-center shadow-sm">
                                                    <i class="ph-bold ph-phone me-2"></i> Call
                                                </a>
                                                <a href="https://wa.me/<?= $wa_phone ?>" target="_blank"
                                                    class="btn btn-success fw-bold flex-grow-1 d-flex align-items-center justify-content-center shadow-sm"
                                                    style="background-color: #25D366; border-color: #25D366; color: white;">
                                                    <i class="ph-bold ph-whatsapp-logo me-2"></i> WhatsApp
                                                </a>
                                            </div>

                                            <!-- Bike Model Badge -->
                                            <div class="mb-1">
                                                <span class="badge bg-white text-primary fw-bold px-3 py-2 rounded-pill searchable-bike">
                                                    <?= htmlspecialchars($lead['bike_model']) ?>
                                                </span>
                                            </div>

                                            <!-- Date -->
                                            <div>
                                                <small class="opacity-75" style="font-size: 0.75rem;">
                                                    <?= date('M d', strtotime($lead['created_at'])) ?>
                                                </small>
                                            </div>

                                        </div>
                                        <i class="ph-bold ph-motorcycle position-absolute text-white opacity-25"
                                            style="font-size: 6rem; right: -10px; bottom: -20px; transform: rotate(-15deg);"></i>
                                    </div>
                                </div>
                            <?php
                            endwhile;
                        else:
                            ?>
                            <div class="col-12 text-center py-5">
                                <p class="text-muted fw-medium">No leads found in database.</p>
                            </div>
                        <?php endif; ?>

                        <div id="noResultsMsg" class="col-12 text-center py-5" style="display: none;">
                            <div class="text-muted opacity-50 mb-2"><i class="ph-bold ph-magnifying-glass fs-1"></i></div>
                            <p class="text-muted fw-medium">No matches found for your search.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="leadModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-0 px-4 pt-4">
                    <h4 class="modal-title fw-bold">Capture Lead</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <form method="POST" action="leads_store.php">
                        <div class="mb-3">
                            <label class="fw-bold small text-secondary mb-1">FULL NAME</label>
                            <input type="text" name="name" class="form-control bg-light border-0 py-2" placeholder="John Doe" required>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold small text-secondary mb-1">PHONE</label>
                            <input type="tel" name="phone" class="form-control bg-light border-0 py-2" placeholder="9876543210" pattern="^\d{10}$" required>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold small text-secondary mb-1">BIKE MODEL</label>
                            <input type="text" name="bike_model" class="form-control bg-light border-0 py-2" placeholder="e.g. Splendor Plus" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 mt-2 btn-shine">Save Lead</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>