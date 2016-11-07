<?php

ini_set('display_errors', 1);
//panell16.recambiosya.es/redirects/update_redirects.php
//include('protected/scraps_lib.php');
require_once("../protected/config/main.php");
include('../protected/models/Categoria.php');
include('../lib/functions.php');
include('../lib/PSWebServiceLibrary.php');
include('../lib/scraps_lib.php');

//Posem l'estoc disponible de les peces a 0 a prestashop

$sql = "SELECT * FROM a_products_15 LEFT JOIN ps_product USING (id_product)            
            WHERE id_supplier IS NULL AND ps_lgseoredirect.url_old IS NULL";

/*
 * LEFT JOIN ps_lgseoredirect ON ps_lgseoredirect.url_old =
                replace(a_products_15.url_product,'http:\/\/www.recambiosya.es', '')
   
                      */

$sql = "SELECT * FROM a_products_15 LEFT JOIN ps_product USING (id_product)            
            WHERE id_supplier IS NULL";
$result = mysql_query($sql);

if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_array($result)) {
        //print_r($row);
        //print_r('<br>');
        $url_desti = "http://www.recambiosya.es/pedir/buscar_index.php";
        $url_old = preg_replace('#http:\/\/www.recambiosya.es#', '', $row[url_product]);

        $sql_exis = "SELECT * from ps_lgseoredirect where url_old = '$url_old'";
        $result_exis = mysql_query($sql_exis);
        //echo "<br>" . $sql_exis;
        //echo "<br>Num rows: " . (mysql_num_rows($result_exis));
        if (mysql_num_rows($result_exis) < 1) {
            $sql2 = "INSERT INTO ps_lgseoredirect SET
                url_old = '$url_old', url_new = '$url_desti',redirect_type='301'";
            echo "<br>" . $sql2;
            $result_sql2 = mysql_query($sql2);
            //echo "<br>" . $result_sql2;
        } else {
            //echo "<br>" . "Ja existeix redirect";
        }
        ini_set("max_execution_time", 300);
    }
} else {
    echo "No hi ha res pendent. Congrats !!!";
}
echo "fi del proces. Congrats !!!";
?>
