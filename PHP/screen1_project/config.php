<?php
$host = "localhost";
$username = "root";  // Change this if needed
$password = "";      // Change this if needed
$dbname = "screen1_db";

// Create database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
