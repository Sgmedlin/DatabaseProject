<?php
	// Start session, checks variables to see if
	session_start();
	// $delete_group_button = ""
	// Uses basic user access level for viewing adventure details
	require_once "session_config.php";
	$profile_error = $profile_btn = "";
	$param_id = $_SESSION["id"];
    $query = "SELECT * FROM User_Profile WHERE user_id=$param_id LIMIT 1";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $rows = mysqli_num_rows($result);
    if ($rows > 0){
       $profile_btn = "<input type='submit' id ='button' name='submit-button' class='btn btn-primary' value='Join this group'>";
    }
    else{
       $profile_error = "<p> Some features will not be available until you create your user profile. To create your profile, please click <a href='profile.php'>here</a>. </p>";
       $profile_btn = "<input type='submit' class='btn btn-primary' value='Join this group' disabled>";
    }
	mysqli_free_result($result);

	$group_id = $_GET['id'];
	$_SESSION['group_id'] = $_GET['id'];
	// Retrieves the group ID from the GET information in groups.php page
	// Uses result of query to show information about the group in question
   	$sql = "SELECT * FROM Groups WHERE group_id = '".$group_id."'";
   	$result = mysqli_query($link, $sql);

   	// Gets the result of the above query and stores them in the group_details variable
   	$group_details = mysqli_fetch_assoc($result);
   	// Sets variables to contain individual cells from the above query
   	$group_name = $group_details['group_name'];
   	$group_description = $group_details['description'];
   	$group_status = $group_details['status'];
   	mysqli_free_result($result);
   	if(isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == true){
   		$current_user_id = $_SESSION["id"];
    }
    else{
    	$current_user_id = 123456;
    }
   	// Query to select the owner of the group
   	$sql1 = "SELECT user_id, name FROM User_Profile NATURAL JOIN belongs_to NATURAL JOIN Groups WHERE membership_level='Owner' AND group_id=".$_GET['id'];
   	$result1 = mysqli_query($link, $sql1);
   	$group_owner_details = mysqli_fetch_assoc($result1);
   	$owner = $group_owner_details['name'];
   	$owner_id = $group_owner_details['user_id'];
   	// if ($owner_id == $current_user_id){
   	// 	echo "You are the owner";
   	// }
   	mysqli_free_result($result1);
	$sql2 = "SELECT `gear_id`, `name`, `type`, `status`, `brand`, `condition` FROM Gear NATURAL JOIN Has WHERE group_id = ".$_GET['id'];
   	$gear_result = mysqli_query($link, $sql2);
	// Query to select all of the members of a group
   	$sql3 = "SELECT user_id, name, membership_level FROM User_Profile NATURAL JOIN belongs_to NATURAL JOIN Groups WHERE group_id=".$_GET['id'];
   	// Store the query in the "result" variable to iteratively list the results
	// in the HTML below
   	$result3 = mysqli_query($link, $sql3);
	   if($_SERVER["REQUEST_METHOD"] == "POST"){
		$sqlx = "INSERT INTO belongs_to VALUES (?, ?, ?)";
        if($stmtx = mysqli_prepare($link, $sqlx)){
            mysqli_stmt_bind_param($stmtx, "iis", $param_user_id, $param_group_id, $param_membership);
			$param_user_id = $_SESSION["id"];
			$param_group_id = $_GET['id'];
			$param_membership = "Member";
            if(mysqli_stmt_execute($stmtx)){
                echo "success";
            } else{
                printf($stmtx->error);
            }
                // If the insert didn't affect any rows, the group was not successfully created.
            mysqli_stmt_close($stmtx);
		}

		$checkQuery = '';
		$date = date('Y-m-d H:i:s');
		//print($date);
		$name = array();
		$user_id = $_SESSION['id'];
		$group_id = $_GET['id'];
		$sql4 = "SELECT `gear_id`, `name`, `type`, `status`, `brand`, `condition` FROM Gear NATURAL JOIN Has WHERE group_id = ".$_GET['id'];
   		$gear_r = mysqli_query($link, $sql4);
		while($r = mysqli_fetch_array($gear_r)) {
			array_push($name, $r['gear_id']);
		}
		if (isset($_POST['submit-btn'])){
			if (!empty($name)){
				for($count = 0; $count < count($name); $count++){
					//$isDefined = false;
					if(isset($_POST["privateCheckbox$count"])){
						//$isDefined = true;
						$checkQuery .= '
                        	INSERT INTO checks_out VALUES("'.$user_id.'", "'.$name[$count].'", "'.$group_id.'", "'.$date.'", NULL);
                    	';
					};
				}
				//echo "checkquery: " . $checkQuery . " .";
				if ($checkQuery != ''){
                    if (mysqli_multi_query($link, $checkQuery)){
						//unset($_SESSION['array_name']);
						//print("check: " . $checkQuery);

                    }
                    else{
						//echo "reporting";
						echo "error";
                    }
                }
                else{
                    echo 'All Fields are Required';
                }
			}
		}
		else{
			echo "not pressed yet!";
		}
	}
?>


<!DOCTYPE html>

<html lang="en" style="min-height:100%; position:relative;"><head>
        <meta charset="UTF-8">
				<link rel="stylesheet" href="css/style.css">

        <!-- BootStrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <!-- Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
 <style>

input[type="text"],
select.form-control {
  background: transparent;
	border: 1px solid #BEBEBEBE;
  -webkit-box-shadow: none;
  box-shadow: none;
  border-radius: 50px;
}

