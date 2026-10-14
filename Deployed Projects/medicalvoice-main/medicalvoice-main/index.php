<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}
include('database.php');
include('header.php');

// ✅ Fetch credits from members table
$userId = $_SESSION['member_id'];
$stmt = $conn->prepare("SELECT credits FROM members WHERE member_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($credits);
$stmt->fetch();
$stmt->close();
?>

<div class="container mt-5">
    <h1 class="mb-5 text-center fw-bold">Chief AI Medical Support Dashboard</h1>

    <!-- Credits Display -->
    <div class="alert alert-info text-center fs-5 mb-4">
        Your Credits: <strong><?php echo $credits; ?></strong>
        <a href="buy_credits.php" class="btn btn-primary btn-sm ms-3">Buy Credits</a>
    </div>

    <!-- Upload Section -->
    <div id="upload" class="card shadow-lg mb-5">
        <div class="card-header bg-primary text-white fs-5">
            <i class="fas fa-upload"></i> Upload an Audio File
        </div>
        <div class="card-body p-4">
            <?php include('upload.php'); ?>
        </div>
    </div>

    <!-- Convert & Read Section -->
    <div id="convert" class="card shadow-lg mb-5">
        <div class="card-header bg-success text-white fs-5">
            <i class="fas fa-microphone"></i> Convert & Read Response
        </div>
        <div class="card-body p-4">
            <?php include('python_process.php'); ?>
        </div>
    </div>

    <!-- Payment Section -->
    <div id="pay" class="card shadow-lg mb-5">
        <div class="card-header bg-warning text-dark fs-5">
            <i class="fas fa-credit-card"></i> Payment
        </div>
        <div class="card-body p-4 text-center">
            <a href="buy_credits.php" class="btn btn-warning btn-lg">
                <i class="fas fa-money-bill-wave"></i> Buy Credits
            </a>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
