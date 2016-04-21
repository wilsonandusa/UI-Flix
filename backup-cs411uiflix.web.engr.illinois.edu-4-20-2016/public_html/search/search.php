<?php
ob_start();
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
	

       			
			$searchname =$_POST['search-field'];
		       	
		       	$user_username=$_GET["user"];
			$user_email=$_GET["email"];
			echo user_username;
			echo user_email;
			
			$mysql_get_movies = mysqli_query($connection, "SELECT * FROM Movies where movie_name LIKE '%$searchname%'");
			//echo $mysql_get_movies;
			$row =  mysqli_fetch_assoc($mysql_get_movies);
		
			$mysql_get_ID = $row["movie_id"];
			
	
    	
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

<?php
ob_start();
$page = $_get['text'];
$ID = (string)$mysql_get_ID;
$link="";

//if(($user_username=="$")&&($user_email=="$"))
$link.="http://cs411uiflix.web.engr.illinois.edu/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=";
$link.=$ID;
$link.="#";
header("location: $link");
echo $user_username;
?>