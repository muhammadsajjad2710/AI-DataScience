<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speech to Text</title>
</head>
<body>
    <h2>Speech to Text Converter</h2>
    <form method="post">
        <button type="submit" name="convert">Convert Speech to Text</button>
        <button type="submit" name="read_json">Read JSON Output</button>
    </form>
</body>
</html>

<?php
$pythonScript = __DIR__ . "/process_audio.py";
$storageDir = dirname(__DIR__) . "/storage"; // Move up one level to access 'storage'
$jsonFile = $storageDir . "/output.json";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["convert"])) {
        // Run the Python script
        $command = escapeshellcmd("python \"$pythonScript\" 2>&1");
        $output = shell_exec($command);
        echo "<pre>Python Output:\n$output</pre>";
    }

    if (isset($_POST["read_json"])) {
        // Check if the JSON file exists
        if (!file_exists($jsonFile)) {
            die("<p style='color:red;'>Error: JSON file not found in storage directory.</p>");
        }

        // Read and decode JSON file
        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);

        // Display the JSON output
        if ($data) {
            echo "<b>Transcribed Text:</b> <pre>" . htmlspecialchars($data['transcribed_text']) . "</pre>";
            echo "<b>ChatGPT Response:</b> <pre>" . htmlspecialchars($data['chatgpt_response']) . "</pre>";
        } else {
            echo "<p style='color:red;'>Error: Could not read JSON data.</p>";
        }
    }
}
?>

