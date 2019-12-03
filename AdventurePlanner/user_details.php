<?php
	// Start a new session containing user's login credentials
	session_start();

	// Uses basic user access level for viewing adventure details
	require_once "session_config.php";

	// Retrieves the user ID from the GET information in group_details.php page
  	$sql = "SELECT * FROM User_Profile WHERE user_id = '".$_GET['id']."'";
   	$result = mysqli_query($link, $sql);

   	// Gets the result of the above query and stores them in the user_details variable
   	$user_details = mysqli_fetch_assoc($result);

   	// Sets variables to contain individual cells from the above query
   	$user_name = $user_details['name'];
   	$user_email = $user_details['email'];
   	$user_bio = $user_details['bio'];

   	mysqli_free_result($result);

   	// Like above, but get user's username from the Users table (distinct from User Profile table)
   	$sql1 = "SELECT * FROM Users WHERE id = '".$_GET['id']."'";
   	$result1 = mysqli_query($link, $sql1);

   	$user_detail = mysqli_fetch_assoc($result1);
   	$username = $user_detail['username'];

   	mysqli_free_result($result1);

	// Query to select all of the groups a user is a member of
   	$sql2 = "SELECT group_id, group_name, description FROM User_Profile NATURAL JOIN belongs_to NATURAL JOIN Groups WHERE user_id=".$_GET['id'];

   	// Store the query in the "result" variable to iteratively list the results
	// in the HTML below
   	$result2 = mysqli_query($link, $sql2);

	$sql3 = "SELECT * FROM User_Profile NATURAL JOIN Drives NATURAL JOIN Car WHERE user_id = '".$_GET['id']."'";
   	$result3 = mysqli_query($link, $sql3);
   	$driver_details = mysqli_fetch_assoc($result3);
	$driver = $driver_details['user_id'];
	$seats = $driver_details['num_seats'];

	mysqli_free_result($result3);


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

		<title>User Details</title>

	</head>

	<body>

		<!-- NavBar must be changed in individual files -->
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
		  <a class="navbar-brand" href="#">Adventure Planner</a>
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
		        <a class="nav-link" href="adventures.php">Adventures </a>
		      </li>
					<li class="nav-item dropdown">
      				<a class="nav-link dropdown-toggle" href="groups.php" id="navbardrop" data-toggle="dropdown">
        				Trips
      				</a>
      			 	<div class="dropdown-menu">
                		<a class="dropdown-item" href="trips.php">All Trips</a>
        				<a class="dropdown-item" href="create_trip.php">Create a Trip</a>
      				</div>
   				</li>

			<li class="nav-item dropdown">
      				<a class="nav-link dropdown-toggle" href="groups.php" id="navbardrop" data-toggle="dropdown">
        				Groups
      				</a>
      			 	<div class="dropdown-menu">
      			 		<?php
			                if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
			                	echo "<a class='dropdown-item' href='groups.php?user_id=" . $_SESSION["id"] . "'> My Groups </a>";
			                }
                		?>
                		<a class="dropdown-item" href="groups.php">All Groups</a>
        				<a class="dropdown-item" href="create_group.php">Create a Group</a>
        				
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

		<!-- Show basic information about a user -->
		<h1> <?php echo $user_name; ?> </h1>
		<p> Username: <?php echo $username; ?> </p>
		<p> Email: <?php echo $user_email; ?>  </p>
		<p> Bio: <?php echo $user_bio; ?> </p>
		<p> Drives: <?php if($driver) {echo "Yes";} else {echo "No";} ?> </p>
		<p> Number of Seats Available: <?php if($driver) {echo $seats;} else {echo "N/A";} ?> </p>
		<h2><?php echo $user_name; ?>'s Groups:</h2>

		<!-- Table for showing the groups a user is a member of -->
		<table class="table table-hover table-sm table-striped">

			<!-- Header of the table -->
			<thead>
				<th scope="col">Group Name</th>
				<th scope="col">Group Description</th>

			</thead>

			<!-- Body of the table, uses PHP to show the results of the query
			 by using the $result variable defined in the PHP section of the file above -->
			<tbody>
				<?php

					while($row = mysqli_fetch_array($result2)) {
						echo "<tr>";
						echo "<td> <a class='btn btn-link' role='button' href='group_details.php?id=" . $row['group_id'] . "'>" . $row['group_name'] .  "</td>";
						echo "<td>" . $row['description'] . "</td>";
						echo "</tr>";
						}
						mysqli_close($con);

				 ?>
			 </tbody>
		</table>

	</body>

</html>
