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

// localhost/panell/update_descriptions_cotxes.php
// panell16.recambiosya.es/updates/update_descriptions_cotxes.php
// genera un descripció automàtica de tots els vehicles
// 
// Anys dels cotxe
// seleccionem tots els cotxes
$sql_product = "SELECT * FROM ps_product 
                WHERE id_product > 8000000 
                order by id_product desc   
                ";

$result_product = mysql_query($sql_product);

while ($row_product = mysql_fetch_array($result_product)) {
    
    $descripcio_anys = category_desc_anys($row_product["id_category_default"]);
    //C0 000 001
    //8  000  000
    $car = new Vehicle(preg_replace('#^8#', "C0", $row_product["id_product"]));
    $model = new Models($car->getModel());
    
    // Peces que venen d'aquest cotxe. Crear descripció i enllaç de la peça  
    // i que la peça existeix a prestashop
    $sql_piezas = "Select * from a_peces 
                    INNER JOIN recya16.ps_product ON 
			recya16.ps_product.id_product = replace(recya16.a_peces.codigo,'C','')+1000000
                    where id_vh = '" . $car->getId_vh() . "' "
            . "     and enestoc = '1'";
    //echo "<br>".$sql_piezas;
    $result_piezas = mysql_query($sql_piezas);
    $desc_piezas = "";

    while ($row_piezas = mysql_fetch_array($result_piezas)) {
        try {
            $peca = new Peca($row_piezas["codigo"]);
            //$pieza_url = "http://www.recambiosya.es" . urlProduct($peca->getid_ps());
            $desc_piezas = $desc_piezas . '<br><b><a href="' . $peca->getUrl_es() . '" target="_blank">' . $peca->getpza() .
                    ' ' . $peca->getpvp() . '€</b></a>';
        } catch (Exception $e) {
            echo "<br>Ha saltat excepció----------------<br>";
        }
    }

    //echo "<br>";
    $desc_generic = "<br><br>Para otras piezas y recambios de segunda mano o usados no despiezados como:
        bomba de freno, reposapiés, asientos, cristales, motor, motores, elevalunas,
        capó, puertas, llantas, pneumáticos usados, portón, porton, radiador, volante,
        pneumáticos nuevos, alternador, aleta delantera, aleta trasera, parachoques, faros, pilotos, retrovisores,
        lunetas, compresores, tapacubos, airbags, alerones, equipos musicales, motor arranque,...pida su presupuesto.
         ";
    //echo $desc_generic;
    $desc_inici = "<br>Las piezas de este vehículo estan preparadas para ser desmontadas y ofrecerle un precio ajustado a su medida, 
        para así conseguir aprovechar piezas usadas de otros vehículos para su vehículo. 
        Debe tener claro que la pieza que necesita puede ser compatible entre vehículos de igual marca y modelo, 
        aunque no sean exactamente igual que el suyo. 
        Pídanos una fotografia de la pieza que necesita si lo cree oportuno.";

    $nova_descripcio = ($desc_inici . '<br>' . $desc_piezas . $descripcio_anys . $desc_generic);
    $query = "UPDATE `ps_product_lang` SET `description`='" . $nova_descripcio . "' WHERE id_product= '" . $row_product["id_product"] . "'";
    //echo $query;
    $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());
    //echo "<br>".$result;
    //echo $nova_descripcio;
    //echo "<br>---------------------------------------------------";
    //echo "<br>";
    ini_set("max_execution_time", 300);
}
echo "fi del proces";
?>
