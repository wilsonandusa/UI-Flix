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
		header('Location: ../user_space/user_space.php');
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





<!--A Design by W3layouts 
Author: W3layout
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!doctype html>
<html>
<head>
<title>Student Login Form Flat Responsive widget :: w3layouts</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Student Login Form Widget Responsive, Login form web template,Flat Pricing tables,Flat Drop downs  Sign up Web Templates, Flat Web Templates, Login signup Responsive web template, Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyErricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href='//fonts.googleapis.com/css?family=Righteous' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<link href="style.css" rel='stylesheet' type='text/css' media="all"/>
</head>
<body>
	<div class="content">
		<div class="row1">
			<h1>User Login</h1>
			<p>Welcome to UI-Flix!</p>
			<form action = "log_in.php" method = "post">
				<input type="text" name="userid" value="USERNAME" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'USERNAME';}">
				<input type="password" name="psw" value="PASSWORD" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'PASSWORD';}">
				
				
				<p id="Register_error">
				<?php echo display_errors($errors); ?>
				</p>
				<div class="row2">
					<a href="/Register/register.php">Register</a>
					<input type="submit" name = "submit"  value="Login">
				</div>
				<div class="row2">
					<input type="submit" name = "delete"  value="DELETE">
					<input type="submit" name = "goback"  value="BACK" >
					
				</div>
				
			</form>
		</div>
	</div>
	<div class="footer">
		<p>Copyright © 2015 Student Login Page. All Rights Reserved | Designed by Zihao Zhou, Xiaosheng Wu, Xiaohao Wang <a href="https://w3layouts.com/" target="_blank"></a></p>
	</div>
</body>
</html>