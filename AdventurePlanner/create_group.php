<?php
    // Initialize the session
    session_start();

    // Uses basic user access level for viewing adventure details
    require_once "session_config.php";

    // Define variables and initialize with empty values
    $groupname = $description = $private = "";
    $groupname_err = $loggedin_err = $description_err = $gear_err = "";
    $group_id = 0;

    // Check to see if a user is logged in. If they are not, display an error and
    // do not let the user create a group
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        $loggedin_err = "You must be logged in to create a group.";
    }

    // Process the form when the "Submit" button is pressed and a POST call is made
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        // Make sure the group name is not empty
        if(empty(trim($_POST["groupname"]))){
            $groupname_err = "Please enter a name for your group.";

        } else{

            // Prepare a SELECT statement to check that the group name
            // entered does not already exist in the database table "Groups"
            $sql = "SELECT group_id FROM Groups WHERE group_name=?";

            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_groupname);

                // Set groupname parameter in the "mysqli_stmt_bind_param" function
                $param_groupname = trim($_POST["groupname"]);

                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // store result
                    mysqli_stmt_store_result($stmt);

                    // If the SELECT statement returns a result, the name is taken
                    // set the group name error variable
                    if(mysqli_stmt_num_rows($stmt) == 1){
                        $groupname_err = "This group name is already taken.";
                    } else{
                        $groupname = trim($_POST["groupname"]);
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
            $description_err = "Please provide a description for your group.";
        } else{
            $description = trim($_POST["description"]);
        }

        if(empty(trim($_POST["gear_name"]))){
            $gear_err = "Please provide a gear name";     
        } else{
            $gear_id = trim($_POST["gear_name"]);
        }

        if(empty(trim($_POST["gear_type"]))){
            $gear_err = "Please provide a gear type";     
        } else{
            $gear_type = trim($_POST["gear_type"]);
        }

        if(empty(trim($_POST["gear_status"]))){
            $gear_err = "Please provide a gear status.";     
        } else{
            $gear_status = trim($_POST["gear_status"]);
        }

        if(empty(trim($_POST["gear_brand"]))){
            $gear_err = "Please provide a gear brand";     
        } else{
            $gear_brand = trim($_POST["gear_brand"]);
        }

        if(empty(trim($_POST["gear_condition"]))){
            $gear_err = "Please provide a condition";     
        } else{
            $gear_condition = trim($_POST["gear_condition"]);
        }  

        // Check the result of the checkbox in the form to decide whether to make the group private or not
        if(isset($_POST['privateCheckbox']) && $_POST['privateCheckbox'] == 'YES'){
        	$private = "Closed";
        }
        else{
        	$private = "Open";
        }

        // If any of the errors checked have been set (i.e. the user is either not logged in, the group name is empty, or the description is empty) then don't let the user create a group.
        if(empty($groupname_err) && empty($description_err) && empty($loggedin_err)){

            // Prepare the SQL statement to INSERT the new group into the table
            $sql0 = "INSERT INTO Groups (group_name, description, status) VALUES (?, ?, ?)";

            if($stmt0 = mysqli_prepare($link, $sql0)){

                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt0, "sss", $param_groupname, $param_description, $param_status);

                // Set parameters in the "mysqli_stmt_bind_param" function
                $param_groupname = $groupname;
                $param_description = $description;
                $param_status = $private;

                // Execute the statement anc insert the new group in the table
                mysqli_stmt_execute($stmt0);

                // If the insert didn't affect any rows, the group was not successfully created.
                if(mysqli_stmt_affected_rows($stmt0) < 1){
                    echo "Could not create group";
                } else{
                    $group_id = mysqli_insert_id($link);
                    printf($stmt0->error);
                }
                // Close the statement made above in mysqli_prepare
                mysqli_stmt_close($stmt0);
            }


            // Prepare an insert statement for the belongs_to table
            // This will set the group owner to the user who created the group
            $sql2 = "INSERT INTO belongs_to VALUES (?, ?, ?)";

            $access_level = "Owner";

            if($stmt2 = mysqli_prepare($link, $sql2)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt2, "iis", $param_user_id, $param_group_id, $param_access_level);

                // Set parameters in the "mysqli_stmt_bind_param" function
                $param_user_id = $_SESSION["id"];
                $param_group_id = $group_id;
                $param_access_level = $access_level;

                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt2)){
                    // Redirect to groups page upon success
                    // header("location: groups.php");
                    echo "success";
                } else{
                    echo "Something went wrong. Please try again later.";
                }
                // Close statement
                mysqli_stmt_close($stmt2);
            }
            $sql3 = "INSERT INTO Gear VALUES (?, ?, ?, ?, ?)";
            if($stmt3 = mysqli_prepare($link, $sql3)){
                mysqli_stmt_bind_param($stmt3, "sssss", $gear_id, $gear_type, $gear_status, $gear_brand, $gear_condition);
                
                if(mysqli_stmt_execute($stmt3)){
                    // Redirect to groups page upon success
                    //header("location: groups.php");
                    echo "success";
                } else{
                    printf($stmt3->error);
                    //echo "Something went wrong. Please try again later.";
                }
                // If the insert didn't affect any rows, the group was not successfully created.
                mysqli_stmt_close($stmt3);
            }

            $sql4 = "INSERT INTO Has VALUES (?,?)";
            if($stmt4 = mysqli_prepare($link, $sql4)){
                mysqli_stmt_bind_param($stmt4, "is", $group_id, $gear_id);
                
                if(mysqli_stmt_execute($stmt4)){
                    // Redirect to groups page upon success
                    header("location: groups.php");
                } else{
                    printf($stmt4->error);
                    echo "Something went wrong. Please try again later.";
                }
                // If the insert didn't affect any rows, the group was not successfully created.
                mysqli_stmt_close($stmt4);
            }

        }

        // Close connection
        mysqli_close($link);
    }

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

		<title>Create Group</title>

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
          <li class="nav-item">
		        <a class="nav-link" href="trips.php"> Trips </a>
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

		<h1> Create Group </h1>

        <!-- This will display an error if the user is not logged in -->
        <span class="help-block"><?php echo $loggedin_err; ?></span>

        <!-- Form that will run the above PHP upon "submit" (POST) -->
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <!-- Labels and input for "Group Name" insert -->
	            <div class="form-group <?php echo (!empty($groupname_err)) ? 'has-error' : ''; ?>">
	                <label>Group Name</label>
	                <input type="text" name="groupname" class="form-control" value="<?php echo $groupname; ?>">
	                <span class="help-block"><?php echo $groupname_err; ?></span>
	            </div>

                <!-- Labels and input for "Description" insert -->
	            <div class="form-group <?php echo (!empty($description_err)) ? 'has-error' : ''; ?>">
	                <label>Description</label>
	                <input type="text" name="description" class="form-control" value="<?php echo $description; ?>">
	                <span class="help-block"><?php echo $description_err; ?></span>
	            </div>

                <div class="table-responsive <?php echo (!empty($gear_err)) ? 'has-error' : ''; ?>">
                    <table class="table table-bordered" id="crud_table" name="crud_table">
                        <tr>
                            <th width="18%">Gear Name</th>
                            <th width="18%">Gear Type</th>
                            <th width="18%">Gear Status</th>
                            <th width="18%">Gear Brand</th>
                            <th width="18%">Gear Condition</th>
                            <th width="10%"></th>
                        </tr>
                        <tr>
                            <td name="gearTable"><input type="text" name="gear_name"></td>
                            <td name="gearTable"><input type="text" name="gear_type"></td>
                            <td name="gearTable"><input type="text" name="gear_status"></td>
                            <td name="gearTable"><input type="text" name="gear_brand"></td>
                            <td name="gearTable"><input type="text" name="gear_condition"></td>
                            <td></td>
                        </tr>
                        <span class="help-block"><?php echo $gear_err; ?></span>
                    </table>
                    <div align="right">
                        <button type="button" name="add" id="add" class="btn btn-success btn-xs">+</button>
                    </div>
                </div> 

                <!-- Checkbox for deciding if a group is public or private -->
	            <div class="form-group form-check">
    				<input type="checkbox" class="form-check-input" name="privateCheckbox" value="YES" id="check">
    				<label class="form-check-label" for="check">Make Group Private (invite only)</label>
  				</div>

                <!-- Group that controls the submission of the form, triggers a POST call -->
	            <div class="form-group">
	                <input type="submit" class="btn btn-primary" value="Submit">
	                <input type="reset" class="btn btn-default" value="Reset">
	            </div>
	        </form>
            <script>
                $(document).ready(function(){
                    var count = 1;
                    $('#add').click(function(){
                        count = count + 1;
                        var html_code = "<tr id='row"+count+"'>";
                        html_code += "<td name='gearTable'><input type='text' name='gear_name"+count+"'></td>";
                        html_code += "<td name='gearTable'><input type='text' name='gear_type"+count+"'></td>";
                        html_code += "<td name='gearTable'><input type='text' name='gear_status"+count+"'></td>";
                        html_code += "<td name='gearTable'><input type='text' name='gear_brand"+count+"'></td>";
                        html_code += "<td name='gearTable'><input type='text' name='gear_condition"+count+"'></td>";
                        html_code += "<td><button type='button' name='remove' data-row='row"+count+"' class='btn btn-danger btn-xs remove'>-</button></td>";
                        html_code += "</tr>";
                        $('#crud_table').append(html_code);
                    });
                    $(document).on('click', '.remove', function(){
                        var delete_row = $(this).data("row");
                        $('#'+delete_row).remove();
                    });
                })
            </script>
	</body>

</html>
