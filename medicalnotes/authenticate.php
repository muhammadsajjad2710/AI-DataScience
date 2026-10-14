<?php

session_start();
include("database.php");


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'], $_POST['password'])) {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    $sql = "SELECT member_id, password, name FROM members WHERE login = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

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
            echo "<p style='color:red;'>Invalid credentials.</p>";
        }
    } else {
        echo "<p style='color:red;'>User not found.</p>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<p style='color:red;'>Invalid request.</p>";
}
?>

