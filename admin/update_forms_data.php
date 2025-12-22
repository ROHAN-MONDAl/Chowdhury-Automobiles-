<?php
// ========================= 1. SETUP =========================
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

    // ==========================================
    // 1. EDIT PROFILE (Existing Logic)
    // ==========================================
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

        $valid_roles = ["admin"];
        if (!in_array($role, $valid_roles)) {
            flash("Invalid role selected.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash("Invalid email format.");
        }

        // --- C. UPDATE DATABASE ---
        if (!empty($pass)) {
            if ($pass !== $confirm) flash("Passwords do not match.");
            if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/", $pass)) {
                flash("Password must be 8+ chars with Uppercase, Lowercase, Number & Symbol.");
            }
            $hash = password_hash($pass, PASSWORD_DEFAULT);

            $sql = "UPDATE users SET email=?, role=?, user_id=?, password_hash=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $email, $role, $new_user_id, $hash, $currentUserId);
        } else {
            $sql = "UPDATE users SET email=?, role=?, user_id=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $email, $role, $new_user_id, $currentUserId);
        }

        if ($stmt->execute()) {
            $_SESSION["user_id"] = $new_user_id;
            flash("Profile updated.", "success");
        } else {
            flash("Failed to update profile (Username or Email might already be taken).");
        }
    }

    // ==========================================
    // 2. CREATE NEW USER (New Logic)
    // ==========================================
    elseif ($_POST['form_type'] === 'create_user') {

        // --- 1. GATHER DATA ---
        $role        = strtolower(trim($_POST["role"] ?? ""));
        $full_name   = trim($_POST["full_name"] ?? "");
        $new_user_id = trim($_POST["user_id"] ?? "");
        $pass        = trim($_POST["password"] ?? "");

        // --- 2. VALIDATE INPUTS ---
        if (empty($role) || empty($full_name) || empty($new_user_id) || empty($pass)) {
            flash("All fields are required.");
            return;
        }

        // === NEW PASSWORD VALIDATIONS START ===

        // Check 1: Minimum Length (8 characters)
        if (strlen($pass) < 8) {
            flash("Password must be at least 8 characters long.");
            return;
        }

        // Check 2: Complexity (Upper, Lower, Number, Special Char)
        // Regex explanation:
        // (?=.*[A-Z]) -> At least one Uppercase
        // (?=.*[a-z]) -> At least one Lowercase
        // (?=.*\d)    -> At least one Number
        // (?=.*[\W_]) -> At least one Special Character (Symbol)
        if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).+$/", $pass)) {
            flash("Password must contain at least one uppercase letter, one lowercase letter, one number, and one symbol.");
            return;
        }

        // === NEW PASSWORD VALIDATIONS END ===

        $hash = password_hash($pass, PASSWORD_DEFAULT);

        // --- 3. MANAGER LOGIC ---
        if ($role === 'manager') {

            // Check for duplicates
            $checkSql = "SELECT id FROM managers WHERE user_id = ?";
            $stmt = $conn->prepare($checkSql);
            $stmt->bind_param("s", $new_user_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                flash("Manager ID '$new_user_id' already exists.");
                return;
            }
            $stmt->close();

            // INSERT into managers
            $sql = "INSERT INTO managers (user_id, full_name, role, password_hash) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt && $stmt->bind_param("ssss", $new_user_id, $full_name, $role, $hash)) {
                if ($stmt->execute()) {
                    flash("Manager account created successfully!", "success");
                } else {
                    flash("Error creating manager: " . $conn->error);
                }
            }
        }
        // --- 4. STAFF LOGIC ---
        elseif ($role === 'staff') {

            // Check for duplicates
            $checkSql = "SELECT id FROM staff WHERE user_id = ?";
            $stmt = $conn->prepare($checkSql);
            $stmt->bind_param("s", $new_user_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                flash("Staff ID '$new_user_id' already exists.");
                return;
            }
            $stmt->close();

            // INSERT into staff
            $sql = "INSERT INTO staff (user_id, full_name, role, password_hash) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt && $stmt->bind_param("ssss", $new_user_id, $full_name, $role, $hash)) {
                if ($stmt->execute()) {
                    flash("Staff account created successfully!", "success");
                } else {
                    flash("Error creating staff: " . $conn->error);
                }
            }
        } else {
            flash("Invalid Role selected.");
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
