<!-- AI Processing Section -->
<h2 class="mb-4 text-success">
    <i class="fas fa-microphone"></i> AI Processing
</h2>

<!-- File Status Display -->
<div class="file-status-section mb-4">
    <?php if (isset($_SESSION['uploaded_file']) && file_exists($_SESSION['uploaded_file'])): ?>
        <div class="file-status-success">
            <div class="status-content">
                <div class="status-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="status-info">
                    <h5 class="status-title">✅ File Ready for Processing</h5>
                    <p class="status-file"><strong>File:</strong> <?php echo basename($_SESSION['uploaded_file']); ?></p>
                    <p class="status-size"><strong>Size:</strong> <?php echo formatFileSize(filesize($_SESSION['uploaded_file'])); ?></p>
                    <p class="status-ready text-success">Ready to convert with AI</p>
                </div>
            </div>
        </div>
        
        <!-- Processing Form -->
        <!-- Enhanced Processing Form -->
        <div class="processing-form mt-3">
            <form action="" method="post" class="d-inline" id="processingForm">
                <input type="hidden" name="action" value="convert">
                <button type="submit" name="convert" class="btn btn-success btn-lg" id="startProcessingBtn">
                    <i class="fas fa-play me-2"></i> Start AI Processing
                </button>
            </form>
            <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="changeFileBtn">
                <i class="fas fa-edit me-1"></i> Change File
            </button>
        </div>
        
        <!-- Real-time Progress Display -->
        <div class="processing-progress" id="processingProgress" style="display: none;">
            <div class="progress-header">
                <h5 class="progress-title">
                    <i class="fas fa-cog fa-spin me-2"></i>AI Processing in Progress
                </h5>
                <div class="progress-stage" id="progressStage">Initializing...</div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" id="progressBar">
                    <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                </div>
                <div class="progress-percentage" id="progressPercentage">0%</div>
            </div>
            <div class="progress-message" id="progressMessage">Starting processing...</div>
            <div class="progress-time" id="progressTime">Estimated time: Calculating...</div>
        </div>
    <?php else: ?>
        <div class="file-status-warning">
            <div class="status-content">
                <div class="status-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="status-info">
                    <h5 class="status-title">⚠️ No File Uploaded</h5>
                    <p class="status-message">Please upload an audio file first to proceed with AI processing.</p>
                    <a href="#upload" class="btn btn-primary btn-sm">
                        <i class="fas fa-upload me-2"></i>Upload Audio File
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Processing Results -->
<?php if (isset($_SESSION['json_file']) && file_exists($_SESSION['json_file'])): ?>
    <div class="processing-results mt-4">
        <h4 class="mb-3 text-primary">
            <i class="fas fa-chart-line me-2"></i>Processing Results
        </h4>
        
        <?php
        $jsonContent = file_get_contents($_SESSION['json_file']);
        $data = json_decode($jsonContent, true);
        
        if ($data && isset($data['transcribed_text']) && (isset($data['chatgpt_response']) || isset($data['medical_analysis']))): ?>
            <div class="results-container">
                <!-- Enhanced Results Header -->
                <div class="results-header">
                    <h4 class="results-main-title">
                        <i class="fas fa-chart-line me-2"></i>AI Processing Results
                    </h4>
                    <?php if (isset($data['processing_stats']['word_count'])): ?>
                        <div class="results-stats">
                            <span class="stat-item">
                                <i class="fas fa-file-alt me-1"></i>
                                <?php echo number_format($data['processing_stats']['word_count']); ?> words
                            </span>
                            <?php if (isset($data['processing_stats']['audio_duration'])): ?>
                                <span class="stat-item">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo gmdate("i:s", $data['processing_stats']['audio_duration']); ?> duration
                                </span>
                            <?php endif; ?>
                            <span class="stat-item">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo date('M j, Y g:i A', strtotime($data['timestamp'])); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Transcription Result -->
                <div class="result-card mb-3">
                    <div class="result-header">
                        <h5 class="result-title">
                            <i class="fas fa-microphone me-2"></i>Speech Transcription
                        </h5>
                        <div class="result-actions-mini">
                            <button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard('transcription')">
                                <i class="fas fa-copy me-1"></i>Copy
                            </button>
                        </div>
                    </div>
                    <div class="result-content">
                        <div class="result-text" id="transcription-text"><?php echo nl2br(htmlspecialchars($data['transcribed_text'])); ?></div>
                    </div>
                </div>
                
                <!-- AI Medical Analysis Result -->
                <div class="result-card mb-3">
                    <div class="result-header">
                        <h5 class="result-title">
                            <i class="fas fa-brain me-2"></i>AI Medical Analysis
                        </h5>
                        <div class="result-actions-mini">
                            <button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard('analysis')">
                                <i class="fas fa-copy me-1"></i>Copy
                            </button>
                        </div>
                    </div>
                    <div class="result-content">
                        <div class="result-text medical-analysis" id="analysis-text">
                            <?php 
                            $analysis = $data['medical_analysis'] ?? $data['chatgpt_response'];
                            echo nl2br(htmlspecialchars($analysis));
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Features (if available) -->
                <?php if (isset($data['ai_summary']) || isset($data['key_highlights']) || isset($data['chapters'])): ?>
                    <div class="enhanced-features">
                        <h5 class="features-title">
                            <i class="fas fa-star me-2"></i>Enhanced AI Features
                        </h5>
                        
                        <?php if (isset($data['ai_summary'])): ?>
                            <div class="feature-card">
                                <h6><i class="fas fa-list-ul me-2"></i>AI Summary</h6>
                                <p><?php echo nl2br(htmlspecialchars($data['ai_summary'])); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($data['key_highlights']) && !empty($data['key_highlights'])): ?>
                            <div class="feature-card">
                                <h6><i class="fas fa-highlight me-2"></i>Key Highlights</h6>
                                <ul class="highlights-list">
                                    <?php foreach (array_slice($data['key_highlights'], 0, 5) as $highlight): ?>
                                        <li><?php echo htmlspecialchars($highlight['text']); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Action Buttons -->
            <div class="result-actions mt-3">
                <button class="btn btn-outline-primary btn-sm" onclick="downloadResults()">
                    <i class="fas fa-download me-1"></i>Download Results
                </button>
                <button class="btn btn-outline-secondary btn-sm ms-2" onclick="clearResults()">
                    <i class="fas fa-trash me-1"></i>Clear Results
                </button>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Processing results are incomplete or corrupted.
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php
// Helper function to format file size
function formatFileSize($bytes) {
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

include('database.php');
$userId = $_SESSION['member_id'];

// ✅ Handle Convert Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'convert') {

    // Check credits
    $stmt = $conn->prepare("SELECT credits FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($credits);
    $stmt->fetch();
    $stmt->close();

    if ($credits <= 0) {
        echo "<div class='alert alert-danger mt-3 p-3'>
                ❌ You have no credits left. 
                <a href='buy_credits.php' class='btn btn-primary btn-sm ms-2'>Buy Credits</a>
              </div>";
        exit;
    }

    // Deduct 1 credit
    $stmt = $conn->prepare("UPDATE members SET credits = credits - 1 WHERE member_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    if (isset($_SESSION['uploaded_file'])) {
        $audioFile = $_SESSION['uploaded_file'];
        $logFile = $_SESSION['log_file'];

        // Create a process lock to prevent multiple executions
        $lockFile = $logFile . '.lock';
        if (file_exists($lockFile)) {
            $lockTime = filemtime($lockFile);
            if (time() - $lockTime < 300) { // 5 minutes lock
                echo "<div class='alert alert-warning mt-3 p-3'>
                        ⚠️ Processing already in progress. Please wait a few minutes.
                      </div>";
                exit();
            } else {
                // Remove stale lock
                unlink($lockFile);
            }
        }

        // Create lock file
        file_put_contents($lockFile, time());

        // Clear any existing log file
        if (file_exists($logFile)) {
            unlink($logFile);
        }

        $pythonBinary = __DIR__ . "/myenv/bin/python"; // Default path
        $pythonScript = __DIR__ . "/process_audio.py";

        // Validate script and python binary
        if (!file_exists($pythonScript)) {
            echo "<div class='alert alert-danger mt-3'>❌ Processing script not available.</div>";
            exit();
        }

        // Find the correct Python binary for Windows
        $possiblePaths = [
            "python",
            "python3",
            "py",
            __DIR__ . "/myenv/Scripts/python.exe",
            __DIR__ . "/myenv/bin/python.exe",
            __DIR__ . "/myenv/bin/python",
            __DIR__ . "/myenv/Scripts/python",
        ];

        $pythonBinary = null;
        foreach ($possiblePaths as $path) {
            if ($path === "python" || $path === "python3" || $path === "py") {
                $testCommand = $path . " --version 2>&1";
                $output = shell_exec($testCommand);
                if ($output && strpos($output, "Python") !== false) {
                    $pythonBinary = $path;
                    break;
                }
            } else {
                if (file_exists($path)) {
                    $pythonBinary = $path;
                    break;
                }
            }
        }

        if (!$pythonBinary) {
            echo "<div class='alert alert-danger mt-3'>
                    ❌ Processing environment not configured properly. Please contact support.
                  </div>";
            exit();
        }

        // Build Command for Windows with proper path handling
        if (in_array($pythonBinary, ["python", "python3", "py"])) {
            $command = $pythonBinary . ' "' . str_replace('/', '\\', $pythonScript) . '" "' . str_replace('/', '\\', $audioFile) . '" "' . str_replace('/', '\\', $logFile) . '"';
        } else {
            $command = '"' . str_replace('/', '\\', $pythonBinary) . '" "' . str_replace('/', '\\', $pythonScript) . '" "' . str_replace('/', '\\', $audioFile) . '" "' . str_replace('/', '\\', $logFile) . '"';
        }

        // Execute Command with timeout
        $output = '';
        $returnCode = -1;
        $descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );

        $process = proc_open($command, $descriptorspec, $pipes);
        
        if (is_resource($process)) {
            stream_set_blocking($pipes[1], 0);
            stream_set_blocking($pipes[2], 0);
            
            $startTime = time();
            $timeout = 60;
            
            while (proc_get_status($process)['running'] && (time() - $startTime) < $timeout) {
                $output .= stream_get_contents($pipes[1]);
                $error = stream_get_contents($pipes[2]);
                if ($error) {
                    $output .= $error;
                }
                usleep(100000);
            }
            
            $output .= stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            if ($error) {
                $output .= $error;
            }
            
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            
            $returnCode = proc_close($process);
        } else {
            $output = shell_exec($command . " 2>&1");
        }

        // Log processing for monitoring
        if ($returnCode !== 0) {
            error_log("MedicalVoice processing failed with return code: " . $returnCode);
        }

        // Parse the output to find the final JSON result (not progress updates)
        $responseData = null;
        $lines = explode("\n", $output);
        
        // Look for the final JSON response (contains 'json_file')
        foreach (array_reverse($lines) as $line) {
            $line = trim($line);
            if (!empty($line) && strpos($line, '{') === 0) {
                $jsonData = json_decode($line, true);
                if ($jsonData && isset($jsonData['json_file'])) {
                    $responseData = $jsonData;
                    break;
                }
            }
        }

        if ($responseData && isset($responseData['json_file']) && (!isset($responseData['status']) || $responseData['status'] === 'success')) {
            $jsonFile = $responseData['json_file'];
            $_SESSION['json_file'] = $jsonFile;

            // Remove lock file on success
            if (file_exists($lockFile)) {
                unlink($lockFile);
            }

            // Enhanced success message with processing stats
            echo "<div class='alert alert-success mt-3 p-3'>
                    <div class='d-flex align-items-center'>
                        <div class='success-icon me-3'>
                            <i class='fas fa-check-circle fa-2x text-success'></i>
                        </div>
                        <div>
                            <h5 class='mb-1'>✅ AI Processing Completed Successfully!</h5>
                            <p class='mb-1'>Your audio has been transcribed and analyzed.</p>";
            
            // Show processing stats if available
            if (isset($responseData['processing_stats'])) {
                $stats = $responseData['processing_stats'];
                echo "<small class='text-muted'>";
                if (isset($stats['word_count'])) {
                    echo "Words: " . number_format($stats['word_count']) . " • ";
                }
                if (isset($stats['audio_duration'])) {
                    echo "Duration: " . gmdate("i:s", $stats['audio_duration']) . " • ";
                }
                if (isset($stats['transcription_confidence'])) {
                    echo "Confidence: " . round($stats['transcription_confidence'] * 100, 1) . "%";
                }
                echo "</small>";
            }
            
            echo "      </div>
                    </div>
                    <div class='mt-3'>
                        <p class='mb-0'>
                            <i class='fas fa-arrow-down me-2'></i>
                            <strong>Next:</strong> Scroll down to <strong>Read Response</strong> section to view your results.
                        </p>
                    </div>
                  </div>";

        } else {
            // Remove lock file on failure
            if (file_exists($lockFile)) {
                unlink($lockFile);
            }

            // Enhanced error reporting for debugging
            echo "<div class='alert alert-danger mt-3 p-3'>
                    <div class='d-flex align-items-center'>
                        <div class='error-icon me-3'>
                            <i class='fas fa-exclamation-triangle fa-2x text-danger'></i>
                        </div>
                        <div>
                            <h5 class='mb-1'>❌ Processing Failed</h5>
                            <p class='mb-0'>The AI processing could not be completed.</p>
                        </div>
                    </div>
                  </div>";
            
            // Show debug information if available
            if (isset($_GET['debug']) && $_GET['debug'] === '1') {
                echo "<div class='card mt-3 p-3' style='background: #f8f9fa;'>
                        <h5>Debug Information:</h5>
                        <p><strong>Return Code:</strong> " . ($returnCode ?? 'Unknown') . "</p>
                        <p><strong>Command:</strong> " . htmlspecialchars($command ?? 'Not set') . "</p>
                        <p><strong>Response Data Found:</strong> " . (isset($responseData) ? 'Yes' : 'No') . "</p>";
                if (isset($responseData)) {
                    echo "<p><strong>Response Keys:</strong> " . implode(', ', array_keys($responseData)) . "</p>";
                }
                echo "<p><strong>Output:</strong></p>
                        <pre style='background: #fff; padding: 10px; border: 1px solid #ddd; max-height: 200px; overflow-y: auto;'>" . htmlspecialchars($output ?? 'No output') . "</pre>
                      </div>";
            } else {
                echo "<div class='mt-2'>
                        <small><a href='?debug=1' class='text-muted'>Show debug information</a></small>
                      </div>";
            }
        }

    } else {
        echo "<div class='alert alert-warning mt-3 p-3'>No uploaded file found in session.</div>";
    }
}
?>

