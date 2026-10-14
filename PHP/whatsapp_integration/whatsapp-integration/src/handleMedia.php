<?php
// src/handleMedia.php

function handleMedia($message) {
    $config = require __DIR__ . '/config.php';
    $api_key = $config['api_key'];
    
    $type = $message['type'];
    $mediaId = $message[$type]['id'];  // Get media ID
    $from = $message['from'];  // Sender

    // Step 1: Get Media URL from WhatsApp API
    $url = "https://graph.facebook.com/v17.0/$mediaId";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $api_key"
    ]);
    $mediaResponse = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (!isset($mediaResponse['url'])) {
        return "Failed to get media URL.";
    }
    
    $mediaUrl = $mediaResponse['url'];

    // Step 2: Download Media File
    $ch = curl_init($mediaUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $api_key"
    ]);
    $fileData = curl_exec($ch);
    curl_close($ch);

    // Step 3: Save Media File
    $extension = ($type == 'image') ? 'jpg' : (($type == 'video') ? 'mp4' : 'mp3');
    $fileName = "../logs/media/{$from}_{$mediaId}.$extension";
    
    if (!file_exists("../logs/media")) {
        mkdir("../logs/media", 0777, true);
    }

    file_put_contents($fileName, $fileData);

    return "Media saved as $fileName";
}
?>
