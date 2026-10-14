<?php
/**
 * Execute Patient Schema Creation
 * Run this script to create patient tables and update database schema
 */

// Include database connection
require_once 'database.php';

echo "Starting database schema update for patient management...\n\n";

// Read and execute SQL file
$sqlFile = __DIR__ . '/sql/create_patients_table.sql';

if (!file_exists($sqlFile)) {
    die("Error: SQL file not found at $sqlFile\n");
}

$sqlContent = file_get_contents($sqlFile);

// Split SQL statements (handling delimiter issues)
$statements = array_filter(array_map('trim', explode(';', $sqlContent)));

$successCount = 0;
$errorCount = 0;

foreach ($statements as $statement) {
    if (empty($statement)) {
        continue;
    }

    // Add semicolon back
    $statement .= ';';

    echo "Executing: " . substr($statement, 0, 100) . "...\n";

    try {
        if ($conn->query($statement) === TRUE) {
            echo "✓ Success\n\n";
            $successCount++;
        } else {
            echo "✗ Error: " . $conn->error . "\n\n";
            $errorCount++;
        }
    } catch (Exception $e) {
        echo "✗ Exception: " . $e->getMessage() . "\n\n";
        $errorCount++;
    }
}

echo "\n===============================\n";
echo "Schema update completed!\n";
echo "Successful operations: $successCount\n";
echo "Failed operations: $errorCount\n";

// Check if tables were created successfully
echo "\n===============================\n";
echo "Verifying tables...\n";

$tables = ['patients', 'patient_access_log'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "✓ Table '$table' exists\n";

        // Show table structure
        $result = $conn->query("DESCRIBE $table");
        echo "  Columns: ";
        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
        echo implode(', ', $columns) . "\n";
    } else {
        echo "✗ Table '$table' does not exist\n";
    }
}

// Check if patient_id was added to transcriptions table
$result = $conn->query("SHOW COLUMNS FROM transcriptions LIKE 'patient_id'");
if ($result && $result->num_rows > 0) {
    echo "✓ Column 'patient_id' added to transcriptions table\n";
} else {
    $result = $conn->query("SHOW COLUMNS FROM medicalvoice_transcriptions LIKE 'patient_id'");
    if ($result && $result->num_rows > 0) {
        echo "✓ Column 'patient_id' added to medicalvoice_transcriptions table\n";
    } else {
        echo "⚠ Note: patient_id column may need to be added manually to your transcriptions table\n";
    }
}

echo "\n===============================\n";
echo "Next steps:\n";
echo "1. Test the patient selector in the MedicalVoice interface\n";
echo "2. Create a test patient to verify functionality\n";
echo "3. Upload an audio file with patient context\n";

$conn->close();
?>