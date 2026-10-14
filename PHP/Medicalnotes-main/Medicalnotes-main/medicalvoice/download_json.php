<?php
/**
 * JSON Download Handler for MedicalVoice
 * 
 * This script safely handles JSON file downloads by:
 * 1. Validating user authentication
 * 2. Checking file existence and permissions
 * 3. Serving files with proper headers
 * 4. Preventing directory traversal attacks
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized. Please log in.']);
    exit;
}

// Check if JSON file path exists in session
if (!isset($_SESSION['json_file'])) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No JSON file available for download.']);
    exit;
}

$filePath = $_SESSION['json_file'];

// Security: Prevent directory traversal attacks
$basePath = realpath(__DIR__ . '/storage/uploads/audio/json/');
$requestedPath = realpath($filePath);

// Ensure the requested file is within the allowed directory
if ($requestedPath === false || strpos($requestedPath, $basePath) !== 0) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid file path.']);
    error_log("Possible directory traversal attempt: " . $filePath);
    exit;
}

// Check if file exists
if (!file_exists($filePath)) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'File not found.']);
    error_log("JSON file not found: " . $filePath);
    exit;
}

// Check if file is readable
if (!is_readable($filePath)) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'File is not accessible.']);
    error_log("JSON file not readable: " . $filePath);
    exit;
}

// Get file info
$fileName = basename($filePath);
$fileSize = filesize($filePath);
$fileMTime = filemtime($filePath);

// Generate a safe filename for download
$safeFileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
if (empty($safeFileName)) {
    $safeFileName = 'medical_transcription_' . date('Y-m-d_His') . '.json';
}

// Log the download
error_log("User {$_SESSION['member_id']} downloading JSON: {$fileName}");

// Set headers for file download
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="' . $safeFileName . '"');
header('Content-Length: ' . $fileSize);
header('Cache-Control: no-cache, must-revalidate');
header('Expires: 0');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $fileMTime) . ' GMT');

// Additional headers for better compatibility
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

// Clear any output buffers to prevent corruption
if (ob_get_level()) {
    ob_end_clean();
}

// Output the file
readfile($filePath);

// Optional: Track download in database
try {
    require_once('database.php');
    
    // Log the download event
    $stmt = $conn->prepare("
        INSERT INTO processing_logs (member_id, module_type, action, status, message, ip_address) 
        VALUES (?, 'medicalvoice', 'download_json', 'success', ?, ?)
    ");
    
    $message = "Downloaded JSON file: " . $fileName;
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $stmt->bind_param("iss", $_SESSION['member_id'], $message, $ipAddress);
    $stmt->execute();
    $stmt->close();
} catch (Exception $e) {
    // Log error but don't interrupt download
    error_log("Failed to log download: " . $e->getMessage());
}

exit;
?>
