<?php
	// require('dbconnect.php');

	// Store user email as $user_email and current movie_id as cur_movie_id


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
	$cur_movie_id=$_GET["movie"];
	$user_email=$_GET["email"];
	$user_username=$_GET["user"];
	$query_movie= mysqli_query($connection,"UPDATE Movies SET counter=counter+1 WHERE movie_id=$cur_movie_id");
	$query_movie= mysqli_query($connection,"SELECT * FROM Movies WHERE movie_id=$cur_movie_id");
	$row=$query_movie->fetch_assoc();
	$movie_array=array($row["movie_name"],$row["movie_year"],$row["duration"],$row["score"],$row["storyline"],$row["genre"],$row["poster"]);
	$cur_genre=substr($row["genre"],0);
	$genre_string=explode(" ", $cur_genre);
	//Update History and History_count
	$query_history= mysqli_query($connection,"SELECT * FROM History WHERE email='$user_email' AND movie_id=$cur_movie_id");
	$hist_exist = mysqli_affected_rows($connection);
	if ($hist_exist==1){
		$row_age=mysqli_fetch_assoc($query_history);
		$cur_age=$row_age["age"];
		$query_increment_exist= mysqli_query($connection,"UPDATE History SET age=age+1 WHERE email='$user_email' AND age < $cur_age");
		$query_reset_exist= mysqli_query($connection,"UPDATE History SET age=0 WHERE email='$user_email' AND movie_id=$cur_movie_id");
		foreach($genre_string as $cur_string)
			$query_increment_genre_exist=mysqli_query($connection,"UPDATE History_count SET counter=counter+1 WHERE email='$user_email' AND genre LIKE '$cur_string'");
	}
	else{
		$query_increment_nonexist= mysqli_query($connection,"UPDATE History SET age=age+1 WHERE email='$user_email'");
		$query_delete_nonexist=mysqli_query($connection,"DELETE FROM History WHERE email='$user_email' AND age >=20");
		$query_add_nonexist=mysqli_query($connection,"INSERT INTO History(email,movie_id,age) VALUES('$user_email',$cur_movie_id,0)");
		foreach($genre_string as $cur_string)
			$query_increment_genre_nonexist=mysqli_query($connection,"UPDATE History_count SET counter=counter+1 WHERE email='$user_email' AND genre LIKE '$cur_string'");
	}

	//Update Favourite_movie and Favourite_count if user pressed Favourite
	if (1==2){
		$query_favourite= mysqli_query($connection,"SELECT * FROM Favourite_movie WHERE email='$user_email' AND movie_id=$cur_movie_id");
		$favourite_exist = mysqli_affected_rows($connection);
		if ($favourite_exist!=1){
			$query_favourite_insert=mysqli_query($connection,"INSERT INTO Favourite_movie(email,movie_id) VALUES ('$user_email',$cur_movie_id)");
			$query_favourite_genre=mysqli_query($connection,"UPDATE Favourite_count SET counter=counter+1 WHERE email='$user_email' AND genre LIKE '$cur_genre'");
		}
	}	
	if (1==2){
		$query_favourite= mysqli_query($connection,"SELECT * FROM Favourite_movie WHERE email='$user_email' AND movie_id=$cur_movie_id");
		$favourite_exist = mysqli_affected_rows($connection);
		if ($favourite_exist==1){
			$query_favourite_delete=mysqli_query($connection,"DELETE FROM Favourite_movie WHERE movie_id=$cur_movie_id AND email='$user_email'");
			$query_favourite_genre=mysqli_query($connection,"UPDATE Favourite_count SET counter=counter-1 WHERE email='$user_email' AND genre LIKE '$cur_genre'");
		}
	}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>3D Portfolio</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link type="text/css" href="styles/reset.css" rel="stylesheet" media="all" />
<link type="text/css" href="styles/text.css" rel="stylesheet" media="all" />
<link type="text/css" href="styles/960.css" rel="stylesheet" media="all" />
<link type="text/css" href="styles/style.css" rel="stylesheet" media="all" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="scripts/slideshow.js"></script>
</head>
<body>
<div class="header_cotainer">
  <div class="container_12">
    <div class="grid_3">
      <h1 class="logo">UI-Flix</h1>
    </div>
    <div class="grid_9">
      <div class="menu_items"> <a href=<?php echo "/../index.php?user=".$user_username."&email=".$user_email."#"; ?> class="home_link">Home</a>
        <div class="search">
          <input type="text" name="search" />
          <input type="submit" name="submit" value="Search" />
        </div>
      </div>
    </div>
  </div>
</div>
<div class="slider_container">
  <div class="container_12 slider_highlight">
    <div id="slideshow">
      <div id="slidesContainer">
        
        <div class="slide"> <img src="<?= $movie_array[6]?>" alt="" class="main_image" />
          <h2><?php echo $movie_array[0]?></h2>
          <p>Year: <?php echo $movie_array[1]?></p>
          <p>Duration: <?php echo $movie_array[2]?>min</p>
          <p>IMDB score: <?php echo $movie_array[3]?></p>
          <p>Genre: <?php echo $movie_array[5]?></p>
          <p>Storyline:<p>
          <p><?php echo $movie_array[4]?></p>
        </div>*/
      </div>
    </div>
  </div>
</div>
<div class="footer_container">
  <div class="container_12">
          <div class="extra_controls">
            <div class="buttons"> <a href="#" class="discover_more"></a> <a href="#" class="watch_video"></a> </div>
          </div>
    <div class="grid_4">Copyright &copy; <a href="#">Domain Name Here</a>, All Rights Reserved</div>
    <div class="grid_8">Design by <a target="_blank" href="http://www.1stwebdesigner.com">1stwebdesigner</a></div>
  </div>
</div>
</body>
</html>