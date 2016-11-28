<?php

// localhost/indexated/sitemaps_xml/google_site_results.php
error_reporting(~0);
ini_set('display_errors', 1);
echo "Europe/Helsinki:" . time();
echo "<br>";
include('../lib/scraps_lib.php');
//require_once("/var/www/public_html/scraps/protected/config/main.php");

//botó siguiente: <a href="http://www.trapicheos.net/MR_siniestros/p/2.html" class="seguent"><span>SIGUIENTE</span> 
//posem scrap de 5 pàgines cada dia
for ($page = 1; $page < 4; $page++) {
    $start_number_result = ($page-1)*10;
    $url = "https://www.google.es/webhp?sourceid=chrome-instant&rlz=1C1AVNG_en&ion=1&espv=2&ie=UTF-8#q=site:matcarrelage.com/fr&start=".$start_number_result;
    
    // --------- CURL
        // create curl resource
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, $url);

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

    // $output contains the output string
    $output = curl_exec($ch);
    $content = $output;
    // close curl resource to free up system resources
    curl_close($ch); 
    
    // -------------------
    
    
    print_R($url);
    //$content = CleanHTML(file_get_contents_curl($url));
    $content = CleanHTML($content);
    print_r($content);
    preg_match_all('#<cite(.*?)>(.*?)</cite>#', $content, $matches);
    //print_r("<br>Page ".$page);
    print_r($matches);
    

    foreach ($matches[0] as $match) {
        $home = array();
        // Title
        preg_match('#<p class="titol">(.*?)</p>#', $match, $title);
        $home["title"] = strip_tags($title[0]);
        // URL
        preg_match('/<a\s+.*?href=[\"\']([^\"\' >]*)[\"\']?[^>]*>(.*?)<\/a>/i', $match, $url);
        $home["url"] = $url[1];
        

        /* ----------------------- GET DETAILS ----------------------- */
        try {
            if (!Car::exist($home["url"])) {
                print_r("<br>-----url: ".$home["url"]."<br><br>");
                $details = CleanHTML(file_get_contents_curl($home["url"]));
                // Insert date        
                preg_match('#<div>Publicaci(.)n:(.*?)</div>#', $details, $publicationDate);
                if (isset($publicationDate[2])) {
                    // Produce: <body text='black'>
                    $publicationDate[2] = str_replace(".", "/", $publicationDate[2]);
                    //ho posem al format que volem
                    $publicationDate[2] = preg_replace("/(\d+)\D+(\d+)\D+(\d+)/", "20$3/$2/$1", $publicationDate[2]);
                } else {
                    $publicationDate[2] = "";
                }
                // Brand
                preg_match('#<div class="categ">MARCA:(.*?)</div>#', $details, $marca);
                if (!(isset($marca[1]))) {
                    $marca[1] = "";
                }
                // Fuel Type
                preg_match('#<div class="categ">COMBUSTIBLE:(.*?)</div>#', $details, $combustible);
                if (!(isset($combustible[1][0])))
                    $combustible[1][0] = "";
                // Year
                preg_match('#<div class="categ">A(.)O:(.*?)</div>#', $details, $ano);
                if (!(isset($ano[2][0])))
                    $ano[2] = "";
                // Mileage
                preg_match('#<div class="categ">KM(..)(.*?)</div>#', $details, $km);
                if (!(isset($km[2][0])))
                    $km[2] = "";
                // Price
                preg_match('#<div class="categ">PRECIO:(.*?)</div>#', $details, $preu);
                if (!(isset($preu[1][0])))
                    $preu[1] = "";
                // Description        
                preg_match('#<div class="categ">DESCRIPCI(.{1})N:</div><div class="categ">(.*?)</div>#', $details, $descripcio);
                if (!(isset($descripcio[2][0])))
                    $descripcio[2] = "";
                // Imatges
                $images_obj[][] = null;
                $images_obj[0]["url"] = "http://scraps.porpra.es/website/img/img_no_disp_es.jpg";
                try {
                    preg_match_all('#<img src="(http://www.trapicheos.net/dfiles/([0-9a-z]*?)_G.jpg)" title="(.*?)"/></div>#', $details, $images);
                    /*for ($conta = 0; $conta < count($images[1]); $conta++) {
                        $images_obj[$conta]["url"] = $images[1][$conta];
                    }*/
                } catch (Exception $e) {                    
                }
                
                // OWNER
                $address = "";
                preg_match('#<p class="titol18">(.*?)</p>#', $details, $owner_scrap);
                try {
                    if (!(isset($owner_scrap[1][0]))) {
                        preg_match('#<p class="titol21_v">(.*?)</p><p class="categ">(.*?)</p>#', $details, $owner_scrap);
                        try {
                            $address = $owner_scrap[2][0];
                            //print_r($owner_scrap);
                        } catch (Exception $e) {
                            
                        }

                        if (!(isset($owner_scrap[1][0]))) {
                            $owner_scrap[1] = "";
                        }
                    }
                } catch (Exception $e) {
                    $owner_scrap[1] = "";
                }
                // Provincia
                preg_match('#<p class="categ">PROVINCIA:(.*?)</p>#', $details, $provincia);
                if (!(isset($provincia[1][0])))
                    $provincia[1] = "";
                // Telefon
                $details = sanear_string($details);
                preg_match('#<p class="categ">TEL(.*?)FONO: (.*?)</p><a href#', $details, $telefon);
                if (!(isset($telefon[2][0])))
                    $telefon[2] = "";

                //ASSIGNACIO DE VALORS A L'ARTICLE
                $owner = new Owner(NULL);
                try{
                    $owner->setName($owner_scrap[1]);
                    $owner->setEmail("");
                    $owner->setPhone(trim($telefon[2]));
                    $owner->setAddress($address);
                    $owner->setCity($provincia[1]);
                    $owner->setZipcode("");
                    $owner->setCountry("");
                    $owner->insertIntoDataBase();
                }catch (Exception $e){
                    $owner = new Owner('1');
                }

                // INSERTAR USER        
                $car = new Car(null);
                $car->setOwner($owner->getId());
                $car->setModel($home["title"]);
                $car->setProvincia($provincia[1]);
                $car->setBrand($marca[1]);
                $car->setPrice($preu[1]);
                $car->setRegistration("");
                $car->setYear($ano[2]);
                $car->setColor("");
                $car->setMileage($km[2]);
                $car->setEngine("");
                $car->setFuelType($combustible[1]);
                $car->setDescription($descripcio[2]);
                $car->setComment("");
                $car->setInsertDate($publicationDate[2]); //date 2013-03-22 00:00:00
                $car->setUrl($home["url"]);
                $car->insertIntoDataBase();
                try{
                    //$car->setImages($images_obj);
                    for ($conta = 0; $conta < count($images[1]); $conta++) {
                        $car->addimage($images[1][$conta]);
                        print_r($car->getId()." foto: ".$images[1][$conta]."<br>");
                    }
                    
                } catch (Exception $e) {                  
                }                
            
            }else{
                print_r("<br>---Entrat--url: ".$home["url"]."<BR><BR>");
            }
        } catch (Exception $e1) {
            //print_r("Exception on url: ".$home["url"],$e1->getMessage(), "\n");
            
        }
        unset($car);
        unset ($images_obj);
    }
}
die();
?>