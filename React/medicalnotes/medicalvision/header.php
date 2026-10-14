<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedicalVision - AI-Powered Document Analysis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
            --success-color: #48bb78;
            --warning-color: #ed8936;
            --danger-color: #f56565;
            --dark-color: #2d3748;
            --light-color: #f8fafc;
            --border-color: #e2e8f0;
            --text-primary: #2d3748;
            --text-secondary: #718096;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--light-color);
            color: var(--text-primary);
            line-height: 1.6;
        }
        
        /* Enhanced Sidebar */
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            min-height: 100vh;
            padding-top: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 280px;
            overflow: hidden;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 0 20px 25px rgba(0,0,0,0.1);
        }
        
        .sidebar.collapsed {
            width: 80px;
        }
        
        .sidebar.collapsed h4, .sidebar.collapsed a span {
            display: none;
        }
        
        .sidebar-header {
            padding: 2rem 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.2rem;
        }
        
        .sidebar-logo img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
        }
        
        .sidebar-menu {
            padding: 0 1rem;
        }
        
        .sidebar a {
            color: white;
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        
        .sidebar a:hover::before {
            left: 100%;
        }
        
        .sidebar a:hover {
            background: rgba(255,255,255,0.15);
            transform: translateX(5px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .sidebar a.active {
            background: rgba(255,255,255,0.2);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        /* Enhanced Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .navbar-brand:hover {
            color: var(--secondary-color) !important;
        }
        
        .navbar-brand img {
            width: 35px;
            height: 35px;
            border-radius: 8px;
        }
        
        .toggle-btn {
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.2rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .toggle-btn:hover {
            background: var(--light-color);
            color: var(--primary-color);
        }

        /* Main Projet button */
        .btn-main {
            background: linear-gradient(135deg, #eef2ff, #ffffff);
            color: #667eea;
            border: 2px solid #667eea;
            padding: 0.45rem 0.9rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
        }
        .btn-main i { color: inherit; }
        .btn-main:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #ffffff;
            border-color: transparent;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.35);
            text-decoration: none;
        }
        .btn-main:focus { outline: none; box-shadow: 0 0 0 0.2rem rgba(102,126,234,0.25); }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            transition: margin-left 0.3s ease;
        }
        
        .main-content.sidebar-collapsed {
            margin-left: 80px;
        }
        
        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .user-details {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.9rem;
        }
        
        .user-role {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <?php
    //session_start();
    include "database.php";
    ?>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-logo">
                <i class="fas fa-eye"></i>
                <span>MedicalVision</span>
            </a>
        </div>
        
        <div class="sidebar-menu">
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-home me-2"></i>
                <span>Dashboard</span>
            </a>
            <a href="index.php#step1" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                <i class="fas fa-upload me-2"></i>
                <span>Upload</span>
            </a>
            <a href="index.php#step2" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                <i class="fas fa-magic me-2"></i>
                <span>Extract Text</span>
            </a>
            <a href="index.php#step3" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                <i class="fas fa-book-open me-2"></i>
                <span>Read Response</span>
            </a>
            <a href="buy_credits.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'buy_credits.php' ? 'active' : ''; ?>">
                <i class="fas fa-coins me-2"></i>
                <span>Buy Credits</span>
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <button class="toggle-btn" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-eye"></i>
                    MedicalVision
                </a>
                
                <div class="ms-auto">
                    <div class="user-info">
                        <a href="../index.php" class="btn btn-main me-2" aria-label="Go to main project">
                            <i class="fas fa-home me-1" aria-hidden="true"></i> Main Project
                        </a>
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-details">
                            <div class="user-name">
                                <?php if (isset($_SESSION['username'])): ?>
                                    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                                <?php else: ?>
                                    Welcome, User
                                <?php endif; ?>
                            </div>
                            <div class="user-role">Medical Document Processor</div>
                        </div>
                        <a href="../logout.php" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Page Content -->
        <div class="container-fluid py-4">