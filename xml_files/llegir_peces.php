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

$file ='http://europiezas.recambiosya.es/FP.xml';
$file ='../../europiezas/FP.xml';
try {
    echo "<br>Entrem a carregar el fitxer";
    $z = new XMLReader();
    $z->open($file);
    $doc = new DOMDocument;
    //$registros = simplexml_load_file($file) or die("Error: Cannot create object");
    //http://stackoverflow.com/questions/1835177/how-to-use-xmlreader-in-php
    //http://php.net/manual/en/book.xmlreader.php
     echo "<br>Fitxer carregat";
} catch (Exception $e) {
    echo "<br>error en carrega de fitxer: " . $e;
    exit();
}
echo '<br> hem carregat i anem a recórrer...';
$linies = 0;
while ($z->read() && $z->name !== 'REGISTRO'){
    $linies++;
};
echo '<br> hem arribat a registro el primer, a la linia: '.$linies;
$i = 0;
while ($z->name === 'REGISTRO') {
    unset($peca);
    unset($peca_llegida);
    unset($pieza);
    $pieza = simplexml_import_dom($doc->importNode($z->expand(), true));

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
    $i++;
    // go to next <registro />
    $z->next('REGISTRO');
}

echo "proces acabat amb success :D Congrats !!!. ".$i;

?>
