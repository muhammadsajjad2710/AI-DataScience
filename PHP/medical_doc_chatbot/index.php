<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Document</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <?php include('header.php'); ?>

    <div class="container">
        <h1>Chief AI Clinical Support System</h1>
        <h2>Upload Medical Document</h2>
        <form id="uploadForm" enctype="multipart/form-data">
            <input type="file" name="document" id="document" accept=".pdf,.doc,.docx,.txt" required>
            <button type="submit">Upload</button>
        </form>
    </div>

    <?php include('footer.php'); ?>
    <script src="script.js"></script>
</body>
</html>
