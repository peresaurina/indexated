<?php

ini_set('display_errors', 1);

//include('protected/scraps_lib.php');
include("../protected/config/main.php");
include('../protected/models/Categoria.php');
include('../protected/models/Models.php');
include('../protected/models/PS_Categoria.php');
//include('../protected/models/Europ_Entradas.php');
include('../lib/PSWebServiceLibrary.php');

include('../lib/functions.php');
include('../lib/scraps_lib.php');

// localhost/panell16/xml_files/category_create.php
// panell16.recambiosya.es/xml_files/category_create.php

$fitxer = true;

$crear_categories_api = !$fitxer;
//$crear_categories_api = false;

//if (isset($_REQUEST["crear"])) $crear_categories_api = $_REQUEST["crear"];
ini_set('max_execution_time', 3000);

if ($fitxer == true) {
    header("Content-type: application/csv; charset=UTF-8");
    //header("Content-type: application/csv");
    header("Content-Disposition: attachment; filename=categories_coches_sinistrats" . time() . ".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
}

/* Fitxer per generar les categories necessaries per pujar les peces
 * de segona ma al programa.
 * Llegim peces de la taula pujada de bbdd de europiezas
 * Comprovem si existeix la categoria de les peces a Prestashop
 * Creem csv per a categories no pujades
 * Faltarà pujar CSV al Prestashop
 * Per continuar amb pujar després les peces
 */


//**********************************************************************//
// CREEM MODEL PER A LES PECES DE SEGONA MA
// NIVELLS: MARCA > MODEL > SUBMODEL
// UN COTXE SINISTRAT ÉS COM SI FOS UNA PEÇA MÉS
//**********************************************************************//
//
// Busquem totes les Marques que tenim i donem d'alta si cal
// HAURÍEM DE SOLUCIONAR QUE NOMÉS AGAFEM ELS COTXES EN ESTOC, DONAR
// D'ALTA NOMÉS LES CATEGORIES NECESSARIES.

$sql = "SELECT distinct(a_mods.mar) FROM a_mods
        INNER JOIN a_vehicles on a_mods.model = a_vehicles.model
        WHERE a_vehicles.id_vh like 'C%'";

$sql = "SELECT * FROM a_vehicles
        INNER JOIN a_mods on a_mods.model = a_vehicles.model
        WHERE a_vehicles.id_vh like 'C%'";

$result = mysql_query($sql);


while ($row = mysql_fetch_array($result)) {

    $marca = trim($row["mar"]);

    // NIVELL 1 : creem categoria Marca del cotxe si no existeix        
    if (!Categoria::existInPrestashop($marca) && ($marca != null)) {
        if ($fitxer != true)
            echo "<br>Crear categora marca: " . $marca;
        $field["id"] = ""; //$id_max;
        $field["active"] = "1";
        $field["name"] = $marca; //marca
        $field["parentcategory"] = "2"; //cal posar el nom de la categoria pare
        $field["rootcategory"] = "1"; //és categoria principal
        $field["description"] = "Recambios usados y piezas de segunda mano para los modelos de la marca " . $marca . ". Servicio 24/48h.";
        $field["metatitle"] = "Recambios usados para coches " . $marca . " de desguace | RecambiosYa";
        $field["metakeywords"] = $marca . ",pieza segunda mano,recambio usado, pieza usada, desguace";
        $field["metadescription"] = $field["description"] . " Con fotografias de las piezas!";
        $field["urlrewritten"] = limpiar_urlrewritten(strtolower($field["name"])) . "-desguaces-piezas";
        $field["imageurl"] = "../imatges-marques/" . $marca . ".jpg";
        $field["imageurl"] = "";
        $field["idboutique"] = "RecambiosYa"; //si no tenim multitienda no cal informar-ho
        if ($fitxer == false)
            echo "<br>NOVA MARCA: " . $marca . "<br>";
        if ($fitxer == true) {
            echo $field["id"] . ";" . $field["active"] . ";" . $field["name"] . ";"
            . $field["parentcategory"] . ";" . $field["rootcategory"] . ";" . $field["description"] . ";"
            . $field["metatitle"] . ";" . $field["metakeywords"] . ";" . $field["metadescription"] . ";"
            . $field["urlrewritten"] . ";" . $field["imageurl"] . ";RecambiosYa\r\n";
        }
        if ($crear_categories_api == true) {
            $nova_categoria = new PS_Categoria($field);
            print_r($nova_categoria);
            $nova_categoria->verifyCategory();
            $registre++;
            echo "--------------------------------------------<br>";
        }
    } else {
        //echo "<br>$registre - Marca existeix: " . $row["marca"];
    }
}

//NIVELL 2 : creem sub-categoria del cotxe : model

$sql = "SELECT distinct(a_mods.modbase),a_mods.mar FROM a_mods
        INNER JOIN a_vehicles on a_mods.model = a_vehicles.model
        WHERE a_vehicles.id_vh like 'C%'
        and modbase !='' and modbase not like '%CAMION%'";

$result = mysql_query($sql);

while ($row = mysql_fetch_array($result)) {
    $modbase = $row["modbase"]; // marca - model
    $marca = trim($row["mar"]); // marca

    if (!Categoria::existInPrestashop(treure_marca_en_categoria($modbase))) {
        if ($fitxer != true)
            echo "<br>Crear categora model: " . $modbase;
        $field["id"] = ""; //$id_max
        $field["active"] = "1";
        $field["name"] = treure_marca_en_categoria($modbase); //model caldrà / treure-hi la marca
        $field["parentcategory"] = $marca;
        $field["rootcategory"] = "0"; //no és categoria principal
        $field["description"] = "Recambios usados y piezas de segunda mano para " . $modbase . ". Servicio 24/48h.";
        $field["metatitle"] = "Piezas de desguace para " . $modbase . "| RecambiosYa";
        $field["metakeywords"] = $marca . " ," . $modbase . ",pieza segunda mano,recambio usado";
        $field["metadescription"] = $field["description"] . " Con fotografias de las piezas!";
        $field["urlrewritten"] = limpiar_urlrewritten(strtolower($modbase)) . "-desguace-piezas";
        $field["imageurl"] = "";
        $field["idboutique"] = "RecambiosYa"; //si no tenim multitienda no cal informar-ho        
        if ($fitxer == false)
            echo "<br> NOVA CATEGORIA MODEL:" . "  " . $modbase . "<br>";
        if ($fitxer == true) {
            echo $field["id"] . ";" . $field["active"] . ";" . $field["name"] . ";"
            . $field["parentcategory"] . ";" . $field["rootcategory"] . ";" . $field["description"] . ";"
            . $field["metatitle"] . ";" . $field["metakeywords"] . ";" . $field["metadescription"] . ";"
            . $field["urlrewritten"] . ";" . $field["imageurl"] . ";RecambiosYa\r\n";
        }

        if ($crear_categories_api == true) {
            $nova_categoria = new PS_Categoria($field);
            print_r($nova_categoria);
            $nova_categoria->verifyCategory();
            $registre++;
            echo "--------------------------------------------<br>";
        }
    } else {
        //echo "<br>- model cotxe existeix: " . $modbase;
    }
}

//NIVELL 3 : creem model versió

$sql = "SELECT distinct(a_mods.model),a_mods.modbase,a_mods.mar,a_mods.categoria_ps FROM a_mods
        INNER JOIN a_vehicles on a_mods.model = a_vehicles.model
        WHERE a_vehicles.id_vh like 'C%'
        and modbase !='' and modbase not like '%CAMI%'";

$result = mysql_query($sql);

while ($row = mysql_fetch_array($result)) {
    $model = $row["model"];
    $modbase = $row["modbase"];
    $marca = trim($row["mar"]);
    $categoria_ps = $row["categoria_ps"];

    if (!Categoria::existInPrestashop($categoria_ps)) {
        //if ($fitxer != true) echo "<br>Crear categoria model any: " . $categoria_ps;
        $field["id"] = ""; //$id_max
        $field["active"] = "1";
        $field["name"] = $categoria_ps; //model
        $field["parentcategory"] = treure_marca_en_categoria($modbase);
        $field["rootcategory"] = "0"; //no és categoria principal
        $field["description"] = "Recambios, piezas de segunda mano de desguace para " . $marca . " " . $categoria_ps . ". Servicio 24/48h.";
        $field["metatitle"] = "Piezas de desguace para " . $marca . " " . ($categoria_ps) . "| RecambiosYa";
        $field["metakeywords"] = $categoria_ps . "," . $marca . ",pieza segunda mano,recambio usado,desguace";
        $field["metadescription"] = $field["description"] . " Con fotografias de las piezas!";
        $field["urlrewritten"] = limpiar_urlrewritten(strtolower($marca . " " . $categoria_ps . "-desguace-piezas"));
        $field["imageurl"] = "";
        $field["idboutique"] = "RecambiosYa"; //si no tenim multitienda no cal informar-ho

        if ($fitxer == false)
            echo "<br> NOVA CATEGORIA MODEL - ANY:" . "  " . $categoria_ps . "<br>";
        if ($fitxer == true) {    
            echo $field["id"] . ";" . $field["active"] . ";" . $field["name"] . ";"
            . $field["parentcategory"] . ";" . $field["rootcategory"] . ";" . $field["description"] . ";"
            . $field["metatitle"] . ";" . $field["metakeywords"] . ";" . $field["metadescription"] . ";"
            . $field["urlrewritten"] . ";" . $field["imageurl"] . ";RecambiosYa\r\n";
        }
        if ($crear_categories_api == true) {
            $nova_categoria = new PS_Categoria($field);
            print_r($nova_categoria);
            $nova_categoria->verifyCategory();
            echo "--------------------------------------------<br>";
        }
    }
}
?>
