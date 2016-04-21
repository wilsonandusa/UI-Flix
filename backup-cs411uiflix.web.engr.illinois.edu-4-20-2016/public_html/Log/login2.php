<?php
	// require('dbconnect.php');
	$server_name="engr-cpanel-mysql.engr.illinois.edu";
	$user_name="cs411uif_wxh";
	$dbpassword="12345";
	$database_name="cs411uif_database";
	$connection = mysqli_connect($server_name,$user_name, $dbpassword);
	$errors = array();
	if (!$connection){
		// <script> alert('connection fail')</script>
	    die("Database Connection Failed" . mysqli_connect_error());
	}
	$select_db = mysqli_select_db($connection,"cs411uif_database");
	if (!$select_db){
		// <script> alert('databaseselection fail')</script>
	    die("Database Selection Failed???????????" . mysqli_error($connection));
	}
    if (isset($_POST['userid']) && isset($_POST['psw']) && isset($_POST[submit]) )
    {
    	//echo "all fields are set";
        $username = mysqli_real_escape_string($connection, $_POST['userid']);
        $password = mysqli_real_escape_string($connection, $_POST['psw']);

	$mysql_get_users = mysqli_query($connection, "SELECT * FROM User WHERE Username='$username' AND Password='$password'");
	$get_rows = mysqli_affected_rows($connection);
	if($get_rows !=1){
		$errors['username'] = "Username or Password incorrect! Please try again!";
	}
	else{
		$msg = "Login successful!";
		$rows=$mysql_get_users->fetch_assoc();
		$email=substr($rows["Email"],0);
		$url='Location: http://' . $_SERVER['HTTP_HOST'].'/index.php?user='.$username.'&email='.$email;
		header($url);
	}
 			
    }
    else if (isset($_POST['userid']) && isset($_POST['psw']) && isset($_POST[delete]) )
    {
    	//echo "all fields are set";
        $username = mysqli_real_escape_string($connection, $_POST['userid']);
        $password = mysqli_real_escape_string($connection, $_POST['psw']);
	$query = mysqli_query($connection, "SELECT * FROM User WHERE Username='$username' AND Password='$password'");
	$get_rows = mysqli_affected_rows($connection);
	if($get_rows==1){
		$row = $query->fetch_assoc();
		$email= substr( $row["Email"], 0);
		$mysql_delete_user = mysqli_query($connection, "DELETE FROM User WHERE Email='$email'");
		$mysql_delete_user = mysqli_query($connection, "DELETE FROM History_count WHERE Email='$email'");
		$mysql_delete_user = mysqli_query($connection, "DELETE FROM Favourite_count WHERE Email='$email'");
		$mysql_delete_user = mysqli_query($connection, "DELETE FROM History WHERE Email='$email'");
		$mysql_delete_user = mysqli_query($connection, "DELETE FROM Favourite_movie WHERE Email='$email'");
		if ($mysql_delete_user )
			header('Location: ../index.php');
		else
			$errors['username'] = "Username or Password incorrect! Please try again!";
			
	}
	else{
		$errors['username'] = "Username or Password incorrect! Please try again!";
	}
 			
    }
     //To go back to previous page 
    else if(isset($_POST['goback']))
    {
     header('Location: http://cs411uiflix.web.engr.illinois.edu');
    }
     else if(isset($_POST['Register']))
    {
     header('Location: http://cs411uiflix.web.engr.illinois.edu/Register/register2.php');
    }
   function display_errors($errors=array()){
   $output = "";
   if (!empty($errors)){ 
     foreach ($errors as $key => $error){
    	$output .= "{$error}<br />";
     }
   }
   return $output;
  }
 

  
?>


<!DOCTYPE html>
<html >
<head>
<meta charset="UTF-8">
<title>User Login</title>

<link rel="stylesheet" href="css/normalize.css">
<style type="text/css">

.footer{ position: absolute;left:28%; top:82%; right:28%;  color:white;font-size:normal;  text-align:center;}

