<?php
session_start();
include('database.php');

require 'vendor/autoload.php';


if (isset($_GET['credits']) && isset($_SESSION['member_id'])) {
    $credits = intval($_GET['credits']);
    $userId = $_SESSION['member_id'];

    $stmt = $conn->prepare("UPDATE members SET credits = credits + ? WHERE member_id = ?");
    $stmt->bind_param("ii", $credits, $userId);
    $stmt->execute();

    echo "<div style='text-align:center; margin-top:50px;'>
            <h1>✅ Payment Successful!</h1>
            <p>Your account has been credited with <strong>$credits credits</strong>.</p>
            <a href='index.php' class='btn btn-primary'>Return to Dashboard</a>
          </div>";
} else {
    echo "Invalid request.";
}
?>
