<?php

include ('../lib/funcions_generals.php');
//include ('../protected/models/Mail_Europ.php');
//include ('../protected/config/main.php');
// localhost/panell/mailing/read_doc.php
//set_time_limit(300);
//$emails[] = null;
/*
$directorio = opendir("rebuts/"); //ruta actual
while ($archiu = readdir($directorio)) { //obtenemos un archivo y luego otro sucesivamente
    if (is_dir($archiu)) {//verificamos si es o no un directorio
        echo "[" . $archiu . "]<br />"; //de ser un directorio lo envolvemos entre corchetes
    } else {
 */       
        //$archiu_contingut = file_get_contents("rebuts/" . $archiu); //Guardamos archivo.txt en $archivo
        $arxiu_contingut = file_get_contents("http://matcarrelage.com/1_fr_0_sitemap.xml");
        //print_r($arxiu_contingut);
        //$z = new XMLReader();
        //$z->open($file);
        //$doc = new DOMDocument;
        
        
        $xml = new SimpleXMLElement($arxiu_contingut);
        foreach ($xml->url as $url_list) {
            $url = $url_list->loc;
            echo $url."<br>";
        }
        
       
        //un cop copiats tots els mails posem l'arxiu a processat
        //rename("rebuts/" . $archiu, "processats/" . $archiu);
 
?>


