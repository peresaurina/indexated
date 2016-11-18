<?php

include ('../lib/funcions_generals.php');
include("../google-url-master/autoload.php");
include("../protected/models/GoogleUrls.php");
include("../protected/config/main.php");


$site_url = "http://matcarrelage.com/fr/";

$googleUrl = new GoogleUrl();
$googleUrl->setLang('fr') // lang allows to adapt the query (tld, and google local params)
        ->setNumberResults(10);                        // 5 results per page
$googleUrl->setNumberResults(1000);
$simpsonPage1 = $googleUrl->setPage(0)->search($site_url); // simpsons results page 1 (results 1-20)
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
    unset($pagina);
}




/*
            if (Indexeds::existUrlDB($url) == '0') {
                if ($url == $result->getUrl()) {
                    echo "<br>Es la mateixa Indexada";
                    $pagina["url"] = $url;
                    $pagina["google_url1"] = '';
                    $pagina["google_index"] = '1';
                    $pagina_indexada = new Indexeds(null, $pagina);
                    $pagina_indexada->insertIntoDataBase();
                } else {

                    $pagina["url"] = $url;
                    $pagina["google_index"] = '0';
                    $pagina["google_url1"] = $result->getUrl();
                    $pagina_indexada = new Indexeds(null, $pagina);
                    //print_r($pagina_indexada);
                    $pagina_indexada->insertIntoDataBase();
                    echo "<br>No indexada</li>";
                }
                echo "</ul>";
                $i++;
                sleep(30);
                //if ($i == 5)
                */

?>