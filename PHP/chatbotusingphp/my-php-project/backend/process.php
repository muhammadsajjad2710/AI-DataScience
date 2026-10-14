<?php
include "../database_configuration/database.php"; // Include database connection

if (!$conn) {
    die("<p style='color:red;'>❌ Database connection failed.</p>");
}

//echo "<p style='color:green;'>✔ Database connected successfully!</p>";

// Check if POST request contains an image path
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["image_path"])) {
    $imagePath = $_POST["image_path"];
    $imageName = basename($imagePath);
    
    // echo "<p>📂 Received image path: <strong>$imagePath</strong></p>";

    // Construct command to execute Python script
    $pythonScript = "handwritten_to_text.py";
    $command = escapeshellcmd("python $pythonScript \"$imagePath\"");
    
    // echo "<p>⚡ Executing command: <code>$command</code></p>";

    // Execute script and capture output
    $output = shell_exec($command);
    echo "<p>🐍 Python output: <code>$output</code></p>";

    // Decode JSON response
    $response = json_decode($output, true);

    if ($response === null) {
        echo "<p style='color:red;'>❌ JSON decoding failed. Possible syntax error in Python output.</p>";
        exit;
    }

    // echo "<p style='color:blue;'>✅ JSON decoded successfully.</p>";

    // Check if text is extracted
    if (isset($response["handwritten_text"])) {
        $extractedText = $response["handwritten_text"];
        $jsonResponse = json_encode($response, JSON_UNESCAPED_UNICODE);
        $fhirJson = json_encode(["resourceType" => "DocumentReference", "content" => $response]); // Example FHIR format

        echo "<p>📝 Extracted text: <strong>$extractedText</strong></p>";

        // Insert extracted data into the database
        $stmt = $conn->prepare("INSERT INTO fhir_documents (image_name, extracted_text, json_response, fhir_json) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $imageName, $extractedText, $jsonResponse, $fhirJson);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>✅ Data saved successfully in database.</p>";
        } else {
            echo "<p style='color:red;'>❌ Database error: " . $conn->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color:red;'>❌ No text extracted from the image.</p>";
    }
} else {
    echo "<p style='color:red;'>⚠ No image path received in POST request.</p>";
}


// Always show link to upload another image
echo "<p><a href='handwriting.php' style='text-decoration:none; color:blue; font-weight:bold;'>📤 Upload Another Image</a></p>";
// Close database connection
$conn->close();
?>
