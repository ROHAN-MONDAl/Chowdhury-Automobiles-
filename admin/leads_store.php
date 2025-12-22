<?php
session_start();
require_once 'db.php';

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$bike = trim($_POST['bike_model'] ?? '');

if ($name === '' || $phone === '' || $bike === '') {
    $_SESSION['login_message'] = "All fields are required.";
    $_SESSION['login_message_type'] = "warning";
    header("Location: employers_dashboard.php");
    exit;
}

// Check for duplicate bike model
$checkStmt = $conn->prepare("SELECT id FROM leads WHERE bike_model = ? LIMIT 1");
$checkStmt->bind_param("s", $bike);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    $_SESSION['login_message'] = "This bike model already exists.";
    $_SESSION['login_message_type'] = "danger";
    header("Location: employers_dashboard.php");
    exit;
}

// Insert new lead
$sql = "INSERT INTO leads (name, phone, bike_model) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $phone, $bike);
$stmt->execute();

$_SESSION['login_message'] = "Lead added successfully.";
$_SESSION['login_message_type'] = "success";
header("Location: employers_dashboard.php");
exit;
