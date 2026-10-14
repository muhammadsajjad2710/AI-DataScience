<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug log file
$debugFile = "debugfile.txt";
$logFile = fopen($debugFile, "a") or die("Unable to open file!");

// Function to write debug logs
function logDebug($message) {
    global $logFile;
    $timestamp = date("Y-m-d H:i:s");
    fwrite($logFile, "[$timestamp] $message\n");
    echo "<pre>Debug: $message</pre>";
}

logDebug("Script execution started");

// Handle WhatsApp webhook verification
$storedverifytoken = "orionsbelt";
$hubmode = $_GET['hub_mode'] ?? "";
$hubchallenge = $_GET['hub_challenge'] ?? "";
$hubverifytoken = $_GET['hub_verify_token'] ?? "";

logDebug("Verification parameters received: Mode=$hubmode, Challenge=$hubchallenge, Token=$hubverifytoken");

if ($hubmode === "subscribe") {
    if ($hubverifytoken === $storedverifytoken) {
        logDebug("Token verification successful. Responding with challenge.");
        echo $hubchallenge;
        fclose($logFile);
        exit();
    } else {
        logDebug("Token verification failed.");
        http_response_code(403);
        die("Forbidden");
    }
}

// Get and decode JSON input
$rawJson = file_get_contents('php://input');
logDebug("Raw JSON received: $rawJson");
$input = json_decode($rawJson, true);

if (!$input) {
    logDebug("Error: JSON decoding failed.");
    die("Invalid JSON received.");
}

try {
    $entry = $input['entry'][0]['changes'][0]['value']['messages'][0] ?? [];
    $whatsappid = $input['entry'][0]['id'] ?? "N/A";
    $whatsappfrom = $entry['from'] ?? "N/A";
    $messagebody = $entry['text']['body'] ?? "N/A";
    $timestamp = $entry['timestamp'] ?? "N/A";
    $messageType = $entry['type'] ?? "N/A";
    $status = $input['entry'][0]['changes'][0]['value']['statuses'][0]['status'] ?? "N/A";
    $mediaId = $entry['image']['id'] ?? "N/A";
    
    logDebug("Parsed WhatsApp data: ID=$whatsappid, From=$whatsappfrom, Message='$messagebody', Type=$messageType, Status=$status, Media ID=$mediaId");
} catch (Exception $e) {
    logDebug("Exception while parsing JSON: " . $e->getMessage());
    die("Error processing JSON data.");
}

// WhatsApp API Credentials
$whatsappBaseUrl = "https://graph.facebook.com/v15.0/"; 
$accessToken = "EAAaatOUZBjfUBOzkl00I6MZBjTXcmsLcyTdCvcpEZAbJOMyHDzZCMgc81J0yB5Rg2yWtlA7YWOHSRjRUAaZCua5rCFkjyT55SZAV1i4ECJZAUsxTMZCcKHpGXMO6lCT4y4m5qR3BTBQanr8UeKZBytmNY5SHpNpYQj5HW1XYMV9t6NO4jIRZCSSEpadzMZAtKqjmxZBUofa7OKel0wZBilt4Y"; // replace with your actual WhatsApp access token
$agentHeader = "WhatsApp/2.19.81 A";
$saveDir = ""; // replace with the path to your save directory

if (!is_dir($saveDir)) {
    mkdir($saveDir, 0777, true);
}

// Fetch media if media ID is available
if ($mediaId !== "N/A") {
    try {
        logDebug("Fetching media with ID: $mediaId");
        
        // Get media URL
        $ch = curl_init();
        $url = $whatsappBaseUrl . $mediaId;
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'User-Agent: ' . $agentHeader
        ];
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        
        $mediaUrlJson = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpStatus !== 200) {
            logDebug("Error fetching media URL. HTTP Status: $httpStatus, Response: $mediaUrlJson");
            die("Error fetching media URL.");
        }
        
        $mediaUrlArray = json_decode($mediaUrlJson, true);
        $mediaUrl = $mediaUrlArray['url'] ?? '';

        if (!$mediaUrl) {
            logDebug("Media URL not found in response.");
            die("Failed to get media URL.");
        }
        
        logDebug("Media URL retrieved: $mediaUrl");
        
        // Download media
        $chMedia = curl_init($mediaUrl);
        curl_setopt($chMedia, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($chMedia, CURLOPT_RETURNTRANSFER, true);
        
        $mediaData = curl_exec($chMedia);
        $httpStatus = curl_getinfo($chMedia, CURLINFO_HTTP_CODE);
        curl_close($chMedia);
        
        if ($httpStatus === 200) {
            $savePath = $saveDir . basename($mediaId) . ".jpg";
            file_put_contents($savePath, $mediaData);
            logDebug("Media saved successfully to: $savePath");
            echo "<pre>Media saved to: $savePath</pre>";
        } else {
            logDebug("Failed to download media. HTTP Status: $httpStatus");
            die("Failed to download media.");
        }
    } catch (Exception $e) {
        logDebug("Exception in media fetching: " . $e->getMessage());
        die("Error fetching media.");
    }
} else {
    logDebug("No media ID found. Skipping media download.");
}

fclose($logFile);
?>
