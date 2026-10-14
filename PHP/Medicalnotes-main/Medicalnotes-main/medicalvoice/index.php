<?php
// Start session early for proper authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    header("Location: ../login.php");
    exit;
}

include('database.php');
include('header.php');

// Include patient functions
require_once 'includes/patient_functions.php';

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

<!-- Include Patient Selector Component -->
<?php include 'patient_selector_simple.php'; ?>

<!-- Professional Dashboard with World-Class UI -->
<div class="dashboard-container">
    <!-- Hero Section -->
    <section class="hero-section" role="banner">
        <div class="hero-content">
            <h1 class="hero-title">Chief.AI MedicalVoice</h1>
            <p class="hero-subtitle">
                Transform your medical workflow with cutting-edge AI-powered voice transcription. 
                Built by Chief.AI - where intelligence meets innovation.
            </p>
            
            <!-- Professional Workflow Progress Tracker -->
            <div class="workflow-progress animate-fade-in-up" role="progressbar" aria-label="MedicalVoice workflow progress">
                <div class="workflow-step active" data-step="upload" role="button" tabindex="0" aria-label="Upload Audio - Ready">
                    <div class="step-icon">
                        <span class="material-icons-round">cloud_upload</span>
                    </div>
                    <div class="step-label">Upload Audio</div>
                    <div class="step-status">Ready</div>
                </div>
                
                <div class="workflow-step" data-step="transcription" role="button" tabindex="0" aria-label="AI Transcription - Pending">
                    <div class="step-icon">
                        <span class="material-icons-round">mic</span>
                    </div>
                    <div class="step-label">AI Transcription</div>
                    <div class="step-status">Pending</div>
                </div>
                
                <div class="workflow-step" data-step="analysis" role="button" tabindex="0" aria-label="Medical Analysis - Pending">
                    <div class="step-icon">
                        <span class="material-icons-round">psychology</span>
                    </div>
                    <div class="step-label">Medical Analysis</div>
                    <div class="step-status">Pending</div>
                </div>
                
                <div class="workflow-step" data-step="results" role="button" tabindex="0" aria-label="Results & Download - Pending">
                    <div class="step-icon">
                        <span class="material-icons-round">insights</span>
                    </div>
                    <div class="step-label">Results & Download</div>
                    <div class="step-status">Pending</div>
                </div>
            </div>
            
            <!-- ARIA Live Region for Status Updates -->
            <div aria-live="polite" aria-atomic="true" class="sr-only" id="statusAnnouncer"></div>
        </div>
    </section>

    <!-- Credits Display -->
    <div class="credits-display animate-fade-in-up">
        <div class="credits-number"><?php echo $credits; ?></div>
        <div class="credits-label">Credits Available</div>
        <a href="../credits.php" class="btn btn-primary">
            <span class="material-icons-round">account_balance_wallet</span>
            Buy Credits
        </a>
    </div>

    <!-- Upload Section -->
    <section id="upload" class="upload-section animate-fade-in-up">
        <div class="upload-area" id="uploadArea">
            <div class="upload-icon">
                <span class="material-icons-round">cloud_upload</span>
            </div>
            <div class="upload-text">Drop your audio file here</div>
            <div class="upload-hint">or click to browse (MP3, WAV, M4A up to 25MB)</div>
            <input type="file" id="audioFile" accept="audio/*" style="display: none;">
        </div>
        
        <!-- Record Audio Button -->
        <div class="record-audio-section" style="margin-top: 20px; text-align: center;">
            <div class="record-audio-divider">
                <span>OR</span>
            </div>
            <button id="recordAudioBtn" class="btn btn-secondary btn-lg record-btn">
                <span class="material-icons-round">mic</span>
                Record Audio
            </button>
            <div id="recordingControls" style="display: none; margin-top: 15px;">
                <button id="startRecordingBtn" class="btn btn-primary">
                    <span class="material-icons-round">fiber_manual_record</span>
                    Start Recording
                </button>
                <button id="stopRecordingBtn" class="btn btn-danger btn-lg" style="display: none;">
                    <span class="material-icons-round">stop</span>
                    Stop Recording
                </button>
                <div id="recordingStatus" style="margin-top: 10px; font-size: 14px; color: #666;"></div>
            </div>
        </div>
        
        <!-- Enhanced Upload Progress -->
        <div class="upload-progress" id="uploadProgress" style="display: none;">
            <div class="progress-header">
                <h5 class="progress-title">
                    <span class="material-icons-round">upload</span>
                    Uploading Audio File
                </h5>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar">
                    <div class="progress-fill" id="uploadProgressFill" style="width: 0%"></div>
                </div>
                <div class="progress-percentage" id="uploadProgressPercentage">0%</div>
            </div>
            <div class="progress-message" id="uploadProgressMessage">Preparing upload...</div>
        </div>
    </section>

    <?php
    // Include document module
    require_once 'document_module.php';
    $documentModule = new DocumentModule();
    echo $documentModule->renderUploadSection();
    ?>

    <!-- Feature Cards -->
    <section class="features-section animate-fade-in-up">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons-round">mic</span>
                </div>
                <h3 class="feature-title">High Accuracy</h3>
                <p class="feature-description">
                    State-of-the-art speech recognition with medical terminology support
                </p>
                <div class="feature-meta">
                    <span class="meta-item">
                        <span class="material-icons-round">check_circle</span>
                        99.5% Accuracy
                    </span>
                </div>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons-round">security</span>
                </div>
                <h3 class="feature-title">HIPAA Compliant</h3>
                <p class="feature-description">
                    Enterprise-grade security for your sensitive medical data
                </p>
                <div class="feature-meta">
                    <span class="meta-item">
                        <span class="material-icons-round">verified</span>
                        HIPAA Certified
                    </span>
                </div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons-round">speed</span>
                </div>
                <h3 class="feature-title">Fast Processing</h3>
                <p class="feature-description">
                    Quick turnaround times for urgent medical documentation needs
                </p>
                <div class="feature-meta">
                    <span class="meta-item">
                        <span class="material-icons-round">timer</span>
                        < 2 min avg
                    </span>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Professional Dashboard Styles */
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Premium Hero Section */
.hero-section {
    background: linear-gradient(135deg, var(--neutral-50) 0%, var(--primary-50) 100%);
    padding: var(--space-16) var(--space-6);
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-bottom: var(--space-8);
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%23DBEAFE" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.hero-content {
    position: relative;
    z-index: 1;
    max-width: 800px;
    margin: 0 auto;
}

.hero-title {
    font-size: 3rem;
    font-weight: 800;
    color: var(--neutral-900);
    margin-bottom: var(--space-4);
    line-height: 1.1;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: fadeInUp 1s ease-out;
}

.hero-subtitle {
    font-size: 1.25rem;
    color: var(--neutral-600);
    max-width: 600px;
    margin: 0 auto var(--space-6);
    line-height: 1.6;
    animation: fadeInUp 1s ease-out 0.2s both;
}

/* Premium Upload Area */
.upload-area {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 2px dashed var(--primary-200);
    border-radius: var(--radius-2xl);
    padding: var(--space-12);
    text-align: center;
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
}

.upload-area::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--primary-50), var(--secondary-50));
    opacity: 0;
    transition: opacity var(--transition-normal);
}

.upload-area:hover::before {
    opacity: 0.3;
}

.upload-area:hover {
    border-color: var(--primary);
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

.upload-area.dragover {
    border-color: var(--accent);
    background: var(--accent-50);
    transform: scale(1.02);
}



.upload-icon {
    font-size: 4rem;
    color: var(--primary);
    margin-bottom: var(--space-4);
    transition: color var(--transition-normal);  // Only transition the color
    transform: none !important;  // Prevent any transformations
}


.upload-area:hover .upload-icon {
    color: var(--primary-dark);  // Only change the color on hover
    transform: none !important;  // Ensure no tilting or scaling
}

.upload-text {
    font-size: 1.25rem;
    color: var(--neutral-700);
    margin-bottom: var(--space-4);
    font-weight: 600;
}

.upload-hint {
    color: var(--neutral-500);
    margin-bottom: var(--space-6);
    font-size: 0.875rem;
}

/* Record Audio Section Styles */
.record-audio-section {
    margin-top: var(--space-6);
}

/* Transcription ready styles */
.transcription-ready, .download-ready {
    text-align: center;
    padding: var(--space-8);
    background: rgba(255, 255, 255, 0.95);
    border-radius: var(--radius-2xl);
    box-shadow: var(--shadow-lg);
}

.success-icon {
    margin-bottom: var(--space-4);
    animation: bounceIn 0.6s ease-out;
}

@keyframes bounceIn {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}

/* Spinning animation for processing */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.spinning {
    animation: spin 1s linear infinite;
}

.record-audio-divider {
    position: relative;
    text-align: center;
    margin: var(--space-4) 0;
}

.record-audio-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: var(--neutral-300);
}

