<?php
session_start();
$userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'User';
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Successfully Logged Out - Medical AI Professional Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            --text-primary: #1a202c;
            --text-secondary: #4a5568;
            --text-muted: #718096;
            --bg-primary: #ffffff;
            --bg-secondary: #f7fafc;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.15);
            --shadow-2xl: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background Elements */
        .bg-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .bg-shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .bg-shape:nth-child(1) {
            width: 350px;
            height: 350px;
            top: -175px;
            left: -175px;
            animation-delay: 0s;
        }

        .bg-shape:nth-child(2) {
            width: 250px;
            height: 250px;
            top: 30%;
            right: -125px;
            animation-delay: 2s;
        }

        .bg-shape:nth-child(3) {
            width: 200px;
            height: 200px;
            bottom: -100px;
            left: 40%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            50% { transform: translateY(-25px) rotate(180deg) scale(1.05); }
        }

        /* Logout Container */
        .logout-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: 20px;
            box-shadow: var(--shadow-2xl);
            padding: 3.5rem;
            width: 100%;
            max-width: 500px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
            text-align: center;
            animation: slideInUp 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .logout-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: var(--danger-gradient);
            transform: scaleX(0);
            animation: expandWidth 1s ease-out 0.5s forwards;
        }

        @keyframes expandWidth {
            to { transform: scaleX(1); }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Logout Icon */
        .logout-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--danger-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            animation: iconPulse 2s ease-in-out infinite;
        }

        .logout-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            filter: blur(30px);
            opacity: 0.6;
            z-index: -1;
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Content Styling */
        .logout-title {
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
            line-height: 1.2;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .logout-message {
            color: var(--text-muted);
            font-size: 1.25rem;
            margin-bottom: 3rem;
            line-height: 1.6;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 1s ease-out 0.6s both;
        }

        .user-highlight {
            background: var(--bg-secondary);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            border: 2px solid var(--border-color);
            display: inline-block;
            margin: 0.5rem 0;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Login Button */
        .login-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--primary-gradient);
            color: white;
            padding: 1rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1rem;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 1s ease-out 0.8s both;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-xl);
        }

        /* Auto Redirect Section */
        .auto-redirect {
            margin-top: 3rem;
            color: var(--text-muted);
            font-size: 1rem;
            animation: fadeInUp 1s ease-out 1s both;
        }

        .countdown {
            color: #667eea;
            font-weight: 700;
            font-size: 1.2rem;
            background: var(--bg-secondary);
            padding: 0.5rem 1rem;
            border-radius: 12px;
            display: inline-block;
            margin: 0 0.5rem;
            border: 1px solid var(--border-color);
        }

        /* Security Notice */
        .security-notice {
            margin-top: 2.5rem;
            padding: 1.5rem;
            background: var(--bg-secondary);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            animation: fadeInUp 1s ease-out 1.2s both;
        }

        .security-notice h4 {
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .security-notice p {
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.5;
            margin: 0;
        }

        /* Platform Stats */
        .platform-stats {
            margin-top: 2.5rem;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            animation: fadeInUp 1s ease-out 1.4s both;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            background: var(--bg-secondary);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-3px);
            background: white;
            box-shadow: var(--shadow-md);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .logout-container {
                padding: 3rem 2rem;
                margin: 1rem;
            }
            
            .logout-icon {
                width: 100px;
                height: 100px;
                font-size: 2.5rem;
            }
            
            .logout-title {
                font-size: 1.75rem;
            }
            
            .logout-message {
                font-size: 1.1rem;
            }
            
            .platform-stats {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
        }

        @media (max-width: 480px) {
            .logout-container {
                padding: 2.5rem 1.5rem;
            }
            
            .logout-icon {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }
            
            .logout-title {
                font-size: 1.5rem;
            }
            
            .login-btn {
                padding: 1rem 2rem;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background Shapes -->
    <div class="bg-shapes">
        <div class="bg-shape"></div>
        <div class="bg-shape"></div>
        <div class="bg-shape"></div>
    </div>

    <div class="logout-container">
        <div class="logout-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        
        <h1 class="logout-title">Successfully Logged Out</h1>
        
        <p class="logout-message">
            Goodbye, <span class="user-highlight"><?php echo htmlspecialchars($userName); ?></span>!<br>
            You have been securely logged out of the Medical AI platform.
        </p>
        
        <a href="login.php" class="login-btn" aria-label="Sign in again">
            <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
            Sign In Again
        </a>
        
        <div class="auto-redirect">
            You will be automatically redirected to login in <span class="countdown" id="countdown">5</span> seconds
        </div>

        <div class="security-notice">
            <h4>
                <i class="fas fa-shield-alt" style="color: #43e97b;"></i>
                Security Notice
            </h4>
            <p>
                Your session has been securely terminated. All sensitive data has been cleared from this device.
            </p>
        </div>

        <div class="platform-stats">
            <div class="stat-item">
                <div class="stat-number">HIPAA</div>
                <div class="stat-label">Compliant</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">256-bit</div>
                <div class="stat-label">Encryption</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">99.9%</div>
                <div class="stat-label">Uptime</div>
            </div>
        </div>
    </div>

    <script>
        // Auto-redirect countdown with enhanced styling
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            // Add visual feedback
            if (countdown <= 3) {
                countdownElement.style.background = '#fee140';
                countdownElement.style.color = '#1a202c';
            }
            
            if (countdown <= 0) {
                clearInterval(timer);
                // Add fade out effect
                document.body.style.transition = 'opacity 0.5s ease';
                document.body.style.opacity = '0';
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 500);
            }
        }, 1000);
    </script>
</body>
</html>

