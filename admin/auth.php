<?php
/* =======================================================================
 * SIMPLE AUTH SYSTEM (Login + Forgot Password + Account Locking)
 * Database Aligned Version
 *
 * This file handles authentication-related operations for the Chowdhury Automobile admin panel.
 * It includes login functionality with CAPTCHA verification, account lockout after failed attempts,
 * forgot password with OTP via email, and secure session management.
 * All operations are secured against direct access and use prepared statements for database queries.
 * =======================================================================
 */

// 1. START SESSION
// Initializes a PHP session to store user data across requests.
// This is essential for maintaining login state and passing messages between pages.
require "db.php"; // Database connection

// Set the default timezone to ensure consistent date/time handling across the application.
// 'Asia/Kolkata' is used here, which is IST (Indian Standard Time).
date_default_timezone_set('Asia/Kolkata');

// 2. SECURITY CHECK
// Prevents direct access to this script via URL or GET requests.
// This file is intended only for handling POST requests from forms (e.g., login or forgot password).
// If accessed directly, redirects to the login page (index.php) to avoid unauthorized execution.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

/* =======================================================================
 * SECTION A: LOAD LIBRARIES & DATABASE
 *
 * This section loads necessary libraries and establishes a secure database connection.
 * PHPMailer is used for sending emails (e.g., OTP for password reset).
 * The database connection uses MySQLi with prepared statements for security against SQL injection.
 * ======================================================================= */

// Load PHPMailer classes for email functionality.
// PHPMailer is a library for sending emails via SMTP, used here for password reset OTPs.
require __DIR__ . "/PHPMailer-master/src/PHPMailer.php";
require __DIR__ . "/PHPMailer-master/src/SMTP.php";
require __DIR__ . "/PHPMailer-master/src/Exception.php";

// Import PHPMailer classes into the current namespace for easier use.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



/* =======================================================================
 * SECTION B: HELPER TOOLS (Functions)
 *
 * This section defines utility functions used throughout the authentication process.
 * These functions handle user lookups, code generation, token creation, and email sending.
 * All functions use secure practices like prepared statements and cryptographically secure random generation.
 * ======================================================================= */

