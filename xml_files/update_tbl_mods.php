<?php

// localhost/panel/xml_files/update_tbl_mods.php
// panell.recambiosya.es/xml_files/update_tbl_mods.php

include('../protected/models/Vehicle.php');
include('../protected/models/Models.php');
require_once("../protected/config/main.php");

include('../lib/functions.php');
include('../lib/scraps_lib.php');


$sql = "SELECT * FROM a_mods";
$result = mysql_query($sql);

while ($row = mysql_fetch_array($result)) {
    //-------
    $model["model"] = strtoupper($row["model"]);
    $model["mar"] = strtoupper($row["mar"]);
    $model["modbase"] = strtoupper($row["modbase"]);
    $categoria_ps = treure_color_en_categoria(treure_marca_en_categoria(sub_model_name($row["model"], null)));
    $model["categoria_ps"] = $categoria_ps;
    $nou_model = new Models(null, $model);
    $nou_model->insertIntoDataBase();
    //---------    
}

echo "<br><br>fi del proces. Congrats :P";
?>
