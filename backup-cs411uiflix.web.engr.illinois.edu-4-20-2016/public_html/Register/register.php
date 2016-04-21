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
	
	$genre=array("action","adventure","animation","biography","comedy","crime","documentary","drama","family","fantacy",
		"film_noir","history","horror","music_","musical","mystery","romance","sci-fi","sport","thriller","war","western"); 

	
    if (isset($_POST['userid']) && isset($_POST['psw'])  && isset($_POST['Email'])
      			&& isset($_POST['Phone'])  && isset($_POST['Address']) )
    {
    	//echo "all fields are set";
        $username = mysqli_real_escape_string($connection, $_POST['userid']);
		$email = mysqli_real_escape_string($connection, $_POST['Email']);
        $password = mysqli_real_escape_string($connection, $_POST['psw']);
		$address = mysqli_real_escape_string($connection, $_POST['Address']);
        $phonenumber = mysqli_real_escape_string($connection, $_POST['Phone']);

			$mysql_get_users = mysqli_query($connection, "SELECT * FROM User where Username='$username'");
			$get_rows = mysqli_affected_rows($connection);
			if($get_rows >=1){
				$errors['username'] = "Username already exists. Please try another name";
			}
            $validate_email = filter_var($email, FILTER_VALIDATE_EMAIL);
            if (!$validate_email) {
            	$errors['email'] = "Please enter a valid email";
            }
            	
			$mysql_get_emails = mysqli_query($connection, "SELECT * FROM User where email='$email'");
			$get_rows2 = mysqli_affected_rows($connection);
			if($get_rows2 >=1){
				$errors['email'] = "Email has been taken. Please use another email address";
			}
           
            if (empty($errors)) {
	            $query = "INSERT INTO User (Username, Password, Phone, Address, Email) 
	 			VALUES ('$username', '$password', '$phonenumber', '$address', '$email')";
	//  			$query="INSERT INTO User( Username, 
	// PASSWORD , phone_num, address, email ) 
	// VALUES (
	// 'newname',  's1sss23123',  '123123',  'asdfasdf',  'a@a.a'
	// )";
		        $result = mysqli_query($connection,$query);
		        if($result){
		        //	echo "user create successfully";
		            $msg = "User Created Successfully.";
		            // create counters for user
			        for ($i=0;$i<22;$i++){
			        	$result_history=mysqli_query($connection,"INSERT INTO History_count VALUES ('$email','$genre[$i]',0)");
			        	$result_Favourite=mysqli_query($connection,"INSERT INTO Favourite_count VALUES ('$email','$genre[$i]',0)");
			    	}
		            // redirect to login page here
		            header('Location: ../Log/log_in.php');
		        }
		        else
		        {
		        	die("Database error: " . mysqli_error($connection));
		        }
            }
 			
    }
    
   
    
    else 
    	$errors['missing'] = "All fields are required";
    	
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
			<h1>User Register</h1>
			<p>Welcome to UI-Flix!</p>
			<form action = "register.php" method = "post" >
				
				<input type="text" name="userid" placeholder="USERNAME" required>
				
				<input type="password" name="psw" placeholder="PASSWORD" required>
				
				<input type="text" name="Email" placeholder="EMAIL" required>
				
				<input type="text" name="Phone" placeholder="PHONE" required>
				
				<input type="text" name="Address" placeholder="ADDRESS" required>
				
				

					<p id="Register_error">
					<?php echo display_errors($errors); ?>
					</p>
					
				<div class="row2">
			
				<input type = "submit" name = "submit" value="REGISTER">
				 <!-- go back to previous page -->
				<input type = "submit" name = "goback2" value="BACK" onclick="location.href='http://cs411uiflix.web.engr.illinois.edu/Log/log_in.php';"> 
				</div>
				
			</form>
		</div>

	</div>
	<div class="footer">
		<p>Copyright © 2015 Student Login Page. All Rights Reserved | Designed by Zihao Zhou, Xiaosheng Wu, Xiaohao Wang <a href="https://w3layouts.com/" target="_blank"></a></p>
	</div>
</body>
</html>