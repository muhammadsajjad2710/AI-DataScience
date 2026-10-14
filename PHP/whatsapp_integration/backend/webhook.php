<?php
//FIRST GET THE 'GET' VARIABLES TO SATISFY WHATSAPP VERIFICATION

//If you add a webhook WhatsApp performs a GET Request to your configured endpoint. This GET Request comes with 3 Query String parameters

//    hub.mode (this one will always be a string containing "subscribe"

//    hub.challenge (this will be an Integer value which needs to be returned by your endpoint)

//    hub.verify_token ( the verify token you set when creating the webhook)

//    Your endpoint needs to have the three parameters as url parameters.



//    The logic in you flow would be

//    Check if the hub.mode query string parameter contains the string "subscribe"

//    Check if the hub.verify_token matches your Token (eg. stored in a site property)

//    If all passes return the value of hub.challenge

//    In all other cases respond with a 403 Status code.


//$input = json_decode(file_get_contents('php://input'), true);
// Get the Senders Graph ID / not applicable in web appp

//$sender = $input['sender'];
// Get the returned message
//$message = $input['message'];

//whatsapp msg in pure json format

//$purejson = file_get_contents('php://input');

//array format
$input = json_decode(file_get_contents('php://input'), true);

//documenting array to string
$arrayrec = var_export($input, true);

//log either array or original json, its your choice

  $myfile = fopen("debugfile.txt", "a") or die("Unable to open file!");
$txt = "JSON received and converted to PHP array:  $arrayrec \n";
fwrite($myfile, $txt);
fclose($myfile); 



//carefully parse the array

$whatsappid= $input['entry'][0]['id'];

$whatsappfrom = $input['entry'][0]['changes'][0]['value']['messages'][0]['from'];

$messagebody = $input['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];

$messageDetails = $input['entry'][0]['changes'][0]['value']['messages'][0];
$timestamp = $messageDetails['timestamp'];
$messageType = $messageDetails['type'];

$statusDetails = $input['entry'][0]['changes'][0]['value']['statuses'][0];
$status = $statusDetails['status'];

$mediaId = $input['entry'][0]['changes'][0]['value']['messages'][0]['image']['id'];




//$arraydump = var_export($whatsappfrom, true);

  $myfile = fopen("debugfile.txt", "a") or die("Unable to open file!");
$txt = "whatsapp id:  $whatsappid timestamp: $timestamp and $whatsappfrom and $messagebody and message type: $messageType and media id if present: $mediaId and status: $status \n";
fwrite($myfile, $txt);
fclose($myfile); 

$storedverifytoken = "orionsbelt";


$hubmode = $_GET['hub_mode'];

$hubchallenge = $_GET['hub_challenge'];

$hubverifytoken = $_GET['hub_verify_token'];

  $myfile = fopen("debugfile.txt", "a") or die("Unable to open file!");
$txt = "verification parameters:  $hubmode $hubchallenge $hubverifytoken \n";
fwrite($myfile, $txt);
fclose($myfile);  

if ($hubmode == "subscribe")
{

  
    
    if ($hubverifytoken == $storedverifytoken)
    {
        
        //if everything matches, reply with affirmative
        echo $hubchallenge;
        exit();
        
        
    }
    
    
}


//END WHATSAPP VERIFICATION CODE

//ATTEMPT MMEDIA FETCH FROM WHATSAPP

// Authorization token



$whatsappBaseUrl = "https://graph.facebook.com/v15.0/"; 
$accessToken = "EAAaatOUZBjfUBOzkl00I6MZBjTXcmsLcyTdCvcpEZAbJOMyHDzZCMgc81J0yB5Rg2yWtlA7YWOHSRjRUAaZCua5rCFkjyT55SZAV1i4ECJZAUsxTMZCcKHpGXMO6lCT4y4m5qR3BTBQanr8UeKZBytmNY5SHpNpYQj5HW1XYMV9t6NO4jIRZCSSEpadzMZAtKqjmxZBUofa7OKel0wZBilt4Y"; // replace with your actual WhatsApp access token
$agentHeader = "WhatsApp/2.19.81 A";
$saveDir = ""; // replace with the path to your save directory

// Initialize cURL session
$ch = curl_init();

// Set URL to fetch the media URL
$url = $whatsappBaseUrl . $mediaId;

// Set the headers
$headers = [
    'Authorization: Bearer ' . $accessToken,
    'User-Agent: ' . $agentHeader
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);

// Execute cURL session to get media URL
$mediaUrlJson = curl_exec($ch);
$mediaUrlArray = json_decode($mediaUrlJson, true);
$mediaUrl = $mediaUrlArray['url'] ?? ''; // assuming the media URL is in the 'url' key

// Check if we got the media URL successfully
if ($mediaUrl) {
    // Initialize a new cURL session to download the media
    $chMedia = curl_init($mediaUrl);
    curl_setopt($chMedia, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($chMedia, CURLOPT_RETURNTRANSFER, true);
    
    // Execute cURL session to download media
    $mediaData = curl_exec($chMedia);
    $httpStatus = curl_getinfo($chMedia, CURLINFO_HTTP_CODE);
    
    if ($httpStatus == 200) {
        // Saving the media to a file
        //$savePath = "mediaimage3.png";
                $savePath = $saveDir . basename($mediaId);

        file_put_contents($savePath, $mediaData);
        echo "Media saved to " . $savePath;
    } else {
        echo "Failed to download media.";
    }
    
    // Close cURL media handle
    curl_close($chMedia);
} else {
    echo "Failed to get media URL.";
}

// Close cURL handle
curl_close($ch);
?>
