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
 * localhost/panell16/updates/update_url_imatge.php
 */

$sql2 = "SELECT * FROM ps_product where id_product < 8000000";

$sql2 = "SELECT id_product FROM ps_product 
                INNER JOIN a_peces ON ps_product.id_product = a_peces.codigo + 1000000
                WHERE a_peces.url_image is null
                order by codigo desc
                ";
//posar left join amb a_peces si url_imatge és null

//echo "<br>".$sql2;
$result2 = mysql_query($sql2);
$count = 1; //fem fitxers de 300 registres per així ser més àgils

while ($row2 = mysql_fetch_array($result2)) {
        $id = $row2["id_product"];    
        try {
            $sql_img = "select  p.id_product,pl.name as Title,
                        cl.name as Categorie,
                        concat('http://', ifnull(conf.value,'www.recambiosya.es'), '/img/p/',
                        SUBSTRING(pi.id_image from -5 FOR 1),
                        '/',SUBSTRING(pi.id_image from -4 FOR 1),
                        '/',SUBSTRING(pi.id_image from -3 FOR 1),
                        '/',SUBSTRING(pi.id_image from -2 FOR 1),
                        '/',SUBSTRING(pi.id_image from -1 FOR 1),
                        '/' , pi.id_image, '.jpg') as url_image,
                            p.id_product AS ID
                        from ps_product p
                        left join ps_image pi on p.id_product = pi.id_product
                        left join ps_product_lang pl on p.id_product = pl.id_product
                        left join ps_category_lang cl on p.id_category_default = cl.id_category
                        left join ps_configuration conf on conf.name = 'www.recambiosya.es'
                        left join ps_product_carrier x on p.id_product = x.id_product
                        where p.id_product = $id
                        group by p.id_product
                        ";
            //echo "<br>".$sql_img;
            $result_img = mysql_query($sql_img);
            $row_img = mysql_fetch_array($result_img);
            if ($row_img["url_image"] != ""){  
                $id_peca = $id-1000000;
                $upd_img = "UPDATE a_peces set url_image='".$row_img["url_image"]."' WHERE codigo = ".$id_peca;
                $result = mysql_query($upd_img);
                if ($result){
                    //echo "<br>".$id_peca." : afegida imatge";
                    $count++;
                }
                //echo "<br><br>".$upd_img;
            }
            ini_set("max_execution_time", 30);
        } catch (Exception $e) {
            echo 'No hi ha fotos de la peça:<br>';
        }
        //echo "<br><br>-------------------------------";
}
echo "<br><br>Finalitzat amb exit-------------------------------".$count;
?>
