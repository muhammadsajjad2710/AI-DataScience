<?php
session_start();

if (!isset($_SESSION["uploaded_image"])) {
    echo "No image found in session.";
    exit;
}

$imagePath = $_SESSION["uploaded_image"];
$imageData = base64_encode(file_get_contents($imagePath));

// Free AI API Example (Google AutoML Vision API)
$apiKey = "YOUR_API_KEY_HERE";
$url = "https://vision.googleapis.com/v1/images:annotate?key=$apiKey";

$requestData = json_encode([
    "requests" => [
        [
            "image" => ["content" => $imageData],
            "features" => [["type" => "LABEL_DETECTION", "maxResults" => 5]]
        ]
    ]
]);

$options = [
    "http" => [
        "header" => "Content-Type: application/json",
        "method" => "POST",
        "content" => $requestData
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

if ($response === FALSE) {
    echo "Error contacting AI API.";
} else {
    $labels = json_decode($response, true)["responses"][0]["labelAnnotations"];
    echo "<strong>Detected Labels:</strong><br>";
    foreach ($labels as $label) {
        echo $label["description"] . " (Confidence: " . round($label["score"] * 100, 2) . "%)<br>";
    }
}
?>
