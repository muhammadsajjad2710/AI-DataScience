<?php
include("database.php");

$login = $_POST['login'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$company = $_POST['company'];
$level = 1; // default user level
$jointdt = date("Y-m-d H:i:s");
$stripeid = null;

$sql = "INSERT INTO members (login, password, name, email, phone, company, level, jointdt, stripeid) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssiss", $login, $password, $name, $email, $phone, $company, $level, $jointdt, $stripeid);

if ($stmt->execute()) {
    header("Location: login.php?success=registered");
    exit;
} else {
    echo "Error: " . $stmt->error;
}
?>
