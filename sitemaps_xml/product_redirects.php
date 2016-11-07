<?php

ini_set('display_errors', 1);
require_once("protected/config/main.php");

include('protected/models/Product.php');
include('protected/models/Europ_Entradas.php');
include('lib/functions.php');
//include('protected/config/functions.php');
include('lib/scraps_lib.php');
include('lib/PSWebServiceLibrary.php');

//http://localhost/panell/product_redirects.php
// panell.recambiosya.es/product_redirects.php

$fitxer = true; //true = genera fitxer csv

if ($fitxer == true) {
    header("Content-type: application/csv; charset=UTF-8");
    $nom_file = "redirects_" . time() . ".csv";
    header("Content-Disposition: attachment; filename= " . $nom_file . "");
    header("Pragma: no-cache");
    header("Expires: 0");
}

// seleccionem articles amb estoc zero

$sql = "SELECT * FROM a_peces 
        INNER JOIN ps_product ON ps_product.id_product = a_peces.codigo+1000000
        WHERE enestoc = 0 
        ORDER BY codigo desc";

if (!$fitxer) echo $sql."<br>";

$count = 0;
$limitlinies = 50; //fem fitxers de x registres per així ser més àgils
$result = mysql_query($sql);

while (($row = mysql_fetch_array($result)) && ($count < $limitlinies)) {
    
    $peca_origen = new Peca($row["codigo"]);
    
    $car = new Europ_Entradas($row["clvaut"]);
    
    
    
    //busquem producte similar i que estigui en estoc
    $sql_substitut_1 = "SELECT Almacen.codigo as codigo 
        FROM Almacen 
        INNER JOIN entradas on entradas.Codigo = Almacen.clvaut
        INNER JOIN B100MOD on  B100MOD.Codigo = entradas.codmodelo        
        where texto = '" . $row["texto"] . "'    
                AND B100MOD.Codigo = '" . $car->getCodmodelo() . "' 
                AND Almacen.albaran = '0'
        order by entradas.Codigo desc";
    
    if (!$fitxer) echo "<br>".$sql_substitut_1."<br>";
    $url_origen = "";
    $url_desti = "";
    try {
        $row_desti = mysql_fetch_array(mysql_query($sql_substitut_1));
        //$id_desti = 1000000 + $row_desti["codigo"];
        $peca_desti = new Peca($row_desti["codigo"]);
        
        
        if (!$fitxer) echo "origen: ".$peca_origen->getUrl_es()."<br>";
        if (!$fitxer) echo "desti: ".$peca_desti->getUrl_es()."<br>";
        //if (($id_desti != 1000000) && (Product::existInPrestashop($id_origen))) {
        if (($peca_origen->getUrl_es() != "")&&($peca_desti->getUrl_es() != "")&&!existRedirect($peca_origen->getUrl_es())) {
        //if (($url_origen != "")&&($url_desti != "")) {
            //echo $url_origen . "<br>";
            //echo $url_desti . "<br>";
            echo $url_origen . ";" . $peca_desti->getUrl_es() . ";" . "301\r\n";
            $count++;
        } else {
            if ($url_origen=""){
                if (!$fitxer) echo "no existeix url del product origen";
            }else{
                if (!$fitxer) echo "origen no existeix<br>";
                if ($url_desti="") {
                    if (!$fitxer) echo "no tenim desti";
                    //cal treure http://www.recambiosya.es
                    echo "/".str_replace("http://www.recambiosya.es","",$peca_origen->getUrl_es()) . ";http://pedir.recambiosya.es/buscar_index.php;" . "301\r\n";
                }else {
                    if (existRedirect($url_origen)){
                        if (!$fitxer) echo "ja redireccionat " . $peca_origen->getUrl_es(). ' '. existRedirect($peca_origen->getUrl_es())."<br>";
                    }else{
                        if (!$fitxer) echo "no existeix redirect<br>";
                    }
                }
            }
            //echo "no substitut<br>";
        }
    } catch (Exception $e) {
        //echo "no substitut<br>";
    }
    
}

//fclose($nom_file);
?>
