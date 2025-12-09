<?php
/* * =======================================================================
 * SIMPLE AUTH SYSTEM (Login + Forgot Password)
 * Beginner-Friendly Version
 * =======================================================================
 */

// 1. START SESSION
// This allows the server to "remember" the user across different pages.
session_start();
date_default_timezone_set('Asia/Kolkata'); // <--- CHANGE THIS to your timezone (e.g., 'America/New_York')

// 2. SECURITY CHECK
// If someone tries to open this file directly in the browser (GET request),
// kick them back to the login page. We only want form submissions (POST).
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

/* =======================================================================
 * SECTION A: LOAD LIBRARIES & DATABASE
 * ======================================================================= */

// Load PHPMailer (The tool used to send emails)
require __DIR__ . "/PHPMailer-master/src/PHPMailer.php";
require __DIR__ . "/PHPMailer-master/src/SMTP.php";
require __DIR__ . "/PHPMailer-master/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database Configuration
// CHANGE THESE to match your live server when you deploy!
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "chowdhury_automobile";
$port = 3306;

// Connect to the Database
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check if connection failed
if ($conn->connect_errno) {
    die("Database Connection Failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4"); // Support special characters

/* =======================================================================
 * SECTION B: HELPER TOOLS (Functions)
 * ======================================================================= */

// Tool 1: Find a user by their ID
// We use 'prepare' and 'bind_param' to prevent hackers from injecting SQL codes.
function getUserByID($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id=? LIMIT 1");
    $stmt->bind_param("s", $id); // 's' means the id is a String
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Tool 2: Find a user by Email
function getUserByEmail($conn, $email)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Tool 3: Create a random 6-digit number (for OTP)
function randomCode($len = 6)
{
    return str_pad(random_int(0, 999999), $len, "0", STR_PAD_LEFT);
}

// Tool 4: Create a long random string (for security tokens)
function secureToken($len = 64)
{
    return bin2hex(random_bytes($len / 2));
}

// Tool 5: Send an Email
function sendMail($to, $subject, $body)
{
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "mindtraveller775@gmail.com";      // <--- PUT YOUR GMAIL HERE
        $mail->Password = "vtnh obqg akzm hoxo";   // <--- PUT YOUR APP PASSWORD HERE
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;

        $mail->setFrom("mindtraveller775@gmail.com", "Chowdhury Automobile"); // <--- PUT YOUR GMAIL HERE
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        return $mail->send();
    } catch (Exception $e) {
        // If mail fails, just return false (don't crash the script)
        return false;
    }
}

// Get the specific "action" from the URL (e.g., auth.php?action=login)
// OR from a hidden input field if you added one.
$action = $_REQUEST["action"] ?? "";

/* =======================================================================
 * SECTION C: LOGIN LOGIC
 * ======================================================================= */
if ($action === 'login') {

    // 1. GET DATA
    $response = $_POST["g-recaptcha-response"] ?? "";

    // ============================================================
    // THE GATEKEEPER: STRICT CAPTCHA CHECK
    // ============================================================

    // Step A: Did the user even click the box?
    if (empty($response)) {
        $_SESSION['login_message'] = "Please click the CAPTCHA box.";
        $_SESSION['login_message_type'] = "warning";
        header("Location: index.php");
        exit(); // <--- STOP HERE. DO NOT CONTINUE.
    }

    // Step B: Ask Google if the token is valid
    $secret = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response";

    $verify = @file_get_contents($url);
    $captcha = json_decode($verify, true);

    // Step C: Did Google say "No"?
    if (empty($captcha['success']) || $captcha['success'] === false) {
        $_SESSION['login_message'] = "CAPTCHA verification failed.";
        $_SESSION['login_message_type'] = "error";
        header("Location: index.php");
        exit(); // <--- STOP HERE. DO NOT CONTINUE.
    }

    // ============================================================
    // ONLY IF CAPTCHA PASSED, WE CONTINUE TO DATABASE
    // ============================================================

    $role = $_POST["role"] ?? "";
    $id = trim($_POST["user_id"] ?? "");
    $pass = $_POST["password"] ?? "";

    $user = getUserByID($conn, $id);

    // Check User & Password
    if (!$user || !password_verify($pass, $user["password_hash"])) {
        $_SESSION['login_message'] = "Incorrect User ID or Password";
        $_SESSION['login_message_type'] = "error";
        header("Location: index.php");
        exit();
    }

    // Check Role
    if ($user["role"] !== $role) {
        $_SESSION['login_message'] = "Access denied for this role.";
        $_SESSION['login_message_type'] = "error";
        header("Location: index.php");
        exit();
    }

    // --- SUCCESS ---
    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["role"] = $user["role"];
    $_SESSION["id"] = $user["id"];

    // Remember Me
    if (isset($_POST["remember"])) {
        $token = bin2hex(random_bytes(32));
        $hash = password_hash($token, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET remember_token_hash=? WHERE user_id=?");
        $stmt->bind_param("ss", $hash, $id);
        $stmt->execute();

        setcookie("remember_me", "$id:$token", time() + (86400 * 30), "/", "", true, true);
    }

    header("Location: dashboard.php");
    exit();
}



// [Image of login authentication process flow chart]


/* =======================================================================
 * SECTION D: FORGOT PASSWORD LOGIC
 * ======================================================================= */

if ($action === "send_otp") {

    // 1. SANITIZE INPUT
    $email = strtolower(trim($_POST["email"] ?? ""));

    // 2. CHECK DATABASE
    $user = getUserByEmail($conn, $email);

    if ($user) {
        // --- USER FOUND ---

        // A. Generate a random 6-digit OTP
        $otp_code = randomCode(6);

        // B. Set expiration time (60 seconds from now)
        $expires = date("Y-m-d H:i:s", time() + 60);

        // C. Save code to database
        $stmt = $conn->prepare("UPDATE users SET otp_code=?, otp_expires=? WHERE user_id=?");
        $stmt->bind_param("sss", $otp_code, $expires, $user["user_id"]);
        $stmt->execute();

        // D. Send email
        sendMail($email, "Password Reset", "Your OTP Code is: <b>$otp_code</b>");

        $_SESSION['login_message'] = "OTP sent! It will expire in 60 seconds.";
        $_SESSION['login_message_type'] = "success";

    } else {
        // --- USER NOT FOUND ---
        $_SESSION['login_message'] = "If this email is registered, we sent an OTP.";
        $_SESSION['login_message_type'] = "info";
    }

    header("Location: index.php");
    exit();

} elseif ($action === "verify_otp") {

    // 1. SANITIZE INPUT
    $email = strtolower(trim($_POST["email"] ?? ""));
    $otp = trim($_POST["otp"] ?? "");

    // 2. FIND USER
    $user = getUserByEmail($conn, $email);

    // 3. VERIFY USER
    if (!$user) {
        $_SESSION['login_message'] = "User not found or invalid email.";
        $_SESSION['login_message_type'] = "error";
        header("Location: index.php");
        exit();
    }

    // 4. DEBUG INFO (optional)
    $_SESSION['debug_info'] = "Current time: " . date("Y-m-d H:i:s") .
        "<br>OTP expires: " . $user["otp_expires"] .
        "<br>Entered OTP: $otp<br>Stored OTP: " . $user["otp_code"];

    $dbOtp = trim($user["otp_code"] ?? "");
    $expiresAt = $user["otp_expires"] ?? "";
    $expiresTime = strtotime($expiresAt);

    // 5. VERIFY OTP & EXPIRATION
    if ($dbOtp === $otp && $expiresTime > time()) {

        // --- SUCCESS ---
        $tempPass = substr(secureToken(12), 0, 10);
        $hash = password_hash($tempPass, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password_hash=?, otp_code=NULL, otp_expires=NULL WHERE user_id=?");
        $stmt->bind_param("ss", $hash, $user["user_id"]);
        $stmt->execute();

        sendMail($email, "New Password", "Your temporary password is: <b>$tempPass</b><br>Please login and change it.");

        $_SESSION['login_message'] = "Success! Check your email for your new password.";
        $_SESSION['login_message_type'] = "success";

    } else {
        // --- FAILURE ---
        $_SESSION['login_message'] = "Invalid or expired OTP. Please try again.";
        $_SESSION['login_message_type'] = "error";
    }

    // 6. REDIRECT
    header("Location: index.php");
    exit();
}




// [Image of password reset flow chart]


/* =======================================================================
 * SECTION E: CATCH-ALL (BLANK SCREEN FIX)
 * ======================================================================= */
// If the code reaches here, it means no action matched.
// We redirect to index.php to prevent a white screen.
header("Location: index.php");
exit();
?>