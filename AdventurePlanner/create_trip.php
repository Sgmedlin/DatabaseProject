<?php
    // // Initialize the session
    // session_start();

    // // Uses basic user access level for viewing adventure details
    // require_once "session_config.php";

    // // Define variables and initialize with empty values


    // // Check to see if a user is logged in. If they are not, display an error and
    // // do not let the user create a group
    // if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    //     $loggedin_err = "You must be logged in to create a group.";
    // }

    session_start();

    // $delete_group_button = ""

    // Uses basic user access level for viewing adventure details
    require_once "session_config.php";

    $trip_name = "";
    $description = $private = $group_id = $adventureID = $pickup_location = $dropoff_location = $pickup_datetime = $dropoff_datetime = $size = "";
    $tripnameerr = $description_err = $groupname_err = $adventurename_err = $pickup_err = $dropoff_err = $start_err = $end_err = $size_err = "";


    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }


        // Process the form when the "Submit" button is pressed and a POST call is made
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $trip_name = trim($_POST["tripName"]);

        // Make sure the group name is not empty
        if($trip_name == ""){
            $tripnameerr = "Please enter a name for your trip.";

        } else{

            // Prepare a SELECT statement to check that the group name
            // entered does not already exist in the database table "Groups"
            $sql = "SELECT trip_id FROM Trips WHERE trip_name=?";

            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_tripname);

                // Set groupname parameter in the "mysqli_stmt_bind_param" function
                $param_tripname = $trip_name;

                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // store result
                    mysqli_stmt_store_result($stmt);

                    // If the SELECT statement returns a result, the name is taken
                    // set the group name error variable
                    if(mysqli_stmt_num_rows($stmt) == 1){
                        $tripname_err = "This trip name is already taken.";
                    } else{
                        $trip_name = $param_tripname;
                    }
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }

            // Close the statement made above in mysqli_prepare
            mysqli_stmt_close($stmt);
        }

        // Validate the description of the group and make sure it is not empty
        if(empty(trim($_POST["description"]))){
            $description_err = "Please provide a description for your trip.";
        } else{
            $description = trim($_POST["description"]);
        }

        if(!isset($_POST['groupID'])){
            $groupname_err = "Please select a group to post the trip to.";
        } else{
            $group_id = trim($_POST["groupID"]);
        }

        if(!isset($_POST['adventureID'])){
            $adventurename_err = "Please select a group to post the trip to.";
        } else{
            $adventure_id = trim($_POST["adventureID"]);
        }

        if(empty(trim($_POST["pickuplocation"]))){
            $pickup_err = "Please specify a Pick-Up location.";
        } else{
            $pickup_location = trim($_POST["pickuplocation"]);
        }

        if(empty(trim($_POST["dropofflocation"]))){
            $dropoff_err = "Please specify a Drop-Off location.";
        } else{
            $dropoff_location = trim($_POST["dropofflocation"]);
        }

        if(empty(trim($_POST["start_datetime"]))){
            $start_err = "Please specify a Pick-Up Date/Time.";
        } else{
            $start_datetime = trim($_POST["start_datetime"]);
        }

        if(empty(trim($_POST["end_datetime"]))){
            $end_err = "Please specify a Drop-Off Date/Time.";
        } else{
            $end_datetime = trim($_POST["end_datetime"]);
        }

        if(empty(trim($_POST["tripSize"]))){
            $size_err = "Please specify the size of your trip.";
        } else{
            $size = (int)trim($_POST["tripSize"]);
        }

        // Check the result of the checkbox in the form to decide whether to make the group private or not
        if(isset($_POST['privateCheckbox']) && $_POST['privateCheckbox'] == 'YES'){
         $private = "Closed";
        }
        else{
         $private = "Open";
        }

        // If any of the errors checked have been set (i.e. the user is either not logged in, the group name is empty, or the description is empty) then don't let the user create a group.
        if(empty($tripnameerr) && empty($description_err) && empty($groupname_err) && empty($adventurename_err) && empty($pickup_err) && empty($dropoff_err)
            && empty($start_err) && empty($end_err) && empty($size_err)){

            // Prepare the SQL statement to INSERT the new group into the table
            $sql0 = "INSERT INTO Trip (group_id, pickup_location, dropoff_location, name, dropoff_datetime, pickup_datetime, description, status, size) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            if($stmt0 = mysqli_prepare($link, $sql0)){

                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt0, "isssssssi", $param_group_id, $param_pickup_location, $param_dropoff_location, $param_name, $param_dropoff_datetime, $param_pickup_datetime, $param_description, $param_status, $param_size);

                // Set parameters in the "mysqli_stmt_bind_param" function
                $param_group_id = $group_id;
                $param_pickup_location = $pickup_location;
                $param_dropoff_location = $dropoff_location;
                $param_name = $trip_name;
                $param_dropoff_datetime = $end_datetime;
                $param_pickup_datetime = $start_datetime;
                $param_description = $description;
                $param_status = $private;
                $param_size = $size;

                // Execute the statement anc insert the new group in the table
                mysqli_stmt_execute($stmt0);

                // If the insert didn't affect any rows, the group was not successfully created.
                if(mysqli_stmt_affected_rows($stmt0) < 1){
                    echo "Could not create trip";
                } else{
                    $trip_id = mysqli_insert_id($link);

                }

            }

            // Close the statement made above in mysqli_prepare
            mysqli_stmt_close($stmt0);


            // Prepare an insert statement for the belongs_to table
            // This will set the group owner to the user who created the group
            $sql2 = "INSERT INTO Attends (user_id, trip_id, leader, driver) VALUES (?, ?, ?, ?)";

            $access_level = 1;
            $driver = 1;

            if($stmt2 = mysqli_prepare($link, $sql2)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt2, "iiii", $param_user_id, $param_trip_id, $param_access_level, $param_driver);

                // Set parameters in the "mysqli_stmt_bind_param" function
                $param_user_id = $_SESSION["id"];
                $param_trip_id = $trip_id;
                $param_access_level = $access_level;
                $param_driver = $driver;

                // Attempt to execute the prepared statement
                mysqli_stmt_execute($stmt2);

            }

            // Close statement
            mysqli_stmt_close($stmt2);


            // Prepare an insert statement for the belongs_to table
            // This will set the group owner to the user who created the group
            $sql3 = "INSERT INTO trip_to (trip_id, adventure_id) VALUES (?, ?)";

            if($stmt3 = mysqli_prepare($link, $sql3)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt3, "ii", $param_trip_id, $param_adventure_id);

                // Set parameters in the "mysqli_stmt_bind_param" function
                $param_trip_id = $trip_id;
                $param_adventure_id = $adventure_id;

                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt3)){
                    // Redirect to groups page upon success
                    header("location: trips.php");
                } else{
                    echo "Something went wrong. Please try again later.";
                }
            }

            // Close statement
            mysqli_stmt_close($stmt3);

        }

        // Close connection
        mysqli_close($link);
    }

    $current_user_id = $_SESSION['id'];



    $sql="SELECT * FROM Groups WHERE group_id IN (SELECT group_id FROM belongs_to WHERE user_id=". $current_user_id .")";

    $result = mysqli_query($link, $sql);

    // Query to select all of the members of a group
    $sql2 = "SELECT * FROM Adventure";

    // Store the query in the "result" variable to iteratively list the results
    // in the HTML below
    $result2 = mysqli_query($link, $sql2);

