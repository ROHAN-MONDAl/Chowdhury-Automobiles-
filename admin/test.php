<?php
/* ================================================================
   SIMPLE AUTH (Login + Create User)
   SINGLE FILE · BEGINNER FRIENDLY
================================================================ */

// START SESSION
session_start();

// DATABASE CONNECTION
$servername = "127.0.0.1";
$username   = "root";
$password   = "";
$dbname     = "chowdhury_automobile";
$port       = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_errno) {
    die("Database Failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// ---------------------------
// FUNCTION: Get user by ID
// ---------------------------
function getUserByID($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id=? LIMIT 1");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// ---------------------------
// FORM SUBMISSION HANDLING
// ---------------------------
$message = "";
$type = "info";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";

    /* =====================================================
       ACTION 1: CREATE NEW USER
    ===================================================== */
    if ($action === "create_user") {

        $userid   = trim($_POST["user_id"]);
        $email    = trim($_POST["email"]);
        $role     = $_POST["role"];
        $password = $_POST["password"];

        // hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO users (user_id, email, role, password_hash, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("ssss", $userid, $email, $role, $password_hash);

        if ($stmt->execute()) {
            $message = "User created successfully!";
            $type = "success";
        } else {
            $message = "Error: " . $conn->error;
            $type = "error";
        }
    }

    /* =====================================================
       ACTION 2: LOGIN USER
    ===================================================== */
    if ($action === "login") {

        $role     = $_POST["role"] ?? "";
        $userid   = trim($_POST["user_id"]);
        $password = $_POST["password"];

        $user = getUserByID($conn, $userid);

        if (!$user) {
            $message = "User ID not found!";
            $type = "error";
        }
        else if ($user["role"] !== $role) {
            $message = "This user cannot login as $role!";
            $type = "error";
        }
        else if (!password_verify($password, $user["password_hash"])) {
            $message = "Incorrect password!";
            $type = "error";
        }
        else {
            // LOGIN SUCCESS
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["role"]    = $user["role"];
            $_SESSION["id"]      = $user["id"];

            // redirect to dashboard
            header("Location: dashboard.php");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Simple Auth - Single File</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial; background:#f2f2f2; padding:20px; }
        .box { background:#fff; padding:20px; width:350px; margin:auto; border-radius:8px; }
        .msg-success { color:green; }
        .msg-error { color:red; }
        input, select { width:100%; padding:10px; margin-top:8px; }
        button { width:100%; padding:10px; margin-top:12px; cursor:pointer; }
        h3 { margin-bottom:5px; }
        hr { margin:20px 0; }
    </style>
</head>

<body>

<div class="box">

    <?php if ($message): ?>
        <p class="msg-<?php echo $type; ?>"><strong><?php echo $message; ?></strong></p>
    <?php endif; ?>

    <!-- ======================================================
         LOGIN FORM
    ====================================================== -->
    <h3>Login</h3>
    <form method="POST">
        <input type="hidden" name="action" value="login">

        <label>Role:</label>
        <select name="role" required>
            <option value="admin">Admin</option>
        </select>

        <label>User ID:</label>
        <input type="text" name="user_id" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button style="background:black;color:white;">Login</button>
    </form>

    <hr>

    <!-- ======================================================
         CREATE NEW USER FORM
    ====================================================== -->
    <h3>Create New User</h3>
    <form method="POST">
        <input type="hidden" name="action" value="create_user">

        <label>User ID:</label>
        <input type="text" name="user_id" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Role:</label>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="manager">Manager</option>
            <option value="user">User</option>
        </select>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button style="background:blue;color:white;">Create User</button>
    </form>

</div>

</body>
</html>
