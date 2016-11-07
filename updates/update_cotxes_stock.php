<?php

ini_set('display_errors', 1);


// localhost/panell16/updates/update_cotxes_stock.php
// panell16.recambiosya.es/updates/update_cotxes_stock.php

include('../lib/PSWebServiceLibrary.php');
require_once("../protected/config/main.php");
include('../protected/models/Product.php');
include('../protected/models/Vehicle.php');
include('../protected/models/Models.php');
include('../protected/models/Peca.php');
include('../lib/scraps_lib.php');
include('../lib/functions.php');

//deshabilitem els cotxes que ja s'han enviat a desfragmentar
$sql = "SELECT * from a_vehicles 
            INNER JOIN ps_product ON ps_product.id_product = replace(a_vehicles.id_vh,'C','')+8000000
            where frag = 'SI' ";

$result = mysql_query($sql);

if (mysql_num_rows($result) > 0) {
    echo "<br>";
    echo "<br>";
    while ($row = mysql_fetch_array($result)) {
        $vehicle = new Vehicle($row["id_vh"]);
        // mirem si l'article ja està posat com que no esta en estoc
        $query_short = "SELECT * 
                        FROM  `ps_product_lang` 
                        WHERE  `description_short` LIKE  '%ATENCION:%' 
                            AND id_product='" . $vehicle->getId_ps() . "'";
        $result_short = mysql_query($query_short);
        ///echo "<br>";
        //print_r($query_short);
        //echo "<br>";
        if (mysql_num_rows($result_short) > 0) {
            //si ja està posat el missatge no fem res.            
        } else {
            $row_short = mysql_fetch_array($result_short);
            //print_r($row_short);
            //echo "<br>";
            $short = utf8_encode('<b>ATENCION:</b> ya no tenemos el coche en nuestras instalaciones,
            por lo tanto solo estant disponibles la piezas indicadas en el listado inferior. 
            <a href="http://pedir.recambiosya.es/buscar_index.php">Buscador de piezas</a><br><br>');
            $desc_short = $short . " " . $row_short["description_short"];
            $query_update = "UPDATE ps_product_lang 
                            SET description_short = CONCAT('" . $desc_short . "', description_short)  
                            WHERE id_product='" . $vehicle->getId_ps() . "'";
            //print_r($query_update);
            mysql_query($query_update);
        }
        ini_set("max_execution_time", 30);
    }
} else {
    echo "No hi ha productes sense estoc";
}
echo "proces acabat amb exit";
?>