.record-audio-divider span {
    background: white;
    padding: 0 var(--space-4);
    color: var(--neutral-500);
    font-size: 0.875rem;
    font-weight: 500;
}

/* Record button styles are now in unified-styles.css */

#recordingControls {
    margin-top: var(--space-4);
}

#startRecordingBtn {
    background: var(--primary-500);
    color: white;
    border: none;
    padding: var(--space-3) var(--space-6);
    border-radius: var(--radius-full);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-bounce);
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
}

#startRecordingBtn:hover {
    background: var(--primary-600);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

#stopRecordingBtn {
    background: #EF4444;
    color: white;
    border: none;
    padding: var(--space-3) var(--space-6);
    border-radius: var(--radius-full);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-bounce);
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
}

#stopRecordingBtn:hover {
    background: #DC2626;
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

#recordingStatus {
    margin-top: var(--space-3);
    font-size: 0.875rem;
    color: var(--neutral-600);
}

.recording-active {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

/* Premium Feature Cards */
.feature-card {
    background: var(--neutral-50);
    border-radius: var(--radius-2xl);
    padding: var(--space-6);
    border: 1px solid var(--neutral-200);
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    height: 280px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    transform: scaleX(0);
    transition: transform var(--transition-bounce);
}

.feature-card:hover::before {
    transform: scaleX(1);
}

.feature-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-100);
}

.feature-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-bottom: var(--space-4);
    transition: all var(--transition-bounce);
}

.feature-card:hover .feature-icon {
    transform: scale(1.1) rotate(5deg);
    box-shadow: var(--shadow-lg);
}

.feature-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--neutral-900);
    margin-bottom: var(--space-3);
    transition: color var(--transition-normal);
}

.feature-card:hover .feature-title {
    color: var(--primary);
}

.feature-description {
    color: var(--neutral-600);
    line-height: 1.6;
    flex-grow: 1;
}

.feature-meta {
    margin-top: var(--space-4);
    padding-top: var(--space-3);
    border-top: 1px solid var(--neutral-200);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    color: var(--accent-700);
    font-size: var(--text-sm);
    font-weight: 500;
}

.meta-item .material-icons-round {
    font-size: 1rem;
    color: var(--accent-500);
}

/* Professional Workflow Progress Tracker */
.workflow-progress {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: var(--space-8) auto;
    max-width: 800px;
    position: relative;
    z-index: 2;
}

.workflow-progress::before {
    content: '';
    position: absolute;
    top: 30px;
    left: 40px;
    right: 40px;
    height: 2px;
    background: var(--neutral-300);
    z-index: -1;
}

.workflow-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-2);
    position: relative;
    transition: all var(--transition-normal);
}

.step-icon {
    width: 60px;
    height: 60px;
    background: var(--neutral-100);
    border: 2px solid var(--neutral-300);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--neutral-500);
    font-size: 1.5rem;
    transition: all var(--transition-bounce);
    position: relative;
    z-index: 2;
}

.workflow-step.active .step-icon {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
    box-shadow: var(--shadow-lg);
}

.workflow-step.completed .step-icon {
    background: var(--accent);
    border-color: var(--accent);
    color: white;
}

.workflow-step.completed .step-icon::after {
    content: '✓';
    position: absolute;
    top: -5px;
    right: -5px;
    background: var(--accent);
    color: white;
    border-radius: var(--radius-full);
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
}

.step-label {
    font-size: var(--text-sm);
    font-weight: 600;
    color: var(--neutral-700);
    text-align: center;
    max-width: 100px;
}

.step-status {
    font-size: var(--text-xs);
    color: var(--neutral-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 500;
}

.workflow-step.active .step-label {
    color: var(--primary);
}

.workflow-step.active .step-status {
    color: var(--primary);
}

/* Enhanced Upload Progress */
.upload-progress {
    background: var(--neutral-50);
    border-radius: var(--radius-xl);
    padding: var(--space-6);
    margin-top: var(--space-6);
    border: 1px solid var(--neutral-200);
    box-shadow: var(--shadow-sm);
}

.progress-header {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    margin-bottom: var(--space-4);
}

.progress-title {
    font-size: var(--text-lg);
    font-weight: 600;
    color: var(--neutral-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.progress-title .material-icons-round {
    color: var(--primary);
}

.progress-bar-container {
    position: relative;
    margin-bottom: var(--space-4);
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: var(--neutral-200);
    border-radius: var(--radius-full);
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), var(--primary-dark));
    border-radius: var(--radius-full);
    transition: width var(--transition-normal);
    position: relative;
}

.progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.progress-percentage {
    position: absolute;
    top: -25px;
    right: 0;
    background: var(--primary);
    color: white;
    padding: var(--space-1) var(--space-2);
    border-radius: var(--radius-sm);
    font-size: var(--text-sm);
    font-weight: 600;
}

.progress-message {
    color: var(--neutral-600);
    font-size: var(--text-sm);
    text-align: center;
}

/* Premium Credits Display */
.credits-display {
    background: linear-gradient(135deg, var(--primary-50), var(--secondary-50));
    border-radius: var(--radius-2xl);
    padding: var(--space-6);
    text-align: center;
    border: 1px solid var(--primary-100);
    box-shadow: var(--shadow-md);
    margin-bottom: var(--space-8);
}

.credits-number {
    font-size: 3rem;
    font-weight: 800;
    color: var(--primary);
    margin-bottom: var(--space-2);
}

.credits-label {
    color: var(--neutral-600);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-size: 0.875rem;
    margin-bottom: var(--space-4);
}

/* Premium Animations */
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

.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
}
        
/* CSS Variables for Consistency */
:root {
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-normal: all 0.3s ease;
    --transition-bounce: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    --transition-slow: all 0.5s ease;
    --space-1: 0.25rem;
    --space-2: 0.5rem;
    --space-3: 0.75rem;
    --space-4: 1rem;
    --space-6: 1.5rem;
    --space-8: 2rem;
    --space-12: 3rem;
    --space-16: 4rem;
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
    --radius-2xl: 1.5rem;
    --radius-full: 9999px;
    --text-xs: 0.75rem;
    --text-sm: 0.875rem;
    --text-base: 1rem;
    --text-lg: 1.125rem;
    --text-xl: 1.25rem;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --neutral-400: #9CA3AF;
}

/* Features Grid */
.features-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
}

/* Feature Cards */
.feature-card {
    background: var(--bg-white);
    border-radius: 1rem;
    border: 1px solid var(--border-muted);
    overflow: hidden;
    transition: var(--transition);
    box-shadow: var(--shadow-sm);
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary);
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary);
}

.feature-header {
    background: var(--bg-gray-50);
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-muted);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.feature-icon {
    width: 80px;
    height: 80px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0;
    font-size: 2rem;
    color: white;
    transition: var(--transition);
}

.upload-icon {
    background: var(--primary);
}

.convert-icon {
    background: var(--secondary);
}

.feature-card:hover .feature-icon {
    transform: scale(1.1);
}

.feature-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.feature-description {
    color: var(--text-muted);
    margin: 0;
    line-height: 1.6;
}

