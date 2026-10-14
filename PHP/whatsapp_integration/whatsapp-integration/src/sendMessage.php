<?php
require_once 'WhatsAppAPI.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['phone']) || !isset($_POST['message'])) {
        echo json_encode(["status" => "error", "message" => "Missing phone or message"]);
        exit;
    }

    $phone = $_POST['phone'];
    $message = $_POST['message'];

    $whatsapp = new WhatsAppAPI();
    $response = $whatsapp->sendMessage($phone, $message);

    file_put_contents("../logs/sendMessage.log", json_encode($response, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);

    if (isset($response['status']) && $response['status'] === "success") {
        echo json_encode(["status" => "success", "message" => "Message sent successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to send message.", "details" => $response]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
