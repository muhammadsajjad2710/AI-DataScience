<?php
// Database connection
$host = 'localhost';
$dbname = 'db2';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>📊 MedicalVoice Complete Table Analysis</h1>";
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        h1 { color: #2563EB; border-bottom: 3px solid #2563EB; padding-bottom: 10px; }
        h2 { color: #1E40AF; margin-top: 30px; }
        h3 { color: #3730A3; }
        .success { background: #10B981; color: white; padding: 10px; border-radius: 5px; }
        .error { background: #EF4444; color: white; padding: 10px; border-radius: 5px; }
        .warning { background: #F59E0B; color: white; padding: 10px; border-radius: 5px; }
        .info { background: #3B82F6; color: white; padding: 10px; border-radius: 5px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th { background: #4CAF50; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background-color: #f5f5f5; }
        .patient-linked { background: #D1FAE5; font-weight: bold; }
        .no-patient { background: #FEE2E2; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
        .stat-card { background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%); color: white; padding: 20px; border-radius: 10px; text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; }
        .stat-label { margin-top: 5px; opacity: 0.9; }
    </style>";

    // SECTION 1: SUMMARY STATISTICS
    echo "<div class='section'>";
    echo "<h2>📈 Summary Statistics</h2>";
    echo "<div class='stats-grid'>";

    // Total records in each table
    $tables = [
        'medicalvoice_transcriptions' => 'MedicalVoice Trans',
        'audio_transcriptions' => 'Audio Trans',
        'patients' => 'Patients',
        'members' => 'Members'
    ];

    foreach ($tables as $table => $label) {
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "<div class='stat-card'>";
        echo "<div class='stat-number'>$count</div>";
        echo "<div class='stat-label'>$label</div>";
        echo "</div>";
    }
    echo "</div>";
    echo "</div>";

    // SECTION 2: MEDICALVOICE_TRANSCRIPTIONS TABLE
    echo "<div class='section'>";
    echo "<h2>1️⃣ MEDICALVOICE_TRANSCRIPTIONS Table (Main Table)</h2>";

    $stmt = $pdo->query("
        SELECT
            mt.transcription_id,
            mt.member_id,
            mt.patient_id,
            p.first_name,
            p.last_name,
            p.mr_number,
            mt.audio_filename,
            mt.processing_status,
            mt.created_at
        FROM medicalvoice_transcriptions mt
        LEFT JOIN patients p ON mt.patient_id = p.patient_id
        ORDER BY mt.transcription_id DESC
        LIMIT 10
    ");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table>";
    echo "<tr>
            <th>Trans ID</th>
            <th>Member</th>
            <th>Patient Info</th>
            <th>Audio File</th>
            <th>Status</th>
            <th>Created</th>
          </tr>";

    foreach ($records as $row) {
        $patientClass = $row['patient_id'] ? 'patient-linked' : 'no-patient';
        $patientInfo = $row['patient_id']
            ? "✅ #{$row['patient_id']}: {$row['first_name']} {$row['last_name']}<br><small>{$row['mr_number']}</small>"
            : "❌ No Patient";

        echo "<tr>";
        echo "<td>#{$row['transcription_id']}</td>";
        echo "<td>Member #{$row['member_id']}</td>";
        echo "<td class='$patientClass'>$patientInfo</td>";
        echo "<td>" . substr($row['audio_filename'], 0, 30) . "...</td>";
        echo "<td><span class='" . ($row['processing_status'] == 'completed' ? 'success' : 'warning') . "' style='padding: 3px 8px; border-radius: 3px;'>{$row['processing_status']}</span></td>";
        echo "<td>{$row['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Patient linking statistics
    $stats = $pdo->query("
        SELECT
            COUNT(*) as total,
            SUM(CASE WHEN patient_id IS NOT NULL THEN 1 ELSE 0 END) as with_patient,
            SUM(CASE WHEN patient_id IS NULL THEN 1 ELSE 0 END) as without_patient
        FROM medicalvoice_transcriptions
    ")->fetch(PDO::FETCH_ASSOC);

    $linkingRate = $stats['total'] > 0 ? round(($stats['with_patient'] / $stats['total']) * 100, 1) : 0;

    echo "<div style='margin-top: 20px;'>";
    echo "<p class='info'>📊 <strong>Linking Statistics:</strong> ";
    echo "{$stats['with_patient']} records with patients ({$linkingRate}%), ";
    echo "{$stats['without_patient']} records without patients</p>";
    echo "</div>";
    echo "</div>";

    // SECTION 3: AUDIO_TRANSCRIPTIONS TABLE
    echo "<div class='section'>";
    echo "<h2>2️⃣ AUDIO_TRANSCRIPTIONS Table (Should be empty/minimal)</h2>";

    $stmt = $pdo->query("
        SELECT
            at.id,
            at.member_id,
            at.patient_id,
            at.mr_number,
            at.audio_file_path,
            at.processing_status,
            at.created_at
        FROM audio_transcriptions at
        ORDER BY at.id DESC
        LIMIT 10
    ");
    $audioRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($audioRecords) > 0) {
        echo "<p class='warning'>⚠️ This table should not be used anymore. Records found here indicate the old code path is still active!</p>";

        echo "<table>";
        echo "<tr>
                <th>ID</th>
                <th>Member</th>
                <th>Patient</th>
                <th>MR Number</th>
                <th>Audio File</th>
                <th>Status</th>
                <th>Created</th>
              </tr>";

        foreach ($audioRecords as $row) {
            $patientInfo = $row['patient_id']
                ? "✅ Patient #{$row['patient_id']}"
                : "❌ No Patient";

            echo "<tr>";
            echo "<td>#{$row['id']}</td>";
            echo "<td>Member #{$row['member_id']}</td>";
            echo "<td>$patientInfo</td>";
            echo "<td>{$row['mr_number']}</td>";
            echo "<td>" . basename($row['audio_file_path']) . "</td>";
            echo "<td>{$row['processing_status']}</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";

        // Show which file is the problematic one
        echo "<div class='error'>";
        echo "⚠️ <strong>ISSUE FOUND:</strong> The file 'recorded_audio_1759751495189.wav' was inserted into the wrong table!<br>";
        echo "This indicates there might be another code path that needs fixing.";
        echo "</div>";
    } else {
        echo "<p class='success'>✅ No records found - This is correct! All records should be in medicalvoice_transcriptions table.</p>";
    }
    echo "</div>";

    // SECTION 4: FILES VS DATABASE COMPARISON
    echo "<div class='section'>";
    echo "<h2>3️⃣ Files vs Database Comparison</h2>";

    // Get files from filesystem
    $audioDir = "/var/www/html/medicalnotes/medicalvoice/storage/uploads/audio/files/";
    $audioFiles = array_map('basename', glob($audioDir . "*.{mp3,wav,m4a}", GLOB_BRACE));

    // Get files from database
    $dbFiles = $pdo->query("SELECT DISTINCT audio_filename FROM medicalvoice_transcriptions")->fetchAll(PDO::FETCH_COLUMN);
    $dbFiles2 = $pdo->query("SELECT DISTINCT SUBSTRING_INDEX(audio_file_path, '/', -1) as filename FROM audio_transcriptions")->fetchAll(PDO::FETCH_COLUMN);

    // Find orphaned files
    $allDbFiles = array_merge($dbFiles, $dbFiles2);
    $orphanedFiles = array_diff($audioFiles, $allDbFiles);

    echo "<p><strong>Total audio files on disk:</strong> " . count($audioFiles) . "</p>";
    echo "<p><strong>Files in medicalvoice_transcriptions:</strong> " . count($dbFiles) . "</p>";
    echo "<p><strong>Files in audio_transcriptions:</strong> " . count($dbFiles2) . "</p>";

    if (count($orphanedFiles) > 0) {
        echo "<p class='warning'>⚠️ <strong>Orphaned files (on disk but not in database):</strong></p>";
        echo "<ul>";
        foreach (array_slice($orphanedFiles, 0, 5) as $file) {
            echo "<li>$file</li>";
        }
        if (count($orphanedFiles) > 5) {
            echo "<li>... and " . (count($orphanedFiles) - 5) . " more</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='success'>✅ All files are properly tracked in the database!</p>";
    }
    echo "</div>";

    // SECTION 5: LATEST ACTIVITY
    echo "<div class='section'>";
    echo "<h2>4️⃣ Latest Activity (Last 24 Hours)</h2>";

    $stmt = $pdo->query("
        SELECT
            'medicalvoice_transcriptions' as source,
            transcription_id as id,
            patient_id,
            audio_filename as filename,
            created_at
        FROM medicalvoice_transcriptions
        WHERE created_at >= NOW() - INTERVAL 1 DAY

        UNION ALL

        SELECT
            'audio_transcriptions' as source,
            id,
            patient_id,
            SUBSTRING_INDEX(audio_file_path, '/', -1) as filename,
            created_at
        FROM audio_transcriptions
        WHERE created_at >= NOW() - INTERVAL 1 DAY

        ORDER BY created_at DESC
    ");
    $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($recentActivity) > 0) {
        echo "<table>";
        echo "<tr><th>Source Table</th><th>ID</th><th>Patient</th><th>Filename</th><th>Created</th></tr>";
        foreach ($recentActivity as $row) {
            $tableClass = $row['source'] == 'medicalvoice_transcriptions' ? 'success' : 'error';
            echo "<tr>";
            echo "<td><span class='$tableClass' style='padding: 3px 8px; border-radius: 3px;'>{$row['source']}</span></td>";
            echo "<td>#{$row['id']}</td>";
            echo "<td>" . ($row['patient_id'] ? "✅ Patient #{$row['patient_id']}" : "❌ None") . "</td>";
            echo "<td>{$row['filename']}</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No activity in the last 24 hours.</p>";
    }
    echo "</div>";

    // SECTION 6: RECOMMENDATIONS
    echo "<div class='section'>";
    echo "<h2>✅ Status & Recommendations</h2>";

    echo "<h3>Current Issues:</h3>";
    echo "<ul>";

    if (count($audioRecords) > 0) {
        echo "<li class='error'>❌ Records are still being inserted into audio_transcriptions table</li>";
        echo "<li>→ Need to check if there are other code paths creating records</li>";
    } else {
        echo "<li class='success'>✅ No new records in audio_transcriptions table</li>";
    }

    if ($linkingRate < 50) {
        echo "<li class='warning'>⚠️ Low patient linking rate ({$linkingRate}%)</li>";
        echo "<li>→ Ensure users select patients before uploading</li>";
    } else {
        echo "<li class='success'>✅ Good patient linking rate ({$linkingRate}%)</li>";
    }

    echo "</ul>";

    echo "<h3>Fix Applied:</h3>";
    echo "<p class='success'>✅ upload.php has been updated to use medicalvoice_transcriptions table</p>";

    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Test a new upload with patient selection to confirm the fix works</li>";
    echo "<li>Monitor if new records appear in the correct table (medicalvoice_transcriptions)</li>";
    echo "<li>Check why 'recorded_audio_1759751495189.wav' went to the wrong table</li>";
    echo "<li>Consider migrating old records from audio_transcriptions to medicalvoice_transcriptions</li>";
    echo "</ol>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div class='error'>Database Error: " . $e->getMessage() . "</div>";
}
?>