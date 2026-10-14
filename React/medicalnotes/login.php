<?php
session_start();
if (isset($_SESSION['member_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Medical AI Professional Healthcare Platform</title>
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
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.12);
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
            width: 300px;
            height: 300px;
            top: -150px;
            left: -150px;
            animation-delay: 0s;
        }

        .bg-shape:nth-child(2) {
            width: 200px;
            height: 200px;
            top: 50%;
            right: -100px;
            animation-delay: 2s;
        }

        .bg-shape:nth-child(3) {
            width: 150px;
            height: 150px;
            bottom: -75px;
            left: 50%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Login Container */
        .login-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: 20px;
            box-shadow: var(--shadow-2xl);
            padding: 3rem;
            width: 100%;
            max-width: 460px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: var(--primary-gradient);
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

        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 3rem;
            animation: fadeInUp 1s ease-out 0.3s both;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .logo i {
            font-size: 3rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-text {
            color: var(--text-muted);
            font-size: 1.2rem;
            font-weight: 400;
            line-height: 1.6;
            max-width: 300px;
            margin: 0 auto;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease-out both;
        }

        .form-group:nth-child(1) { animation-delay: 0.4s; }
        .form-group:nth-child(2) { animation-delay: 0.5s; }

        .form-label {
            display: block;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .form-group:focus-within .form-label {
            color: #667eea;
        }

        .input-wrapper {
            position: relative;
            transition: transform 0.3s ease;
        }

        .input-wrapper:hover {
            transform: translateY(-2px);
        }

        .input-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .form-group:focus-within .input-icon {
            color: #667eea;
            transform: translateY(-50%) scale(1.1);
        }

        .form-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3.25rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-weight: 500;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: var(--bg-primary);
        }

        .form-input::placeholder {
            color: var(--text-muted);
            font-weight: 400;
        }

        .form-input:focus::placeholder {
            opacity: 0.7;
        }

        /* Login Button */
        .login-btn {
            width: 100%;
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1.75rem;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 1s ease-out 0.6s both;
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
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .login-btn:active {
            transform: translateY(-1px);
        }

        /* Divider */
        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
            animation: fadeInUp 1s ease-out 0.7s both;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--border-color);
        }

        .divider span {
            background: var(--bg-primary);
            padding: 0 1.5rem;
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Register Link */
        .register-link {
            text-align: center;
            color: var(--text-muted);
            font-size: 1rem;
            animation: fadeInUp 1s ease-out 0.8s both;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            position: relative;
        }

        .register-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-gradient);
            transition: width 0.3s ease;
        }

        .register-link a:hover::after {
            width: 100%;
        }

        .register-link a:hover {
            color: #5a67d8;
        }

        /* Features Section */
        .features {
            margin-top: 3rem;
            padding-top: 2.5rem;
            border-top: 1px solid var(--border-color);
            animation: fadeInUp 1s ease-out 0.9s both;
        }

        .features-title {
            text-align: center;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .feature-item {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            padding: 1rem 0.5rem;
            transition: all 0.3s ease;
            border-radius: 12px;
        }

        .feature-item:hover {
            background: var(--bg-secondary);
            transform: translateY(-3px);
        }

        .feature-item i {
            display: block;
            font-size: 1.5rem;
            color: #667eea;
            margin-bottom: 0.75rem;
            transition: transform 0.3s ease;
        }

        .feature-item:hover i {
            transform: scale(1.1);
        }

        /* Security Badge */
        .security-badge {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: var(--success-gradient);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .login-container {
                padding: 2.5rem;
                margin: 1rem;
            }
            
            .logo {
                font-size: 1.75rem;
            }
            
            .logo i {
                font-size: 2.5rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .security-badge {
                position: relative;
                top: auto;
                right: auto;
                margin: 0 auto 2rem;
                width: fit-content;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem;
            }
            
            .logo {
                font-size: 1.5rem;
            }
            
            .logo i {
                font-size: 2rem;
            }
            
            .form-input {
                padding: 1rem 1rem 1rem 3rem;
            }
            
            .input-icon {
                left: 1rem;
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

    <div class="login-container">
        <!-- Security Badge -->
        <div class="security-badge">
            <i class="fas fa-shield-alt"></i>
            HIPAA Secure
        </div>

        <div class="logo-section">
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
                <span>Medical AI</span>
            </div>
            <p class="welcome-text">
                Welcome back! Sign in to access your professional healthcare AI platform.
            </p>
        </div>

                <form action="authenticate.php" method="POST" novalidate>
            <div class="form-group">
                <label class="form-label" for="login">Username</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon" aria-hidden="true"></i>
                    <input type="text" name="login" id="login" class="form-input" 
                           placeholder="Enter your username" required autocomplete="username"
                           aria-describedby="login-help">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon" aria-hidden="true"></i>
                    <input type="password" name="password" id="password" class="form-input" 
                           placeholder="Enter your password" required autocomplete="current-password"
                           aria-describedby="password-help">
                </div>
            </div>

            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                Sign In to Platform
            </button>
    </form>

        <div class="divider">
            <span>New to Medical AI?</span>
        </div>

        <div class="register-link">
            Create your professional account <a href="register.php">here</a>
        </div>

        <div class="features">
            <h4 class="features-title">Enterprise Features</h4>
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    HIPAA Compliant
                </div>
                <div class="feature-item">
                    <i class="fas fa-bolt"></i>
                    Real-time Processing
                </div>
                <div class="feature-item">
                    <i class="fas fa-brain"></i>
                    AI-Powered
                </div>
            </div>
        </div>
    </div>
</body>
</html>
