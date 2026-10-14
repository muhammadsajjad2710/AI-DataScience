<?php
include('database.php');

// Start session early for AJAX upload handler
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// AJAX: handle image upload on this page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_image') {
    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'No image selected or upload error']);
        exit;
    }

    $uploadDir = __DIR__ . "/../storage/uploads/images/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = time() . '_' . basename($_FILES['image']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $_SESSION['uploaded_image'] = $targetPath;
        echo json_encode(['status' => 'success', 'message' => 'Image uploaded successfully', 'file' => $fileName]);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
    exit;
}

include('header.php');

$userId = $_SESSION['member_id'];
$stmt = $conn->prepare("SELECT credits FROM members WHERE member_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($credits);
$stmt->fetch();
$stmt->close();
?>

<!-- Enhanced MedicalVision Dashboard -->
<div class="dashboard-container">
    <!-- Hero Section -->
    <div class="hero-section text-center py-5 mb-5">
        <div class="hero-content">
            <h1 class="hero-title mb-3">
                <i class="fas fa-eye text-primary me-3"></i>
                Medical Vision AI
            </h1>
            <p class="hero-subtitle text-muted fs-5">
                Advanced AI-powered document analysis with handwritten text recognition and medical data extraction
            </p>
        </div>
    </div>

    <!-- Credits Display -->
    <div class="credits-card mb-5">
        <div class="credits-content">
            <div class="credits-info">
                <div class="credits-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="credits-details">
                    <h3 class="credits-label">AVAILABLE CREDITS</h3>
                    <div class="credits-balance"><?php echo (int)$credits; ?></div>
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

    <!-- Messages Area -->
    <div id="messages" class="container mb-4" style="display:none;" aria-live="polite"></div>

    <!-- Three-step inline flow -->
    <div class="features-grid">
        <!-- Step 1: Upload -->
        <div class="feature-card" id="step1">
            <div class="feature-header">
                <div class="feature-icon upload-icon"><i class="fas fa-upload"></i></div>
                <div class="feature-info">
                    <h3 class="feature-title">Upload</h3>
                    <p class="feature-description">Start by uploading a medical image or document</p>
                </div>
            </div>
            <div class="feature-content">
                <div class="upload-area text-center" id="uploadArea" tabindex="0" role="region" aria-label="Image upload area">
                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3" aria-hidden="true"></i>
                    <h5 class="mb-1">Drag & Drop Image Here</h5>
                    <p class="text-muted mb-3">or click to browse</p>
                    <input type="file" id="imageInput" accept="image/*" style="display:none;" aria-label="Choose image file">
                    <button class="btn btn-outline-primary" id="chooseBtn" type="button">
                        <i class="fas fa-folder-open me-2" aria-hidden="true"></i>Choose Image
                    </button>
                </div>
                <div id="imagePreview" class="text-center mt-3" style="display:none;">
                    <img id="previewImg" class="image-preview shadow" alt="Preview">
                    <div class="mt-3">
                        <button class="btn btn-success" id="proceedToStep2">
                            <i class="fas fa-arrow-right me-2"></i>Proceed to Extract Text
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Extract Text -->
        <div class="feature-card" id="step2">
            <div class="feature-header">
                <div class="feature-icon upload-icon"><i class="fas fa-magic"></i></div>
                <div class="feature-info">
                    <h3 class="feature-title">Extract Text</h3>
                    <p class="feature-description">Run OCR and AI processing on the uploaded image</p>
                </div>
            </div>
            <div class="feature-content text-center">
                <button class="btn btn-info" id="extractTextBtn" type="button">
                    <i class="fas fa-magic me-2" aria-hidden="true"></i>Extract Text & Process
                </button>
                <div id="processingStatus" class="text-center mt-3" style="display:none;" aria-live="polite" role="status">
                    <div class="spinner-border text-info" role="presentation" aria-hidden="true"></div>
                    <p class="mt-2 text-muted">Processing image with OCR and AI...</p>
                </div>
            </div>
        </div>

        <!-- Step 3: Read Response -->
        <div class="feature-card" id="step3">
            <div class="feature-header">
                <div class="feature-icon upload-icon"><i class="fas fa-book-open"></i></div>
                <div class="feature-info">
                    <h3 class="feature-title">Read Response</h3>
                    <p class="feature-description">View extracted text and AI (FHIR) response</p>
                </div>
            </div>
            <div class="feature-content text-center">
                <button class="btn btn-success" id="readResponseBtn">
                    <i class="fas fa-book-open me-2"></i>Read Response
                </button>
            </div>
        </div>

        <!-- Results -->
        <div class="feature-card" id="result" style="display:none;">
            <div class="feature-header">
                <div class="feature-icon upload-icon"><i class="fas fa-check"></i></div>
                <div class="feature-info">
                    <h3 class="feature-title">Results</h3>
                    <p class="feature-description">Extracted text and AI response</p>
                </div>
            </div>
            <div class="feature-content">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="fas fa-file-text me-2"></i>Extracted Text</h6>
                        <div class="bg-light p-3 rounded border" style="max-height:400px; overflow-y:auto;">
                            <pre id="extractedText" class="mb-0" style="white-space:pre-wrap; font-family:inherit;"></pre>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success"><i class="fas fa-robot me-2"></i>AI Response (FHIR)</h6>
                        <div class="bg-light p-3 rounded border" style="max-height:400px; overflow-y:auto;">
                            <pre id="aiResponse" class="mb-0" style="white-space:pre-wrap; font-family:inherit;"></pre>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-muted small d-flex flex-wrap gap-3">
                    <div><i class="fas fa-id-card me-1"></i>Document ID: <span id="documentId">-</span></div>
                    <div><i class="fas fa-clock me-1"></i>Processed: <span id="timestamp">-</span></div>
                    <div><i class="fas fa-save me-1"></i>JSON: <span id="jsonFile">-</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles specific to steps -->
<style>
:root {
    --mv-bg: #f8fafc;
    --mv-card: #ffffff;
    --mv-text: #2d3748;
    --mv-muted: #718096;
    --mv-border: #e2e8f0;
    --mv-primary: #667eea;
    --mv-secondary: #764ba2;
    --mv-success: #48bb78;
    --mv-shadow: 0 10px 30px rgba(0,0,0,0.08);
    --mv-shadow-lg: 0 20px 50px rgba(0,0,0,0.12);
}

.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px 24px;
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
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800;
    margin-bottom: 1.5rem;
    text-shadow: 0 4px 20px rgba(0,0,0,0.3);
    line-height: 1.1;
    animation: fadeInUp 1s ease-out;
}

