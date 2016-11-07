<!DOCTYPE html>
<?php
// http://localhost/panell16/mailing/mail_200.php
// http://panell16.recambiosya.es/mailing/mail_200.php

include('../protected/config/main.php');
include('../protected/models/Mailing.php');

$query = "SELECT * FROM a_mail_europ where mail_first=0 order by updatedAt asc limit 0,200";
$result = mysql_query($query);

if (!$result) {
    die('Invalid query: ' . mysql_error());
}

$i=0;
while ($row = mysql_fetch_array($result)) {
        $fields["from"] = "atencionclientes@recambiosya.es";
        $fields["from_name"] = "RecambiosYa"; //això és from_email
        $fields["subject"] = "50.000 piezas de desguace";
        $fields["to"] = $row["email"];
        //$fields["to"] = "pere.saurina@gmail.com";
        
        $url ="http://www.recambiosya.es/pedir/buscar_index.php?utm_source=Email&utm_medium=Email&utm_term=mail5000&utm_content=mail5000&utm_campaign=mail5000";
        $fields["body"] = ''
                . ' Hola,<br><br>'
                . ' en <strong>RecambiosYa</strong> ya le podemos ofrecer más de <h2><font color="#9b0000">50.000 referencias</font></h2> en estoc para su vehículo.'
                . ' Todas las referencias con <strong>fotografías</strong> para que así pueda identificar'
                . ' exactamente la que usted necesita, <strong>ya que cada pieza es única.</strong>'
                . ' Visite <a href="'.$url.'">encontrar piezas</a>, seleccione su vehículo y podrá'
                . ' identificar la mejor pieza para usted.'
                . '<br><br><br>';

        $fields["body"] .= '<br><a href="'.$url.'"><strong><font color="#000000">RecambiosYa.es :</font>'
                . '<font color="#9b0000"> tu desguace on-line</font></strong></a>'
                . '<br><br>Respuesta rápida y directa, sin intermediarios, recambios baratos';

        $email = new Mailing(null, $fields);
        $email->enviar();     

        $query_upd = "UPDATE a_mail_europ SET mail_first='1',updatedAt=NOW() where email='".$row["email"]."'";
        $result_upd = mysql_query($query_upd);
        if (!$result_upd) {
            die('Invalid query update table: ' . mysql_error());
        }  
        $i++;
        set_time_limit(300);
        
}

echo utf8_encode("<h1>Procés acabat amb éxit. Mails enviats:".$i);

?>
