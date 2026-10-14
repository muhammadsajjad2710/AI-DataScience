<?php
include('database.php');
include('header.php');

// Only clear when explicitly requested
if (isset($_GET['clear_session']) && $_GET['clear_session'] === '1') {
	unset($_SESSION['uploaded_file'], $_SESSION['log_file'], $_SESSION['json_file'], $_SESSION['processing_complete']);
}

$userId = $_SESSION['member_id'];
$stmt = $conn->prepare("SELECT credits FROM members WHERE member_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($credits);
$stmt->fetch();
$stmt->close();

// Debug: Check session data (remove in production)
if (isset($_GET['debug']) && $_GET['debug'] === '1') {
    echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc;'>";
    echo "<h4>Session Debug Info:</h4>";
    echo "<p><strong>Uploaded File:</strong> " . (isset($_SESSION['uploaded_file']) ? $_SESSION['uploaded_file'] : 'Not set') . "</p>";
    echo "<p><strong>File Exists:</strong> " . (isset($_SESSION['uploaded_file']) && file_exists($_SESSION['uploaded_file']) ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>Log File:</strong> " . (isset($_SESSION['log_file']) ? $_SESSION['log_file'] : 'Not set') . "</p>";
    echo "<p><strong>JSON File:</strong> " . (isset($_SESSION['json_file']) ? $_SESSION['json_file'] : 'Not set') . "</p>";
    echo "</div>";
}
?>

<!-- Enhanced Dashboard with Modern UI -->
<div class="dashboard-container">
    <!-- Hero Section -->
    <div class="hero-section text-center py-5 mb-5">
        <div class="hero-content">
            <h1 class="hero-title mb-3">
                <i class="fas fa-brain text-primary me-3"></i>
                Chief AI Medical Support System
            </h1>
            <p class="hero-subtitle text-muted fs-5">
                Advanced AI-powered medical audio processing with professional transcription and analysis
            </p>
        </div>
    </div>

    <!-- Credits Display Card -->
    <div class="credits-card mb-5">
        <div class="credits-content">
            <div class="credits-info">
                <div class="credits-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="credits-details">
                    <h3 class="credits-label">Available Credits</h3>
                    <div class="credits-balance"><?php echo $credits; ?></div>
                    <p class="credits-description">Credits remaining for AI processing</p>
                </div>
            </div>
            <div class="credits-action">
                <a href="buy_credits.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Buy Credits
                </a>
            </div>
        </div>
    </div>

    <!-- Main Features Grid -->
    <div class="features-grid">
        <!-- Upload Section -->
        <div class="feature-card" id="upload">
            <div class="feature-header">
                <div class="feature-icon upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div class="feature-info">
                    <h3 class="feature-title">Audio Upload</h3>
                    <p class="feature-description">Upload medical audio files for AI processing</p>
                </div>
            </div>
            <div class="feature-content">
                <?php include('upload.php'); ?>
            </div>
        </div>

        <!-- Convert Section -->
        <div class="feature-card" id="convert">
            <div class="feature-header">
                <div class="feature-icon convert-icon">
                    <i class="fas fa-microphone"></i>
                </div>
                <div class="feature-info">
                    <h3 class="feature-title">AI Processing</h3>
                    <p class="feature-description">Convert speech to text and analyze with AI</p>
                </div>
            </div>
            <div class="feature-content">
                <?php include('python_process.php'); ?>
            </div>
        </div>
    </div>

    <!-- Quick Stats Section -->
    <div class="stats-section mt-5">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h4 class="stat-number">99.9%</h4>
                    <p class="stat-label">Accuracy Rate</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h4 class="stat-number">24/7</h4>
                    <p class="stat-label">Availability</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="stat-content">
                    <h4 class="stat-number">HIPAA</h4>
                    <p class="stat-label">Compliant</p>
                </div>
            </div>
        </div>
    </div>


</div>

<!-- Enhanced CSS for Modern UI -->
<style>
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    color: white;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-title {
    font-size: 3rem;
    font-weight: 800;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.hero-subtitle {
    font-weight: 300;
    opacity: 0.9;
}

/* Credits Card */
.credits-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.credits-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 2rem;
}

.credits-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.credits-icon {
    font-size: 3rem;
    opacity: 0.9;
}

.credits-label {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
}

.credits-balance {
    font-size: 3rem;
    font-weight: 800;
    margin: 0.5rem 0;
}

.credits-description {
    margin: 0;
    opacity: 0.9;
}

.credits-action .btn {
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.3);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.credits-action .btn:hover {
    background: rgba(255,255,255,0.3);
    border-color: rgba(255,255,255,0.5);
    transform: translateY(-2px);
}

/* Features Grid */
.features-grid {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    margin-bottom: 3rem;
}

.feature-card {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
    animation: fadeInUp 0.6s ease forwards;
}

.feature-card:nth-child(1) {
    animation-delay: 0.1s;
}

.feature-card:nth-child(2) {
    animation-delay: 0.3s;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
}

.feature-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 2rem 2rem 1.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.feature-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    box-shadow: var(--shadow-md);
}

.upload-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.convert-icon {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.feature-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    color: #2d3748;
}

.feature-description {
    margin: 0.5rem 0 0;
    color: #718096;
    font-size: 0.95rem;
}

.feature-content {
    padding: 1.5rem 2rem 2rem;
}

/* Stats Section */
.stats-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 3rem 2rem;
    color: white;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    text-align: center;
}

.stat-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    font-size: 2.5rem;
    opacity: 0.9;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    margin: 0;
}

.stat-label {
    margin: 0;
    opacity: 0.9;
    font-size: 1.1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .credits-content {
        flex-direction: column;
        text-align: center;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}

/* Animation Classes */
.feature-card {
    animation: fadeInUp 0.6s ease forwards;
}

.feature-card:nth-child(1) {
    animation-delay: 0.1s;
}

.feature-card:nth-child(2) {
    animation-delay: 0.3s;
}

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

/* Enhanced Button Styles */
.btn {
    border-radius: 12px;
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
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}
</style>

<?php include('footer.php'); ?>

<script>
// Always reset client-side UI sections on load so the page is fresh after refresh
document.addEventListener('DOMContentLoaded', function() {
    // Clear any stored file data in JavaScript
    if (typeof selectedFile !== 'undefined') {
        selectedFile = null;
    }

    // Reset file input if exists
    const fileInput = document.getElementById('audioFile');
    if (fileInput) {
        fileInput.value = '';
    }

    // Hide any preview sections
    const filePreview = document.getElementById('filePreview');
    if (filePreview) {
        filePreview.style.display = 'none';
    }

    // Show upload area
    const uploadArea = document.querySelector('.upload-area');
    if (uploadArea) {
        uploadArea.style.display = 'block';
    }

    // Clear any progress indicators
    const progressElements = document.querySelectorAll('.processing-progress, .upload-progress');
    progressElements.forEach(el => {
        el.style.display = 'none';
    });

    // Clear any success/error messages
    const messageElements = document.querySelectorAll('.upload-success, .upload-error, .processing-success, .processing-error');
    messageElements.forEach(el => {
        el.remove();
    });
});
</script>
