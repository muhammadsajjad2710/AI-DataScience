<?php
/**
 * Patient Export API Endpoint
 * Provides RESTful API access to patient data for external systems
 *
 * Usage:
 * GET /medicalvoice/api/patient_export_api.php?mr_number=MRN-2024-0001&format=json
 * GET /medicalvoice/api/patient_export_api.php?mr_number=MRN-2024-0001&format=fhir
 * GET /medicalvoice/api/patient_export_api.php?mr_number=MRN-2024-0001&format=csv
 */

session_start();

// CORS headers (configure as needed for your external systems)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check authentication - you may want to implement API key authentication here
if (!isset($_SESSION['member_id']) && !isset($_REQUEST['api_key'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Authentication required'
    ]);
    exit;
}

// Include necessary files
require_once dirname(__DIR__) . '/database.php';
require_once dirname(__DIR__) . '/includes/patient_export_functions.php';

// Get request parameters
$mrNumber = $_REQUEST['mr_number'] ?? null;
$format = $_REQUEST['format'] ?? 'json';
$apiKey = $_REQUEST['api_key'] ?? null;

// Validate API key if provided (implement your own API key validation)
if ($apiKey) {
    // Example API key validation (replace with your actual implementation)
    $validApiKeys = [
        'test_api_key_123' => true,
        // Add your valid API keys here
    ];

    if (!isset($validApiKeys[$apiKey])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid API key'
        ]);
        exit;
    }
}

// Validate MR Number
if (!$mrNumber) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'MR Number is required'
    ]);
    exit;
}

// Process based on format
try {
    switch (strtolower($format)) {
        case 'json':
            header('Content-Type: application/json');
            $data = exportPatientRecordByMRN($conn, $mrNumber);

            if (isset($data['error'])) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => $data['error']
                ]);
            } else {
                echo json_encode([
                    'status' => 'success',
                    'data' => $data
                ]);
            }
            break;

        case 'fhir':
            header('Content-Type: application/json+fhir');
            $fhirData = exportPatientAsFHIR($conn, $mrNumber);

            if (isset($fhirData['error'])) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => $fhirData['error']
                ]);
            } else {
                echo json_encode($fhirData);
            }
            break;

        case 'csv':
            $csvData = exportPatientAsCSV($conn, $mrNumber);

            if (isset($csvData['error'])) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => $csvData['error']
                ]);
            } else {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="patient_' . $mrNumber . '_' . date('Y-m-d') . '.csv"');

                $output = fopen('php://output', 'w');
                foreach ($csvData as $row) {
                    fputcsv($output, $row);
                }
                fclose($output);
            }
            break;

        case 'summary':
            header('Content-Type: application/json');
            $data = getPatientDataForAPI($conn, $mrNumber);

            if (isset($data['error'])) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => $data['error']
                ]);
            } else {
                echo json_encode($data);
            }
            break;

        default:
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid format. Supported formats: json, fhir, csv, summary'
            ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
    error_log("Patient Export API Error: " . $e->getMessage());
}
?>