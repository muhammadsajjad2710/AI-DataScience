<?php
/**
 * Patient Management Functions for MedicalVoice
 * Core functions for patient CRUD operations and utilities
 */

// Generate unique MR Number
function generateMRNumber($conn) {
    $prefix = "MRN";
    $year = date('Y');

    do {
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $mrNumber = "$prefix-$year-$random";

        // Check if MR number already exists
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM patients WHERE mr_number = ?");
        $stmt->bind_param("s", $mrNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $exists = $row['count'] > 0;
        $stmt->close();
    } while ($exists);

    return $mrNumber;
}

// Create new patient
function createPatient($conn, $data, $memberId) {
    try {
        // Generate MR Number if not provided
        if (empty($data['mr_number'])) {
            $data['mr_number'] = generateMRNumber($conn);
        }

        $sql = "INSERT INTO patients (
            mr_number, member_id, first_name, last_name, date_of_birth,
            gender, phone, email, address, emergency_contact,
            emergency_phone, blood_type, allergies, medical_conditions
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sissssssssssss",
            $data['mr_number'],
            $memberId,
            $data['first_name'],
            $data['last_name'],
            $data['date_of_birth'],
            $data['gender'],
            $data['phone'],
            $data['email'],
            $data['address'],
            $data['emergency_contact'],
            $data['emergency_phone'],
            $data['blood_type'],
            $data['allergies'],
            $data['medical_conditions']
        );

        if ($stmt->execute()) {
            $patientId = $conn->insert_id;
            $stmt->close();

            // Log patient creation
            logPatientAccess($conn, $patientId, $memberId, 'create');

            return [
                'success' => true,
                'patient_id' => $patientId,
                'mr_number' => $data['mr_number']
            ];
        }

        $stmt->close();
        return ['success' => false, 'error' => 'Failed to create patient'];

    } catch (Exception $e) {
        error_log("Error creating patient: " . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Get patient by ID
function getPatientById($conn, $patientId, $memberId) {
    $sql = "SELECT * FROM patients WHERE patient_id = ? AND member_id = ? AND is_active = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $patientId, $memberId);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
    $stmt->close();

    if ($patient) {
        // Log patient access
        logPatientAccess($conn, $patientId, $memberId, 'view');
    }

    return $patient;
}

// Get patient by MR Number
function getPatientByMRNumber($conn, $mrNumber, $memberId) {
    $sql = "SELECT * FROM patients WHERE mr_number = ? AND member_id = ? AND is_active = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $mrNumber, $memberId);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
    $stmt->close();

    if ($patient) {
        // Log patient access
        logPatientAccess($conn, $patient['patient_id'], $memberId, 'view');
    }

    return $patient;
}

// Search patients
function searchPatients($conn, $memberId, $searchTerm = '', $limit = 10) {
    $searchTerm = "%$searchTerm%";

    $sql = "SELECT patient_id, mr_number, first_name, last_name, date_of_birth, phone
            FROM patients
            WHERE member_id = ?
            AND is_active = 1
            AND (mr_number LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR CONCAT(first_name, ' ', last_name) LIKE ?)
            ORDER BY updated_at DESC
            LIMIT ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssi", $memberId, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $patients = [];
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }

    $stmt->close();
    return $patients;
}

// Get recent patients
function getRecentPatients($conn, $memberId, $limit = 5) {
    $sql = "SELECT DISTINCT p.patient_id, p.mr_number, p.first_name, p.last_name, p.date_of_birth, p.phone,
            MAX(t.created_at) as last_transcription
            FROM patients p
            LEFT JOIN audio_transcriptions t ON p.patient_id = t.patient_id
            WHERE p.member_id = ? AND p.is_active = 1
            GROUP BY p.patient_id
            ORDER BY last_transcription DESC, p.updated_at DESC
            LIMIT ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $memberId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $patients = [];
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }

    $stmt->close();
    return $patients;
}

// Update patient
function updatePatient($conn, $patientId, $data, $memberId) {
    try {
        // Verify ownership
        $sql = "SELECT patient_id FROM patients WHERE patient_id = ? AND member_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $patientId, $memberId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $stmt->close();
            return ['success' => false, 'error' => 'Patient not found or access denied'];
        }
        $stmt->close();

        // Update patient
        $sql = "UPDATE patients SET
                first_name = ?, last_name = ?, date_of_birth = ?,
                gender = ?, phone = ?, email = ?, address = ?,
                emergency_contact = ?, emergency_phone = ?,
                blood_type = ?, allergies = ?, medical_conditions = ?
                WHERE patient_id = ? AND member_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssssssii",
            $data['first_name'],
            $data['last_name'],
            $data['date_of_birth'],
            $data['gender'],
            $data['phone'],
            $data['email'],
            $data['address'],
            $data['emergency_contact'],
            $data['emergency_phone'],
            $data['blood_type'],
            $data['allergies'],
            $data['medical_conditions'],
            $patientId,
            $memberId
        );

        if ($stmt->execute()) {
            $stmt->close();

            // Log patient update
            logPatientAccess($conn, $patientId, $memberId, 'update');

            return ['success' => true];
        }

        $stmt->close();
        return ['success' => false, 'error' => 'Failed to update patient'];

    } catch (Exception $e) {
        error_log("Error updating patient: " . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Get patient transcription history
function getPatientTranscriptions($conn, $patientId, $memberId) {
    $sql = "SELECT t.*, p.first_name, p.last_name, p.mr_number
            FROM transcriptions t
            JOIN patients p ON t.patient_id = p.patient_id
            WHERE t.patient_id = ? AND t.member_id = ?
            ORDER BY t.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $patientId, $memberId);
    $stmt->execute();
    $result = $stmt->get_result();

    $transcriptions = [];
    while ($row = $result->fetch_assoc()) {
        $transcriptions[] = $row;
    }

    $stmt->close();
    return $transcriptions;
}

// Log patient access for audit trail
function logPatientAccess($conn, $patientId, $memberId, $accessType) {
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $sql = "INSERT INTO patient_access_log (patient_id, member_id, access_type, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $patientId, $memberId, $accessType, $ipAddress, $userAgent);
    $stmt->execute();
    $stmt->close();
}

// Validate patient data
function validatePatientData($data) {
    $errors = [];

    // Required fields
    if (empty($data['first_name'])) {
        $errors[] = "First name is required";
    }

    if (empty($data['last_name'])) {
        $errors[] = "Last name is required";
    }

    // Email validation
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Date validation
    if (!empty($data['date_of_birth'])) {
        $date = DateTime::createFromFormat('Y-m-d', $data['date_of_birth']);
        if (!$date || $date->format('Y-m-d') !== $data['date_of_birth']) {
            $errors[] = "Invalid date of birth format";
        }
    }

    // Phone validation (basic)
    if (!empty($data['phone']) && !preg_match('/^[\d\s\-\+\(\)]+$/', $data['phone'])) {
        $errors[] = "Invalid phone number format";
    }

    return $errors;
}

// Format patient display name
function formatPatientName($patient) {
    return $patient['first_name'] . ' ' . $patient['last_name'];
}

// Calculate patient age
function calculateAge($dateOfBirth) {
    if (empty($dateOfBirth)) {
        return null;
    }

    $dob = new DateTime($dateOfBirth);
    $now = new DateTime();
    $age = $now->diff($dob);

    return $age->y;
}
?>