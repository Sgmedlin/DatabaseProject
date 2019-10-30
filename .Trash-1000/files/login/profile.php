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
$name_err = $email_err = "";
$welcome_message = "";
 
 function checkUserProfileExists($user_id){
    $query = "SELECT * FROM User_Profile WHERE user_id='$user_id' LIMIT 1";
    $result = mysqli_query($link, $query);

    if(mysqli_fetch_array($result) !== NULL){
        return true;
    }
    return false;
 }

 $param_id = $_SESSION["id"];

 if (checkUserProfileExists($param_id) == false){
    $welcome_message = "Thank you for signing up, please fill out the information below!";
 }

 $query = "SELECT * FROM User_Profile WHERE user_id='$param_id' LIMIT 1";
 $result = mysqli_query($link, $query);

 $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
 $name = $row['name']; 
 $email = $row['email']; 
 $bio = $row['bio']; 
  
  echo "<p>$email</p>";

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


    if (checkUserProfileExists($param_id) == true){

        // Check input errors before inserting in database
        if(empty($name_err) && empty($email_err)){
            
            // Prepare an insert statement
            $sql = "UPDATE User_Profile SET email='$email', name='$name', bio='$bio' WHERE user_id='$param_id'";
             
            if($stmt = mysqli_prepare($link, $sql)){

                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Redirect to welcome page
                    header("location: welcome.php");
                } else{
                    echo "Something went wrong. Please try again later.";
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
                    header("location: welcome.php");
                } else{
                    echo "Something went wrong. Please try again later.";
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
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
                    <input type="name" name="name" class="form-control" value="<?php echo $name; ?>">
                    <span class="help-block"><?php echo $name_err; ?></span>
                </div>  
                <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
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
            </form>
        </div>  
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
    </div>
    <p>
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
</body>
</html>