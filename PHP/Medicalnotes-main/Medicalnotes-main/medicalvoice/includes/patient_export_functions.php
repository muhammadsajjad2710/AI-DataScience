<?php
/**
 * Patient Data Export Functions
 * Provides comprehensive export capabilities for patient records
 * including all linked audio transcriptions, documents, and reports
 */

/**
 * Export complete patient record by MR Number
 * Returns all associated data for external system integration
 */
function exportPatientRecordByMRN($conn, $mrNumber) {
    $exportData = [
        'export_timestamp' => date('Y-m-d H:i:s'),
        'export_version' => '1.0',
        'mr_number' => $mrNumber
    ];

    try {
        // 1. Get patient basic information
        $stmt = $conn->prepare("
            SELECT patient_id, mr_number, first_name, last_name, date_of_birth,
                   gender, phone, email, address, blood_type, allergies,
                   medical_conditions, emergency_contact, emergency_phone,
                   created_at, updated_at
            FROM patients
            WHERE mr_number = ? AND is_active = 1
        ");
        $stmt->bind_param("s", $mrNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            return ['error' => 'Patient not found with MR Number: ' . $mrNumber];
        }

        $exportData['patient'] = $result->fetch_assoc();
        $patientId = $exportData['patient']['patient_id'];
        $stmt->close();

        // 2. Get all audio transcriptions
        $stmt = $conn->prepare("
            SELECT id, audio_file_path, transcribed_text, medical_analysis,
                   json_file_path, log_file_path, file_size, duration_seconds,
                   processing_status, created_at, updated_at
            FROM audio_transcriptions
            WHERE patient_id = ? OR mr_number = ?
            ORDER BY created_at DESC
        ");
        $stmt->bind_param("is", $patientId, $mrNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $exportData['audio_transcriptions'] = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // 3. Get all patient documents
        $stmt = $conn->prepare("
            SELECT document_id, transcription_id, document_type, original_filename,
                   stored_filename, file_path, file_type, file_size, extracted_text,
                   metadata, upload_date, processing_status
            FROM patient_documents
            WHERE patient_id = ? OR mr_number = ?
            ORDER BY upload_date DESC
        ");
        $stmt->bind_param("is", $patientId, $mrNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $exportData['documents'] = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // 4. Get all patient reports
        $stmt = $conn->prepare("
            SELECT report_id, report_type, report_title, report_content,
                   report_text, report_html, related_transcriptions,
                   related_documents, generation_date, export_format,
                   fhir_resource, is_finalized, finalized_date
            FROM patient_reports
            WHERE patient_id = ? OR mr_number = ?
            ORDER BY generation_date DESC
        ");
        $stmt->bind_param("is", $patientId, $mrNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $exportData['reports'] = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // 5. Get medical history
        $stmt = $conn->prepare("
            SELECT history_id, history_type, description, icd_code,
                   snomed_code, start_date, end_date, severity,
                   status, notes, source_transcription_id,
                   source_document_id, created_at, updated_at
            FROM patient_medical_history
            WHERE patient_id = ? OR mr_number = ?
            ORDER BY created_at DESC
        ");
        $stmt->bind_param("is", $patientId, $mrNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $exportData['medical_history'] = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // 6. Get patient visits
        $stmt = $conn->prepare("
            SELECT visit_id, visit_date, visit_type, chief_complaint,
                   diagnosis_summary, treatment_plan, provider_name,
                   provider_id, facility_name, transcription_ids,
                   document_ids, report_ids, visit_notes,
                   follow_up_required, follow_up_date
            FROM patient_visits
            WHERE patient_id = ? OR mr_number = ?
            ORDER BY visit_date DESC
        ");
        $stmt->bind_param("is", $patientId, $mrNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $exportData['visits'] = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // 7. Calculate summary statistics
        $exportData['summary'] = [
            'total_transcriptions' => count($exportData['audio_transcriptions']),
            'total_documents' => count($exportData['documents']),
            'total_reports' => count($exportData['reports']),
            'total_visits' => count($exportData['visits']),
            'medical_history_entries' => count($exportData['medical_history'])
        ];

        return $exportData;

    } catch (Exception $e) {
        error_log("Patient export error: " . $e->getMessage());
        return ['error' => 'Export failed: ' . $e->getMessage()];
    }
}

/**
 * Export patient data as FHIR Bundle
 */
function exportPatientAsFHIR($conn, $mrNumber) {
    $patientData = exportPatientRecordByMRN($conn, $mrNumber);

    if (isset($patientData['error'])) {
        return $patientData;
    }

    $fhirBundle = [
        'resourceType' => 'Bundle',
        'type' => 'collection',
        'timestamp' => date('c'),
        'entry' => []
    ];

    // Create FHIR Patient resource
    $patientResource = [
        'resourceType' => 'Patient',
        'id' => $patientData['patient']['patient_id'],
        'identifier' => [[
            'system' => 'http://hospital.local/mrn',
            'value' => $patientData['patient']['mr_number']
        ]],
        'name' => [[
            'family' => $patientData['patient']['last_name'],
            'given' => [$patientData['patient']['first_name']]
        ]],
        'gender' => strtolower($patientData['patient']['gender']),
        'birthDate' => $patientData['patient']['date_of_birth'],
        'telecom' => []
    ];

    if ($patientData['patient']['phone']) {
        $patientResource['telecom'][] = [
            'system' => 'phone',
            'value' => $patientData['patient']['phone']
        ];
    }

    if ($patientData['patient']['email']) {
        $patientResource['telecom'][] = [
            'system' => 'email',
            'value' => $patientData['patient']['email']
        ];
    }

    $fhirBundle['entry'][] = ['resource' => $patientResource];

    // Add DocumentReference resources for transcriptions
    foreach ($patientData['audio_transcriptions'] as $transcription) {
        $docRef = [
            'resourceType' => 'DocumentReference',
            'id' => 'transcription-' . $transcription['id'],
            'status' => 'current',
            'type' => [
                'coding' => [[
                    'system' => 'http://loinc.org',
                    'code' => '34133-9',
                    'display' => 'Summary of episode note'
                ]]
            ],
            'subject' => [
                'reference' => 'Patient/' . $patientData['patient']['patient_id']
            ],
            'date' => $transcription['created_at'],
            'content' => [[
                'attachment' => [
                    'contentType' => 'text/plain',
                    'data' => base64_encode($transcription['transcribed_text'] ?? '')
                ]
            ]]
        ];
        $fhirBundle['entry'][] = ['resource' => $docRef];
    }

    return $fhirBundle;
}

/**
 * Export patient data as CSV
 */
function exportPatientAsCSV($conn, $mrNumber) {
    $patientData = exportPatientRecordByMRN($conn, $mrNumber);

    if (isset($patientData['error'])) {
        return $patientData;
    }

    $csv = [];

    // Patient header
    $csv[] = ['PATIENT INFORMATION'];
    $csv[] = ['Field', 'Value'];
    foreach ($patientData['patient'] as $key => $value) {
        if ($key !== 'patient_id') {
            $csv[] = [ucwords(str_replace('_', ' ', $key)), $value];
        }
    }

    $csv[] = [];
    $csv[] = ['AUDIO TRANSCRIPTIONS'];
    $csv[] = ['Date', 'Status', 'File Path', 'Summary'];
    foreach ($patientData['audio_transcriptions'] as $trans) {
        $csv[] = [
            $trans['created_at'],
            $trans['processing_status'],
            basename($trans['audio_file_path'] ?? ''),
            substr($trans['transcribed_text'] ?? '', 0, 100) . '...'
        ];
    }

    return $csv;
}

/**
 * Generate comprehensive patient report
 */
function generatePatientReport($conn, $mrNumber, $reportType = 'comprehensive_report') {
    $patientData = exportPatientRecordByMRN($conn, $mrNumber);

    if (isset($patientData['error'])) {
        return $patientData;
    }

    $patient = $patientData['patient'];
    $reportContent = [
        'patient_info' => $patient,
        'transcription_count' => $patientData['summary']['total_transcriptions'],
        'document_count' => $patientData['summary']['total_documents'],
        'visit_count' => $patientData['summary']['total_visits'],
        'recent_activities' => []
    ];

    // Get recent activities
    foreach ($patientData['audio_transcriptions'] as $trans) {
        if (count($reportContent['recent_activities']) < 5) {
            $reportContent['recent_activities'][] = [
                'type' => 'transcription',
                'date' => $trans['created_at'],
                'description' => 'Audio transcription processed'
            ];
        }
    }

    // Generate report text
    $reportText = "PATIENT MEDICAL REPORT\n";
    $reportText .= "========================\n\n";
    $reportText .= "Patient Name: {$patient['first_name']} {$patient['last_name']}\n";
    $reportText .= "MR Number: {$patient['mr_number']}\n";
    $reportText .= "Date of Birth: {$patient['date_of_birth']}\n";
    $reportText .= "Gender: {$patient['gender']}\n\n";

    $reportText .= "SUMMARY\n";
    $reportText .= "-------\n";
    $reportText .= "Total Transcriptions: {$patientData['summary']['total_transcriptions']}\n";
    $reportText .= "Total Documents: {$patientData['summary']['total_documents']}\n";
    $reportText .= "Total Visits: {$patientData['summary']['total_visits']}\n\n";

    // Save report to database
    $stmt = $conn->prepare("
        INSERT INTO patient_reports
        (patient_id, mr_number, member_id, report_type, report_title,
         report_content, report_text, export_format)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'json')
    ");

    $reportTitle = "Comprehensive Medical Report - " . date('Y-m-d');
    $reportContentJson = json_encode($reportContent);

    $stmt->bind_param("isissss",
        $patient['patient_id'],
        $mrNumber,
        $_SESSION['member_id'] ?? 1,
        $reportType,
        $reportTitle,
        $reportContentJson,
        $reportText
    );

    if ($stmt->execute()) {
        $reportId = $conn->insert_id;
        $stmt->close();

        return [
            'success' => true,
            'report_id' => $reportId,
            'report_text' => $reportText,
            'report_content' => $reportContent
        ];
    }

    return ['error' => 'Failed to generate report'];
}

/**
 * Link existing transcription to patient
 */
function linkTranscriptionToPatient($conn, $transcriptionId, $patientId) {
    // Get patient MR Number
    $stmt = $conn->prepare("SELECT mr_number FROM patients WHERE patient_id = ?");
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        return ['error' => 'Patient not found'];
    }

    $patient = $result->fetch_assoc();
    $mrNumber = $patient['mr_number'];
    $stmt->close();

    // Update audio_transcriptions table
    $stmt = $conn->prepare("
        UPDATE audio_transcriptions
        SET patient_id = ?, mr_number = ?
        WHERE id = ?
    ");
    $stmt->bind_param("isi", $patientId, $mrNumber, $transcriptionId);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Transcription linked to patient'];
    }

    return ['error' => 'Failed to link transcription'];
}

/**
 * Get patient data for API response
 */
function getPatientDataForAPI($conn, $mrNumber) {
    $data = exportPatientRecordByMRN($conn, $mrNumber);

    if (isset($data['error'])) {
        return $data;
    }

    // Format for API response
    return [
        'status' => 'success',
        'data' => [
            'patient' => $data['patient'],
            'summary' => $data['summary'],
            'recent_transcriptions' => array_slice($data['audio_transcriptions'], 0, 5),
            'recent_documents' => array_slice($data['documents'], 0, 5),
            'recent_visits' => array_slice($data['visits'], 0, 5)
        ],
        'timestamp' => $data['export_timestamp']
    ];
}
?>