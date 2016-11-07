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

// http://localhost/panell16/updates/update_canonicals.php
// panell16.recambiosya.es/updates/update_canonicals.php
// 
// seleccionem totes peces que estant a prestashop
// no ho farem amb els vehicles perquè ja tenen la pàgina de la categoria


//netegem els que tenen la url buida per tal de crear la nova
$sql_delete = "DELETE FROM ps_simplecanonicalurls where url = '' or url='-' or url is null";
$result_delete = mysql_query($sql_delete);
echo "Borrats registre ps_simplecanonicalurls amb nula url : ".$result_delete."<br>";


$sql_piezas = "SELECT * from ps_product
                    LEFT JOIN ps_simplecanonicalurls ON ps_product.id_product = ps_simplecanonicalurls.id 
                    where url = '' or url is null
                    order by id_product desc
                    limit 0,10000
                    ";

$result_piezas = mysql_query($sql_piezas);

//$webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, false);
// www.recambiosya.es/pedir/piezas.php?marca=SEAT&modelo=seat%20altea&pieza=Direccion Asistida
while ($row = mysql_fetch_array($result_piezas)) {
    if ($row["id_product"] < 8000000) {
        $product_id = $row["id_product"];
        $peca = new Peca($row["id_product"] - 1000000);
        $vehicle = new Vehicle($peca->getid_vh());
        $model = new Models($vehicle->getModel());

        $url_canonical = 'http://www.recambiosya.es/pedir'
                . '/' . urlencode(strtolower($model->getMar()))
                . '/' . urlencode(strtolower($model->getModbase()))
                . '/' . urlencode(strtolower($peca->getpza()));
    } else {
        //producte és un cotxe = en principi no serveix per res
        //la pàgina de categoria ja posiciona per cada cotxe
        $product_id = $row["id_product"];
        $vehicle = new Vehicle(preg_replace('#^8#', "C0", $row["id_product"]));
        $model = new Models($vehicle->getModel());
        $sql2 = "SELECT * FROM ps_category_lang WHERE name = '" . $model->getCategoria_ps() . "'";
        $result2 = mysql_query($sql2);
        $row2 = mysql_fetch_array($result2);
        $url_canonical = 'http://www.recambiosya.es/'
                . $row2["id_category"]
                . "-" . urlencode($row2["link_rewrite"]);
    }

    //$url_canonical = urlencode($url_canonical);
    if (($url_canonical != "")&&($url_canonical != "-")&&($model->getModbase()!='')) {
        $sql = "INSERT INTO ps_simplecanonicalurls
            SET id = '$product_id', id_shop = '1', url = '$url_canonical'";
        $result1 = 0;
        $result1 = mysql_query($sql);
        if($result1 != '1'){
                echo "<br>No s'ha pogut insertar: " . $sql . "-" . $result1;
        }
    }
    unset($url_canonical);
    ini_set("max_execution_time", 30);
}

$sql_count = "SELECT count(*) as conta from ps_product
                    LEFT JOIN ps_simplecanonicalurls ON ps_product.id_product = ps_simplecanonicalurls.id 
                    where url = '' or url is null
                    order by id_product desc";
$result_count = mysql_query($sql_count);
$numero = mysql_fetch_array($result_count);
echo "<br><br><br>pendents: ".$numero["conta"];
//print_r($numero);


echo "<br><br>Proces acabat amb success Peces !!! ;P";
?>
