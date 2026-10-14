<?


$host        = "host = ";
   $port        = "port = ";
   $dbname      = "dbname = ";
   $credentials = "user = ";



	$db = pg_connect( "$host $port $dbname $credentials"  );
		if(!$db) {
			echo "Error : Unable to open database\n";
		}
	

function flatten(array $array) {
    $return = array();
    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
    return $return;
}


$modelslug = $_GET['modelslug'];
//$modelnameincoming = str_replace("-", " ", $modelslug);
//$modelnameincoming = str_replace("_", " ", $modelnameincoming);

header("Strict-Transport-Security:max-age=63072000");
header("X-Frame-Options: DENY");
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

session_start(['cookie_lifetime' => 43200,'cookie_secure' => true,'cookie_httponly' => true]);

if(!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID']) == '')) {
		header("location: https://chief.ai/sajjad/marketplace");
		exit();
	}

$memberid = $_SESSION['SESS_MEMBER_ID'];



if (isset($_SESSION['SESS_MEMBER_ID']))
{  



$qrymember = "select * from members where member_id = ".$_SESSION['SESS_MEMBER_ID'];


$ret = pg_query($db, $qrymember);
   if(!$ret) {
      echo pg_last_error($db);
     // exit;
   } 

while ($row = pg_fetch_assoc($ret)) {
	$displayname =  $row['displayname'];
        $membername = $row['login'];
        $membertype = $row['membertype'];

	}

if ($membertype == "C")
{
$membertype = "Customer";
}

else if ($membertype == "S")
{
$membertype = "Supplier";
}

$qryapi = "select apikey from members where member_id = ".$_SESSION['SESS_MEMBER_ID'];


$ret = pg_query($db, $qryapi);
   if(!$ret) {
      echo pg_last_error($db);
     // exit;
   } 

while ($row = pg_fetch_assoc($ret)) {
	$apikey =  $row['apikey'];
	}

$topbar = "Welcome, $displayname  <div class=\"dropdown\">
  <button class=\"btn btn-outline-primary dropdown-toggle\" type=\"button\" id=\"dropdownMenuButton\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
Account  </button>
  <div class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton\">
    <a class=\"dropdown-item\" href=\"https://chief.ai/sajjad/security\">Security</a>
    <a class=\"dropdown-item\" href=\"https://chief.ai/sajjad/billing\">Billing</a>
  </div>
</div>



<a class=\"btn btn-outline-primary logout\" data-toggle=\"modal\" data-target=\"#logoutmodal\">Sign Out</a>";







}

else

{
$topbar = " <a class=\"btn btn-outline-primary\" href=\"#\" data-toggle=\"modal\" data-target=\"#registermodal\">Register</a><a class=\"btn btn-outline-primary\" href=\"#\" data-toggle=\"modal\" data-target=\"#loginmodal\">Sign In</a>";

}


//var_dump($r);





if (isset($_SESSION['SESS_MEMBER_ID']))
{  

$qryused = "select model_id from customermodels where member_id = ".$_SESSION['SESS_MEMBER_ID'];


$resused = pg_query($db, $qryused);
   if(!$resused) {
      echo pg_last_error($db);
     //exit;
   } 


$used = array();
$used = pg_fetch_all($resused);
if ($used)
{
$used = flatten($used);
}
}

if (isset($_SESSION['SESS_MEMBER_ID']))
{  



$qrycredits = "select * from credits where member_id = ".$_SESSION['SESS_MEMBER_ID'];


$resultcredits = pg_query($db, $qrycredits);
   if(!$resultcredits) {
      echo pg_last_error($db);
     // exit;
   } 

while ($row = pg_fetch_assoc($resultcredits)) {
	$credits =  $row['credits'];
	}

}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico">

    <title>Customer - Patient Diagnosis</title>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js'></script>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/album/">

    <!-- Bootstrap core CSS -->
<link href="https://chief.ai/boot/css/bootstrap.min.css" rel="stylesheet">
<link href="https://chief.ai/boot/css/chiefy.css" rel="stylesheet">
<link href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/brands.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/solid.min.css" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <!-- Custom styles for this template -->



<style>