// Retrieves user data from the database using their unique user ID.
// This function is used during login to fetch user details for authentication.
// Parameters: $conn (database connection), $id (user's login ID).
// Returns: Associative array of user data or null if not found.
function getUserByID($conn, $id)
{
    // Looks up user by their Login ID (varchar 'user_id' column)
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id=? LIMIT 1");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Retrieves user data from the database using their email address.
// This function is used during password reset to verify email ownership.
// Parameters: $conn (database connection), $email (user's email).
// Returns: Associative array of user data or null if not found.
function getUserByEmail($conn, $email)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Generates a random numeric code of specified length, padded with leading zeros.
// Used for creating OTP codes for password reset.
// Parameters: $len (length of the code, default 6).
// Returns: String representation of the random code.
function randomCode($len = 6)
{
    return str_pad(random_int(0, 999999), $len, "0", STR_PAD_LEFT);
}

// Generates a secure random token using cryptographically secure bytes.
// Used for creating remember-me tokens and temporary passwords.
// Parameters: $len (length of the token in characters, default 64).
// Returns: Hexadecimal string of the random token.
function secureToken($len = 64)
{
    return bin2hex(random_bytes($len / 2));
}

// Sends an email using PHPMailer via Gmail SMTP.
// This function is used to send OTP codes and new passwords during password reset.
// Parameters: $to (recipient email), $subject (email subject), $body (HTML email body).
// Returns: Boolean indicating success or failure of email sending.
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
 *
 * This section handles the login process, including CAPTCHA verification,
 * user authentication, account lockout management, and session setup.
 * It ensures security by checking multiple factors before allowing access.
 * ======================================================================= */
if ($action === 'login') {

    // 1. GET DATA
    // Retrieve the CAPTCHA response from the POST data.
    // This is required to prevent automated login attempts.
    $response = $_POST["g-recaptcha-response"] ?? "";

    // --- CAPTCHA CHECKS ---
    // Ensure the CAPTCHA response is not empty.
    // If empty, prompt the user to complete the CAPTCHA.
    if (empty($response)) {
        $_SESSION['login_message'] = "Please click the CAPTCHA box.";
        $_SESSION['login_message_type'] = "warning";
        header("Location: index.php");
        exit();
    }

    // Verify the CAPTCHA response with Google's reCAPTCHA API.
    // Uses a secret key to authenticate the request.
    $secret = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";
    $verify = @file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response");
    $captcha = json_decode($verify, true);

    // If CAPTCHA verification fails, display an error and redirect.
    if (empty($captcha['success']) || $captcha['success'] === false) {
        $_SESSION['login_message'] = "CAPTCHA verification failed.";
        $_SESSION['login_message_type'] = "error";
        header("Location: index.php");
        exit();
    }

    // --- INPUT CHECKS ---
    // Retrieve and sanitize user input: role, user ID, and password.
    // Role determines access level (e.g., admin, user).
    $role = $_POST["role"] ?? "";
    $id = trim($_POST["user_id"] ?? "");
    $pass = $_POST["password"] ?? "";

    // 2. CHECK IF USER EXISTS
    // Fetch user data from the database using the provided user ID.
    // If no user is found, display a generic error to avoid revealing valid IDs.
    $user = getUserByID($conn, $id);

    if (!$user) {
        $_SESSION['login_message'] = "Incorrect User ID or Password";
        $_SESSION['login_message_type'] = "error";
        header("Location: index.php");
        exit();
    }

    // 3. CHECK LOCKOUT STATUS (Column: locked_until)
    // Check if the account is currently locked due to previous failed attempts.
    // If locked, calculate remaining time and inform the user.
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
    // Compare the provided password with the stored hash using password_verify.
    // If incorrect, handle failed attempt counters and potential lockout.
    if (!password_verify($pass, $user["password_hash"])) {

        // --- FIX: Ensure attempts is treated as an integer ---
        $current_attempts = (int) $user['failed_attempts'];
        $attempts = $current_attempts + 1;
        $remaining = 5 - $attempts;

        // --- SECURITY PROTOCOL: CHECK ATTEMPTS ---
        if ($attempts >= 5) {
            // ======================================================
            // ACTION: PERMANENT LOCK & AUTO-RESET
            // ======================================================

            // 1. Generate new secure password (invalidating the old one)
            $new_temp_pass = substr(secureToken(12), 0, 10);
            $new_hash = password_hash($new_temp_pass, PASSWORD_DEFAULT);

            // 2. Update Database (Reset attempts, remove lock timer, set NEW password)
            // CRITICAL FIX: Use $user['user_id'] (from DB) not $id (from Input)
            $stmt = $conn->prepare("UPDATE users SET failed_attempts=0, locked_until=NULL, password_hash=? WHERE user_id=?");
            $stmt->bind_param("ss", $new_hash, $user['user_id']);

            if ($stmt->execute()) {
                // 3. Send Security Alert Email
                $email = $user['email'];
                $subject = "URGENT: Suspicious Login Activity";
                $body = "Your account was locked due to 5 failed login attempts. <br>Your new temporary password is: <b>$new_temp_pass</b>";
                sendMail($email, $subject, $body);

                // 4. Set Session Message
                $_SESSION['login_message'] = "SECURITY ALERT: Too many failed attempts. Your account has been locked and a new password sent to your email.";
                $_SESSION['login_message_type'] = "error";
            } else {
                $_SESSION['login_message'] = "System Error: Could not lock account.";
                $_SESSION['login_message_type'] = "error";
            }

        } else {
            // ======================================================
            // ACTION: INCREMENT COUNTER
            // ======================================================

            // Update the failed attempts counter
            // CRITICAL FIX: Use $user['user_id'] to ensure the row is found
            $stmt = $conn->prepare("UPDATE users SET failed_attempts=? WHERE user_id=?");
            $stmt->bind_param("is", $attempts, $user['user_id']);
            $stmt->execute();

            // Set Session Message
            $_SESSION['login_message'] = "Incorrect Password. Warning: You have $remaining attempt(s) left before permanent lockout.";
            $_SESSION['login_message_type'] = "warning";
        }

        header("Location: index.php");
        exit();
    }

    // 5. SUCCESSFUL LOGIN (PASSWORD CORRECT)

    // Check Role
    // Ensure the user's role matches the selected role during login.
    // This prevents unauthorized access to role-specific areas.
    if ($user["role"] !== $role) {
        $_SESSION['login_message'] = "Access denied for this role.";
        $_SESSION['login_message_type'] = "error";
        header("Location: index.php");
        exit();
    }

    // Reset Lockout Counters (User proved identity, so we forgive past mistakes)
    // Clear failed attempts and lockout status since authentication succeeded.
    $stmt = $conn->prepare("UPDATE users SET failed_attempts=0, locked_until=NULL WHERE user_id=?");
    $stmt->bind_param("s", $user["user_id"]);
    $stmt->execute();

    // Set Session
    // Store user information in the session for authenticated access.
    // Includes user ID, role, and database auto-increment ID.
    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["role"] = $user["role"];
    $_SESSION["id"] = $user["id"]; // The auto-increment ID

    // Remember Me
    // If the user checked "Remember Me", create a secure token for persistent login.
    // Store a hashed token in the database and set a cookie for future sessions.
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
 *
 * This section handles the forgot password process using OTP (One-Time Password) sent via email.
 * It includes sending OTP to the user's email and verifying the OTP to reset the password.
 * Security measures include OTP expiration and clearing OTP data after use.
 * ======================================================================= */

if ($action === "send_otp") {
    // Handle the request to send an OTP for password reset.
    // Retrieves the email from POST data, normalizes it, and checks if a user exists with that email.

    $email = strtolower(trim($_POST["email"] ?? ""));
    $user = getUserByEmail($conn, $email);

    if ($user) {
        // If user exists, generate a 6-digit OTP code and set expiration to 60 seconds from now.
        $otp_code = randomCode(6);
        $expires = date("Y-m-d H:i:s", time() + 60); // 60 Seconds

        // Store the OTP code and expiration time in the database.
        // Columns: otp_code, otp_expires
        $stmt = $conn->prepare("UPDATE users SET otp_code=?, otp_expires=? WHERE user_id=?");
        $stmt->bind_param("sss", $otp_code, $expires, $user["user_id"]);
        $stmt->execute();

        // Send the OTP via email to the user.
        sendMail($email, "Password Reset", "Your OTP Code is: <b>$otp_code</b>");

        // Set a success message indicating OTP was sent.
        $_SESSION['login_message'] = "OTP sent! It will expire in 60 seconds.";
        $_SESSION['login_message_type'] = "success";
    } else {
        // If no user found, provide a generic message to avoid revealing email existence.
        $_SESSION['login_message'] = "If this email is registered, we sent an OTP.";
        $_SESSION['login_message_type'] = "info";
    }

    // Redirect back to the login page after processing.
    header("Location: index.php");
    exit();

} elseif ($action === "verify_otp") {
    // Handle the OTP verification for password reset.
    // Retrieves email and OTP from POST data, verifies the user, and checks OTP validity.

    $email = strtolower(trim($_POST["email"] ?? ""));
    $otp = trim($_POST["otp"] ?? "");
    $user = getUserByEmail($conn, $email);

    if (!$user) {
        // If user not found, display an error.
        $_SESSION['login_message'] = "User not found or invalid email.";
        $_SESSION['login_message_type'] = "error";
        header("Location: index.php");
        exit();
    }

    // OTP Verification
    // Retrieve stored OTP and expiration from the database.
    $dbOtp = trim($user["otp_code"] ?? "");
    $expiresAt = $user["otp_expires"] ?? "";
    $expiresTime = strtotime($expiresAt);

    if ($dbOtp === $otp && $expiresTime > time()) {
        // If OTP matches and hasn't expired, proceed to reset the password.

        // Generate a temporary password and hash it.
        $tempPass = substr(secureToken(12), 0, 10);
        $hash = password_hash($tempPass, PASSWORD_DEFAULT);

        // Update the user's password and clear OTP fields to prevent reuse.
        $stmt = $conn->prepare("UPDATE users SET password_hash=?, otp_code=NULL, otp_expires=NULL WHERE user_id=?");
        $stmt->bind_param("ss", $hash, $user["user_id"]);
        $stmt->execute();

        // Send the new temporary password via email.
        sendMail($email, "New Password", "Your temporary password is: <b>$tempPass</b><br>Please login and change it.");

        // Set a success message.
        $_SESSION['login_message'] = "Success! Check your email for your new password.";
        $_SESSION['login_message_type'] = "success";
    } else {
        // If OTP is invalid or expired, display an error.
        $_SESSION['login_message'] = "Invalid or expired OTP. Please try again.";
        $_SESSION['login_message_type'] = "error";
    }

    // Redirect back to the login page.
    header("Location: index.php");
    exit();
}

// Fallback
// If no valid action is provided, redirect to the login page as a safety measure.
header("Location: index.php");
exit();
?>