<!-- Upload Section -->
<h2 class="mb-4 text-primary">
    <i class="fas fa-upload"></i> Upload an Audio File
</h2>

<!-- Upload Form -->
<form action="" method="post" enctype="multipart/form-data" class="row g-3 align-items-center mb-4">
    <div class="col-md-8">
        <input type="file" name="audio_file" class="form-control form-control-lg" accept="audio/*" required>
    </div>
    <div class="col-md-4 d-grid">
        <input type="submit" name="upload" value="Upload" class="btn btn-primary btn-lg">
    </div>
</form>

<?php
// ✅ Define storage paths inside medicalvoice
$baseDir = __DIR__ . '/storage/uploads/audio';
$audioDir = $baseDir . '/files/';
$jsonDir = $baseDir . '/json/';
$logDir = $baseDir . '/logs/';

// ✅ Ensure directories exist
foreach ([$audioDir, $jsonDir, $logDir] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// ✅ Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['audio_file']['tmp_name'];
        $fileName = basename($_FILES['audio_file']['name']);
        $targetFile = $audioDir . $fileName;

        if (move_uploaded_file($fileTmp, $targetFile)) {
            $_SESSION['uploaded_file'] = $targetFile;
            $_SESSION['log_file'] = $logDir . pathinfo($fileName, PATHINFO_FILENAME) . '.log';
            echo "<div class='alert alert-success mt-3 p-3'>
                    ✅ File uploaded successfully: <strong>$fileName</strong>
                  </div>";
        } else {
            echo "<div class='alert alert-danger mt-3 p-3'>❌ Failed to move uploaded file.</div>";
        }
    } else {
        echo "<div class='alert alert-warning mt-3 p-3'>No file uploaded or an error occurred.</div>";
    }
}
?>
