<?php
include("database.php");

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $login = trim($_POST['login']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $company = trim($_POST['company']);
    $level = 1; // default user level
    $jointid = date("Y-m-d H:i:s");
    $stripeid = null;

    // Check if username or email already exists
    $checkSql = "SELECT member_id FROM members WHERE login = ? OR email = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ss", $login, $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<p style='color:red;'>Username or Email already exists.</p>";
        exit;
    }
    $checkStmt->close();

    // Insert user into database
    $sql = "INSERT INTO members (login, password, name, email, phone, company, level, jointid, stripeid)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("ssssssiss", $login, $password, $name, $email, $phone, $company, $level, $jointid, $stripeid);

    if ($stmt->execute()) {
        header("Location: login.php?success=registered");
        exit;
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<p style='color:red;'>Invalid request.</p>";
}
?>

