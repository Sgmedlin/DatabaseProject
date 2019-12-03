<?php
	// Initialize the session
	session_start();

	// Uses basic user access level for viewing adventure details
	require_once "session_config.php";

	// Query all of the adventures from the Adventure database table
	$sql="SELECT * FROM Trip";

	// Store the query in the "result" variable to iteratively list the results
	// in the HTML below
	$result = mysqli_query($link, $sql);


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
		<h1> Trips </h1>

		<!-- Table for showing the results of the query on Adventures table -->
		<table class="table table-hover table-sm table-striped">


			<!-- Header of the table -->
			<thead>
				<th scope="col">Trip Name</th>
				<th scope="col">Status</th>
				<th scope="col">Size</th>
			</thead>

			<!-- Body of the table, uses PHP to show the results of the query
			on the Adventures table by using the $result variable defined in the
			PHP section of the file above -->
			<tbody>
				<?php
					while($row = mysqli_fetch_array($result)) {
						echo "<tr>";
						echo "<td> <a class='btn btn-link' role='button' href='trip_details.php?id=" . $row['trip_id'] . "'>" . $row['name'] .  "</td>";
						echo "<td>" . $row['status'] . "</td>";
						echo "<td>" . $row['size'] . "</td>";
						echo "</tr>";
					}

					mysqli_close($con);

			 	?>
			</tbody>
		</table>


	</body>


</html>
