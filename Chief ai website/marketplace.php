<?
$host        = "host = localhost";
   $port        = "port = 5432";
   $dbname      = "dbname = chiefai";
   $credentials = "user = postgress password=1234";

	$db = pg_connect( "$host $port $dbname $credentials"  );
		if(!$db) {
			echo "Error : Unable to open database\n";
		}
	

function flatten(array $array) {
    $return = array();
    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
    return $return;
}

header("Strict-Transport-Security:max-age=63072000");
header("X-Frame-Options: DENY");
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

session_start(['cookie_lifetime' => 43200,'cookie_secure' => true,'cookie_httponly' => true]);

	


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
        	$apikey =  $row['apikey'];

	}

//$qryapi = "select * from apikey where member_id = ".$_SESSION['SESS_MEMBER_ID'];


//$ret = pg_query($db, $qryapi);
//   if(!$ret) {
 //     echo pg_last_error($db);
     // exit;
 //  } 

//while ($row = pg_fetch_assoc($ret)) {
//	$apikey =  $row['apikey'];
//	}

$topbar = "Welcome, $displayname &nbsp;<div class=\"dropdown\">
  <button class=\"btn btn-outline-primary dropdown-toggle\" type=\"button\" id=\"dropdownMenuButton\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
Account  </button>
  <div class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton\">
    <a class=\"dropdown-item\" href=\"https://chief.ai/sajjad/security\">Security</a>
  </div>
</div>



<a class=\"btn btn-outline-primary logout\" data-toggle=\"modal\" data-target=\"#logoutmodal\">Sign Out</a>";







}

else

{
$topbar = "  <a class=\"btn btn-outline-primary\" href=\"#\" data-toggle=\"modal\" data-target=\"#registermodal\">Register</a> <a class=\"btn btn-outline-primary\" href=\"#\" data-toggle=\"modal\" data-target=\"#loginmodal\">Sign In</a>";


}

$qrymodel = "select * from models";


$retmodel = pg_query($db, $qrymodel);
   if(!$retmodel) {
      echo pg_last_error($db);
     // exit;
   } 



$r = array();
$r = pg_fetch_all($retmodel);
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


?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>AI Marketplace for Healthcare and Bioinformatics Algorithms</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/album/">

    <!-- Bootstrap core CSS -->
<link href="https://chief.ai/boot/css/bootstrap.min.css" rel="stylesheet">
<link href="https://chief.ai/boot/css/chiefy.css" rel="stylesheet">
<link href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

  </head>

  <body>

    <div class="d-flex flex-column flex-md-row align-items-center p-1 px-md-4 mb-3 bg-chief shadow-sm">
  <div class="my-0 mr-md-auto font-weight-normal" data-toggle="modal" data-target="#conceptmodal">
                    Chief Artificial Intelligence</div>
                
<? echo $topbar; ?>
  
</div>



    <main role="main">

      <section class="jumbotron text-center" style="background-image: linear-gradient(to bottom right, #9600ff 2%, #0b7dc4 30%, #00c8b0 70% );">
        <div class="container">
          <h1 class="jumbotron-heading" style = "color: #ffffff;">AI Marketplace</h1>
          <p class="lead" style = "color:#ffffff;">The world's best healthcare AI, available for rapid human and machine access</p>
                    <p class="lead" style = "color:#ffffff;">Please note: New customer accounts must be approved before algorithms can be used</p>

          <p>
<? 

if(isset($_SESSION['SESS_MEMBER_ID']) ) {
	 
 echo "<a href=\"https://chief.ai/sajjad/supplier\" class=\"btn btn-primary my-2\">Supplier Dashboard</a>
            <a href=\"https://chief.ai/sajjad/customer\" class=\"btn btn-secondary my-2\">Customer Dashboard</a>";
	}

?>
           
          </p>
        </div>
      </section>

      <div class="album py-5 bg-light">
        <div class="container">

          <div class="row">

<?

foreach ($r as $a)
{

$modelname = ucfirst($a['modelname']);
$modelid = ucfirst($a['model_id']);
$modeltext = $a['modeltext'];
$modeltext = ucfirst($modeltext);
$modality = ucfirst($a['modality']);
$accuracy = $a['accuracy'];
$sensitivity = $a['sensitivity'];
$specificity = $a['specificity'];
$algotype = ucfirst($a['type']);
$modelslug = $a['modelslug'];

$image = $a['modelimage'];
$imagepath = "https://chief.ai/sajjad/uploads/".$image;

//if (in_array($modelid, $used)) {

//$usedmodel = "<button type=\"button\" class=\"access btn btn-sm btn-success $modelid\"><i style = \"color: #ffd700;\" class=\"bx bx-star\" ></i></button>";

//}

//else

//{
$usedmodel = "<button type=\"button\" class=\"access btn btn-sm btn-primary $modelid\"><i class='bx bx-star' ></i></button>";
//}

echo "
  <div class=\"col-sm-3\">
              <div class=\"card mb-4 box-shadow\">
                <img src=\"$imagepath\" class=\"card-img-top\"  alt=\"Card image cap\">
                <div class=\"card-body\">
                  <h6 class=\"card-text\">$modelname</h6>

                  <p class=\"card-text\">$modeltext</p>
                  <div class=\"d-flex justify-content-between align-items-center\">
                    <div class=\"btn-group\">
                      <a href = \"https://chief.ai/sajjad/model/$modelslug\"><button type=\"button\" class=\"btn btn-sm btn-outline-secondary\">Access</button></a>

$usedmodel
                    </div>
                    <small class=\"text-muted\">Runtime: 12 sec</small>
                  </div>
                </div>
              </div>
            </div>

";

}

