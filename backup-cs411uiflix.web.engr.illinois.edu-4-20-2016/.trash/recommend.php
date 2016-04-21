<?php

	//assume user email stored in $user_email
	//recommend result is sorted and stored in $movie_array(movie_id,poster,modified_score)

	//sort function for usort
	function sort($a,$b){
		return $b[2]-$a[2];
	}
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
	$History_array=array("NULL","NULL","NULL");
	$Favourite_array=array("NULL","NULL","NULL");
	$movie_array=array();
	//query for top 3 genre for both count tables
	$top3_history=mysqli_query($connection,"SELECT * FROM　History_count WHERE email='$user_email' ORDER BY counter DESC LIMIT 3");
	$top3_Favourite=mysqli_query($connection,"SELECT * FROM　Favourite_count WHERE email='$user_email' ORDER BY counter DESC LIMIT 3");
	//fetch genres into genre_array, ignore genres with 0 count
	for ($i=0;$i<3;$i++){
		$top3_history_row=$top3_history->fetch_assoc();
		if ($top3_history_row["counter"]!=0){
			$temp_genre=substr($top3_history_row["genre"],0);
			str_replace("NULL",'%$temp_genre%',$History_array[$i]);
			$count_array++;
		}
		$top3_Favourite_row=$top3_Favourite->fetch_assoc();
		if ($top3_Favourite_row["counter"]!=0){
			$temp_genre=substr($top3_Favourite_row["genre"],0);
			str_replace("NULL",'%$temp_genre%',$Favourite_array[$i]);
			$count_array++;
		}
	}
	//query for movies
	$movie_query=mysqli_query($connection,"SELECT * FROM Movies WHERE score>=8.7 OR genre LIKE '$History_array[0]' OR genre LIKE '$History_array[1]'
		 OR genre LIKE '$History_array[2]' OR genre LIKE '$Favourite_array[0]' OR genre LIKE '$Favourite_array[1]' OR genre LIKE '$Favourite_array[2]'");
	//push all qualified movie into $movie_array
	foreach ($movie_query as $row){
		$modified_score=$row["score"];
		//do some funny stuff on score
		for ($i=0;i<3;i++){
			if (strpos($row["genre"],$History_array[$i])!==false)
				$modified_score=$modified_score+0.2*$i;
			if (strpos($row["genre"],$Favourite_array[$i])!==false)
				$modified_score=$modified_score+0.3*$i;
		}
		array_push($movie_array,array($row["movie_id"],$row["poster"],$modified_score));
	}
	usort($movie_array,'sort');
?>