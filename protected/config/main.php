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

define('DB_HOST', 'sql336.your-server.de');
define('DB_USER', 'indexated_16');
define('DB_PASS', 'indexated_16');
define('DB_DATA', 'indexated_16');

ini_set("max_execution_time",30);


$con = @mysql_connect(DB_HOST, DB_USER, DB_PASS);
if (!$con) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db(DB_DATA, $con);
mysql_query("SET NAMES UTF8");
?>
