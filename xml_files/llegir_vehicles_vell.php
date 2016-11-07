<?php

// localhost/panell16/xml_files/llegir_vehicles.php
// panell16.recambiosya.es/xml_files/llegir_vehicles.php

include('../protected/models/Vehicle.php');
include('../protected/models/Models.php');
require_once("../protected/config/main.php");

include('../lib/functions.php');
include('../lib/scraps_lib.php');

$file = 'http://europiezas.recambiosya.es/FV.xml';

$i = 0;
try {
    echo "<br>Entrem a carregar el fitxer";
    $registros = simplexml_load_file($file);
} catch (Exception $e) {
    echo "<br>error en carrega de fitxer: " . $e;
    exit();
}
foreach ($registros->REGISTRO as $vehicle) {
    print_r($vehicle);

    $vehicle_nou["id_vh"] = $vehicle->ID_VH;
    $vehicle_nou["tip"] = $vehicle->TIP;
    $vehicle_nou["mar"] = $vehicle->MAR;
    $vehicle_nou["model"] = $vehicle->MOD; //canviema a Model pq es paraula reserva en mysql
    $vehicle_nou["ver"] = $vehicle->VER;
    $vehicle_nou["a"] = $vehicle->A;
    $vehicle_nou["cm"] = $vehicle->CM;
    $vehicle_nou["col"] = $vehicle->COL;
    $vehicle_nou["m"] = $vehicle->M;
    $vehicle_nou["mat"] = $vehicle->MAT;
    $vehicle_nou["cbs"] = $vehicle->CBS;
    $vehicle_nou["pts"] = $vehicle->PTs;
    if ($vehicle->KM = '?')
        $vehicle->KM = null;
    $vehicle_nou["km"] = $vehicle->KM; // si no n'hi ha  = ?
    $vehicle_nou["frag"] = $vehicle->FRAG;

    //---------------------camps per PRESTASHOP------------------
    //$marca = strtoupper(marca_normalizar($vehicle->MAR));
    //echo "<br>Marca: " . $marca . "<br>";
    //echo "<br>" . $modbase . "<br>";
    //
    // Volem trobar el model base !!!
    // Si no tenim el model base, creem una línia i el deixem en buit
    // a mà l'haurem d'omplir
    try {
        //$sql = "SELECT *,A100MOD.Nombre as modbase FROM B100MOD INNER JOIN A100MOD on B100MOD.clvvin = A100MOD.codigo WHERE B100MOD.Nombre = '" . $vehicle->MOD . "'";
        $sql = "SELECT * FROM a_mods WHERE model = '" . $vehicle->MOD . "'";
        $result = mysql_query($sql);

        //Això ens avisarà d'afegir models mentre Europiezas no ens doni
        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_assoc($result);
            //-------
            $model["model"] = strtoupper($vehicle->MOD);
            $model["mar"] = strtoupper($vehicle->MAR);
            $model["modbase"] = strtoupper($row["modbase"]);
            $categoria_ps = treure_color_en_categoria(treure_marca_en_categoria(sub_model_name($vehicle->MOD, null)));
            $model["categoria_ps"] = $categoria_ps;
            $nou_model = new Models(null, $model);
            $nou_model->insertIntoDataBase();
        } else {
            //echo "<br>" . $sql;
            echo "<br>No tenim aquest model: " . $vehicle->MOD . "<br>";
            // el modbase ho deixem en blanc per així posar-ho a mà
            $model["model"] = $vehicle->MOD;
            $model["mar"] = $vehicle->MAR;
            //$model["modbase"] = '';
            $categoria_ps = treure_color_en_categoria(treure_marca_en_categoria(sub_model_name($vehicle->MOD, null)));
            $model["categoria_ps"] = $categoria_ps;
            $nou_model = new Models(null, $model);
            print_r($nou_model);
            $nou_model->insertIntoDataBase();
            mysql_query($sql);
        }
    } catch (Exception $e) {
        echo "<br>Ha saltat una excepció: " . $e;
    }

    $vehicle_nou["fv1"] = $vehicle->FV1; // si no n'hi ha = SF
    $vehicle_nou["fv2"] = $vehicle->FV2;
    $vehicle_nou["fv3"] = $vehicle->FV3;
    $vehicle_nou["fv4"] = $vehicle->FV4;
    $vehicle_nou["fv5"] = $vehicle->FV5;
    $vehicle_nou["fv6"] = $vehicle->FV6;
    $vehicle_nou["fv7"] = $vehicle->FV7;
    $vehicle_nou["fv8"] = $vehicle->FV8;
    $vehicle_nou["fv9"] = $vehicle->FV9;
    $vehicle_nou["fv10"] = $vehicle->FV10;
    //print_r($vehicle_nou);
    $vehicle_bd = new Vehicle(null, $vehicle_nou);
    $vehicle_bd->insertIntoDataBase();
 

    if ($i > 50)
        break;
    $i++;
    ini_set("max_execution_time", 30);
    //print_r($vehicle);
    //echo "<br><br>";
}

echo "<br><br>fi del proces. Congrats :P, num. registres: " . $i;
?>
