<?php
require "config.php";

if (!isset($_FILES['file']['name'])) {
    echo "<span style='color: red;'>No file uploaded.</span>";
    exit;
}

$uploadDir = "uploads/";
$fileName = basename($_FILES['file']['name']);
$uploadPath = $uploadDir . $fileName;

// Debugging: Print received file name
echo "<script>console.log('Uploading: " . $fileName . "');</script>";

// Check if file exists
if (file_exists($uploadPath)) {
    echo "<span style='color: red;'>File already exists.</span>";
    exit;
}

// Allowed file types
$allowedTypes = ["txt", "csv", "json"];
$fileType = pathinfo($uploadPath, PATHINFO_EXTENSION);

if (!in_array($fileType, $allowedTypes)) {
    echo "<span style='color: red;'>Invalid file type. Only text-based files are allowed.</span>";
    exit;
}

// Move file
if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
    echo "<span style='color: green;'>File uploaded successfully.</span>";
} else {
    echo "<span style='color: red;'>File upload failed.</span>";
}
?>
