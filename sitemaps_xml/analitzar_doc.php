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
            echo " - url no existeix en bbdd";
            $googleUrl = new GoogleUrl();
            $googleUrl->setLang('fr') // lang allows to adapt the query (tld, and google local params)
                    ->setNumberResults(2);                        // 5 results per page            
            $googleUrl->setNumberResults(2);            
            $simpsonPage1 = $googleUrl->setPage(0)->search($url); // simpsons results page 1 (results 1-20)            
            // GET NATURAL RESULTS
            $positions = $simpsonPage1->getPositions();     
            try{
                //foreach ($positions as $result) {
                //entrem aquí les N vegades del foreach....i només hi hem d'entrar un cop!
                $result = $positions[0];
                //echo "<ul>";
                //echo "<li>position : " . $result->getPosition() . "</li>";
                //echo "<li>title : " . utf8_decode($result->getTitle()) . "</li>";
                //echo "<li>website : " . $result->getWebsite() . "</li>";
                //echo "<li>URL google : <a href='" . $result->getUrl() . "'>" . $result->getUrl() . "</a></li>";
                //echo "<li>URL sitemap : <a href='" . $url . "'>" . $url . "</a></li>";
                if (!is_null($result)){

                    if ($url == $result->getUrl()) {
                        echo "<br>Es la mateixa Indexada";
                        $pagina["url"] = $url;
                        $pagina["google_url1"] = '';
                        $pagina["google_index"] = '1';
                        $pagina_indexada = new Indexeds(null, $pagina);
                        $pagina_indexada->insertIntoDataBase();
                    } else {
                        echo "<br>No indexada";
                        $pagina["url"] = $url;
                        $pagina["google_index"] = '0';
                        $pagina["google_url1"] = $result->getUrl();
                        $pagina_indexada = new Indexeds(null, $pagina);
                        $pagina_indexada->insertIntoDataBase();                
                    }  
                    sleep(30);  

                }else{
                    echo "-> No result on Google";
                }      

            }catch (Exception $e){
                echo "  -> Saltem URL";
            }       
    }else{
        $i++;
        echo " -> Ja analitzada";
    }
    ini_set('max_execution_time', 300);
}
?>