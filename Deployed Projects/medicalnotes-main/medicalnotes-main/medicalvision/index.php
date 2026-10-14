<?php
// Start output buffering to prevent any unwanted output
ob_start();

// Start session early for AJAX upload handler
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle AJAX requests first before any HTML output
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Clear any buffered output for clean JSON response
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    
    // Include configuration file for AJAX requests
    $configPath = dirname(__DIR__) . '/config.php';
    if (!file_exists($configPath)) {
        echo json_encode(['status' => 'error', 'message' => 'Configuration file not found']);
        exit;
    }
    require_once $configPath;
    
    // Check if user is logged in for AJAX requests
    if (!isset($_SESSION['member_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
        exit;
    }
    
    $userId = $_SESSION['member_id'];
    $credits = getUserCredits($userId);
    
    // Handle upload action
    if ($_POST['action'] === 'upload_image') {
        error_log("MedicalVision: Upload request received for user " . $userId);
        
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'error', 'message' => 'No image selected or upload error']);
            exit;
        }

        // Check if user has credits
        if ($credits < 1) {
            echo json_encode(['status' => 'error', 'message' => 'Insufficient credits. Please purchase more credits.']);
            exit;
        }
        
        $uploadDir = __DIR__ . "/storage/uploads/images/";
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory']);
                exit;
            }
        }
        
        // Ensure directory is writable
        if (!is_writable($uploadDir)) {
            echo json_encode(['status' => 'error', 'message' => 'Upload directory is not writable']);
            exit;
        }

        // New image - upload and process
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            // Store relative path for database
            $relativePath = "medicalvision/storage/uploads/images/" . $fileName;
            
            // Store in session for frontend access
            $_SESSION['uploaded_image'] = $targetPath;
            $_SESSION['uploaded_filename'] = $fileName;
            $_SESSION['upload_status'] = 'completed';
            $_SESSION['relative_path'] = $relativePath;
            
            // Return success response
            echo json_encode([
                'status' => 'success', 
                'message' => 'Image uploaded successfully', 
                'file' => $fileName,
                'next_step' => 'ocr_processing'
            ]);
            exit;
        }

        // Log error and return failure
        error_log("MedicalVision: Upload failed - Temp file: " . $_FILES['image']['tmp_name'] . ", Target path: " . $targetPath);
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
        exit;
    }
    
    // Handle OCR processing
    if ($_POST['action'] === 'process_ocr') {
        if (!isset($_SESSION['uploaded_image']) || !file_exists($_SESSION['uploaded_image'])) {
            echo json_encode(['status' => 'error', 'message' => 'No image found for processing']);
            exit;
        }
        
        try {
            $imagePath = $_SESSION['uploaded_image'];
            $relativePath = $_SESSION['relative_path'];
            
            // Execute OCR command
            $command = "python \"" . __DIR__ . "\\handwritten_to_text.py\" \"" . $imagePath . "\"";
            $output = shell_exec($command . " 2>&1");
            
            if ($output) {
                $result = json_decode($output, true);
                
                if ($result && isset($result['handwritten_text'])) {
                    // Save OCR results to database
                    $pdo = getDatabaseConnection();
                    if ($pdo) {
                        $stmt = $pdo->prepare("INSERT INTO fhir_documents (member_id, image_path, extracted_text, created_at) VALUES (?, ?, ?, NOW())");
                        $stmt->execute([$userId, $relativePath, $result['handwritten_text']]);
                        $documentId = $pdo->lastInsertId();
                        $_SESSION['document_id'] = $documentId;
                    }
                    
                    // Deduct 1 credit
                    deductCredits($userId, 1, 'MedicalVision OCR processing');
                    
                    $_SESSION['extracted_text'] = $result['handwritten_text'];
                    
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Text extracted successfully',
                        'text' => $result['handwritten_text'],
                        'document_id' => $documentId
                    ]);
                    exit;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to extract text from image']);
                    exit;
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'OCR processing failed']);
                exit;
            }
            
        } catch (Exception $e) {
            error_log("MedicalVision: OCR processing error: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'OCR processing error: ' . $e->getMessage()]);
            exit;
        }
    }
    
    // Handle AI analysis action
    if ($_POST['action'] === 'process_ai') {
        if (!isset($_SESSION['extracted_text']) || empty($_SESSION['extracted_text'])) {
            echo json_encode(['status' => 'error', 'message' => 'No extracted text available for AI analysis']);
            exit;
        }
        
        try {
            $extractedText = $_SESSION['extracted_text'];
            $userId = $_SESSION['member_id'];
            
            // Check if user has credits for AI analysis
            $currentCredits = getUserCredits($userId);
            if ($currentCredits < 1) {
                echo json_encode(['status' => 'error', 'message' => 'Insufficient credits for AI analysis. Please purchase more credits.']);
                exit;
            }
            
            error_log("MedicalVision: Starting AI analysis for text length: " . strlen($extractedText));
            
            // Prepare the prompt for analysis
            $prompt = "Analyze this text and provide a comprehensive analysis. If it contains medical information, provide medical insights. If it's about travel, education, or other topics, provide relevant analysis. Include key points, summary, and recommendations.\n\nText to analyze: " . $extractedText;
            
            // Make API request to OpenAI
            // Temporary: Use hardcoded API key for testing
            $openai_api_key = 'YOUR_API_KEY_HERE';
            
            // API key is now hardcoded for testing
            
            $url = "https://api.openai.com/v1/chat/completions";
            
            $data = [
                "model" => "gpt-4",
                "messages" => [
                    ["role" => "system", "content" => "You are an intelligent AI assistant that provides comprehensive analysis of any text content."],
                    ["role" => "user", "content" => $prompt]
                ],
                "max_tokens" => 2000,
                "temperature" => 0.3
            ];
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer " . $openai_api_key,
                "Content-Type: application/json"
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            error_log("MedicalVision: OpenAI API HTTP Code: " . $httpCode);
            
            if ($httpCode !== 200) {
                throw new Exception("OpenAI API error: HTTP $httpCode - $response");
            }
            
            if ($curlError) {
                throw new Exception("cURL Error: " . $curlError);
            }
            
            $result = json_decode($response, true);
            if (isset($result['choices'][0]['message']['content'])) {
                $aiAnalysis = $result['choices'][0]['message']['content'];
                
                error_log("MedicalVision: AI analysis completed successfully");
                
                // Update database with AI analysis
                $pdo = getDatabaseConnection();
                if ($pdo && isset($_SESSION['document_id'])) {
                    $stmt = $pdo->prepare("UPDATE fhir_documents SET fhir_json = ? WHERE id = ?");
                    $fhirData = json_encode([
                        'ai_analysis' => $aiAnalysis,
                        'extracted_text' => $extractedText,
                        'analysis_timestamp' => date('Y-m-d H:i:s')
                    ]);
                    $stmt->execute([$fhirData, $_SESSION['document_id']]);
                }
                
                // Store AI analysis in session
                $_SESSION['ai_analysis'] = $aiAnalysis;
                $_SESSION['ai_status'] = 'completed';
                
                // Deduct 1 credit for AI analysis
                deductCredits($userId, 1, 'MedicalVision AI analysis');
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'AI analysis completed successfully',
                    'analysis' => $aiAnalysis,
                    'ai_status' => 'completed'
                ]);
                exit;
            } else {
                throw new Exception("Invalid OpenAI API response format");
            }
            
        } catch (Exception $e) {
            error_log("MedicalVision: AI analysis exception: " . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'AI analysis failed: ' . $e->getMessage()
            ]);
            exit;
        }
    }
    
    // If we get here, the action was not recognized
    echo json_encode(['status' => 'error', 'message' => 'Invalid action specified']);
    exit;
}

