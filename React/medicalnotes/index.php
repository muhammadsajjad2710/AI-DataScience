<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical AI Document Assistant - Professional Healthcare Solutions</title>
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
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 1rem 2rem;
            box-shadow: var(--shadow-lg);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: var(--shadow-xl);
        }

        .nav-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .logo:focus {
            outline: 2px solid var(--primary-gradient);
            outline-offset: 4px;
            border-radius: 8px;
        }

        .logo i {
            font-size: 2.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-text {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-secondary);
            font-weight: 500;
            padding: 0.5rem 1rem;
            background: var(--bg-secondary);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .logout-btn {
            background: var(--danger-gradient);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-xl);
        }

        .logout-btn:focus {
            outline: 2px solid rgba(255, 107, 107, 0.5);
            outline-offset: 2px;
        }

        .logout-btn:active {
            transform: translateY(0);
        }

        /* Main Container */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Hero Section */
        .hero-section {
            text-align: center;
            margin-bottom: 5rem;
            color: white;
            position: relative;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            right: -50px;
            bottom: -50px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            z-index: -1;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            line-height: 1.1;
            animation: fadeInUp 1s ease-out;
        }

        .hero-subtitle {
            font-size: clamp(1.1rem, 2.5vw, 1.5rem);
            font-weight: 400;
            opacity: 0.95;
            max-width: 700px;
            margin: 0 auto 2rem;
            line-height: 1.6;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-top: 3rem;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Modules Grid */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 2.5rem;
            margin-bottom: 4rem;
        }

        .module-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: var(--shadow-2xl);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out both;
        }

        .module-card:nth-child(1) { animation-delay: 0.1s; }
        .module-card:nth-child(2) { animation-delay: 0.2s; }

        .module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .module-card:hover::before {
            transform: scaleX(1);
        }

        .module-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.2);
        }

        .module-icon {
            width: 100px;
            height: 100px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .module-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            filter: blur(20px);
            opacity: 0.5;
            z-index: -1;
        }

        .voice-icon {
            background: var(--primary-gradient);
        }

        .vision-icon {
            background: var(--secondary-gradient);
        }

        .module-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .module-description {
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            line-height: 1.7;
            font-size: 1.1rem;
        }

        .module-features {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .feature-tag {
            background: var(--bg-secondary);
            color: var(--text-secondary);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            border: 1px solid var(--border-color);
        }

        .module-btn {
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            background: var(--primary-gradient);
            color: white;
            padding: 1.25rem 2.5rem;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }

        .module-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .module-btn:hover::before {
            left: 100%;
        }

        .module-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .module-btn:focus {
            outline: 3px solid rgba(102, 126, 234, 0.5);
            outline-offset: 2px;
        }

        .module-btn:active {
            transform: translateY(-1px);
        }

        /* Stats Section */
        .stats-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 3rem;
            text-align: center;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 3rem;
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }

        .stats-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.15);
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Footer */
        .footer {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            padding: 2rem;
            margin-top: 4rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 1rem;
        }

        .footer-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }

        .footer-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .footer-link:focus {
            outline: 2px solid rgba(255, 255, 255, 0.5);
            outline-offset: 2px;
        }

        /* Loading States */
        .loading {
            opacity: 0.7;
            pointer-events: none;
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Focus Management */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 6px;
            background: var(--primary-gradient);
            color: white;
            padding: 8px;
            text-decoration: none;
            border-radius: 4px;
            z-index: 10000;
            transition: top 0.3s;
        }

        .skip-link:focus {
            top: 6px;
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

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .shimmer-effect {
            background: linear-gradient(90deg, 
                rgba(255,255,255,0.1) 0%, 
                rgba(255,255,255,0.3) 50%, 
                rgba(255,255,255,0.1) 100%);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }

        /* Screen Reader Only */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Enhanced Focus Indicators */
        *:focus {
            outline: 2px solid transparent;
        }

        .module-card:focus-within {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .modules-grid {
                grid-template-columns: 1fr;
            }
            
            .hero-stats {
                flex-direction: column;
                gap: 2rem;
            }
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }
            
            .navbar {
                padding: 1rem;
            }
            
            .nav-content {
                flex-direction: column;
                gap: 1rem;
            }
            
            .user-menu {
                flex-direction: column;
                gap: 1rem;
            }
            
            .module-card {
                padding: 2rem;
            }
            
            .stats-section {
                padding: 2rem;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .module-card {
                padding: 1.5rem;
            }
            
            .module-icon {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <nav class="navbar" role="navigation" aria-label="Main navigation">
        <div class="nav-content">
            <a href="#" class="logo" aria-label="Medical AI - Return to homepage">
                <i class="fas fa-heartbeat" aria-hidden="true"></i>
                <span class="logo-text">Medical AI</span>
            </a>
            <div class="user-menu">
                <div class="user-info" role="status" aria-live="polite">
                    <div class="user-avatar" aria-hidden="true">
                        <?php echo isset($_SESSION['name']) ? strtoupper(substr($_SESSION['name'], 0, 1)) : 'U'; ?>
                    </div>
                    <span>Welcome, <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'User'; ?></span>
                </div>
                <a href="logout.php" class="logout-btn" aria-label="Sign out of your account">
                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <main id="main-content" class="main-container" role="main">
        <header class="hero-section">
            <h1 class="hero-title">Medical AI Document Assistant</h1>
            <p class="hero-subtitle">
                Transform your medical workflow with cutting-edge AI-powered voice transcription and vision recognition. 
                Streamline documentation, enhance accuracy, and save valuable time for healthcare professionals.
            </p>
            
            <div class="hero-stats" role="region" aria-label="Platform statistics">
                <div class="hero-stat">
                    <div class="hero-stat-number" aria-label="99.9 percent">99.9%</div>
                    <div class="hero-stat-label">Accuracy Rate</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number">24/7</div>
                    <div class="hero-stat-label">Availability</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number">HIPAA</div>
                    <div class="hero-stat-label">Compliant</div>
                </div>
            </div>
        </header>

        <section class="modules-grid" role="region" aria-labelledby="modules-heading">
            <h2 id="modules-heading" class="sr-only">Available AI Modules</h2>
            
            <article class="module-card">
                <div class="module-icon voice-icon" aria-hidden="true">
                    <i class="fas fa-microphone"></i>
                </div>
                <h3 class="module-title">Medical Voice Transcription</h3>
                <p class="module-description">
                    Advanced AI-powered speech-to-text conversion for medical dictations, patient consultations, 
                    and clinical notes. Achieve exceptional accuracy with our state-of-the-art transcription engine.
                </p>
                
                <div class="module-features" role="list" aria-label="Voice module features">
                    <span class="feature-tag" role="listitem">Real-time Processing</span>
                    <span class="feature-tag" role="listitem">Multi-language Support</span>
                    <span class="feature-tag" role="listitem">Medical Terminology</span>
                </div>
                
                <a href="medicalvoice/index.php" class="module-btn" aria-describedby="voice-desc">
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    Launch Voice Converter
                </a>
                <div id="voice-desc" class="sr-only">Access the medical voice transcription module</div>
            </article>

            <article class="module-card">
                <div class="module-icon vision-icon" aria-hidden="true">
                    <i class="fas fa-eye"></i>
                </div>
                <h3 class="module-title">Medical Vision Recognition</h3>
                <p class="module-description">
                    Extract text from handwritten prescriptions, medical forms, and image-based documents 
                    using cutting-edge OCR technology and AI analysis for unparalleled accuracy.
                </p>
                
                <div class="module-features" role="list" aria-label="Vision module features">
                    <span class="feature-tag" role="listitem">OCR Technology</span>
                    <span class="feature-tag" role="listitem">Handwriting Recognition</span>
                    <span class="feature-tag" role="listitem">Document Analysis</span>
                </div>
                
                <a href="medicalvision/index.php" class="module-btn" aria-describedby="vision-desc">
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    Launch Vision Analyzer
                </a>
                <div id="vision-desc" class="sr-only">Access the medical vision recognition module</div>
            </article>
        </section>

        <section class="stats-section" role="region" aria-labelledby="stats-heading">
            <h3 id="stats-heading" class="stats-title">Platform Excellence</h3>
            <div class="stats-grid" role="list" aria-label="Platform performance metrics">
                <div class="stat-item" role="listitem">
                    <div class="stat-number" aria-label="2 modules">2</div>
                    <div class="stat-label">AI-Powered Modules</div>
                </div>
                <div class="stat-item" role="listitem">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Global Availability</div>
                </div>
                <div class="stat-item" role="listitem">
                    <div class="stat-number" aria-label="99.9 percent">99.9%</div>
                    <div class="stat-label">Uptime Guarantee</div>
                </div>
                <div class="stat-item" role="listitem">
                    <div class="stat-number">Secure</div>
                    <div class="stat-label">Enterprise Grade</div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer" role="contentinfo">
        <div class="footer-content">
            <nav class="footer-links" role="navigation" aria-label="Footer navigation">
                <a href="#" class="footer-link">Privacy Policy</a>
                <a href="#" class="footer-link">Terms of Service</a>
                <a href="#" class="footer-link">Support</a>
                <a href="#" class="footer-link">Contact</a>
            </nav>
            <p>&copy; 2025 Medical AI. All rights reserved. Professional healthcare solutions.</p>
        </div>
    </footer>

    <script>
        // Enhanced accessibility and interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Navbar scroll effect
            const navbar = document.querySelector('.navbar');
            let lastScrollY = window.scrollY;

            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // Enhanced button interactions
            const moduleButtons = document.querySelectorAll('.module-btn');
            moduleButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    // Add loading state
                    this.classList.add('loading');
                    
                    // Announce to screen readers
                    const announcement = document.createElement('div');
                    announcement.setAttribute('aria-live', 'polite');
                    announcement.setAttribute('aria-atomic', 'true');
                    announcement.className = 'sr-only';
                    announcement.textContent = 'Loading module, please wait...';
                    document.body.appendChild(announcement);
                    
                    // Clean up after navigation
                    setTimeout(() => {
                        document.body.removeChild(announcement);
                    }, 1000);
                });
            });

            // Enhanced keyboard navigation
            document.addEventListener('keydown', function(e) {
                // ESC key to remove focus from current element
                if (e.key === 'Escape') {
                    document.activeElement.blur();
                }
            });

            // Announce page load completion to screen readers
            const pageLoadAnnouncement = document.createElement('div');
            pageLoadAnnouncement.setAttribute('aria-live', 'polite');
            pageLoadAnnouncement.setAttribute('aria-atomic', 'true');
            pageLoadAnnouncement.className = 'sr-only';
            pageLoadAnnouncement.textContent = 'Medical AI Dashboard loaded successfully';
            document.body.appendChild(pageLoadAnnouncement);
            
            setTimeout(() => {
                document.body.removeChild(pageLoadAnnouncement);
            }, 2000);

            // Preconnect to module domains for faster loading
            const preconnectLinks = [
                'medicalvoice/index.php',
                'medicalvision/index.php'
            ];
            
            preconnectLinks.forEach(link => {
                const linkEl = document.createElement('link');
                linkEl.rel = 'preconnect';
                linkEl.href = link;
                document.head.appendChild(linkEl);
            });
        });

        // Performance monitoring
        window.addEventListener('load', function() {
            // Log performance metrics (for development)
            if ('performance' in window) {
                const perfData = window.performance.timing;
                const loadTime = perfData.loadEventEnd - perfData.navigationStart;
                console.log('Page load time:', loadTime + 'ms');
            }
        });
    </script>
</body>
</html>
