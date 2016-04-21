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
    if ( isset($_POST['old_psw'])  && isset($_POST['E_mail'])
      			&& isset($_POST['new_psw'])  )
    {
    	//echo "all fields are set";
	$email = mysqli_real_escape_string($connection, $_POST['E_mail']);
        $old_password = mysqli_real_escape_string($connection, $_POST['old_psw']);
	$new_password= mysqli_real_escape_string($connection, $_POST['new_psw']);
	if (strcmp($old_password,$new_password)!=0){

	$mysql_get_users = mysqli_query($connection, "SELECT * FROM User WHERE Email='$email' AND Password='$old_password'");
	$get_rows = mysqli_num_rows($mysql_get_users);
	if($get_rows==1){
		$mysql_update_user = mysqli_query($connection, "UPDATE User SET Password='$new_password' WHERE Email='$email'");
		if ($mysql_update_user )
			header('Location: ../user_space/user_space.php');
		else
			$errors['sql'] = "Something went wrong. Please try again!";
	}
	else{
		$errors['email'] = "E_mail or Password incorrect! Please try again!";
	}
	}
	else
		$errors['password'] = "New password is the same as old password! Please try again!";
			
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
			<h1>Change password</h1>
			<p>Welcome to UI-Flix!</p>
			<form action = "change_pwd.php" method = "post" >
				
				<input type="text" name="E_mail" placeholder="EMAIL" required>
				
				<input type="password" name="old_psw" placeholder="OLD PASSWORD" required >
				
				<input type="password" name="new_psw" placeholder="NEW PASSWORD" required >
				
				

					<p id="Register_error">
					<?php echo display_errors($errors); ?>
					</p>
					
				<div class="row2">
			
				<input type = "submit" name = "submit" value="Submit">
				
				</div>
				
			</form>
		</div>

	</div>
	<div class="footer">
		<p>Copyright © 2015 Student Login Page. All Rights Reserved | Designed by Zihao Zhou, Xiaosheng Wu, Xiaohao Wang <a href="https://w3layouts.com/" target="_blank"></a></p>
	</div>
</body>
</html>