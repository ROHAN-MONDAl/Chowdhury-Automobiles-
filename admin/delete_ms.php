<?php
// delete.php example logic
include 'db.php';

if(isset($_GET['id']) && isset($_GET['table'])) {
    $id = $_GET['id'];
    $table = $_GET['table'];

    // Security: Only allow specific table names to prevent SQL injection
    if($table === 'managers' || $table === 'staff') {
        $sql = "DELETE FROM $table WHERE id = $id";
        if($conn->query($sql) === TRUE) {
            header("Location: dashboard.php?msg=deleted");
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }
}
?>