<?php
class DbUtil{
	public static $loginUser = "sgm4gu"; 
	public static $loginPass = "PleaseDontStealThisPassword";
	public static $host = "http://cs4750.cs.virginia.edu"; // DB Host
	public static $schema = "sgm4gu"; // DB Schema
	
	public static function loginConnection(){
		$db = new mysqli(DbUtil::$host, DbUtil::$loginUser, DbUtil::$loginPass, DbUtil::$schema);
	
		if($db->connect_errno){
			echo("Could not connect to db");
			$db->close();
			exit();
		}
		
		return $db;
	}
	
}
?>