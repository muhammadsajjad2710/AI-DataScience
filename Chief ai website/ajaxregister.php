<?php
	//Start session
	session_start();
	
	//Include database connection details
	require_once('config.php');

//uses replysecondarymodals to get it in line with rest of website

 $db = pg_connect( "$host $port $dbname $credentials"  );
   if(!$db) {
      echo "Error : Unable to open database\n";
   } else {
      //echo "Opened database successfully\n";
   }


	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	
	
	
	//Function to sanitize values received from the form. Prevents SQL injection
	
	//Sanitize the POST values
	$fname = $_POST['name'];

	$login = $_POST['login'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$company = $_POST['company'];

	$membertype = $_POST['membertype'];

	$password = $_POST['password'];
	$cpassword = $_POST['cpassword'];
	

	//Input Validations
	

        if($login == '') {
		$errmsg_arr[] = 'Login ID missing.';
		$errflag = true;
	}


        if($phone == '') {
		$errmsg_arr[] = 'Mobile phone number missing.';
		$errflag = true;
	}


       if ( preg_match('/\s/',$login) ) 

       {
       $errmsg_arr[] = 'Username contains space.';
		$errflag = true;

       }




       
	if($password == '') {
		$errmsg_arr[] = 'Password missing';
		$errflag = true;
	}
	if($cpassword == '') {
		$errmsg_arr[] = 'Confirm password missing';
		$errflag = true;
	}
	if( strcmp($password, $cpassword) != 0 ) {
		$errmsg_arr[] = 'Passwords do not match';
		$errflag = true;
	}
        if($fname == '') {
		$displayname = $login;
	}
        else
        {
                $displayname = $fname;
        } 
	
	//Check for duplicate login ID

if($login != '') {
 $qry = "SELECT * FROM members WHERE login='$login'";

 $ret = pg_query($db, $qry);
                if($ret) {
             $num = pg_num_rows($ret);
                       if ($num > 0 ) 
                        {
               $errmsg_arr[] = 'Login ID already in use';
				$errflag = true;
                        }
                       } 
   else

            {
           echo pg_last_error($db);
           exit;
            } 

}


if($login != '') {
 $qry = "SELECT * FROM members WHERE email='$email'";

 $ret = pg_query($db, $qry);
                if($ret) {
             $num = pg_num_rows($ret);
                       if ($num > 0 ) 
                        {
               $errmsg_arr[] = 'email already in use';
				$errflag = true;
                        }
                       } 
   else

            {
           echo pg_last_error($db);
           exit;
            } 

}

	
if($login != '') {
 $qry = "SELECT * FROM members WHERE phone ='$phone'";

 $ret = pg_query($db, $qry);
                if($ret) {
             $num = pg_num_rows($ret);
                       if ($num > 0 ) 
                        {
               $errmsg_arr[] = 'Phone number already in use';
				$errflag = true;
                        }
                       } 
   else

            {
           echo pg_last_error($db);
           exit;
            } 

}





	
	//If there are input validations, redirect back to the registration form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		//header("location: register-form.php");
		echo $errmsg_arr[0];
		exit();
	}

	$key = implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6));

	//Create INSERT query
	$qry = "INSERT INTO members(fullname, displayname,company,email, phone, membertype, login, password, joindt, apikey) VALUES('$fname','$fname','$company','$email','$phone','$membertype','$login','".md5($_POST['password'])."',NOW(),'$key') RETURNING member_id";
	 
$retx = pg_query($db, $qry);
   if(!$retx) {
      echo pg_last_error($db);
      exit;
   } 




	
	//Check whether the query was successful or not
	if($retx) {


while ($rowreturned = pg_fetch_assoc($retx)) {
	$lastid =  $rowreturned['member_id'];
	}

//echo "$retx $lastid retxfired";

$arr = array('lastid' => $lastid, 'result' => "success", 'apikey' => $key);

		


              //Login right after registration

			session_regenerate_id();
			$_SESSION['SESS_MEMBER_ID'] = $lastid;
			$_SESSION['SESS_LOGIN'] = $login;
                        $_SESSION['SESS_DISPLAYNAME'] = $displayname;
			session_write_close();
                      //echo "success";
                      header("Content-type:application/json"); 

                      echo json_encode($arr);


	}else {
                      echo "failed";
	}
?>