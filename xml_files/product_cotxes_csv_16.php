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

// localhost/panell16/xml_files/product_cotxes_csv_16.php
// panell16.recambiosya.es/xml_files/product_cotxes_csv.php

$fitxer = true;

if ($fitxer = true) {
    header("Content-type: application/csv; charset=UTF-8");
    //header("Content-type: application/csv");
    header("Content-Disposition: attachment; filename=coches_sinistrats" . time() . ".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
}

//$sql = "SELECT * FROM entradas WHERE FOTO1 LIKE  '%jpg'"; //agafem tots els cotxes possibles

$sql = "SELECT * FROM a_vehicles where id_vh like 'C%'"; //agafem tots els cotxes possibles

$sql = "SELECT * FROM a_vehicles
        INNER JOIN a_mods on a_mods.model = a_vehicles.model
        WHERE a_vehicles.id_vh like 'C%'";

$result = mysql_query($sql);
$count = 1; 
$limit_productes = 240;//fem fitxers de 300 registres per així ser més àgils

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

    if((!Product::existInPrestashop($vehicle->getId_ps()))&&(Categoria::existInPrestashop($model->getCategoria_ps()))){
    //if ((!Product::existInPrestashop($vehicle->getId_ps())) && (!Categoria::existInPrestashop($model->getCategoria_ps()))) {


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
        unset($field);
        $field["active"] = "1";

        //Nou H1 de producte
        $anys_string = "";
        $anys = array_year_category($model->getCategoria_ps());
        if (isset($anys)) {
            $anys_string = "Años: " . implode(', ', $anys);
        } else {
            $anys_string = "";
        }
        
        // En 1.6 hem de posar la cadena de categories amb els respectius ID 
        $categories_prestashop = '';
        $id_cotxe_categoria = Categoria::CategoryIdPrestashop($model->getCategoria_ps());
        $categoria3 = new Categoria($id_cotxe_categoria);
        $categoria2 = new Categoria($categoria3->getIdparent());
        $categoria1 = new Categoria($categoria2->getIdparent()); //aquesta ja és root
        
        if ($categoria1->getIsrootcategory() == true){
            $categories_prestashop = $categoria1->getId().",".$categoria2->getId().",".$categoria3->getId();
        }else{
            $categories_prestashop = $categoria1->getIdparent().",".$categoria1->getId().",".$categoria2->getId().",".$categoria3->getId();
        }
        
        //$categories_prestashop = $id_cotxe_categoria.",2";
        $categories_prestashop = $id_cotxe_categoria;
        
        //echo "id_cotxe_categoria ".$id_cotxe_categoria;
        //echo "<br>".$categories_prestashop."<br>";
        //---------------------------------------------------------------
        $field["name"] = ucwords(strtoupper($model->getMar()))." ".ucwords(strtoupper($model->getCategoria_ps() . " PARA RECAMBIOS" . " " . $vehicle->getMat()));
        //aquí el nombre correspon al model
        //$field["categories"]= strtoupper($car->getCategoria());
        
        $field["categories"] = $categories_prestashop;
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
        $field["manufacturer"] = $model->getMar();
        $field["ean13"] = "";
        $field["upc"] = "";
        $field["ecotax"] = "";
        $field["weigth"] = "";
        $field["quantity"] = "1";
        $short_description = "Coche para despiezar en nuestro desguace  " .  ucwords(strtolower($model->getMar()." ". $model->getCategoria_ps())) . ", versión " . $vehicle->getVer() . " " . ucwords(strtolower($vehicle->getCbs())) . " " . $anys_string;
        if ($vehicle->getCm() != ""){ $short_description.= "<br>Tipo de motor : ".$vehicle->getCm().".<br>";}

        if (strlen($short_description) > 800) {
            $short_description = substr($short_description, 0, 790) . "...";
        }
        unset($link_pedir);
        unset($fi_short_description);
        unset($field["shortdescription"]);
        $link_pedir = '<br><br><strong><a href="http://pedir.recambiosya.es/">PEDIR PRESUPUESTO PIEZA</a></strong>';
        $fi_short_description = '<br><br><strong><font color="#f13340">Más abajo puede ver el listado de piezas de este vehiculo</font></strong>';
        
        $field["shortdescription"] = $short_description.' '.$link_pedir.$fi_short_description;
        //$field["description"] = "Las piezas de este vehículo estan preparadas para ser desmontadas en nuestro desguace y ofrecerle un precio ajustado a su medida, para así conseguir aprovechar piezas usadas de otros vehículos para su vehículo. Debe tener claro que la pieza que necesita puede ser compatible entre vehículos de igual marca y modelo, aunque no sean exactamente igual que el suyo. Pídanos una fotografia de la pieza que necesita si lo cree oportuno. ";
        $field["description"] .= $link_pedir;
        $field["tags"] = $model->getCategoria_ps() . "," . $model->getMar() . "," . $model->getCategoria_ps() . ",barato, segunda mano, recambio,usado";
        $field["metatitle"] = "Piezas de desguace para " . ucwords(strtolower($model->getMar()." ".$model->getCategoria_ps())) . "| RecambiosYa";
        $field["metakeywords"] = $model->getCategoria_ps() . "," . $model->getMar() . ",comprar recambio " . $model->getCategoria_ps() . ",barato, segunda mano, recambio,comprar pieza,desguace, " . $model->getCategoria_ps() . "," . $anys_string;
        $field["metakeywords"] = substr($field["metakeywords"], 0, 255);
        $field["metadescription"] = "Oportunidad: piezas de recambios usados baratos. Para coches ".ucfirst(strtolower($model->getMar()))." " . ucwords($model->getCategoria_ps()) . ". Recambios usados de desguace, piezas de segunda mano a buen precio. En 24h";
        $field["urlrewritten"] = limpiar_urlrewritten(strtolower("desguace-" . $model->getModbase() . "-" . $vehicle->getMat()));
        $field["textinstock"] = "";
        $field["textinback"] = "";
        $urlsfotos = '';
        if ($vehicle->getFv1() != 'SF')
            $urlsfotos = "http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv1();
        if ($vehicle->getFv2() != 'SF')
            $urlsfotos = $urlsfotos . ", http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv2();
        if ($vehicle->getFv3() != 'SF')
            $urlsfotos = $urlsfotos . ", http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv3();
        if ($vehicle->getFv4() != 'SF')
            $urlsfotos = $urlsfotos . ", http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv4();
        if ($vehicle->getFv5() != 'SF')
            $urlsfotos = $urlsfotos . ", http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv5();
        if ($vehicle->getFv6() != 'SF')
            $urlsfotos = $urlsfotos . ", http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv6();
        if ($vehicle->getFv7() != 'SF')
            $urlsfotos = $urlsfotos . ", http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv7();
        if ($vehicle->getFv8() != 'SF')
            $urlsfotos = $urlsfotos . ", http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv8();
        if ($vehicle->getFv9() != 'SF')
            $urlsfotos = $urlsfotos . ", http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv9();
        if ($vehicle->getFv10() != 'SF')
            $urlsfotos = $urlsfotos . ", http://europiezas.recambiosya.es/imatges/" . $vehicle->getFv10();

        $field["imageurls"] = $urlsfotos;
        $field["feature"] = "Combustible:" . $vehicle->getCbs() . ":10,Color:" . $vehicle->getCol() . ":11,Puertas:" . $vehicle->getPts() . ":12";
        $field["onlyonline"] = "";


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
        $field["width"] ="";
        $field["height"]="";
        $field["depth"] ="";
        $field["minimalquantity"] ="";
        $field["visibility"] ="";
        $field["aditionalshipingcost"] = 0;
        $field["unity"] = "";
        $field["uniteprice"] = "";
        $field["availablefororder"] ="0";
        $field["availabledate"] ="0";
        $field["productavailabledate"] ="";
        $field["deleteimages"] ="1";
        $field["availableonlineonly"] ="1";
        $field["condition"]="used";
        $field["customizable"]="0";
        $field["uploadablefiles"] ="";
        $field["textfields"]="";
        $field["outofstock"]="";
        $field["productcreationdate"] = date("Y-m-d");
        $field["productcreationdate"] ="";
        $field["showprice"] = '0';
        $field["advancedstockmanagement"] = '';
        $field["dependsonstock"] = '';
        $field["warehouse"] = '';
        
        echo $vehicle->getId_ps() . ";" . $field["active"] = "1" . ";" . $field["name"] . ";" .
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
            
        $count++;
        if ($fitxer = false) {
            print_r("<br><br>");
            ;
        }
        if ($count > $limit_productes)
            break;
    } else {
        //print_r($car);
        //echo "<br>Vehicle no es pot donar d'alta<br>";
        //si el codi del producte ja existeix
        //haurem d'activar-lo o desactivar-lo
        //falta saber camp de recambiosya
    }
    ini_set("max_execution_time", 30);
}
?>
