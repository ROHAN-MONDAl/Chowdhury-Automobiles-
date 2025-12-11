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
    <link rel="icon" type="image/png" class="border-5 rounded-5" href="../images/logo.jpeg">
    <!-- ==================== Stylesheets ==================== -->

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="login.css">

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
    <script src="login.js" defer></script>
</head>


<body>

    <!-- Loading Screen -->
    <div id="loading-screen">
        <div class="loader-content">
            <video class="bike-video" autoplay muted loop playsinline>
                <source src="../images/Motorcycle.mp4" type="video/mp4">
            </video>
            <h2>Chowdhury</h2>
            <span class="text-secondary fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 1.5px;">
                Automobile
            </span>
        </div>
    </div>


    <?php include 'global_message.php'; ?>


    <!-- Main Section -->
    <section class="min-vh-100 d-flex p-0 m-0 bg-light">
        <div class="container-fluid g-0">
            <div class="row g-0 min-vh-100">

                <div
                    class="col-lg-6 d-none d-lg-flex flex-column justify-content-center align-items-center position-relative bg-dark text-white overflow-hidden">
                    <img src="../images/loginBanner.avif" alt="Automobile Background"
                        class="position-absolute w-100 h-100" style="object-fit: cover; opacity: 0.6; z-index: 0;">

                    <div class="position-absolute w-100 h-100"
                        style="background: linear-gradient(to top, #000000cc, transparent); z-index: 1;"></div>

                    <div class="position-relative text-center p-5" style="z-index: 2;">
                        <h2 class="display-5 fw-bold mb-3">Drive Excellence.</h2>
                        <p class="lead text-light opacity-75">Streamlining operations for Chowdhury Automobile.</p>
                    </div>
                </div>

                <div class="col-lg-6 d-flex flex-column justify-content-center align-items-center bg-light">

                    <div class="card border-0 shadow-lg rounded-4 w-100" style="max-width: 500px;">
                        <div class="card-body p-4 p-sm-5">

                            <div class="text-center mb-4">
                                <div
                                    class="d-inline-block rounded-circle p-1 border border-3 border-light shadow-sm mb-3">
                                    <img src="../images/logo.jpeg" alt="Logo" class="rounded-circle"
                                        style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                                <h4 class="fw-bold text-dark mb-1">Chowdhury Automobile</h4>
                                <p class="text-muted small text-uppercase fw-semibold ls-1">Manager Portal Login</p>
                            </div>

                            <form id="loginForm" method="POST" action="auth.php">
                                <input type="hidden" name="action" value="login">

                                <div class="mb-3">
                                    <label class="form-label small text-muted fw-bold">Select Role</label>
                                    <select class="form-select py-2 bg-light border-0 fw-semibold" id="roleSelect"
                                        name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-muted">User ID</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                            <i class="ph-bold ph-user-circle fs-5"></i>
                                        </span>
                                        <input type="text" class="form-control bg-light border-start-0 ps-0"
                                            id="usernameInput" name="user_id" placeholder="Enter your ID" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-muted">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                            <i class="ph-bold ph-lock-key fs-5"></i>
                                        </span>
                                        <input type="password" class="form-control bg-light border-start-0 ps-0"
                                            id="passwordInput" name="password" placeholder="••••••••" required>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                                        <label class="form-check-label small text-muted" for="rememberMe">
                                            Remember me
                                        </label>
                                    </div>

                                    <a href="#" class="small text-decoration-none fw-bold text-primary"
                                        data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                                        Forgot password?
                                    </a>
                                </div>

                                <div class="d-flex justify-content-center mb-3"
                                    style="transform: scale(0.85); transform-origin: center;">
                                    <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-dark w-100 py-2 rounded-3 fw-bold shadow-sm">
                                    Sign In to Portal
                                </button>

                            </form>

                            <div class="mt-4 text-center">
                                <p class="text-muted small mb-2">Looking for the Staff Portal?</p>
                                <a href="../index.php" class="text-decoration-none fw-bold text-dark small">
                                    <i class="ph-bold ph-arrow-right me-1"></i> Access Staff Portal
                                </a>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4">

                <div class="modal-header border-bottom-0 pb-0">
                    <h6 class="modal-title fw-bold" id="modalTitle">Reset Password</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-2">

                    <form id="step1Form" action="auth.php" method="POST">
                        <input type="hidden" name="action" value="send_otp">

                        <div id="emailStep">
                            <p class="small text-muted mb-3">Enter your registered email address.</p>

                            <div class="mb-3">
                                <input type="email" id="resetEmail" name="email" class="form-control bg-light"
                                    placeholder="name@company.com" required>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-sm">Send OTP</button>
                            </div>

                            <div class="text-center">
                                <a href="#" onclick="toggleResetSteps()" class="small text-decoration-none text-muted">
                                    Already have a code? <span class="text-primary fw-bold">Enter it here</span>
                                </a>
                            </div>
                        </div>
                    </form>

                    <form id="step2Form" action="auth.php" method="POST" onsubmit="combineOtp()">
                        <input type="hidden" name="action" value="verify_otp">
                        <input type="hidden" name="email" id="hiddenEmail">

                        <div id="otpStep" class="d-none">
                            <p class="small text-muted mb-3">Enter the 6-digit OTP sent to your email.</p>

                            <div class="d-flex justify-content-between mb-3 gap-1">
                                <input type="text" maxlength="1" class="form-control text-center otp-box bg-light"
                                    oninput="moveToNext(this, 0)">
                                <input type="text" maxlength="1" class="form-control text-center otp-box bg-light"
                                    oninput="moveToNext(this, 1)">
                                <input type="text" maxlength="1" class="form-control text-center otp-box bg-light"
                                    oninput="moveToNext(this, 2)">
                                <input type="text" maxlength="1" class="form-control text-center otp-box bg-light"
                                    oninput="moveToNext(this, 3)">
                                <input type="text" maxlength="1" class="form-control text-center otp-box bg-light"
                                    oninput="moveToNext(this, 4)">
                                <input type="text" maxlength="1" class="form-control text-center otp-box bg-light"
                                    oninput="moveToNext(this, 5)">
                                <input type="hidden" id="fullOtp" name="otp">
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-dark btn-sm">Verify & Reset</button>
                            </div>

                            <div class="text-center">
                                <a href="#" onclick="toggleResetSteps()" class="small text-decoration-none text-muted">
                                    Wrong email? <span class="text-primary fw-bold">Go back</span>
                                </a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>





</body>

</html>