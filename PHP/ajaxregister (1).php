<?php
//Start session
session_start();

//Include database connection details
require_once('config.php');

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

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
$fname = clean($con, $_POST['fullname']);


$login = clean($con, $_POST['login']);
$email = clean($con, $_POST['email']);

$password = clean($con, $_POST['password']);
$cpassword = clean($con, $_POST['cpassword']);

//Input Validations


if ($login == '') {
	$errmsg_arr[] = 'Login ID missing';
	$errflag = true;
}


if (preg_match('/\s/', $login)) {
	$errmsg_arr[] = 'Username contains space.';
	$errflag = true;
}





if ($password == '') {
	$errmsg_arr[] = 'Password missing';
	$errflag = true;
}
if ($cpassword == '') {
	$errmsg_arr[] = 'Confirm password missing';
	$errflag = true;
}
if (strcmp($password, $cpassword) != 0) {
	$errmsg_arr[] = 'Passwords do not match';
	$errflag = true;
}
if ($fname == '') {
	$displayname = $login;
} else {
	$displayname = $fname;
}

//Check for duplicate login ID
if ($login != '') {
	$qry = "SELECT * FROM members WHERE login='$login'";
	$result = $con->query($qry) or die($con->error . __LINE__);
	if ($result) {
		if ($result->num_rows > 0) {
			$errmsg_arr[] = 'Login ID already in use';
			$errflag = true;
		}
		$result->free();
	} else {
		die("Query failed 1");
	}
}

if ($email != '') {
	$qry = "SELECT * FROM members WHERE email ='$email'";
	$result = $con->query($qry) or die($con->error . __LINE__);
	if ($result) {
		if ($result->num_rows > 0) {
			$errmsg_arr[] = 'Email address already in use';
			$errflag = true;
		}
		$result->free();
	} else {
		die("Query failed 1");
	}
}

//If there are input validations, redirect back to the registration form
if ($errflag) {
	$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
	session_write_close();
	//header("location: login-form.php");
	echo $errmsg_arr[0];
	exit();
}

//Create INSERT query
$qry = "INSERT INTO members(name, email, login, password, joindt) VALUES('$fname','$email','$login','" . md5($_POST['password']) . "',NOW())";
$result = $con->query($qry) or die($con->error . __LINE__);
$lastid = $con->insert_id;


//Check whether the query was successful or not
if ($result) {


	//Login right after registration

	session_regenerate_id();
	$_SESSION['SESS_MEMBER_ID'] = $lastid;
	$_SESSION['SESS_LOGIN'] = $login;
	// $_SESSION['SESS_DISPLAYNAME'] = $displayname;
	session_write_close();
	echo "success";
} else {
	echo "failed";
}