h1,h2,h3
{
color:white;
}  
.shimmer
{
background-image: linear-gradient(to bottom right, #9600ff 2%, #0b7dc4 30%, #00c8b0 70% );
}


progress[value]::-webkit-progress-value {
  background-image:
	   -webkit-linear-gradient(-45deg, 
	                           transparent 33%, rgba(0, 0, 0, .1) 33%, 
	                           rgba(0,0, 0, .1) 66%, transparent 66%),
	   -webkit-linear-gradient(top, 
	                           rgba(255, 255, 255, .25), 
	                           rgba(0, 0, 0, .25)),
	   -webkit-linear-gradient(left, #9600ff, #0b7dc4);

    border-radius: 2px; 
    background-size: 35px 20px, 100% 100%, 100% 100%;
width: 250px;
  height: 20px;
font-size:1.75rem;
}

</style>




  </head>

  <body>

    <div class="d-flex flex-column flex-md-row align-items-center p-1 px-md-4 mb-3 bg-chief shadow-sm">
  <div class="my-0 mr-md-auto font-weight-normal" data-toggle="modal" data-target="#conceptmodal">
                    Chief Artificial Intelligence</div>
                
<? echo $topbar; ?>
  
</div>

<div class="container">

<div class="row">
<ul class="nav">
  <li class="nav-item">
    <a class="nav-link active" href="#"><span style = "color: #9600ff;"><i class='bx bx-category-alt'></i>Dashboard</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="https://chief.ai/sajjad/marketplace"><span style = "color: #9600ff;"><i class='bx bx-brain'></i>AI Marketplace</a></span>
  </li>

  <li class="nav-item">
  </li>
</ul>
</div>

</div>




    <main role="main">

      <section class="jumbotron text-center shimmer">
        <div class="container">
          <h1 class="jumbotron-heading">AI Dashboard</h1>
          <p class="lead text-muted">Provide test data to execute a model</p>
          <p>
            <a href="https://chief.ai/sajjad/marketplace" class="btn btn-primary my-2">Marketplace</a>
            <a href="https://chief.ai/sajjad/supplier" class="btn btn-secondary my-2">For Suppliers</a>
          </p>
        <span style= "color:white; font-size:10px;">[ <?php echo $membertype; ?> account ] For help, please email support@chief.ai</span>
        </div>
      </section>








  <div class="container">

 <div class="row">
      <div class="col-md-4 columns"><i class='bx bx-time bx-sm'></i> Time <canvas id="executiontime" style = "width:100%;"></canvas></div>
      <div class="col-md-4 columns"><i class='bx bx-coin bx-sm'></i> Expense <canvas id="expensechart" style = "width:100%;"></canvas> </div>
      <div class="col-md-4 columns"><i class='bx bx-clipboard bx-sm'></i>Results<canvas id="resultschart" style = "width:100%;"></canvas></div>
</div>
</div>


      <div class="album py-5 bg-light">
        <div class="container">

<div class="row">
<div class="col-8 columns">


<span>API key:</span> <?  echo $apikey;   ?>
</div>


<div class="col-4 columns">

<span id = "viewresults" class="btn btn-outline-primary" data-toggle="modal">Refresh Results</span>

</div>


</div>

 <div class="row">
      <div class="col-md-4 columns">

<div class="card mb-4 shadow-sm">
      <div class="card-header">
        <h5 class="my-0 font-weight-normal"><i class='bx bx-caret-right-square'></i>Run AI Model</h5>
      </div>

<div class="card-body">

<progress class="progress m-1" value="10" max="100" style = "width:300px;"></progress>
 Status: <span class = "uploadmessage" style = "color:green; font-size:14px;">Ready</span>


<div class="form-group float-container" id="floatContainer">
	<SPAN class = "datasetsimport">Apply Dataset</SPAN>
						
							
						</div>
						<div class="form-group float-container"  id="floatContainer">
							<label class="float-label" for="floatField">Selected Model Name</label>
							<input class="form-control form-control-sm model-name" id="model-name" type="text" data-placeholder="Please enter the name of your model" required />
						</div>
						
					
						<div class="custom-file">
							<input type="file" class="custom-file-input zipped-model-folder" id="customFile">
							<label class="custom-file-label" for="customFile">Select Test Data</label>
						</div>
						<table> <!-- These are two smaller fields and are forced to be inline elements within a table here -->
							<tr>
								<td>
									<div class="form-group date-container">
										
									</div>
								</td>
								<td>
									<div class="form-group date-container">
									
									</div>
								</td>
							</tr>
						</table>
						
						<button type="submit" class="btn btn-primary btn-custom-form">Submit</button>



	
</div>
</div>

<div class="card shadow-sm">
      <div class="card-header">
        <h5 class="my-0 font-weight-normal"><i class='bx bx-history'></i>Execution history</h5>
      </div>
      <div class="card-body" >
        
 <div class="completedexecutions overflow-aut