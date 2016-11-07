<?php

ini_set('display_errors', 1);
require_once("../protected/config/main.php");
//include('protected/models/Categoria.php');
include('../protected/models/Product.php');
//include('protected/models/Europ_Entradas.php');
include('../protected/models/Europ_Pieza.php');
include('../lib/scraps_lib.php');
include('../lib/functions.php');
include('../lib/PSWebServiceLibrary.php');


/* creació del fitxer dels cotxes sinistrats de la base de dades
 * Prensado => significa que el cotxe ja no està en estoc
 * localhost/panell16/xml_files/product_no_fotos.php
 */

$sql2 = "SELECT * FROM ps_product where id_product < 8000000 limit 0,10";

$result2 = mysql_query($sql2);
$count = 1; //fem fitxers de 300 registres per així ser més àgils

while ($row2 = mysql_fetch_array($result2)) {
    $id = $row2["id_product"];
    if (Product::existInPrestashop($id)) {
        try {
            $sql_img = "select  p.id_product,pl.name as Title,
                        cl.name as Categorie,
                        concat('http://', ifnull(conf.value,'www.recambiosya.es'), '/img/p/',
                        SUBSTRING(pi.id_image from -5 FOR 1),
                        '/',SUBSTRING(pi.id_image from -4 FOR 1),
                        '/',SUBSTRING(pi.id_image from -3 FOR 1),
                        '/',SUBSTRING(pi.id_image from -2 FOR 1),
                        '/',SUBSTRING(pi.id_image from -1 FOR 1),
                        '/' , pi.id_image, '.jpg') as product_image,
                            p.id_product AS ID
                        from ps_product p
                        left join ps_image pi on p.id_product = pi.id_product
                        left join ps_product_lang pl on p.id_product = pl.id_product
                        left join ps_category_lang cl on p.id_category_default = cl.id_category
                        left join ps_configuration conf on conf.name = 'www.recambiosya.es'
                        left join ps_product_carrier x on p.id_product = x.id_product
                        where p_id_product = $id
                        group by p.id_product
                        limit 0,1";
            
            
            
        } catch (Exception $e) {
            echo 'No hi ha fotos de la peça: ' . $peca->getId() . "<br>";
        }
    }
}
?>
