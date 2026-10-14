<?php
/**
 * Document Download Handler for MedicalVoice
 * Allows users to view/download their uploaded documents
 */

session_start();

// Check authentication
if (!isset($_SESSION['member_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit('Unauthorized access');
}

// Validate document ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('Invalid document ID');
}

$documentId = intval($_GET['id']);
$memberId = $_SESSION['member_id'];

// Include database connection
require_once dirname(__FILE__) . '/../config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    header('HTTP/1.1 500 Internal Server Error');
    exit('Database connection failed');
}

// Fetch document details
$stmt = $conn->prepare("SELECT original_filename, stored_filename, file_path, file_type
                        FROM transcription_documents
                        WHERE id = ? AND member_id = ?");
$stmt->bind_param("ii", $documentId, $memberId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header('HTTP/1.1 404 Not Found');
    exit('Document not found or access denied');
}

$document = $result->fetch_assoc();
$stmt->close();
$conn->close();

$filePath = $document['file_path'];

// Validate file exists
if (!file_exists($filePath)) {
    header('HTTP/1.1 404 Not Found');
    exit('Document file not found');
}

// Determine content type
$contentTypes = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'txt' => 'text/plain'
];

$fileExt = strtolower($document['file_type']);
$contentType = isset($contentTypes[$fileExt]) ? $contentTypes[$fileExt] : 'application/octet-stream';

// Check if it's a view or download request
$action = isset($_GET['action']) && $_GET['action'] === 'view' ? 'inline' : 'attachment';

// Set headers for file delivery
header('Content-Type: ' . $contentType);
header('Content-Disposition: ' . $action . '; filename="' . $document['original_filename'] . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// For images and PDFs, allow inline viewing
if ($action === 'inline' && in_array($fileExt, ['pdf', 'jpg', 'jpeg', 'png'])) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
}

// Output file
readfile($filePath);
exit;
?>