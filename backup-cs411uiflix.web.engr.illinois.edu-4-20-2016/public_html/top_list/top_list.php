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
 
 	
 	$result = mysqli_query( $connection, "SELECT * FROM Movies");
 	
?>

<html>
<head>
<title>Top15/500</title>
</head>

<body>
<table width="600" border="1" cellpadding="1"cellspacing="1">
<tr>
<th>Name</th>
<th>Year</th>
<th>Duration</th>
<th>Score</th>
<th>Storyline</th>

<tr>

<?php

while($movie = mysqli_fetch_assoc( $result)){


echo "<tr>";
echo"<td>".$movie['name']."</td>";
echo"<td>".$movie['year']."</td>";
echo"<td>".$movie['duration']."</td>";
echo"<td>".$movie['score']."</td>";
echo"<td>".$movie['storyline']."</td>";
echo "</tr>";
}
?>
</table>
</body>

</html>