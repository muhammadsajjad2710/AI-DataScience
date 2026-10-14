<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Vision - Document AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f8f9fa; }
        .sidebar { background: #007bff; color: white; min-height: 100vh; padding-top: 20px; transition: width 0.3s ease; width: 210px; overflow: hidden; }
        .sidebar.collapsed { width: 60px; }
        .sidebar.collapsed h4, .sidebar.collapsed a span { display: none; }
        .sidebar a { color: white; display: flex; align-items: center; padding: 10px 15px; text-decoration: none; }
        .sidebar a i { width: 30px; text-align: center; }
        .sidebar a:hover { background: #0056b3; }
        .content-area { transition: margin-left 0.3s ease; margin-left: 100px; }
        .content-area.expanded { margin-left: 50px; }
        .toggle-btn { background: none; border: none; color: white; font-size: 20px; cursor: pointer; }
        .logo { height: 50px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <button class="toggle-btn" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <a class="navbar-brand d-flex align-items-center ms-3" href="index.php">
            <img src="images/logo.jpeg" alt="Chief AI Logo" class="logo me-2">
            Medical Vision
        </a>
        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['member_id'])): ?>
                <span class="text-white me-3">Welcome, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></span>
                <a href="logout.php" class="btn btn-light btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-light btn-sm"><i class="fas fa-sign-in-alt"></i> Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
        <div class="col-auto sidebar" id="sidebarMenu">
            <h4 class="px-3">Menu</h4>
            <a href="index.php"><i class="fas fa-upload"></i> <span>Upload</span></a>
            <a href="buy_credits.php"><i class="fas fa-credit-card"></i> <span>Buy Credits</span></a>
        </div>
        <div class="col content-area" id="contentArea"></div>