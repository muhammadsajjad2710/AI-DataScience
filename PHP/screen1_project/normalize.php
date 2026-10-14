<?php
require "config.php";
require "vendor/autoload.php"; // Required for DOCX & XLSX processing

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;

$folder = "uploads/";
clearstatcache();
$files = array_diff(scandir($folder), array('.', '..'));

// echo "<pre>DEBUG: Scanned files in directory: ";
print_r($files);
echo "</pre>";

if (empty($files)) {
    echo "<span style='color: red;'>No files found for normalization.</span>";
    exit;
}

// **Sort files by last modified time**
$latestFile = null;
$latestTime = 0;

foreach ($files as $file) {
    $filePath = $folder . $file;
    if (is_file($filePath)) {
        $fileTime = filemtime($filePath); // Get the modification time
        if ($fileTime > $latestTime) {
            $latestTime = $fileTime;
            $latestFile = $file;
        }
    }
}

if (!$latestFile) {
    echo "<span style='color: red;'>No valid files found for processing.</span>";
    exit;
}

$filePath = $folder . $latestFile;
$fileType = pathinfo($filePath, PATHINFO_EXTENSION);

// echo "<pre>DEBUG: Latest file detected: $latestFile</pre>";
// echo "<pre>DEBUG: File path: $filePath</pre>";
// echo "<pre>DEBUG: File type: $fileType</pre>";

$normalizedData = [];

// Function to clean text data
function cleanText($text) {
    // Remove non-UTF8 characters
    $text = preg_replace('/[^\x20-\x7E]/', '', $text);

    // Remove extra spaces
    $text = trim(preg_replace('/\s+/', ' ', $text));

    // Convert special characters to HTML entities
    return htmlentities($text, ENT_QUOTES, "UTF-8");
}

// Normalize TXT
if ($fileType == "txt") {
    echo "<pre>DEBUG: Processing a TXT file...</pre>";
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $normalizedData[] = [cleanText($line)];
    }
}

// Normalize CSV
elseif ($fileType == "csv") {
    echo "<pre>DEBUG: Processing a CSV file...</pre>";
    $rows = array_map('str_getcsv', file($filePath));
    foreach ($rows as $row) {
        $normalizedData[] = array_map("cleanText", $row);
    }
}

// Normalize JSON
elseif ($fileType == "json") {
    // echo "<pre>DEBUG: Processing a JSON file...</pre>";
    $jsonData = json_decode(file_get_contents($filePath), true);
    
    if ($jsonData === null) {
        echo "<span style='color: red;'>DEBUG: JSON file is invalid or empty.</span>";
    } elseif (is_array($jsonData)) {
        foreach ($jsonData as $row) {
            if (is_array($row)) {
                $normalizedData[] = array_map("cleanText", $row);
            } else {
                // If it's a string or other type, wrap it in an array
                $normalizedData[] = [cleanText($row)];
            }
        }
    }
}

// Print raw normalized data for debugging
// echo "<pre>DEBUG: Normalized Data (First 5 Rows): ";
print_r(array_slice($normalizedData, 0, 5));
echo "</pre>";

// Convert to HTML table
if (!empty($normalizedData)) {
    echo "<h5>Preview of Normalized Data:</h5>";
    echo "<table class='table table-bordered'><thead><tr>";
    
    // Headers
    foreach ($normalizedData[0] as $key => $value) {
        echo "<th>Column " . ($key + 1) . "</th>";
    }
    echo "</tr></thead><tbody>";
    
    // Show only first 5 rows
    for ($i = 0; $i < min(5, count($normalizedData)); $i++) {
        echo "<tr>";
        foreach ($normalizedData[$i] as $value) {
            echo "<td>$value</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
    
    // Enable the "Next" button automatically
    echo "<script>$('#next2').prop('disabled', false);</script>";
} else {
    echo "<span style='color: red;'>No data available after normalization.</span>";
}
?>