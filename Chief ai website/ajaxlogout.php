<?php
$c = $_POST['logout'];

if ($c == "logout")
{
session_start();
	
	//Unset the variables stored in session
	unset($_SESSION['SESS_MEMBER_ID']);
	unset($_SESSION['SESS_LOGIN']);
	unset($_SESSION['SESS_DISPLAYNAME']);

echo "loggedout";
}
?>