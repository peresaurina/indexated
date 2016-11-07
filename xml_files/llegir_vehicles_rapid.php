<?php

// localhost/panell16/xml_files/llegir_vehicles.php
// panell16.recambiosya.es/xml_files/llegir_vehicles.php
echo "<br>entrada a fitxer nou<br>";
include('../protected/models/Vehicle.php');
include('../protected/models/Models.php');
require_once("../protected/config/main.php");

include('../lib/functions.php');
include('../lib/scraps_lib.php');

$file = 'http://europiezas.recambiosya.es/FV.xml';
$file ='../../europiezas/FV.xml';

$i = 0;
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

while ($z->name === 'REGISTRO') {
    unset($vehicle);
    unset($vehicle_nou);
    $vehicle = simplexml_import_dom($doc->importNode($z->expand(), true));
    
    $vehicle_nou["id_vh"] = $vehicle->ID_VH;
    $vehicle_nou["tip"] = $vehicle->TIP;
    $vehicle_nou["mar"] = $vehicle->MAR;
    $vehicle_nou["model"] = $vehicle->MOD; //canviema a Model pq es paraula reserva en mysql
    $vehicle_nou["ver"] = $vehicle->VER;
    $vehicle_nou["a"] = $vehicle->A;
    $vehicle_nou["cm"] = $vehicle->CM;
     if ($vehicle->COL = '?')
        $vehicle->COL = null;
    $vehicle_nou["col"] = $vehicle->COL;
    $vehicle_nou["m"] = $vehicle->M;
    $vehicle_nou["mat"] = $vehicle->MAT;
    $vehicle_nou["cbs"] = $vehicle->CBS;
    $vehicle_nou["pts"] = $vehicle->PTs;
    if ($vehicle->KM = '?')
        $vehicle->KM = null;
    $vehicle_nou["km"] = $vehicle->KM; // si no n'hi ha  = ?
    $vehicle_nou["frag"] = $vehicle->FRAG;
    
    if ($i > 50000)
        break;
    $i++;
    ini_set("max_execution_time", 30);
    $z->next('REGISTRO');
}

echo "<br><br>fi del proces. Congrats :P, num. registres: " . $i;
?>
