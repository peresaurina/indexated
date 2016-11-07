<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// panell16.recambiosya.es/xml_files/sitemap_generator_1.php
// localhost/panell16/xml_files/sitemap_generator_1.php
// url_sitemap WEBMASTER TOOLS = pedir/sitemap_pedir.xml
// LIMIT DEL SITEMAP
// Number of URLs = 50,000
// File size ( uncompressed ) = 10MB




error_reporting(~0);
ini_set('display_errors', 1);
ini_set('max_execution_time', 3000);

require_once("../protected/config/main.php");
require_once("../protected/config/functions.php");
include('../protected/models/Peca.php');
include('../protected/models/Models.php');
include('../protected/models/Vehicle.php');
include('../protected/models/Product.php');
include('../lib/functions.php');
include('../lib/scraps_lib.php');
include('../protected/models/Europ_Entradas.php');

//header('Content-type: application/rss+xml; charset=utf-8');


$sql = "SELECT id_product,url,url_image,pza FROM ps_simplecanonicalurls 
        INNER JOIN ps_stock_available ON ps_stock_available.id_product = ps_simplecanonicalurls.id 
        LEFT JOIN a_peces ON ps_simplecanonicalurls.id = a_peces.codigo + 1000000 
        where ps_stock_available.quantity = 1 and url != 'http://www.recambiosya.es/-' and url_image is not null    
        
        ";

$result = mysql_query($sql);
$fitxer = 1;
$i = 0;

$whitelist = array(
    '127.0.0.1',
    '::1'
);

if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
    // not valid
    $cfile = '../sitemaps/sitemap_' . $fitxer . '.xml';
    $createfile = fopen($cfile, 'w');
} else {
    $cfile = '../../recya16/pedir/sitemap_' . $fitxer . '.xml';
    $createfile = fopen($cfile, 'w');
}

fwrite($createfile, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
fwrite($createfile, "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"\n");
fwrite($createfile, "xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\" >\n");
//fwrite($createfile, "xmlns:video=\"http://www.sitemaps.org/schemas/sitemap-video/1.1\">\n");
$fitxer++;

while ($row = mysql_fetch_array($result)) {

    // indicar limit de productes per cada sitemap
    if ($i > 4000) {
        fwrite($createfile, "</urlset>");
        fclose($createfile);

        if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            // not valid
            $cfile = '../sitemaps/sitemap_' . $fitxer . '.xml';
            $createfile = fopen($cfile, 'w');
        } else {
            $cfile = '../../recya16/pedir/sitemap_' . $fitxer . '.xml';
            $createfile = fopen($cfile, 'w');
        }
        
        fwrite($createfile, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
        fwrite($createfile, "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" ");
        fwrite($createfile, "xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\" >\n");
        //fwrite($createfile, "xmlns:video=\"http://www.sitemaps.org/schemas/sitemap-video/1.1\">\n");
        $i = 0;
        $fitxer++;
    }

    $row["url"] = preg_replace('#&#', '&amp;', $row["url"]);
    $link = utf8_encode($row["url"]);
    $row["url_image"] = preg_replace('#&#', '&amp;', $row["url_image"]);
    $link_image = utf8_encode($row["url_image"]);
    //$title = ucwords($peca->getpza()). " " . ucwords($model->getCategoria_ps());
    $title = ucwords($row["pza"]);

    if ($link != "") {

        $linia = "<url>\n
            <loc>" . $link . "</loc>\n                
            <lastmod>" . date('Y-m-d') . "</lastmod>\n";

        if ($link_image != "") {
            $peca = new Peca($row["id_product"] - 1000000);
            $vehicle = new Vehicle($peca->getid_vh());
            $model = new Models($vehicle->getModel());

            $title = ucwords($row["pza"]) . " " . ucwords(strtolower($vehicle->getMar())) . " " . ucwords(strtolower($model->getCategoria_ps()));

            $linia .= "<image:image>\n
                    <image:loc>" . $link_image . "</image:loc>\n
                    <image:title>" . $title . "</image:title>\n
                    </image:image>\n";
            print_r($linia);
        }

        $linia .= "<changefreq>weekly</changefreq>\n     
            <priority>0.8</priority>\n           
        </url>\n";
        //print_r($linia);
        fwrite($createfile, $linia);
        unset($linia);
        ini_set("max_execution_time", 30);
        $i++;
    }
}
fwrite($createfile, "</urlset>");
fclose($createfile);

//arxiu index de sitemaps
/*
  <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
  <loc>http://www.dominio.es/sitemap1.xml</loc>
  <lastmod>2004-10-01T18:23:17+00:00</lastmod>
  </sitemap>
  <sitemap>
  <loc>http://www.dominio.es/sitemap2.xml</loc>
  <lastmod>2005-01-01</lastmod>
  </sitemap>
  </sitemapindex>
 */


if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            // not valid
            $cfile = '../sitemaps/sitemap_index.xml';
            $createfile = fopen($cfile, 'w');
        } else {
            $cfile = '../../recya16/pedir/sitemap_index.xml';
            $createfile = fopen($cfile, 'w');
        }
fwrite($createfile, '<?xml version="1.0" encoding="UTF-8"?>');
fwrite($createfile, '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');       

for ($index = 1; $index < ($fitxer); $index++) {
    echo "<br>index" . $index;
    echo "<br>fitxe" . $fitxer;
    $cfile = 'http://www.recambiosya.es/pedir/sitemap_' . $index . '.xml';
    $text = '<sitemap>
                <loc>' . $cfile . '</loc>
                <lastmod>' . date('Y-m-d') . '</lastmod>
            </sitemap>';
    fwrite($createfile, $text);
}
fwrite($createfile, '</sitemapindex>');
fclose($createfile);

// rename("sitemaps/" . $archiu, "processats/" . $archiu);
?>
