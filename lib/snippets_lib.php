<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function getSnippets($content) {
    //print_r($content);
    
    //snippets name
    $patro = '#<h1 itemprop="name"\s*>(.*?)</h1>#';
    preg_match_all($patro, $content, $result);
    
    $product["name"] = $result[1][0];

    //snippets brand    
    $patro = '#<meta itemprop="brand" content="(.*?)"\s*/>#';
    preg_match_all($patro, $content, $result);
    //print_r($result);
    $product["brand"] = $result[1][0];
    
    //snippets availability
    $patro = '#<meta itemprop="availability" content="(.*?)"\s*>#';
    preg_match_all($patro, $content, $result);
    $product["availability"] = $result[1][0];

    //snippets price
    $patro = '#<meta itemprop="price" content="(.*?)"\s*/>#';
    preg_match_all($patro, $content, $result);
    $product["instock"] = $result[1][0];

    //snippets currency
    $patro = '#<meta itemprop="priceCurrency" content="(.*?)"\s*/>#';
    preg_match_all($patro, $content, $result);
    $product["currency"] = $result[1][0];

    //snippets sku
    $patro = '#<meta itemprop="sku" content="(.*?)"\s*/>#';
    preg_match_all($patro, $content, $result);
    $product["sku"] = $result[1][0];    
    //print_r($result);
    
    //agregate rating
    $patro = '#<meta itemprop="ratingValue" content="(.*?)"\s*>#';
    preg_match_all($patro, $content, $result);
    $product["ratingValue"] = $result[1][0];
    
    //max rating
    
    $patro = '#<meta itemprop="bestRating" content="(.*?)">#';
    preg_match_all($patro, $content, $result);
    $product["bestRating"] = $result[1][0];
    
    //review count
    $patro = '#<span itemprop="reviewCount">(.*?)</span>#';
    preg_match_all($patro, $content, $result);
    //print_r($result);
    $product["reviewCount"] = $result[1][0];       
    
    return ($product);
}

?>
