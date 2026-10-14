<?php
// Check upload limits via web server
echo "<h2>PHP Upload Limits Check</h2>";
echo "<pre>";
echo "Current PHP Version: " . phpversion() . "\n";
echo "SAPI: " . php_sapi_name() . "\n\n";

echo "=== Upload Limits ===\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . " seconds\n";
echo "max_input_time: " . ini_get('max_input_time') . " seconds\n";

$upload_max = ini_get('upload_max_filesize');
$post_max = ini_get('post_max_size');

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

$upload_bytes = return_bytes($upload_max);
$post_bytes = return_bytes($post_max);

echo "\n=== Status ===\n";
if ($upload_bytes >= 50 * 1024 * 1024) {
    echo "✅ Upload limit is sufficient for MedicalVoice (50MB required)\n";
} else {
    echo "❌ Upload limit is too low for MedicalVoice (50MB required, currently " . $upload_max . ")\n";
}

if ($post_bytes >= 52 * 1024 * 1024) {
    echo "✅ POST limit is sufficient\n";
} else {
    echo "⚠️ POST limit should be slightly higher than upload limit\n";
}

echo "\n=== Loaded Configuration File ===\n";
echo php_ini_loaded_file() . "\n";

echo "\n=== Additional .ini Files ===\n";
if ($iniFiles = php_ini_scanned_files()) {
    echo $iniFiles . "\n";
} else {
    echo "None\n";
}

echo "</pre>";

// Test form
echo '<hr><h3>Test Upload Form</h3>';
echo '<form method="post" enctype="multipart/form-data">';
echo '<input type="file" name="test_file" accept="audio/*">';
echo '<input type="submit" value="Test Upload">';
echo '</form>';

if ($_FILES) {
    echo '<h4>Upload Test Result:</h4><pre>';
    print_r($_FILES);
    echo '</pre>';
}
?>