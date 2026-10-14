<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speech to Text Converter</title>
</head>
<body>
    <h2>Upload an Audio File</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="audio_file" accept="audio/*" required>
        <button type="submit" name="upload">Upload</button>
    </form>

    <?php
// session_start(); // Start session if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
        // Define storage path outside the frontend directory
        $uploadDir = __DIR__ . "/../storage/uploads/audio/";

        // Ensure the directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Get file name and target path
        $fileName = basename($_FILES['audio_file']['name']);
        $targetFile = $uploadDir . $fileName;

        // Check if the file already exists
        if (file_exists($targetFile)) {
            echo "<p><strong>Info:</strong> File already exists. Upload skipped.</p>";
        } else {
            // Move file to the target directory
            if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $targetFile)) {
                $_SESSION['uploaded_file'] = $targetFile;
                echo "<p><strong>Success:</strong> File uploaded successfully.</p>";
                // echo "<p><strong>Path:</strong> " . realpath($targetFile) . "</p>";
            } else {
                echo "<p>Error: File upload failed.</p>";
            }
        }
    } else {
        echo "<p>Error: No file uploaded.</p>";
    }
}
?>




    <h2>Convert Speech to Text</h2>
    <form action="" method="post">
        <button type="submit" name="convert">Convert</button>
    </form>



    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['convert'])) {
        if (isset($_SESSION['uploaded_file'])) {
            $targetFile = $_SESSION['uploaded_file'];
            // $pythonScript = realpath(__DIR__ . "/backend/process_audio.py");
            $pythonScript = "D:/Web Development/PHP/chatbotusingphp/my-php-project/backend/process_audio.py";

            
            // Debug: Check resolved path
            if ($pythonScript === false) {
            echo "<p><strong>Debug Error:</strong> realpath() failed. File might not exist.</p>";
                exit();
            }

            // Debug: Check if script exists
            if (!file_exists($pythonScript)) {
                echo "<p><strong>Debug Error:</strong> Python script not found at $pythonScript</p>";
                exit();
            }
            

            // Construct the command
            $command = "python \"$pythonScript\" \"$targetFile\"";

            echo "<p><strong>Debug:</strong> Executing command - <code>$command</code></p>";

            // Execute the command
            $output = shell_exec($command);

            // Debug: Show raw output
            echo "<p><strong>Debug: Raw Output - </strong><pre>$output</pre></p>";

            // Check if the output is valid JSON
            $jsonCheck = json_decode($output, true);
            if ($jsonCheck === null) {
                echo "<p><strong>Debug Error:</strong> JSON decode failed. Check for extra debug prints in Python script.</p>";
                exit();
            }

            $responseData = json_decode($output, true);

            if ($responseData && isset($responseData['json_file'])) {
                $_SESSION['json_file'] = $responseData['json_file'];
                echo "<p>Speech converted successfully.</p>";
            } else {
                
                echo "<p><strong>Debug Error:</strong> JSON decode failed. Check script output.</p>";
            }
        } else {
            echo "<p>Error: No uploaded file found.</p>";
        }
    }
    ?>

    <h2>Read Response</h2>
    <form action="" method="post">
        <button type="submit" name="read">Read Response</button>
    </form>



    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['read'])) {
        if (isset($_SESSION['json_file']) && file_exists($_SESSION['json_file'])) {
            $jsonData = file_get_contents($_SESSION['json_file']);
            $responseData = json_decode($jsonData, true);

            if (isset($responseData['transcribed_text']) && isset($responseData['chatgpt_response'])) {
                echo "<h3>Transcribed Text:</h3><pre>" . htmlspecialchars($responseData['transcribed_text']) . "</pre>";
                echo "<h3>ChatGPT Response:</h3><pre>" . htmlspecialchars($responseData['chatgpt_response']) . "</pre>";
            } else {
                echo "<p><strong>Debug Error:</strong> Invalid response data.</p>";
            }
        } else {
            echo "<p>Error: No response found.</p>";
        }
    }
    ?>
</body>
</html>
