<?php

	session_start();

	require_once "session_config.php";

   $sql = "SELECT * FROM Adventure WHERE adventure_id = '".$_GET['id']."'";
   $result = mysqli_query($link, $sql);

   $adventure_details = mysqli_fetch_assoc($result);

   echo 'Adventure Name : '.$adventure_details['name'].'<br>';
   echo 'Latitude: '.$adventure_details['latitude'].'<br>';
   echo 'Longitude: '.$adventure_details['longitude'].'<br>';
   echo 'Description: '.$adventure_details['description'].'<br>';


?>