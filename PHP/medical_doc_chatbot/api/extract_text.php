<?php
header('Content-Type: application/json');

if ($_FILES['document']) {
    $file = $_FILES['document'];
    $uploadDir = "../uploads/";
    $filename = time() . "_" . basename($file["name"]);
    $filePath = $uploadDir . $filename;

    if (move_uploaded_file($file["tmp_name"], $filePath)) {
        // Extract text from file
        $text = extractText($filePath);
        
        // Save extracted text in a text file
        file_put_contents("../uploads/$filename.txt", $text);

        // Also save as JSON

        $jsonData = json_encode(["text" => $text], JSON_PRETTY_PRINT);
        file_put_contents("../uploads/{$filename}_text.json", $jsonData);
        
        echo json_encode(["status" => "success", "filename" => $filename]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to upload file."]);
    }
}

// Function to extract text
// function extractText($filePath) {
//     $ext = pathinfo($filePath, PATHINFO_EXTENSION);

//     if ($ext === "txt") {
//         return file_get_contents($filePath);
//     } elseif ($ext === "pdf") {
//         return shell_exec("pdftotext $filePath -"); // Requires pdftotext installed
//     } elseif (in_array($ext, ["doc", "docx"])) {
//         return shell_exec("catdoc $filePath"); // Requires catdoc installed
//     }

//     return "Unsupported file format!";
// }
function extractText($filePath) {
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    if (!is_readable($filePath)) {
        return "Error: File is not readable.";
    }

    if ($ext === "txt") {
        return file_get_contents($filePath);
    } elseif ($ext === "pdf") {
        $output = shell_exec("pdftotext " . escapeshellarg($filePath) . " -");
        if (trim($output) === "") {
            return "No text extracted — PDF might be encoded or corrupted.";
        }
        return $output;
    } elseif (in_array($ext, ["doc", "docx"])) {
        $output = shell_exec("catdoc " . escapeshellarg($filePath));
        if (trim($output) === "") {
            return "No text extracted — DOC file might be empty or corrupted.";
        }
        return $output;
    }

    return "Unsupported file format!";
}

?>
