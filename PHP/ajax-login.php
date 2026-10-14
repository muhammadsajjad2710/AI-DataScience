<?php
//Start session
session_start();

//Include database connection details
require_once('config.php');

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

//Connect to mysql server
$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	exit();
}

//Function to sanitize values received from the form. Prevents SQL injection
function clean($con, $str)
{

	return mysqli_real_escape_string($con, $str);
}

//Sanitize the POST values
$login = clean($con, $_POST['login']);
$password = clean($con, $_POST['password']);

//Input Validations
if ($login == '') {
	$errmsg_arr[] = 'Login ID missing';
	$errflag = true;
}
if ($password == '') {
	$errmsg_arr[] = 'Password missing';
	$errflag = true;
}

//If there are input validations, redirect back to the login form
if ($errflag) {
	$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
	session_write_close();
	//header("location: login-form.php");
	echo $errmsg_arr[0];
	exit();
}

//Create query
$qry = "SELECT * FROM members WHERE login='$login' AND password='" . md5($_POST['password']) . "'";
$result = $con->query($qry) or die($con->error . __LINE__);

//Check whether the query was successful or not
if ($result) {
	if ($result->num_rows > 0) {
		//Login Successful
		session_regenerate_id();
		$member = $result->fetch_assoc();
		$_SESSION['SESS_MEMBER_ID'] = $member['member_id'];
		$_SESSION['SESS_LOGIN'] = $member['login'];
		session_write_close();
		echo "success";
		exit();
	} else {
		//Login failed
		echo "failed";
		exit();
	}
} else {
	die("Query failed");
}