<!-- Enhanced AI Processing Styles -->
<style>
.file-status-section {
    margin-bottom: 2rem;
}

.file-status-success, .file-status-warning {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.05);
}

.file-status-success {
    border-left: 4px solid #48bb78;
}

.file-status-warning {
    border-left: 4px solid #ed8936;
}

.status-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.status-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.file-status-success .status-icon {
    background: linear-gradient(135deg, #48bb78, #38a169);
}

.file-status-warning .status-icon {
    background: linear-gradient(135deg, #ed8936, #dd6b20);
}

.status-info {
    flex: 1;
}

.status-title {
    margin: 0 0 0.5rem 0;
    color: #2d3748;
    font-weight: 600;
    font-size: 1.1rem;
}

.status-file, .status-size, .status-ready, .status-message {
    margin: 0.25rem 0;
    color: #4a5568;
    font-size: 0.95rem;
}

.status-ready {
    color: #48bb78 !important;
    font-weight: 600;
}

.status-message {
    color: #718096;
    margin-bottom: 1rem;
}

.processing-form {
    text-align: center;
}

/* Processing Results */
.processing-results {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.05);
}

.results-container {
    margin-bottom: 1.5rem;
}

.result-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid rgba(0,0,0,0.05);
}

.result-header {
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.result-title {
    margin: 0;
    color: #2d3748;
    font-weight: 600;
    font-size: 1.1rem;
}

.result-content {
    line-height: 1.6;
}

.result-text {
    margin: 0;
    color: #4a5568;
    font-size: 0.95rem;
    white-space: pre-wrap;
}

.result-actions {
    text-align: center;
}

/* Enhanced Results Display Styles */
.results-header {
    text-align: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.results-main-title {
    margin: 0 0 1rem 0;
    font-weight: 700;
    font-size: 1.5rem;
}

.results-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.1);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.result-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e2e8f0;
}

