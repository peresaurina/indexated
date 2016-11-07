<?php

// panell16.recambiosya.es/updates/img_resize.php
include("../protected/models/ResizeImage.php");

$dir = opendir('../../europiezas/imatges/');
$i = 0;
while ($archivo = readdir($dir)) { //obtenemos un archivo y luego otro sucesivamente
    if (is_dir($archivo)) {//verificamos si es o no un directorio
        echo "[" . $archivo . "]<br />"; //de ser un directorio lo envolvemos entre corchetes
    } else {
        try {
            //echo $archivo . "<br />";
            if (!file_exists('../../europiezas/imatges200/' . $archivo)) {
                $resize = new ResizeImage('../../europiezas/imatges/' . $archivo);
                $resize->resizeTo(200, 200, 'maxHeight');
                $resize->saveImage('../../europiezas/imatges200/' . $archivo);
                $i++;
            }
        } catch (Exception $e) {
            echo "<br>Ha saltat excepció:" . $e . " : " . $archivo;
        }
    }
    ini_set("max_execution_time", 30);
    //if ($i > 100) break;    
}
echo "proces acabat amb éxit. Imatges transformades:" . $i;
?>

