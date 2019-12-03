<?php
	session_start();

	// $delete_group_button = ""

	// Uses basic user access level for viewing adventure details
	require_once "session_config.php";

	// Form the SQL query (an INSERT query)

 	$sql = "DELETE FROM Groups WHERE group_id = ?";

 	if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_group_id);

        // Set parameters
        $param_group_id = $_GET['id'];

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            // Redirect to login page
            header("location: groups.php");
        } else{
            echo "Something went wrong. Please try again later.";
            // header("location: groups.php");
        }
    }

?>