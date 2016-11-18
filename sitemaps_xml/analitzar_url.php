<?php

include ('../lib/funcions_generals.php');
include("../google-url-master/autoload.php");
include("../protected/models/GoogleUrls.php");
include("../protected/config/main.php");


$site_url = "http://matcarrelage.com/fr/";
$num_page = 100;

$googleUrl = new GoogleUrl();
$googleUrl->setLang('fr') // lang allows to adapt the query (tld, and google local params)
        ->setNumberResults(10*$num_page);                        // 5 results per page
$googleUrl->setNumberResults(10*$num_page);


for ($page=0; $page < $num_page;$page++){
    echo "<br>Nova cerca num_page: ".$page."<br>";

    $simpsonPage1 = $googleUrl->setPage($page)->search($site_url);   
    // GET NATURAL RESULTS
    $positions = $simpsonPage1->getPositions();

    foreach ($positions as $result) {

        echo "<ul>";
        echo "<li>position : " . $result->getPosition() . "</li>";
        echo "<li>title : " . utf8_decode($result->getTitle()) . "</li>";
        echo "<li>website : " . $result->getWebsite() . "</li>";
        echo "<li>URL google : <a href='" . $result->getUrl() . "'>" . $result->getUrl() . "</a></li>";
        echo "</ul>";
        echo "<br> ---- <br>";
        $pagina["url"] = $result->getUrl();
        $pagina["title"] = utf8_decode($result->getTitle());
        $pagina["google_index"] = $result->getPosition();
        //print_r($pagina);

        $pagina_index = new GoogleUrls(null,$pagina);
        $pagina_index->insertIntoDataBase();
        //ini_set('max_execution_time', 300);   
    }
}


?>