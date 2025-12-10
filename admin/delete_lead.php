<?php
session_start();
require 'db.php'; // your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_lead_id'])) {
    $deleteId = intval($_POST['delete_lead_id']);
    $stmt = $conn->prepare("DELETE FROM leads WHERE id = ?");
    $stmt->bind_param("i", $deleteId);

    if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Lead deleted successfully.";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Failed to delete lead.";
        $_SESSION['flash_type'] = "danger";
    }

    // Redirect back to dashboard
    header("Location: dashboard.php");
    exit;
}
