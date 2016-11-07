<?php

ini_set('display_errors', 1);
require_once("protected/config/main.php");

include('protected/models/Product.php');
include('protected/models/Europ_Entradas.php');
include('lib/functions.php');
//include('protected/config/functions.php');
include('lib/scraps_lib.php');
include('lib/PSWebServiceLibrary.php');
//ini_set('max_execution_time', 3000);

$fitxer = (isset($_REQUEST["fitxer"]) ? $_REQUEST["fitxer"] : 0); //true = genera fitxer csv

if ($fitxer == 1) {
    header("Content-type: application/csv; charset=UTF-8");
    $nom_file = "redirects_" . time() . ".csv";
    header("Content-Disposition: attachment; filename= " . $nom_file . "");
    header("Pragma: no-cache");
    header("Expires: 0");
}
// Script per gestió de productes sense estoc que no tenen redirect
// 
//http://localhost/panell/product_no_estoc_no_redirects.php
// panell.recambiosya.es/product_no_estoc_no_redirects.php?fitxer=1
// panell.recambiosya.es/product_no_estoc_no_redirects.php?fitxer=0

$sql = "SELECT codigo.Almacen FROM Almacen 
        where clvaut != '0' 
        and albaran != '0'          
        order by codigo desc      
        ";

//Articles en Almacen que s'han venut, i que estant donat d'altes a prestashop
$sql = "SELECT Almacen.codigo FROM Almacen
        INNER JOIN ps_product ON ps_product.id_product = 1000000+Almacen.codigo 
        where Almacen.clvaut != '0' 
        and Almacen.albaran != '0'     
        order by codigo desc ";

$result = mysql_query($sql);
if ($fitxer != 1){
    echo "Articles sense estoc, mirem si tenen redireccionament:<br>";    
}

while ($row = mysql_fetch_array($result)) {
    //sql per si tenen redirect creat
    $id_origen = 1000000 + $row["codigo"];
    $url_origen = urlProduct($id_origen);
    if ($fitxer != 1) echo "<br>" . $i;
    if (existRedirect($url_origen)) {
        // if ($fitxer != 1) echo "<br>Tenim redirect fet.";
    } else {
        if ($fitxer != 1) {
            if (!$fitxer)
                echo "<br>" . $url_origen;
                echo "<br>Redireccionart : " . $id_origen;
            //buscar producte substitutiu            
        }
        $redirects[] = $url_origen;
        $i++;        
    }

    if ($fitxer != 1)
        echo "<br>---------------------------------------------------";
}
if (sizeof($redirects) > 0) {
    foreach ($redirects as $redirect) {
        echo $redirect . ";http://pedir.recambiosya.es/buscar_index.php;" . "301\r\n";
        /* $sql_insert = "INSERT into `ps_lgseoredirect` set 
          url_old = '".$redirect."',
          url_new = 'http://pedir.recambiosya.es/buscar_index.php',
          redirect_type = '301', update = NOW()";
          mysql_query($sql_insert); */
    }
}
if ($fitxer != 1)
    echo "<br><br>Fi del proces";
?>
