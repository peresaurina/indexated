<!DOCTYPE html>
<?php
// http://localhost/panell16/mailing/cross_mailing.php
// http://panell16.recambiosya.es/mailing/cross_mailing.php

ini_set('display_errors', 1);
require_once("../protected/config/main.php");
//include('protected/models/Categoria.php');
include('../protected/models/Mailing.php');
include('../protected/models/Peca.php');
include('../protected/models/Product.php');
include('../protected/models/Vehicle.php');
include('../protected/models/Models.php');
include('../protected/models/Europ_Entradas.php');
include('../lib/scraps_lib.php');
include('../lib/functions.php');
include('../lib/PSWebServiceLibrary.php');

//buscar comandes pendents de confirmar de fa més de 
// 3 dies i amb data superior al 7/11/2014

// agafem totes les peticions rebudes, que faci més d'una setmana
// per tal de no ser repetitius
$query = "SELECT * 
                FROM a_peticio
                where product_id != 0
                and createdAt < DATE_SUB(NOW(),INTERVAL 6 DAY)
                order by createdAt desc                 
                ";
// SELECT field1, field2, field3,field4 FROM table
// GROUP BY field1, field2

$result = mysql_query($query);

if (!$result) {
    die('Invalid query: ' . mysql_error());
}
$i = 0;
while ($row = mysql_fetch_array($result)) {
    //if ($i > 46) {
    //$row["email"] = "pere.saurina@gmail.com";
    //print_r($row);
    if ($row["product_id"] > 8000000) {
        $vehicle = new Vehicle(preg_replace('#^8#', "C0", $row["product_id"]));
        $model = new Models($vehicle->getModel());
        $id_vehicle = preg_replace('#^8#', "C0", $row["product_id"]);
    } else {
        echo "<br>";
        $id_vehicle = $row["product_id"] - 1000000;
        echo "<br>";
        $peca = new Peca($row["product_id"] - 1000000);
        $vehicle = new Vehicle($peca->getid_vh());
        $model = new Models($vehicle->getModel());
        //print_r($peca);
    }
    // Buscar models cotxes similars al model
    //print_r($model);
    
    //busquem entre els cotxes que han entrat els últimes dies
    $sql_models = "Select * from a_vehicles 
                    inner join a_mods on a_mods.model = a_vehicles.model
                    where   a_vehicles.frag = 'NO'
                            and a_mods.modbase ='" . $model->getModbase() . "'
                            and a_vehicles.createdAt > DATE_SUB(NOW(),INTERVAL 6 DAY)
                            and a_vehicles.id_vh != '" . $id_vehicle . "' 
                            limit 0,1
                            ";
    $result_models = mysql_query($sql_models);
    
    if ((mysql_num_rows($result_models) > 0)&&($model->getModbase()!='')) {
        // --------------------------------------
        echo "<br>";
        $row_model = mysql_fetch_array($result_models);
        $vehicle_similar = new Vehicle($row_model["id_vh"]);
        $link_track = "?utm_source=Email&utm_medium=Email&utm_term=vehicle_similar&utm_content=vehicle_similar&utm_campaign=vehicle_similar";
        
        $fields["from"] = "atencionclientes@recambiosya.es";
        $fields["from_name"] = "RecambiosYa"; //això és from_email
        $fields["subject"] = "Piezas para tu coche";
        $fields["to"] = $row["email"];
        $fields["body"] = ''
                . '<strong>Hola,</strong><br><br><br>'
                . ' hemos recibido un vehiculo en nuestro desguace que puede interesarle,'
                . ' se trata de un <a href="' . $vehicle_similar->getUrl_es() . '"><strong>' . $model->getModbase() . '</strong></a>, lo tenemos en nuestro estoc'
                . ' para despiezar. Ver <a href="' . $vehicle_similar->getUrl_es() . '"><strong>' . $model->getModbase() . ' del desguace</strong></a> con fotografias y despiece.'
                . ' '
                . '<br><br><br>';
        $link_track_buscar = "?utm_source=Email&utm_medium=Email&utm_term=vehicle_similar&utm_content=buscar_peca&utm_campaign=buscar_peca";
        $link_buscar = "http://pedir.recambiosya.es/buscar_index.php".$link_track_buscar;
        $fields["body"] .= '<br><a href="'.$link_buscar.'"><strong><font color="#000000">RecambiosYa.es :</font>'
                . '<font color="#9b0000"> tu desguace on-line</font></strong></a>'
                . '<br><br>Respuesta rápida y directa, sin intermediarios, recambios baratos';

        print_r($fields);

        $email = new Mailing(null, $fields);

        if (isset($_REQUEST["sent"]) && ($_REQUEST["sent"] == "ok")) {
            $email->enviar();
            echo('<br>Habilitat enviament.');
        } else {
            echo('<br>No habilitat enviament.');
        }
        echo ('<br><br>---------------------------------');
        echo('<br><br><br>');
    }else{
        /*
        echo ('<br>');
        echo ('---------------------------------');
        echo ('<br>');
        echo ('No hem trobat vehicle equivalent');
        echo ('<br>');
        echo ('---------------------------------');
        echo ('<br><br><br>');
         * 
         */
    }
    $i++;
    set_time_limit(300);
}

// enviar un e-mail per cada comanda no confirmada
// amb el pdf de la comanda
?>
<html>

</head>

<body> 
    <form class = "form-horizontal" <?php if (isset($_REQUEST["sent"])) echo 'style="display: none;"'; ?>>
        <fieldset>

            <!--Form Name -->
            <legend>Enviar Mails</legend>

            <!--Button -->
            <div class = "control-group">
                <label class = "control-label" for = "singlebutton"></label>
                <div class = "controls">
                    <input id="sent" name="sent" value="ok" style="display: none"></input> 
                    <button id = "singlebutton" name = "singlebutton" class = "btn btn-primary">Enviar Mail</button>
                </div>
            </div>

        </fieldset>
    </form>
</body>
</html>