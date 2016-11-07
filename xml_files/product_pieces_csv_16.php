<?php

ini_set('display_errors', 1);

//if ($fitxer == false) echo "<br> Inici 0";

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

// panell16.recambiosya.es/xml_files/product_pieces_csv_16.php
// localhost/panell16/xml_files/product_pieces_csv_16.php
//$codi1 = $_REQUEST[]

$fitxer = true; //true = genera fitxer csv
//if ($fitxer == false) echo "<br> Inici 1";

if ($fitxer == true) {
    header("Content-type: application/csv; charset=UTF-8");
    $nom_file = "peces-16-" . time() . ".csv";
    header("Content-Disposition: attachment; filename= " . $nom_file . "");
    header("Pragma: no-cache");
    header("Expires: 0");
}

//agafem totes les peces que tenen un cotxe
$sql = "SELECT * FROM recya16.a_peces 
        INNER JOIN recya16.a_vehicles ON a_vehicles.id_vh = a_peces.id_vh
        ";
$sql ="SELECT recya16.a_peces.codigo FROM recya16.a_peces 
        INNER JOIN recya16.a_vehicles ON a_vehicles.id_vh = a_peces.id_vh
        LEFT JOIN recya16.ps_product ON 
			recya16.ps_product.id_product = replace(recya16.a_peces.codigo,'C','')+1000000
        WHERE recya16.ps_product.id_product is null and a_peces.updatedtried = '0'
        order by recya16.a_peces.codigo desc
		";
//treiem la restricció de updatedtried
$sql ="SELECT recya16.a_peces.codigo FROM recya16.a_peces 
        INNER JOIN recya16.a_vehicles ON a_vehicles.id_vh = a_peces.id_vh
        LEFT JOIN recya16.ps_product ON 
			recya16.ps_product.id_product = replace(recya16.a_peces.codigo,'C','')+1000000
        WHERE recya16.ps_product.id_product is null 
        order by recya16.a_peces.codigo desc
		";

//haurem de fer el mateix procés en peces que no tenen cotxe, i assignar-les
//a una cosa genèrica.
/*
 * SELECT * FROM recya16.a_peces 
        LEFT JOIN recya16.a_vehicles ON a_vehicles.id_vh = a_peces.id_vh
        WHERE a_vehicles.id_vh is null
 * Actualment 1200 peces
 */

$count = 0;
$limitlinies = 199; //fem fitxers de x registres per així ser més àgils
$result = mysql_query($sql);

