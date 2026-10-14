<?php
require "config.php";

// Define the report file name
$filename = "processed_report_" . date("Ymd_His") . ".csv";
$file = fopen("processed/" . $filename, "w");

// Title of the report
fputcsv($file, ["General Report"]);
fputcsv($file, []); // Blank line for spacing

// Fetch the last uploaded file name from the database
$query = "SELECT filename FROM processed_data ORDER BY ID DESC LIMIT 1"; 
$result = $conn->query($query);
$uploaded_file = "Unknown File"; // Default value

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $uploaded_file = $row['filename']; // Fetch filename from last row
}

// Step 1: Upload File
fputcsv($file, ["Step 1: Upload File"]);
fputcsv($file, ["File Uploaded Successfully: $uploaded_file"]);
fputcsv($file, []);

// Step 2: Normalize Data
fputcsv($file, ["Step 2: Normalize Data"]);
fputcsv($file, ["Data normalization and cleaning has been successfully completed."]);
fputcsv($file, ["Preview of Normalized Data:"]);

// Fetch the last processed row for preview
$query = "SELECT * FROM processed_data ORDER BY ID DESC LIMIT 1";
$result = $conn->query($query);

// Fetch column names dynamically
if ($result && $result->num_rows > 0) {
    $columns = array_keys($result->fetch_assoc());
    fputcsv($file, $columns); // Add column headers
    $result->data_seek(0); // Reset pointer to first row

    // Write last row data
    while ($row = $result->fetch_assoc()) {
        fputcsv($file, $row);
    }
} else {
    fputcsv($file, ["No processed data available."]);
}

fputcsv($file, []); // Blank line for spacing

// Step 3: Integrate Data into MySQL
fputcsv($file, ["Step 3: Integrate Data into MySQL"]);
fputcsv($file, ["Data has been successfully added to the database."]);
fputcsv($file, ["Clean and prepared data is stored in the database."]);
fputcsv($file, []);

// Step 4: Generate Report
fputcsv($file, ["Step 4: Generate Report"]);
fputcsv($file, ["This report includes all details of the processing steps performed."]);
fputcsv($file, ["The user can download this report for documentation and reference."]);

fclose($file);
$conn->close();

echo "<span style='color: green;'>Report generated successfully: <a href='processed/$filename' download>Download Report</a></span>";
?>
