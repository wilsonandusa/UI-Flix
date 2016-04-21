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
    if (isset($_POST['userid']) && isset($_POST['psw']) )
    {
    	//echo "all fields are set";
        $username = mysqli_real_escape_string($connection, $_POST['userid']);
        $password = mysqli_real_escape_string($connection, $_POST['psw']);

	$mysql_delete_users = mysqli_query($connection, "DELETe FROM User WHERE Username='$username' AND Password='$password'");
	if($mysql_delete_users){
		$msg = "delete successful!";
		header('Location: ../index.php');
	}
	else{
		$errors['username'] = "Username or Password incorrect! Please try again!";
	}
 			
    }

?>