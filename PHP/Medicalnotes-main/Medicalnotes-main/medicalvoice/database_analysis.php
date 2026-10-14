<?php
/**
 * Database Analysis Script for MedicalVoice
 * Shows all tables, record counts, and relationships
 */

require_once 'database.php';

// Set header for proper display
header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedicalVoice Database Analysis</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #4a5568;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        h2 {
            color: #667eea;
            margin-top: 30px;
            border-left: 4px solid #667eea;
            padding-left: 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #f6f9fc 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #dee2e6;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9em;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
        }
        tr:hover {
            background-color: #f8f9fa;
        }
        .relationship {
            background: #e3f2fd;
            padding: 10px;
            margin: 10px 0;
            border-left: 4px solid #2196F3;
            border-radius: 4px;
        }
        .foreign-key {
            color: #2196F3;
            font-weight: bold;
        }
        .primary-key {
            color: #f44336;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
            margin: 0 3px;
        }
        .badge-pk {
            background: #ffebee;
            color: #c62828;
        }
        .badge-fk {
            background: #e3f2fd;
            color: #1565c0;
        }
        .badge-index {
            background: #f3e5f5;
            color: #6a1b9a;
        }
        .empty-table {
            color: #999;
            font-style: italic;
        }
        .has-records {
            color: #4caf50;
            font-weight: bold;
        }
        .sample-data {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
        .schema-diagram {
            background: #fafafa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 2px solid #e0e0e0;
        }
        pre {
            background: #2d3748;
            color: #48bb78;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🏥 MedicalVoice Database Analysis</h1>

    <?php
    // Get all tables in the database
    $allTablesQuery = "SHOW TABLES";
    $tablesResult = $conn->query($allTablesQuery);
    $allTables = [];
    while ($row = $tablesResult->fetch_array()) {
        $allTables[] = $row[0];
    }

    // Define MedicalVoice specific tables
    $medicalVoiceTables = [
        'patients',
        'audio_transcriptions',
        'patient_documents',
        'patient_reports',
        'patient_medical_history',
        'patient_visits',
        'patient_access_log',
        'medicalvoice_transcriptions',
        'transcription_documents',
        'transcriptions',
        'members',
        'credit_transactions',
        'processing_logs'
    ];

    // Filter to only MedicalVoice related tables
    $relevantTables = array_intersect($allTables, $medicalVoiceTables);

    // Get overview statistics
    $stats = [];
    foreach ($relevantTables as $table) {
        $countQuery = "SELECT COUNT(*) as count FROM `$table`";
        $countResult = $conn->query($countQuery);
        $count = $countResult->fetch_assoc()['count'];
        $stats[$table] = $count;
    }
    ?>

    <h2>📊 Database Overview</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo count($relevantTables); ?></div>
            <div class="stat-label">Total Tables</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo array_sum($stats); ?></div>
            <div class="stat-label">Total Records</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['patients'] ?? 0; ?></div>
            <div class="stat-label">Patients</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['audio_transcriptions'] ?? 0; ?></div>
            <div class="stat-label">Audio Transcriptions</div>
        </div>
    </div>

    <h2>📋 Table Details & Records</h2>

    <?php
    foreach ($relevantTables as $table) {
        echo "<h3>Table: <code>$table</code></h3>";

        // Get table structure
        $structureQuery = "DESCRIBE `$table`";
        $structureResult = $conn->query($structureQuery);

        echo "<table>";
        echo "<thead><tr>";
        echo "<th>Column</th>";
        echo "<th>Type</th>";
        echo "<th>Null</th>";
        echo "<th>Key</th>";
        echo "<th>Default</th>";
        echo "<th>Extra</th>";
        echo "</tr></thead>";
        echo "<tbody>";

        $columns = [];
        while ($col = $structureResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td><strong>{$col['Field']}</strong>";

            if ($col['Key'] == 'PRI') {
                echo " <span class='badge badge-pk'>PK</span>";
            } elseif ($col['Key'] == 'MUL') {
                echo " <span class='badge badge-fk'>FK</span>";
            } elseif ($col['Key'] == 'UNI') {
                echo " <span class='badge badge-index'>UNIQUE</span>";
            }

            echo "</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
            echo "<td>{$col['Extra']}</td>";
            echo "</tr>";
            $columns[] = $col['Field'];
        }
        echo "</tbody></table>";

        // Show record count and sample data
        $count = $stats[$table];
        echo "<p>Records: ";
        if ($count > 0) {
            echo "<span class='has-records'>$count records found</span>";

            // Show sample data for tables with records
            if ($count > 0) {
                $sampleQuery = "SELECT * FROM `$table` LIMIT 3";
                $sampleResult = $conn->query($sampleQuery);

                if ($sampleResult && $sampleResult->num_rows > 0) {
                    echo "<div class='sample-data'>";
                    echo "<strong>Sample Records (First 3):</strong><br>";
                    echo "<table style='margin-top: 10px;'>";
                    echo "<thead><tr>";
                    foreach ($columns as $col) {
                        echo "<th style='background: #546e7a; font-size: 0.85em;'>$col</th>";
                    }
                    echo "</tr></thead>";
                    echo "<tbody>";

                    while ($row = $sampleResult->fetch_assoc()) {
                        echo "<tr>";
                        foreach ($columns as $col) {
                            $value = $row[$col] ?? 'NULL';
                            // Truncate long values
                            if (strlen($value) > 50) {
                                $value = substr($value, 0, 47) . '...';
                            }
                            echo "<td style='font-size: 0.85em;'>" . htmlspecialchars($value) . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                    echo "</div>";
                }
            }
        } else {
            echo "<span class='empty-table'>No records</span>";
        }
        echo "</p>";
    }
    ?>

    <h2>🔗 Table Relationships</h2>
    <div class="schema-diagram">
        <?php
        $relationships = [
            ['audio_transcriptions', 'patient_id', 'patients', 'patient_id'],
            ['audio_transcriptions', 'member_id', 'members', 'member_id'],
            ['patient_documents', 'patient_id', 'patients', 'patient_id'],
            ['patient_documents', 'member_id', 'members', 'member_id'],
            ['patient_documents', 'transcription_id', 'audio_transcriptions', 'id'],
            ['patient_reports', 'patient_id', 'patients', 'patient_id'],
            ['patient_reports', 'member_id', 'members', 'member_id'],
            ['patient_medical_history', 'patient_id', 'patients', 'patient_id'],
            ['patient_medical_history', 'member_id', 'members', 'member_id'],
            ['patient_medical_history', 'source_transcription_id', 'audio_transcriptions', 'id'],
            ['patient_medical_history', 'source_document_id', 'patient_documents', 'document_id'],
            ['patient_visits', 'patient_id', 'patients', 'patient_id'],
            ['patient_visits', 'member_id', 'members', 'member_id'],
            ['patient_access_log', 'patient_id', 'patients', 'patient_id'],
            ['patient_access_log', 'member_id', 'members', 'member_id'],
            ['credit_transactions', 'member_id', 'members', 'member_id'],
            ['processing_logs', 'member_id', 'members', 'member_id']
        ];

        foreach ($relationships as $rel) {
            echo "<div class='relationship'>";
            echo "<strong>{$rel[0]}</strong>.<span class='foreign-key'>{$rel[1]}</span> → ";
            echo "<strong>{$rel[2]}</strong>.<span class='primary-key'>{$rel[3]}</span>";
            echo "</div>";
        }
        ?>
    </div>

    <h2>🏗️ Database Schema Diagram</h2>
    <pre>
┌─────────────────────┐
│     MEMBERS         │
├─────────────────────┤
│ PK: member_id       │
│ username            │
│ email               │
│ credits             │
└─────────────────────┘
         │
         │ 1:N
         ├─────────────────┬───────────────┬──────────────┬──────────────┐
         ↓                 ↓               ↓              ↓              ↓
┌─────────────────┐ ┌──────────────┐ ┌─────────────┐ ┌────────────┐ ┌──────────────┐
│   PATIENTS      │ │ AUDIO_TRANS  │ │ PATIENT_    │ │ PATIENT_   │ │ CREDIT_      │
├─────────────────┤ ├──────────────┤ │ DOCUMENTS   │ │ REPORTS    │ │ TRANSACTIONS │
│ PK: patient_id  │ │ PK: id       │ ├─────────────┤ ├────────────┤ ├──────────────┤
│ FK: member_id   │ │ FK: member_id│ │ PK: doc_id  │ │ PK: report │ │ PK: id       │
│ mr_number       │ │ FK: patient_ │ │ FK: patient │ │ FK: patient│ │ FK: member_id│
│ first_name      │ │     id       │ │     _id     │ │     _id    │ │ amount       │
│ last_name       │ │ mr_number    │ │ FK: member_ │ │ FK: member │ │ balance_after│
│ date_of_birth   │ │ audio_file   │ │     id      │ │     _id    │ └──────────────┘
│ gender          │ │ transcribed_ │ │ mr_number   │ │ mr_number  │
│ phone           │ │     text     │ │ document_   │ │ report_    │
│ email           │ │ medical_     │ │     type    │ │     type   │
│ blood_type      │ │     analysis │ │ file_path   │ │ report_    │
│ allergies       │ └──────────────┘ │ extracted_  │ │     content│
│ medical_        │         ↑        │     text    │ └────────────┘
│     conditions  │         │        └─────────────┘
└─────────────────┘         │                ↑
         │                  │                │
         │ 1:N              └────────────────┘
         ├────────────┬────────────┬─────────────┐
         ↓            ↓            ↓             ↓
┌──────────────┐ ┌────────────┐ ┌─────────────┐ ┌──────────────┐
│ PATIENT_     │ │ PATIENT_   │ │ PATIENT_    │ │ PATIENT_     │
│ VISITS       │ │ MEDICAL_   │ │ ACCESS_LOG  │ │ DOCUMENTS    │
├──────────────┤ │ HISTORY    │ ├─────────────┤ │ (linked)     │
│ PK: visit_id │ ├────────────┤ │ PK: log_id  │ └──────────────┘
│ FK: patient_ │ │ PK: history│ │ FK: patient │
│     id       │ │     _id    │ │     _id     │
│ FK: member_  │ │ FK: patient│ │ FK: member_ │
│     id       │ │     _id    │ │     id      │
│ mr_number    │ │ FK: member │ │ access_type │
│ visit_date   │ │     _id    │ │ timestamp   │
│ visit_type   │ │ mr_number  │ └─────────────┘
└──────────────┘ │ history_   │
                 │     type   │
                 └────────────┘

KEY RELATIONSHIPS:
═════════════════
→ All patient data linked via MR_NUMBER
→ Audio transcriptions linked to patients
→ Documents linked to patients and optionally to transcriptions
→ Reports generated from patient data
→ Complete audit trail via patient_access_log
    </pre>

    <h2>📈 Data Flow</h2>
    <div class="schema-diagram">
        <h4>1. Audio Upload Flow:</h4>
        <pre style="background: #f5f5f5; color: #333;">
User uploads audio → Creates audio_transcriptions record
                   → Links to patient via patient_id & mr_number
                   → Generates transcribed_text
                   → Stores medical_analysis JSON
        </pre>

        <h4>2. Document Upload Flow:</h4>
        <pre style="background: #f5f5f5; color: #333;">
User uploads document → Creates patient_documents record
                      → Links to patient via patient_id & mr_number
                      → Optionally links to transcription
                      → Extracts text content
                      → Categorizes document type
        </pre>

        <h4>3. Data Export Flow:</h4>
        <pre style="background: #f5f5f5; color: #333;">
External system requests by MR Number → Fetches patient record
                                      → Retrieves all audio_transcriptions
                                      → Retrieves all patient_documents
                                      → Retrieves all patient_reports
                                      → Returns complete dataset
        </pre>
    </div>

    <h2>✅ Integration Points</h2>
    <table>
        <thead>
            <tr>
                <th>Integration Point</th>
                <th>Method</th>
                <th>Key Field</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Audio Upload</td>
                <td>POST /upload.php</td>
                <td>patient_id</td>
                <td>✅ Active</td>
            </tr>
            <tr>
                <td>Document Upload</td>
                <td>POST /document_handler.php</td>
                <td>patient_id</td>
                <td>✅ Active</td>
            </tr>
            <tr>
                <td>Data Export API</td>
                <td>GET /api/patient_export_api.php</td>
                <td>mr_number</td>
                <td>✅ Active</td>
            </tr>
            <tr>
                <td>FHIR Export</td>
                <td>GET /api/patient_export_api.php?format=fhir</td>
                <td>mr_number</td>
                <td>✅ Active</td>
            </tr>
        </tbody>
    </table>

    <h2>🔍 Query Examples</h2>
    <pre>
-- Get all data for a patient by MR Number
SELECT p.*,
       COUNT(DISTINCT at.id) as transcription_count,
       COUNT(DISTINCT pd.document_id) as document_count,
       COUNT(DISTINCT pr.report_id) as report_count
FROM patients p
LEFT JOIN audio_transcriptions at ON p.patient_id = at.patient_id
LEFT JOIN patient_documents pd ON p.patient_id = pd.patient_id
LEFT JOIN patient_reports pr ON p.patient_id = pr.patient_id
WHERE p.mr_number = 'MRN-2024-0001'
GROUP BY p.patient_id;

-- Get recent patient activity
SELECT
    p.mr_number,
    p.first_name,
    p.last_name,
    MAX(at.created_at) as last_transcription,
    MAX(pd.upload_date) as last_document,
    MAX(pv.visit_date) as last_visit
FROM patients p
LEFT JOIN audio_transcriptions at ON p.patient_id = at.patient_id
LEFT JOIN patient_documents pd ON p.patient_id = pd.patient_id
LEFT JOIN patient_visits pv ON p.patient_id = pv.patient_id
GROUP BY p.patient_id
ORDER BY GREATEST(
    COALESCE(MAX(at.created_at), '1900-01-01'),
    COALESCE(MAX(pd.upload_date), '1900-01-01'),
    COALESCE(MAX(pv.visit_date), '1900-01-01')
) DESC
LIMIT 10;
    </pre>

</div>
</body>
</html>
<?php
$conn->close();
?>