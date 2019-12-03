<?php
    // Initialize the session
    session_start();

    // Uses basic user access level for viewing adventure details
    require_once "session_config.php";

    // Define variables and initialize with empty values
    $groupname = $description = $private = "";
    $groupname_err = $loggedin_err = $description_err = "";
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
                    $_SESSION["group_id"] = $group_id;
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
                   //header("location: groups.php");
                   echo("success");
                } else{
                    echo "Something went wrong. Please try again later.";
                }
                // Close statement
                mysqli_stmt_close($stmt2);
            }
            if(isset($_SESSION["array_name"])){
                $array_name = $_SESSION["array_name"];
            }
            if (!empty($array_name)){
                $hasQuery = '';
                for ($count = 0; $count < count($array_name); $count++){
                    $hasQuery .= '
                        INSERT INTO Has VALUES("'.$group_id.'", "'.$array_name[$count].'");  
                    ';
                }
                if ($hasQuery != ''){
                    if (mysqli_multi_query($link, $hasQuery)){
                        //unset($_SESSION['array_name']);
                        header("location: groups.php");
                    }
                    else{
                        echo 'Error';
                    }
                }
                else{
                    echo 'All Fields are Required';
                }
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
        <script src="https://code.jquery.com/jquery-3.3.1.min.js">
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
		<form id="done" name="done" method="POST">
                <!-- Labels and input for "Group Name" insert -->
	            <div class="form-group">
	                <label>Group Name</label>
	                <input type="text" name="groupname" class="form-control" required="true">
	            </div>

                <!-- Labels and input for "Description" insert -->
	            <div class="form-group">
	                <label>Description</label>
	                <input type="text" name="description" class="form-control" required="true">
	            </div>

                <div id="table_ready" class="form-group table-responsive">
                    <label>Gear</label>
                    <table class="table table-bordered" id="crud_table">
                        <tr>
                            <th width="10%">Name</th>
                            <th width="10%">Type</th>
                            <th width="10%">Status</th>
                            <th width="10%">Brand</th>
                            <th width="10%">Condition</th>
                            <th width="5%"></th>
                        </tr>
                        <tr>
                            <td contenteditable="true" class="name"></td>
                            <td contenteditable="true" class="type"></td>
                            <td contenteditable="true" class="status"></td>
                            <td contenteditable="true" class="brand"></td>
                            <td contenteditable="true" class="condition"></td>
                            <td></td>
                        </tr>
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
	                <button type="submit" id="save" name="save" class="btn btn-primary">Submit</button>
                    <input type="reset" class="btn btn-default" value="Reset">
	            </div>
	        </form>
	</body>
    <script>
        $('#done').ready(function(){
            var count = 1;
            $('#add').click(function(){
                count = count + 1;
                var html_code = "<tr id='row"+count+"'>";
                html_code += "<td contenteditable='true' class='name'></td>";
                html_code += "<td contenteditable='true' class='type'></td>";
                html_code += "<td contenteditable='true' class='status'></td>";
                html_code += "<td contenteditable='true' class='brand'></td>";            
                html_code += "<td contenteditable='true' class='condition'></td>";
                html_code += "<td><button type='button' name='remove' data-row='row"+count+"' class='btn btn-danger btn-xs remove'>-</button></td>";
                html_code += "</tr>";
                $('#crud_table').append(html_code);  
            });
            $(document).on('click', '.remove', function(){
                var delete_row = $(this).data("row");
                $('#'+delete_row).remove();
            });
                $('#save').on("click", function(){  
                    var name = [];
                    var type = [];
                    var status = [];
                    var brand = [];
                    var condition = [];
                    $('.name').each(function(){
                        name.push($(this).text());
                    });
                    $('.type').each(function(){
                        type.push($(this).text());
                    });
                    $('.status').each(function(){
                        status.push($(this).text());
                    });
                    $('.brand').each(function(){
                        brand.push($(this).text());
                    });
                    $('.condition').each(function(){
                        condition.push($(this).text());
                    });
                    jQuery.ajax({
                        url:"insert.php",
                        method: "POST",
                        data:{
                            name:name, 
                            type:type, 
                            status:status, 
                            brand:brand, 
                            condition:condition},
                        success:function(result){
                            //window.location.href = "groups.php";
                        },
                        error:function(result){
                            alert("error");
                        }
                    });
                });
        });
    </script>
</html>