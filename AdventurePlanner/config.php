<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'cs4750.cs.virginia.edu');
define('DB_USERNAME', 'sgm4gu_d');
define('DB_PASSWORD', 'Ix8ieJoo');
define('DB_NAME', 'sgm4gu');
 
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>