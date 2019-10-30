<?php
class DbUtil{
public static $user = "sgm4gu";
public static $pass = "PleaseDontStealThisPassword";
public static $host = "cs4750.cs.virginia.edu";
public static $schema = "sgm4gu";
public static function loginConnection() {
$db = new mysqli(DbUtil::$host, DbUtil::$user,
DbUtil::$pass, DbUtil::$schema);
if($db->connect_errno) {
echo "fail";
$db->close();
exit();
}
return $db;
}
}
?>