?>

<!DOCTYPE html>




<html lang="en" style="min-height:100%; position:relative;"><head><!-- BootStrap CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

		<!-- Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

        <!-- <script src="js/jquery-3.4.1.min.js"></script> -->
        <!-- <script src="/js/jquery-3.4.1.min.js"></script> -->
        <script src="js/moment.js"></script>


        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/js/tempusdominus-bootstrap-4.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css" /><style>

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
        <title>Create Trip</title>
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
          <li class="nav-item dropdown active">
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
  <h1> Create Trip </h1>


          <span class="help-block"><?php echo $loggedin_err; ?></span>


          <!-- Form that will run the above PHP upon "submit" (POST) -->



          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="width: 100%;">

                      <div class="form-group <?php echo (!empty($tripname_err)) ? 'has-error' : ''; ?>">
                          <label>Trip Name</label>
                          <input type="text" name="tripName" class="form-control" value="<?php echo $trip_name; ?>">
                          <span class="help-block" style="color:red;text-align:center;"><?php echo $tripname_err; ?></span>
                      </div>

                      <div class="form-group <?php echo (!empty($description_err)) ? 'has-error' : ''; ?>">
                          <label>Description</label>
                          <input type="text" name="description" class="form-control" value="<?php echo $description; ?>">
                          <span class="help-block" style="color:red;text-align:center;"><?php echo $description_err; ?></span>
                      </div>

                      <div class="form-group col-md-8 <?php echo (!empty($groupname_err)) ? 'has-error' : ''; ?>">
                        <label for="adventureName">Group</label>
                        <select name="groupID" id="groupName" class="form-control">
                          <option selected>Choose...</option>
                          <?php
                              while($row = mysqli_fetch_array($result)) {
                                  echo "<option value='" . $row['group_id']  . "'>" . $row['group_name'] . "</option>";
                                  }
                                  mysqli_close($con);
                           ?>
                        </select>
                        <span class="help-block" style="color:red;text-align:center;"><?php echo $groupname_err; ?></span>
                      </div>

                      <div class="form-group col-md-8 <?php echo (!empty($adventurename_err)) ? 'has-error' : ''; ?>">
                        <label for="adventureName">Adventure</label>
                        <select name="adventureID" id="adventureName" class="form-control">
                          <option selected>Choose...</option>
                          <?php
                              while($row = mysqli_fetch_array($result2)) {
                                  echo "<option value='" . $row['adventure_id']  . "'>" . $row['name'] . "</option>";
                                  }
                                  mysqli_close($con);
                           ?>
                        </select>
                        <span class="help-block" style="color:red;text-align:center;"><?php echo $adventurename_err; ?></span>
                      </div>

                      <div class="form-group <?php echo (!empty($pickup_err)) ? 'has-error' : ''; ?>">
                          <label>Pick-Up Location</label>
                          <input type="text" name="pickuplocation" class="form-control" value="<?php echo $pickup_location; ?>">
                          <span class="help-block" style="color:red;text-align:center;"><?php echo $pickup_err; ?></span>
                      </div>

                      <div class="form-group <?php echo (!empty($dropoff_err)) ? 'has-error' : ''; ?>">
                          <label>Drop-Off Location</label>
                          <input type="text" name="dropofflocation" class="form-control" value="<?php echo $dropoff_location; ?>">
                          <span class="help-block" style="color:red;text-align:center;"><?php echo $dropoff_err; ?></span>
                      </div>

                      <div class="form-group <?php echo (!empty($start_err)) ? 'has-error' : ''; ?>">
                          <label>Pick-Up Time</label>
                         <div class="input-group date" id="datetimepicker7" data-target-input="nearest">
                              <input type="text" name="start_datetime" class="form-control datetimepicker-input" data-target="#datetimepicker7"/>
                              <div class="input-group-append" data-target="#datetimepicker7" data-toggle="datetimepicker">
                                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                              </div>
                          </div>
                          <span class="help-block" style="color:red;text-align:center;"><?php echo $start_err; ?></span>
                      </div>

                      <div class="form-group <?php echo (!empty($end_err)) ? 'has-error' : ''; ?>">
                          <label>Drop-Off Time</label>
                         <div class="input-group date" id="datetimepicker8" data-target-input="nearest">
                              <input type="text" name="end_datetime" class="form-control datetimepicker-input" data-target="#datetimepicker8"/>
                              <div class="input-group-append" data-target="#datetimepicker8" data-toggle="datetimepicker">
                                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                              </div>
                          </div>
                          <span class="help-block" style="color:red;text-align:center;"><?php echo $end_err; ?></span>
                      </div>


                      <div class="form-group col-md-4 <?php echo (!empty($size_err)) ? 'has-error' : ''; ?>">
                          <label>Max Number of Attendees</label>
                          <input type="text" name="tripSize" class="form-control" value="<?php echo $size; ?>">
                          <span class="help-block" style="color:red;text-align:center;"><?php echo $size_err; ?></span>
                      </div>

                      <div class="form-group form-check">
                          <input type="checkbox" class="form-check-input" name="privateCheckbox" value="YES" id="check">
                          <label class="form-check-label" for="check">Make Trip Private (invite only)</label>
                      </div>

              <script type="text/javascript">
                  $(function () {
                      $('#datetimepicker7').datetimepicker();
                      $('#datetimepicker8').datetimepicker({
                          useCurrent: false
                      });
                      $("#datetimepicker7").on("change.datetimepicker", function (e) {
                          $('#datetimepicker8').datetimepicker('minDate', e.date);
                      });
                      $("#datetimepicker8").on("change.datetimepicker", function (e) {
                          $('#datetimepicker7').datetimepicker('maxDate', e.date);
                      });
                  });
              </script>

                  <div class="form-group">
                      <input type="submit" class="btn btn-primary" value="Submit">
                      <input type="reset" class="btn btn-default" value="Reset">
                  </div>
          </form>

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
</body>
</html>
