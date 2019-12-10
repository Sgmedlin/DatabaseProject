<?php
    // Code help from
    // https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php
    // Initialize the session
    session_start();
    // Check if the user is already logged in, if yes then redirect him to welcome page
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location: index.php");
        exit;
    }
    // Include config file
    require_once "config.php";
    // Define variables and initialize with empty values
    $username = $password = "";
    $username_err = $password_err = "";
    // Processing form data when form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // Check if username is empty
        if(empty(trim($_POST["username"]))){
            $username_err = "Please enter username.";
        } else{
            $username = trim($_POST["username"]);
        }
        // Check if password is empty
        if(empty(trim($_POST["password"]))){
            $password_err = "Please enter your password.";
        } else{
            $password = trim($_POST["password"]);
        }
        // Validate credentials
        if(empty($username_err) && empty($password_err)){
            // Prepare a select statement
            $sql = "SELECT id, username, password FROM Users WHERE username = ?";
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                // Set parameters
                $param_username = $username;
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Store result
                    mysqli_stmt_store_result($stmt);
                    // Check if username exists, if yes then verify password
                    if(mysqli_stmt_num_rows($stmt) == 1){
                        // Bind result variables
                        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                        if(mysqli_stmt_fetch($stmt)){
                            if(password_verify($password, $hashed_password)){
                                // Password is correct, so start a new session
                                session_start();
                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                // Redirect user to welcome page
                                header("location: index.php");
                            } else{
                                // Display an error message if password is not valid
                                $password_err = "The password you entered was not valid.";
                            }
                        }
                    } else{
                        // Display an error message if username doesn't exist
                        $username_err = "No account found with that username.";
                    }
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
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
<html lang="en" style="min-height:100%; position:relative;"><head>
        <meta charset="UTF-8">

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

input[type="password"],
select.form-control {
  background: transparent;
	border: 1px solid #BEBEBEBE;
  -webkit-box-shadow: none;
  box-shadow: none;
  border-radius: 50px;
}

input[type="password"]:focus,
select.form-control:focus {
  -webkit-box-shadow: none;
  box-shadow: none;
}
    </style>
        <title>Login</title>
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
                      <a href='logout.php' class='nav-link' style='color:white;'> Log Out </a>
                  </span>";
                } else {
                  echo "<span class='navbar-nav'>
                <a href='login.php' class='nav-link' style='color:white;'> Login </a>
              </span>";
                }
                ?>

      </div>


		</nav>
        <div class="container" style="padding:25px 25px 145px 25px;">
<div class="card" style="padding: 25px; width: 90%; margin:auto; border-radius:25px; background-color:#f5f5f6;">
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
              <label>Username</label>
              <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
              <span class="help-block"><?php echo $username_err; ?></span>
          </div>
          <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
              <label>Password</label>
              <input type="password" name="password" class="form-control">
              <span class="help-block"><?php echo $password_err; ?></span>
          </div>
          <div class="form-group">
              <input type="submit" class="btn btn-primary" value="Login">
          </div>
          <p>Don't have an account? <a href="signup.php">Sign up now</a>.</p>
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
