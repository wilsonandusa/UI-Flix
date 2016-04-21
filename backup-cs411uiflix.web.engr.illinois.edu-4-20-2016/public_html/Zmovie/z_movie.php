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
	$user_phone=$_GET["phone"];
	$favourite_string="";
	$favourite_status=0;
	$query_movie= mysqli_query($connection,"UPDATE Movies SET counter=counter+1 WHERE movie_id=$cur_movie_id");
	$query_movie= mysqli_query($connection,"SELECT * FROM Movies WHERE movie_id=$cur_movie_id");
	$query_status= mysqli_query($connection,"SELECT * FROM Favourite_movie WHERE movie_id=$cur_movie_id AND email='$user_email'");
	if ($query_status){
	if (mysqli_affected_rows($connection)==1){
		$favourite_status=1;
		$favourite_string="unfavourite";
	}
	else{
		$favourite_status=0;
		$favourite_string="favourite";
	}
	}
	if ($query_movie){
		$row=$query_movie->fetch_assoc();
		$movie_array=array($row["movie_name"],$row["movie_year"],$row["duration"],$row["score"],$row["storyline"],$row["genre"],$row["poster"]);
		$cur_genre=substr($row["genre"],0);
		$genre_string=explode(" ", $cur_genre);
		//Update History and History_count
		$query_history= mysqli_query($connection,"SELECT * FROM History WHERE email='$user_email' AND movie_id=$cur_movie_id");
		$hist_exist = mysqli_affected_rows($connection);
		if ($hist_exist==1&&($user_email!=null)){
			$row_age=mysqli_fetch_assoc($query_history);
			$cur_age=$row_age["age"];
			$query_increment_exist= mysqli_query($connection,"UPDATE History SET age=age+1 WHERE email='$user_email' AND age < $cur_age");
			$query_reset_exist= mysqli_query($connection,"UPDATE History SET age=0 WHERE email='$user_email' AND movie_id=$cur_movie_id");
			foreach($genre_string as $cur_string)
				$query_increment_genre_exist=mysqli_query($connection,"UPDATE History_count SET counter=counter+1 WHERE email='$user_email' AND genre LIKE '%$cur_string%'");
		}
		else{
			$query_increment_nonexist= mysqli_query($connection,"UPDATE History SET age=age+1 WHERE email='$user_email'");
			$query_delete_nonexist=mysqli_query($connection,"DELETE FROM History WHERE email='$user_email' AND age >=20");
			$query_add_nonexist=mysqli_query($connection,"INSERT INTO History(email,movie_id,age) VALUES('$user_email',$cur_movie_id,0)");
			foreach($genre_string as $cur_string)
				$query_increment_genre_nonexist=mysqli_query($connection,"UPDATE History_count SET counter=counter+1 WHERE email='$user_email' AND genre LIKE '%$cur_string%'");
		}
	
		//Update Favourite_movie and Favourite_count if user pressed Favourite
		if (isset($_POST['favourite_action'])&&($user_email!='')){
			if ($favourite_status==0){
				$query_favourite= mysqli_query($connection,"SELECT * FROM Favourite_movie WHERE email='$user_email' AND movie_id=$cur_movie_id");
				$favourite_exist = mysqli_affected_rows($connection);
				if ($favourite_exist!=1){
					$query_favourite_insert=mysqli_query($connection,"INSERT INTO Favourite_movie(email,movie_id) VALUES ('$user_email',$cur_movie_id)");
					foreach($genre_string as $cur_string)
						$query_increment_genre_exist=mysqli_query($connection,"UPDATE Favourite_count SET counter=counter+1 WHERE email='$user_email' AND genre LIKE '%$cur_string%'");
					$favourite_status=1;
					$favourite_string="unfavourite";
				}
				else
					echo "error! trying to favourite already favourited movie!";
			}	
			else if ($favourite_status==1){
				$query_favourite= mysqli_query($connection,"SELECT * FROM Favourite_movie WHERE email='$user_email' AND movie_id=$cur_movie_id");
				$favourite_exist = mysqli_affected_rows($connection);
				if ($favourite_exist==1){
					$query_favourite_delete=mysqli_query($connection,"DELETE FROM Favourite_movie WHERE movie_id=$cur_movie_id AND email='$user_email'");
					foreach($genre_string as $cur_string)
						$query_increment_genre_exist=mysqli_query($connection,"UPDATE Favourite_count SET counter=counter-1 WHERE email='$user_email' AND genre LIKE '%$cur_string%'");
					$favourite_status=0;
					$favourite_string="favourite";
				}
				else
					echo "error! trying to unfavourite already unfavourited movie!";
			}
		}
		if (isset($_POST['YP'])&&($user_email!=null))
		{
			 $got_one = 0;
			$info = mysqli_query($connection,"SELECT phone
			                                  FROM   User
			                                  WHERE  email='$user_email'");	                                
			$curr_phone =mysqli_fetch_assoc($info);
			$area_code = substr($curr_phone["phone"],0,3);  //notice this is a string 
			//echo "<script type='text/javascript '>alert('$area_code');</script>";	
		
		
/*			$test_query = mysqli_query($connection,"SELECT DISTINCT c1.email,c1.counter,c1.genre
									      FROM Favourite_count c1, Favourite_count c2
									      WHERE c1.email= c2.email AND c1.counter >= (SELECT MAX(counter)
                                                   				                                              FROM Favourite_count 
                                                   									      WHERE email= c1.email AND counter NOT IN (SELECT MAX(counter)  
                                                   									                                                  FROM Favourite_count 
                                                   									                                                  WHERE email= c1.email))
									      ORDER BY c1.email ASC,c1.counter DESC");
			$test_fetch=mysqli_fetch_assoc($test_query);
			 while($rowww = $test_query ->fetch_assoc())
			 {
      			 echo "<br>{$rowww["email"]}  {$rowww["genre"]}   {$rowww["counter"]}<br>" ;
  			 }
			//print_r($test_fetch);
			//echo "<script type='text/javascript '>alert('$test_fetch[Email]');</script>";//
*/		
		
			//find other people favorite the same movie
			$query_find_the_one= mysqli_query($connection,"SELECT email 
			                                               FROM   Favourite_movie 
			                                               WHERE  movie_id=$cur_movie_id AND email IN(SELECT Email
			                                              		    				  FROM User
			                                            		   				  WHERE Phone LIKE '$area_code%' AND Email <> '$user_email')");                                                            
			$yp_exist = mysqli_affected_rows($connection);
			
			if ($yp_exist !=0)
			{
			$got_one = 1;
			 while($roww = $query_find_the_one->fetch_assoc())
			 {
      				// echo "<br>{$roww["email"]} <br>" ;
      				 $mmsg = "This is an automatically generated message from UI-Flix\n
      				          {$user_username} would like to see a movie with you \n
      				           reply back at {$user_email}";
  				 mail("{$roww["email"]}","Would You Like To See Movie With Me?",$mmsg);
  				echo "<script type='text/javascript '>alert('Email sent to {$roww ["email"]} ');</script>";
  			 }
  			}
  			 else
  			 {
  			 //find other people who likes this movie's genre
  			 $query_find_maybe_the_one= mysqli_query($connection,"SELECT DISTINCT c1.email,c1.counter,c1.genre
									      FROM Favourite_count c1, Favourite_count c2
									      WHERE c1.email IN email IN(SELECT Email
			                                              		    	                 FROM User
			                                            		   		         WHERE Phone LIKE '$area_code%' AND Email <> '$user_email') 
			                                            		   		         AND  c1.email !='$user_email' AND c1.email= c2.email AND c1.counter >= (SELECT MAX(counter)
                                                   				                                                                                                  FROM Favourite_count 
                                                   									                                                          WHERE email= c1.email AND counter NOT IN (SELECT MAX(counter)  
                                                   									                       							                           FROM Favourite_count 
                                                   									                         						                         WHERE email= c1.email))
									      ORDER BY c1.email ASC,c1.counter DESC");
			 $pieces = explode(" ", strtolower($cur_genre));
							       
  			/*
  			  while($rowww = $query_find_maybe_the_one->fetch_assoc())
			 {
      			 echo "<br>{$rowww ["email"]}  {$rowww ["genre"]}   {$rowww ["counter"]}<br>" ;
  			 }
  			 */
 
  			 
  			if ($query_find_maybe_the_one)
  			 {
  			 while($rowww = $query_find_maybe_the_one->fetch_assoc())
			 {
      			   if ($rowww["genre"] == $pieces[0] or $rowww["genre"] == $pieces[1])
  			 	{
  			 	// echo "<br>{$rowww["email"]} <br>" ;
  			 	 $mmsg = "This is an automatically generated message from UI-Flix\n
      				          {$user_username} would like to see a movie with you \n
      				           reply back at {$user_email}";
  				 mail("{$rowww["email"]}","Would You Like To See Movie With Me?",$mmsg);
  				echo "<script type='text/javascript '>alert('Email sent to {$rowww["email"]} ');</script>";//
  			 	$got_one = 1;
  			 	}
  			 }
  			 }
  			 if(!$got_one)
  			 {
  			 $string = 'What kinda werido watch movie like that?! \n You ought to be lonely';
				echo "<script>alert(\"$string\")</script>";
  			//  echo "<script type='text/javascript'>alert('');</script>";
  			  }		 
  
  			 
  			 
  			 } 
  			 
  			 
		
		 }
		
	}
?>


<!DOCTYPE html>
<head>

    <!-- Basic Page Needs
  ================================================== -->
	<meta charset="utf-8">
	<title>zMovie</title>
	<meta name="description" content="Free Responsive Html5 Css3 Templates | zerotheme.com">
	<meta name="author" content="www.zerotheme.com">
	
    <!-- Mobile Specific Metas
  ================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    
    <!-- CSS
  ================================================== -->
  	<link rel="stylesheet" href="css/zerogrid.css">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/responsive.css">
    
</head>
<body>
<div class="wrap-body">

<!--////////////////////////////////////Header-->
<header>
	<div class="wrap-header zerogrid">
		<div class="row">
			<div class="col-1-2">
				<div class="wrap-col">
					<div class="logo"><a href=<?php echo "../index.php?user=".$user_username."&email=".$user_email."#"; ?>><img src="images/logo.jpg"/></a></div>	
				</div>
			</div>
			<!--
			<div class="col-1-2">
				<div class="wrap-col f-right">
					<form method="get" action="/search" id="search"  >
						  <input name="q" type="text" size="40" placeholder="Search..." />
						  <input type="submit" value="Submit">
						</form>
				</div>
			</div> -->
		</div>
	</div>
</header>


<!--////////////////////////////////////Container-->
<section id="container">
	<div class="wrap-container zerogrid">
		<div id="main-content" class="col-2-3">
			<div class="wrap-content">
				<article>
					<div class="art-header">
						<div class="col-1-3">
							<div class="wrap-col">
								<img src="<?= $movie_array[6]?>" />
							</div>
						</div>
						<div class="col-2-3">
							<div class="wrap-col">
								<ul>
									<li><p>Name: <a href="#"><?php echo $movie_array[0]?></a></p></li>
									<li><p>Year: <a href="#"><?php echo $movie_array[1]?></a></p></li>
									<li><p>Duration: <a href="#"><?php echo $movie_array[2]?> mins</a></p></li>
									<li><p>IMDB Score: <a href="#"><?php echo $movie_array[3]?></a></p></li>
									<li><p>Genre: <a href="#"><?php echo $movie_array[5]?></a></p></li>
									<form method="POST" action=''>
									<li>
									<button id="submit" name="favourite_action"><?php echo $favourite_string; ?></button>
									<button id="submit" name="YP">YP</button>
									</li>
									</form>
								</ul>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<div class="art-content">
						<p><?php echo $movie_array[4]?></p>
						
					</div>
				</article>
			</div>
		</div>
	</div>
</section>

<!--////////////////////////////////////Footer-->
<footer>
	<div class="bottom-footer">
		<div class="wrap-bottom ">
		
				<p>Copyright © 2016 Movie Page. All Rights Reserved | Designed by Zihao Zhou, Xiaosheng Wu, Xiaohao Wang</p>
			
		</div>
	</div>
</footer>


</div>

</body></html>