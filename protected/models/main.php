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
define('PS_SHOP_PATH', 'recambiosya.es/');
define('PS_WS_AUTH_KEY', 'CJTT6RH9E6LGRXH4042ZU6YXW3XDW2KO');
define('DB_HOST', 'bbdd.recambiosya.es');
define('DB_USER', 'ddb34891');
define('DB_PASS', 'G7xMFJyVzd');
define('DB_DATA', 'ddb34891');


ini_set("max_execution_time",30);

$clau_api = "CJTT6RH9E6LGRXH4042ZU6YXW3XDW2KO";
$dbhost = 'bbdd.recambiosya.es';
$dbuser = 'ddb34891';
$dbpass = 'G7xMFJyVzd';
$dbdata = 'ddb34891';

$con = mysql_connect($dbhost, $dbuser, $dbpass);
if (!$con) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db($dbdata, $con);
mysql_query("SET NAMES UTF8");
?>
