<?php

include ('../lib/funcions_generals.php');
include ('../protected/models/Mail_Europ.php');
include ('../protected/config/main.php');
// localhost/panell/mailing/read_doc.php
set_time_limit(300);
$emails[] = null;

$directorio = opendir("rebuts/"); //ruta actual
while ($archiu = readdir($directorio)) { //obtenemos un archivo y luego otro sucesivamente
    if (is_dir($archiu)) {//verificamos si es o no un directorio
        echo "[" . $archiu . "]<br />"; //de ser un directorio lo envolvemos entre corchetes
    } else {
        //echo $archiu . "<br />";
        $archiu_contingut = file_get_contents("rebuts/" . $archiu); //Guardamos archivo.txt en $archivo
        $archiu_contingut = ucfirst($archiu_contingut); //Le damos un poco de formato
        //$archiu = nl2br($archiu); //Transforma todos los saltos de linea en tag <br/>
        $emails = extract_email_address($archiu_contingut);
        foreach ($emails as $mail) {
            //validar que és un format mail correcte
            $mail = str_replace('mailto','',$mail);
            if ($mail != null) {
                //insertar db mails trobats, amb la data de quan s'ha creat, i un comptador
                //per saber el nombre de vegades que li enviem un mail
                $fields["email"] = $mail;
                $lead = new Mail_Europ(null, $fields);
                $lead->insertIntoDataBase();
                echo "<br>";
            }
        }
        //un cop copiats tots els mails posem l'arxiu a processat
        rename("rebuts/" . $archiu, "processats/" . $archiu);
    }
    set_time_limit(300);
}


//Hem de fer-los un mail on diem:
$body = "Buenos días, encuentra las piezas de segunda mano que estas buscando en RecambiosYa,"
        . " con fotografías de la piezas para que puedas encontrar y verificar la pieza que estas"
        . " buscando. "
        . "...y si no encuentras la pieza, cuándo la tengamos, <strong>serás el primero en saberlo !<br>.  "
        . "\n\n"
        . "RecambiosYa, piezas únicas de desguace";


//URL del artículo: http://www.ejemplode.com/20-php/138-ejemplo_de_leer_y_mostrar_archivo_de_texto_en_php.html
//Fuente: Ejemplo de Leer y mostrar archivo de texto en PHP

echo '<br><br><br><a href="http://panell16.recambiosya.es">Tornar</a>';
?>


