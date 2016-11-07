<?php

// localhost/panell16/xml_files/llegir_vehicles.php
// panell16.recambiosya.es/xml_files/llegir_vehicles.php
echo "<br>entrada a fitxer nou<br>";
include('../protected/models/Vehicle.php');
include('../protected/models/Models.php');
require_once("../protected/config/main.php");

include('../lib/functions.php');
include('../lib/scraps_lib.php');


// Seleccionem tots els vehicles que hem carregat que no tenen model
// associat

$sql = "SELECT * FROM a_vehicles";
$result = mysql_query($sql);


while ($vehicles = mysql_fetch_array($result)) {
    //unset($vehicle);
    unset($vehicle_nou);
    unset($model);
    unset($nou_model);
    
    //var_dump($node->element_1);    
    //print_r($vehicle);
    $vehicle_nou["id_vh"] = $vehicle["ID_VH"];
    $vehicle_nou["tip"] = $vehicle["TIP"];
    $vehicle_nou["mar"] = $vehicle["MAR"];
    $vehicle_nou["model"] = $vehicle["MOD"]; //canviema a Model pq es paraula reserva en mysql
    $vehicle_nou["ver"] = $vehicle["VER"];
    $vehicle_nou["a"] = $vehicle["A"];
    $vehicle_nou["cm"] = $vehicle["CM"];
     if ($vehicle["COL"] = '?')
        $vehicle["COL"] = null;
    $vehicle_nou["col"] = $vehicle["COL"];
    $vehicle_nou["m"] = $vehicle["M"];
    $vehicle_nou["mat"] = $vehicle["MAT"];
    $vehicle_nou["cbs"] = $vehicle["CBS"];
    $vehicle_nou["pts"] = $vehicle["PTs"];
    if ($vehicle["KM"] = '?')
        $vehicle["KM"] = null;
    $vehicle_nou["km"] = $vehicle["KM"]; // si no n'hi ha  = ?
    $vehicle_nou["frag"] = $vehicle["FRAG"];
    
    
    try {
        //$sql = "SELECT *,A100MOD.Nombre as modbase FROM B100MOD INNER JOIN A100MOD on B100MOD.clvvin = A100MOD.codigo WHERE B100MOD.Nombre = '" . $vehicle->MOD . "'";
        $sql = "SELECT * FROM a_mods WHERE model = '" . $vehicle_nou["model"] . "'";
        $result = mysql_query($sql);
        //Això ens avisarà d'afegir models mentre Europiezas no ens doni
        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_assoc($result);
            //-------
            $model["model"] = strtoupper($vehicle_nou["model"]);
            $model["mar"] = strtoupper($vehicle_nou["mar"]);
            $model["modbase"] = strtoupper($row["modbase"]);
            $categoria_ps = treure_color_en_categoria(treure_marca_en_categoria(sub_model_name($model["model"], null)));
            $model["categoria_ps"] = $categoria_ps;
            $nou_model = new Models(null, $model);            
            $nou_model->insertIntoDataBase();
        } else {
            // el modbase ho deixem en blanc per així posar-ho a mà
            $model["model"] = strtoupper($vehicle_nou["model"]);
            $model["mar"] = strtoupper($vehicle_nou["mar"]);
            $model["modbase"] = '';
            $categoria_ps = treure_color_en_categoria(treure_marca_en_categoria(sub_model_name($model["model"], null)));
            $model["categoria_ps"] = $categoria_ps;
            $nou_model = new Models(null, $model);
            $nou_model->insertIntoDataBase();
            mysql_query($sql);
        }
    } catch (Exception $e) {
        echo "<br>Ha saltat una excepció: " . $e;
    }

    if ($i > 50000)
        break;
    $i++;
    ini_set("max_execution_time", 30);
    //print_r($vehicle);
    //echo "<br><br>";
    // go to next <registro />
    $z->next('REGISTRO');
}

echo "<br><br>fi del proces. Congrats :P, num. registres: " . $i;
?>
