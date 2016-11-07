<?php

ini_set('display_errors', 1);

//include('protected/scraps_lib.php');
require_once("../protected/config/main.php");

include('../lib/functions.php');
include('../lib/PSWebServiceLibrary.php');
include('../protected/models/Product.php');
include('../protected/models/Vehicle.php');
include('../protected/models/Models.php');
include('../protected/models/Peca.php');
include('../protected/models/Europ_Entradas.php');
include('../lib/scraps_lib.php');



// localhost/panell16/update/update_test.php
//Posem l'estoc disponible de les peces a 0 a prestashop

$sql = "SELECT * FROM ps_product limit 0,1";
$result = mysql_query($sql);
echo $sql;
echo "<br>";
if (mysql_num_rows($result) > 0) {
    $row = mysql_fetch_array($result);
    //$peca = new Peca($row["codigo"]);
    echo 'id_product: '.$row["id_product"];
    echo  '<br>url del producte: '.(urlProduct($row["id_product"]));
}else {
    echo "No hi ha productes sense estoc";
}


?>
