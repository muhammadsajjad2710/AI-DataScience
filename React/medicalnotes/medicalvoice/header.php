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
    <title>Chief AI Medical Support System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
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
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --shadow-xl: 0 20px 25px rgba(0,0,0,0.1);
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
            box-shadow: var(--shadow-xl);
        }

        .sidebar.collapsed { 
            width: 80px; 
            transform: translateX(0);
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .sidebar.collapsed h4, 
        .sidebar.collapsed a span { 
            display: none; 
        }

        .sidebar.collapsed .sidebar-logo span {
            display: none;
        }

        .sidebar.collapsed .sidebar-logo img {
            margin: 0 auto;
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
            box-shadow: var(--shadow-md);
        }

        .sidebar a i { 
            width: 24px; 
            text-align: center;
            margin-right: 1rem;
            font-size: 1.1rem;
        }

        .sidebar.collapsed a i {
            margin-right: 0;
        }

        .sidebar a.active {
            background: rgba(255,255,255,0.2);
            box-shadow: var(--shadow-md);
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
            box-shadow: var(--shadow-sm);
        }

        .navbar .container-fluid {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        /* Toggle Button Container */
        .toggle-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .toggle-btn {
            order: 1;
        }

        .toggle-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
            user-select: none;
        }

        .toggle-info {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .toggle-status {
            color: var(--text-secondary);
            font-size: 0.75rem;
            opacity: 0.7;
            font-style: italic;
        }

        /* Enhanced Toggle Button */
        .toggle-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: 2px solid rgba(255,255,255,0.2);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
            z-index: 1001;
            min-width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .toggle-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .toggle-btn:hover::before {
            left: 100%;
        }

        .toggle-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            border-color: rgba(255,255,255,0.4);
        }

        .toggle-btn:active {
            transform: translateY(0);
        }

        .toggle-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
        }

        .toggle-btn .toggle-icon {
            transition: transform 0.3s ease;
            font-size: 1.2rem;
        }

        .sidebar.hidden .toggle-btn .toggle-icon {
            transform: rotate(180deg);
        }

        /* Mobile Toggle Button */
        @media (max-width: 768px) {
            .toggle-btn {
                min-width: 45px;
                height: 45px;
                padding: 0.6rem 0.8rem;
            }
            
            .toggle-label {
                display: none !important;
            }
        }

        /* Navbar Brand */
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

        /* Nav Actions */
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-btn.primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
        }

        .nav-btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: white;
        }

        .nav-btn.outline {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .nav-btn.outline:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.95rem;
        }

        .user-role {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        /* Content Area Adjustments */
        .content-area {
            margin-left: 280px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            padding: 2rem;
        }

        .content-area.expanded {
            margin-left: 80px;
        }

        .content-area.full-width {
            margin-left: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .content-area {
                margin-left: 0;
                padding: 1rem;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }

            .nav-actions {
                flex-direction: column;
                gap: 0.5rem;
            }

            .user-info {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }
        }

        /* Animation Classes */
        .sidebar {
            animation: slideInLeft 0.5s ease;
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
            }
            to {
                transform: translateX(0);
            }
        }

        /* Enhanced Button Styles */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
    </style>
</head>
<body>
    <!-- Enhanced Sidebar -->
    <div class="sidebar" id="sidebarMenu">
        <div class="sidebar-header">
            <a href="../index.php" class="sidebar-logo">
                <img src="images/logo.jpeg" alt="Chief AI Logo" onerror="this.style.display='none'">
                <span>Chief AI</span>
            </a>
        </div>
        
        <div class="sidebar-menu">
            <a href="index.php#upload" class="nav-link">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Audio Upload</span>
            </a>
            <a href="index.php#convert" class="nav-link">
                <i class="fas fa-microphone"></i>
                <span>AI Processing</span>
            </a>
            <a href="index.php#read" class="nav-link">
                <i class="fas fa-book-open"></i>
                <span>Read Response</span>
            </a>
            <a href="buy_credits.php" class="nav-link">
                <i class="fas fa-credit-card"></i>
                <span>Buy Credits</span>
            </a>
        </div>
    </div>

    <!-- Enhanced Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <!-- Toggle Button Container -->
            <div class="toggle-container">
                <button class="toggle-btn" id="sidebarToggle" title="Toggle Sidebar - Click to hide/show sidebar">
                    <i class="fas fa-bars toggle-icon"></i>
                </button>
                <div class="toggle-info">
                    <span class="toggle-label d-none d-md-inline">Toggle Sidebar</span>
                    <span class="toggle-status d-none d-lg-inline">(Click to hide/show)</span>
                </div>
            </div>

            <!-- Logo -->
            <a class="navbar-brand" href="../index.php">
                <img src="images/logo.jpeg" alt="Chief AI Logo" onerror="this.style.display='none'">
                Chief AI Medical Support
            </a>

            <!-- Right Side Actions -->
            <div class="nav-actions">
                <a href="../index.php" class="nav-btn outline">
                    <i class="fas fa-home"></i>
                    <span class="d-none d-md-inline">Main Project</span>
                </a>
                
                <?php if (isset($_SESSION['member_id'])): ?>
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)); ?>
                        </div>
                        <div class="user-details">
                            <div class="user-name"><?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></div>
                            <div class="user-role">Medical Professional</div>
                        </div>
                    </div>
                    <a href="../logout.php" class="nav-btn primary">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="d-none d-md-inline">Logout</span>
                    </a>
                <?php else: ?>
                    <a href="../login.php" class="nav-btn primary">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="d-none d-md-inline">Login</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Content Area -->
    <div class="content-area" id="contentArea">
