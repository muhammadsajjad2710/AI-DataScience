<?php
	//Start session
	session_start();
	
	//Include database connection details
	require_once('config.php');
	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	
	$db = pg_connect( "$host $port $dbname $credentials"  );
   if(!$db) {
      echo "Error : Unable to open database\n";
   } else {
      echo "Opened database successfully\n";
   }

	
	//Function to sanitize values received from the form. Prevents SQL injection
	
	
	//Sanitize the POST values
	$login = $_POST['login'];
	$password = $_POST['password'];
	
	//Input Validations
	if($login == '') {
		$errmsg_arr[] = 'Login ID missing';
		$errflag = true;
	}
	if($password == '') {
		$errmsg_arr[] = 'Password missing';
		$errflag = true;
	}
	
	//If there are input validations, redirect back to the login form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: login-form.php");
		exit();
	}
	
	//Create query
	$qry="SELECT * FROM members WHERE (phone = '$login' or login = '$login' ) AND password='".md5($_POST['password'])."'";
	 $result = pg_query($db, $qry);

	//Check whether the query was successful or not
	if($result) {
		if(pg_num_rows($result) > 0)  {
			//Login Successful
			session_regenerate_id();

                      while ($row = pg_fetch_assoc($result)) {
                      $_SESSION['SESS_MEMBER_ID'] = $row['member_id'];
                      $_SESSION['SESS_LOGIN'] = $row['login'];
                      $_SESSION['SESS_DISPLAYNAME'] = $member['displayname'];
                      }

		      session_write_close();
                      echo "success ".$_SESSION['SESS_LOGIN'];
		      exit();
		}else {
			//Login failed
		      echo "failed";
		      exit();
		}
	}else {

		die("Query failed");
	}
?>