.feature-content {
    padding: 1.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .credits-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .credits-info {
        flex-direction: column;
        text-align: center;
    }
    
    .feature-header {
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
    }
    
    /* Mobile Workflow Progress */
    .workflow-progress {
        flex-direction: column;
        gap: var(--space-4);
        max-width: 100%;
        margin: var(--space-6) auto;
    }
    
    .workflow-progress::before {
        display: none; /* Hide connecting line on mobile */
    }
    
    .workflow-step {
        flex-direction: row;
        gap: var(--space-3);
        width: 100%;
        justify-content: flex-start;
        padding: var(--space-3);
        background: var(--neutral-50);
        border-radius: var(--radius-lg);
        border: 1px solid var(--neutral-200);
    }
    
    .step-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .step-label {
        font-size: var(--text-base);
        font-weight: 600;
        text-align: left;
        max-width: none;
    }
    
    .step-status {
        margin-left: auto;
        font-size: var(--text-xs);
        padding: var(--space-1) var(--space-2);
        background: var(--neutral-200);
        border-radius: var(--radius-sm);
        color: var(--neutral-600);
    }
    
    /* Mobile Feature Cards */
    .features-grid {
        grid-template-columns: 1fr;
        gap: var(--space-4);
    }
    
    .feature-card {
        height: auto;
        min-height: 200px;
    }
    
    /* Mobile Upload Area */
    .upload-area {
        padding: var(--space-6);
        margin: 0 var(--space-2);
    }
    
    .upload-icon {
        font-size: 3rem;
    }
    
    .upload-text {
        font-size: 1.125rem;
    }
    
    .upload-hint {
        font-size: 0.875rem;
    }
    
    /* Mobile Progress Bars */
    .upload-progress {
        margin: var(--space-4) var(--space-2);
        padding: var(--space-4);
    }
    
    .progress-title {
        font-size: var(--text-base);
    }
}

@media (max-width: 480px) {
    .hero-title {
        font-size: 1.75rem;
    }
    
    .credits-number {
        font-size: 2.5rem;
    }
    
    .upload-area {
        padding: var(--space-4);
    }
    
    .feature-card {
        margin: 0 var(--space-2);
    }
    
    .workflow-step {
        padding: var(--space-2);
    }
    
    .step-label {
        font-size: var(--text-sm);
    }
    
    .notification {
        right: 10px;
        left: 10px;
        max-width: none;
        min-width: auto;
    }
}

/* Enhanced Button Styles */
.btn {
    border-radius: 9999px; /* pill style */
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: var(--transition);
    border: none;
    position: relative;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    font-size: 1rem;
    cursor: pointer;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn:focus {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}
        
.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}
        
.btn-outline-primary {
    background: transparent;
    border: 2px solid var(--primary);
    color: var(--primary);
}
        
.btn-outline-primary:hover {
    background: var(--primary);
    color: white;
}

