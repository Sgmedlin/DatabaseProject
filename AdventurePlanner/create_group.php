<?php
// Initialize the session
session_start();



require_once "session_config.php";
 
// Define variables and initialize with empty values
$groupname = $description = $private = "";
$groupname_err = $loggedin_err = $description_err = "";
$group_id = 0;

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    $loggedin_err = "You must be logged in to create a group.";
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate username
    if(empty(trim($_POST["groupname"]))){
        $groupname_err = "Please enter a name for your group.";
    } else{
        // Prepare a select statement
        $sql = "SELECT group_id FROM Groups WHERE group_name=?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_groupname);
            
            // Set parameters
            $param_groupname = trim($_POST["groupname"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $groupname_err = "This group name is already taken.";
                } else{
                    $groupname = trim($_POST["groupname"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Validate password
    if(empty(trim($_POST["description"]))){
        $description_err = "Please provide a description for your group.";     
    } else{
        $description = trim($_POST["description"]);
    }

    if(isset($_POST['privateCheckbox']) && $_POST['privateCheckbox'] == 'YES'){
    	$private = "Closed";
    }
    else{
    	$private = "Open";
    }


    // Check input errors before inserting in database
    if(empty($groupname_err) && empty($description_err) && empty($loggedin_err)){


        $sql0 = "INSERT INTO Groups (group_name, description, status) VALUES (?, ?, ?)";

        if($stmt0 = mysqli_prepare($link, $sql0)){
            mysqli_stmt_bind_param($stmt0, "sss", $param_groupname, $param_description, $param_status);

            $param_groupname = $groupname;
            $param_description = $description;
            $param_status = $private;


            mysqli_stmt_execute($stmt0);

            if(mysqli_stmt_affected_rows($stmt0) < 1){
                echo "Could not create group";
            }else{
                $group_id = mysqli_insert_id($link);

            }

        }



        mysqli_stmt_close($stmt0);

    
        // $sql1 = "SELECT group_id FROM Group WHERE group_name = ?";
        
        // if($stmt1 = mysqli_prepare($link, $sql1)){
        //     // Bind variables to the prepared statement as parameters
        //     mysqli_stmt_bind_param($stmt1, "s", $param_groupname);
            
        //     // Set parameters
        //     $param_groupname = $groupname;
            
        //     // Attempt to execute the prepared statement
        //     if(mysqli_stmt_execute($stmt1)){
        //         // Store result
        //         mysqli_stmt_store_result($stmt1);
                
        //         // Check if username exists, if yes then verify password
        //         if(mysqli_stmt_num_rows($stmt1) == 1){                    
        //             // Bind result variables
        //             mysqli_stmt_bind_result($stmt1, $id);
        //             if(mysqli_stmt_fetch($stmt1)){
                        
        //             	$group_id = $id;

        //             }
        //         } else{
        //             // Display an error message if username doesn't exist
        //             $groupname_err = "No group with that name exists.";
        //         }
        //     } else{
        //         echo "Oops! Something went wrong. Please try again later.";
        //     }
        // }

        // // Close statement
        // mysqli_stmt_close($stmt1);

        // Prepare an insert statement
        $sql2 = "INSERT INTO belongs_to (user_id, group_id, membership_level) VALUES (?, ?, ?)";
         
        $access_level = "Owner";

        if($stmt2 = mysqli_prepare($link, $sql2)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt2, "iis", $param_user_id, $param_group_id, $param_access_level);

            $param_user_id = $_SESSION["id"];
            $param_group_id = $group_id;
            $param_access_level = $access_level;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt2)){
                // Redirect to login page
                header("location: groups.php");
            } else{
                // echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt2);


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
        <span class="help-block"><?php echo $loggedin_err; ?></span>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
	            <div class="form-group <?php echo (!empty($groupname_err)) ? 'has-error' : ''; ?>">
	                <label>Group Name</label>
	                <input type="text" name="groupname" class="form-control" value="<?php echo $groupname; ?>">
	                <span class="help-block"><?php echo $groupname_err; ?></span>
	            </div>    
	            <div class="form-group <?php echo (!empty($description_err)) ? 'has-error' : ''; ?>">
	                <label>Description</label>
	                <input type="text" name="description" class="form-control" value="<?php echo $description; ?>">
	                <span class="help-block"><?php echo $description_err; ?></span>
	            </div>
	            <div class="form-group form-check">
    				<input type="checkbox" class="form-check-input" name="privateCheckbox" value="YES" id="check">
    				<label class="form-check-label" for="check">Make Group Private (invite only)</label>
  				</div>
	            <div class="form-group">
	                <input type="submit" class="btn btn-primary" value="Submit">
	                <input type="reset" class="btn btn-default" value="Reset">
	            </div>
	        </form>




	</body>


</html>
