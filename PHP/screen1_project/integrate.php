<?php
require "config.php"; // Connect to DB

// Get the latest uploaded file
$folder = "uploads/";
$files = array_diff(scandir($folder), array('.', '..'));

if (empty($files)) {
    echo "<span style='color: red;'>No files found for processing.</span>";
    exit;
}

// Sort files by modification time (latest first)
$files = array_map(function($file) use ($folder) {
    return ["file" => $file, "time" => filemtime($folder . $file)];
}, $files);
usort($files, function($a, $b) {
    return $b["time"] - $a["time"]; // Sort descending (latest first)
});

// Get the most recently modified file
$latestFile = $files[0]["file"];
$filePath = $folder . $latestFile;
$fileContent = "";

// Read file content
$fileType = pathinfo($filePath, PATHINFO_EXTENSION);

if (in_array($fileType, ["txt", "csv", "json"])) {
    $fileContent = file_get_contents($filePath);
} elseif ($fileType == "xlsx") {
    require "vendor/autoload.php";
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    foreach ($worksheet->getRowIterator() as $row) {
        $rowData = [];
        foreach ($row->getCellIterator() as $cell) {
            $rowData[] = $cell->getValue();
        }
        $fileContent .= implode(",", $rowData) . "\n";
    }
}

// Store in database
$stmt = $conn->prepare("INSERT INTO processed_data (filename, content) VALUES (?, ?)");
$stmt->bind_param("ss", $latestFile, $fileContent);

if ($stmt->execute()) {
    echo "<span style='color: green;'>Data stored successfully in MySQL.</span>";
} else {
    echo "<span style='color: red;'>Failed to store data.</span>";
}

$stmt->close();
$conn->close();
?>