.result-actions-mini {
    display: flex;
    gap: 0.5rem;
}

.medical-analysis {
    line-height: 1.7;
    font-size: 1rem;
}

.enhanced-features {
    margin-top: 2rem;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
}

.features-title {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 1.5rem;
    text-align: center;
}

.feature-card {
    background: white;
    padding: 1.25rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid rgba(0,0,0,0.05);
}

.feature-card h6 {
    color: #4a5568;
    font-weight: 600;
    margin-bottom: 0.75rem;
    font-size: 1rem;
}

.highlights-list {
    margin: 0;
    padding-left: 1.5rem;
}

.highlights-list li {
    margin-bottom: 0.5rem;
    line-height: 1.5;
    color: #4a5568;
}

/* Enhanced button animations */
.btn {
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.btn:hover {
    transform: translateY(-2px);
}

.btn:active {
    transform: translateY(0);
}

/* Copy button success state */
.btn.btn-success {
    background-color: #48bb78 !important;
    border-color: #48bb78 !important;
}

/* Responsive enhancements */
@media (max-width: 768px) {
    .results-stats {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .stat-item {
        justify-content: center;
    }
    
    .result-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .result-actions-mini {
        justify-content: center;
    }
}

/* Enhanced Processing Progress Styles */
.processing-progress {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-top: 1.5rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.05);
    animation: fadeInUp 0.5s ease;
}

.progress-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.progress-title {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.progress-stage {
    color: #667eea;
    font-weight: 500;
    font-size: 1rem;
}

.progress-bar-container {
    position: relative;
    margin-bottom: 1rem;
}

.progress-bar {
    width: 100%;
    height: 12px;
    background: #e2e8f0;
    border-radius: 6px;
    overflow: hidden;
    position: relative;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 6px;
    transition: width 0.3s ease;
    position: relative;
    overflow: hidden;
}

.progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background-image: linear-gradient(
        -45deg,
        rgba(255, 255, 255, .2) 25%,
        transparent 25%,
        transparent 50%,
        rgba(255, 255, 255, .2) 50%,
        rgba(255, 255, 255, .2) 75%,
        transparent 75%,
        transparent
    );
    background-size: 50px 50px;
    animation: move 2s linear infinite;
}

@keyframes move {
    0% { background-position: 0 0; }
    100% { background-position: 50px 50px; }
}

.progress-percentage {
    text-align: center;
    font-weight: 600;
    color: #667eea;
    margin-top: 0.5rem;
    font-size: 1.1rem;
}

.progress-message {
    text-align: center;
    color: #4a5568;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.progress-time {
    text-align: center;
    color: #718096;
    font-size: 0.85rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .status-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .file-status-success .status-icon::after,
    .file-status-warning .status-icon::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .processing-form {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .processing-form .btn {
        width: 100%;
    }
    
    .processing-progress {
        padding: 1.5rem;
    }
}
</style>

<!-- Enhanced JavaScript for AI Processing -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Change file button functionality
    const changeFileBtn = document.getElementById('changeFileBtn');
    if (changeFileBtn) {
        changeFileBtn.addEventListener('click', function() {
            // Scroll to upload section
            const uploadSection = document.getElementById('upload');
            if (uploadSection) {
                uploadSection.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    }
});

// Enhanced copy to clipboard function
function copyToClipboard(type) {
    let text = '';
    if (type === 'transcription') {
        text = document.getElementById('transcription-text').textContent;
    } else if (type === 'analysis') {
        text = document.getElementById('analysis-text').textContent;
    }
    
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-primary');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy to clipboard');
    });
}

