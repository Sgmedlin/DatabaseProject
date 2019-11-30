<?php
    // Include config file
    require_once "config.php";

    // Define variables and initialize with empty values
    $username = $password = $confirm_password = "";
    $username_err = $password_err = $confirm_password_err = "";

    // Processing form data when form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        // Validate username
        if(empty(trim($_POST["username"]))){
            $username_err = "Please enter a username.";
        } else{
            // Prepare a select statement
            $sql = "SELECT id FROM Users WHERE username = ?";

            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_username);

                // Set parameters
                $param_username = trim($_POST["username"]);

                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    /* store result */
                    mysqli_stmt_store_result($stmt);

                    if(mysqli_stmt_num_rows($stmt) == 1){
                        $username_err = "This username is already taken.";
                    } else{
                        $username = trim($_POST["username"]);
                    }
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }

        // Validate password
        if(empty(trim($_POST["password"]))){
            $password_err = "Please enter a password.";
        } elseif(strlen(trim($_POST["password"])) < 6){
            $password_err = "Password must have atleast 6 characters.";
        } else{
            $password = trim($_POST["password"]);
        }

        // Validate confirm password
        if(empty(trim($_POST["confirm_password"]))){
            $confirm_password_err = "Please confirm password.";
        } else{
            $confirm_password = trim($_POST["confirm_password"]);
            if(empty($password_err) && ($password != $confirm_password)){
                $confirm_password_err = "Password did not match.";
            }
        }

        // Check input errors before inserting in database
        if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

            // Prepare an insert statement
            $sql = "INSERT INTO Users (username, password) VALUES (?, ?)";

            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

                // Set parameters
                $param_username = $username;
                $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Redirect to login page
                    header("location: profile.php");
                } else{
                    echo "Something went wrong. Please try again later.";
                }
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }

        // Close connection
        mysqli_close($link);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">

        <!-- BootStrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <!-- Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>


        <title>Login</title>
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
    	    <div class="wrapper">
    	        <h2>Sign Up</h2>
    	        <p>Please fill this form to create an account.</p>
    	        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    	            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
    	                <label>Username</label>
    	                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
    	                <span class="help-block"><?php echo $username_err; ?></span>
    	            </div>
    	            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
    	                <label>Password</label>
    	                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
    	                <span class="help-block"><?php echo $password_err; ?></span>
    	            </div>
    	            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
    	                <label>Confirm Password</label>
    	                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
    	                <span class="help-block"><?php echo $confirm_password_err; ?></span>
    	            </div>
    	            <div class="form-group">
    	                <input type="submit" class="btn btn-primary" value="Submit">
    	                <input type="reset" class="btn btn-default" value="Reset">
    	            </div>
    	            <p>Already have an account? <a href="login.php">Login here</a>.</p>
    	        </form>
    	    </div>
	</body>
</html>
