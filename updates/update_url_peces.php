<?php

ini_set('display_errors', 1);
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

// http://localhost/panell16/update/update_url_peces.php
// panell16.recambiosya.es/updates/update_url_peces.php
// 
// seleccionem totes peces que estant a prestashop, en estoc  i 
//  que no tenen una url assignada

$sql_piezas = "Select * from a_peces 
                    INNER JOIN ps_product ON ps_product.id_product = a_peces.codigo + 1000000
                    where   a_peces.enestoc = '1'
                            and a_peces.url_es is null
                            limit 0,1000
                            ";
$result_piezas = mysql_query($sql_piezas);

$webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, false);

while ($row_piezas = mysql_fetch_array($result_piezas)) {
    $peca = new Peca($row_piezas["codigo"]);
    //print_r($peca);    

    try {
        //UTILITZANT WEBSERVICES        
        // http://localhost/prestashop/api/images/products/3000943/511
        // Define the resource
        $opt = array('resource' => 'products');
        // Define the resource id to modify
        $opt['id'] = $peca->getid_ps();
        // Call the web service, recuperate the XML file
        $xml = $webService->get($opt);

        $resources = $xml->product[0]->associations[0];
        //print_r($resources);
        $json_xml = json_encode($xml);
        //$long = $xml->product[0]->associations[0]->images[0]->image[0]->id;
        $url_lin = (string) $xml->product[0]->link_rewrite[0]->language;

        $categoria_id = (string) $xml->product[0]->associations[0]->categories->category->id;
        $opt_cat = array('resource' => 'categories');
        $opt_cat['id'] = $categoria_id;
        $xml_cat = $webService->get($opt_cat);
        //print_r($xml_cat);        
        $nom_cat = (string) $xml_cat->category[0]->link_rewrite[0]->language;
        $nom_cat = preg_replace("# #", "-", $nom_cat);
        $link = strtolower("/" . $nom_cat . "/" . $peca->getid_ps() . "-" . $url_lin . ".html");
    } catch (PrestaShopWebserviceException $ex) {
        // Shows a message related to the error
        echo 'Other error: <br />' . $ex->getMessage();
        $link = "/almera-4-2002-2006-2-2-d-5p/" . $peca->getid_ps() . "-recambios-piloto-trasero-derecho-nissan.html";
        return $pieza_url;
    } catch (Exception $e) {
        echo 'No hi ha url';
        $pieza_url = "/almera-4-2002-2006-2-2-d-5p/" . $peca->getid_ps() . "-recambios-piloto-trasero-derecho-nissan.html";
        $link = $pieza_url;
    }

    //$link = urlProduct($peca->getid_ps());
    //echo "<br>".$link;
    $peca->setUrl_es("http://www.recambiosya.es" . $link);
    $peca->insertIntoDataBase();
    ini_set("max_execution_time", 30);
    //echo "<br>" . $peca->getid_ps() . " - " . $peca->getUrl_es();
    //echo "<br>--------------------------<br>";
}
$sql = "Select count(*) from a_peces 
                    INNER JOIN ps_product ON ps_product.id_product = a_peces.codigo + 1000000
                    where   a_peces.enestoc = '1'
                            and a_peces.url_es is null
                            limit 0,1000
                            ";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);
echo "<br><br>Pendent : ".$row["count(*)"];
echo "<br><br>Proces acabat amb success Peces !!! ;P";

// --------------- cotxes --------------------------
$sql_piezas = "Select * from a_vehicles 
                    INNER JOIN ps_product ON ps_product.id_product = replace(a_vehicles.id_vh,'C','')+8000000
                    where   a_vehicles.url_es is null
                            limit 0,1000
                            ";
$result_piezas = mysql_query($sql_piezas);

$webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, false);

while ($row_piezas = mysql_fetch_array($result_piezas)) {
    $vehicle = new Vehicle($row_piezas["id_vh"]);
    //print_r($peca);    

    try {
        //UTILITZANT WEBSERVICES        
        // http://localhost/prestashop/api/images/products/3000943/511
        // Define the resource
        $opt = array('resource' => 'products');
        // Define the resource id to modify
        $opt['id'] = $vehicle->getId_ps();
        // Call the web service, recuperate the XML file
        $xml = $webService->get($opt);

        $resources = $xml->product[0]->associations[0];
        //print_r($resources);
        $json_xml = json_encode($xml);
        //$long = $xml->product[0]->associations[0]->images[0]->image[0]->id;
        $url_lin = (string) $xml->product[0]->link_rewrite[0]->language;

        $categoria_id = (string) $xml->product[0]->associations[0]->categories->category->id;
        $opt_cat = array('resource' => 'categories');
        $opt_cat['id'] = $categoria_id;
        $xml_cat = $webService->get($opt_cat);
        //print_r($xml_cat);        
        $nom_cat = (string) $xml_cat->category[0]->link_rewrite[0]->language;
        $nom_cat = preg_replace("# #", "-", $nom_cat);
        $link = strtolower("/" . $nom_cat . "/" . $vehicle->getid_ps() . "-" . $url_lin . ".html");
    } catch (PrestaShopWebserviceException $ex) {
        // Shows a message related to the error
        echo 'Other error: <br />' . $ex->getMessage();
        $link = "/almera-4-2002-2006-2-2-d-5p/" . $vehicle->getid_ps() . "-recambios-piloto-trasero-derecho-nissan.html";
        return $pieza_url;
    } catch (Exception $e) {
        echo 'No hi ha url';
        $pieza_url = "/almera-4-2002-2006-2-2-d-5p/" . $vehicle->getid_ps() . "-recambios-piloto-trasero-derecho-nissan.html";
        $link = $pieza_url;
    }

    //$link = urlProduct($peca->getid_ps());
    //echo "<br>".$link;
    $vehicle->setUrl_es("http://www.recambiosya.es" . $link);
    $vehicle->insertIntoDataBase();
    ini_set("max_execution_time", 30);
    echo "<br>" . $vehicle->getid_ps() . " - " . $vehicle->getUrl_es();
    echo "<br>--------------------------";
}
$sql = "Select count(*) from a_vehicles 
                    INNER JOIN ps_product ON ps_product.id_product = replace(a_vehicles.id_vh,'C','')+8000000
                    where   a_vehicles.url_es is null
                            limit 0,1000
                            ";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);
echo "<br><br>Pendent cotxes : ".$row["count(*)"];
echo "<br><br>Proces acabat amb success Peces !!! ;P";


?>
