<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['audio_file'])) {
    // Allowed audio formats (case insensitive)
    $allowedExtensions = ['mp3', 'm4a', 'mp4', 'webm', '3gp', 'wav', 'ogg'];

    // Get file details
    $fileName = $_FILES['audio_file']['name'];
    $fileTmpName = $_FILES['audio_file']['tmp_name'];
    $fileSize = $_FILES['audio_file']['size'];

    // Extract file extension (case insensitive)
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Get MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileTmpName);
    finfo_close($finfo);

    // Allowed MIME types for both audio and video
    $allowedMimeTypes = [
        'audio/mpeg', 'audio/mp4', 'audio/x-m4a', 'audio/webm', 'audio/3gpp', 
        'audio/wav', 'audio/ogg', 'video/mp4', 'video/webm', 'video/3gpp' // Added video types
    ];

    // Validate file type
    if (!in_array($fileExtension, $allowedExtensions) || !in_array($mimeType, $allowedMimeTypes)) {
        die("Error: Only allowed formats (MP3, M4A, MP4, WebM, 3GP, WAV, OGG) can be uploaded.");
    }

    // Define upload path
    $uploadDir = __DIR__ . '/../storage/uploads/';
    $targetFile = $uploadDir . basename($fileName);

    // Ensure upload directory exists
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
        die("Error: Failed to create upload directory.");
    }

    // Check if directory is writable
    if (!is_writable($uploadDir)) {
        die("Error: Upload directory is not writable.");
    }

    // Move file to storage
    if (move_uploaded_file($fileTmpName, $targetFile)) {
        echo "File uploaded successfully!";
    } else {
        die("Error: There was an issue uploading the file.");
    }
} else {
    die("Error: No file uploaded.");
}
?>
