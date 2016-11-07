<?php

ini_set('display_errors', 1);

// localhost/panell16/mailing/product_MailChimp.php

require_once("../protected/config/main.php");
//include('protected/models/Categoria.php');
include('../protected/models/Product.php');
include('../protected/models/Vehicle.php');
include('../protected/models/Models.php');
include('../protected/models/Peca.php');
include('../protected/models/Europ_Entradas.php');
include('../lib/scraps_lib.php');
include('../lib/functions.php');
include('../lib/PSWebServiceLibrary.php');
//ini_set('max_execution_time', 3000);

$fitxer = (isset($_REQUEST["fitxer"]) ? $_REQUEST["fitxer"] : 0); //true = genera fitxer csv
// Script per gestió de productes sense estoc que no tenen redirect
// 
//http://localhost/panell/product_no_estoc_no_redirects.php
// panell.recambiosya.es/product_MailChimp.php

//Articles en Almacen que s'han venut, i que estant donat d'altes a prestashop
$sql = "SELECT * FROM entradas where foto != '' order by codigo desc limit 0,20";

$sql = "Select * from ($sql) as alias order by marca asc";

$sql = "SELECT ps_product.id_product FROM ps_product 
                INNER JOIN ps_product_lang on ps_product_lang.id_product = ps_product.id_product
                WHERE ps_product.id_product > 8000000                 
                order by ps_product.id_product desc 
                limit 0,20";

//echo $sql;

$result = mysql_query($sql);

echo "Aquests són els últims cotxes que han arribat per ésser desballestats :<br><br><br>";

echo "<table>";
echo "<tbody>";
$link_track = "?utm_source=Email&utm_medium=Email&utm_term=nous_productes_wk&utm_content=nous_productes_wk&utm_campaign=nous_productes_wk";
while ($row = mysql_fetch_array($result)) {
    //sql per si tenen redirect creat
    $car = new Vehicle(preg_replace('#^8#', "C0", $row["id_product"]));
    $model = new Models($car->getModel());
    $id_origen = $row["id_product"];
    

    //$url_origen = "http://www.recambiosya.es" . urlProduct($id_origen);
    $urlfoto = "http://europiezas.recambiosya.es/imatges/" . $car->getFv1();
    
    echo "<tr>";
    echo "<td><img src='$urlfoto' alt='".$model->getCategoria_ps()."' height='48' width='80'></td>";
    echo "<td>".$model->getMar()."</td>";
    $cotxe_link =  '<a href="' . $car->getUrl_es().$link_track . '" target="_blank" style="color:black">' . ucwords(strtoupper($model->getCategoria_ps())).'</a>';
    echo "<td><strong>".$cotxe_link."</strong></td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";

echo "<br><br>Fi del proces";
?>
