<?php
$whatsappBaseUrl = "https://graph.facebook.com/v22.0/107981278905960/messages";
$accessToken = "EAAMf995DNuYBOzVkZB4C13bKaR0MQSxhX9YPepf1WaX0xNQh2JE5CnNodsUs5yZB8PFDwLoI4HZCLUepWGqrBQrVO7G2h2ZCjRPR852x4oncbfl3CZB1AiapWuXxxqUNg3GZCB0KeR1g6s0N6E82IpVAgE68Vq5MResBTR4eG0ZAOrnqEL31EZBKo44ZBZBZBvRhJfZB7edTwECv4KtqqZCuQkNhhgTkY";
$recipientPhone = "+923419843763"; // Replace with recipient's phone number (including country code)

$data = [
    "messaging_product" => "whatsapp",
    "to" => $recipientPhone,
    "type" => "text",
    "text" => ["body" => "Hello, this is a test message from my WhatsApp API!"]
];

$headers = [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
];

$ch = curl_init($whatsappBaseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

echo "Response: " . $response;
?>
