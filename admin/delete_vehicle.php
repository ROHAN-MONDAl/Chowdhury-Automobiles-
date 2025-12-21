<?php
session_start();
require_once 'db.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("Invalid ID");
}

$vehicleId = (int) $id;
$filesToDelete = [];

// ======================================================
// 1. CONFIGURATION: Map tables to their subdirectories
// ======================================================
$config = [
    'vehicle'           => [
        'foreign_key' => 'id',
        'columns'     => ['photo1', 'photo2', 'photo3', 'photo4'],
        'directory'   => '../images/'  // <-- ADD SUBDIRECTORY
    ],
    'vehicle_seller'    => [
        'foreign_key' => 'vehicle_id',
        'columns'     => ['doc_aadhar_front', 'doc_aadhar_back', 'doc_voter_front', 'doc_voter_back', 'rc_front', 'rc_back'],
        'directory'   => '../images/'   // <-- ADD SUBDIRECTORY
    ],
    'vehicle_purchaser' => [
        'foreign_key' => 'vehicle_id',
        'columns'     => ['purchaser_doc_aadhar_front', 'purchaser_doc_aadhar_back', 'purchaser_doc_voter_front', 'purchaser_doc_voter_back'],
        'directory'   => '../images/'  // <-- ADD SUBDIRECTORY
    ]
];

try {
    $conn->begin_transaction();

    // ======================================================
    // 2. FETCH FILENAMES & BUILD FULL PATHS
    // ======================================================
    foreach ($config as $table => $data) {
        $fkColumn   = $data['foreign_key'];
        $columns    = $data['columns'];
        $directory  = $data['directory'];  // <-- GET DIRECTORY

        $foundFiles = getFilesFromTable($conn, $table, $fkColumn, $vehicleId, $columns, $directory);
        $filesToDelete = array_merge($filesToDelete, $foundFiles);
    }

    // ======================================================
    // 3. DELETE DATABASE RECORDS
    // ======================================================

    // Delete Children first
    $children = ['vehicle_seller', 'vehicle_purchaser', 'vehicle_ot'];
    foreach ($children as $table) {
        $conn->query("DELETE FROM $table WHERE vehicle_id = $vehicleId");
    }

    // Delete Parent (Vehicle)
    $conn->query("DELETE FROM vehicle WHERE id = $vehicleId");

    $conn->commit();

    // ======================================================
    // 4. DELETE PHYSICAL FILES
    // ======================================================
    foreach ($filesToDelete as $path) {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    $_SESSION['flash_message'] = "Deleted successfully.";
    $_SESSION['flash_class']   = "global-success-msg";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['flash_message'] = "Error: " . $e->getMessage();
    $_SESSION['flash_class']   = "global-error-msg";
}

header("Location: inventory.php");
exit;

// ======================================================
// HELPER FUNCTION - Now accepts directory parameter
// ======================================================
function getFilesFromTable($conn, $tableName, $whereCol, $id, $columns, $directory)
{
    $list = [];
    if (empty($columns)) return $list;

    $colStr = implode(", ", $columns);
    $sql = "SELECT $colStr FROM $tableName WHERE $whereCol = $id";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        foreach ($row as $file) {
            if (!empty($file)) {
                // Combine directory + filename for full path
                $list[] = $directory . $file;
            }
        }
    }
    return $list;
}