while ($row = mysql_fetch_array($result)) {
    
    if ($count > $limitlinies) break;
    
    $peca = new Peca($row["codigo"]);
    $vehicle = new Vehicle($peca->getid_vh());
    $model = new Models($vehicle->getModel());
    $peca->setUpdatedtried('1');
    $peca->insertIntoDataBase();
    if ($fitxer == false)
        echo "<br>Entrada al while";
    
    $car_txt = "";
    
    if (Product::existInPrestashop($vehicle->getId_ps())) {
        $car_url = "http://www.recambiosya.es/fiat-punto-3-siniestro-piezas/" . $vehicle->getId_ps() . "-segunda-mano-piezas--1-3-g-fiat.html";
        //Posem la url ben posada, a veure si millorem posicionament
        //$car_url = "http://www.recambiosya.es" . urlProduct($vehicle->getId_ps());
        $car_txt = '. El recambio procede de este <td><a href="' . $vehicle->getUrl_es() . '" target="_blank">'.$model->getModbase().'</a></td>';
    }

    $row["texto"] = sanear_string(quitar_abreviaturas($peca->getpza()));

    
    if ($fitxer == false)
        echo "<br> Categoria peça marca model: " .$model->getCategoria_ps() ;
    /*
    if (($categoria_peça_marca_model != "") && (Categoria::existInPrestashop($categoria_peça_marca_model))) {
        if ($fitxer == false)
            echo "<br> TROBAT!!!!";
        $categories = $model->getCategoria_ps() . ',' . $categoria_peça_marca_model;
    }else {
        $categories = $model->getCategoria_ps();
    }
    */
    $categories = $model->getCategoria_ps();
    
    if ($fitxer == false)
        echo "<br> comprovar categoria";
    //comprovem que el producte no existeix i la seva categoria sí que existeixi
    if ((!Product::existInPrestashop($peca->getid_ps())) && (Categoria::existInPrestashop($model->getCategoria_ps()))) {

        //if ((Categoria::existInPrestashop($categoria) && ($count < $limitlinies))) {
        //Nou H1 de producte
        $anys = array_year_category($model->getCategoria_ps());
        $anys_explicacio = "";
        if (isset($anys)) {
            $name = strtoupper($row["texto"]) . " " .
                    strtoupper($model->getCategoria_ps());
            //." " . implode(' ', $anys);
            $anys_explicacio = "Pieza para vehiculos de los años: " . implode(', ', $anys);
            ;
            try {
                $string_anys = implode(', ', $anys);
            } catch (Exception $e) {
                $string_anys = "";
            }
        } else {
            $name = strtoupper($row["texto"]) . " " .
                    strtoupper($model->getCategoria_ps());
            $string_anys = "";
        }
        if (strlen($name) > 128) {
            $name = substr($name, 0, 124) . "...";
        }
        //------------------------------

        $field["active"] = "1";
        $field["name"] = strtoupper($peca->getpza()); //treure accents i coses
        //aquí el nombre correspon al model
        $field["categories"] = $categories;
        $field["pricetaxexcluded"] = round($peca->getpvp() / 1.21, 3); //hi posem un 10% de recàrrec
        $field["taxrulesid"] = "1";
        $field["wholesaleprice"] = "";
        $field["onsale"] = "";
        $field["discountamount"] = "";
        $field["discountpecent"] = "";
        $field["discountfrom"] = "";
        $field["discountto"] = "";
        $field["reference"] = substr($row["codigo"] . "-" . $peca->getref(),0,32);
        $field["supplierreference"] = "";
        $field["supplier"] = $model->getMar();
        $field["manufacturer"] = $model->getMar();
        $field["ean13"] = "";
        $field["upc"] = "";
        $field["ecotax"] = "";
        $field["weigth"] = "";
        $field["quantity"] = "1";
        
        $short_description = $row["texto"] . " del modelo " . $model->getCategoria_ps() . " , " . $model->getMar() . " " . $car_txt . ". ";        
        if ($vehicle->getCm() != ""){ $short_description.= "<br>Tipo de motor del vehiculo al que pertenece : ".$vehicle->getCm().".<br> ";}        
        $field["shortdescription"] = substr($short_description . $anys_explicacio,0,800);
        
        $field["description"] = $row["texto"] . " del modelo " . $model->getCategoria_ps() . " , " . $model->getMar() . " " . $car_txt . ". " . $anys_explicacio;
        $field["tags"] = "comprar " . $row["texto"] . " " . $model->getCategoria_ps() . " usado," . $row["texto"] . "," . $model->getMar() . ",barato, segunda mano, recambio,usado," . $string_anys;
        $field["metatitle"] = ucwords($peca->getpza()." ".$model->getModbase()."|RecambiosYa");
        $field["metakeywords"] = $row["texto"] . "," . $model->getMar() . ",baratos, segunda mano, recambio," . $string_anys;
        $field["metakeywords"] = substr($field["metakeywords"], 0, 255);
        $field["metadescription"] = "En desguace " . $row["texto"] . " de " . $model->getCategoria_ps() . ". Piezas unicas, con fotografias, en 24h.";
        $field["urlrewritten"] = limpiar_urlrewritten(strtolower($peca->getpza()."-desguace"));
        $field["textinstock"] = "";
        $field["textinback"] = "";
        
        $ids_presta_array = null;
        
        $urlsfotos = '';
        if ($peca->getFp1() != '')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $peca->getfp1();
        if ($peca->getFp2() != '')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $peca->getfp2();
        if ($peca->getFp3() != '')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $peca->getfp3();
        if ($peca->getFp4() != '')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $peca->getfp4();
        if ($peca->getFp5() != '')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $peca->getfp5();

        if ($vehicle->getFv1() != 'SF')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv1();
        if ($vehicle->getFv2() != 'SF')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv2();
        if ($vehicle->getFv3() != 'SF')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv3();
        if ($vehicle->getFv4() != 'SF')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv4();
        if ($vehicle->getFv5() != 'SF')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv5();
        if ($vehicle->getFv6() != 'SF')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv6();
        if ($vehicle->getFv7() != 'SF')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv7();
        if ($vehicle->getFv8() != 'SF')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv8();
        if ($vehicle->getFv9() != 'SF')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv9();
        if ($vehicle->getFv10() != 'SF')
            $ids_presta_array[] = "http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv10();

        if (isset($ids_presta_array)) {
            $urlsfotos = implode(',', $ids_presta_array);
            unset($ids_presta_array);
        } else {
            $urlsfotos = "";
        }
        
        $field["imageurls"] = $urlsfotos;
        //$field["imageurls"] = ""; //no carreguem fotos        
        $field["feature"] = "Color:" . $vehicle->getCol() . ":11,Grupo Pieza:" . strtoupper($peca->getpza());
        $field["onlyonline"] = "";        
        //Camps 1.6 Afegits
        $field["width"] ="";
        $field["height"]="";
        $field["depth"] ="";
        $field["minimalquantity"] ="";
        $field["visibility"] ="";
        $field["aditionalshipingcost"] = 0;
        $field["unity"] = "";
        $field["uniteprice"] = "";
        $field["availablefororder"] ="1";
        $field["availabledate"] ="";
        $field["productavailabledate"] ="";
        $field["deleteimages"] ="1";
        $field["availableonlineonly"] ="1";
        $field["condition"]="used";
        $field["customizable"]="0";
        $field["uploadablefiles"] ="";
        $field["textfields"]="";
        $field["outofstock"]="";
        //$field["productcreationdate"] = date("Y-m-d");
        $field["productcreationdate"] = "";
        $field["showprice"] = '1';
        $field["advancedstockmanagement"] = '';
        $field["dependsonstock"] = '';
        $field["warehouse"] = '';
             
        /** CSV de PRODUCT 1.6
         * 
            ID - Active (0/1) - Name * - Categories (x,y,z...) - Price tax excluded or Price tax included
            Tax rules ID - Wholesale price - On sale (0/1) - Discount amount - Discount percent
            Discount from (yyyy-mm-dd) - Discount to (yyyy-mm-dd) - Reference # - Supplier reference #
            Supplier - Manufacturer - EAN13 - UPC - Ecotax - Width - Height - Depth
            Weight - Quantity - Minimal quantity - Visibility - Additional shipping cost
            Unity - Unit price - Short description - Description - Tags (x,y,z...)
            Meta title - Meta keywords - Meta description - URL rewritten - Text when in stock
            
         * Text when backorder allowed - Available for order (0 = No, 1 = Yes)
            Product available date - Product creation date - Show price (0 = No, 1 = Yes)
            Image URLs (x,y,z...) - Delete existing images (0 = No, 1 = Yes)
            Feature(Name:Value:Position) - Available online only (0 = No, 1 = Yes)
            Condition - Customizable (0 = No, 1 = Yes) - Uploadable files (0 = No, 1 = Yes)
            Text fields (0 = No, 1 = Yes)- Out of stock - ID / Name of shop
            Advanced stock management - Depends On Stock - Warehouse
        * */
        
        echo $peca->getid_ps() . ";" . $field["active"] = "1" . ";" . $field["name"] . ";" .
                    $field["categories"] . ";" . $field["pricetaxexcluded"] . ";" .
             $field["taxrulesid"] . ";" . $field["wholesaleprice"] . ";" . $field["onsale"] . ";" . 
                    $field["discountamount"] . ";" . $field["discountpecent"] . ";" . 
            $field["discountfrom"] . ";" . $field["discountto"] . ";" . $field["reference"] . ";" . $field["supplierreference"] . ";" . 
            $field["supplier"] . ";" . $field["manufacturer"] . ";" . $field["ean13"] . ";" . $field["upc"] . ";" .
                    $field["ecotax"] . ";" . $field["width"] . ";". $field["height"] . ";". $field["depth"] . ";". 
            $field["weigth"] . ";" . $field["quantity"] . ";" . $field["minimalquantity"].";".$field["visibility"] .";".
                    $field["aditionalshipingcost"] .";".
            $field["unity"] . ";" .  $field["uniteprice"] . ";" . $field["shortdescription"] . ";" . $field["description"] . ";" . 
                    $field["tags"] . ";" .
            $field["metatitle"] . ";" . $field["metakeywords"] . ";" . $field["metadescription"] . ";" .
                    $field["urlrewritten"] . ";" . $field["textinstock"] . ";" . 
            $field["textinback"] . ";" .$field["availablefororder"].";".
            $field["productavailabledate"].";".$field["productcreationdate"].";".$field["showprice"].";".
            $field["imageurls"] .";".$field["deleteimages"].";".
            $field["feature"] . ";" .$field["availableonlineonly"].";".
            $field["condition"] .";".$field["customizable"].";".$field["uploadablefiles"].";".
            $field["textfields"].";". $field["outofstock"].";". "RecambiosYa".";".
            $field["advancedstockmanagement"] .";".$field["dependsonstock"].";".$field["warehouse"] = ''."\r\n";
        
        
        if ($fitxer == false)
            echo "<br> count" . $count;
        $count++;
    } else {
        //si el codi del producte ja existeix
        //haurem d'activar-lo o desactivar-lo
        //falta saber camp de recambiosya
        /*
        if (Product::existInPrestashop($field["id"])) {
            $notes[] = "CODI EXISTEIX: " . $field["id"] . " categoria: " . $categoria . "<br>";
        } elseif (!Categoria::existInPrestashop($categoria)) {
            $notes[] = "Categoria no existeix. codi: " . $field["id"] . " categoria: " . $categoria . "<br>";
        } elseif ($count >= $limitlinies) {
            $notes[] = "ja en tenim el limit linies " . $limitlinies . "<br>";
            break;
        } else {
            $notes[] = "Alguna cosa passa. I no sabem...." . $field["id"] . " categoria: " . $categoria . "<br>";
        }
        */
    }
    ini_set("max_execution_time", 30);
}

if ($fitxer != true) {
    print_r($notes);
}else{
    //fclose($nom_file);
}
?>
