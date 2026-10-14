<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Allowed file types
    $allowedTypes = ["jpg", "jpeg", "png"];
    if (!in_array($fileType, $allowedTypes)) {
        echo "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
        exit;
    }

    // Check if the file already exists
    if (file_exists($targetFilePath)) {
        $_SESSION["uploaded_image"] = $targetFilePath;
        echo $targetFilePath; // Return existing file path
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $_SESSION["uploaded_image"] = $targetFilePath;
            echo $targetFilePath; // Return new file path
        } else {
            echo "Error uploading file.";
        }
    }
} else {
    echo "No file uploaded.";
}
?>
