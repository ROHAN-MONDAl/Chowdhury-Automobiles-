<?php
session_start();
require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = "Invalid vehicle ID.";
    $_SESSION['flash_class']   = "global-error-msg";
    header("Location: inventory.php");
    exit;
}

$vehicleId = (int) $_GET['id'];

try {
    $conn->begin_transaction();

    // Delete child tables first
    $tables = ['vehicle_seller', 'vehicle_purchaser', 'vehicle_ot'];

    foreach ($tables as $table) {
        $stmt = $conn->prepare("DELETE FROM $table WHERE vehicle_id = ?");
        $stmt->bind_param("i", $vehicleId);
        $stmt->execute();
    }

    // Delete main vehicle
    $stmt = $conn->prepare("DELETE FROM vehicle WHERE id = ?");
    $stmt->bind_param("i", $vehicleId);
    $stmt->execute();

    $conn->commit();

    $_SESSION['flash_message'] = "Vehicle deleted successfully.";
    $_SESSION['flash_class']   = "global-success-msg";

} catch (Exception $e) {
    $conn->rollback();

    $_SESSION['flash_message'] = "Failed to delete vehicle.";
    $_SESSION['flash_class']   = "global-error-msg";
}

header("Location: inventory.php");
exit;
