<?php

ini_set('display_errors', 1);
require_once("../protected/config/main.php");
//include('protected/models/Categoria.php');
include('../protected/models/Product.php');
include('../protected/models/Vehicle.php');
include('../protected/models/Models.php');
include('../protected/models/Europ_Entradas.php');
include('../lib/scraps_lib.php');
include('../lib/functions.php');

/* creació del fitxer dels cotxes sinistrats de la base de dades
 * Prensado => significa que el cotxe ja no està en estoc
 */

// localhost/panell16/xml_files/product_cotxes_csv.php
// panell16.recambiosya.es/xml_files/product_cotxes_csv.php

$fitxer = true;

if ($fitxer == true) {
    header("Content-type: application/csv; charset=UTF-8");
    //header("Content-type: application/csv");
    header("Content-Disposition: attachment; filename=coches_sinistrats" . time() . ".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
}

//$sql = "SELECT * FROM entradas WHERE FOTO1 LIKE  '%jpg'"; //agafem tots els cotxes possibles

$sql = "SELECT * FROM a_vehicles where id_vh like 'C%'"; //agafem tots els cotxes possibles
$result = mysql_query($sql);
$count = 1; //fem fitxers de 300 registres per així ser més àgils

while ($row = mysql_fetch_array($result)) {
    //$car = new Europ_Entradas($row["codigo"]);   
    $vehicle = new Vehicle($row["id_vh"]);
    $model = new Models($row["model"]);
    //$row["texto"] = sanear_string(quitar_abreviaturas($row["texto"]));
    //$row["Nombre"] = strtoupper(marca_normalizar($car->getMarca()));
    // Creem categoria del cotxe segons recanviscategoriacotxes_csv.php
    //$categoria = treure_color_en_categoria(treure_marca_en_categoria(sub_model_name($car->getModelo(), $car->getVersion())));
    //comprovem que el producte no existeix i la seva categoria sí que existeixi
    //echo "id_ps : " . $vehicle->getId_ps() . "<br>";
    //echo "categoria : " . $model->getCategoria_ps() . "<br>";

    //if((!Product::existInPrestashop($vehicle->getId_ps()))&&(Categoria::existInPrestashop($model->getCategoria_ps()))){
    if ((!Product::existInPrestashop($vehicle->getId_ps())) && (!Categoria::existInPrestashop($model->getCategoria_ps()))) {


        //així fem tots els productes, es per fer un update massiu.
        //if((Categoria::existInPrestashop($categoria))){

        /** CSV de PRODUCT 1.5
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
        
        /** CSV de PRODUCT 1.6
            ID - Active (0/1) - Name * - Categories (x,y,z...) - Price tax excluded or Price tax included
            Tax rules ID - Wholesale price - On sale (0/1) - Discount amount - Discount percent
            Discount from (yyyy-mm-dd) - Discount to (yyyy-mm-dd) - Reference # - Supplier reference #
            Supplier - Manufacturer - EAN13 - UPC - Ecotax - Width - Height - Depth
            Weight - Quantity - Minimal quantity - Visibility - Additional shipping cost
            Unity - Unit price - Short description - Description - Tags (x,y,z...)
            Meta title - Meta keywords - Meta description - URL rewritten - Text when in stock
            Text when backorder allowed - Available for order (0 = No, 1 = Yes)
            Product available date - Product creation date - Show price (0 = No, 1 = Yes)
            Image URLs (x,y,z...) - Delete existing images (0 = No, 1 = Yes)
            Feature(Name:Value:Position) - Available online only (0 = No, 1 = Yes)
            Condition - Customizable (0 = No, 1 = Yes) - Uploadable files (0 = No, 1 = Yes)
            Text fields (0 = No, 1 = Yes)- Out of stock - ID / Name of shop
            Advanced stock management - Depends On Stock - Warehouse
        * */
        $field["active"] = "1";

        //Nou H1 de producte
        $anys_string = "";
        $anys = array_year_category($model->getCategoria_ps());
        if (isset($anys)) {
            $anys_string = "Años: " . implode(', ', $anys);
        } else {
            $anys_string = "";
        }

        $field["name"] = ucwords(strtoupper($model->getCategoria_ps() . " PARA RECAMBIOS" . " " . $vehicle->getMat()));
        //aquí el nombre correspon al model
        //$field["categories"]= strtoupper($car->getCategoria());
        $field["categories"] = $model->getCategoria_ps();
        $price = 0; //el cotxes no tenen preu de venda        
        $field["pricetaxexcluded"] = 0; //hi posem un 10% de recàrrec
        $field["taxrulesid"] = "1";
        $field["wholesaleprice"] = "";
        $field["onsale"] = "";
        $field["discountamount"] = "";
        $field["discountpecent"] = "";
        $field["discountfrom"] = "";
        $field["discountto"] = "";
        $field["reference"] = $vehicle->getCm() . " - " . $vehicle->getMat();
        $field["supplierreference"] = "";
        $field["supplier"] = $model->getMar();
        $field["manufacturer"] = "";
        $field["ean13"] = "";
        $field["upc"] = "";
        $field["ecotax"] = "";
        $field["weigth"] = "";
        $field["quantity"] = "1";
        $short_description = "Coche para despiezar en nuestro desguace  " .$model->getMar()." ". $model->getCategoria_ps() . ", versión " . $vehicle->getVer() . " " . $vehicle->getCbs() . " " . $anys_string;
        //$short_description = "Coche para despiezar del modelo ".$categoria." de la marca ".$car->getMarca().", versión ".$categoria." ".$car->getCombustible();

        if (strlen($short_description) > 800) {
            $short_description = substr($short_description, 0, 790) . "...";
        }

        $field["shortdescription"] = $short_description;
        $field["description"] = "Las piezas de este vehículo estan preparadas para ser desmontadas en nuestro desguace y ofrecerle un precio ajustado a su medida, para así conseguir aprovechar piezas usadas de otros vehículos para su vehículo. Debe tener claro que la pieza que necesita puede ser compatible entre vehículos de igual marca y modelo, aunque no sean exactamente igual que el suyo. Pídanos una fotografia de la pieza que necesita si lo cree oportuno. ";
        //$field["description"] = "";
        $field["tags"] = $model->getCategoria_ps() . "," . $model->getMar() . "," . $model->getCategoria_ps() . ",barato, segunda mano, recambio,usado";
        $field["metatitle"] = "Piezas de segunda mano de " . $model->getCategoria_ps() . " " . $model->getMar() . "| RecambiosYa";
        $field["metakeywords"] = $model->getCategoria_ps() . "," . $model->getMar() . ",comprar recambio " . $model->getCategoria_ps() . ",barato, segunda mano, recambio,comprar pieza,desguace, " . $model->getCategoria_ps() . "," . $anys_string;
        $field["metakeywords"] = substr($field["metakeywords"], 0, 255);
        $field["metadescription"] = "Oportunidad: piezas de recambios usados baratos. Para coches de los modelos " . $model->getCategoria_ps() . " de la marca " . $model->getMar() . " Recambios usados, piezas de segunda mano a buen precio";
        $field["urlrewritten"] = limpiar_urlrewritten(strtolower("segunda-mano-" . $model->getModbase() . "-" . $model->getMar() . "-" . $vehicle->getMat()));
        $field["textinstock"] = "";
        $field["textinback"] = "";
        $urlsfotos = '';
        if ($vehicle->getFv1() != 'SF')
            $urlsfotos = "http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv1();
        if ($vehicle->getFv2() != 'SF')
            $urlsfotos = $urlsfotos . ", http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv2();
        if ($vehicle->getFv3() != 'SF')
            $urlsfotos = $urlsfotos . ", http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv3();
        if ($vehicle->getFv4() != 'SF')
            $urlsfotos = $urlsfotos . ", http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv4();
        if ($vehicle->getFv5() != 'SF')
            $urlsfotos = $urlsfotos . ", http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv5();
        if ($vehicle->getFv6() != 'SF')
            $urlsfotos = $urlsfotos . ", http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv6();
        if ($vehicle->getFv7() != 'SF')
            $urlsfotos = $urlsfotos . ", http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv7();
        if ($vehicle->getFv8() != 'SF')
            $urlsfotos = $urlsfotos . ", http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv8();
        if ($vehicle->getFv9() != 'SF')
            $urlsfotos = $urlsfotos . ", http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv9();
        if ($vehicle->getFv10() != 'SF')
            $urlsfotos = $urlsfotos . ", http://meet-greets.com/recambiosya/europiezas/imatges/" . $vehicle->getFv10();

        $field["imageurls"] = $urlsfotos;
        $field["feature"] = "Combustible:" . $vehicle->getCbs() . ":10,Color:" . $vehicle->getCol() . ":11,Puertas:" . $vehicle->getPts() . ":12";
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
        echo $vehicle->getId_ps() . ";" . $field["active"] = "1" . ";" . $field["name"] . ";" .
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
        $count++;
        if ($fitxer != true) {
            print_r("<br><br>");
            ;
        }
        if ($count > 20)
            break;
    } else {
        //print_r($car);
        //si el codi del producte ja existeix
        //haurem d'activar-lo o desactivar-lo
        //falta saber camp de recambiosya
    }
    ini_set("max_execution_time", 30);
}
?>
