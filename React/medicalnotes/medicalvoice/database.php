<?php
// Create a MySQLi connection in database.php
$host = 'localhost';
$dbname = 'medical_notes';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("<p style='color: red;'>❌ Database connection failed: " . $conn->connect_error . "</p>");
}

//echo "<p style='color: green;'>✅ Database connected successfully.</p>";

?>

