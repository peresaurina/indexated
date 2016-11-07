<?php

ini_set('display_errors', 1);
require_once("../protected/config/main.php");

include('../protected/models/Product.php');
include('../protected/models/Peca.php');
include('../lib/functions.php');
//include('protected/config/functions.php');
// http://localhost/panell16/updates/product_redirects.php
// panell16.recambiosya.es/updates/product_redirects.php

$sql = "SELECT * FROM a_peces 
        INNER JOIN ps_product ON ps_product.id_product = a_peces.codigo+1000000
        LEFT JOIN ps_lgseoredirect ON ps_lgseoredirect.url_old like CONCAT('%',ps_product.id_product,'%')
        WHERE enestoc = 0 AND a_peces.url_es IS NOT null and ps_lgseoredirect.url_old is null        
        ORDER BY codigo desc
        ";

$result = mysql_query($sql);
$i = 0;
while (($row = mysql_fetch_array($result))) {

    $peca_origen = new Peca($row["codigo"]);
    // Obtenir canonical
    $peca_origen->getCanonical();
    $url_old = preg_replace("#http:\/\/www\.recambiosya\.es#", "", $peca_origen->getUrl_es());

    $sql = "INSERT INTO ps_lgseoredirect SET
                    url_old = '" . $url_old . "',
                    url_new = '" . $peca_origen->getCanonical() . "',
                    redirect_type = '301',
                    ps_lgseoredirect.update = now()";

    $result1 = mysql_query($sql);
    if ($result1 != '1') {
        echo "<br>No s'ha pogut insertar: " . $sql . "-" . $result1;
    }
    ini_set("max_execution_time", 30);
    $i++;
}

echo utf8_decode("<h2>Procés acabat amb éxit. Registres actualitzats: " . $i);
?>
