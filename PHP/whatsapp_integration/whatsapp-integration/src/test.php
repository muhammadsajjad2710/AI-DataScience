<?php
// src/sendMessage.php
require_once 'WhatsAppAPI.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    $whatsapp = new WhatsAppAPI();
    $response = $whatsapp->sendMessage($phone, $message);

    echo json_encode($response);
}
 ?> 
