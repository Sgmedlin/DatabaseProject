<?php

	// Start a new session containing user's login credentials
	session_start();

	// Uses basic user access level for viewing adventure details
	require_once "session_config.php";

	// Retrieves the adventure ID from the GET information in adventures.php page
	$sql = "SELECT * FROM Trip WHERE trip_id = '".$_GET['id']."'";
	$result = mysqli_query($link, $sql);

	// Gets the result of the above query and stores them in the adventure_details variable
	$trip_details = mysqli_fetch_assoc($result);

	// Sets variables to contain individual cells from the above query
  	$trip_pickup_location = $trip_details['pickup_location'];
   	$trip_dropoff_location = $trip_details['dropoff_location'];
		$trip_name = $trip_details['name'];
		$trip_dropoff_datetime = $trip_details['dropoff_datetime'];
		$trip_pickup_datetime = $trip_details['pickup_datetime'];
		$trip_description = $trip_details['description'];
		$trip_status = $trip_details['status'];
		$trip_size = $trip_details['size'];


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

		<title>Trips</title>

	</head>

	<body>

		<!-- NavBar must be changed in individual files -->
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
		  <a class="navbar-brand" href="#">Trip Planner</a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		  </button>

		  <div class="collapse navbar-collapse" id="navbarSupportedContent">
		    <ul class="navbar-nav mr-auto">
		      <li class="nav-item">
		        <a class="nav-link" href="index.php">Home</a>
		      </li>
		      <?php
                if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
                	echo "<li class='nav-item'>
                	<a class='nav-link' href='profile.php'>Profile <span class='sr-only'>(current)</span></a>
                	</li>";
                }
                ?>
		      <li class="nav-item">
		        <a class="nav-link" href="adventures.php">Adventures <span class="sr-only">(current)</span></a>
		      </li>
		      <li class="nav-item active">
		        <a class="nav-link" href="trips.php"> Trips <span class="sr-only">(current)</span></a>
		      </li>

			<li class="nav-item dropdown">
      				<a class="nav-link dropdown-toggle" href="groups.php" id="navbardrop" data-toggle="dropdown">
        				Groups
      				</a>
      			 	<div class="dropdown-menu">
        				<a class="dropdown-item" href="create_group.php">Create a Group</a>
        				<a class="dropdown-item" href="groups.php">List of Groups</a>
      				</div>
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

		<!-- Displays the information contained in the variables
		set in the PHP section of the file -->

		<h1> <?php echo $trip_name; ?> </h1>
		<p> Description: <?php echo $trip_description; ?> </p>
		<p> Status: <?php echo $trip_status; ?> </p>
		<p> Size: <?php echo $trip_size; ?> </p>
		<p> Pickup Location: <?php echo $trip_pickup_location; ?> </p>
		<p> Pickup Datetime: <?php echo $trip_pickup_datetime; ?> </p>
		<p> Dropoff Location: <?php echo $trip_dropoff_location; ?> </p>
		<p> Dropoff Datetime: <?php echo $trip_dropoff_datetime; ?> </p>



	</body>


</html>