.btn { display: inline-block; *display: inline; *zoom: 1; padding: 4px 10px 4px; margin-bottom: 0; font-size: 13px; line-height: 18px; color: #333333; text-align: center;text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75); vertical-align: middle; background-color: #f5f5f5; background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6); background-image: -ms-linear-gradient(top, #ffffff, #e6e6e6); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6)); background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6); background-image: -o-linear-gradient(top, #ffffff, #e6e6e6); background-image: linear-gradient(top, #ffffff, #e6e6e6); background-repeat: repeat-x; filter: progid:dximagetransform.microsoft.gradient(startColorstr=#ffffff, endColorstr=#e6e6e6, GradientType=0); border-color: #e6e6e6 #e6e6e6 #e6e6e6; border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25); border: 1px solid #e6e6e6; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); cursor: pointer; *margin-left: .3em; }
.btn:hover, .btn:active, .btn.active, .btn.disabled, .btn[disabled] { background-color: #e6e6e6; }
.btn-large { padding: 9px 14px; font-size: 15px; line-height: normal; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; }
.btn:hover { color: #333333; text-decoration: none; background-color: #e6e6e6; background-position: 0 -15px; -webkit-transition: background-position 0.1s linear; -moz-transition: background-position 0.1s linear; -ms-transition: background-position 0.1s linear; -o-transition: background-position 0.1s linear; transition: background-position 0.1s linear; }
.btn-primary, .btn-primary:hover { text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25); color: #ffffff; }
.btn-primary.active { color: rgba(255, 255, 255, 0.75); }
.btn-primary { background-color: #4a77d4; background-image: -moz-linear-gradient(top, #6eb6de, #4a77d4); background-image: -ms-linear-gradient(top, #6eb6de, #4a77d4); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#6eb6de), to(#4a77d4)); background-image: -webkit-linear-gradient(top, #6eb6de, #4a77d4); background-image: -o-linear-gradient(top, #6eb6de, #4a77d4); background-image: linear-gradient(top, #6eb6de, #4a77d4); background-repeat: repeat-x; filter: progid:dximagetransform.microsoft.gradient(startColorstr=#6eb6de, endColorstr=#4a77d4, GradientType=0);  border: 1px solid #3762bc; text-shadow: 1px 1px 1px rgba(0,0,0,0.4); box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.5); }
.btn-primary:hover, .btn-primary:active, .btn-primary.active, .btn-primary.disabled, .btn-primary[disabled] { filter: none; background-color: #4a77d4; }
.btn-block { width: 100%; display:block; }



* { -webkit-box-sizing:border-box; -moz-box-sizing:border-box; -ms-box-sizing:border-box; -o-box-sizing:border-box; box-sizing:border-box; }

html { width: 100%; height:100%; overflow:hidden; }

body { 
	width: 100%;
	height:100%;
	font-family: 'Open Sans', sans-serif;
	background: #092756;
	background: -moz-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%),-moz-linear-gradient(top,  rgba(57,173,219,.25) 0%, rgba(42,60,87,.4) 100%), -moz-linear-gradient(-45deg,  #670d10 0%, #092756 100%);
	background: -webkit-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -webkit-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -webkit-linear-gradient(-45deg,  #670d10 0%,#092756 100%);
	background: -o-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -o-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -o-linear-gradient(-45deg,  #670d10 0%,#092756 100%);
	background: -ms-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -ms-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -ms-linear-gradient(-45deg,  #670d10 0%,#092756 100%);
	background: -webkit-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), linear-gradient(to bottom,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), linear-gradient(135deg,  #670d10 0%,#092756 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#3E1D6D', endColorstr='#092756',GradientType=1 );
}
.login { 
	position: absolute;
	top: 40%;
	left: 50%;
	margin: -150px 0 0 -150px;
	width:300px;
	height:300px;
}
.login h1 { color: #fff; text-shadow: 0 0 10px rgba(0,0,0,0.3); letter-spacing:1px; text-align:center;  }


input { 
	width: 100%; 
	margin-bottom: 10px; 
	background: rgba(0,0,0,0.3);
	border: none;
	outline: none;
	padding: 10px;
	font-size: 13px;
	color: #fff;
	text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
	border: 1px solid rgba(0,0,0,0.3);
	border-radius: 4px;
	box-shadow: inset 0 -5px 45px rgba(100,100,100,0.2), 0 1px 1px rgba(255,255,255,0.2);
	-webkit-transition: box-shadow .5s ease;
	-moz-transition: box-shadow .5s ease;
	-o-transition: box-shadow .5s ease;
	-ms-transition: box-shadow .5s ease;
	transition: box-shadow .5s ease;
}
input:focus { box-shadow: inset 0 -5px 45px rgba(100,100,100,0.4), 0 1px 1px rgba(255,255,255,0.2); }


#btncontainer1 {position:absolute;top:80%;left:2%;width:130px}
#btncontainer2 {position:absolute;top:80%;right:2%;width:130px}

#btncontainer3 {position:absolute;top:95%;left:2%;width:130px}
#btncontainer4 {position:absolute;top:95%;right:2%;width:130px}
#err_msg       {position:absolute;top:61%;width:300px;color:gray;font-size:17px;text-align:center}

</style>
<script src="js/prefixfree.min.js">
</script>

<script type="text/javascript">
</script>


</head>

<body>

<div class="login">
	<h1>User Login</h1>
	<br></br>
	<form action = "login2.php" method = "post">
		<input type="text" name="userid" value="USERNAME" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'USERNAME';}">
		<input type="password" name="psw" value="PASSWORD" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'PASSWORD';}">
		<div id="err_msg">
			<p id="Register_error">
			<?php echo display_errors($errors); ?>
			</p>
			<br></br>
		</div>	
		<div id="btncontainer1">
		<button   type="submit" class="btn btn-primary btn-block btn-large"  name = "submit"   id="Login" onClick="error_msg();">Login</button>
		<p></p></div>
		<div id="btncontainer2">
		<button type="submit" class="btn btn-primary btn-block btn-large" name = "goback"  id="Back" >Back</button>
		<p></p>
		</div>
		
		<div id="btncontainer3">
		<button   type="submit" class="btn btn-primary btn-block btn-large"  name = "Register" id="Register">Register</button>
		<p></p></div>
		<div id="btncontainer4">
		<button type="submit" class="btn btn-primary btn-block btn-large" name = "delete"  id="Delete" >Delete</button>
		<p></p>
		</div>
	</form>
</div>

<div class="footer">
		<p>Copyright © 2016 Login Page. All Rights Reserved | Designed by Zihao Zhou, Xiaosheng Wu, Xiaohao Wang <a href="https://w3layouts.com/" target="_blank"></a></p>
	</div>

</body>
</html>