// If not a POST request, continue with normal page display
include('header.php');

// Get user credits for display
$credits = 0;
if (isset($_SESSION['member_id'])) {
    // Include configuration file for credits display
    $configPath = dirname(__DIR__) . '/config.php';
    if (file_exists($configPath)) {
        require_once $configPath;
        $credits = getUserCredits($_SESSION['member_id']);
    }
}
?>

<!-- Enhanced MedicalVision Dashboard -->
<div class="dashboard-container">
    <!-- Hero Section -->
    <section class="hero-section" role="banner">
        <div class="hero-content">
            <h1 class="hero-title">Chief.AI MedicalVision</h1>
            <p class="hero-subtitle">
                Advanced AI-powered medical document analysis with OCR technology and intelligent text extraction. 
                Built by Chief.AI - where intelligence meets innovation.
            </p>
        </div>
    </section>

    <!-- Credits Display -->
    <div class="credits-display animate-fade-in-up">
        <div class="credits-number"><?php echo (int)$credits; ?></div>
        <div class="credits-label">Credits Available</div>
        <a href="../buy_credits.php" class="btn btn-primary">
            <span class="material-icons-round">account_balance_wallet</span>
            Buy Credits
        </a>
    </div>

    <!-- Messages Area -->
    <div id="messages" class="messages-area" style="display:none;" aria-live="polite"></div>

    <!-- Upload Section -->
    <div class="features-grid">
        <div class="feature-card" id="step1">
            <div class="feature-header">
                <div class="feature-icon upload-icon">
                    <span class="material-icons-round">cloud_upload</span>
                </div>
                <div class="feature-info">
                    <h3 class="feature-title">Upload Medical Image</h3>
                    <p class="feature-description">Start by uploading a medical image</p>
                </div>
            </div>
            <div class="feature-content">
                <div class="upload-area" id="uploadArea" tabindex="0" role="region" aria-label="Image upload area">
                    <div class="upload-icon">
                        <span class="material-icons-round">cloud_upload</span>
                    </div>
                    <div class="upload-text">Drop your medical image here</div>
                    <div class="upload-hint">or click to browse (JPG, PNG, GIF, BMP, WebP up to 10MB)</div>
                    <input type="file" id="imageFile" accept="image/*" style="display: none;">
                </div>
            </div>
        </div>

        <!-- Processing Section -->
        <div class="feature-card" id="step2" style="display:none;">
            <div class="feature-header">
                <div class="feature-icon">
                    <span class="material-icons-round">visibility</span>
                </div>
                <div class="feature-info">
                    <h3 class="feature-title">Processing</h3>
                    <p class="feature-description">Extracting text with advanced OCR</p>
                </div>
            </div>
            <div class="feature-content">
                <div class="processing-area">
                    <div class="spinner-ring"></div>
                    <div class="processing-text">Processing your document...</div>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="feature-card" id="step3" style="display:none;">
            <div class="feature-header">
                <div class="feature-icon">
                    <span class="material-icons-round">task_alt</span>
                </div>
                <div class="feature-info">
                    <h3 class="feature-title">Results</h3>
                    <p class="feature-description">Extracted text and analysis</p>
                </div>
            </div>
            <div class="feature-content">
                <div class="results-area">
                    <div class="extracted-text" id="extractedText"></div>
                    <div class="action-buttons">
                        <button class="btn btn-primary" onclick="processAI()">Analyze with AI</button>
                        <button class="btn btn-secondary" onclick="downloadResults()">Download Results</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Analysis Section -->
        <div class="feature-card" id="step4" style="display:none;">
            <div class="feature-header">
                <div class="feature-icon">
                    <span class="material-icons-round">psychology</span>
                </div>
                <div class="feature-info">
                    <h3 class="feature-title">AI Analysis</h3>
                    <p class="feature-description">Intelligent text analysis and insights</p>
                </div>
            </div>
            <div class="feature-content">
                <div class="ai-analysis-area">
                    <div class="ai-processing" id="aiProcessing" style="display:block;">
                        <div class="spinner-ring"></div>
                        <div class="processing-text">AI is analyzing your text...</div>
                    </div>
                    <div class="ai-results" id="aiResults" style="display:none;">
                        <div class="analysis-content" id="analysisContent"></div>
                        <button class="btn btn-primary" onclick="downloadAIResults()">Download AI Analysis</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Simple upload functionality
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const imageFile = document.getElementById('imageFile');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    const step4 = document.getElementById('step4');
    const messages = document.getElementById('messages');

    // Show message function
    function showMessage(message, type = 'info') {
        messages.innerHTML = `<div class="message ${type}">${message}</div>`;
        messages.style.display = 'block';
        setTimeout(() => {
            messages.style.display = 'none';
        }, 5000);
    }

    // Upload area click handler
    uploadArea.addEventListener('click', () => imageFile.click());

    // File selection handler
    imageFile.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            showMessage('Please select an image file', 'error');
            return;
        }

        // Validate file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            showMessage('File size must be less than 10MB', 'error');
            return;
        }

        // Show processing step
        step1.style.display = 'none';
        step2.style.display = 'block';

        // Upload file
        const formData = new FormData();
        formData.append('image', file);
        formData.append('action', 'upload_image');

        fetch('index.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showMessage('Image uploaded successfully!', 'success');
                setTimeout(() => {
                    processOCR();
                }, 1000);
            } else {
                showMessage(data.message || 'Upload failed', 'error');
                step1.style.display = 'block';
                step2.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            showMessage('Upload failed. Please try again.', 'error');
            step1.style.display = 'block';
            step2.style.display = 'none';
        });
    });

    // Process OCR
    function processOCR() {
        fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=process_ocr'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showMessage('Text extracted successfully!', 'success');
                step2.style.display = 'none';
                step3.style.display = 'block';
                document.getElementById('extractedText').textContent = data.text;
            } else {
                showMessage(data.message || 'OCR failed', 'error');
                step1.style.display = 'block';
                step2.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('OCR error:', error);
            showMessage('OCR processing failed. Please try again.', 'error');
            step1.style.display = 'block';
            step2.style.display = 'none';
        });
    }

    // Process AI analysis
    window.processAI = function() {
        // Show AI analysis step
        step3.style.display = 'none';
        step4.style.display = 'block';
        
        // Show processing
        document.getElementById('aiProcessing').style.display = 'block';
        document.getElementById('aiResults').style.display = 'none';
        
        // Call AI analysis API
        fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=process_ai'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showMessage('AI analysis completed successfully!', 'success');
                document.getElementById('aiProcessing').style.display = 'none';
                document.getElementById('aiResults').style.display = 'block';
                document.getElementById('analysisContent').innerHTML = data.analysis.replace(/\n/g, '<br>');
            } else {
                showMessage(data.message || 'AI analysis failed', 'error');
                step3.style.display = 'block';
                step4.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('AI analysis error:', error);
            showMessage('AI analysis failed. Please try again.', 'error');
            step3.style.display = 'block';
            step4.style.display = 'none';
        });
    };

    // Download results
    window.downloadResults = function() {
        const text = document.getElementById('extractedText').textContent;
        const blob = new Blob([text], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'extracted_text.txt';
        a.click();
        window.URL.revokeObjectURL(url);
    };

    // Download AI analysis results
    window.downloadAIResults = function() {
        const analysis = document.getElementById('analysisContent').textContent;
        const blob = new Blob([analysis], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'ai_analysis.txt';
        a.click();
        window.URL.revokeObjectURL(url);
    };
});
</script>

<?php 
// Include footer
$footerPath = __DIR__ . '/footer.php';
if (file_exists($footerPath)) {
    include($footerPath);
}
?>