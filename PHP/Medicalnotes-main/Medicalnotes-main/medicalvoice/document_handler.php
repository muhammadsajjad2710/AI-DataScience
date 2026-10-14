<?php
/**
 * Document Upload Handler for MedicalVoice v1
 * Processes document uploads and extracts text
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display, just log
ini_set('log_errors', 1);

session_start();

// Log request for debugging
error_log("Document handler called. Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));
error_log("FILES data: " . print_r($_FILES, true));

// Check authentication
if (!isset($_SESSION['member_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized', 'message' => 'Please login first']);
    exit;
}

// Include configuration first
require_once dirname(__FILE__) . '/../config.php';

// Include document module
require_once dirname(__FILE__) . '/document_module.php';

// Establish database connection using mysqli (matching the rest of the application)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}
$conn->set_charset("utf8mb4");

$response = ['success' => false];

// Set proper headers
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    try {
        $documentModule = new DocumentModule();

    switch ($_POST['action']) {
        case 'upload_documents':
            if (isset($_FILES['documents'])) {
                $transcriptionId = $_POST['transcription_id'] ?? null;
                $patientId = $_POST['patient_id'] ?? null;
                $memberId = $_SESSION['member_id'];

                // Get patient MR Number if patient_id provided
                $mrNumber = null;
                if ($patientId) {
                    $mrStmt = $conn->prepare("SELECT mr_number FROM patients WHERE patient_id = ?");
                    $mrStmt->bind_param("i", $patientId);
                    $mrStmt->execute();
                    $mrResult = $mrStmt->get_result();
                    if ($mrResult->num_rows > 0) {
                        $mrRow = $mrResult->fetch_assoc();
                        $mrNumber = $mrRow['mr_number'];
                    }
                    $mrStmt->close();
                }

                // Process uploaded files
                $result = $documentModule->processUploadedFiles($_FILES, $transcriptionId);

                if ($result['success']) {
                    $documentIds = [];

                    // Save to database and extract text
                    foreach ($result['files'] as $file) {
                        // Extract text from document
                        $extractedText = $documentModule->extractTextFromDocument($file['path'], $file['type']);

                        // Determine document type based on file name/content
                        $documentType = 'other'; // Default
                        $fileName = strtolower($file['original_name']);
                        if (strpos($fileName, 'lab') !== false) $documentType = 'lab_report';
                        elseif (strpos($fileName, 'prescription') !== false || strpos($fileName, 'rx') !== false) $documentType = 'prescription';
                        elseif (strpos($fileName, 'imaging') !== false || strpos($fileName, 'xray') !== false || strpos($fileName, 'ct') !== false) $documentType = 'imaging';
                        elseif (strpos($fileName, 'discharge') !== false) $documentType = 'discharge_summary';
                        elseif (strpos($fileName, 'consultation') !== false) $documentType = 'consultation';
                        elseif (strpos($fileName, 'note') !== false) $documentType = 'clinical_note';

                        // ALWAYS use transcription_documents table (now with patient_id support)
                        $stmt = $conn->prepare("INSERT INTO transcription_documents
                            (transcription_id, member_id, patient_id, mr_number, document_type, original_filename, stored_filename, file_path, file_type, file_size, extracted_text)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                        $stmt->bind_param("iiissssssds",
                            $transcriptionId,
                            $memberId,
                            $patientId,      // Will be NULL if no patient selected
                            $mrNumber,       // Will be NULL if no patient selected
                            $documentType,
                            $file['original_name'],
                            $file['stored_name'],
                            $file['path'],
                            $file['type'],
                            $file['size'],
                            $extractedText
                        );

                        if ($stmt->execute()) {
                            $documentIds[] = $conn->insert_id;

                            // Update transcription record
                            if ($transcriptionId) {
                                $updateStmt = $conn->prepare("UPDATE medicalvoice_transcriptions SET has_documents = TRUE WHERE transcription_id = ?");
                                $updateStmt->bind_param("i", $transcriptionId);
                                $updateStmt->execute();
                                $updateStmt->close();
                            }
                        }
                        $stmt->close();
                    }

                    $response = [
                        'success' => true,
                        'message' => count($result['files']) . ' document(s) uploaded successfully',
                        'document_ids' => $documentIds,
                        'errors' => $result['errors']
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'No files uploaded successfully',
                        'errors' => $result['errors']
                    ];
                }
            } else {
                $response = ['success' => false, 'message' => 'No files provided'];
            }
            break;

        case 'get_document_texts':
            // Get extracted texts for combined analysis
            $transcriptionId = $_POST['transcription_id'] ?? null;

            if ($transcriptionId) {
                $stmt = $conn->prepare("SELECT extracted_text FROM transcription_documents WHERE transcription_id = ? AND member_id = ?");
                $stmt->bind_param("ii", $transcriptionId, $_SESSION['member_id']);
                $stmt->execute();
                $result = $stmt->get_result();

                $texts = [];
                while ($row = $result->fetch_assoc()) {
                    if ($row['extracted_text']) {
                        $texts[] = $row['extracted_text'];
                    }
                }
                $stmt->close();

                $response = [
                    'success' => true,
                    'texts' => $texts
                ];
            } else {
                $response = ['success' => false, 'message' => 'No transcription ID provided'];
            }
            break;
    }
    } catch (Exception $e) {
        error_log("Document handler error: " . $e->getMessage());
        $response = [
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ];
    }
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid request method or missing action'
    ];
}

echo json_encode($response);
?>