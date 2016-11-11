<?php

include ('../lib/funcions_generals.php');
include("../google-url-master/autoload.php");
include("../protected/models/Indexeds.php");
include("../protected/config/main.php");

$arxiu_contingut = file_get_contents("http://matcarrelage.com/1_fr_0_sitemap.xml");
//$arxiu_contingut = file_get_contents("http://matcarrelage.com/1_es_0_sitemap.xml");
//$arxiu_contingut = file_get_contents("../sitemaps/1_fr_0_sitemap.xml");
$xml = new SimpleXMLElement($arxiu_contingut);
$i = 1;
foreach ($xml->url as $url_list) {

    $url = utf8_encode($url_list->loc);
    echo "<br>" . $i . " web : " . $url;

    if (Indexeds::existUrlDB($url) == '0') {

        $googleUrl = new GoogleUrl();
        $googleUrl->setLang('fr') // lang allows to adapt the query (tld, and google local params)
                ->setNumberResults(10);                        // 5 results per page
        $googleUrl->setNumberResults(1);
        $simpsonPage1 = $googleUrl->setPage(0)->search($url); // simpsons results page 1 (results 1-20)
        // GET NATURAL RESULTS
        $positions = $simpsonPage1->getPositions();

        foreach ($positions as $result) {

            //echo "<ul>";
            //echo "<li>position : " . $result->getPosition() . "</li>";
            //echo "<li>title : " . utf8_decode($result->getTitle()) . "</li>";
            //echo "<li>website : " . $result->getWebsite() . "</li>";
            //echo "<li>URL google : <a href='" . $result->getUrl() . "'>" . $result->getUrl() . "</a></li>";
            //echo "<li>URL sitemap : <a href='" . $url . "'>" . $url . "</a></li>";
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
                //  exit();
            }
        }
    } else {
        $i++;
        echo "-> Ja analitzada";
    }
    ini_set('max_execution_time', 300);
}
?>