<?php

ini_set('display_errors', 1);

//if ($fitxer == false) echo "<br> Inici 0";

require_once("../protected/config/main.php");
include('../protected/models/Categoria.php');
include('../protected/models/Product.php');
include('../protected/models/Vehicle.php');
include('../protected/models/Models.php');
include('../protected/models/Europ_Entradas.php');
include('../lib/scraps_lib.php');
include('../lib/functions.php');

// panell.recambiosya.es/product_pieces_csv.php
//$codi1 = $_REQUEST[]

$fitxer = true; //true = genera fitxer csv
//if ($fitxer == false) echo "<br> Inici 1";

if ($fitxer == true) {
    header("Content-type: application/csv; charset=UTF-8");
    $nom_file = "peces_segona_ma_" . time() . ".csv";
    header("Content-Disposition: attachment; filename= " . $nom_file . "");
    header("Pragma: no-cache");
    header("Expires: 0");
}

$maxim_product = 300;
//així fem tots els productes, es per fer un update massiu.
$maxim_product = 0;

//agafem totes les peces de Almacen i validem si s'han de pujar o no
//en funció de si existeixen o no
$sql = "SELECT * FROM Almacen 
        where clvaut != '0' 
        and albaran = '0' 
        order by codigo desc";

$sql = "SELECT * FROM Almacen  
        LEFT JOIN ps_product on Almacen.codigo + 1000000 = ps_product.id_product 
        WHERE ps_product.id_product is null and clvaut != '0' 
        and albaran = '0'
        AND Almacen.texto not like '%//%'";

$sql = "SELECT * FROM a_peces";

$count = 0;
$limitlinies = 1000; //fem fitxers de x registres per així ser més àgils
$result = mysql_query($sql);

