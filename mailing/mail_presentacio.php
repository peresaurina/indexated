<!DOCTYPE html>
<?php
// http://localhost/panell16/mailing/mail_presentacio.php
// http://panell16.recambiosya.es/mailing/mail_presentacio.php

include('../protected/config/main.php');
include('../protected/models/Mailing.php');

//buscar comandes pendents de confirmar de fa més de 
// 3 dies i amb data superior al 7/11/2014


$query = "SELECT * 
                FROM a_mail_europ
                where createdAt > curdate()-1
                ";
$result = mysql_query($query);

if (!$result) {
    die('Invalid query: ' . mysql_error());
}

$i=0;
while ($row = mysql_fetch_array($result)) {
    //if ($i > 46) {
        //$row["email"] = "pere.saurina@gmail.com";
        $fields["from"] = "atencionclientes@recambiosya.es";
        $fields["from_name"] = "RecambiosYa"; //això és from_email
        $fields["subject"] = "Tus piezas de desguace";
        $fields["to"] = $row["email"];
        $url ="http://www.recambiosya.es/pedir/buscar_index.php?utm_source=Email&utm_medium=Email&utm_term=presentacio&utm_content=presentacio&utm_campaign=presentacio";
        $fields["body"] = ''
                . ' Hola,<br><br>'
                . ' hemos recibido sus peticiones de piezas en RecambiosYa. Creemos que podemos ayudarle,'
                . ' hemos realizado <strong>fotografias de todas las piezas</strong> de nuestro estoc para que así pueda identificar'
                . ' exactamente la que usted necesita, ya que cada pieza es única.'
                . ' Visite <a href="'.$url.'">encontrar piezas</a>, seleccione su vehículo y podrá'
                . ' identificar la mejor pieza para usted.'
                . '<br><br><br>';

        $fields["body"] .= '<br><a href="'.$url.'"><strong><font color="#000000">RecambiosYa.es :</font>'
                . '<font color="#9b0000"> tu desguace on-line</font></strong></a>'
                . '<br><br>Respuesta rápida y directa, sin intermediarios, recambios baratos';

        $email = new Mailing(null, $fields);        
        $email->enviar();        
        $i++;   
        set_time_limit(300);
}

// enviar un e-mail per cada comanda no confirmada
// amb el pdf de la comanda

echo "<h1>Procés acabat amb éxit";
?>