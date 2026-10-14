<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Medical AI Professional Healthcare Platform</title>
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
            animation: float 8s ease-in-out infinite;
        }

        .bg-shape:nth-child(1) {
            width: 400px;
            height: 400px;
            top: -200px;
            left: -200px;
            animation-delay: 0s;
        }

        .bg-shape:nth-child(2) {
            width: 300px;
            height: 300px;
            top: 20%;
            right: -150px;
            animation-delay: 2s;
        }

        .bg-shape:nth-child(3) {
            width: 250px;
            height: 250px;
            bottom: -125px;
            left: 30%;
            animation-delay: 4s;
        }

        .bg-shape:nth-child(4) {
            width: 200px;
            height: 200px;
            top: 60%;
            left: -100px;
            animation-delay: 6s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            50% { transform: translateY(-30px) rotate(180deg) scale(1.1); }
        }

        /* Register Container */
        .register-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: 20px;
            box-shadow: var(--shadow-2xl);
            padding: 2.75rem;
            width: 100%;
            max-width: 580px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .register-container::before {
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
            max-width: 400px;
            margin: 0 auto;
        }

        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            animation: fadeInUp 1s ease-out both;
        }

        .form-group:nth-child(1) { animation-delay: 0.4s; }
        .form-group:nth-child(2) { animation-delay: 0.5s; }
        .form-group:nth-child(3) { animation-delay: 0.6s; }
        .form-group:nth-child(4) { animation-delay: 0.7s; }
        .form-group:nth-child(5) { animation-delay: 0.8s; }
        .form-group:nth-child(6) { animation-delay: 0.9s; }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

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

        .required-field {
            color: #e53e3e;
            font-size: 0.8rem;
            margin-top: 0.25rem;
            font-weight: 500;
        }

        /* Register Button */
        .register-btn {
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
            animation: fadeInUp 1s ease-out 1s both;
        }

        .register-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .register-btn:hover::before {
            left: 100%;
        }

        .register-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .register-btn:active {
            transform: translateY(-1px);
        }

        /* Divider */
        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
            animation: fadeInUp 1s ease-out 1.1s both;
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

        /* Login Link */
        .login-link {
            text-align: center;
            color: var(--text-muted);
            font-size: 1rem;
            animation: fadeInUp 1s ease-out 1.2s both;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            position: relative;
        }

        .login-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-gradient);
            transition: width 0.3s ease;
        }

        .login-link a:hover::after {
            width: 100%;
        }

        .login-link a:hover {
            color: #5a67d8;
        }

        /* Benefits Section */
        .benefits {
            margin-top: 3rem;
            padding-top: 2.5rem;
            border-top: 1px solid var(--border-color);
            animation: fadeInUp 1s ease-out 1.3s both;
        }

        .benefits-title {
            text-align: center;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .benefit-item {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            padding: 1rem 0.5rem;
            transition: all 0.3s ease;
            border-radius: 12px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
        }

        .benefit-item:hover {
            background: white;
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: #667eea;
        }

        .benefit-item i {
            display: block;
            font-size: 1.5rem;
            color: #667eea;
            margin-bottom: 0.75rem;
            transition: transform 0.3s ease;
        }

        .benefit-item:hover i {
            transform: scale(1.1);
        }

        /* Progress Indicator */
        .progress-bar {
            width: 100%;
            height: 4px;
            background: var(--border-color);
            border-radius: 2px;
            margin-bottom: 2rem;
            overflow: hidden;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .progress-fill {
            height: 100%;
            background: var(--primary-gradient);
            width: 0%;
            transition: width 0.5s ease;
            animation: progressFill 2s ease-out 1.5s forwards;
        }

        @keyframes progressFill {
            to { width: 100%; }
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
        @media (max-width: 768px) {
            .register-container {
                padding: 2.5rem;
                margin: 1rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .logo {
                font-size: 1.75rem;
            }
            
            .logo i {
                font-size: 2.5rem;
            }
            
            .benefits-grid {
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
            .register-container {
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
        <div class="bg-shape"></div>
    </div>

    <div class="register-container">
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
                Join Medical AI and transform your medical workflow with cutting-edge AI technology.
            </p>
        </div>

        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>

    <form action="register_user.php" method="POST" novalidate>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="login">Username <span class="required-field">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon" aria-hidden="true"></i>
                        <input type="text" name="login" id="login" class="form-input" 
                               placeholder="Choose username" required autocomplete="username"
                               aria-describedby="login-help">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password <span class="required-field">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon" aria-hidden="true"></i>
                        <input type="password" name="password" id="password" class="form-input" 
                               placeholder="Create password" required autocomplete="new-password"
                               aria-describedby="password-help">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="name">Full Name <span class="required-field">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-id-card input-icon" aria-hidden="true"></i>
                        <input type="text" name="name" id="name" class="form-input" 
                               placeholder="Enter your full name" required autocomplete="name"
                               aria-describedby="name-help">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email <span class="required-field">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon" aria-hidden="true"></i>
                        <input type="email" name="email" id="email" class="form-input" 
                               placeholder="your@email.com" required autocomplete="email"
                               aria-describedby="email-help">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone</label>
                    <div class="input-wrapper">
                        <i class="fas fa-phone input-icon" aria-hidden="true"></i>
                        <input type="tel" name="phone" id="phone" class="form-input" 
                               placeholder="Phone number" autocomplete="tel"
                               aria-describedby="phone-help">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="company">Company/Organization</label>
                    <div class="input-wrapper">
                        <i class="fas fa-building input-icon" aria-hidden="true"></i>
                        <input type="text" name="company" id="company" class="form-input" 
                               placeholder="Company name" autocomplete="organization"
                               aria-describedby="company-help">
                    </div>
                </div>
            </div>

            <button type="submit" class="register-btn">
                <i class="fas fa-user-plus" aria-hidden="true"></i>
                Create Professional Account
            </button>
    </form>

        <div class="divider">
            <span>Already have an account?</span>
        </div>

        <div class="login-link">
            Sign in to your existing account <a href="login.php">here</a>
        </div>

        <div class="benefits">
            <h4 class="benefits-title">Why Choose Medical AI?</h4>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <i class="fas fa-shield-alt"></i>
                    HIPAA Compliant
                </div>
                <div class="benefit-item">
                    <i class="fas fa-bolt"></i>
                    Real-time Processing
                </div>
                <div class="benefit-item">
                    <i class="fas fa-brain"></i>
                    AI-Powered
                </div>
                <div class="benefit-item">
                    <i class="fas fa-mobile-alt"></i>
                    Mobile Optimized
                </div>
            </div>
        </div>
    </div>
</body>
</html>
