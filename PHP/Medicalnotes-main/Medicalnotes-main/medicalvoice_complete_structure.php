<?php
// Database connection
$host = 'localhost';
$dbname = 'db2';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>MedicalVoice Complete Database Structure & Records</title>
        <style>
            body { font-family: 'Consolas', 'Courier New', monospace; margin: 20px; background: #1a1a1a; color: #e0e0e0; }
            h1 { color: #4CAF50; text-align: center; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
            h2 { color: #2196F3; margin-top: 30px; border-bottom: 1px solid #2196F3; }
            h3 { color: #FFC107; }
            .structure { background: #2d2d2d; padding: 15px; border-radius: 5px; margin: 10px 0; }
            .records { background: #1e1e1e; padding: 10px; border-radius: 5px; margin: 10px 0; overflow-x: auto; }
            table { width: 100%; border-collapse: collapse; margin: 10px 0; }
            th { background: #3a3a3a; color: #4CAF50; padding: 8px; text-align: left; border: 1px solid #555; }
            td { padding: 6px; border: 1px solid #555; color: #e0e0e0; }
            .relationship { background: #2a2a3e; padding: 15px; border-left: 4px solid #9C27B0; margin: 10px 0; }
            .fk { color: #FF9800; }
            .pk { color: #4CAF50; }
            .null { color: #9E9E9E; font-style: italic; }
            .count { background: #4CAF50; color: white; padding: 2px 6px; border-radius: 3px; }
            pre { color: #e0e0e0; }
        </style>
    </head>
    <body>";

    echo "<h1>🔍 MEDICALVOICE COMPLETE DATABASE STRUCTURE & ALL RECORDS</h1>";
    echo "<p style='text-align: center; color: #888;'>Generated: " . date('Y-m-d H:i:s') . " | READ-ONLY VIEW</p>";

    // Define all MedicalVoice related tables
    $tables = [
        'medicalvoice_transcriptions',
        'audio_transcriptions',
        'patients',
        'patient_documents',
        'patient_reports',
        'patient_medical_history',
        'patient_visits',
        'patient_access_log',
        'transcription_documents',
        'members',
        'credit_transactions',
        'processing_logs'
    ];

    foreach ($tables as $table) {
        echo "<h2>📊 TABLE: $table</h2>";

        // Get table structure
        echo "<div class='structure'>";
        echo "<h3>Structure:</h3>";
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

        $structure = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($structure as $field) {
            $keyClass = '';
            if ($field['Key'] == 'PRI') $keyClass = 'pk';
            elseif ($field['Key'] == 'MUL') $keyClass = 'fk';

            echo "<tr>";
            echo "<td class='$keyClass'>{$field['Field']}</td>";
            echo "<td>{$field['Type']}</td>";
            echo "<td>{$field['Null']}</td>";
            echo "<td class='$keyClass'>{$field['Key']}</td>";
            echo "<td>" . ($field['Default'] ?? '<span class="null">NULL</span>') . "</td>";
            echo "<td>{$field['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";

        // Get all records
        echo "<div class='records'>";
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "<h3>Records: <span class='count'>$count</span></h3>";

        if ($count > 0) {
            // Limit display for large tables
            $limit = $count > 100 ? 100 : $count;
            $records = $pdo->query("SELECT * FROM $table LIMIT $limit")->fetchAll(PDO::FETCH_ASSOC);

            if (count($records) > 0) {
                echo "<table>";
                // Header
                echo "<tr>";
                foreach (array_keys($records[0]) as $column) {
                    echo "<th>$column</th>";
                }
                echo "</tr>";

                // Data
                foreach ($records as $record) {
                    echo "<tr>";
                    foreach ($record as $value) {
                        if ($value === null) {
                            echo "<td><span class='null'>NULL</span></td>";
                        } elseif (strlen($value) > 100) {
                            echo "<td>" . htmlspecialchars(substr($value, 0, 100)) . "...</td>";
                        } else {
                            echo "<td>" . htmlspecialchars($value) . "</td>";
                        }
                    }
                    echo "</tr>";
                }
                echo "</table>";

                if ($count > $limit) {
                    echo "<p style='color: #FFC107;'>Showing first $limit of $count records</p>";
                }
            }
        } else {
            echo "<p style='color: #9E9E9E;'>No records in this table</p>";
        }
        echo "</div>";
    }

    // Show relationships
    echo "<h2>🔗 TABLE RELATIONSHIPS</h2>";
    echo "<div class='relationship'>";

    // Get foreign key relationships
    $relationships = $pdo->query("
        SELECT
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE REFERENCED_TABLE_NAME IS NOT NULL
        AND TABLE_SCHEMA = '$dbname'
        AND TABLE_NAME IN ('" . implode("','", $tables) . "')
        ORDER BY TABLE_NAME, COLUMN_NAME
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Foreign Key Relationships:</h3>";
    echo "<pre>";
    foreach ($relationships as $rel) {
        echo "{$rel['TABLE_NAME']}.{$rel['COLUMN_NAME']} ";
        echo "→ ";
        echo "{$rel['REFERENCED_TABLE_NAME']}.{$rel['REFERENCED_COLUMN_NAME']}";
        echo " (Constraint: {$rel['CONSTRAINT_NAME']})\n";
    }
    echo "</pre>";

    echo "<h3>Relationship Map:</h3>";
    echo "<pre>
┌─────────────────────────────────────────────────────────┐
│                    MEMBERS                              │
│  PK: member_id                                         │
└─────────────────────────────────────────────────────────┘
                           │
        ┌──────────────────┼──────────────────┐
        ↓                  ↓                  ↓
┌──────────────┐  ┌────────────────┐  ┌──────────────────┐
│   PATIENTS   │  │  MEDICALVOICE  │  │ AUDIO_TRANSCR.   │
│ PK: patient_ │  │ TRANSCRIPTIONS │  │    (OLD)         │
│     id       │  │ PK: transcript │  │ PK: id           │
│ FK: member_  │  │     ion_id     │  │ FK: member_id    │
│     id       │  │ FK: member_id  │  │ FK: patient_id   │
└──────────────┘  │ FK: patient_id │  └──────────────────┘
        │         └────────────────┘
        │                  │
        ↓                  ↓
┌──────────────┐  ┌────────────────┐
│   PATIENT_   │  │  TRANSCRIPTION │
│  DOCUMENTS   │  │   DOCUMENTS    │
│ FK: patient_ │  │ FK: transcript │
│     id       │  │     ion_id     │
└──────────────┘  └────────────────┘
        │
        ├──────────────┬──────────────┐
        ↓              ↓              ↓
┌──────────────┐ ┌──────────────┐ ┌──────────────┐
│   PATIENT_   │ │   PATIENT_   │ │   PATIENT_   │
│   VISITS     │ │ MED_HISTORY  │ │ ACCESS_LOG   │
│ FK: patient_ │ │ FK: patient_ │ │ FK: patient_ │
│     id       │ │     id       │ │     id       │
└──────────────┘ └──────────────┘ └──────────────┘
</pre>";
    echo "</div>";

    // Summary statistics
    echo "<h2>📈 SUMMARY STATISTICS</h2>";
    echo "<div class='structure'>";

    $stats = [];
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        $stats[$table] = $count;
    }

    echo "<pre>";
    echo "Table Record Counts:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    foreach ($stats as $table => $count) {
        printf("%-35s : %5d\n", $table, $count);
    }
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "TOTAL RECORDS                      : " . array_sum($stats) . "\n";
    echo "</pre>";

    // Patient linking statistics
    $patientStats = $pdo->query("
        SELECT
            COUNT(DISTINCT p.patient_id) as total_patients,
            COUNT(DISTINCT CASE WHEN mt.patient_id IS NOT NULL THEN p.patient_id END) as patients_with_transcriptions,
            COUNT(DISTINCT mt.transcription_id) as total_transcriptions,
            COUNT(DISTINCT CASE WHEN mt.patient_id IS NOT NULL THEN mt.transcription_id END) as linked_transcriptions
        FROM patients p
        LEFT JOIN medicalvoice_transcriptions mt ON p.patient_id = mt.patient_id
    ")->fetch(PDO::FETCH_ASSOC);

    echo "<h3>Patient-Transcription Linking:</h3>";
    echo "<pre>";
    echo "Total Patients                : {$patientStats['total_patients']}\n";
    echo "Patients with Transcriptions  : {$patientStats['patients_with_transcriptions']}\n";
    echo "Total Transcriptions          : {$patientStats['total_transcriptions']}\n";
    echo "Transcriptions with Patients  : {$patientStats['linked_transcriptions']}\n";
    $linkRate = $patientStats['total_transcriptions'] > 0
        ? round(($patientStats['linked_transcriptions'] / $patientStats['total_transcriptions']) * 100, 2)
        : 0;
    echo "Linking Rate                  : {$linkRate}%\n";
    echo "</pre>";
    echo "</div>";

    echo "</body></html>";

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?>