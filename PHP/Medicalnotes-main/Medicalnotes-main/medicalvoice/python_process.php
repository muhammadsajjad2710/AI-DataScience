<?php
// Start session FIRST before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    header("Location: ../login.php");
    exit;
}

// Include required files
require_once('database.php');

// Get user ID
$userId = $_SESSION['member_id'];

// Helper function to format file size
function formatFileSize($bytes) {
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

// Handle AJAX processing request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'convert') {
    header('Content-Type: application/json');

    error_log("=== MEDICALVOICE AI PROCESSING START ===");
    error_log("Session ID: " . session_id());
    error_log("User ID: " . $userId);
    error_log("Uploaded file: " . ($_SESSION['uploaded_file'] ?? 'Not set'));
    error_log("With documents: " . ($_POST['with_documents'] ?? 'false'));

    // Check if file exists in session
    if (!isset($_SESSION['uploaded_file']) || !file_exists($_SESSION['uploaded_file'])) {
        echo json_encode(['success' => false, 'message' => 'No audio file found. Please upload a file first.']);
        exit;
    }

    // Get document texts if documents were uploaded
    $documentTexts = [];
    if (isset($_POST['with_documents']) && $_POST['with_documents'] === 'true' && isset($_SESSION['transcription_id'])) {
        $stmt = $conn->prepare("SELECT extracted_text FROM transcription_documents WHERE transcription_id = ? AND member_id = ?");
        $stmt->bind_param("ii", $_SESSION['transcription_id'], $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            if ($row['extracted_text']) {
                $documentTexts[] = $row['extracted_text'];
            }
        }
        $stmt->close();

        error_log("Found " . count($documentTexts) . " documents for combined analysis");
    }
    
    // Check user credits
    $stmt = $conn->prepare("SELECT credits FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($credits);
    $stmt->fetch();
    $stmt->close();
    
    if ($credits <= 0) {
        echo json_encode(['success' => false, 'message' => 'Insufficient credits. Please purchase more credits.']);
        exit;
    }
    
    // Get file paths
    $audioFile = $_SESSION['uploaded_file'];
    $logFile = $_SESSION['log_file'] ?? str_replace('/files/', '/logs/', $audioFile) . '.log';
    
    // Clear any existing log file
    if (file_exists($logFile)) {
        unlink($logFile);
    }
    
    // Prepare Python command
    $pythonScript = __DIR__ . "/process_audio.py";

    if (!file_exists($pythonScript)) {
        echo json_encode(['success' => false, 'message' => 'Processing script not found.']);
        exit;
    }

    // Build command with document texts if available
    if (!empty($documentTexts)) {
        // Save document texts to temporary file
        $docFile = $logDir . pathinfo($audioFile, PATHINFO_FILENAME) . '_docs.txt';
        file_put_contents($docFile, implode("\n---DOCUMENT---\n", $documentTexts));
        $command = "python3 \"$pythonScript\" \"$audioFile\" \"$logFile\" \"$docFile\" 2>&1";
        error_log("Executing command with documents: $command");
    } else {
        // Original command without documents
        $command = "python3 \"$pythonScript\" \"$audioFile\" \"$logFile\" 2>&1";
        error_log("Executing command: $command");
    }
    
    // Execute Python script
    $output = shell_exec($command);
    
    error_log("Python output: " . substr($output, 0, 500));
    
    // Parse the output
    $lines = explode("\n", $output);
    $lastLine = '';
    foreach ($lines as $line) {
        if (trim($line) !== '') {
            $lastLine = $line;
        }
    }
    
    // Try to decode JSON response
    $responseData = json_decode($lastLine, true);
    
    if ($responseData && isset($responseData['json_file'])) {
        // Success - deduct credit
        $stmt = $conn->prepare("UPDATE members SET credits = credits - 1 WHERE member_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();

        // Store JSON file path in session
        $_SESSION['json_file'] = $responseData['json_file'];
        $_SESSION['processing_complete'] = true;

        // Update transcription record if exists
        if (isset($_SESSION['transcription_id'])) {
            $transcriptionText = isset($responseData['database_data']['extracted_text']) ? $responseData['database_data']['extracted_text'] : '';
            $medicalAnalysis = isset($responseData['database_data']['fhir_json']) ? $responseData['database_data']['fhir_json'] : '';

            $stmt = $conn->prepare("UPDATE medicalvoice_transcriptions SET
                transcription_text = ?,
                medical_analysis = ?,
                json_output_path = ?,
                processing_status = 'completed',
                processing_time_seconds = TIMESTAMPDIFF(SECOND, created_at, NOW()),
                updated_at = NOW()
                WHERE transcription_id = ?");

            $stmt->bind_param("sssi",
                $transcriptionText,
                $medicalAnalysis,
                $responseData['json_file'],
                $_SESSION['transcription_id']
            );

            if ($stmt->execute()) {
                error_log("MedicalVoice: Updated transcription record ID: " . $_SESSION['transcription_id']);
            } else {
                error_log("MedicalVoice: Failed to update transcription record: " . $conn->error);
            }
            $stmt->close();
        }
        
        // Store data in fhir_audio table if database_data is available
        if (isset($responseData['database_data'])) {
            try {
                $stmt = $conn->prepare("INSERT INTO fhir_audio (member_id, audio_path, extracted_text, fhir_json) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", 
                    $userId, 
                    $responseData['database_data']['audio_path'],
                    $responseData['database_data']['extracted_text'],
                    $responseData['database_data']['fhir_json']
                );
                $stmt->execute();
                $stmt->close();
                
                error_log("MedicalVoice: Data stored in fhir_audio table successfully");
            } catch (Exception $e) {
                error_log("MedicalVoice: Failed to store data in fhir_audio table: " . $e->getMessage());
                // Continue with processing even if database storage fails
            }
        }
        
        // Read the JSON file to get the actual results
        $jsonContent = null;
        if (isset($responseData['json_file']) && file_exists($responseData['json_file'])) {
            $jsonContent = json_decode(file_get_contents($responseData['json_file']), true);
        }

        // Get uploaded documents for this transcription
        $uploadedDocuments = [];
        if (isset($_SESSION['transcription_id'])) {
            $stmt = $conn->prepare("SELECT id, original_filename, stored_filename, file_type, file_size, created_at
                                    FROM transcription_documents
                                    WHERE transcription_id = ? AND member_id = ?");
            $stmt->bind_param("ii", $_SESSION['transcription_id'], $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $uploadedDocuments[] = [
                    'id' => $row['id'],
                    'original_name' => $row['original_filename'],
                    'stored_name' => $row['stored_filename'],
                    'file_type' => $row['file_type'],
                    'file_size' => $row['file_size'],
                    'created_at' => $row['created_at'],
                    'download_url' => 'download_document.php?id=' . $row['id']
                ];
            }
            $stmt->close();
        }

        echo json_encode([
            'success' => true,
            'message' => 'Processing completed successfully!',
            'json_file' => $responseData['json_file'],
            'results' => $jsonContent,
            'documents' => $uploadedDocuments
        ]);
    } else {
        // Check for error in response
        if ($responseData && isset($responseData['error'])) {
            echo json_encode(['success' => false, 'message' => $responseData['error']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Processing failed. Please try again.']);
        }
    }
    exit;
}

// Include header for HTML pages
include('header.php');
?>

<style>
/* Text wrapping fixes for MedicalVoice results display */
.results-text,
.transcription-content,
.border p,
.card-body p {
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
    white-space: normal;
    max-width: 100%;
    display: block;
}

/* Handle pre tags for medical analysis */
.medical-analysis-content,
.analysis-pre,
.border pre {
    white-space: pre-wrap;
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
    overflow-x: auto;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    line-height: 1.5;
}

/* Container overflow protection */
.card-body {
    overflow-x: auto;
    max-width: 100%;
}

/* Results section specific styles */
.bg-light {
    overflow-x: auto;
    max-width: 100%;
}

/* Ensure proper text display in results */
.border.p-3 {
    max-width: 100%;
    overflow-x: auto;
}
</style>

<div class="container mt-4">
    <h2>AI Audio Processing</h2>
    
    <!-- Session Status -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Session Status</h5>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['uploaded_file'])): ?>
                <?php if (file_exists($_SESSION['uploaded_file'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> 
                        <strong>File Ready:</strong> <?php echo basename($_SESSION['uploaded_file']); ?>
                        (<?php echo formatFileSize(filesize($_SESSION['uploaded_file'])); ?>)
                    </div>
                    
                    <!-- Processing Button -->
                    <div class="text-center mt-3">
                        <button id="startProcessingBtn" class="btn btn-primary btn-lg" onclick="startAIProcessing()">
                            <i class="fas fa-play"></i> Start AI Processing
                        </button>
                    </div>
                    
                    <!-- Progress Display -->
                    <div id="processingProgress" style="display: none;" class="mt-4">
                        <div class="progress">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                 style="width: 0%"></div>
                        </div>
                        <p class="text-center mt-2" id="progressText">Initializing...</p>
                    </div>
                    
                    <!-- Results Display -->
                    <div id="processingResults" style="display: none;" class="mt-4">
                        <div class="alert alert-info">
                            <h5>Processing Complete!</h5>
                            <div id="resultsContent"></div>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        File in session but not found on disk. 
                        <a href="index.php" class="btn btn-sm btn-primary">Upload New File</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    No audio file uploaded. 
                    <a href="index.php" class="btn btn-primary">Upload Audio File</a>
                    <button class="btn btn-secondary ms-2" onclick="showQuickUpload()">Quick Upload</button>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Results Section if already processed -->
    <?php if (isset($_SESSION['json_file']) && file_exists($_SESSION['json_file'])): ?>
    <div class="card">
        <div class="card-header">
            <h5>Previous Processing Results</h5>
        </div>
        <div class="card-body">
            <?php
            $jsonContent = file_get_contents($_SESSION['json_file']);
            $data = json_decode($jsonContent, true);
            if ($data):
            ?>
                <h6>Transcribed Text:</h6>
                <div class="border p-3 bg-light">
                    <p class="results-text transcription-content"><?php echo htmlspecialchars($data['transcribed_text'] ?? 'No transcription'); ?></p>
                </div>
                
                <h6>Medical Analysis:</h6>
                <div class="border p-3 bg-light">
                    <pre class="medical-analysis-content analysis-pre"><?php echo htmlspecialchars($data['medical_analysis'] ?? $data['chatgpt_response'] ?? 'No analysis'); ?></pre>
                </div>
                
                <div class="mt-3">
                    <a href="download_json.php" class="btn btn-success">
                        <i class="fas fa-download"></i> Download JSON
                    </a>
                    <a href="clear_session.php" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> New Analysis
                    </a>
                </div>
            <?php else: ?>
                <p>Unable to load results.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Quick Upload Modal -->
<div class="modal fade" id="quickUploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Audio Upload</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickUploadForm">
                    <div class="mb-3">
                        <label for="audioFile" class="form-label">Select Audio File</label>
                        <input type="file" class="form-control" id="audioFile" accept="audio/*" required>
                        <div class="form-text">MP3, M4A, WAV, AAC (Max 50MB)</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="uploadQuickFile()">Upload</button>
            </div>
        </div>
    </div>
</div>

<script>
// Show quick upload modal
function showQuickUpload() {
    const modal = new bootstrap.Modal(document.getElementById('quickUploadModal'));
    modal.show();
}

// Upload quick file
function uploadQuickFile() {
    const fileInput = document.getElementById('audioFile');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Please select a file');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'upload_file');
    formData.append('audio_file', file);
    
    // Use the current page URL to ensure correct path resolution
    const currentUrl = window.location.href;
    const baseUrl = currentUrl.substring(0, currentUrl.lastIndexOf('/'));
    const uploadUrl = baseUrl + '/upload.php';
    
    console.log('Uploading to:', uploadUrl);
    
    fetch(uploadUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Upload failed: ' + data.message);
        }
    })
    .catch(error => {
        alert('Upload error: ' + error);
    });
}

// Start AI Processing
function startAIProcessing() {
    const btn = document.getElementById('startProcessingBtn');
    const progress = document.getElementById('processingProgress');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    
    // Disable button and show progress
    btn.disabled = true;
    progress.style.display = 'block';
    
    // Animate progress
    let progressValue = 0;
    const progressInterval = setInterval(() => {
        progressValue += Math.random() * 15;
        if (progressValue > 90) progressValue = 90;
        progressBar.style.width = progressValue + '%';
    }, 1000);
    
    // Update status messages
    const messages = [
        'Initializing AI processing...',
        'Transcribing audio with AssemblyAI...',
        'Analyzing medical content...',
        'Generating insights with OpenAI...',
        'Finalizing results...'
    ];
    
    let messageIndex = 0;
    const messageInterval = setInterval(() => {
        if (messageIndex < messages.length) {
            progressText.textContent = messages[messageIndex];
            messageIndex++;
        }
    }, 3000);
    
    // Make AJAX request
    const formData = new FormData();
    formData.append('action', 'convert');
    
    // Use the current page URL to ensure correct path resolution
    const currentUrl = window.location.href;
    const baseUrl = currentUrl.substring(0, currentUrl.lastIndexOf('/'));
    const fetchUrl = baseUrl + '/python_process.php';
    
    console.log('Fetching from:', fetchUrl);
    
    fetch(fetchUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        clearInterval(progressInterval);
        clearInterval(messageInterval);
        
        if (data.success) {
            progressBar.style.width = '100%';
            progressText.textContent = 'Processing complete!';
            
            // Show success and reload
            setTimeout(() => {
                alert('Processing completed successfully!');
                window.location.reload();
            }, 1000);
        } else {
            alert('Processing failed: ' + data.message);
            btn.disabled = false;
            progress.style.display = 'none';
        }
    })
    .catch(error => {
        clearInterval(progressInterval);
        clearInterval(messageInterval);
        
        console.error('Processing error:', error);
        alert('Processing failed. Please check the console for details.');
        btn.disabled = false;
        progress.style.display = 'none';
    });
}

// Auto-start processing if redirected from upload
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('auto_start') === 'true') {
        const uploadSuccess = sessionStorage.getItem('uploadSuccess');
        if (uploadSuccess === 'true') {
            sessionStorage.removeItem('uploadSuccess');
            setTimeout(() => {
                const btn = document.getElementById('startProcessingBtn');
                if (btn) {
                    console.log('Auto-starting processing...');
                    startAIProcessing();
                }
            }, 1000);
        }
    }
});
</script>

<?php include('footer.php'); ?>
