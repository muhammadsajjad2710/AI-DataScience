<?php
/**
 * Download JSON for MedicalVision Module
 * Allows users to download their analysis results in JSON format
 */

// Start session early
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include('database.php');

// Check if document ID is provided
if (!isset($_GET['document_id']) || !is_numeric($_GET['document_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid document ID']);
    exit;
}

$documentId = (int)$_GET['document_id'];
$userId = $_SESSION['member_id'];

try {
    // Get document data from database
    $stmt = $conn->prepare("SELECT id, member_id, image_path, extracted_text, fhir_json, created_at, updated_at FROM fhir_documents WHERE id = ? AND member_id = ?");
    $stmt->bind_param("ii", $documentId, $userId);
    $stmt->execute();
    $stmt->bind_result($id, $member_id, $image_path, $extracted_text, $fhir_json, $created_at, $updated_at);
    $stmt->fetch();
    $stmt->close();
    
    if (!$id) {
        http_response_code(404);
        echo json_encode(['error' => 'Document not found or access denied']);
        exit;
    }
    
    // Parse FHIR JSON if it exists
    $ai_analysis = null;
    if ($fhir_json) {
        $ai_analysis = json_decode($fhir_json, true);
    }
    
    // Prepare the complete JSON data
    $downloadData = [
        'document_info' => [
            'id' => $id,
            'member_id' => $member_id,
            'image_path' => $image_path,
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ],
        'ocr_results' => [
            'extracted_text' => $extracted_text,
            'text_length' => strlen($extracted_text),
            'processing_status' => 'completed'
        ],
        'ai_analysis' => $ai_analysis,
        'system_info' => [
            'module' => 'MedicalVision',
            'version' => '1.0',
            'processing_date' => date('Y-m-d H:i:s'),
            'export_format' => 'JSON',
            'compliance' => [
                'fhir' => true,
                'hipaa' => true
            ]
        ]
    ];
    
    // Generate JSON content first
    $jsonContent = json_encode($downloadData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if ($jsonContent === false) {
        throw new Exception('Failed to encode JSON data');
    }
    
    // Set headers for JSON download
    $filename = "medicalvision_analysis_" . $documentId . "_" . date('Y-m-d_H-i-s') . ".json";
    
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set headers
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($jsonContent));
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    header('Pragma: no-cache');
    
    // Output the JSON data
    echo $jsonContent;
    
} catch (Exception $e) {
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
