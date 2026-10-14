<?php
// Database connection
$host = 'localhost';
$dbname = 'db2';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>MedicalVoice Database Records Analysis</h1>";
    echo "<style>
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .json-data { background: #f0f0f0; padding: 10px; border-radius: 5px; margin: 5px 0; }
        .section { margin: 30px 0; padding: 20px; border: 2px solid #ccc; border-radius: 10px; }
    </style>";

    // 1. PATIENTS TABLE
    echo "<div class='section'>";
    echo "<h2>1. PATIENTS Table (Patient Master Records)</h2>";
    $stmt = $pdo->query("SELECT * FROM patients LIMIT 5");
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($patients) {
        echo "<table>";
        echo "<tr><th>ID</th><th>MR Number</th><th>Name</th><th>DOB</th><th>Gender</th><th>Phone</th><th>Allergies</th><th>Conditions</th><th>Created By</th></tr>";
        foreach ($patients as $row) {
            echo "<tr>";
            echo "<td>{$row['patient_id']}</td>";
            echo "<td><strong>{$row['mr_number']}</strong></td>";
            echo "<td>{$row['first_name']} {$row['last_name']}</td>";
            echo "<td>{$row['date_of_birth']}</td>";
            echo "<td>{$row['gender']}</td>";
            echo "<td>{$row['phone']}</td>";
            echo "<td>" . substr($row['allergies'] ?? 'None', 0, 50) . "</td>";
            echo "<td>" . substr($row['medical_conditions'] ?? 'None', 0, 50) . "</td>";
            echo "<td>Member #{$row['member_id']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p>Total Patients: " . $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn() . "</p>";
    }
    echo "</div>";

    // 2. AUDIO_TRANSCRIPTIONS TABLE
    echo "<div class='section'>";
    echo "<h2>2. AUDIO_TRANSCRIPTIONS Table (Main Transcription Storage)</h2>";
    $stmt = $pdo->query("SELECT * FROM audio_transcriptions ORDER BY id DESC LIMIT 3");
    $transcriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($transcriptions) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Patient</th><th>MR Number</th><th>Audio File</th><th>Status</th><th>Created</th></tr>";
        foreach ($transcriptions as $row) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>" . ($row['patient_id'] ? "Patient #{$row['patient_id']}" : "Not Linked") . "</td>";
            echo "<td>{$row['mr_number']}</td>";
            echo "<td>" . basename($row['audio_file_path']) . "</td>";
            echo "<td><strong>{$row['processing_status']}</strong></td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";

        // Show sample transcribed text and analysis
        foreach ($transcriptions as $row) {
            if ($row['transcribed_text']) {
                echo "<h4>Transcription #{$row['id']} Content:</h4>";
                echo "<div class='json-data'>";
                echo "<strong>Transcribed Text (first 500 chars):</strong><br>";
                echo substr($row['transcribed_text'], 0, 500) . "...";
                echo "</div>";

                if ($row['medical_analysis']) {
                    echo "<div class='json-data'>";
                    echo "<strong>Medical Analysis (JSON):</strong><br>";
                    $analysis = json_decode($row['medical_analysis'], true);
                    echo "<pre>" . json_encode($analysis, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>";
                    echo "</div>";
                }
                break; // Show only first one with content
            }
        }
    }
    echo "<p>Total Transcriptions: " . $pdo->query("SELECT COUNT(*) FROM audio_transcriptions")->fetchColumn() . "</p>";
    echo "</div>";

    // 3. MEDICALVOICE_TRANSCRIPTIONS (Legacy Table)
    echo "<div class='section'>";
    echo "<h2>3. MEDICALVOICE_TRANSCRIPTIONS Table (New Patient-Linked System)</h2>";
    $stmt = $pdo->query("SELECT * FROM medicalvoice_transcriptions ORDER BY transcription_id DESC LIMIT 3");
    $legacy = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($legacy) {
        echo "<table>";
        echo "<tr><th>Trans ID</th><th>Patient</th><th>Audio File</th><th>Status</th><th>Processing Time</th><th>Created</th></tr>";
        foreach ($legacy as $row) {
            echo "<tr>";
            echo "<td>{$row['transcription_id']}</td>";
            echo "<td>" . ($row['patient_id'] ? "Patient #{$row['patient_id']}" : "Member #{$row['member_id']}") . "</td>";
            echo "<td>" . basename($row['audio_filename']) . "</td>";
            echo "<td><strong>{$row['processing_status']}</strong></td>";
            echo "<td>{$row['processing_time_seconds']}s</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";

        // Show sample content
        foreach ($legacy as $row) {
            if ($row['transcription_text']) {
                echo "<h4>Sample Transcription Content (ID: {$row['transcription_id']}):</h4>";
                echo "<div class='json-data'>";
                echo "<strong>Transcript (first 300 chars):</strong><br>";
                echo substr($row['transcription_text'], 0, 300) . "...";
                echo "</div>";

                if ($row['medical_analysis']) {
                    echo "<div class='json-data'>";
                    echo "<strong>Medical Analysis Extract:</strong><br>";
                    echo substr($row['medical_analysis'], 0, 500) . "...";
                    echo "</div>";
                }
                break;
            }
        }

        echo "<p>Total Records: " . $pdo->query("SELECT COUNT(*) FROM medicalvoice_transcriptions")->fetchColumn() . "</p>";
    }
    echo "</div>";

    // 4. PATIENT_DOCUMENTS TABLE
    echo "<div class='section'>";
    echo "<h2>4. PATIENT_DOCUMENTS Table</h2>";
    $stmt = $pdo->query("SELECT * FROM patient_documents LIMIT 5");
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($docs && count($docs) > 0) {
        echo "<table>";
        echo "<tr><th>Doc ID</th><th>Patient</th><th>Type</th><th>File</th><th>Upload Date</th></tr>";
        foreach ($docs as $row) {
            echo "<tr>";
            echo "<td>{$row['document_id']}</td>";
            echo "<td>Patient #{$row['patient_id']}<br>({$row['mr_number']})</td>";
            echo "<td>{$row['document_type']}</td>";
            echo "<td>{$row['original_filename']}</td>";
            echo "<td>{$row['upload_date']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No documents uploaded yet (Table ready for use)</p>";
    }
    echo "<p>Total Documents: " . $pdo->query("SELECT COUNT(*) FROM patient_documents")->fetchColumn() . "</p>";
    echo "</div>";

    // 5. PATIENT_ACCESS_LOG TABLE
    echo "<div class='section'>";
    echo "<h2>5. PATIENT_ACCESS_LOG Table (Audit Trail)</h2>";
    $stmt = $pdo->query("SELECT * FROM patient_access_log ORDER BY access_id DESC LIMIT 10");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($logs) {
        echo "<table>";
        echo "<tr><th>Log ID</th><th>Patient</th><th>User</th><th>Action</th><th>Details</th><th>Timestamp</th></tr>";
        foreach ($logs as $row) {
            echo "<tr>";
            echo "<td>{$row['access_id']}</td>";
            echo "<td>Patient #{$row['patient_id']}</td>";
            echo "<td>Member #{$row['member_id']}</td>";
            echo "<td><strong>{$row['access_type']}</strong></td>";
            echo "<td>" . substr($row['access_details'] ?? 'N/A', 0, 50) . "</td>";
            echo "<td>{$row['access_timestamp']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "<p>Total Access Logs: " . $pdo->query("SELECT COUNT(*) FROM patient_access_log")->fetchColumn() . "</p>";
    echo "</div>";

    // 6. TRANSCRIPTION_DOCUMENTS TABLE
    echo "<div class='section'>";
    echo "<h2>6. TRANSCRIPTION_DOCUMENTS Table (Document-Transcription Links)</h2>";
    $stmt = $pdo->query("SELECT * FROM transcription_documents ORDER BY id DESC LIMIT 5");
    $transDocs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($transDocs) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Transcription</th><th>Document Type</th><th>File Path</th><th>Uploaded</th></tr>";
        foreach ($transDocs as $row) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>Trans #{$row['transcription_id']}</td>";
            echo "<td>{$row['document_type']}</td>";
            echo "<td>" . basename($row['file_path']) . "</td>";
            echo "<td>{$row['uploaded_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "<p>Total Linked Documents: " . $pdo->query("SELECT COUNT(*) FROM transcription_documents")->fetchColumn() . "</p>";
    echo "</div>";

    // 7. MEMBERS TABLE (Users)
    echo "<div class='section'>";
    echo "<h2>7. MEMBERS Table (System Users)</h2>";
    $stmt = $pdo->query("SELECT member_id, username, email, credits, created_at FROM members LIMIT 5");
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($members) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Credits</th><th>Joined</th></tr>";
        foreach ($members as $row) {
            echo "<tr>";
            echo "<td>{$row['member_id']}</td>";
            echo "<td>{$row['username']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td><strong>{$row['credits']}</strong></td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "<p>Total Members: " . $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn() . "</p>";
    echo "</div>";

    // 8. DATA RELATIONSHIPS DEMONSTRATION
    echo "<div class='section'>";
    echo "<h2>8. Data Relationships Example</h2>";

    // Show a complete patient record with all related data
    $patientId = 1; // Example patient
    $stmt = $pdo->prepare("
        SELECT p.*,
               COUNT(DISTINCT at.id) as transcription_count,
               COUNT(DISTINCT pd.document_id) as document_count,
               COUNT(DISTINCT pal.access_id) as access_count
        FROM patients p
        LEFT JOIN audio_transcriptions at ON p.patient_id = at.patient_id
        LEFT JOIN patient_documents pd ON p.patient_id = pd.patient_id
        LEFT JOIN patient_access_log pal ON p.patient_id = pal.patient_id
        WHERE p.patient_id = ?
        GROUP BY p.patient_id
    ");
    $stmt->execute([$patientId]);
    $patientData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($patientData) {
        echo "<h3>Complete Patient Record Example (Patient #{$patientId})</h3>";
        echo "<div class='json-data'>";
        echo "<strong>Patient:</strong> {$patientData['first_name']} {$patientData['last_name']}<br>";
        echo "<strong>MR Number:</strong> {$patientData['mr_number']}<br>";
        echo "<strong>Related Records:</strong><br>";
        echo "- Transcriptions: {$patientData['transcription_count']}<br>";
        echo "- Documents: {$patientData['document_count']}<br>";
        echo "- Access Logs: {$patientData['access_count']}<br>";
        echo "</div>";
    }
    echo "</div>";

    // 9. FILE STORAGE STRUCTURE
    echo "<div class='section'>";
    echo "<h2>9. File Storage Structure</h2>";
    $audioDir = "/var/www/html/medicalnotes/medicalvoice/storage/uploads/audio/";

    echo "<div class='json-data'>";
    echo "<strong>Audio Files Directory:</strong><br>";
    $audioFiles = glob($audioDir . "files/*.{mp3,wav,m4a}", GLOB_BRACE);
    echo "Total Audio Files: " . count($audioFiles) . "<br>";
    if (count($audioFiles) > 0) {
        echo "Sample files:<br>";
        foreach (array_slice($audioFiles, 0, 3) as $file) {
            echo "- " . basename($file) . " (" . round(filesize($file) / 1024 / 1024, 2) . " MB)<br>";
        }
    }

    echo "<br><strong>JSON Output Directory:</strong><br>";
    $jsonFiles = glob($audioDir . "json/*.json");
    echo "Total JSON Files: " . count($jsonFiles) . "<br>";
    if (count($jsonFiles) > 0) {
        echo "Sample JSON files:<br>";
        foreach (array_slice($jsonFiles, 0, 3) as $file) {
            echo "- " . basename($file) . "<br>";
        }
    }
    echo "</div>";
    echo "</div>";

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?>