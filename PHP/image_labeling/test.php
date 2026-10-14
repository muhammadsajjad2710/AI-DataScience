<?php
session_start();

if (!isset($_SESSION['uploaded_image'])) {
    echo json_encode(["error" => "No image found in session"]);
    exit;
}

$image_path = $_SESSION['uploaded_image']; // Get the stored image path

// Check if file exists
if (!file_exists($image_path)) {
    echo json_encode(["error" => "File does not exist: $image_path"]);
    exit;
}

// Read and encode the image properly
$image_data = file_get_contents($image_path);
$base64_image = base64_encode($image_data);

// Prepare the JSON payload (without the "data:image/jpeg;base64," prefix)
$data = json_encode(["inputs" => $base64_image]);

// Hugging Face API URL (Replace with an appropriate free model)
$model_url = "https://api-inference.huggingface.co/models/facebook/detr-resnet-50"; 
$api_token = "YOUR_API_KEY_HERE"; // Optional, for better API limits

$headers = [
    "Authorization: Bearer $api_token",
    "Content-Type: application/json"
];

// Send the request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $model_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

// Output response
echo $response;
?>
