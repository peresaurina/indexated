<?php

include ('../lib/funcions_generals.php');
include("../google-url-master/autoload.php");
include("../protected/models/Indexeds.php");
include("../protected/config/main.php");

//localhost/indexated/sitemaps_xml/list.php

$arxiu_contingut = file_get_contents("http://matcarrelage.com/1_fr_0_sitemap.xml");
//$arxiu_contingut = file_get_contents("http://matcarrelage.com/1_es_0_sitemap.xml");
//$arxiu_contingut = file_get_contents("../sitemaps/1_fr_0_sitemap.xml");
$xml = new SimpleXMLElement($arxiu_contingut);
$i = 1;
foreach ($xml->url as $url_list) {
    $url = utf8_encode($url_list->loc);
    echo "<br>" . $i . ";" . $url.";";
$i++;    
}
?>