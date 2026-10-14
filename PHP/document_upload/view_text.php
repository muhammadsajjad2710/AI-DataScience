<?php

session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['file'])) {
    die("Invalid Access!");
}

$filename = $_GET['file'];
$jsonFile = "uploads/{$filename}_text.json";

if (!file_exists($jsonFile)) {
    die("Extracted JSON not found!");
}

$jsonData = json_decode(file_get_contents($jsonFile), true);
$extractedText = $jsonData["text"] ?? "No text found!";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extracted Text</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include('header.php'); ?>

    <div class="container">
        <h2>Extracted Text</h2>
        <pre><?php echo htmlentities($extractedText); ?></pre>
        <a href="index.php">Upload Another Document</a>
    </div>

    <form action="result.php" method="POST">
    <input type="hidden" name="filename" value="<?php echo htmlspecialchars($_GET['file']); ?>">
    <button type="submit">Analyze</button>
    </form>

    <?php include('footer.php'); ?>


</body>
</html>
