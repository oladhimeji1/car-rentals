<?php 
// DB credentials.
define('DB_HOST','localhost:3302');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','carrental');
// define('DB_HOST','localhost');
// define('DB_USER','id21504807_root');
// define('DB_PASS','Ola-2000');
// define('DB_NAME','id21504807_carrental');
// Establish database connection.
try
{
$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e)
{
exit("Error: " . $e->getMessage());
}
?>