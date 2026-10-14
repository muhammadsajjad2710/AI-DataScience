<?php


include('database.php');
include('header.php');

$userId = $_SESSION['member_id'];
$stmt = $conn->prepare("SELECT credits FROM members WHERE member_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($credits);
$stmt->fetch();
$stmt->close();
?>
<div class="container mt-5">
    <h1 class="mb-5 text-center fw-bold">Medical Vision - Document Analysis</h1>

    <div class="alert alert-info text-center fs-5 mb-4">
        Your Credits: <strong><?php echo $credits; ?></strong>
        <a href="buy_credits.php" class="btn btn-primary btn-sm ms-3">Buy Credits</a>
    </div>

    <div class="card shadow-lg mb-5">
        <div class="card-header bg-primary text-white fs-5">
            <i class="fas fa-upload"></i> Upload Image
        </div>
        <div class="card-body p-4">
            <?php include('upload_form.php'); ?>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>