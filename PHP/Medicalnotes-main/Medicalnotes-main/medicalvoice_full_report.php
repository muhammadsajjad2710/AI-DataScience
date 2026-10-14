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
        <title>MedicalVoice Complete Database Report</title>
        <style>
            body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
            .container { max-width: 1400px; margin: 0 auto; background: white; border-radius: 20px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
            h1 { color: #2563EB; text-align: center; font-size: 2.5em; margin-bottom: 30px; text-shadow: 2px 2px 4px rgba(0,0,0,0.1); }
            h2 { color: #1E40AF; border-bottom: 3px solid #3B82F6; padding-bottom: 10px; margin-top: 40px; }
            h3 { color: #3730A3; margin-top: 25px; }
            .alert { padding: 15px; border-radius: 10px; margin: 15px 0; }
            .alert-success { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); color: #064E3B; }
            .alert-error { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
            .alert-warning { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: #78350F; }
            .alert-info { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #1E3A8A; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-radius: 10px; overflow: hidden; }
            th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; text-align: left; font-weight: 600; }
            td { padding: 12px 15px; border-bottom: 1px solid #E5E7EB; }
            tr:hover { background-color: #F3F4F6; transition: all 0.3s ease; }
            .patient-yes { background: #D1FAE5; color: #064E3B; font-weight: bold; padding: 5px 10px; border-radius: 5px; display: inline-block; }
            .patient-no { background: #FEE2E2; color: #991B1B; padding: 5px 10px; border-radius: 5px; display: inline-block; }
            .status-completed { background: #10B981; color: white; padding: 5px 10px; border-radius: 5px; display: inline-block; }
            .status-pending { background: #F59E0B; color: white; padding: 5px 10px; border-radius: 5px; display: inline-block; }
            .status-failed { background: #EF4444; color: white; padding: 5px 10px; border-radius: 5px; display: inline-block; }
            .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 30px 0; }
            .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 15px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2); transform: translateY(0); transition: all 0.3s ease; }
            .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.3); }
            .stat-number { font-size: 2.5em; font-weight: bold; margin-bottom: 10px; }
            .stat-label { font-size: 1.1em; opacity: 0.95; }
            .relationship-diagram { background: #F9FAFB; border: 2px solid #E5E7EB; border-radius: 10px; padding: 20px; margin: 20px 0; }
            .relationship-line { padding: 10px; margin: 5px 0; background: white; border-left: 4px solid #3B82F6; }
            .timestamp { color: #6B7280; font-size: 0.9em; }
            .file-name { font-family: 'Courier New', monospace; background: #F3F4F6; padding: 3px 6px; border-radius: 3px; }
        </style>
    </head>
    <body>
    <div class='container'>";

    echo "<h1>🏥 MedicalVoice Complete Database Report</h1>";
    echo "<p class='timestamp' style='text-align: center;'>Generated: " . date('Y-m-d H:i:s') . "</p>";

    // ===== SUMMARY STATISTICS =====
    echo "<h2>📊 Summary Statistics</h2>";
    echo "<div class='stats-grid'>";

    $stats = [
        ['table' => 'medicalvoice_transcriptions', 'label' => 'MedicalVoice Trans', 'icon' => '🎙️'],
        ['table' => 'audio_transcriptions', 'label' => 'Audio Trans (Old)', 'icon' => '📼'],
        ['table' => 'patients', 'label' => 'Patients', 'icon' => '👥'],
        ['table' => 'transcription_documents', 'label' => 'Documents', 'icon' => '📄'],
        ['table' => 'members', 'label' => 'Members', 'icon' => '👤'],
        ['table' => 'processing_logs', 'label' => 'Process Logs', 'icon' => '📋']
    ];

    foreach ($stats as $stat) {
        $count = $pdo->query("SELECT COUNT(*) FROM {$stat['table']}")->fetchColumn();
        echo "<div class='stat-card'>";
        echo "<div style='font-size: 3em;'>{$stat['icon']}</div>";
        echo "<div class='stat-number'>$count</div>";
        echo "<div class='stat-label'>{$stat['label']}</div>";
        echo "</div>";
    }
    echo "</div>";

    // ===== CHECK FOR NEW UPLOADS =====
    echo "<h2>🆕 Recent Activity Check</h2>";

    // Check for uploads in the last hour
    $recentUploads = $pdo->query("
        SELECT transcription_id, patient_id, audio_filename, processing_status, created_at
        FROM medicalvoice_transcriptions
        WHERE created_at >= NOW() - INTERVAL 1 HOUR
        ORDER BY created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    if (count($recentUploads) > 0) {
        echo "<div class='alert alert-success'>✅ Found " . count($recentUploads) . " new upload(s) in the last hour!</div>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Patient</th><th>File</th><th>Status</th><th>Time</th></tr>";
        foreach ($recentUploads as $upload) {
            echo "<tr>";
            echo "<td>#{$upload['transcription_id']}</td>";
            echo "<td>" . ($upload['patient_id'] ? "<span class='patient-yes'>Patient #{$upload['patient_id']}</span>" : "<span class='patient-no'>No Patient</span>") . "</td>";
            echo "<td class='file-name'>" . substr($upload['audio_filename'], 0, 40) . "</td>";
            echo "<td><span class='status-{$upload['processing_status']}'>{$upload['processing_status']}</span></td>";
            echo "<td class='timestamp'>{$upload['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='alert alert-warning'>⚠️ No new uploads found in the last hour</div>";
    }

    // ===== MAIN TABLES =====
    echo "<h2>📁 1. MEDICALVOICE_TRANSCRIPTIONS (Main Table)</h2>";

    $mainRecords = $pdo->query("
        SELECT mt.*, p.first_name, p.last_name, p.mr_number
        FROM medicalvoice_transcriptions mt
        LEFT JOIN patients p ON mt.patient_id = p.patient_id
        ORDER BY mt.transcription_id DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo "<table>";
    echo "<tr><th>Trans ID</th><th>Member</th><th>Patient Info</th><th>Audio File</th><th>Status</th><th>Created</th></tr>";
    foreach ($mainRecords as $record) {
        $patientInfo = $record['patient_id']
            ? "<span class='patient-yes'>#{$record['patient_id']}: {$record['first_name']} {$record['last_name']}<br><small>{$record['mr_number']}</small></span>"
            : "<span class='patient-no'>No Patient</span>";

        echo "<tr>";
        echo "<td><strong>#{$record['transcription_id']}</strong></td>";
        echo "<td>Member #{$record['member_id']}</td>";
        echo "<td>$patientInfo</td>";
        echo "<td class='file-name'>" . substr($record['audio_filename'], 0, 35) . "...</td>";
        echo "<td><span class='status-{$record['processing_status']}'>{$record['processing_status']}</span></td>";
        echo "<td class='timestamp'>{$record['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Patient linking stats
    $linkStats = $pdo->query("
        SELECT
            COUNT(*) as total,
            SUM(CASE WHEN patient_id IS NOT NULL THEN 1 ELSE 0 END) as with_patient,
            SUM(CASE WHEN patient_id IS NULL THEN 1 ELSE 0 END) as without_patient
        FROM medicalvoice_transcriptions
    ")->fetch(PDO::FETCH_ASSOC);

    $linkRate = $linkStats['total'] > 0 ? round(($linkStats['with_patient'] / $linkStats['total']) * 100, 1) : 0;
    echo "<div class='alert alert-info'>";
    echo "📊 <strong>Patient Linking:</strong> ";
    echo "{$linkStats['with_patient']} records with patients ({$linkRate}%), ";
    echo "{$linkStats['without_patient']} records without patients";
    echo "</div>";

    // ===== AUDIO_TRANSCRIPTIONS TABLE =====
    echo "<h2>📼 2. AUDIO_TRANSCRIPTIONS (Old Table - Should Be Empty)</h2>";

    $audioRecords = $pdo->query("SELECT * FROM audio_transcriptions ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

    if (count($audioRecords) > 0) {
        echo "<div class='alert alert-error'>❌ WARNING: This table should not have new records!</div>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Member</th><th>Patient</th><th>MR Number</th><th>File</th><th>Status</th><th>Created</th></tr>";
        foreach ($audioRecords as $record) {
            echo "<tr>";
            echo "<td>#{$record['id']}</td>";
            echo "<td>Member #{$record['member_id']}</td>";
            echo "<td>" . ($record['patient_id'] ? "<span class='patient-yes'>Patient #{$record['patient_id']}</span>" : "<span class='patient-no'>None</span>") . "</td>";
            echo "<td>{$record['mr_number']}</td>";
            echo "<td class='file-name'>" . basename($record['audio_file_path']) . "</td>";
            echo "<td><span class='status-{$record['processing_status']}'>{$record['processing_status']}</span></td>";
            echo "<td class='timestamp'>{$record['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='alert alert-success'>✅ Good! No records in old table.</div>";
    }

    // ===== PATIENTS TABLE =====
    echo "<h2>👥 3. PATIENTS Table</h2>";

    $patients = $pdo->query("
        SELECT p.*,
               COUNT(DISTINCT mt.transcription_id) as transcription_count,
               COUNT(DISTINCT td.id) as document_count
        FROM patients p
        LEFT JOIN medicalvoice_transcriptions mt ON p.patient_id = mt.patient_id
        LEFT JOIN transcription_documents td ON p.patient_id = td.patient_id
        GROUP BY p.patient_id
        ORDER BY p.patient_id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo "<table>";
    echo "<tr><th>ID</th><th>MR Number</th><th>Name</th><th>Gender</th><th>DOB</th><th>Transcriptions</th><th>Documents</th><th>Created By</th></tr>";
    foreach ($patients as $patient) {
        echo "<tr>";
        echo "<td><strong>#{$patient['patient_id']}</strong></td>";
        echo "<td><strong>{$patient['mr_number']}</strong></td>";
        echo "<td>{$patient['first_name']} {$patient['last_name']}</td>";
        echo "<td>{$patient['gender']}</td>";
        echo "<td>{$patient['date_of_birth']}</td>";
        echo "<td>" . ($patient['transcription_count'] > 0 ? "<span class='patient-yes'>{$patient['transcription_count']}</span>" : "<span class='patient-no'>0</span>") . "</td>";
        echo "<td>" . ($patient['document_count'] > 0 ? "<span class='patient-yes'>{$patient['document_count']}</span>" : "<span class='patient-no'>0</span>") . "</td>";
        echo "<td>Member #{$patient['member_id']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    // ===== TRANSCRIPTION_DOCUMENTS =====
    echo "<h2>📄 4. TRANSCRIPTION_DOCUMENTS Table</h2>";

    $documents = $pdo->query("
        SELECT td.*, mt.patient_id as trans_patient_id
        FROM transcription_documents td
        LEFT JOIN medicalvoice_transcriptions mt ON td.transcription_id = mt.transcription_id
        ORDER BY td.id DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

    if (count($documents) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Trans ID</th><th>Patient</th><th>Type</th><th>File</th><th>Size</th><th>Created</th></tr>";
        foreach ($documents as $doc) {
            echo "<tr>";
            echo "<td>#{$doc['id']}</td>";
            echo "<td>Trans #{$doc['transcription_id']}</td>";
            echo "<td>" . ($doc['patient_id'] ? "Patient #{$doc['patient_id']}" : "Via Trans #{$doc['trans_patient_id']}") . "</td>";
            echo "<td>{$doc['document_type']}</td>";
            echo "<td class='file-name'>" . basename($doc['file_path']) . "</td>";
            echo "<td>" . round($doc['file_size'] / 1024, 2) . " KB</td>";
            echo "<td class='timestamp'>{$doc['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='alert alert-info'>No documents uploaded yet</div>";
    }

    // ===== TABLE RELATIONSHIPS =====
    echo "<h2>🔗 5. Database Table Relationships</h2>";
    echo "<div class='relationship-diagram'>";

    $relationships = [
        ['from' => 'patients', 'to' => 'medicalvoice_transcriptions', 'via' => 'patient_id', 'type' => '1:N'],
        ['from' => 'members', 'to' => 'medicalvoice_transcriptions', 'via' => 'member_id', 'type' => '1:N'],
        ['from' => 'medicalvoice_transcriptions', 'to' => 'transcription_documents', 'via' => 'transcription_id', 'type' => '1:N'],
        ['from' => 'patients', 'to' => 'patient_documents', 'via' => 'patient_id', 'type' => '1:N'],
        ['from' => 'patients', 'to' => 'patient_visits', 'via' => 'patient_id', 'type' => '1:N'],
        ['from' => 'patients', 'to' => 'patient_medical_history', 'via' => 'patient_id', 'type' => '1:N'],
        ['from' => 'members', 'to' => 'patients', 'via' => 'member_id', 'type' => '1:N']
    ];

    foreach ($relationships as $rel) {
        echo "<div class='relationship-line'>";
        echo "<strong>{$rel['from']}</strong> → <strong>{$rel['to']}</strong> ";
        echo "<span style='color: #6B7280;'>(via {$rel['via']}, {$rel['type']})</span>";
        echo "</div>";
    }
    echo "</div>";

    // ===== ISSUES AND RECOMMENDATIONS =====
    echo "<h2>⚠️ Issues & Recommendations</h2>";

    $issues = [];

    if (count($audioRecords) > 0) {
        $issues[] = ['type' => 'error', 'message' => 'Records found in audio_transcriptions table - should use medicalvoice_transcriptions'];
    }

    if ($linkRate < 50) {
        $issues[] = ['type' => 'warning', 'message' => "Low patient linking rate ({$linkRate}%) - ensure patients are selected before upload"];
    }

    // Check for orphaned files
    $audioDir = "/var/www/html/medicalnotes/medicalvoice/storage/uploads/audio/files/";
    $diskFiles = count(glob($audioDir . "*.{mp3,wav,m4a}", GLOB_BRACE));
    $dbFiles = $pdo->query("SELECT COUNT(DISTINCT audio_filename) FROM medicalvoice_transcriptions")->fetchColumn();

    if ($diskFiles > $dbFiles) {
        $orphaned = $diskFiles - $dbFiles;
        $issues[] = ['type' => 'warning', 'message' => "$orphaned audio files on disk not tracked in database"];
    }

    if (count($issues) > 0) {
        foreach ($issues as $issue) {
            echo "<div class='alert alert-{$issue['type']}'>{$issue['message']}</div>";
        }
    } else {
        echo "<div class='alert alert-success'>✅ No issues detected - system is working correctly!</div>";
    }

    echo "</div></body></html>";

} catch (PDOException $e) {
    echo "<div class='alert alert-error'>Database Error: " . $e->getMessage() . "</div>";
}
?>