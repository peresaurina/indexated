<?php

include ('../lib/funcions_generals.php');
include("../google-url-master/autoload.php");
include("../protected/models/Indexeds.php");
include("../protected/config/main.php");


$arxiu_contingut = file_get_contents("http://matcarrelage.com/1_fr_0_sitemap.xml");
$xml = new SimpleXMLElement($arxiu_contingut);
$i = 0;
foreach ($xml->url as $url_list) {

    $url = utf8_encode($url_list->loc);
    //print_r($url);
    
    if (Indexeds::existUrlDB($url) == '0') {
        $googleUrl = new GoogleUrl();
        $googleUrl->setLang('fr') // lang allows to adapt the query (tld, and google local params)
                ->setNumberResults(5);                        // 10 results per page
        //$googleUrl->setNumberResults(1);
        $simpsonPage1 = $googleUrl->setPage(0)->search($url); // simpsons results page 1 (results 1-20)
        // GET NATURAL RESULTS
        $positions = $simpsonPage1->getPositions();
        print_r($positions);
        foreach ($positions as $result) {

            echo "<ul>";
            echo "<li>position : " . $result->getPosition() . "</li>";
            echo "<li>title : " . utf8_decode($result->getTitle()) . "</li>";
            echo "<li>website : " . $result->getWebsite() . "</li>";
            echo "<li>URL google : <a href='" . $result->getUrl() . "'>" . $result->getUrl() . "</a></li>";
            echo "<li>URL sitemap : <a href='" . $url . "'>" . $url . "</a></li>";
            if ($url == $result->getUrl()) {
                echo "<li>Es la mateixa Indexada!!!!</li>";
                $pagina["url"] = $url;
                $pagina["google_index"] = '1';
                $pagina_indexada = new Indexeds(null, $pagina);
                $pagina_indexada->insertIntoDataBase();
            } else {
                $pagina["url"] = $url;
                $pagina["google_index"] = '0';
                $pagina_indexada = new Indexeds(null, $pagina);
                print_r($pagina_indexada);
                $pagina_indexada->insertIntoDataBase();
                echo "<li>No indexada</li>";
            }
            echo "</ul>";
            $i++;
            sleep(10);
            if ($i == 25)
                exit();
        }
    }else {
        echo "<ul><li>" . $url . " -> Ja analitzada</li></ul>";
    }    
    ini_set('max_execution_time', 30);
}
?>