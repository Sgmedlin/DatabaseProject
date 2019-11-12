<?php
	// This configuration file will be used for standard user access
	// The user "sgm4gu_a" has only been granted read and write access for most
	// Relevant user database tables

	/* Database credentials */
	define('DB_SERVER', 'cs4750.cs.virginia.edu');
	define('DB_USERNAME', 'sgm4gu_a');
	define('DB_PASSWORD', 'Ix8ieJoo');
	define('DB_NAME', 'sgm4gu');
	 
	/* Attempt to connect to MySQL database */
	$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
	 
	// Check connection
	if($link === false){
	    die("ERROR: Could not connect. " . mysqli_connect_error());
	}
?>