?>

            
         
       
           
          </div>
        </div>
      </div>


<div class="modal fade" id="loginmodal" tabindex="-1" role="dialog" aria-labelledby="requestmodal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="signupnmodalLabel1">Sign In</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

<br>
<input id = "username" type = "text" class = "login form-control" placeholder="username" />
<br>
<input id = "password" type = "password" class = "password form-control" placeholder="password" />



</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary buttonsignin" data-dismiss="modal">Sign In</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="registermodal" tabindex="-1" role="dialog" aria-labelledby="requestmodal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="signupnmodalLabel1">Register</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
<span class = "registermessage mt-2 mb-2"></span>
<input id = "fullname" type = "text" class = "login form-control" placeholder="name" />

<br>
<input id = "login" type = "text" class = "login form-control" placeholder="username" />
<br>
<input id = "rpassword" type = "password" class = "password form-control" placeholder="password" />
<br>
<input id = "cpassword" type = "password" class = "password form-control" placeholder="confirm password" />
<br>
<select id = "membertype" class = "membertype form-control" placeholder="Account Type" />
    <option value="" disabled selected>Select Account Type</option>
<option value="C">Customer</option>
  <option value="S">Supplier</option>
  <option value="CS">Customer and Supplier</option>
</select>
<br>
<input id = "email" type = "text" class = "password form-control" placeholder="email" />
<br>
<input id = "phone" type = "text" class = "password form-control" placeholder="phone" />
<br>
<input id = "company" type = "text" class = "password form-control" placeholder="company name" />


</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary buttonregister">Register</button>
      </div>
    </div>
  </div>
</div>



    </main>

    <footer class="text-muted">
      <div class="container">
        <p class="float-right">
          <a href="#">Back to top</a>
        </p>
        <p>&copy; Chief.AI 2024</p>
      </div>
    </footer>

   <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
<script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<script src="https://chief.ai/boot/js/bootstrap.min.js"></script>

    <script src="https://chief.ai/js/jsfunctions.js"></script>



<script>

$(function(){


$(document).on('click', '.buttonregister', function() {
  var login = $('#login').val();
  var rpassword = $('#rpassword').val();
  var cpassword = $('#cpassword').val();
  var fullname = $('#fullname').val();
  var email = $('#email').val();
  var company = $('#company').val();
  var phone = $('#phone').val();
  var membertype = $('#membertype').val();

  $.post("https://chief.ai/sajjad/ajaxregister.php", {
    login: login,
    name: fullname,
    password: rpassword,
    cpassword: cpassword,
    email: email,
    company: company,
    phone: phone,
    membertype: membertype
  }, function(data, status) {
    if (data.result == "success") {
      var lastid = data.lastid;
      var newapikey = data.apikey;

      var secondRequest = $.post("https://api.chief.ai/api/v1.0/mb/add", {
        member_id: lastid,
        membername: login,
        role: membertype,
        api_key: newapikey,
        created_on: 'NOW()'
      });

      secondRequest.done(function(data, status) {
        window.location.replace("https://chief.ai/sajjad/marketplace");
      });

      secondRequest.fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Request failed: " + textStatus + ", " + errorThrown);
        window.location.replace("https://chief.ai/sajjad/marketplace");
      });
    } else {
      $('.registermessage').text(data);
    }
  });
});


$(document).on('click', '.buttonsignin', function(){


var login = $('#username').val();
var password = $('#password').val();

$.post("https://chief.ai/sajjad/dashboard/ajaxlogin.php",
    {
      login: login,
      password: password,
    },
    function(data,status){

console.log(data);

if (data=="success")

{
//refresh page

window.location.replace("https://chief.ai/sajjad/marketplace");
 
}

else
{
$('#loginmessage').html("Wrong username or password.");

}

});


});



$(document).on('click', '.logout', function(){




$.post("https://chief.ai/sajjad/ajaxlogout.php",
    {
      logout: "logout",
    },
    function(data,status){

window.location.replace("https://chief.ai/sajjad/marketplace");



});


});





$(document).on('click', '.access', function(){


var holder = $(this);

var classes = holder.attr("class").split(" ");

var modelid = classes[classes.length-1];



 holder.toggleClass("btn-success"); //you can list several class names 
 holder.toggleClass("btn-primary"); //you can list several class names 

if (holder.hasClass('btn-success')) {
       // holder.text("Access");
holder.html('<i style = "color: #ffd700;" class="bx bx-star" ></i>');

    }








$.post("https://chief.ai/sajjad/customermodel.php",
    {
      modelid: modelid,
    },
    function(data,status){




});





});



});
</script>
  </body>
</html>