.hero-subtitle {
    font-size: clamp(1.1rem, 2.5vw, 1.3rem);
    font-weight: 400;
    opacity: 0.95;
    max-width: 700px;
    margin: 0 auto 2rem;
    line-height: 1.6;
    animation: fadeInUp 1s ease-out 0.2s both;
}

/* Credits Card */
.credits-card {
    background: var(--mv-card);
    border-radius: 16px;
    padding: 1.5rem 1.75rem;
    box-shadow: var(--mv-shadow);
    border: 1px solid var(--mv-border);
    animation: fadeInUp 0.6s ease-out 0.2s both;
}

.credits-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
}

.credits-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.credits-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #333;
    font-size: 1.5rem;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.credits-label {
    font-size: 1rem;
    font-weight: 600;
    color: #718096;
    margin: 0 0 0.5rem 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.credits-balance {
    font-size: 2.5rem;
    font-weight: 800;
    color: #2d3748;
    margin: 0 0 0.5rem 0;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.credits-description {
    color: #718096;
    margin: 0;
    font-size: 0.9rem;
}

.credits-action .btn {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.credits-action .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

/* Features Grid */
.features-grid {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    margin-bottom: 3rem;
}

.feature-card {
    background: var(--mv-card);
    border-radius: 16px;
    box-shadow: var(--mv-shadow);
    overflow: hidden;
    border: 1px solid var(--mv-border);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    animation: fadeInUp 0.5s ease-out both;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: var(--primary-gradient, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.feature-card:hover::before {
    transform: scaleX(1);
}

.feature-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--mv-shadow-lg);
}

.feature-header {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.25rem 1.5rem 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
    border-bottom: 1px solid var(--mv-border);
}

.feature-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.25rem;
    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
}

.upload-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.feature-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
    color: var(--mv-text);
    line-height: 1.2;
}

.feature-description {
    color: var(--mv-muted);
    margin: 0.25rem 0 0;
    line-height: 1.6;
    font-size: 0.95rem;
}

.feature-content {
    padding: 1.25rem 1.5rem 1.5rem;
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
    .credits-content {
        flex-direction: column;
        text-align: center;
    }
    
    .credits-action {
        flex-direction: column;
        gap: 1rem;
    }
    
    .feature-header {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .feature-icon {
        margin: 0 auto;
    }
}

@media (max-width: 480px) {
    .hero-section {
        padding: 2rem 1rem;
    }
    
    .credits-card {
        padding: 1.5rem;
    }
    
    .feature-card {
        margin: 0 -10px;
    }
}

.upload-area { border: 2px dashed var(--mv-border); border-radius: 12px; padding: 32px; transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease; outline: none; }
.upload-area:hover, .upload-area:focus { border-color: var(--mv-primary); background: #f8f9ff; box-shadow: 0 8px 20px rgba(102, 126, 234, 0.12); }
.upload-area.dragover { border-color: var(--mv-primary); background: #eef2ff; }
.image-preview { max-width: 100%; max-height: 320px; border-radius: 10px; border: 1px solid var(--mv-border); }

/* Buttons refinements */
.btn { border-radius: 10px; font-weight: 600; }
.btn-info { background: linear-gradient(135deg, var(--mv-primary), var(--mv-secondary)); border: none; }
.btn-info:hover { filter: brightness(1.05); transform: translateY(-1px); }
.btn-outline-primary { border-width: 2px; }

/* Reduced motion preference */
@media (prefers-reduced-motion: reduce) {
  * { animation: none !important; transition: none !important; }
}
</style>

<script>
(function(){
  const messages = document.getElementById('messages');
  function showMessage(type, text){
    messages.style.display = 'block';
    messages.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">${text}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
  }

  const uploadArea = document.getElementById('uploadArea');
  const imageInput = document.getElementById('imageInput');
  const chooseBtn = document.getElementById('chooseBtn');
  const imagePreview = document.getElementById('imagePreview');
  const previewImg = document.getElementById('previewImg');
  const proceedBtn = document.getElementById('proceedToStep2');
  const extractBtn = document.getElementById('extractTextBtn');
  const readBtn = document.getElementById('readResponseBtn');
  const processingStatus = document.getElementById('processingStatus');
  const resultCard = document.getElementById('result');

  chooseBtn.addEventListener('click', () => imageInput.click());

  uploadArea.addEventListener('dragover', (e)=>{ e.preventDefault(); uploadArea.classList.add('dragover'); });
  uploadArea.addEventListener('dragleave', ()=> uploadArea.classList.remove('dragover'));
  uploadArea.addEventListener('drop', (e)=>{ e.preventDefault(); uploadArea.classList.remove('dragover'); if(e.dataTransfer.files.length){ handleUpload(e.dataTransfer.files[0]); }});
  imageInput.addEventListener('change', (e)=>{ if(e.target.files.length){ handleUpload(e.target.files[0]); }});

  function handleUpload(file){
    if(!file.type.startsWith('image/')){ showMessage('warning','Please select an image file.'); return; }
    const formData = new FormData();
    formData.append('action','upload_image');
    formData.append('image', file);

    fetch(window.location.href, { method:'POST', body: formData })
      .then(r=>r.json())
      .then(data=>{
        if(data.status==='success'){
          const reader = new FileReader();
          reader.onload = (e)=>{
            previewImg.src = e.target.result;
            imagePreview.style.display = 'block';
            uploadArea.style.display = 'none';
            document.getElementById('step2').scrollIntoView({behavior:'smooth'});
          };
          reader.readAsDataURL(file);
          showMessage('success','Image uploaded successfully.');
        } else {
          showMessage('danger', 'Upload failed: ' + (data.message||'Unknown error'));
        }
      })
      .catch(()=> showMessage('danger','Upload failed. Please try again.'));
  }

  proceedBtn.addEventListener('click', ()=>{
    document.getElementById('step2').scrollIntoView({behavior:'smooth'});
  });

  extractBtn.addEventListener('click', async ()=>{
    extractBtn.disabled = true;
    processingStatus.style.display = 'block';
    try{
      const resp = await fetch('process_complete.php', { method:'POST', body: JSON.stringify({action:'extract_text'}), headers:{'Content-Type':'application/json'} });
      const result = await resp.json();
      if(result.status==='success'){
        window.processingResults = result.data;
        showMessage('success','Processing completed successfully.');
        document.getElementById('step3').scrollIntoView({behavior:'smooth'});
      }else{
        showMessage('danger','Processing failed: '+ (result.message||'Unknown error'));
      }
    }catch(e){ showMessage('danger','Processing failed. Please try again.'); }
    finally{ processingStatus.style.display = 'none'; extractBtn.disabled = false; }
  });

  readBtn.addEventListener('click', ()=>{
    if(!window.processingResults){ showMessage('warning','Please complete extraction first.'); return; }
    document.getElementById('extractedText').textContent = window.processingResults.extracted_text;
    document.getElementById('aiResponse').textContent = JSON.stringify(window.processingResults.fhir_data, null, 2);
    document.getElementById('documentId').textContent = window.processingResults.document_id;
    document.getElementById('timestamp').textContent = window.processingResults.timestamp;
    document.getElementById('jsonFile').textContent = window.processingResults.json_file.split('/').pop();
    resultCard.style.display = 'block';
    resultCard.scrollIntoView({behavior:'smooth'});
  });
})();
</script>

<?php include('footer.php'); ?>