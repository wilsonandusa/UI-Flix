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
	$query_movie= mysqli_query($connection,"SELECT * FROM Movies WHERE movie_id=$cur_movie_id");
	$row=$query_movie->fetch_assoc();
	$cur_genre=substr($row["genre"],0);
	//Update History and History_count
	$query_history= mysqli_query($connection,"SELECT * FROM History WHERE email='$user_email' AND movie_id=$cur_movie_id");
	$hist_exist = mysqli_affected_rows($connection);
	if ($hist_exist==1){
		$query_increment_exist= mysqli_query($connection,"UPDATE History SET age=age+1 WHERE email='$user_email' AND age < 
			(SELECT age FROM History WHERE email='$user_email' AND movie_id=$cur_movie_id)");
		$query_reset_exist= mysqli_query($connection,"UPDATE History SET age=0 WHERE email='$user_email' AND movie_id=$cur_movie_id");
		$query_increment_genre_exist=mysqli_query($connection,"UPDATE History_count SET counter=counter+1 WHERE email='$user_email' AND genre LIKE '%$cur_genre%'");
	}
	else{
		$query_increment_nonexist= mysqli_query($connection,"UPDATE History SET age=age+1 WHERE email='$user_email"'');
		$query_delete_nonexist=mysqli_query($connection,"DELETE FROM History WHERE email='$user_email' AND age >=20");
		$query_add_nonexist=mysqli_query($connection,"INSERT INTO History(email,movie_id,age) VALUES('$user_email',$curmovie_id,0)");
		$query_increment_genre_nonexist=mysqli_query($connection,"UPDATE History_count SET counter=counter+1 WHERE email='$user_email' AND genre LIKE '%$cur_genre%'");
	}

	//Update Favourite_movie and Favourite_count if user pressed Favourite
	if (/* put some condition here! */){
		$query_favourite= mysqli_query($connection,"SELECT * FROM Favourite_movie WHERE email='$user_email' AND movie_id=$cur_movie_id");
		$favourite_exist = mysqli_affected_rows($connection);
		if ($favourite_exist!=1){
			$query_favourite_insert=mysqli_query($connection,"INSERT INTO Favourite_movie(email,movie_id) VALUES ('$user_email',$cur_movie_id)");
			$query_favourite_genre=mysqli_query($connection,"UPDATE Favourite_count SET counter=counter+1 WHERE email='$user_email' AND genre LIKE '%$cur_genre%'");
		}
	}
	if (/* put some condition here! */){
		$query_favourite= mysqli_query($connection,"SELECT * FROM Favourite_movie WHERE email='$user_email' AND movie_id=$cur_movie_id");
		$favourite_exist = mysqli_affected_rows($connection);
		if ($favourite_exist==1){
			$query_favourite_delete=mysqli_query($connection,"DELETE FROM Favourite_movie WHERE movie_id=$cur_movie_id AND email='$user_email'");
			$query_favourite_genre=mysqli_query($connection,"UPDATE Favourite_count SET counter=counter-1 WHERE email='$user_email' AND genre LIKE '%$cur_genre%'");
		}
	}
?>