<?php
/**
 * MedicalNotes Installation Verification Script
 * Run this to verify everything is properly installed
 */

echo "========================================\n";
echo "MedicalNotes Installation Verification\n";
echo "========================================\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Check PHP Version
echo "1. Checking PHP Version...\n";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    $success[] = "✅ PHP Version: " . PHP_VERSION;
} else {
    $errors[] = "❌ PHP Version too old: " . PHP_VERSION . " (need 7.4+)";
}

// 2. Check Database Connection
echo "2. Checking Database Connection...\n";
try {
    $conn = new mysqli('localhost', 'root', '', 'db2');
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
    $success[] = "✅ Database 'db2' connected successfully";
    
    // Check tables
    $result = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    $success[] = "✅ Found " . count($tables) . " tables in database";
    
    // Check test user
    $result = $conn->query("SELECT username, credits FROM members WHERE username='testuser'");
    if ($user = $result->fetch_assoc()) {
        $success[] = "✅ Test user found: " . $user['username'] . " with " . $user['credits'] . " credits";
    } else {
        $warnings[] = "⚠️ Test user not found";
    }
    
    $conn->close();
} catch (Exception $e) {
    $errors[] = "❌ Database error: " . $e->getMessage();
}

// 3. Check Required Files
echo "3. Checking Required Files...\n";
$required_files = [
    'medicalvoice/database.php',
    'medicalvision/database.php',
    '.env',
    'config_loader.php',
    'medicalvoice/process_audio.py',
    'medicalvision/handwritten_to_text.py'
];

foreach ($required_files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        $success[] = "✅ File exists: $file";
    } else {
        $errors[] = "❌ Missing file: $file";
    }
}

// 4. Check Directories
echo "4. Checking Directories...\n";
$required_dirs = [
    'storage/uploads/audio/files',
    'storage/uploads/audio/json',
    'storage/uploads/audio/logs',
    'storage/uploads/vision/files',
    'medicalvoice/storage/uploads/audio',
    'medicalvision/storage/uploads/images'
];

foreach ($required_dirs as $dir) {
    if (is_dir(__DIR__ . '/' . $dir)) {
        $success[] = "✅ Directory exists: $dir";
        if (is_writable(__DIR__ . '/' . $dir)) {
            $success[] = "✅ Directory writable: $dir";
        } else {
            $warnings[] = "⚠️ Directory not writable: $dir";
        }
    } else {
        $errors[] = "❌ Missing directory: $dir";
    }
}

// 5. Check Python Modules
echo "5. Checking Python Modules...\n";
$python_check = shell_exec("python3 -c \"import openai, assemblyai, google.generativeai; print('OK')\" 2>&1");
if (strpos($python_check, 'OK') !== false) {
    $success[] = "✅ Python modules installed (openai, assemblyai, google.generativeai)";
} else {
    $errors[] = "❌ Python modules not installed properly";
}

// 6. Check .env file
echo "6. Checking Environment Configuration...\n";
if (file_exists(__DIR__ . '/.env')) {
    $env_content = file_get_contents(__DIR__ . '/.env');
    
    // Check for API keys
    if (strpos($env_content, 'OPENAI_API_KEY=') !== false) {
        if (strpos($env_content, 'YOUR_ACTUAL_OPENAI_KEY_HERE') !== false) {
            $warnings[] = "⚠️ OpenAI API key not configured (still using placeholder)";
        } else {
            $success[] = "✅ OpenAI API key configured";
        }
    }
    
    if (strpos($env_content, 'ASSEMBLYAI_API_KEY=') !== false) {
        $success[] = "✅ AssemblyAI API key configured";
    }
    
    if (strpos($env_content, 'GOOGLE_AI_API_KEY=') !== false) {
        $success[] = "✅ Google AI API key configured";
    }
}

// Print Summary
echo "\n========================================\n";
echo "VERIFICATION SUMMARY\n";
echo "========================================\n\n";

if (!empty($success)) {
    echo "✅ SUCCESSES (" . count($success) . "):\n";
    foreach ($success as $msg) {
        echo "  $msg\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️ WARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $msg) {
        echo "  $msg\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ ERRORS (" . count($errors) . "):\n";
    foreach ($errors as $msg) {
        echo "  $msg\n";
    }
    echo "\n";
}

// Final Status
echo "========================================\n";
if (empty($errors)) {
    if (empty($warnings)) {
        echo "✅ INSTALLATION COMPLETE AND VERIFIED!\n";
        echo "Both medicalvoice and medicalvision modules are ready to use.\n";
    } else {
        echo "✅ INSTALLATION COMPLETE WITH WARNINGS\n";
        echo "The system should work but check the warnings above.\n";
    }
} else {
    echo "❌ INSTALLATION INCOMPLETE\n";
    echo "Please fix the errors above before using the system.\n";
}
echo "========================================\n\n";

echo "Test Credentials:\n";
echo "Username: testuser\n";
echo "Password: password\n";
echo "Credits: 100\n\n";

echo "Access the application at: http://localhost/medicalnotes\n";
?>