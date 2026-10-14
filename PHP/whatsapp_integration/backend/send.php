<?php
function sendnow ($to, $msg, $type,$productimage)

{
    
    
    $myfile = fopen("debugfile.txt", "a") or die("Unable to open file!");
$txt = "sendnow fired is $msg $to \n";
fwrite($myfile, $txt);
fclose($myfile);

   $jsonData =  '{ "messaging_product": "whatsapp", "to": "'.$to.'", "type": "text", "text": { "preview_url": false, "body": "'.$msg.'" } }';
    
     //   $jsonData =  '{ "messaging_product": "whatsapp", "to": "447833286993", "type": "text", "text": { "preview_url": false, "body": "HAHAHAHAH" } }';

 
$url = 'https://graph.facebook.com/v16.0/107981278905960/messages';
 
 
 
//Initiate cURL.




$bearerToken = "EAAaatOUZBjfUBOzkl00I6MZBjTXcmsLcyTdCvcpEZAbJOMyHDzZCMgc81J0yB5Rg2yWtlA7YWOHSRjRUAaZCua5rCFkjyT55SZAV1i4ECJZAUsxTMZCcKHpGXMO6lCT4y4m5qR3BTBQanr8UeKZBytmNY5SHpNpYQj5HW1XYMV9t6NO4jIRZCSSEpadzMZAtKqjmxZBUofa7OKel0wZBilt4Y";

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' ));


//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BEARER);
curl_setopt($ch,CURLOPT_XOAUTH2_BEARER,$bearerToken);

//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//Execute the request but first check if the message is not empty.

  $result = curl_exec($ch);


$myfile = fopen("debugfile.txt", "a") or die("Unable to open file!");
$txt = "curl is $result \n";
fwrite($myfile, $txt);
fclose($myfile);
    
    
}
?>