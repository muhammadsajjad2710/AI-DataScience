<?php
// src/WhatsAppAPI.php

class WhatsAppAPI {
    private $api_key;
    private $base_url;

    public function __construct() {
        $config = require __DIR__ . '/config.php';
        $this->api_key = $config['api_key'];
        $this->base_url = $config['base_url'];
    }

    public function sendMessage($phone, $message) {
        $data = [
            'phone' => $phone,
            'message' => $message
        ];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->base_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
        $response = curl_exec($ch);
    
        // Check if cURL encountered an error
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            file_put_contents("../logs/errors.log", "cURL Error: " . $error_msg . PHP_EOL, FILE_APPEND);
            return ["status" => "error", "message" => "cURL Error: " . $error_msg];
        }
    
        curl_close($ch);
    
        // Log the API response
        file_put_contents("../logs/api_response.log", $response . PHP_EOL, FILE_APPEND);
    
        return json_decode($response, true);
    }
    
}
?>