// Enhanced download results function
function downloadResults() {
    const transcription = document.getElementById('transcription-text')?.textContent || '';
    const analysis = document.getElementById('analysis-text')?.textContent || '';
    
    // Get additional data if available
    const statsElements = document.querySelectorAll('.stat-item');
    let stats = '';
    statsElements.forEach(stat => {
        stats += stat.textContent + '\n';
    });
    
    const timestamp = new Date().toLocaleString();
    const content = `MEDICAL VOICE AI PROCESSING RESULTS
${'='.repeat(50)}

PROCESSING INFORMATION:
${stats}
Generated on: ${timestamp}

SPEECH TRANSCRIPTION:
${'='.repeat(30)}
${transcription}

AI MEDICAL ANALYSIS:
${'='.repeat(30)}
${analysis}

${'='.repeat(50)}
This report was generated by Medical Voice AI
Professional healthcare documentation system
`;
    
    const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `medicalvoice_results_${new Date().toISOString().slice(0,10)}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Clear results function
function clearResults() {
    if (confirm('Are you sure you want to clear the processing results?')) {
        // Send AJAX request to clear results
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=clear_results'
        })
        .then(response => {
            // Reload page to refresh results
            window.location.reload();
        })
        .catch(error => {
            console.error('Error clearing results:', error);
            alert('Failed to clear results. Please try again.');
        });
    }
}
</script>

<!-- Read Response Section -->
<h2 id="read" class="mt-5 mb-4 text-info">
    <i class="fas fa-book-open"></i> Read Response
</h2>

<form action="" method="post" class="mb-4">
    <input type="hidden" name="action" value="read">
    <button type="submit" name="read" class="btn btn-info btn-lg">
        <i class="fas fa-eye"></i> Read Response
    </button>
</form>

<?php
// ✅ Handle Read Response Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'read') {

    if (isset($_SESSION['json_file'])) {
        $jsonFile = $_SESSION['json_file'];

        if (file_exists($jsonFile)) {
            $jsonData = file_get_contents($jsonFile);
            $responseData = json_decode($jsonData, true);

            if (isset($responseData['transcribed_text']) && isset($responseData['chatgpt_response'])) {
                echo "<div class='card mt-3 p-4 shadow-sm'>
                        <h4 class='mb-3 text-primary'>Transcribed Text:</h4>
                        <p class='border rounded p-3 bg-light'>" . nl2br(htmlspecialchars($responseData['transcribed_text'])) . "</p>
                        <h4 class='mt-4 mb-3 text-success'>ChatGPT Response:</h4>
                        <p class='border rounded p-3 bg-light'>" . nl2br(htmlspecialchars($responseData['chatgpt_response'])) . "</p>
                      </div>";
            } else {
                echo "<div class='alert alert-warning mt-3 p-3'>Invalid response data in JSON file.</div>";
            }
        } else {
            echo "<div class='alert alert-danger mt-3 p-3'>JSON file not found.</div>";
        }
    } else {
        echo "<div class='alert alert-warning mt-3 p-3'>No JSON file path found in session.</div>";
    }
}
?>

<!-- Post-Read Action: Start a new upload session -->
<div class="mt-4">
    <a href="clear_session.php?anchor=upload" class="btn btn-primary btn-lg">
        <i class="fas fa-file-audio me-2"></i>Start New Audio Upload
    </a>
    
</div>
