<?php
/* =======================================================================
 * SIMPLE AUTH SYSTEM (Login + Forgot Password + Account Locking)
 * Database Aligned Version
 * =======================================================================
 */

// 1. START SESSION
session_start();
date_default_timezone_set('Asia/Kolkata');

// 2. SECURITY CHECK
// Block direct access to this file. Only allow POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

/* =======================================================================
 * SECTION A: LOAD LIBRARIES & DATABASE
 * ======================================================================= */

// Load PHPMailer
require __DIR__ . "/PHPMailer-master/src/PHPMailer.php";
require __DIR__ . "/PHPMailer-master/src/SMTP.php";
require __DIR__ . "/PHPMailer-master/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database Configuration
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "chowdhury_automobile";
$port = 3306;

// Connect to Database
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_errno) {
    die("Database Connection Failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

/* =======================================================================
 * SECTION B: HELPER TOOLS (Functions)
 * ======================================================================= */

function getUserByID($conn, $id)
{
    // Looks up user by their Login ID (varchar 'user_id' column)
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id=? LIMIT 1");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getUserByEmail($conn, $email)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function randomCode($len = 6)
{
    return str_pad(random_int(0, 999999), $len, "0", STR_PAD_LEFT);
}

function secureToken($len = 64)
{
    return bin2hex(random_bytes($len / 2));
}

function sendMail($to, $subject, $body)
{
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "mindtraveller775@gmail.com";
        $mail->Password = "vtnh obqg akzm hoxo";
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;

        $mail->setFrom("mindtraveller775@gmail.com", "Chowdhury Automobile");
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}

$action = $_REQUEST["action"] ?? "";

/* =======================================================================
 * SECTION C: LOGIN LOGIC
 * ======================================================================= */
if ($action === 'login') {

    // 1. GET DATA
    $response = $_POST["g-recaptcha-response"] ?? "";

    // --- CAPTCHA CHECKS ---
    if (empty($response)) {
        $_SESSION['login_message'] = "Please click the CAPTCHA box.";
        $_SESSION['login_message_type'] = "warning";
        header("Location: index.php");
        exit();
    }

    $secret = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";
    $verify = @file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response");
    $captcha = json_decode($verify, true);

    if (empty($captcha['success']) || $captcha['success'] === false) {
        $_SESSION['login_message'] = "CAPTCHA verification failed.";
        $_SESSION['login_message_type'] = "error";
        header("Location: index.php");
        exit();
    }

    // --- INPUT CHECKS ---
    $role = $_POST["role"] ?? "";
    $id = trim($_POST["user_id"] ?? "");
    $pass = $_POST["password"] ?? "";

    // 2. CHECK IF USER EXISTS
    $user = getUserByID($conn, $id);

    if (!$user) {
        $_SESSION['login_message'] = "Incorrect User ID or Password";
        $_SESSION['login_message_type'] = "error";
        header("Location: index.php");
        exit();
    }

    // 3. CHECK LOCKOUT STATUS (Column: locked_until)
    $current_time = date('Y-m-d H:i:s');

    if ($user['locked_until'] && $current_time < $user['locked_until']) {
        $diff = strtotime($user['locked_until']) - time();
        $minutes_left = ceil($diff / 60);

        $_SESSION['login_message'] = "Account locked due to too many failed attempts. Try again in $minutes_left minute(s).";
        $_SESSION['login_message_type'] = "error";
        header("Location: index.php");
        exit();
    }

    // 4. VERIFY PASSWORD
    if (!password_verify($pass, $user["password_hash"])) {

        // --- PASSWORD FAILED: HANDLE COUNTERS ---
        $attempts = $user['failed_attempts'] + 1; // Column: failed_attempts

        if ($attempts >= 5) {
            // Lock for 2 minutes
            $lockout_time = date('Y-m-d H:i:s', strtotime('+2 minutes'));

            $stmt = $conn->prepare("UPDATE users SET failed_attempts=0, locked_until=? WHERE user_id=?");
            $stmt->bind_param("ss", $lockout_time, $id);
            $stmt->execute();

            $_SESSION['login_message'] = "Maximum attempts reached. Account locked for 2 minutes.";
            $_SESSION['login_message_type'] = "error";
        } else {
            // Just count the failure
            $remaining = 5 - $attempts;

            $stmt = $conn->prepare("UPDATE users SET failed_attempts=? WHERE user_id=?");
            $stmt->bind_param("is", $attempts, $id);
            $stmt->execute();

            $_SESSION['login_message'] = "Incorrect Password. You have $remaining attempt(s) left.";
            $_SESSION['login_message_type'] = "warning";
        }

        header("Location: index.php");
        exit();
    }

    // 5. SUCCESSFUL LOGIN (PASSWORD CORRECT)

    // Check Role
    if ($user["role"] !== $role) {
        $_SESSION['login_message'] = "Access denied for this role.";
        $_SESSION['login_message_type'] = "error";
        header("Location: index.php");
        exit();
    }

    // Reset Lockout Counters (User proved identity, so we forgive past mistakes)
    $stmt = $conn->prepare("UPDATE users SET failed_attempts=0, locked_until=NULL WHERE user_id=?");
    $stmt->bind_param("s", $user["user_id"]);
    $stmt->execute();

    // Set Session
    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["role"] = $user["role"];
    $_SESSION["id"] = $user["id"]; // The auto-increment ID

    // Remember Me
    if (isset($_POST["remember"])) {
        $token = bin2hex(random_bytes(32));
        $hash = password_hash($token, PASSWORD_DEFAULT);

        // Column: remember_token_hash
        $stmt = $conn->prepare("UPDATE users SET remember_token_hash=? WHERE user_id=?");
        $stmt->bind_param("ss", $hash, $id);
        $stmt->execute();

        setcookie("remember_me", "$id:$token", time() + (86400 * 30), "/", "", true, true);
    }

    header("Location: dashboard.php");
    exit();
}

/* =======================================================================
 * SECTION D: FORGOT PASSWORD LOGIC
 * ======================================================================= */

if ($action === "send_otp") {

    $email = strtolower(trim($_POST["email"] ?? ""));
    $user = getUserByEmail($conn, $email);

    if ($user) {
        $otp_code = randomCode(6);
        $expires = date("Y-m-d H:i:s", time() + 60); // 60 Seconds

        // Columns: otp_code, otp_expires
        $stmt = $conn->prepare("UPDATE users SET otp_code=?, otp_expires=? WHERE user_id=?");
        $stmt->bind_param("sss", $otp_code, $expires, $user["user_id"]);
        $stmt->execute();

        sendMail($email, "Password Reset", "Your OTP Code is: <b>$otp_code</b>");

        $_SESSION['login_message'] = "OTP sent! It will expire in 60 seconds.";
        $_SESSION['login_message_type'] = "success";
    } else {
        $_SESSION['login_message'] = "If this email is registered, we sent an OTP.";
        $_SESSION['login_message_type'] = "info";
    }

    header("Location: index.php");
    exit();

} elseif ($action === "verify_otp") {

    $email = strtolower(trim($_POST["email"] ?? ""));
    $otp = trim($_POST["otp"] ?? "");
    $user = getUserByEmail($conn, $email);

    if (!$user) {
        $_SESSION['login_message'] = "User not found or invalid email.";
        $_SESSION['login_message_type'] = "error";
        header("Location: index.php");
        exit();
    }

    // OTP Verification
    $dbOtp = trim($user["otp_code"] ?? "");
    $expiresAt = $user["otp_expires"] ?? "";
    $expiresTime = strtotime($expiresAt);

    if ($dbOtp === $otp && $expiresTime > time()) {

        // Reset Password
        $tempPass = substr(secureToken(12), 0, 10);
        $hash = password_hash($tempPass, PASSWORD_DEFAULT);

        // Reset OTP fields to NULL
        $stmt = $conn->prepare("UPDATE users SET password_hash=?, otp_code=NULL, otp_expires=NULL WHERE user_id=?");
        $stmt->bind_param("ss", $hash, $user["user_id"]);
        $stmt->execute();

        sendMail($email, "New Password", "Your temporary password is: <b>$tempPass</b><br>Please login and change it.");

        $_SESSION['login_message'] = "Success! Check your email for your new password.";
        $_SESSION['login_message_type'] = "success";
    } else {
        $_SESSION['login_message'] = "Invalid or expired OTP. Please try again.";
        $_SESSION['login_message_type'] = "error";
    }

    header("Location: index.php");
    exit();
}

// Fallback
header("Location: index.php");
exit();
?>