<?php
    // Initialize the session
    session_start();

    // Check if the user is logged in, if not then redirect him to login page
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }

    // Include config file
    require_once "session_config.php";

    // Define variables and initialize with empty values
    $name = $email = $bio = "";
    $name_err = $email_err = $bio_err= "";
    $welcome_message = "";
    $update_information_text = "";

     $param_id = $_SESSION["id"];

     $query = "SELECT * FROM User_Profile WHERE user_id=$param_id LIMIT 1";
     $result = mysqli_query($link, $query);

     $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
     $name = $row['name'];
     $email = $row['email'];
     $bio = $row['bio'];

     $rows = mysqli_num_rows($result);

     if ($rows > 0){
        $param_result = true;
     }
     else{
        $param_result = false;
     }

     if ($param_result == false){
        $welcome_message = "Thank you for signing up, please fill out the information below!";
     }

     mysqli_free_result($result);
    // Processing form data when form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        // Validate name field
        if(empty(trim($_POST["name"]))){
            $name_err = "Please enter your name.";
        } else{
            $name = trim($_POST["name"]);
        }

        // Validate email field
        if(empty(trim($_POST["email"]))){
            $email_err = "Please enter your email";
        } else{
            $email = trim($_POST["email"]);
        }

        // Make sure bio field is set to empty string if none is input
        if(empty(trim($_POST["bio"]))){
            $bio = " ";
        } else{
            $bio = trim($_POST["bio"]);
        }


        if ($param_result == true){

            // Check input errors before inserting in database
            if(empty($name_err) && empty($email_err)){

                // Prepare an insert statement
                $sql = "UPDATE User_Profile SET email='$email', name='$name', bio='$bio' WHERE user_id='$param_id'";

                if($stmt = mysqli_prepare($link, $sql)){

                    // Attempt to execute the prepared statement
                    if(mysqli_stmt_execute($stmt)){
                        // Redirect to welcome page
                        $update_information_text = "Successfully updated information.";
                    } else{
                        $update_information_text = "Something went wrong... Please try again later.";
                    }
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }

        }
        else{
            // Check input errors before inserting in database
            if(empty($name_err) && empty($email_err)){

                // Prepare an insert statement
                $sql = "INSERT INTO User_Profile (user_id, email, name, bio) VALUES (?, ?, ?, ?)";

                if($stmt = mysqli_prepare($link, $sql)){
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "isss", $param_id, $email, $name, $bio);

                    // Set parameters
                    $param_username = $username;
                    $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

                    // Attempt to execute the prepared statement
                    if(mysqli_stmt_execute($stmt)){
                        // Redirect to welcome page
                        $update_information_text = "Successfully created user profile.";
                    } else{
                        $update_information_text = "Something went wrong. Please try again later.";
                    }
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }

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
    </style>
        <title>Profile</title>
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
                  echo "<li class='nav-item active'>
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

        <div class="wrapper">
                <h2>Profile</h2>
                <p><?php echo $welcome_message ?></p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($_SESSION["username"]); ?>">
                    </div>
                    <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                        <span class="help-block"><?php echo $name_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                        <label>Email</label>
                        <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                        <span class="help-block"><?php echo $email_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($bio_err)) ? 'has-error' : ''; ?>">
                        <label>Bio</label>
                        <input type="text" name="bio" class="form-control" value="<?php echo $bio; ?>">
                        <span class="help-block"><?php echo $bio_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <input type="reset" class="btn btn-default" value="Reset">
                    </div>
                    <?php echo $update_information_text ?>
                </form>
            </div>

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
