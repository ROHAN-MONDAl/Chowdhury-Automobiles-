<?php
// ========================= 1. SETUP =========================
session_start();
require "db.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$currentUserId = $_SESSION["user_id"];

// ========================= 2. HELPER FUNCTION =========================
function flash($msg, $type = "error")
{
    $_SESSION["update_msg"] = $msg;
    $_SESSION["update_type"] = $type;
    header("Location: dashboard.php");
    exit();
}

// ========================= 3. PROCESS FORM =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type'])) {

    if ($_POST['form_type'] === 'profile') {

        // --- A. GATHER INPUTS ---
        $role = strtolower(trim($_POST["role"] ?? ""));
        $email = strtolower(trim($_POST["email"] ?? ""));
        $new_user_id = trim($_POST["user_id"] ?? "");
        $pass = trim($_POST["password"] ?? "");
        $confirm = trim($_POST["confirmPassword"] ?? "");

        // --- B. BASIC VALIDATION ---
        if (!$role || !$email || !$new_user_id) {
            flash("All fields are required.");
        }

        $valid_roles = ["admin", "user", "manager"];
        if (!in_array($role, $valid_roles)) {
            flash("Invalid role selected.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash("Invalid email format.");
        }

        // --- C. UPDATE DATABASE ---

        // Path 1: User wants to change Password
        if (!empty($pass)) {

            // Password Validation
            if ($pass !== $confirm)
                flash("Passwords do not match.");

            // Regex: Upper, Lower, Number, Special Char, Min 8 length
            if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/", $pass)) {
                flash("Password must be 8+ chars with Uppercase, Lowercase, Number & Symbol.");
            }

            $hash = password_hash($pass, PASSWORD_DEFAULT);

            // Update Query WITH Password
            $sql = "UPDATE users SET email=?, role=?, user_id=?, password_hash=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $email, $role, $new_user_id, $hash, $currentUserId);

        }
        // Path 2: User keeps existing password
        else {
            // Update Query WITHOUT Password
            $sql = "UPDATE users SET email=?, role=?, user_id=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $email, $role, $new_user_id, $currentUserId);
        }

        // --- D. EXECUTE & FINISH ---
        // If email/user_id is duplicate, execute() will return false (if DB columns are UNIQUE)
        if ($stmt->execute()) {
            $_SESSION["user_id"] = $new_user_id; // Update session
            flash("Profile updated.", "success");
        } else {
            flash("Failed to update profile (Username or Email might already be taken).");
        }
    }
}

// ========================= 4. PROCESS LEADS FORM =========================

// Trim input values
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$bike_model = trim($_POST['bike_model'] ?? '');
$comment = trim($_POST['comment'] ?? ''); // New field for comment

// Validation
if (!$name || !$phone || !$bike_model) {
    flash("All fields are required.");
    header("Location: dashboard.php");
    exit;
}

if (!preg_match("/^[A-Za-z\s]{2,50}$/", $name)) {
    flash("Invalid name format.");
    header("Location: dashboard.php");
    exit;
}

if (!preg_match("/^\d{10}$/", $phone)) {
    flash("Phone must be 10 digits.");
    header("Location: dashboard.php");
    exit;
}

if (!preg_match("/^[A-Za-z0-9\s\-]{2,30}$/", $bike_model)) {
    flash("Invalid bike model format.");
    header("Location: dashboard.php");
    exit;
}

// Optional: validate comment length
if ($comment && strlen($comment) > 500) {
    flash("Comment is too long (max 500 characters).");
    header("Location: dashboard.php");
    exit;
}

// Check for duplicate lead based on bike model
$stmt_check = $conn->prepare("SELECT id FROM leads WHERE bike_model = ?");
$stmt_check->bind_param("s", $bike_model);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    flash("A lead with this bike model already exists.");
    header("Location: dashboard.php");
    exit;
}

// Insert into DB
$stmt = $conn->prepare("INSERT INTO leads (name, phone, bike_model) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $phone, $bike_model);

if ($stmt->execute()) {
    flash("Lead saved.", "success");
} else {
    flash("Failed to save lead.");
}

header("Location: dashboard.php");
exit;
?>