<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function getMarca($str)
{
    $marques = array("SEAT", "VOLVO","FORD","NISSAN","VOLKSWAGEN","CADILLAC");
    $str = strtoupper($str);
    return (in_string($marques, $str, "any"));
    
}

function quitar_abreviaturas($code) {
    $code = preg_replace('# Dcha.#', " derecha", $code);    
    $code = preg_replace('# Dcho.#', " derecho", $code);
    $code = preg_replace('# Dcha#', " derecha", $code);    
    $code = preg_replace('# Dcho#', " derecho", $code);
    $code = preg_replace('# DCHA#', " derecha", $code);    
    $code = preg_replace('# DCHO#', " derecho", $code);
    $code = preg_replace('# dcha#', " derecha", $code);    
    $code = preg_replace('# dcho.#', " derecho", $code);
    $code = preg_replace('# Izq.#', " izquierdo", $code);    
    $code = preg_replace('#Llanta #', "Llantas ", $code);
    $code = preg_replace('# llanta #', " llanta ", $code);
    //$code = preg_replace('#Motor #', "Motor - Motores ", $code);
    return  $code;
}


function limpiar_urlrewritten($code){
    $code = preg_replace("# #", "-", $code);
    $code = preg_replace("#mod.-#", "", $code);
    $code = preg_replace("#mod-#", "", $code);
    $code = preg_replace("#---#", "-", $code);
    $code = preg_replace("#--#", "-", $code);
    $code = preg_replace("#\/#", "-", $code);
    $code = preg_replace("#\.#", "-", $code);
    $code = preg_replace("#\[#", "", $code);
    $code = preg_replace("#\]#", "", $code);
    return $code;
}


function sub_model_name($code,$versio){
    //preg_match("#\[(.?)*\]#", $code, $dades);
    $code = preg_replace("#V1\[1991#", "V1 \[1991", $code);
    
    if (!isset($versio)){ 
        //echo "<br> sub_model_name: versió és null";
        $versio="";
        
        }
    if (!isset($code)){
        //echo "<br> sub_model_name: code de la versió és null";
        return "";
    }
    //print_r($code);
    //echo "<br>";
    $code = preg_replace("#[0-9][0-9]\/#", "", $code); 
    for ($i = 0; $i<100;$i++){
        if($i<10){
            //anys que són del 2000
            $code = preg_replace("#\[0$i-#", 2000+$i."-", $code);
            $code = preg_replace("#-0$i\]#", -(2000+$i), $code);
        }elseif ($i>10 && $i<20){
            //anys que són del 2000
            $code = preg_replace("#\[$i-#", 2000+$i."-", $code);
            $code = preg_replace("#-$i\]#", -(2000+$i), $code);
        }else{
            //anys que son del 1900
            $code = preg_replace("#\[$i-#", 1900+$i."-", $code);
            $code = preg_replace("#-$i\]#", -(1900+$i), $code);
        }
        //echo $i. " valor ".$code."<br>";    
    }
    
    $code = preg_replace("#-\]#", "", $code);
    $code = preg_replace("#\[#", "", $code);
    $code = preg_replace("#\]#", "", $code);
    //echo "<br> sub_model_versio1: ".$code;
    //$code = strtoupper($code." ".$versio);
    //echo "<br> sub_model_versio2: ".$code;
    //$code = treure_marca_en_categoria($code);  
    //echo "<br> sub_model_versio3: ".$code;
    return $code;
}

/**
* Checks if the given words is found in a string or not.
* 
* @param Array $words The array of words to be given.
* @param String $string The string to be checked on.
* @param String $option all - should have all the words in the array. any - should have any of the words in the array
* @return boolean True, if found, False if not found, depending on the $option
*/
function in_string ($words, $string, $option)
{
    $marca="";
    if ($option == "all") {
        $isFound = true;
        foreach ($words as $value) {
            $isFound = $isFound && (stripos($string, $value) !== false); // returns boolean false if nothing is found, not 0
            if ($isFound) {
                $marca=$value;
                break; // if a word was found, there is no need to continue
            }
        }
    } else {
        $isFound = false;
        foreach ($words as $value) {
            $isFound = $isFound || (stripos($string, $value) !== false);
            if ($isFound) {
                $marca=$value;
                break; // if a word was found, there is no need to continue
            }
        }
    }
    return $marca;
}

function CleanHTML($code) {
    $code = preg_replace('# style=\"(.*?)\"#', "", $code);    
    $code = preg_replace('#<b>#', "", $code);
    $code = preg_replace('#</b>#', "", $code);
    $code = preg_replace('#</B>#', "", $code);
    $code = preg_replace('#<span>#', "", $code);
    $code = preg_replace('#<span (.*?)>#', "", $code);
    $code = preg_replace('#<Span(.*?)>#', "", $code);    
    $code = preg_replace('#</span>#', "", $code);
    $code = preg_replace('#</Span>#', "", $code);
    $code = preg_replace('#<br>#', "", $code);
    $code = preg_replace('#<br />#', "", $code);
    $code = preg_replace('#<br/>#', "", $code);
    $code = sanear_string($code); //no hi ha manera de treure accents ni res.
    return str_replace("> <", "><", preg_replace(array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s'), array('>', '<', '\\1'), $code));
}

function file_get_contents_curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE); 
    $userAgent = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.152 Safari/537.22";
    //$useragent[] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
    sleep(0);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    $data = curl_exec($ch);
    curl_close($ch);
    sleep(0);
    return $data;
}

function url_exists($url) {
  $ch = curl_init($url);
  curl_setopt($ch,CURLOPT_HEADER,true);
  curl_setopt($ch,CURLOPT_POST,false);
  curl_setopt($ch,CURLOPT_FAILONERROR,true);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    // set user agent
    $userAgent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/534.53.11 (KHTML, like Gecko) Version/5.1.3";
    //$useragent="Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_exec($ch);
  $curlInfo = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  curl_close ($ch);

  if ($curlInfo != 200 && $curlInfo != 302 && $curlInfo != 304) {
     return false;
  } else {
      return true;
  }
}

function sanear_string($string)
{

    $string = trim($string);

    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );

    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );

    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );

    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );

    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );

    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );

    //Esta parte se encarga de eliminar cualquier caracter extraño
    
    $string = str_replace(
        array("\\", "¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "`", "]",
             "+", "}", "{", "¨", "´",
             ">", "< ", ";", ",", ":",
             ".", " "),
        ' ',
        $string
    );
    

    return $string;
}

?>
