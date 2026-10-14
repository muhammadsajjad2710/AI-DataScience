<?php
// src/webhook.php
file_put_contents("../logs/webhook.log", json_encode($_POST, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);

header("Content-Type: application/json");

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Log incoming request data
file_put_contents("../logs/webhook_data.log", json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);

if (isset($data['entry'][0]['changes'][0]['value']['messages'])) {
    $message = $data['entry'][0]['changes'][0]['value']['messages'][0];

    $from = $message['from'];
    $type = $message['type'];

    if ($type == 'text') {
        $text = $message['text']['body'];
        $responseMessage = "Received your message: $text";
    } elseif (in_array($type, ['image', 'video', 'audio', 'document'])) {
        require_once "handleMedia.php";  
        $mediaResponse = handleMedia($message);
        $responseMessage = "Received your $type. It has been saved!";
    } else {
        $responseMessage = "Received a different type of message.";
    }

    file_put_contents("../logs/webhook_messages.log", json_encode($message, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);

    $response = [
        "messaging_product" => "whatsapp",
        "to" => $from,
        "text" => ["body" => $responseMessage]
    ];

    echo json_encode($response);
} else {
    file_put_contents("../logs/webhook_error.log", "No message received" . PHP_EOL, FILE_APPEND);
    echo json_encode(["status" => "no message received"]);
}

?>
