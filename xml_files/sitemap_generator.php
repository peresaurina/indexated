<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// panell16.recambiosya.es/xml_files/sitemap_generator.php
// url_sitemap WEBMASTER TOOLS = pedir/sitemap_pedir.xml
// LIMIT DEL SITEMAP
// Number of URLs = 50,000
// File size ( uncompressed ) = 10MB

/*
  <?xml version="1.0" encoding="UTF-8"?>

  <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

  <url>

  <loc>http://www.example.com/</loc>

  <lastmod>2005-01-01</lastmod>

  <changefreq>monthly</changefreq>

  <priority>0.8</priority>

  </url>

  </urlset>
 */


error_reporting(~0);
ini_set('display_errors', 1);
ini_set('max_execution_time', 3000);

require_once("../protected/config/main.php");
require_once("../protected/config/functions.php");
include('../protected/models/Product.php');
include('../lib/functions.php');
include('../lib/scraps_lib.php');
include('../protected/models/Europ_Entradas.php');

//header('Content-type: application/rss+xml; charset=utf-8');

$nom_file = "sitemap_pedir.xml";
//../../recya16/pedir/
header("Content-Disposition: attachment; filename= " . $nom_file . "");
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');
/*
  echo "<?xml version='1.0' encoding='UTF-8'?>";
  echo "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>";
 */

$linies[] = "<?xml version='1.0' encoding='UTF-8'?>";
$linies[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

$sql = "SELECT url FROM ps_simplecanonicalurls 
        INNER JOIN ps_stock_available ON ps_stock_available.id_product = ps_simplecanonicalurls.id 
        where ps_stock_available.quantity = 1
        ";

$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {

    //$peca = new Peca($row["id"]);
    //$vehicle = new Vehicle($peca->getid_vh());
    //$model = new Models($vehicle->getModel());
    $row["url"] = preg_replace('#&#', '&amp;', $row["url"]);
    $link = utf8_encode($row["url"]);
    if ($link != "") {
        /*
          echo "<url>
          <loc>" . $link . "</loc>
          <lastmod>" . date('Y-m-d') . "</lastmod>
          <changefreq>weekly</changefreq>
          <priority>0.8</priority>
          </url>";
         */
        $linies[] = "<url>
            <loc>" . $link . "</loc>                
            <lastmod>" . date('Y-m-d') . "</lastmod>
            <changefreq>weekly</changefreq>     
            <priority>0.8</priority>           
        </url>";
    }
}
$linies[] = '</urlset>';

    foreach ($linies as $registre) {
        echo $registre;
    }


        
?>
