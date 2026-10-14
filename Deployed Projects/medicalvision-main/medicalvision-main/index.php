<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: ../medicalvision/login.php");
    exit;
}

// Image upload and duplicate check logic
$uploadedImagePath = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $uploadDir = __DIR__ . "/../storage/uploads/images/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($_FILES["image"]["name"]);
    $uploadFile = $uploadDir . $fileName;

    $uploadedFileHash = md5_file($_FILES["image"]["tmp_name"]);

    $duplicateFound = false;
    $existingFilePath = "";

    foreach (scandir($uploadDir) as $existingFile) {
        if ($existingFile === "." || $existingFile === "..") continue;

        $existingFilePath = $uploadDir . $existingFile;

        if (md5_file($existingFilePath) === $uploadedFileHash) {
            $duplicateFound = true;
            break;
        }
    }

    if ($duplicateFound) {
        $uploadedImagePath = $existingFilePath;
        $message = "<p class='success'>Image already exists. Using existing file.</p>";
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploadFile)) {
            $uploadedImagePath = $uploadFile;
            $message = "<p class='success'>Image uploaded successfully.</p>";
        } else {
            $message = "<p class='error'>Error uploading file.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Vision - Handwriting to Text</title>
    <link rel="stylesheet" href="../medicalvision/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            padding: 30px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }

        h1, h2 {
            color: #333;
        }

        form {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        input[type="file"] {
            margin-bottom: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: #3498db;
            border: none;
            color: white;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        .stripe-button {
            background-color: #28a745;
            margin-top: 30px;
        }

        .stripe-button:hover {
            background-color: #1e7e34;
        }

        img {
            margin-top: 20px;
            max-width: 300px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .success { color: green; }
        .error { color: red; }
        .message { margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>

<?php include('../medicalvision/header.php'); ?>

<div class="container">
    <h1>Medical AI Document Assistant</h1>
    <h2>Handwriting to Text</h2>

    <?php if (!empty($message)) echo "<div class='message'>{$message}</div>"; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="image" required>
        <br>
        <button type="submit">Upload Image</button>
    </form>

    <?php if (!empty($uploadedImagePath)): ?>
        <h3>Uploaded Image:</h3>
        <img src="<?= htmlspecialchars(str_replace(__DIR__ . '/../', '../', $uploadedImagePath)) ?>" alt="Uploaded Image">
        <form action="process.php" method="post">
            <input type="hidden" name="image_path" value="<?= htmlspecialchars($uploadedImagePath) ?>">
            <br>
            <button type="submit">Extract Text</button>
        </form>
    <?php endif; ?>

    <form action="../medicalvision/stripe_checkout.php" method="POST">
        <button type="submit" class="stripe-button">Pay Now (Stripe)</button>
    </form>
</div>

<?php include('../medicalvision/footer.php'); ?>
</body>
</html>
