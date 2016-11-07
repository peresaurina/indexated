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

// Set the xml header

$sql ="SELECT recya16.a_peces.codigo FROM recya16.a_peces 
        INNER JOIN recya16.a_vehicles ON a_vehicles.id_vh = a_peces.id_vh
        INNER JOIN recya16.ps_product ON 
			recya16.ps_product.id_product = replace(recya16.a_peces.codigo,'C','')+100000
        
        order by recya16.a_peces.codigo desc
		";

$result = mysql_query($sql);
// Echo out all the details

header('Content-type: text/xml');
$nom_file = "oxl_feed.xml";
header("Content-Disposition: attachment; filename= " . $nom_file . "");
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');


echo '<?xml version="1.0" encoding ="UTF-8" ?>';
echo '<ADS>';
       

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
    $price = $peca->getpvp(); //preu amb iva
    if ($row['albaran'] == "0") {        
            $availability = 'in stock';        
    } else {
        $availability = 'in stock';
    }
    
    //$image = 'http://www.recambiosya.es/imatges/' . $row["foto"];
    $image = "http://europiezas.recambiosya.es/imatges/" . $peca->getfp1();
    $category = "Piezas para vehículos motorizados";
    $gtin = ""; //$r['GTIN'];
    $mpn = ""; //$r['MPN'];
    // output all variables into the correct google tags
    // OLX CSV
    
    echo "<AD> 
            <ID>".$peca->getid_ps()."</ID>
            <TITLE><![CDATA[".$title."]]></TITLE>
            <DESCRIPTION><![CDATA[".$description."]]></DESCRIPTION> 
            <DATE>".date("Y-m-d")."</DATE>
            <EMAIL><![CDATA[atencionclientes@recambiosya.es]]></EMAIL>      
            <PHONE>633367831</PHONE>  
            <ADDRESS><![CDATA[Av. Mas Pins 98]]></ADDRESS>
            <ZIP_CODE><![CDATA[17457]]></ZIP_CODE>
            <LOCATION_COUNTRY>SP</LOCATION_COUNTRY>
            <LOCATION_STATE><![CDATA[ GI ]]></LOCATION_STATE>
            <LOCATION_CITY><![CDATA[ GIRONA ]]></LOCATION_CITY>
            <CATEGORY><![CDATA[ 377 ]]></CATEGORY>
            <YEAR></YEAR>
            <MAKE>$brand</MAKE>
            <MODEL>$categoria</MODEL>
            <MILEAGE></MILEAGE>
            <CONDITION>used</CONDITION>      
            <IMAGE_URL><![CDATA[ $image ]]></IMAGE_URL>            
            <PRICE>$price</PRICE>
            <CURRENCY>EUR</CURRENCY>
        </AD>";
}
echo '</ADS>';


?>

