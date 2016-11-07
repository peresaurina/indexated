<?php

/**
 * Website main config file
 * 
 * @author pgarriga
 * @author Porpra Augmented SL
 * @name Website Main Config
 * @category Porpra
 * @package twitypromotions
 * @subpackage config
 */

require_once("autoload.php");

define('DEBUG', false);
define('PS_SHOP_PATH', 'http://www.recambiosya.es');
define('PS_WS_AUTH_KEY', 'RJUD42WEQUTB4EJVWUPQ8BJJVIAWCRFA');

define('DB_HOST', 'sql23.your-server.de');
define('DB_USER', 'recya16');
define('DB_PASS', 'recya16');
define('DB_DATA', 'recya16');

ini_set("max_execution_time",30);

$clau_api = "RJUD42WEQUTB4EJVWUPQ8BJJVIAWCRFA";
$dbhost = 'sql23.your-server.de';
$dbuser = 'recya16';
$dbpass = 'recya16';
$dbdata = 'recya16';

/*
$mysqli = new mysqli(DB_HOST, DB_USER,DB_PASS, DB_DATA);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
mysqli_select_db($mysqli, DB_DATA);
mysqli_query("SET NAMES UTF8");
*/

$con = @mysql_connect(DB_HOST, DB_USER, DB_PASS);
if (!$con) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db(DB_DATA, $con);
mysql_query("SET NAMES UTF8");
?>
