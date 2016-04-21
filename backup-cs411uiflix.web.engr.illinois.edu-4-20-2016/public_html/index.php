<?php

	//assume user email stored in $user_email
	//recommend result is sorted and stored in $movie_array(movie_id,poster,modified_score)
	
	//sort function for usort
	function custom_sort($a,$b){
		if ($a[2]==$b[2]) return 0;
		return ($b[2]<$a[2])?-1:1;
	}
	$user_status=0;
	$user_username=$_GET["user"];
	$user_email=$_GET["email"];
	if ($user_email!=null)
		$user_status=1;
	// require('dbconnect.php');
	$server_name="engr-cpanel-mysql.engr.illinois.edu";
	$user_name="cs411uif_wxh";
	$dbpassword="12345";
	$database_name="cs411uif_database";
	$connection = mysqli_connect($server_name,$user_name, $dbpassword);
	$errors = array();
	if (!$connection){
		echo "database connection error";
	    die("Database Connection Failed" . mysqli_connect_error());
	}
	$select_db = mysqli_select_db($connection,"cs411uif_database");
	if (!$select_db){
		echo "database connection error";
	    die("Database Selection Failed???????????" . mysqli_error($connection));
	}
	$History_array=array("","","","","");
	$Favourite_array=array("","","","","");
	$rand_genre_array=array("animation","action","adventure","biography","comedy","crime","drama","family","fantasy","film-noir","history",
		"horror","music","mystery","romance","sci-fi","sport","thriller","war","western");
	$movie_array=array();
	$genre_array=array();
	$row_1=array();
	$row_2=array();
	$row_3=array();
	$row_4=array();
	$row_5=array();
	$row_6=array();
	$query_genre=array();
	$genre_output_array=array();
	$shuffle_array=range(0,19);
	$user_history=array();
	shuffle($shuffle_array);
	//query for top 5 genre for both count tables and recent history
	$top5_history=mysqli_query($connection,"SELECT * FROM History_count WHERE email='$user_email' ORDER BY counter DESC LIMIT 5");
	$top5_Favourite=mysqli_query($connection,"SELECT * FROM Favourite_count WHERE email='$user_email' ORDER BY counter DESC LIMIT 5");
	$recent_history=mysqli_query($connection,"SELECT * FROM History WHERE email='$user_email'");
	//push recent history into $user_history
	if ($recent_history){
		foreach($recent_history as $recent_history_row){
			array_push($user_history,array($recent_history_row["movie_id"],20-$recent_history_row["age"]));
		}
	}
	//push 5 random genres to the genre array
	$i=0;
	//get top5s for history and favourite
	if ($top5_history){
		foreach($top5_history as $top5_history_row){
			if ($top5_history_row["counter"]!=0){
				$temp_genre=substr($top5_history_row["genre"],0);
				$History_array[$i]=$temp_genre;
				array_push($query_genre,$temp_genre);
			}
			$i++;
		}
	}
	else
		echo "Error history query failure!";
	$i=0;
	if ($top5_Favourite){
		foreach($top5_Favourite as $top5_Favourite_row){
			if ($top5_Favourite_row["counter"]!=0){
				$temp_genre=substr($top5_Favourite_row["genre"],0);
				$Favourite_array[$i]=$temp_genre;
				array_push($query_genre,$temp_genre);
			}
			$i++;
		}
	}
	else
		echo "Error favourite query failure!";
	for ($i=0;$i<5;$i++){
		if ($Favourite_array[$i]=='')
			$Favourite_array[$i].="NULL";
		if ($History_array[$i]=='')
			$History_array[$i].="NULL";
	}
	if ($user_status==1){
		//push all genres to $genre_array
		for ($i=0;$i<5;$i++){
			if (strpos(strtolower($Favourite_array[$i]),strtolower("NULL"))===FALSE)
				array_push($genre_array,$Favourite_array[$i]);
			if (strpos(strtolower($History_array[$i]),strtolower("NULL"))===FALSE)
				array_push($genre_array,$History_array[$i]);
		}
		$genre_array=array_unique($genre_array);
		$num_elem=count($genre_array);
		if ($num_elem==1&&$genre_array[0]==''){
			$num_elem=0;
			$genre_array=array();
		}
		$count_rand=0;
		while ($num_elem<5){
			if (in_array($rand_genre_array[$shuffle_array[$count_rand]], $genre_array)==FALSE){
				array_push($genre_array,$rand_genre_array[$shuffle_array[$count_rand]]);
				array_push($query_genre,$rand_genre_array[$shuffle_array[$count_rand]]);
				$num_elem++;
			}
			$count_rand++;
		}
		shuffle($genre_array);
		$genre_array=array_unique($genre_array);
		$genre_output_array=array_slice($genre_array,0,5,true);
		$num_elem=count($genre_output_array);
		while ($num_elem<6){
			if (in_array($rand_genre_array[$shuffle_array[$count_rand]], $genre_output_array)==FALSE){
				array_push($genre_output_array,$rand_genre_array[$shuffle_array[$count_rand]]);
				array_push($query_genre,$rand_genre_array[$shuffle_array[$count_rand]]);
				$num_elem++;
			}
			$count_rand++;
		}
		shuffle($genre_output_array);
	}
	else{
		for ($i=0;$i<6;$i++){
			array_push($genre_output_array,$rand_genre_array[$shuffle_array[$i]]);
			array_push($query_genre,$rand_genre_array[$shuffle_array[$i]]);
		}
	}
	//get the query array for movie

	//create query for movies
	$query="SELECT * FROM Movies WHERE score>8.5 ";
	foreach ($query_genre as $row)
		$query.="OR genre LIKE '%".$row."%' ";
	$movie_query=mysqli_query($connection,$query);
	//push all qualified movie into $movie_array
	$max_count_query=mysqli_query($connection,"SELECT MAX(counter) AS counter FROM Movies");
	$max_count_row=mysqli_fetch_array($max_count_query);
	$max_count=$max_count_row["counter"];
	$count=0;
	while($row = mysqli_fetch_assoc($movie_query)){
		$modified_score=floatval($row["score"]);
		//do some funny stuff on score=
		if ($user_status==1){
			for ($i=0;$i<5;$i++){
				if (strpos(strtolower($row["genre"]),strtolower($History_array[$i]))!==FALSE)
					$modified_score+=(2.0+0.5*(5-$i));
				if (strpos(strtolower($row["genre"]),strtolower($Favourite_array[$i]))!==FALSE)
					$modified_score+=(3.0+0.5*(5-$i));
			}
			foreach ($user_history as $user_history_row) {
				if ($user_history_row[0]==$row["movie_id"])
					$modified_score-=$user_history_row[1]/4;
					$modified_score-=6;
			}
			$modified_score+=$row["counter"]/$max_count*10;
		}
		array_push($movie_array,array($row["movie_id"],$row["poster"],$modified_score));

	}
	$movie_query_1=mysqli_query($connection,"SELECT * FROM Movies WHERE genre LIKE '%$genre_output_array[0]%' ORDER BY score DESC LIMIT 20");
	while($row = mysqli_fetch_assoc($movie_query_1))
		array_push($row_1,array($row["movie_id"],$row["poster"],$row["score"]));
	$movie_query_2=mysqli_query($connection,"SELECT * FROM Movies WHERE genre LIKE '%$genre_output_array[1]%' ORDER BY score DESC LIMIT 20");
	while($row = mysqli_fetch_assoc($movie_query_2))
		array_push($row_2,array($row["movie_id"],$row["poster"],$row["score"]));
	$movie_query_3=mysqli_query($connection,"SELECT * FROM Movies WHERE genre LIKE '%$genre_output_array[2]%' ORDER BY score DESC LIMIT 20");
	while($row = mysqli_fetch_assoc($movie_query_3))
		array_push($row_3,array($row["movie_id"],$row["poster"],$row["score"]));
	$movie_query_4=mysqli_query($connection,"SELECT * FROM Movies WHERE genre LIKE '%$genre_output_array[3]%' ORDER BY score DESC LIMIT 20");
	while($row = mysqli_fetch_assoc($movie_query_4))
		array_push($row_4,array($row["movie_id"],$row["poster"],$row["score"]));
	$movie_query_5=mysqli_query($connection,"SELECT * FROM Movies WHERE genre LIKE '%$genre_output_array[4]%' ORDER BY score DESC LIMIT 20");
	while($row = mysqli_fetch_assoc($movie_query_5))
		array_push($row_5,array($row["movie_id"],$row["poster"],$row["score"]));
	$movie_query_6=mysqli_query($connection,"SELECT * FROM Movies WHERE genre LIKE '%$genre_output_array[5]%' ORDER BY score DESC LIMIT 20");
	while($row = mysqli_fetch_assoc($movie_query_6))
		array_push($row_6,array($row["movie_id"],$row["poster"],$row["score"]));
	usort($movie_array,'custom_sort');
	shuffle($row_1);
	shuffle($row_2);
	shuffle($row_3);
	shuffle($row_4);
	shuffle($row_5);
	shuffle($row_6);
	for ($i=0;$i<6;$i++){
		$genre_output_array[$i]=ucfirst($genre_output_array[$i]);
	}
	
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<!-- Latest compiled and minified CSS -->

	
	<title>UI-Flix</title>
	



	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
	
	
	
	<!--[if IE 6]>
		<link rel="stylesheet" href="css/ie6.css" type="text/css" media="all" />
	<![endif]-->
	<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-func.js"></script>
	
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	<script type="text/javascript">
	function Slider(){
	}
	</script>
