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

// http://localhost/panell16/update/update_descriptions_peces.php
// panell16.recambiosya.es/updates/update_descriptions_peces.php
// 
// genera un descripció automàtica de totes les peces amb peces
// que tenen el seu mateix $peca->getPza(), que estant en estoc, i tenen
// la mateixa categoria de prestashop i/o el mateix model base
// Hi hauríem de posar la foto, el nom de la peça i al costat el preu
// fer-ho en una taula, a veure com queda en Prestashop, comencem
// amb una fila per peça i llavors ja veurem si ho podem millorar...
// 
// Anys dels cotxe
// seleccionem tots els cotxes
$sql_product = "SELECT codigo,pza FROM a_peces 
                INNER JOIN ps_product ON ps_product.id_product = a_peces.codigo + 1000000
                WHERE enestoc > '0'
                order by codigo desc                
                ";

$result_product = mysql_query($sql_product);
//echo $sql_product;
$i=0;
while ($row_product = mysql_fetch_array($result_product)) {
    
    $peca = new Peca($row_product["codigo"]);
    $car = new Vehicle($peca->getid_vh());
    $model = new Models($car->getModel());
    
    
      
    //echo ($car->getId_vh())."<br>";
    //echo($model->getMar()."<br>");
    //echo($model->getCategoria_ps()."<br>");
    
    //echo "<br>-----------------------------------------";   
    
//}

// Peces que tenen el mateix text i de la mateixa categoria
    
      
    $sql_piezas = "Select * from a_peces 
                    inner join a_vehicles on a_vehicles.id_vh=a_peces.id_vh
                    inner join a_mods on a_mods.model = a_vehicles.model
                    INNER JOIN ps_product ON ps_product.id_product = a_peces.codigo + 1000000
                    where   a_peces.enestoc = '1'
                            and a_peces.pza ='".$peca->getpza()."'
                            and a_mods.modbase ='".$model->getModbase()."'
                            ";
    //echo "<br>".$sql_piezas;
    $result_piezas = mysql_query($sql_piezas);
    $desc_piezas ="";
    
    while ($row_piezas = mysql_fetch_array($result_piezas)) {
        $peca2 = new Peca($row_piezas["codigo"]);
        $car2 = new Vehicle($peca2->getid_vh());
        $model2 = new Models($car2->getModel());
        //$pieza_url = "http://www.recambiosya.es/almera-4-2002-2006-2-2-d-5p/" . $peca->getid_ps() . "-recambios-piloto-trasero-derecho-nissan.html";
        //$pieza_url = "http://www.recambiosya.es" .urlProduct($peca2->getid_ps());
        $desc_piezas = $desc_piezas.'<br>'.$model2->getCategoria_ps().' ('.$car2->getMat().'): <b><a href="' . $peca2->getUrl_es() . '" target="_blank">'.$peca2->getpza().
                ' '.$peca2->getpvp().'€</b></a>';
    }
    
    //echo "<br>";
    $link_pedir = '<br><strong><a href="http://www.recambiosya.es/pedir/">PEDIR PRESUPUESTO PIEZA</a></strong>';
    $desc_generic = "<br><br>Para otras piezas y recambios de segunda mano o usados no despiezados como:
        bomba de freno, reposapiés, asientos, cristales, motor, motores, elevalunas,
        capó, puertas, llantas, pneumáticos usados, portón, porton, radiador, volante,
        pneumáticos nuevos, alternador, aleta delantera, aleta trasera, parachoques, faros, pilotos, retrovisores,
        lunetas, compresores, tapacubos, airbags, alerones, equipos musicales, motor arranque,...".$link_pedir;
    
    //echo $desc_generic;
    $desc_inici = "<br>Disponemos otras piezas en nuestro desguace similares a esta, procedentes de"
            . "otros vehiculos, y de modelos parecidos: <br>";
    //falta posar-hi anys
    $nova_descripcio = ($desc_inici.'<br>'.$desc_piezas." ".$desc_generic);
    $query = "UPDATE `ps_product_lang` SET `description`='" . $nova_descripcio . "' WHERE id_product= '". $peca->getid_ps()."'";
    //echo $query;
    $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());
    //echo "<br>".$result;
    //echo $nova_descripcio;
    //echo $nova_descripcio;
    //echo "<br>";
    ini_set("max_execution_time", 300);
    $i++;
}

echo "<h1>Actualitzades amb éxit descripcions de : ".$i." articles.";

echo '<br><br><br><a href="http://panell16.recambiosya.es">Tornar</a>';

?>
