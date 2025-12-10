<?php    
session_start();
date_default_timezone_set('Asia/Kolkata');
// Database Configuration
// Defines the database server details for connecting to the MySQL database.
// Server: Localhost (127.0.0.1), User: root (default for local development), No password (insecure for production).
// Database: chowdhury_automobile, Port: 3306 (default MySQL port).
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "chowdhury_automobile";
$port = 3306;

// Connect to Database
// Establishes a connection to the MySQL database using MySQLi.
// This connection is used throughout the script for user authentication and data retrieval.
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check for connection errors and terminate the script if the database is unreachable.
// This prevents further execution if the database is down or misconfigured.
if ($conn->connect_errno) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set the character set to UTF-8 for proper handling of international characters.
// This ensures data integrity when storing or retrieving text from the database.
$conn->set_charset("utf8mb4");
?>