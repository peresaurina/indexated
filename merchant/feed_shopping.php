<?php

error_reporting(~0);
ini_set('display_errors', 1);
require_once("../protected/config/main.php");
//include('../protected/models/Categoria.php');
include('../protected/models/Product.php');
include('../protected/models/Vehicle.php');
include('../protected/models/Peca.php');
include('../protected/models/Models.php');
include('../protected/models/Europ_Entradas.php');
include('../lib/scraps_lib.php');
include('../lib/functions.php');
include('../lib/PSWebServiceLibrary.php');

$sql ="SELECT a_peces.codigo 
        FROM a_peces 
        WHERE enestoc='1'";

$result = mysql_query($sql);
// Echo out all the details
header('Content-type: text/xml');
$nom_file = "peces_google_shopping.xml";
header("Content-Disposition: attachment; filename= " . $nom_file . "");
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');

echo '<?xml version="1.0"?>
        <rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
        <channel>
        <title>recambiosya.es</title>
        <link>http://www.recambiosya.es</link>
        <description>Google Merchant Feed</description>';
// while loop, this will cycle through the products and echo out all the variables
while ($row = mysql_fetch_array($result)) {
    
    $peca = new Peca($row["codigo"]);
    $vehicle = new Vehicle($peca->getid_vh());
    $model = new Models($vehicle->getModel());
    
    // collect all variables    
    //$title = sanear_string(quitar_abreviaturas($row["texto"]));
    
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
    $price = round($peca->getpvp() / 1.21, 0);
    if ($peca == "0") {
        $availability = 'out of stock';    
    } else {
        $availability = 'in stock';
    }
    
    $image = 'http://europiezas.recambiosya.es/imatges/' . $peca->getfp1();
    $category = "Piezas para vehículos motorizados";
    $gtin = ""; //$r['GTIN'];
    $mpn = ""; //$r['MPN'];
    // output all variables into the correct google tags
    echo "<item> 
            <title>$title</title>
            <link>".$peca->getUrl_es()."</link>
            <description>$description</description>
            <g:google_product_category>$category</g:google_product_category>
            <g:id>".$peca->getid_ps()."</g:id>
            <g:condition>$condition</g:condition>
            <g:price>$price EUR</g:price>
            <g:availability>$availability</g:availability>
            <g:image_link>$image</g:image_link>
            <g:shipping>
                <g:country>ES</g:country>
                <g:service>Standard</g:service>
                <g:price>8.5 EUR</g:price>
            </g:shipping>
            <g:gtin>$gtin</g:gtin>
            <g:brand>".$model->getMar()."</g:brand>
            <g:mpn>$mpn</g:mpn>
            <g:identifier_exists>FALSE</g:identifier_exists>
            <g:product_type>".$model->getCategoria_ps()."</g:product_type>
        </item>";
}
echo '</channel>';
echo '</rss>';

?>

