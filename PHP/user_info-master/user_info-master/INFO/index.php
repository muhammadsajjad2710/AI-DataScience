<?php
require("UserInfo.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Getting user data using PHP</title>
</head>
<body>
    <center><h1>Getting user data using php</h1></center>
    <h1><?= UserInfo::get_ip();?></h1>
    <h1><?= UserInfo::get_device();?></h1>
    <h1><?= UserInfo::get_os();?></h1>
    <h1><?= UserInfo::get_browser();?></h1>
    
</body>
</html>