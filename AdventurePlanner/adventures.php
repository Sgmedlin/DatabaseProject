<?php
// Initialize the session
session_start();

require_once "session_config.php";

// Form the SQL query (a SELECT query)
$sql="SELECT * FROM Adventure";
$result = mysqli_query($link, $sql);
// Print the data from the table row by row

?>


<!DOCTYPE html>
<html lang="en">
<html>
	<head>

		<!-- BootStrap CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

		<!-- Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

		<title>Adventures</title>

	</head>

	<body>

		<nav class="navbar navbar-expand-lg navbar-light bg-light">
		  <a class="navbar-brand" href="#">Adventure Planner</a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		  </button>

		  <div class="collapse navbar-collapse" id="navbarSupportedContent">
		    <ul class="navbar-nav mr-auto">
		      <li class="nav-item">
		        <a class="nav-link" href="index.php"> Home </a>
		      </li>
		      <?php 
                if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
                	echo "<li class='nav-item'>
                	<a class='nav-link' href='profile.php'>Profile <span class='sr-only'>(current)</span></a>
                	</li>";
                } 
                ?>
		      <li class="nav-item active">
		        <a class="nav-link" href="adventures.php">Adventures <span class="sr-only">(current)</span></a>
		      </li>
		      <li class="nav-item">
		        <a class="nav-link" href="groups.php"> Groups </a>
		      </li>
		    </ul>
		    <?php 
                if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
                	echo "<span class='navbar-nav'>
                			<a href='logout.php' class='nav-link'> Log Out </a>
            			</span>";
                } else {
                	echo "<span class='navbar-nav'>
		    				<a href='login.php' class='nav-link'> Login </a>
		    			</span>";
                }
                ?>
		    
		    	
		  </div>
		</nav>	

		<h1> Adventures </h1>
		<table class="table table-hover table-sm">
			<thead>
				<th scope="col">Adventure Name</th>
				<th scope="col">Latitude</th>
				<th scope="col">Longitude</th>

			</thead>
		<tbody>
		<?php 
		while($row = mysqli_fetch_array($result)) {
			echo "<tr>";
			echo "<td> <a class='btn btn-link' role='button' href='adventure_details.php?id=" . $row['adventure_id'] . "'>" . $row['name'] .  "</td>";
			echo "<td>" . $row['latitude'] . "</td>";
			echo "<td>" . $row['longitude'] . "</td>";
			echo "</tr>";
			}
			mysqli_close($con);

		 ?>
		 </tbody>
		</table>


	</body>


</html>