</head>
<body>
<!-- Shell -->
<div id="shell">
	<!-- Header -->
	<div id="header">
		<h1 id="logo"><a href="#">UI-Flix</a></h1>
		<div class="social">
			
		</div>
		
		<!-- Navigation -->
		<div id="navigation">
		<ul>
		<li>
		 <a  href="#">HOME</a>
		</li>
		<li>
		 <a  href="#">HOME</a>
		</li>
		<li>
		<a  href="#">HOME</a>
		
		</li>
		
		<li id='hide1'>
		 <a  href="#">HOME</a>
		</li>
		<li id='hide2'>
		 <a  href="#">HOME</a>
		</li>
		<li id='hide3'>
		  <a href="/Log/login2.php"><font color="red">Log In</font></a>
		</li>
		</div>
		</ul>
		
		<!-- end Navigation -->
		
		
		
		<!-- login panel -->
		<div id="loginpanel">
		
		<p id="curr_user"></p>
		</div>
		<!-- end  login panel  -->
		
		
		
		
		
		
		
		<!-- Sub-menu -->
		<div id="sub-navigation">
			<ul>
			    <li><a href="#">SHOW ALL</a></li>
			    <li><a href="#">LATEST TRAILERS</a></li>
			    <li><a href="../top_list/top_list.php">TOP15/500</a></li>
			    <li><a href="#">MOST COMMENTED</a></li>
			</ul>
			<div id="search">
				<form action=<?php echo "/search/search.php?user=".$user_username."&email=".$user_email."#"; ?> method="POST" accept-charset="utf-8">
					<label for="search-field">SEARCH</label>					
					<input type="text" name="search-field"  id="search-field" placeholder="Enter search here" class="blink search-field"  />
						<input type="submit" value="GO!" class="search-button" />
						
							<input type="hidden" name="username" value=$user_username />
  									<input type="hidden" name="email" value=$user_email />

				</form>
			</div>
		</div>
		<!-- end Sub-Menu -->
		
	</div>
	<!-- end Header -->
	
	<!-- Main -->
	<div id="main">
		<!-- Content -->
		<div id="content">
					<!-- Box -->
			<div class="box">
				<div class="head">
					<h2><font size="3"><center>Recommend</center> </font></h2>
					<p class="text-right"><a href="#">See all</a></p>
				</div>

				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$movie_array[0][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $movie_array[0][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$movie_array[1][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $movie_array[1][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$movie_array[2][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $movie_array[2][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$movie_array[3][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $movie_array[3][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$movie_array[4][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $movie_array[4][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie last">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$movie_array[5][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $movie_array[5][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
								<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$movie_array[6][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $movie_array[6][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$movie_array[7][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $movie_array[7][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$movie_array[8][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $movie_array[8][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$movie_array[9][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $movie_array[9][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$movie_array[10][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $movie_array[10][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie last">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$movie_array[11][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $movie_array[11][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				<div class="cl">&nbsp;</div>
			</div>
			<!-- end Box -->

			<!-- Box -->
			<div class="box">
				<div class="head">
					<h2><font size="3"><center> <?php echo $genre_output_array[0]; ?> </center> </font></h2>
					<p class="text-right"><a href="#">See all</a></p>
				</div>

				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_1[0][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_1[0][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_1[1][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_1[1][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_1[2][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_1[2][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_1[3][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_1[3][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_1[4][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_1[4][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie last">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_1[5][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_1[5][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				<div class="cl">&nbsp;</div>
			</div>
			<!-- end Box -->
						<!-- Box -->
			<div class="box">
				<div class="head">
					<h2><font size="3"><center> <?php echo $genre_output_array[1]; ?> </center> </font></h2>
					<p class="text-right"><a href="#">See all</a></p>
				</div>

				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_2[0][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_2[0][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_2[1][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_2[1][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_2[2][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_2[2][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_2[3][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_2[3][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_2[4][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_2[4][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie last">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_2[5][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_2[5][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				<div class="cl">&nbsp;</div>
			</div>
			<!-- end Box -->
						<!-- Box -->
			<div class="box">
				<div class="head">
					<h2><font size="3"><center> <?php echo $genre_output_array[2]; ?> </center> </font></h2>
					<p class="text-right"><a href="#">See all</a></p>
				</div>

				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_3[0][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_3[0][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_3[1][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_3[1][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_3[2][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_3[2][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_3[3][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_3[3][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_3[4][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_3[4][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie last">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_3[5][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_3[5][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				<div class="cl">&nbsp;</div>
			</div>
			<!-- end Box -->
						<!-- Box -->
			<div class="box">
				<div class="head">
					<h2><font size="3"><center> <?php echo $genre_output_array[3]; ?> </center> </font></h2>
					<p class="text-right"><a href="#">See all</a></p>
				</div>

				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_4[0][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_4[0][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_4[1][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_4[1][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_4[2][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_4[2][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_4[3][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_4[3][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_4[4][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_4[4][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie last">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_4[5][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_4[5][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				<div class="cl">&nbsp;</div>
			</div>
			<!-- end Box -->
						<!-- Box -->
			<div class="box">
				<div class="head">
					<h2><font size="3"><center> <?php echo $genre_output_array[4]; ?> </center> </font></h2>
					<p class="text-right"><a href="#">See all</a></p>
				</div>

				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_5[0][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_5[0][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_5[1][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_5[1][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_5[2][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_5[2][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_5[3][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_5[3][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_5[4][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_5[4][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie last">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_5[5][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_5[5][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				<div class="cl">&nbsp;</div>
			</div>
			<!-- end Box -->
						<!-- Box -->
			<div class="box">
				<div class="head">
					<h2><font size="3"><center> <?php echo $genre_output_array[5]; ?> </center> </font></h2>
					<p class="text-right"><a href="#">See all</a></p>
				</div>

				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_6[0][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_6[0][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_6[1][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_6[1][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_6[2][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_6[2][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_6[3][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_6[3][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_6[4][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_6[4][1]?>" alt="movie" /></a>
					</div>
				</div>
				<!-- end Movie -->
				
				<!-- Movie -->
				<div class="movie last">
					<div class="movie-image">
						<a href=<?php echo "/Zmovie/z_movie.php?user=".$user_username."&email=".$user_email."&movie=".$row_6[5][0]."#"; ?>><span class="play"><span class="name"></span></span><img src="<?= $row_6[5][1]?>" alt="movie" /></a>
					</div>	
				</div>
				<!-- end Movie -->
				<div class="cl">&nbsp;</div>
			</div>
			<!-- end Box -->
			
			
			
		</div>
		<!-- end Content -->

		<div class="cl">&nbsp;</div>
	</div>
	<!-- end Main -->

	<!-- Footer -->
	<div id="footer">
		<p> &copy; 2016 UI-Flix. All Rights Reserved.  Designed by Xiaosheng Wu, Xiaohao Wang, Zihao Zhou <a href="http://chocotemplates.com" target="_blank" title="The Sweetest CSS Templates WorldWide"></a></p>
	</div>
	<!-- end Footer -->
</div>
<!-- end Shell -->
</body>
</html>




<script>



//When user login, change curr_user and user_login
var curr_user = <?php echo json_encode($user_username); ?>;
var user_exist=<?php echo $user_status; ?>;


document.getElementById("loginpanel").style.display = "none";
document.getElementById("loginpanel").style.pointerEvents = "none";


if(user_exist==0)
{
//document.getElementById("hide1").style.visibility= "hidden";
//document.getElementById("hide2").style.visibility= "hidden";
//document.getElementById("hide3").style.visibility= "hidden";
document.getElementById("logout").style.visibility= "hidden";
}
else
{
document.getElementById("loginpanel").style.display = "block";
document.getElementById("loginpanel").style.pointerEvents = "auto";
document.getElementById("curr_user").innerHTML = "Welcome back," +"        <font color='red'> "+curr_user+"" + " !</font>" + "   <a href='http://cs411uiflix.web.engr.illinois.edu/'>Log Out</a>" ;
document.getElementById("hide1").style.visibility= "hidden";
document.getElementById("hide2").style.visibility= "hidden";
document.getElementById("hide3").style.visibility= "hidden";
}





</script>