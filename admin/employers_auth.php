<?php
// ---------------------------------------------
// 1. SAFE SESSION START
// ---------------------------------------------

require_once 'db.php';

// Set default timezone to ensure strict 1-hour calculation
date_default_timezone_set('Asia/Kolkata'); 

/* =========================
   Allow only POST
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: employer.php"); 
    exit;
}

if (!isset($_POST['action']) || $_POST['action'] !== 'login') {
    header("Location: employer.php");
    exit;
}

/* =============================================
   2. RECAPTCHA VALIDATION
============================================= */
$recaptcha_secret = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

if (empty($recaptcha_response)) {
    $_SESSION['login_message'] = "Please verify that you are not a robot.";
    $_SESSION['login_message_type'] = "warning";
    header("Location: employer.php");
    exit;
}

$verify_url = "https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}";
$verify_response = file_get_contents($verify_url);
$response_data = json_decode($verify_response);

if (!$response_data->success) {
    $_SESSION['login_message'] = "Captcha verification failed. Please try again.";
    $_SESSION['login_message_type'] = "error";
    header("Location: employer.php");
    exit;
}

/* =========================
   Inputs
========================= */
$role     = trim($_POST['role'] ?? '');
$user_id  = trim($_POST['user_id'] ?? '');
$password = $_POST['password'] ?? '';

if ($role === '' || $user_id === '' || $password === '') {
    $_SESSION['login_message'] = "Please fill in all required fields.";
    $_SESSION['login_message_type'] = "warning";
    header("Location: employer.php");
    exit;
}

/* =========================
   Role → Table
========================= */
if ($role === 'manager') {
    $table = 'managers';
} elseif ($role === 'staff') {
    $table = 'staff';
} else {
    $_SESSION['login_message'] = "Invalid role selected.";
    $_SESSION['login_message_type'] = "error";
    header("Location: employer.php");
    exit;
}

/* =========================
   Fetch User & Lockout Info
========================= */
$sql = "SELECT id, user_id, full_name, role, password_hash, failed_attempts, lockout_until
        FROM $table
        WHERE user_id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    // Ambiguous message for security
    $_SESSION['login_message'] = "Invalid User ID or Password.";
    $_SESSION['login_message_type'] = "error";
    header("Location: employer.php");
    exit;
}

$user = $result->fetch_assoc();

/* =========================
   CHECK LOCKOUT STATUS
========================= */
// If lockout time exists AND is in the FUTURE
if ($user['lockout_until'] && strtotime($user['lockout_until']) > time()) {
    
    // Calculate minutes remaining
    $remaining = ceil((strtotime($user['lockout_until']) - time()) / 60);
    
    $_SESSION['login_message'] = "Account locked due to too many failed attempts. Try again in $remaining minutes.";
    $_SESSION['login_message_type'] = "danger"; 
    header("Location: employer.php");
    exit;
}

/* =========================
   Verify Password
========================= */
if (!password_verify($password, $user['password_hash'])) {
    
    // --- FAILURE LOGIC START ---
    
    // 1. Calculate new failed attempts
    $new_attempts = $user['failed_attempts'] + 1;
    $remaining_attempts = 5 - $new_attempts;

    // 2. Check if we reached the limit (5)
    if ($new_attempts >= 5) {
        // LOCK THE ACCOUNT: Set lockout time to NOW + 1 HOUR
        $update_sql = "UPDATE $table SET failed_attempts = ?, lockout_until = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?";
        $fail_msg = "Account locked for 1 hour due to 5 failed attempts.";
    } else {
        // JUST INCREMENT: Keep lockout_until NULL (or unchanged)
        $update_sql = "UPDATE $table SET failed_attempts = ? WHERE id = ?";
        $fail_msg = "Invalid User ID or Password. You have $remaining_attempts attempts remaining.";
    }

    // 3. Update the database
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $new_attempts, $user['id']);
    $update_stmt->execute();

    $_SESSION['login_message'] = $fail_msg;
    $_SESSION['login_message_type'] = "error";
    header("Location: employer.php");
    exit;
    // --- FAILURE LOGIC END ---
}

/* =========================
   3. LOGIN SUCCESS - RESET TIMERS
========================= */
// Since login succeeded, we reset attempts to 0 and clear lockout time
$reset_sql = "UPDATE $table 
              SET failed_attempts = 0, 
                  lockout_until = NULL 
              WHERE id = ?";

$reset_stmt = $conn->prepare($reset_sql);
$reset_stmt->bind_param("i", $user['id']);
$reset_stmt->execute();
$reset_stmt->close();

/* =========================
   Set Session Variables
========================= */
session_regenerate_id(true);

$_SESSION['auth']      = true;
$_SESSION['user_id']   = $user['user_id'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['role']      = $user['role'];
$_SESSION['table']     = $table;
$_SESSION['login_at']  = time();

$_SESSION['login_message'] = "Welcome back, {$user['full_name']}!";
$_SESSION['login_message_type'] = "success";

/* =========================
   Redirect
========================= */
header("Location: employers_dashboard.php");
exit;
?>