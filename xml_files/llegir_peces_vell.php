<?php

// localhost/panell16/xml_files/llegir_peces.php
// panell16.recambiosya.es/xml_files/llegir_peces.php
// [CODIGO] => 18525 [ID_VH] => C0001935 [PZA] => Airbag Conductor [REF] => SimpleXMLElement Object ( ) 
// [PVP] => 60,00 [FP1] => SF [FP2] => SF [FP3] => SF [FP4] => SF [FP5] => SF

include('../protected/models/Peca.php');
require_once("../protected/config/main.php");
include('../lib/functions.php');
include('../lib/scraps_lib.php');

// ------------------------------------------------------------
// Només tenim estoc de les peces que hi ha al fitxer
// per tant posem estoc a 0 de totes les peces i llavors
// al anar llegint, posem l'estoc a 1
$sql = "UPDATE a_peces set enestoc='0'";
$result = mysql_query($sql);
// ------------------------------------------------------------

$registros = simplexml_load_file('http://europiezas.recambiosya.es/FP.xml');

foreach ($registros as $pieza) {
    unset($peca);
    $peca["codigo"] = $pieza->CODIGO;
    $peca["id_vh"] = $pieza->ID_VH;
    $peca["pza"] = sanear_string(quitar_abreviaturas($pieza->PZA));
    $peca["ref"] = sanear_string(quitar_abreviaturas($pieza->REF));
    $peca["pvp"] = $pieza->PVP;
    $peca["enestoc"] = '1'; //les peces que tenim en l'arxiu és que estant en estoc
    
    if ($pieza->FP1 != "SF")
        $peca["fp1"] = $pieza->FP1;
    if ($pieza->FP2 != "SF")
        $peca["fp2"] = $pieza->FP2;
    if ($pieza->FP3 != "SF")
        $peca["fp3"] = $pieza->FP3;
    if ($pieza->FP4 != "SF")
        $peca["fp4"] = $pieza->FP4;
    if ($pieza->FP5 != "SF")
        $peca["fp5"] = $pieza->FP5;

    $peca_llegida = new Peca(null, $peca);
    $peca_llegida->insertIntoDataBase();
    ini_set("max_execution_time", 10);
}

echo "proces acabat amb success :D Congrats !!!"

?>
