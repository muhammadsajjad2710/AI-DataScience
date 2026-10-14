<?php
// Database Structure Analysis Script
include('database.php');

// Get all MedicalVoice related tables and their structures
$tables = [
    'members',
    'patients',
    'audio_transcriptions',
    'medicalvoice_transcriptions',
    'patient_documents',
    'patient_reports',
    'patient_medical_history',
    'patient_visits',
    'patient_access_log',
    'credit_transactions',
    'processing_logs',
    'transcription_documents',
    'medicalvoice_docs',
    'medicalvoice_doc_associations',
    'medicalvoice_doc_tags',
    'fhir_audio'
];

$database_structure = [];

foreach ($tables as $table) {
    // Check if table exists
    $check_query = "SHOW TABLES LIKE '$table'";
    $check_result = $conn->query($check_query);

    if ($check_result && $check_result->num_rows > 0) {
        // Get table structure
        $structure_query = "DESCRIBE $table";
        $structure_result = $conn->query($structure_query);

        $columns = [];
        if ($structure_result) {
            while ($row = $structure_result->fetch_assoc()) {
                $columns[] = [
                    'field' => $row['Field'],
                    'type' => $row['Type'],
                    'null' => $row['Null'],
                    'key' => $row['Key'],
                    'default' => $row['Default'],
                    'extra' => $row['Extra']
                ];
            }
        }

        // Get sample data
        $sample_query = "SELECT * FROM $table LIMIT 3";
        $sample_result = $conn->query($sample_query);

        $sample_data = [];
        if ($sample_result) {
            while ($row = $sample_result->fetch_assoc()) {
                $sample_data[] = $row;
            }
        }

        // Get row count
        $count_query = "SELECT COUNT(*) as count FROM $table";
        $count_result = $conn->query($count_query);
        $row_count = 0;
        if ($count_result) {
            $count_row = $count_result->fetch_assoc();
            $row_count = $count_row['count'];
        }

        $database_structure[$table] = [
            'columns' => $columns,
            'sample_data' => $sample_data,
            'row_count' => $row_count
        ];
    }
}

// Get foreign key relationships
$relationships = [];
$fk_query = "
    SELECT
        TABLE_NAME,
        COLUMN_NAME,
        CONSTRAINT_NAME,
        REFERENCED_TABLE_NAME,
        REFERENCED_COLUMN_NAME
    FROM
        INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE
        REFERENCED_TABLE_SCHEMA = 'db2'
        AND TABLE_SCHEMA = 'db2'
    ORDER BY
        TABLE_NAME, COLUMN_NAME
";

$fk_result = $conn->query($fk_query);
if ($fk_result) {
    while ($row = $fk_result->fetch_assoc()) {
        $relationships[] = $row;
    }
}

// Output as JSON for easy reading
header('Content-Type: application/json');
echo json_encode([
    'database' => 'db2',
    'tables' => $database_structure,
    'relationships' => $relationships,
    'timestamp' => date('Y-m-d H:i:s')
], JSON_PRETTY_PRINT);
?>