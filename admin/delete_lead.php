<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';

header('Content-Type: application/json; charset=utf-8');

$response = [
    'status' => 'error',
    'message' => 'Invalid request'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_lead_id'])) {

    $deleteId = (int) $_POST['delete_lead_id'];

    $stmt = $conn->prepare("DELETE FROM leads WHERE id = ?");
    $stmt->bind_param("i", $deleteId);

    if ($stmt->execute()) {

        $_SESSION['flash_message'] = "Lead deleted successfully.";
        $_SESSION['flash_class']   = "global-success-msg";

        $response = [
            'status' => 'success'
        ];
    } else {
        $response['message'] = 'Database delete failed';
    }

    $stmt->close();
    $conn->close();
}

echo json_encode($response);
exit; // 🚨 REQUIRED