/* Loading States */
.loading {
    opacity: 0.7;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid rgba(37, 99, 235, 0.3);
    border-top: 2px solid var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Accessibility */
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

/* Focus Management */
*:focus {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}

/* High Contrast Mode Support */
@media (prefers-contrast: high) {
    .feature-card {
        border-width: 2px;
    }
    
    .btn {
        border-width: 2px;
    }
    
    .workflow-step {
        border-width: 2px;
    }
    
    .progress-bar {
        border: 2px solid var(--neutral-400);
    }
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
    
    .workflow-step:hover {
        transform: none;
    }
    
    .feature-card:hover {
        transform: none;
    }
}

        /* Features Grid Layout */
        .features-section {
            margin-top: var(--space-12);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--space-6);
            margin-top: var(--space-8);
        }

        /* Upload Section Styling */
        .upload-section {
            margin: var(--space-8) 0;
        }

        /* Enhanced Button Styling */
        .btn {
            padding: var(--space-3) var(--space-6);
            border-radius: var(--radius-full);
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-bounce);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: var(--space-2);
            font-size: 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left var(--transition-slow);
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-xl);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: var(--space-4);
            }
            
            .upload-area {
                padding: var(--space-8);
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .credits-number {
                font-size: 2.5rem;
            }
            
            .upload-area {
                padding: var(--space-6);
            }
        }

        /* CSS Variables for consistent styling */
        :root {
            --radius-lg: 12px;
            --space-3: 12px;
            --space-4: 16px;
            --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --primary: #007bff;
            --transition-bounce: 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        /* Premium Notifications */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: var(--radius-lg);
            padding: var(--space-4);
            box-shadow: var(--shadow-xl);
            border-left: 4px solid var(--primary);
            display: flex;
            align-items: center;
            gap: var(--space-3);
            z-index: 10000;
            transform: translateX(400px);
            transition: transform var(--transition-bounce);
            max-width: 400px;
            min-width: 300px;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification-success {
            border-left-color: var(--accent);
        }

        .notification-error {
            border-left-color: #EF4444;
        }

        .notification .material-icons-round {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .notification-success .material-icons-round {
            color: var(--accent);
        }

        .notification-error .material-icons-round {
            color: #EF4444;
        }
        
        .notification .btn-sm {
            margin-left: auto;
            flex-shrink: 0;
        }

        /* Premium Spinner */
        .premium-spinner {
            display: inline-block;
            position: relative;
            width: 40px;
            height: 40px;
            margin-bottom: var(--space-4);
        }

        .spinner-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 3px solid transparent;
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        }

        .spinner-ring:nth-child(1) {
            animation-delay: -0.45s;
        }

        .spinner-ring:nth-child(2) {
            animation-delay: -0.3s;
        }

        .spinner-ring:nth-child(3) {
            animation-delay: -0.15s;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
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

<script>
        // Premium Chief.AI MedicalVoice Interactions
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('uploadArea');
            const audioFile = document.getElementById('audioFile');
            const recordAudioBtn = document.getElementById('recordAudioBtn');
            const recordingControls = document.getElementById('recordingControls');
            const startRecordingBtn = document.getElementById('startRecordingBtn');
            const stopRecordingBtn = document.getElementById('stopRecordingBtn');
            const recordingStatus = document.getElementById('recordingStatus');
            
            // Initialize accessibility enhancements
            enhanceAccessibility();
            
            // Enhanced drag and drop functionality
            uploadArea.addEventListener('click', () => audioFile.click());
            
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });
            
            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileUpload(files[0]);
                }
            });
            
            audioFile.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFileUpload(e.target.files[0]);
                }
            });
            
            // Record Audio Functionality
            let mediaRecorder = null;
            let audioChunks = [];
            let recordingStartTime = null;
            
            // Test microphone functionality with comprehensive debugging
            function testMicrophone() {
                console.log('=== MICROPHONE TEST DEBUG START ===');
                console.log('1. Testing microphone access...');
                
                const audioConstraints = {
                    audio: {
                        echoCancellation: false,
                        noiseSuppression: false,
                        autoGainControl: false,
                        sampleRate: 44100,
                        channelCount: 1
                    }
                };
                
                console.log('2. Audio constraints:', audioConstraints);
                
                navigator.mediaDevices.getUserMedia(audioConstraints)
                    .then(stream => {
                        console.log('3. Microphone access granted');
                        console.log('3a. Stream object:', stream);
                        console.log('3b. Stream active:', stream.active);
                        
                        const audioTracks = stream.getAudioTracks();
                        console.log('3c. Audio tracks:', audioTracks.length);
                        
                        // Get track settings
                        const audioTrack = audioTracks[0];
                        if (audioTrack) {
                            console.log('4. Audio track details:');
                            console.log('4a. Track enabled:', audioTrack.enabled);
                            console.log('4b. Track muted:', audioTrack.muted);
                            console.log('4c. Track readyState:', audioTrack.readyState);
                            console.log('4d. Track settings:', audioTrack.getSettings());
                            console.log('4e. Track capabilities:', audioTrack.getCapabilities());
                        }
                        
                        // Stop the test stream
                        console.log('5. Stopping test stream...');
                        stream.getTracks().forEach(track => {
                            console.log('5a. Stopping track:', track.kind, track.id);
                            track.stop();
                        });
                        
                        console.log('6. Microphone test completed successfully');
                        showNotification('Microphone test successful! Device found and accessible.', 'success');
                    })
                    .catch(err => {
                        console.error('ERROR: Microphone test failed:', err);
                        console.error('Error name:', err.name);
                        console.error('Error message:', err.message);
                        showNotification('Microphone test failed: ' + err.message, 'error');
                    });
            }
            
            // Add test button functionality
            recordAudioBtn.addEventListener('click', () => {
                recordingControls.style.display = 'block';
                recordAudioBtn.style.display = 'none';
                
                // Add a test button for debugging
                const testBtn = document.createElement('button');
                testBtn.textContent = 'Test Microphone';
                testBtn.className = 'btn btn-sm btn-outline-primary';
                testBtn.style.marginTop = '10px';
                testBtn.onclick = testMicrophone;
                recordingControls.appendChild(testBtn);
            });
            
            startRecordingBtn.addEventListener('click', startRecording);
            stopRecordingBtn.addEventListener('click', stopRecording);
            
            function startRecording() {
                console.log('=== MEDICALVOICE RECORDING DEBUG START ===');
                console.log('1. Starting Chrome-optimized recording...');
                
                // Check browser support first
                if (typeof navigator.mediaDevices === 'undefined') {
                    console.error('ERROR: navigator.mediaDevices not supported');
                    showNotification('Browser does not support media devices', 'error');
                    return;
                }
                
                if (typeof MediaRecorder === 'undefined') {
                    console.error('ERROR: MediaRecorder not supported');
                    showNotification('Browser does not support MediaRecorder', 'error');
                    return;
                }
                
                console.log('2. Browser support verified');
                
                // Chrome-optimized audio constraints
                const audioConstraints = {
                    audio: {
                        echoCancellation: false,
                        noiseSuppression: false,
                        autoGainControl: false,
                        sampleRate: 48000,  // Chrome prefers 48kHz
                        channelCount: 1,
                        // Force microphone input
                        deviceId: 'default'
                    }
                };
                
                console.log('3. Audio constraints set:', audioConstraints);
                
                // First enumerate devices to ensure we get the right microphone
                navigator.mediaDevices.enumerateDevices()
                    .then(devices => {
                        console.log('3a. Available devices:', devices.length);
                        
                        const audioInputs = devices.filter(device => device.kind === 'audioinput');
                        console.log('3b. Audio input devices:', audioInputs.length);
                        
                        if (audioInputs.length > 0) {
                            // Use the first available microphone
                            const microphone = audioInputs[0];
                            console.log('3c. Using microphone:', microphone.label || microphone.deviceId);
                            audioConstraints.audio.deviceId = microphone.deviceId;
                        }
                        
                        return navigator.mediaDevices.getUserMedia(audioConstraints);
                    })
                    .then(stream => {
                        console.log('4. getUserMedia SUCCESS - Stream obtained');
                        console.log('4a. Stream object:', stream);
                        console.log('4b. Stream ID:', stream.id);
                        console.log('4c. Stream active:', stream.active);
                        
                        // Check audio tracks
                        const audioTracks = stream.getAudioTracks();
                        console.log('4d. Audio tracks found:', audioTracks.length);
                        
                        if (audioTracks.length === 0) {
                            console.error('ERROR: No audio tracks in stream');
                            showNotification('No audio tracks found in stream', 'error');
                            return;
                        }
                        
                        const audioTrack = audioTracks[0];
                        console.log('4e. First audio track:', audioTrack);
                        console.log('4f. Track enabled:', audioTrack.enabled);
                        console.log('4g. Track muted:', audioTrack.muted);
                        console.log('4h. Track readyState:', audioTrack.readyState);
                        
                        // Get track settings
                        const trackSettings = audioTrack.getSettings();
                        console.log('4i. Track settings:', trackSettings);
                        
                        // Chrome-specific MIME type selection
                        const mimeTypes = [
                            'audio/webm;codecs=opus',
                            'audio/webm',
                            'audio/ogg;codecs=opus',
                            'audio/mp4',
                            'audio/wav'
                        ];
                        
                        let selectedMimeType = null;
                        for (const mimeType of mimeTypes) {
                            if (MediaRecorder.isTypeSupported(mimeType)) {
                                selectedMimeType = mimeType;
                                break;
                            }
                        }
                        
                        if (!selectedMimeType) {
                            console.error('ERROR: No supported MIME type found');
                            showNotification('No supported audio format found', 'error');
                            return;
                        }
                        
                        console.log('5. MIME type support check:', selectedMimeType, '=', true);
                        
                        console.log('6. Creating MediaRecorder...');
                        
                        try {
                            // Chrome-optimized MediaRecorder options
                            const recorderOptions = {
                                mimeType: selectedMimeType,
                                audioBitsPerSecond: 128000
                            };
                            
                            console.log('6a. Recorder options:', recorderOptions);
                            
                            mediaRecorder = new MediaRecorder(stream, recorderOptions);
                            
                            console.log('7. MediaRecorder created successfully');
                            console.log('7a. MediaRecorder state:', mediaRecorder.state);
                            console.log('7b. MediaRecorder mimeType:', mediaRecorder.mimeType);
                            
                            audioChunks = [];
                            recordingStartTime = Date.now();
                            
                            // Set up event handlers with enhanced logging
                            mediaRecorder.ondataavailable = (event) => {
                                console.log('8. ondataavailable triggered');
                                console.log('8a. Event data size:', event.data.size);
                                console.log('8b. Event data type:', event.data.type);
                                console.log('8c. Event data blob:', event.data);
                                
                                if (event.data.size > 0) {
                                    audioChunks.push(event.data);
                                    console.log('8d. Audio chunk added, total chunks:', audioChunks.length);
                                    console.log('8e. Total size so far:', audioChunks.reduce((sum, chunk) => sum + chunk.size, 0));
                                } else {
                                    console.warn('8f. WARNING: Empty data chunk received');
                                }
                            };
                            
                            mediaRecorder.onstop = () => {
                                console.log('9. onstop triggered');
                                console.log('9a. Total chunks collected:', audioChunks.length);
                                console.log('9b. Total size:', audioChunks.reduce((sum, chunk) => sum + chunk.size, 0));
                                
                                if (audioChunks.length === 0) {
                                    console.error('ERROR: No audio chunks collected');
                                    showNotification('No audio data captured. Please try again.', 'error');
                                    resetRecordingUI();
                                    return;
                                }
                                
                                console.log('9c. Creating audio blob...');
                                const audioBlob = new Blob(audioChunks, { type: selectedMimeType });
                                console.log('9d. Audio blob created, size:', audioBlob.size);
                                console.log('9e. Audio blob type:', audioBlob.type);
                                
                                const fileName = `recorded_audio_${Date.now()}.webm`;
                                const file = new File([audioBlob], fileName, { type: selectedMimeType });
                                console.log('9f. File created:', fileName, 'size:', file.size);
                                
                                console.log('9g. Processing audio for upload...');
                                // Try to process the audio directly without conversion first
                                if (audioBlob.size > 0) {
                                    // Directly use the recorded format
                                    const webmFileName = `recorded_audio_${Date.now()}.webm`;
                                    const webmFile = new File([audioBlob], webmFileName, { type: selectedMimeType });
                                    console.log('9h. WebM file created:', webmFileName, 'size:', webmFile.size);

                                    // Upload the WebM file directly
                                    handleFileUpload(webmFile);
                                    resetRecordingUI();
                                } else {
                                    console.error('ERROR: Audio blob is empty');
                                    showNotification('No audio data captured. Please try again.', 'error');
                                    resetRecordingUI();
                                }
                            };
                            
                            mediaRecorder.onerror = (event) => {
                                console.error('ERROR: MediaRecorder error:', event);
                                console.error('Error details:', event.error);
                                showNotification('Recording error occurred: ' + event.error, 'error');
                                resetRecordingUI();
                            };
                            
                            mediaRecorder.onstart = () => {
                                console.log('10. MediaRecorder started successfully');
                                console.log('10a. MediaRecorder state after start:', mediaRecorder.state);
                            };
                            
                            console.log('11. Starting recording...');
                            // Chrome-optimized: use smaller timeslice for better data collection
                            mediaRecorder.start(500); // Collect data every 500ms
                            
                            startRecordingBtn.style.display = 'none';
                            stopRecordingBtn.style.display = 'inline-flex';
                            recordingStatus.textContent = 'Recording... Click Stop when finished.';
                            recordingStatus.classList.add('recording-active');
                            
                            // Update recording time
                            updateRecordingTime();
                            
                            console.log('12. Recording setup complete');
                            
                        } catch (error) {
                            console.error('ERROR: Failed to create MediaRecorder:', error);
                            showNotification('Failed to create recorder: ' + error.message, 'error');
                            resetRecordingUI();
                        }
                    })
                    .catch(err => {
                        console.error('ERROR: getUserMedia failed:', err);
                        console.error('Error name:', err.name);
                        console.error('Error message:', err.message);
                        
                        let errorMessage = 'Unable to access microphone. ';
                        
                        if (err.name === 'NotAllowedError') {
                            errorMessage += 'Please allow microphone access.';
                        } else if (err.name === 'NotFoundError') {
                            errorMessage += 'No microphone found.';
                        } else if (err.name === 'NotReadableError') {
                            errorMessage += 'Microphone is in use by another application.';
                        } else {
                            errorMessage += err.message;
                        }
                        
                        showNotification(errorMessage, 'error');
                        resetRecordingUI();
                    });
            }
            
            function stopRecording() {
                console.log('=== STOP RECORDING DEBUG ===');
                console.log('1. Stop recording function called');
                
                if (mediaRecorder) {
                    console.log('2. MediaRecorder exists');
                    console.log('2a. MediaRecorder state:', mediaRecorder.state);
                    
                    if (mediaRecorder.state === 'recording') {
                        console.log('3. MediaRecorder is recording, stopping...');
                        mediaRecorder.stop();
                        
                        console.log('4. Stopping all tracks...');
                        mediaRecorder.stream.getTracks().forEach(track => {
                            console.log('4a. Stopping track:', track.kind, track.id);
                            track.stop();
                        });
                        
                        stopRecordingBtn.style.display = 'none';
                        recordingStatus.textContent = 'Processing recorded audio...';
                        recordingStatus.classList.remove('recording-active');
                        
                        console.log('5. Stop recording complete');
                    } else {
                        console.log('3. MediaRecorder not in recording state:', mediaRecorder.state);
                    }
                } else {
                    console.log('2. MediaRecorder does not exist');
                }
            }
            
            function updateRecordingTime() {
                if (recordingStartTime && mediaRecorder && mediaRecorder.state === 'recording') {
                    const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
                    const minutes = Math.floor(elapsed / 60);
                    const seconds = elapsed % 60;
                    recordingStatus.textContent = `Recording... ${minutes}:${seconds.toString().padStart(2, '0')}`;
                    setTimeout(updateRecordingTime, 1000);
                }
            }
            
            function convertToWAV(audioBlob, fileName) {
                console.log('=== WAV CONVERSION DEBUG START ===');
                console.log('1. Starting WAV conversion...');
                console.log('1a. Input blob size:', audioBlob.size);
                console.log('1b. Input blob type:', audioBlob.type);
                
                // Create audio context for conversion
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                console.log('2. AudioContext created');
                console.log('2a. AudioContext state:', audioContext.state);
                console.log('2b. AudioContext sample rate:', audioContext.sampleRate);
                
                const fileReader = new FileReader();
                console.log('3. FileReader created');
                
                fileReader.onload = function(e) {
                    console.log('4. FileReader onload triggered');
                    console.log('4a. File read successfully');
                    console.log('4b. ArrayBuffer size:', e.target.result.byteLength);
                    
                    console.log('5. Starting decodeAudioData...');
                    audioContext.decodeAudioData(e.target.result, function(buffer) {
                        console.log('6. Audio decoded successfully');
                        console.log('6a. Buffer length:', buffer.length);
                        console.log('6b. Sample rate:', buffer.sampleRate);
                        console.log('6c. Number of channels:', buffer.numberOfChannels);
                        console.log('6d. Duration:', buffer.duration);
                        
                        // Convert to WAV format
                        console.log('7. Starting bufferToWAV conversion...');
                        const wavBlob = bufferToWav(buffer);
                        console.log('7a. WAV blob created, size:', wavBlob.size);
                        
                        const wavFileName = fileName.replace('.webm', '.wav');
                        const wavFile = new File([wavBlob], wavFileName, { type: 'audio/wav' });
                        console.log('7b. WAV file created:', wavFileName, 'size:', wavFile.size);
                        
                        console.log('8. Starting file upload...');
                        // Upload the WAV file
                        handleFileUpload(wavFile);
                        
                        console.log('9. WAV conversion complete');
                        
                        // Reset UI
                        resetRecordingUI();
                    }, function(error) {
                        console.error('ERROR: Audio decoding failed:', error);
                        showNotification('Error processing recorded audio: ' + error.message, 'error');
                        resetRecordingUI();
                    });
                };
                
                fileReader.onerror = function(error) {
                    console.error('ERROR: FileReader error:', error);
                    showNotification('Error reading audio file: ' + error.message, 'error');
                    resetRecordingUI();
                };
                
                console.log('3a. Starting file read...');
                fileReader.readAsArrayBuffer(audioBlob);
            }
            
            function bufferToWav(buffer) {
                const length = buffer.length;
                const sampleRate = buffer.sampleRate;
                const numberOfChannels = buffer.numberOfChannels;
                const arrayBuffer = new ArrayBuffer(44 + length * numberOfChannels * 2);
                const view = new DataView(arrayBuffer);
                
                // WAV header
                const writeString = (offset, string) => {
                    for (let i = 0; i < string.length; i++) {
                        view.setUint8(offset + i, string.charCodeAt(i));
                    }
                };
                
                writeString(0, 'RIFF');
                view.setUint32(4, 36 + length * numberOfChannels * 2, true);
                writeString(8, 'WAVE');
                writeString(12, 'fmt ');
                view.setUint32(16, 16, true);
                view.setUint16(20, 1, true);
                view.setUint16(22, numberOfChannels, true);
                view.setUint32(24, sampleRate, true);
                view.setUint32(28, sampleRate * numberOfChannels * 2, true);
                view.setUint16(32, numberOfChannels * 2, true);
                view.setUint16(34, 16, true);
                writeString(36, 'data');
                view.setUint32(40, length * numberOfChannels * 2, true);
                
                // Convert audio data
                const channelData = [];
                for (let i = 0; i < numberOfChannels; i++) {
                    channelData.push(buffer.getChannelData(i));
                }
                
                let offset = 44;
                for (let i = 0; i < length; i++) {
                    for (let channel = 0; channel < numberOfChannels; channel++) {
                        const sample = Math.max(-1, Math.min(1, channelData[channel][i]));
                        view.setInt16(offset, sample < 0 ? sample * 0x8000 : sample * 0x7FFF, true);
                        offset += 2;
                    }
                }
                
                return new Blob([arrayBuffer], { type: 'audio/wav' });
            }
            
            function resetRecordingUI() {
                recordingControls.style.display = 'none';
                recordAudioBtn.style.display = 'inline-flex';
                recordingStatus.textContent = '';
                recordingStatus.classList.remove('recording-active');
                mediaRecorder = null;
                audioChunks = [];
                recordingStartTime = null;
            }
            
            function handleFileUpload(file) {
                console.log('=== UPLOAD PROCESS STARTED ===');
                console.log('File:', file);
                console.log('File name:', file.name);
                console.log('File size:', file.size);
                console.log('File type:', file.type);
                
                // Set last operation for retry mechanism
                setLastOperation('upload', file);
                
                // Validate file type
                if (!file.type.startsWith('audio/')) {
                    console.error('Invalid file type:', file.type);
                    showNotification('Please select a valid audio file', 'error');
                    return;
                }
                
                // Validate file size (25MB limit)
                if (file.size > 25 * 1024 * 1024) {
                    console.error('File too large:', file.size);
                    showNotification('File size must be less than 25MB', 'error');
                    return;
                }
                
                console.log('✅ File validation passed');
                
                // Show upload progress
                showUploadProgress();
                
                // Update workflow step
                updateWorkflowStep('upload', 'processing');
                
                // Create FormData and upload
                const formData = new FormData();
                formData.append('action', 'upload_file');
                formData.append('audio_file', file);

                // Add patient_id if one is selected
                if (window.patientSelector && window.patientSelector.currentPatient()) {
                    const patient = window.patientSelector.currentPatient();
                    formData.append('patient_id', patient.patient_id);
                    console.log('✅ Adding patient_id:', patient.patient_id);
                }

                console.log('✅ FormData created');
                console.log('FormData entries:', Array.from(formData.entries()));
                
                // Simulate upload progress
                simulateUploadProgress();
                
                console.log('🔄 Sending fetch request to upload.php...');
                fetch('upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('✅ Response received:', response);
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    return response.json();
                })
                .then(data => {
                    console.log('✅ JSON parsed successfully:', data);
                    if (data.success) {
                        // Update workflow step
                        updateWorkflowStep('upload', 'completed');
                        updateWorkflowStep('transcription', 'ready');

                        showNotification('Audio file uploaded successfully!', 'success');

                        // Hide upload progress
                        hideUploadProgress();

                        // Show transcription button instead of redirecting
                        showTranscriptionButton();

                        // Store upload success in session storage
                        sessionStorage.setItem('uploadSuccess', 'true');
                        sessionStorage.setItem('uploadedFileName', file.name);

                        // Store transcription ID if provided
                        if (data.transcription_id) {
                            sessionStorage.setItem('transcriptionId', data.transcription_id);
                        }
                    } else {
                        showNotification(data.message || 'Upload failed', 'error');
                        resetUploadArea();
                        hideUploadProgress();
                        updateWorkflowStep('upload', 'ready');
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    let errorMessage = 'Upload failed. Please try again.';
                    
                    // Try to get more specific error information
                    if (error.name === 'TypeError' && error.message.includes('JSON')) {
                        errorMessage = 'Server response error. Please check console for details.';
                    } else if (error.name === 'NetworkError') {
                        errorMessage = 'Network error. Please check your connection.';
                    }
                    
                    // Enhanced error handling with fallback display
                    try {
                        showNotification(errorMessage, 'error');
                    } catch (notificationError) {
                        console.error('Notification display failed:', notificationError);
                        // Fallback: show error in page
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'alert alert-danger mt-3';
                        errorDiv.innerHTML = `
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <span>${errorMessage}</span>
                                <button class="btn btn-sm btn-outline-primary ms-auto" onclick="this.parentElement.parentElement.remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                        
                        // Insert error message after upload area
                        const uploadSection = document.getElementById('upload');
                        if (uploadSection) {
                            uploadSection.appendChild(errorDiv);
                        }
                    }
                    
                    resetUploadArea();
                    hideUploadProgress();
                    updateWorkflowStep('upload', 'ready');
                });
            }
            
            function showUploadProgress() {
                const uploadProgress = document.getElementById('uploadProgress');
                if (uploadProgress) {
                    uploadProgress.style.display = 'block';
                }
            }
            
            function hideUploadProgress() {
                const uploadProgress = document.getElementById('uploadProgress');
                if (uploadProgress) {
                    uploadProgress.style.display = 'none';
                }
            }
            
            function simulateUploadProgress() {
                const progressFill = document.getElementById('uploadProgressFill');
                const progressPercentage = document.getElementById('uploadProgressPercentage');
                const progressMessage = document.getElementById('uploadProgressMessage');
                
                if (!progressFill || !progressPercentage || !progressMessage) return;
                
                let progress = 0;
                const interval = setInterval(() => {
                    progress += Math.random() * 15;
                    if (progress > 90) progress = 90;
                    
                    progressFill.style.width = progress + '%';
                    progressPercentage.textContent = Math.round(progress) + '%';
                    
                    if (progress < 30) {
                        progressMessage.textContent = 'Preparing file for upload...';
                    } else if (progress < 60) {
                        progressMessage.textContent = 'Uploading audio file...';
                    } else if (progress < 90) {
                        progressMessage.textContent = 'Finalizing upload...';
                    }
                    
                    if (progress >= 90) {
                        clearInterval(interval);
                    }
                }, 200);
            }
            
            // Enhanced accessibility
            function enhanceAccessibility() {
                // Add ARIA labels and roles
                const navLinks = document.querySelectorAll('.nav-link');
                navLinks.forEach((link, index) => {
                    link.setAttribute('role', 'menuitem');
                    link.setAttribute('aria-label', link.textContent.trim());
                });
                
                // Add keyboard navigation for workflow steps
                const workflowSteps = document.querySelectorAll('.workflow-step');
                workflowSteps.forEach((step, index) => {
                    step.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            this.click();
                        }
                    });
                    
                    step.addEventListener('click', function() {
                        // Update active step
                        workflowSteps.forEach(s => s.classList.remove('active'));
                        this.classList.add('active');
                        
                        // Announce status change to screen readers
                        announceStatusChange(this);
                    });
                });
                
                // Add focus management for upload area
                const uploadArea = document.getElementById('uploadArea');
                if (uploadArea) {
                    uploadArea.setAttribute('tabindex', '0');
                    uploadArea.setAttribute('role', 'button');
                    uploadArea.setAttribute('aria-label', 'Audio file upload area. Drop audio files here or click to browse.');
                    
                    uploadArea.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            document.getElementById('audioFile').click();
                        }
                    });
                }
            }
            
            function announceStatusChange(stepElement) {
                const statusAnnouncer = document.getElementById('statusAnnouncer');
                if (statusAnnouncer) {
                    const stepLabel = stepElement.querySelector('.step-label').textContent;
                    const stepStatus = stepElement.querySelector('.step-status').textContent;
                    statusAnnouncer.textContent = `${stepLabel} step is now ${stepStatus}`;
                    
                    // Clear announcement after a delay
                    setTimeout(() => {
                        statusAnnouncer.textContent = '';
                    }, 3000);
                }
            }
            
            function updateWorkflowStep(stepName, status) {
                const stepElement = document.querySelector(`[data-step="${stepName}"]`);
                if (!stepElement) return;
                
                // Remove all status classes
                stepElement.classList.remove('active', 'completed', 'ready', 'processing');
                
                // Add new status class
                stepElement.classList.add(status);
                
                // Update step status text
                const statusElement = stepElement.querySelector('.step-status');
                if (statusElement) {
                    statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                }
                
                // Update step icon
                const iconElement = stepElement.querySelector('.step-icon');
                if (iconElement) {
                    switch (status) {
                        case 'completed':
                            iconElement.style.background = 'var(--accent)';
                            iconElement.style.borderColor = 'var(--accent)';
                            iconElement.style.color = 'white';
                            break;
                        case 'active':
                        case 'ready':
                            iconElement.style.background = 'var(--primary)';
                            iconElement.style.borderColor = 'var(--primary)';
                            iconElement.style.color = 'white';
                            break;
                        case 'processing':
                            iconElement.style.background = 'var(--secondary)';
                            iconElement.style.borderColor = 'var(--secondary)';
                            iconElement.style.color = 'white';
                            break;
                        default:
                            iconElement.style.background = 'var(--neutral-100)';
                            iconElement.style.borderColor = 'var(--neutral-300)';
                            iconElement.style.color = 'var(--neutral-500)';
                    }
                }
                
                // Announce status change to screen readers
                announceStatusChange(stepElement);
            }
            
            function resetUploadArea() {
                uploadArea.innerHTML = `
                    <div class="upload-icon">
                        <span class="material-icons-round">cloud_upload</span>
                    </div>
                    <div class="upload-text">Drop your audio file here</div>
                    <div class="upload-hint">or click to browse (MP3, WAV, M4A up to 25MB)</div>
                    <input type="file" id="audioFile" accept="audio/*" style="display: none;">
                `;
                
                // Re-attach event listeners
                const newFileInput = uploadArea.querySelector('#audioFile');
                if (newFileInput) {
                    newFileInput.addEventListener('change', (e) => {
                        if (e.target.files.length > 0) {
                            handleFileUpload(e.target.files[0]);
                        }
                    });
                }
            }
            
            function showNotification(message, type) {
                // Create notification element
                const notification = document.createElement('div');
                notification.className = `notification notification-${type}`;

                // Add retry button for errors
                let notificationContent = `
                    <span class="material-icons-round">${type === 'success' ? 'check_circle' : 'error'}</span>
                    <span>${message}</span>
                `;

                if (type === 'error' && message.includes('failed')) {
                    notificationContent += `
                        <button class="btn btn-sm btn-outline-primary ms-2" onclick="window.retryLastOperation()">
                            <span class="material-icons-round">refresh</span>
                            Retry
                        </button>
                    `;
                }
                
                notification.innerHTML = notificationContent;
                
                // Add to page
                document.body.appendChild(notification);
                
                // Show notification
                setTimeout(() => notification.classList.add('show'), 100);
                
                // Remove after 5 seconds
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => notification.remove(), 300);
                }, 5000);
            }
            
            // Retry mechanism for failed operations
            let lastOperation = null;
            let lastOperationData = null;
            
            // Make retry function globally accessible
            window.retryLastOperation = function() {
                if (lastOperation && lastOperationData) {
                    console.log('Retrying last operation:', lastOperation);
                    
                    // Show retry notification
                    showNotification('Retrying operation...', 'success');
                    
                    // Execute retry based on operation type
                    switch (lastOperation) {
                        case 'upload':
                            handleFileUpload(lastOperationData);
                            break;
                        case 'transcription':
                            // Retry transcription if implemented
                            showNotification('Transcription retry not yet implemented', 'error');
                            break;
                        default:
                            showNotification('Retry not available for this operation', 'error');
                    }
                } else {
                    showNotification('No operation to retry', 'error');
                }
            }
            
            function setLastOperation(operation, data) {
                lastOperation = operation;
                lastOperationData = data;
            }

            // Make setLastOperation globally accessible
            window.setLastOperation = setLastOperation;

            // Function to show transcription button after upload
            function showTranscriptionButton() {
                // Replace upload area with transcription button
                const uploadContainer = document.querySelector('.upload-area').parentElement;
                if (uploadContainer) {
                    uploadContainer.innerHTML = `
                        <div class="transcription-ready">
                            <div class="success-icon">
                                <span class="material-icons-round" style="font-size: 4rem; color: var(--accent);">check_circle</span>
                            </div>
                            <h3 style="color: var(--neutral-800); margin: var(--space-4) 0;">Audio Ready for Processing</h3>
                            <p style="color: var(--neutral-600); margin-bottom: var(--space-6);">
                                Your audio file has been uploaded successfully. Click below to start AI transcription.
                            </p>
                            <button id="startTranscriptionBtn" class="btn btn-primary btn-lg" onclick="startTranscription()">
                                <span class="material-icons-round">psychology</span>
                                Start AI Transcription
                            </button>
                            <div style="margin-top: var(--space-3);">
                                <button class="btn btn-outline-primary btn-sm" onclick="toggleDocumentSection()">
                                    <span class="material-icons-round">attach_file</span>
                                    Add Supporting Documents (Optional)
                                </button>
                                <button class="btn btn-outline-primary btn-sm" onclick="resetAndUploadNew()">
                                    <span class="material-icons-round">upload_file</span>
                                    Upload Different File
                                </button>
                            </div>
                        </div>
                    `;
                }
            }

            // Function to start transcription
            function startTranscription() {
                console.log('Starting transcription process...');
                const btn = document.getElementById('startTranscriptionBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="material-icons-round spinning">sync</span> Processing...';
                }

                // Update workflow
                updateWorkflowStep('transcription', 'processing');

                // Prepare request with document info if available
                const formData = new FormData();
                formData.append('action', 'convert');

                // Add document IDs if documents were uploaded
                const documentIds = sessionStorage.getItem('documentIds');
                if (documentIds) {
                    formData.append('document_ids', documentIds);
                    formData.append('with_documents', 'true');
                }

                // Call the transcription endpoint
                fetch('python_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateWorkflowStep('transcription', 'completed');
                        updateWorkflowStep('download', 'ready');
                        showNotification('Transcription completed successfully!', 'success');

                        // Show download options
                        showDownloadOptions(data);
                    } else {
                        showNotification(data.message || 'Transcription failed', 'error');
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = '<span class="material-icons-round">psychology</span> Retry Transcription';
                        }
                        updateWorkflowStep('transcription', 'ready');
                    }
                })
                .catch(error => {
                    console.error('Transcription error:', error);
                    showNotification('Transcription failed. Please try again.', 'error');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<span class="material-icons-round">psychology</span> Retry Transcription';
                    }
                    updateWorkflowStep('transcription', 'ready');
                });
            }

            // Helper function to get file icon based on type
            function getFileIcon(fileType) {
                const type = fileType.toLowerCase();
                const iconMap = {
                    'pdf': 'picture_as_pdf',
                    'jpg': 'image',
                    'jpeg': 'image',
                    'png': 'image',
                    'doc': 'description',
                    'docx': 'description',
                    'txt': 'text_snippet'
                };
                return iconMap[type] || 'insert_drive_file';
            }

            // Function to show download options with results display
            function showDownloadOptions(data) {
                const container = document.querySelector('.transcription-ready') || document.querySelector('.upload-area').parentElement;
                if (container) {
                    // Extract results from the response
                    const results = data.results || {};
                    const transcribedText = results.transcribed_text || 'No transcription available';
                    const medicalAnalysis = results.medical_analysis || results.chatgpt_response || 'No analysis available';
                    const fhirData = results.fhir_json || null;
                    const documents = data.documents || [];

                    // Format the medical analysis sections
                    let analysisHtml = medicalAnalysis.replace(/\n\n/g, '</p><p>').replace(/\n/g, '<br>');
                    analysisHtml = '<p>' + analysisHtml + '</p>';

                    container.innerHTML = `
                        <div class="results-display" style="width: 100%; max-width: 1200px; margin: 0 auto;">
                            <div class="success-header" style="text-align: center; margin-bottom: 2rem;">
                                <div class="success-icon">
                                    <span class="material-icons-round" style="font-size: 4rem; color: var(--accent);">task_alt</span>
                                </div>
                                <h3 style="color: var(--neutral-800); margin: var(--space-4) 0;">AI Analysis Complete!</h3>
                            </div>

                            <!-- Transcription Section -->
                            <div class="result-section" style="background: #f8f9fa; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                                <h4 style="color: #2c3e50; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <span class="material-icons-round" style="color: #3B82F6;">mic</span>
                                    Transcribed Text
                                </h4>
                                <div style="background: white; padding: 1rem; border-radius: 8px; border-left: 4px solid #3B82F6;">
                                    <p style="color: #555; line-height: 1.6; margin: 0;">${transcribedText}</p>
                                </div>
                            </div>

                            <!-- Medical Analysis Section -->
                            <div class="result-section" style="background: #f8f9fa; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                                <h4 style="color: #2c3e50; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <span class="material-icons-round" style="color: #10B981;">psychology</span>
                                    Medical Analysis
                                </h4>
                                <div style="background: white; padding: 1rem; border-radius: 8px; border-left: 4px solid #10B981;">
                                    <div style="color: #555; line-height: 1.8;">
                                        ${analysisHtml}
                                    </div>
                                </div>
                            </div>

                            <!-- Uploaded Documents Section -->
                            ${documents.length > 0 ? `
                            <div class="result-section" style="background: #f8f9fa; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                                <h4 style="color: #2c3e50; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <span class="material-icons-round" style="color: #9b59b6;">folder_open</span>
                                    Supporting Documents (${documents.length} files)
                                </h4>
                                <div style="background: white; padding: 1rem; border-radius: 8px; border-left: 4px solid #9b59b6;">
                                    <div style="display: grid; gap: 0.75rem;">
                                        ${documents.map(doc => {
                                            const fileIcon = getFileIcon(doc.file_type);
                                            const fileSizeMB = (doc.file_size / 1024 / 1024).toFixed(2);
                                            return `
                                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: #f8f9fa; border-radius: 6px; border: 1px solid #e0e0e0;">
                                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                    <span class="material-icons-round" style="color: #7f8c8d; font-size: 1.5rem;">${fileIcon}</span>
                                                    <div>
                                                        <div style="font-weight: 500; color: #2c3e50;">${doc.original_name}</div>
                                                        <div style="font-size: 0.85rem; color: #7f8c8d;">${doc.file_type.toUpperCase()} • ${fileSizeMB} MB</div>
                                                    </div>
                                                </div>
                                                <div style="display: flex; gap: 0.5rem;">
                                                    ${['pdf', 'jpg', 'jpeg', 'png'].includes(doc.file_type.toLowerCase()) ? `
                                                    <a href="${doc.download_url}&action=view" target="_blank"
                                                       class="btn btn-sm btn-primary"
                                                       title="View document">
                                                        <span class="material-icons-round" style="font-size: 1rem;">visibility</span>
                                                        View
                                                    </a>` : ''}
                                                    <a href="${doc.download_url}" download
                                                       class="btn btn-sm btn-primary"
                                                       title="Download document">
                                                        <span class="material-icons-round" style="font-size: 1rem;">download</span>
                                                        Download
                                                    </a>
                                                </div>
                                            </div>
                                            `;
                                        }).join('')}
                                    </div>
                                </div>
                            </div>
                            ` : ''}

                            <!-- FHIR Data Section (Collapsible) -->
                            ${fhirData ? `
                            <div class="result-section" style="background: #f8f9fa; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                                <h4 style="color: #2c3e50; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;"
                                    onclick="document.getElementById('fhirContent').style.display = document.getElementById('fhirContent').style.display === 'none' ? 'block' : 'none'">
                                    <span class="material-icons-round" style="color: #8B5CF6;">description</span>
                                    FHIR Data (Click to expand)
                                    <span class="material-icons-round" style="margin-left: auto;">expand_more</span>
                                </h4>
                                <div id="fhirContent" style="display: none; background: white; padding: 1rem; border-radius: 8px; border-left: 4px solid #8B5CF6;">
                                    <pre style="color: #555; overflow-x: auto; font-size: 0.9rem; margin: 0;">${typeof fhirData === 'string' ? fhirData : JSON.stringify(JSON.parse(fhirData), null, 2)}</pre>
                                </div>
                            </div>
                            ` : ''}

                            <!-- Action Buttons -->
                            <div style="text-align: center; margin-top: 2rem;">
                                <button class="btn btn-primary btn-lg" onclick="downloadResults()" style="margin: 0.5rem;">
                                    <span class="material-icons-round">download</span>
                                    Download Full Report (JSON)
                                </button>
                                <button class="btn btn-outline-primary" onclick="window.print()" style="margin: 0.5rem;">
                                    <span class="material-icons-round">print</span>
                                    Print Report
                                </button>
                                <button class="btn btn-outline-primary" onclick="window.location.href='index.php?clear_session=1'" style="margin: 0.5rem;">
                                    <span class="material-icons-round">add_circle</span>
                                    Process New Audio
                                </button>
                            </div>
                        </div>
                    `;

                    // Add print styles
                    if (!document.getElementById('print-styles')) {
                        const printStyles = document.createElement('style');
                        printStyles.id = 'print-styles';
                        printStyles.innerHTML = `
                            @media print {
                                .navbar, .credits-bar, .hero-section, .workflow-section, .features-section {
                                    display: none !important;
                                }
                                .results-display {
                                    padding: 20px;
                                }
                                .btn {
                                    display: none !important;
                                }
                                .result-section {
                                    page-break-inside: avoid;
                                }
                            }
                        `;
                        document.head.appendChild(printStyles);
                    }
                }
            }

            // Function to download results
            function downloadResults() {
                window.location.href = 'download_json.php';
            }

            // Function to reset and upload new file
            function resetAndUploadNew() {
                window.location.href = 'index.php?clear_session=1';
            }

            // Handle file upload function
            function handleFileUpload(file) {
                console.log('Uploading file:', file.name);

                // Validate file
                if (!file.type.startsWith('audio/')) {
                    showNotification('Please select a valid audio file', 'error');
                    return;
                }

                if (file.size > 50 * 1024 * 1024) {
                    showNotification('File size must be less than 50MB', 'error');
                    return;
                }

                // Show upload progress
                const uploadProgress = document.getElementById('uploadProgress');
                if (uploadProgress) {
                    uploadProgress.style.display = 'block';
                }

                // Create FormData
                const formData = new FormData();
                formData.append('action', 'upload_file');
                formData.append('audio_file', file);

                // Add patient_id if one is selected
                if (window.patientSelector && window.patientSelector.currentPatient()) {
                    const patient = window.patientSelector.currentPatient();
                    formData.append('patient_id', patient.patient_id);
                    console.log('Adding patient_id:', patient.patient_id);
                }

                // Upload file
                fetch('upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Upload response:', data);
                    if (data.success) {
                        // Store transcription ID
                        if (data.transcription_id) {
                            sessionStorage.setItem('transcriptionId', data.transcription_id);
                        }

                        // Hide upload progress
                        if (uploadProgress) {
                            uploadProgress.style.display = 'none';
                        }

                        showNotification('Audio uploaded successfully!', 'success');

                        // Show transcription UI
                        const uploadContainer = document.querySelector('.upload-area').parentElement;
                        if (uploadContainer) {
                            uploadContainer.innerHTML = `
                                <div class="transcription-ready">
                                    <div class="success-icon">
                                        <span class="material-icons-round" style="font-size: 4rem; color: #4CAF50;">check_circle</span>
                                    </div>
                                    <h3 style="margin: 20px 0;">Audio Ready for Processing</h3>
                                    <p style="margin-bottom: 20px;">Your audio file has been uploaded successfully.</p>
                                    <button id="startTranscriptionBtn" class="btn btn-primary btn-lg" onclick="startTranscription()">
                                        <span class="material-icons-round">psychology</span>
                                        Start AI Transcription
                                    </button>
                                    <div style="margin-top: 20px;">
                                        <button class="btn btn-outline-primary btn-sm" onclick="toggleDocumentSection()">
                                            <span class="material-icons-round">attach_file</span>
                                            Add Supporting Documents (Optional)
                                        </button>
                                        <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='index.php?clear_session=1'">
                                            <span class="material-icons-round">upload_file</span>
                                            Upload Different File
                                        </button>
                                    </div>
                                </div>
                            `;
                        }
                    } else {
                        showNotification(data.message || 'Upload failed', 'error');
                        if (uploadProgress) {
                            uploadProgress.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    showNotification('Upload failed: ' + error.message, 'error');
                    if (uploadProgress) {
                        uploadProgress.style.display = 'none';
                    }
                });
            }

            // Initialize upload area
            document.addEventListener('DOMContentLoaded', function() {
                const uploadArea = document.querySelector('.upload-area');
                const fileInput = document.getElementById('audioFile');

                if (uploadArea && fileInput) {
                    // Click to upload
                    uploadArea.addEventListener('click', () => {
                        fileInput.click();
                    });

                    // File input change
                    fileInput.addEventListener('change', (e) => {
                        if (e.target.files.length > 0) {
                            handleFileUpload(e.target.files[0]);
                        }
                    });

                    // Drag and drop
                    uploadArea.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        uploadArea.classList.add('dragover');
                    });

                    uploadArea.addEventListener('dragleave', () => {
                        uploadArea.classList.remove('dragover');
                    });

                    uploadArea.addEventListener('drop', (e) => {
                        e.preventDefault();
                        uploadArea.classList.remove('dragover');
                        if (e.dataTransfer.files.length > 0) {
                            handleFileUpload(e.dataTransfer.files[0]);
                        }
                    });
                }

                // Handle record button if exists
                const recordButton = document.getElementById('recordButton');
                if (recordButton) {
                    recordButton.addEventListener('click', startRecording);
                }
            });

            // Make functions globally accessible
            window.startTranscription = startTranscription;
            window.downloadResults = downloadResults;
            window.resetAndUploadNew = resetAndUploadNew;
            window.showNotification = showNotification;
            window.handleFileUpload = handleFileUpload;
            window.toggleDocumentSection = toggleDocumentSection;
            
            // Enhanced scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            // Observe all animated elements
            document.querySelectorAll('.animate-fade-in-up').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
                observer.observe(el);
            });
            
            // Enhanced feature card interactions
            document.querySelectorAll('.feature-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
    });
});
</script>