while ($row = mysql_fetch_array($result)) {
    //if (!isset($row["marca"])) $row["marca"] = "";
    $peca = new Peca($row["codigo"]);
    $vehicle = new Vehicle($peca->getid_vh());
    $model = new Models($vehicle->getModel());
    
    if ($fitxer == false)
        echo "<br> Entrada al while";
    
    $field["id"] = $peca->getid_ps();
    $car_txt = "";

    
    $row["marca"] = $model->getMar();
    $row["Nombre"] = strtoupper(marca_normalizar($car->getMarca()));
    //$categoria = Categoria::nom_categoria_model_b100($car->getCodmodelo()) . "-2";
    
    if (Product::existInPrestashop($vehicle->getId_ps())) {
        //$car_url = "http://www.recambiosya.es/fiat-punto-3-siniestro-piezas/" . $car_url_id . "-segunda-mano-piezas--1-3-g-fiat.html";
        //Posem la url ben posada, a veure si millorem posicionament
        $car_url = "http://www.recambiosya.es" . urlProduct($car_url_id);
        $car_txt = 'Puede ver el coche del que procede el recambio en este <td><a href="' . $car_url . '" target="_blank"> enlace</a></td>';
    }

    $row["texto"] = sanear_string(quitar_abreviaturas($row["texto"]));

    $categoria_peça_marca_model = Categoria::nom_categoria_peça_marca_model($row["texto"], $row["clvaut"]);
    if ($fitxer == false)
        echo "<br> Categoria peça marca model: " . $categoria_peça_marca_model;
    if (($categoria_peça_marca_model != "") && (Categoria::existInPrestashop($categoria_peça_marca_model))) {
        if ($fitxer == false)
            echo "<br> TROBAT!!!!";
        $categories = $model->getCategoria_ps() . ',' . $categoria_peça_marca_model;
    }else {
        $categories = $model->getCategoria_ps();
    }
    if ($fitxer == false)
        echo "<br> comprovar categoria";
    //comprovem que el producte no existeix i la seva categoria sí que existeixi
    if ((!Product::existInPrestashop($field["id"])) && (Categoria::existInPrestashop($model->getCategoria_ps()) && ($count < $limitlinies))) {

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
        $field["name"] = $peca->getpza(); //treure accents i coses
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
        $field["reference"] = $row["codigo"] . "-" . $peca->getref();
        $field["supplierreference"] = "";
        $field["supplier"] = $model->getMar();
        $field["manufacturer"] = $model->getMar();
        $field["ean13"] = "";
        $field["upc"] = "";
        $field["ecotax"] = "";
        $field["weigth"] = "";
        $field["quantity"] = "1";
        $field["shortdescription"] = $row["texto"] . " del modelo " . $categoria . " , " . $model->getMar() . " " . $row["refer"] . " ." . $car_txt . " . " . $anys_explicacio;
        $field["description"] = $field["shortdescription"] . " " . $row["refer"] . " " . $anys_explicacio;
        $field["tags"] = "comprar " . $row["texto"] . " " . $categoria . " usado," . $row["texto"] . "," . $row["Nombre"] . "," . $row["marca"] . ",barato, segunda mano, recambio,usado" . $string_anys;
        $field["metatitle"] = ucfirst($name);
        $field["metakeywords"] = $row["texto"] . "," . $row["Nombre"] . "," . $model->getMar() . ",baratos, segunda mano, recambio," . $string_anys;
        $field["metakeywords"] = substr($field["metakeywords"], 0, 255);
        $field["metadescription"] = "Oportunidad: " . $row["texto"] . " de " . $row["Nombre"] . " Pieza de segunda mano, a un precio muy competitivo.";
        $field["urlrewritten"] = limpiar_urlrewritten(strtolower("desguaces-" . $row["texto"] . "-" . $categoria . "-" . $model->getMar()));
        $field["textinstock"] = "";
        $field["textinback"] = "";
        $urlsfotos = '';
        
        if ($peca->getFp1() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $peca->getfp1();
        if ($peca->getFp2() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $peca->getfp2();
        if ($peca->getFp3() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $peca->getfp3();
        if ($peca->getFp4() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $peca->getfp4();
        if ($peca->getFp5() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $peca->getfp5();

        if ($vehicle->getFv1() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv1();
        if ($vehicle->getFv2() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv2();
        if ($vehicle->getFv3() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv3();
        if ($vehicle->getFv4() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv4();
        if ($vehicle->getFv5() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv5();
        if ($vehicle->getFv6() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv6();
        if ($vehicle->getFv7() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv7();
        if ($vehicle->getFv8() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv8();
        if ($vehicle->getFv9() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv9();
        if ($vehicle->getFv1() != 'SF')
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv10();

        if (isset($ids_presta_array)) {
            $urlsfotos = implode(',', $ids_presta_array);
            unset($ids_presta_array);
        } else {
            $urlsfotos = "";
        }
        
        $field["imageurls"] = $urlsfotos;
        //$field["imageurls"] = ""; //no carreguem fotos
        //6/10/2014 : afegirm la feature "Grupo Pieza"
        $field["feature"] = "Color:" . $vehicle->getCol() . ":11,Grupo Pieza:" . strtoupper($peca->getpza());
        $field["onlyonline"] = "";


        /**
          id                      Active (0/1)                        Name*
         *  Categories (x,y,z,...)  Price tax excl. Or Price tax excl   Tax rules id        
          Wholesale price         On sale (0/1)                       Discount amount
         *  Discount percent        Discount from (yyy-mm-dd)           Discount to (yyy-mm-dd)
         *  Reference #             Supplier reference #                Supplier        
         *  Manufacturer            EAN13                               UPC        
         *  Ecotax                  Weight                              Quantity
         *  Short description       Description                         Tags (x,y,z,...)        
         *  Meta-title              Meta-keywords                       Meta-description        
         *  URL rewritten           Text when in-stock                  Text if back-order allowed
          available to order       date update product                 showprice
         *  Image URLs (x,y,z,...)  Delete existent image               Feature
         *  Only available online   Condition                           idtienda
         * */
        echo $field["id"] . ";" . $field["active"] = "1" . ";" . $field["name"] . ";" .
        $field["categories"] . ";" . $field["pricetaxexcluded"] . ";" . $field["taxrulesid"] . ";" .
        $field["wholesaleprice"] . ";" . $field["onsale"] . ";" . $field["discountamount"] . ";" .
        $field["discountpecent"] . ";" . $field["discountfrom"] . ";" . $field["discountto"] . ";" .
        $field["reference"] . ";" . $field["supplierreference"] . ";" . $field["supplier"] . ";" .
        $field["manufacturer"] . ";" . $field["ean13"] . ";" . $field["upc"] . ";" .
        $field["ecotax"] . ";" . $field["weigth"] . ";" . $field["quantity"] . ";" .
        $field["shortdescription"] . ";" . $field["description"] . ";" . $field["tags"] . ";" .
        $field["metatitle"] . ";" . $field["metakeywords"] . ";" . $field["metadescription"] . ";" .
        $field["urlrewritten"] . ";" . $field["textinstock"] . ";" . $field["textinback"] . ";" .
        ";;;" .
        $field["imageurls"] . ";;" . $field["feature"] . ";" .
        $field["onlyonline"] . ";;" . "RecambiosYa\r\n";
        if ($fitxer == false)
            echo "<br> count" . $count;
        $count++;
    } else {
        //si el codi del producte ja existeix
        //haurem d'activar-lo o desactivar-lo
        //falta saber camp de recambiosya
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
    }
}

if ($fitxer != true) {
    print_r($notes);
}else{
    //fclose($nom_file);
}
?>
