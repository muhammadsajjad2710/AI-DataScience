<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $uploadDir = "../storage/uploads/images/";

    // Ensure the images folder exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($_FILES["image"]["name"]);
    $uploadFile = $uploadDir . $fileName;

    // Compute hash of uploaded file
    $uploadedFileHash = md5_file($_FILES["image"]["tmp_name"]);

    // Check if the same file (content-wise) exists
    $duplicateFound = false;
    $existingFilePath = "";

    foreach (scandir($uploadDir) as $existingFile) {
        if ($existingFile === "." || $existingFile === "..") continue;

        $existingFilePath = $uploadDir . $existingFile;
        
        // Check if hashes match
        if (md5_file($existingFilePath) === $uploadedFileHash) {
            $duplicateFound = true;
            break;
        }
    }

    if ($duplicateFound) {
        $uploadedImagePath = $existingFilePath; // Use existing file
        echo "<p style='color:green;'>Image already exists. Using existing file.</p>";
    } else {
        // Save the new image
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploadFile)) {
            $uploadedImagePath = $uploadFile;
        } else {
            echo "<p style='color:red;'>Error uploading file.</p>";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>
</head>
<body>

    <h2>Upload Image</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="image" required>
        <button type="submit">Upload Image</button>
    </form>

    <?php if (isset($uploadedImagePath)): ?>
        <h3>Uploaded Image:</h3>
        <img src="<?= htmlspecialchars($uploadedImagePath) ?>" alt="Uploaded Image" width="300">
        <br><br>
        <form action="process.php" method="post">
            <input type="hidden" name="image_path" value="<?= htmlspecialchars($uploadedImagePath) ?>">
            <button type="submit">Extract Text</button>
        </form>
    <?php endif; ?>

</body>
</html>
