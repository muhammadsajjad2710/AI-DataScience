<?php

if (!isset($_POST['filename'])) {
    die("Invalid access!");
}

$filename = $_POST['filename'];
$jsonPath = "uploads/{$filename}_text.json";

if (!file_exists($jsonPath)) {
    die("Extracted JSON file not found.");
}

$jsonData = json_decode(file_get_contents($jsonPath), true);
$text = $jsonData["text"] ?? "";

if (empty($text)) {
    die("No text found in JSON!");
}

// echo "<h3>Extracted Document Text:</h3><pre>" . htmlentities($text) . "</pre>";

require_once("config.php");  // contains your $openai_api_key

$text = substr($text, 0, 2000);  // limit to 2000 chars

$url = "https://api.openai.com/v1/chat/completions";

$data = [
    "model" => "gpt-4",
    "messages" => [
        [
            "role" => "system",
            "content" => "You are a medical assistant. The user will upload a medical document related to a disease, patient, or clinical note. Your task is to:

1. Extract and return the following structured information:
   - First Key Point: The most important medical point (e.g. disease name, patient condition).
   - Second Key Point: The second most important medical fact (e.g. severity, duration, treatment).
   - Third Key Point: The third important medical point (e.g. symptoms or side notes).

2. Provide the same extracted information as a FHIR-compliant JSON format."
        ],
        [
            "role" => "user",
            "content" => $text
        ]
    ]
];

$jsonData = json_encode($data);

$headers = [
    "Content-Type: application/json",
    "Authorization: Bearer $openai_api_key"
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
    curl_close($ch);
    exit;
}

curl_close($ch);

$responseData = json_decode($response, true);

if (!isset($responseData["choices"][0]["message"]["content"])) {
    echo "<h3>Error from OpenAI:</h3><pre>";
    print_r($responseData);
    echo "</pre>";
    exit;
}

$gptText = $responseData["choices"][0]["message"]["content"];

// Attempt to extract key points and FHIR from GPT response
$firstKey = $secondKey = $thirdKey = $fhirJson = "Not Found";

if (preg_match('/First Key Point\:(.*)/i', $gptText, $matches)) {
    $firstKey = trim($matches[1]);
}
if (preg_match('/Second Key Point\:(.*)/i', $gptText, $matches)) {
    $secondKey = trim($matches[1]);
}
if (preg_match('/Third Key Point\:(.*)/i', $gptText, $matches)) {
    $thirdKey = trim($matches[1]);
}
if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $gptText, $matches)) {
    $fhirJson = json_encode(json_decode($matches[0], true), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ChatGPT Medical Summary</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            vertical-align: top; /* <-- This ensures text starts from top */
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
        }
        .container {
            margin: 20px auto;
            width: 90%;
            max-width: 1100px;
        }
    </style>
    
</head>
<body>
<?php include('header.php'); ?>

<div class="container">
    <h1>Chief AI Clinical Support System</h1>
    <h2>ChatGPT Medical Summary:</h2>
    <table>
        <tr>
            <th>First Key Point</th>
            <th>Second Key Point</th>
            <th>Third Key Point</th>
            <th>FHIR Format Output</th>
        </tr>
        <tr>
            <td><?php echo htmlentities($firstKey); ?></td>
            <td><?php echo htmlentities($secondKey); ?></td>
            <td><?php echo htmlentities($thirdKey); ?></td>
            <td><pre><?php echo htmlentities($fhirJson); ?></pre></td>
        </tr>
    </table>

    <a href="index.php">Upload Another Document</a>
</div>
<?php include('footer.php'); ?>

</body>
</html>
