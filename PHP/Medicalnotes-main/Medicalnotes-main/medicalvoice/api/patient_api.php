<?php
/**
 * Patient API Endpoints for MedicalVoice
 * Handles all AJAX requests for patient management
 */

session_start();

// Check authentication
if (!isset($_SESSION['member_id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Include required files
require_once '../database.php';
require_once '../includes/patient_functions.php';

// Set JSON header
header('Content-Type: application/json');

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

$memberId = $_SESSION['member_id'];
$response = [];

try {
    switch ($method) {
        case 'GET':
            switch ($action) {
                case 'search':
                    $searchTerm = $_GET['q'] ?? '';
                    $limit = intval($_GET['limit'] ?? 10);
                    $patients = searchPatients($conn, $memberId, $searchTerm, $limit);
                    $response = ['success' => true, 'patients' => $patients];
                    break;

                case 'recent':
                    $limit = intval($_GET['limit'] ?? 5);
                    $patients = getRecentPatients($conn, $memberId, $limit);
                    $response = ['success' => true, 'patients' => $patients];
                    break;

                case 'get':
                    $patientId = intval($_GET['patient_id'] ?? 0);
                    if ($patientId) {
                        $patient = getPatientById($conn, $patientId, $memberId);
                        if ($patient) {
                            // Calculate age
                            if (!empty($patient['date_of_birth'])) {
                                $patient['age'] = calculateAge($patient['date_of_birth']);
                            }
                            $response = ['success' => true, 'patient' => $patient];
                        } else {
                            $response = ['success' => false, 'error' => 'Patient not found'];
                        }
                    } else {
                        $response = ['success' => false, 'error' => 'Patient ID required'];
                    }
                    break;

                case 'history':
                    $patientId = intval($_GET['patient_id'] ?? 0);
                    if ($patientId) {
                        $transcriptions = getPatientTranscriptions($conn, $patientId, $memberId);
                        $response = ['success' => true, 'transcriptions' => $transcriptions];
                    } else {
                        $response = ['success' => false, 'error' => 'Patient ID required'];
                    }
                    break;

                case 'generate_mr':
                    $mrNumber = generateMRNumber($conn);
                    $response = ['success' => true, 'mr_number' => $mrNumber];
                    break;

                default:
                    $response = ['success' => false, 'error' => 'Invalid action'];
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            switch ($action) {
                case 'create':
                    // Validate required fields
                    $errors = validatePatientData($data);
                    if (!empty($errors)) {
                        $response = ['success' => false, 'errors' => $errors];
                    } else {
                        // Set defaults for optional fields
                        $patientData = [
                            'mr_number' => $data['mr_number'] ?? '',
                            'first_name' => $data['first_name'],
                            'last_name' => $data['last_name'],
                            'date_of_birth' => $data['date_of_birth'] ?? null,
                            'gender' => $data['gender'] ?? null,
                            'phone' => $data['phone'] ?? null,
                            'email' => $data['email'] ?? null,
                            'address' => $data['address'] ?? null,
                            'emergency_contact' => $data['emergency_contact'] ?? null,
                            'emergency_phone' => $data['emergency_phone'] ?? null,
                            'blood_type' => $data['blood_type'] ?? null,
                            'allergies' => $data['allergies'] ?? null,
                            'medical_conditions' => $data['medical_conditions'] ?? null
                        ];

                        $result = createPatient($conn, $patientData, $memberId);
                        if ($result['success']) {
                            // Get the created patient details
                            $patient = getPatientById($conn, $result['patient_id'], $memberId);
                            if (!empty($patient['date_of_birth'])) {
                                $patient['age'] = calculateAge($patient['date_of_birth']);
                            }
                            $response = [
                                'success' => true,
                                'patient' => $patient,
                                'message' => 'Patient created successfully'
                            ];
                        } else {
                            $response = $result;
                        }
                    }
                    break;

                case 'select':
                    $patientId = intval($data['patient_id'] ?? 0);
                    if ($patientId) {
                        $patient = getPatientById($conn, $patientId, $memberId);
                        if ($patient) {
                            // Store in session
                            $_SESSION['current_patient_id'] = $patient['patient_id'];
                            $_SESSION['current_patient'] = $patient;

                            // Calculate age
                            if (!empty($patient['date_of_birth'])) {
                                $patient['age'] = calculateAge($patient['date_of_birth']);
                            }

                            $response = [
                                'success' => true,
                                'patient' => $patient,
                                'message' => 'Patient selected successfully'
                            ];
                        } else {
                            $response = ['success' => false, 'error' => 'Patient not found'];
                        }
                    } else {
                        $response = ['success' => false, 'error' => 'Patient ID required'];
                    }
                    break;

                case 'clear':
                    // Clear patient from session
                    unset($_SESSION['current_patient_id']);
                    unset($_SESSION['current_patient']);
                    $response = ['success' => true, 'message' => 'Patient selection cleared'];
                    break;

                default:
                    $response = ['success' => false, 'error' => 'Invalid action'];
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $patientId = intval($_GET['patient_id'] ?? 0);

            if ($patientId) {
                // Validate data
                $errors = validatePatientData($data);
                if (!empty($errors)) {
                    $response = ['success' => false, 'errors' => $errors];
                } else {
                    $result = updatePatient($conn, $patientId, $data, $memberId);
                    if ($result['success']) {
                        // Get updated patient
                        $patient = getPatientById($conn, $patientId, $memberId);
                        if (!empty($patient['date_of_birth'])) {
                            $patient['age'] = calculateAge($patient['date_of_birth']);
                        }

                        // Update session if this is the current patient
                        if (isset($_SESSION['current_patient_id']) && $_SESSION['current_patient_id'] == $patientId) {
                            $_SESSION['current_patient'] = $patient;
                        }

                        $response = [
                            'success' => true,
                            'patient' => $patient,
                            'message' => 'Patient updated successfully'
                        ];
                    } else {
                        $response = $result;
                    }
                }
            } else {
                $response = ['success' => false, 'error' => 'Patient ID required'];
            }
            break;

        default:
            $response = ['success' => false, 'error' => 'Method not allowed'];
    }

} catch (Exception $e) {
    error_log("Patient API Error: " . $e->getMessage());
    $response = ['success' => false, 'error' => 'Internal server error'];
    http_response_code(500);
}

// Output response
echo json_encode($response);
?>