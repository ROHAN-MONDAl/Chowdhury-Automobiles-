<?php
// ==============================
include "db.php"; // Database connection
// -----------------------------
// REMOVE REMEMBER-ME TOKEN
// -----------------------------
if (isset($_COOKIE["remember_me"])) {

    // cookie format = user_id:token
    list($uid, $token) = explode(":", $_COOKIE["remember_me"]);

    // Remove token from database
    $stmt = $conn->prepare("UPDATE users SET remember_token_hash=NULL WHERE user_id=?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();

    // Delete cookie
    setcookie("remember_me", "", time() - 3600, "/", "", true, true);
}

// -----------------------------
// DESTROY SESSION SECURELY
// -----------------------------
$_SESSION = [];
session_unset();
session_destroy();

// Redirect to login
header("Location: index.php");
exit();
?>