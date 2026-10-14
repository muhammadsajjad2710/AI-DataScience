<!-- <div class="header">
    <img src="images/logo.jpeg" alt="Chief AI Logo" class="logo">
</div> -->
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document Analysis System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="header" style="display: flex; justify-content: space-between; align-items: center; padding: 10px;">
    <img src="images/logo.jpeg" alt="Chief AI Logo" class="logo" style="height: 60px;">

    <div>
        <?php if (isset($_SESSION['member_id'])): ?>
            <span>Welcome, <?php echo $_SESSION['name']; ?>!</span>
            <a href="logout.php"><button>Logout</button></a>
        <?php else: ?>
            <a href="login.php"><button>Login</button></a>
        <?php endif; ?>
    </div>
</div>
<hr>

