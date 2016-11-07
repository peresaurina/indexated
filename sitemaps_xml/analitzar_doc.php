<?php

include ('../lib/funcions_generals.php');
include("../google-url-master/autoload.php");


$arxiu_contingut = file_get_contents("http://matcarrelage.com/1_fr_0_sitemap.xml");
$xml = new SimpleXMLElement($arxiu_contingut);
foreach ($xml->url as $url_list) {
    $url = utf8_encode($url_list->loc);
    //echo $url . "<br>";

    
    $googleUrl = new GoogleUrl();
    $googleUrl->setLang('es') // lang allows to adapt the query (tld, and google local params)
            ->setNumberResults(10);                        // 10 results per page
    //$acdcPage1 = $googleUrl->setPage(0)->search("acdc"); // acdc results page 1 (results 1-10)
    //$acdcPage2 = $googleUrl->setPage(1)->search("acdc"); // acdc results page 2 (results 11-20)
    //$url = "bascara";
    $googleUrl->setNumberResults(1);
    $simpsonPage1 = $googleUrl->setPage(0)->search($url); // simpsons results page 1 (results 1-20)
    //
    //
    //print_r($simpsonPage1);
    
    // GET NATURAL RESULTS
    $positions = $simpsonPage1->getPositions();

    echo "results for " . $simpsonPage1->getKeywords();
    echo "<ul>";
    foreach ($positions as $result) {
        echo "<li>";
        echo "<ul>";
        echo "<li>position : " . $result->getPosition() . "</li>";
        echo "<li>title : " . utf8_decode($result->getTitle()) . "</li>";
        echo "<li>website : " . $result->getWebsite() . "</li>";
        echo "<li>URL : <a href='" . $result->getUrl() . "'>" . $result->getUrl() . "</a></li>";
        echo "<li>URL : <a href='" . $url . "'>" . $url . "</a></li>";
        if ($url == $result->getUrl()) echo "<li>És la mateixa!!!!</li>";
        echo "</ul>";
        echo "</li>";
    }
    echo "</ul>";
    exit;
    sleep(30);
}

?>


