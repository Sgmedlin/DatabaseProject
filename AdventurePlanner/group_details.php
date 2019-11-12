<?php
	
	// Start session, checks variables to see if 
	session_start();

	// Uses basic user access level for viewing adventure details
	require_once "session_config.php";

	// Retrieves the group ID from the GET information in groups.php page
	// Uses result of query to show information about the group in question
   	$sql = "SELECT * FROM Groups WHERE group_id = '".$_GET['id']."'";
   	$result = mysqli_query($link, $sql);

   	// Gets the result of the above query and stores them in the group_details variable
   	$group_details = mysqli_fetch_assoc($result);

   	// Sets variables to contain individual cells from the above query
   	$group_name = $group_details['group_name'];
   	$group_description = $group_details['description'];
   	$group_status = $group_details['status'];
   
   	mysqli_free_result($result);


   	// Query to select the owner of the group
   	$sql1 = "SELECT user_id, name FROM User_Profile NATURAL JOIN belongs_to NATURAL JOIN Groups WHERE membership_level='Owner' AND group_id=".$_GET['id'];

   	$result1 = mysqli_query($link, $sql1);

   	$group_owner_details = mysqli_fetch_assoc($result1);

   	$owner = $group_owner_details['name'];
   	$owner_id = $group_owner_details['user_id'];

   	mysqli_free_result($result1);

	// Query to select all of the members of a group
   	$sql2 = "SELECT user_id, name, membership_level FROM User_Profile NATURAL JOIN belongs_to NATURAL JOIN Groups WHERE group_id=".$_GET['id'];

   	// Store the query in the "result" variable to iteratively list the results
	// in the HTML below
   	$result2 = mysqli_query($link, $sql2);

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

		<title>Group Details</title>

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
		        <a class="nav-link" href="index.php"> Home </a>
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
		      <li class="nav-item active">
		        <a class="nav-link" href="groups.php"> Groups <span class="sr-only">(current)</span> </a>
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

		<!-- Shows the main details of a group -->
		<h1> <?php echo $group_name; ?> </h1>
		<p> Owner:<?php echo "<a class='btn btn-link' role='button' href='user_details.php?id=" . $owner_id . "'>" . $owner .  "</a>"; ?> </p>
		<p> Status: <?php echo $group_status; ?>  </p>
		<p> Description: <?php echo $group_description; ?> </p>

		<!-- Shows the members of a group -->
		<h2>Members:</h2>

		<!-- Table for showing the results of the query on group users -->
		<table class="table table-hover table-sm">

			<!-- Header of the table -->
			<thead>
				<th scope="col">Name</th>
				<th scope="col">Role</th>
			</thead>

			<!-- Body of the table, uses PHP to show the results of the query
			 by using the $result variable defined in the PHP section of the file above -->
			<tbody>
				<?php 
				
					while($row = mysqli_fetch_array($result2)) {
					echo "<tr>";
					echo "<td> <a class='btn btn-link' role='button' href='user_details.php?id=" . $row['user_id'] . "'>" . $row['name'] .  "</td>";
					echo "<td>" . $row['membership_level'] . "</td>";
					echo "</tr>";
					}
					mysqli_close($con);

			 	?>
		 	</tbody>

		</table>

	</body>

</html>