<?php

error_reporting(~0);
ini_set('display_errors', 1);
ini_set('max_execution_time', 3000);

require_once("../protected/config/main.php");
require_once("../protected/config/functions.php");
include('../protected/models/Product.php');
include('../lib/functions.php');
include('../lib/scraps_lib.php');
include('../protected/models/Europ_Entradas.php');
// http://panell.recambiosya.es/merchant/trovit_feed.php?linies=50
// localhost/panell/merchant/trovit_feed.php?linies=50
// Set the xml header
// també útil per anuncit

$sql ="SELECT a_peces.codigo 
        FROM a_peces 
        ";

$result = mysql_query($sql);
// Echo out all the details

header('Content-type: application/rss+xml; charset=utf-8');

echo "<?xml version='1.0' encoding='UTF-8'?>";
echo '<trovit>';


// while loop, this will cycle through the products and echo out all the variables
while ($row = mysql_fetch_array($result)) {

    $peca = new Peca($row["codigo"]);
    $vehicle = new Vehicle($peca->getid_vh());
    $model = new Models($vehicle->getModel());
    
    
   

    $anys = array_year_category($model->getCategoria_ps());
    
    if (isset($anys)&&is_array($anys)) {
        try{
            $title = ucwords(strtolower(strtoupper(sanear_string(quitar_abreviaturas($peca->getpza())))));
            $description = ucfirst(strtolower($title) . " de un vehiculo despiezado, del modelo: " 
            . ucwords(strtolower($model->getCategoria_ps())) . ", de la marca " . ucwords(strtolower($model->getMar())).
                    ". Modelo de los años: " . implode(' ', $anys) );
        }catch (Exception $e){
            //$title = ucwords(strtolower(strtoupper($row["texto"]) . " " .
            //    strtoupper(Categoria::nom_categoria_model_b100($car->getCodmodelo()))));
            $title = ucwords(strtolower(strtoupper(sanear_string(quitar_abreviaturas($peca->getpza())))));
           
            $description = ucfirst(strtolower($title) . " de un vehiculo despiezado, del modelo: " 
            . ucwords(strtolower($model->getCategoria_ps())) . ", de la marca " . ucwords(strtolower($model->getMar())));
        }
    } else {
        //$title = ucwords(strtolower(strtoupper($row["texto"]) . " " .
        //        strtoupper(Categoria::nom_categoria_model_b100($car->getCodmodelo()))));
        $title = ucwords(strtolower(strtoupper(sanear_string(quitar_abreviaturas($row["texto"])))));
           
        $description = ucfirst(strtolower($title) . " de un vehiculo despiezado, del modelo: " 
            . ucwords(strtolower($model->getCategoria_ps())) . ", de la marca " . ucwords(strtolower($model->getMar())));
    }
    if (strlen($title) > 30) {
        $title = substr($title, 0, 30);
    }

    if (strlen($description) > 500) {
        $description = substr($description, 0, 496). "...";
    }


    $condition = "used";
    $price = utf8_encode($peca->getpvp());//preu amb IVA
    if ($peca->getEnestoc() == "0") {
        $expiration_date = null;
        $availability = 'in stock';
    } else {
        //$availability = 'out of stock';
        $expiration_date = date("2014-01-01");
    }
    
    $image = 'http://europiezas.recambiosya.es/imatges/' . $row["foto"];
        
    // output all variables into the correct google tags
    // OLX CSV
    $category = "Car parts and Accessories";
    $address = "Avinguda Mas Pins 98";
    $shipping_cost = 8.5;
    
    //<mobile_url><![CDATA[" . $link . "]]></mobile_url>
    //<category><![CDATA[" .$category. "]]></category>
    //<address><![CDATA[" .$address. "]]></address>  
    //<shipping_cost><![CDATA[" .$shipping_cost. "]]></shipping_cost> 
    echo "<ad> 
            <id><![CDATA[".$peca->getid_ps()."]]></id>
            <title><![CDATA[".$title." - ".$model->getCategoria_ps()."]]></title>
            <url><![CDATA[".$peca->getUrl_es()."]]></url>
            <content><![CDATA[".$description."]]></content>     
            <category><![CDATA[" .$category. "]]></category>
            <price><![CDATA[".$price."]]></price>   
            <shipping_cost><![CDATA[" .$shipping_cost. "]]></shipping_cost>
            <address><![CDATA[" .$address. "]]></address>    
            <city_area><![CDATA[Girona]]></city_area>
            <city><![CDATA[Girona]]></city>
            <region><![CDATA[Girona]]></region>
            <postcode><![CDATA[17457]]></postcode>
            
            <pictures>
                <picture>
                    <picture_url><![CDATA[$image]]></picture_url>
                    <picture_title><![CDATA[$title]]></picture_title>
                </picture>
            </pictures>
            
            <date><![CDATA[". date('Y-m-d') ."]]></date>
            <expiration_date><![CDATA[$expiration_date]]></expiration_date> 
            <make><![CDATA[ ". $brand." ]]></make>
            <model><![CDATA[ ".$categoria." ]]></model>
        </ad>";
}
echo '</trovit>';
?>

