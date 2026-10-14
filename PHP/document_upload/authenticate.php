<?php
session_start();
include("database.php");

$login = $_POST['login'];
$password = $_POST['password'];

$sql = "SELECT * FROM members WHERE login = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        $_SESSION['member_id'] = $row['member_id'];
        $_SESSION['name'] = $row['name'];
        header("Location: index.php");
        exit;
    } else {
        echo "Invalid credentials.";
    }
} else {
    echo "User not found.";
}
?>
