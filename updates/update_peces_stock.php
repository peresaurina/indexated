<?php

ini_set('display_errors', 1);

//include('protected/scraps_lib.php');
require_once("../protected/config/main.php");
include('../protected/models/Categoria.php');
include('../lib/functions.php');
include('../lib/PSWebServiceLibrary.php');
include('../lib/scraps_lib.php');

//Posem l'estoc disponible de les peces a 0 a prestashop

$sql = "SELECT * FROM a_peces where enestoc = '0'";
$sql = "SELECT * FROM a_peces 
        inner join ps_stock_available on ps_stock_available.id_product = a_peces.codigo + 1000000
        where a_peces.enestoc = '0' 
            and ps_stock_available.quantity = 1";

$result = mysql_query($sql);

if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_array($result)) {
        //print_r($row);
        //print_r('<br>');
        
        $result_ok = updateProductStock(1000000+$row["codigo"], 0);
        ini_set("max_execution_time", 300);
        //if ($result_ok)
            //print_r("id: " . $row["id_product"] . " estoc actualitzat<br>");
        //else
            //print_r("id: " . $row["id_product"] . " ERROR producte no existeix<br>");
    }
}else {
    echo "No hi ha productes sense estoc";
}

echo "<h1>Actualitzat estoc peces: ".mysql_num_rows($result);

echo '<br><br><br><a href="http://panell16.recambiosya.es">Tornar</a>';

?>