input[type="text"]:focus,
select.form-control:focus {
  -webkit-box-shadow: none;
  box-shadow: none;
}
    </style>
        <title>Group Details</title>
    </head>

    <body style="background-color:#e1e2e1;">

       <!-- NavBar must be changed in individual files -->
		<nav style="background-color:#7da453" class="navbar navbar-expand-lg navbar-dark ">
		  <a style="" href="#" class="navbar-brand">Hiking Buddies</a>
		  <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" style="opacity:1;">
		    <span class="navbar-toggler-icon" style="opacity:1;"></span>
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

			<li class="nav-item dropdown active">
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
    <div class="container" style="padding:25px 25px 145px 25px;">
			<div class="card" style="padding: 25px; width: 90%; margin:auto; border-radius:25px; background-color:#f5f5f6;">

				<!-- Shows the main details of a group -->
						<?php
					        if($owner_id == $current_user_id){
					            echo "<h1>" . $group_name . " (Your Group)" . "</h1> <a href='delete_group.php?id=" . $group_id . " class='nav-link'> Delete Group </a>";
					        } else {
					            echo "<h1>" . $group_name . "</h1>";
					        }
					    ?>

						<p> Owner:<?php echo "<a class='btn btn-link' role='button' href='user_details.php?id=" . $owner_id . "'>" . $owner .  "</a>"; ?> </p>
						<p> Status: <?php echo $group_status; ?>  </p>
						<p> Description: <?php echo $group_description; ?> </p>

						<p> Gear : The available gears are: </p>
						<div class="table-responsive">
						<table class="table table-bordered" id="crud_table">
							<thead>
								<th scope="col">Name</th>
								<th scope="col">Type</th>
								<th scope="col">Status</th>
								<th scope="col">Brand</th>
								<th scope="col">Condition</th>
							</thead>

							<tbody>
								<?php
								$name = array();
								while($row = mysqli_fetch_array($gear_result)) {
									array_push($name, $row['name']);
									echo "<tr>";
									echo "<td>" . $row['name'] . "</td>";
									echo "<td>" . $row['type'] . "</td>";
									echo "<td>" . $row['status'] . "</td>";
									echo "<td>" . $row['brand'] . "</td>";
									echo "<td>" . $row['condition'] . "</td>";
									echo "</tr>";
								}
								?>
							</tbody>
						</table>
						</div>
							<!-- <form method="post"> -->
							<?php echo $profile_btn; ?>
							<?php echo $profile_error; ?>
							<!-- </form> -->
						</div>

						<div class="bg-modal">
							<div class="modal-content">
								<div class="modal-close">+</div>
								<h1>Gear List</h1>
								<form method="post">
								<ul id="list" class="list-group overflow-auto">
									<?php
										for($i = 0; $i < count($name); $i++){
											echo "<li>
												<div class=\"form-check\">
												<div class=\"form-group form-check\">
													<input type=\"checkbox\" class=\"form-check-input\" name=\"privateCheckbox$i\" value=\"YES\" id=\"gear$i\">
													<label class=\"form-check-label\" for=\"gear$i\">$name[$i]</label>
												</div>
												</li>";
											}
										?>
								</ul>
									<div class="submit">
										<input type="submit" id="submit-btn" name="submit-btn" class="btn btn-primary" value="submit">
									</div>
								</form>
							</div>
						</div>

						<!-- export data -->
						<form action="data.php" method="post">
							<button type="submit" id="export-btn" name="export-btn" class="btn btn-success">export Gear data</button>
						</form>
						<!-- <form action="members.php" method="post">
							<button type="submit" id="member-list-btn" name="member-list-btn" class="btn btn-success">export Members data</button>
						</form> -->

						<!-- Shows the members of a group -->
						<h2>Members:</h2>

						<!-- Table for showing the results of the query on group users -->
						<table class="table table-hover table-sm">

							<!-- Header of the table -->
							<thead>
								<th scope="col">Name</th>
								<th scope="col">Role</th>
								<th scope="col">Gear</th>
							</thead>

							<!-- Body of the table, uses PHP to show the results of the query
							 by using the $result variable defined in the PHP section of the file above -->
							<tbody>
								<?php
									while($row = mysqli_fetch_array($result3)) {
									echo "<tr>";
									echo "<td> <a class='btn btn-link' role='button' href='user_details.php?id=" . $row['user_id'] . "'>" . $row['name'] .  "</td>";
									echo "<td>" . $row['membership_level'] . "</td>";
									$sql4 = "SELECT `name`, `user_id` FROM checks_out NATURAL JOIN Gear WHERE user_id=".$row['user_id'] ." AND group_id=".$_GET['id'];
									$result4 = mysqli_query($link, $sql4);
									$value = '';
									while ($row4 = mysqli_fetch_array($result4)){
										$value .= $row4['name'] . " , ";
									}
									echo "<td>" . $value . "</td>";
									echo "<td>" . "</td>";
									echo "</tr>";
									}
									//mysqli_close($con);
							 	?>
						 	</tbody>

						</table>

					<script>
						document.getElementById('button').addEventListener('click', function(){
							document.querySelector('.bg-modal').style.display = 'flex';
						});
						document.querySelector('.modal-close').addEventListener('click', function(){
							document.querySelector('.bg-modal').style.display = 'none';
						});
					</script>

      </div>
		</div>


<footer class="footer navbar-static-bottom font-small blue pt-4" style="position:absolute; bottom: 0; width: 100%;">
		<nav class="navbar navbar-light" style="background-color: #f5f5f6;">
			<div class="container">
				<div class="row w-100 pagination-centered" style="padding-top:15px">
					<a href="https://forms.gle/wBXK6W9BE3W1y4ro6" target="_blank" class="btn btn-secondary">Contact us!</a>
				</div>
				<div class="row w-100 pagination-centered" style="padding-top:5px">
					<p class="text">© 2019 Copyright: CS 4750</p>
				</div>
			</div>
		</nav>
		</footer>

</body></html>
