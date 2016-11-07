<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function crear_img($texto) {
// Establecer el tipo de contenido de salida
header('Content-Type: image/png');

// Datos iniciales
//$texto = 'Garabatos Linux | Conocimiento libre con software libre';
$tamanio_fuente= 26;
$ancho=640;
$alto=480;
$fuente = 'fuente/Ubuntu-Regular.ttf';
//fin datos iniciales

// Crear la imagen con ancho y alto
$imagen = imagecreatetruecolor($ancho, $alto);
// Crear algunos colores para usar
$blanco = imagecolorallocate($imagen, 255, 255, 255);
$gris = imagecolorallocate($imagen, 128, 128, 128);
$negro = imagecolorallocate($imagen, 0, 0, 0);
//Establecemos el fondo de la imágen
imagefill($imagen,0,0,$blanco);

// Añadir sombra al texto
// la sombra siempre va primero
imagettftext($imagen, $tamanio_fuente, 0, 6, 32, $gris, $fuente, $texto);

// Añadir el texto
imagettftext($imagen, $tamanio_fuente, 0, 5, 30, $negro, $fuente, $texto);

// imagepng() creo una imagen png
//que posee mayor claridad
//que otros formatos
return imagepng($imagen);
//imagedestroy($imagen);
}
function delete_product($productId) {
    try {
        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, false);
        //print_r(PS_SHOP_PATH.' - '.PS_WS_AUTH_KEY);
        $opt['resource'] = 'products';            // Resource to use
        $opt['id'] = $productId;                             // ID to use
        $webService->delete($opt);                 // Delete
        echo 'Client ' . $productId . ' successfully deleted!'; // If we can see this message, that means we have not left the try block
    } catch (PrestaShopWebserviceException $ex) {
        $trace = $ex->getTrace();                // Retrieve all info on this error
        $errorCode = $trace[0]['args'][0]; // Retrieve error code
        if ($errorCode == 401)
            echo 'Bad auth key';
        else
            echo 'Other error: <br />' . $ex->getMessage();
        // Display error message{color}
    }
}

function existRedirect($url_product) {
    $url_product_redirect = preg_replace("#http://www.recambiosya.es#", "", $url_product);
    $query = 'SELECT * FROM `ps_lgseoredirect` WHERE url_old ="' . $url_product_redirect.'"';
    $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());
    if (mysql_num_rows($result) == 0)
        return false;
    else
        return true;
}

function urlProduct($id_product){
    try {
        //UTILITZANT WEBSERVICES
        
        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, false);
        // http://loclhost/prestashop/api/images/products/3000943/511
        // Define the resource
        $opt = array('resource' => 'products');
        // Define the resource id to modify
        $opt['id'] = $id_product;
        // Call the web service, recuperate the XML file
        $xml = $webService->get($opt);

        $resources = $xml->product[0]->associations[0];
        //print_r($resources);
        $json_xml = json_encode($xml);
        //$long = $xml->product[0]->associations[0]->images[0]->image[0]->id;
        $url_lin = (string) $xml->product[0]->link_rewrite[0]->language;
        
        $categoria_id = (string) $xml->product[0]->associations[0]->categories->category->id;
        $opt_cat = array('resource' => 'categories');
        $opt_cat['id'] = $categoria_id;
        $xml_cat = $webService->get($opt_cat);
        //print_r($xml_cat);        
        $nom_cat = (string)$xml_cat->category[0]->link_rewrite[0]->language;
        $nom_cat = preg_replace("# #","-",$nom_cat);
        return strtolower("/".$nom_cat."/".$id_product . "-" . $url_lin.".html");
                
        
    } catch (PrestaShopWebserviceException $ex) {
        // Shows a message related to the error
        echo 'Other error: <br />' . $ex->getMessage();
        $pieza_url = "http://www.recambiosya.es/almera-4-2002-2006-2-2-d-5p/" . $id_product . "-recambios-piloto-trasero-derecho-nissan.html";
        return $pieza_url;
    } catch (Exception $e) {
        echo 'No hi ha url';
        $pieza_url = "http://www.recambiosya.es/almera-4-2002-2006-2-2-d-5p/" . $id_product . "-recambios-piloto-trasero-derecho-nissan.html";
        return $pieza_url;
    }
}
function updateProductPrice($productId, $newPrice) {
    $allOK = false;
    try {
        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
        $opt = array('resource' => 'products');
        $opt['id'] = $productId;
        $xml = $webService->get($opt);
        /* List of nodes that can't modify:
         *  "manufacturer_name" - "position_in_category"
         *  "quantity" - "type"
         */

        unset($xml->children()->children()->manufacturer_name);
        unset($xml->children()->children()->position_in_category);
        unset($xml->children()->children()->quantity);
        unset($xml->children()->children()->type);

        //Posem nou preu:
        $xml->children()->children()->price = $newPrice;
        //Load new data to query generator
        $opt['putXml'] = $xml->asXML();
        $xml = $webService->edit($opt);

        //si arribem aqui tot ok. Pertant tornem true.
        return true;
    } catch (PrestaShopWebserviceException $ex) {
        // Control d'errors:
        // Si entra aquí és que hi ha hagut un error.
        return false;
    }
}

function updateProductStock($id_product, $quantity) {
    $query = "UPDATE ps_stock_available set quantity='" . $quantity . "' where id_product = '" . $id_product . "'";
    //echo $query;
    $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());
    return true;
}

function activeProduct($id_product, $active) {
    $query = "UPDATE ps_product set active='" . $active . "' where id_product = '" . $id_product . "'";
    echo $query;
    $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());
    return true;
}

/**
 * Funció per saber si existeix el producte amb aquesta id
 * @param int $id
 * @return boolean
 */
function existeixProducte($id) {
    $query = 'SELECT active FROM `ps_product` WHERE id_product=' . $id;
    $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());
    if (mysql_num_rows($result) == 0)
        return false;
    else
        return true;
}

function category_desc_anys($id) {
    $query = 'SELECT name FROM `ps_category_lang` WHERE id_category=' . $id;
    //echo "<br>".$query;
    $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());
    if (mysql_num_rows($result) == 0)
        return "";
    else {
        $row = mysql_fetch_row($result);
        //echo "<br>".$row[0]."<br>";
        preg_match_all("#\d{4}#", $row[0], $anys_array);
        //print_r ($anys_array);        
    }
    //return $anys_array;
    //print_r($anys_array);
    if (sizeof($anys_array[0]) == 2) {
        //echo "<br>entrem al 2";
        $anysi = $anys_array[0][0];
        while (($anysi <= $anys_array[0][1]) && ($anysi < 2020)) {
            $anys_validesa[] = $anysi;
            $anysi++;
        }
        $desc = "<br><br>Vehículo de los años: " . implode(', ', $anys_validesa);
    } elseif (sizeof($anys_array[0]) == 1) {
        //echo "<br>entrem al 1";
        $anysi = $anys_array[0][0];
        while (($anysi <= 2014) && ($anysi <= 2020)) {
            $anys_validesa[] = $anysi;
            $anysi++;
        }
        $desc = "<br><br>Vehículo de los años: " . implode(', ', $anys_validesa);
    } else {
        //no hi ha descripció
        //echo "<br>entrem al else";
        $anys_validesa = null;
        $desc = "";
    }

    return $desc;
}

function array_year_category($name_categoria) {
    /*
    $query = 'SELECT name FROM `ps_category_lang` WHERE name="' . $name_categoria.'"';
    //echo "<br>".$query;
    $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());
    if (mysql_num_rows($result) == 0)
        return "";
    else {
        $row = mysql_fetch_row($result);
        //echo "<br>".$row[0]."<br>";
        preg_match_all("#\d{4}#", $row[0], $anys_array);
        //print_r ($anys_array);        
    }*/
    
    preg_match_all("#\d{4}#", $name_categoria, $anys_array);
    //return $anys_array;
    //print_r($anys_array);
    if (sizeof($anys_array[0]) == 2) {
        //echo "<br>entrem al 2";
        $anysi = $anys_array[0][0];
        while (($anysi <= $anys_array[0][1]) && ($anysi < 2020)) {
            $anys_validesa[] = $anysi;
            $anysi++;
        }
        //$desc = "<br><br>Vehículo de los años: " . implode(',', $anys_validesa);
    } elseif (sizeof($anys_array[0]) == 1) {
        //echo "<br>entrem al 1";
        $anysi = $anys_array[0][0];
        while (($anysi <= 2014) && ($anysi <= 2020)) {
            $anys_validesa[] = $anysi;
            $anysi++;
        }
        //$desc = "<br><br>Vehículo de los años: " . implode(',', $anys_validesa);
    } else {
        //no hi ha descripció
        //echo "<br>entrem al else";
        $anys_validesa = null;        
    }

    return $anys_validesa;
}

function treure_marca_en_categoria($name) {
    //obtenir marques existents
    $name = strtoupper($name);
    
    $sql2 = "SELECT distinct(mar) as name FROM a_vehicles 
        where mar NOT in ('Santana','Galloper')";
    
    $result2 = mysql_query($sql2);
    while ($row2 = mysql_fetch_array($result2)) {
        $marques[] = strtoupper($row2["name"]);
        $name = trim(str_replace(strtoupper($row2["name"]), "", $name));
    }
    //echo "<br> treure marca en categoria1: ".$name;
    if (isset($marques)) {
        $name = trim(str_replace($marques, "", $name));
    }
    //echo "<br> treure marca en categoria2: ".$name;
    return $name;
}

function treure_color_en_categoria($name) {
    //obtenir marques existents
    //echo "<br> treure_color_en categoria 1: ".$name;
    /*
    $sql_color = "SELECT distinct(color) FROM `entradas` where color !=''";
    $result_color = mysql_query($sql_color);
    $name = strtoupper($name);
    while ($row_color = mysql_fetch_array($result_color)) {
        $colors[] = strtoupper($row_color["color"]);
    }*/
    //treiem els parèntesis si n'hi ha, perquè a vegades el color
    //va entre parèntesi
    $name = preg_replace('#\(#', "", $name);
    $name = preg_replace('#\)#', "", $name);
    //echo "<br> treure_color_en categoria 2: ".$name;
    //posem espai davant el color perquè no passi cOROlla -> clla
    /*
    foreach ($colors as $color) {
        $name = trim(str_replace(" " . $color, "", $name));
        $name = trim(str_replace(" (" . $color, "", $name));
    }*/
    //echo "<br> treure_color_en categoria 3: ".$name;
    return $name;
}

function marca_normalizar($code) {
    $code = strtoupper($code);
    $code = preg_replace('#M. BENZ#', "MERCEDES", $code);
    $code = preg_replace('#\(#', "", $code);
    $code = preg_replace('#\)#', "", $code);
    $code = trim($code);
    return $code;
}

/**
 * Funció per normalitzar el mom de model d'un producte
 * @param type $marca i $model
 * @return boolean
 */

/**
 * Funció per normalitzar el mom de model d'un producte
 * @param type $marca i $model
 */
function mostraFormulari($id) {
    //obtenim dades i mostrem formulari:

    /*
      $query = 'SELECT name, description, description_short FROM `ps_product_lang` WHERE id_product=' . $id;
      $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());
      while ($row = mysql_fetch_assoc($result)) {
      $nom = $row["name"];
      $descripcio = $row["description"];
      $descripcio_curta = $row["description_short"];
      }
     */
    $query = 'SELECT name, description, description_short FROM `ps_product_lang` WHERE id_product=' . $id;
    $cotxe = new Product($id);
    $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
        $nom = $cotxe->getName();
        $descripcio = $cotxe->getDescription();
        $descripcio_curta = $row["description_short"];
    }

    $cotxe_europiezas = new Europ_Entradas($id - 8000000);
    ?>

    <form method="post" action="form_update.php">
        <p><b><?php echo $nom ?></b></p>
        <p><?php echo utf8_decode("Descripció curta:"); ?></p>
        <textarea class="form-control span6" rows="6" name="short_description"><?php echo utf8_decode($descripcio_curta) ?></textarea>
        <p><?php echo utf8_decode("Descripció llarga:"); ?></p>
        <textarea class="form-control span6" rows="10" name="description"><?php echo utf8_decode($descripcio) ?></textarea>
        <input type="hidden" value="<?php echo $id; ?>" name="id">
        <br>
        <!-- FORM ADD DESCRIPTION -->



        <!-- Select Basic -->
        <div class="control-group">
            <label class="control-label" for="selectbasic">Marca </label>
            <div class="controls">
                <select id="selectbasic" name="selectbasic" class="input-medium">
                    <option><?php echo $cotxe_europiezas->getMarca(); ?></option>
                    <option>OTRA</option>
                </select>
            </div>
        </div>

        <!-- Text input-->
        <div class="control-group">
            <label class="control-label" for="modelo">modelo</label>
            <div class="controls">
                <input id="modelo" name="modelo" type="text" placeholder="" class="input-xlarge" value="<?php echo $cotxe_europiezas->getModelo(); ?>">
                </input>
            </div>
        </div>



        <!-- Text input-->
        <div class="control-group">
            <label class="control-label" for="motor">Motor</label>
            <div class="controls">
                <input id="motor" name="motor" type="text" placeholder="" class="input-xlarge" value="<?php echo $cotxe_europiezas->getCod_motor(); ?>">

            </div>
        </div>

        <!-- Select Basic -->
        <div class="control-group" name="estadoMotor">
            <label class="control-label" for="estadoMotor">estadoMotor</label>
            <div class="controls">
                <label class="radio inline" for="radios-0">
                    <input type="radio" name="estadoMotor" id="radios-0" value="perfecto">
                    Perfecto
                </label>
                <label class="radio inline" for="radios-1">
                    <input type="radio" name="estadoMotor" id="radios-1" value="reutilizable">
                    reutilizable
                </label>
                <label class="radio inline" for="radios-2">
                    <input type="radio" name="estadoMotor" id="radios-2" value="no reciclable">
                    no reciclable
                </label>                         
            </div>
        </div>

        <div class="control-group" name="combustible">
            <label class="control-label" for="combustible">Combustible</label>
            <div class="controls">
                <label class="radio inline" for="radios-0">
                    <input type="radio" name="combustible" id="radios-0" value="Gasoil" checked="checked">
                    Gasoil
                </label>
                <label class="radio inline" for="radios-1">
                    <input type="radio" name="combustible" id="radios-1" value="Gasolina">
                    Gasolina
                </label>
                <label class="radio inline" for="radios-2">
                    <input type="radio" name="combustible" id="radios-2" value="Hybrido">
                    Hybrido
                </label>
            </div>
        </div>     

        <!-- Select Basic -->
        <div class="control-group">
            <label class="control-label" for="any">Any</label>
            <div class="controls">
                <select id="year" name="year" class="input-small">
                    <option value="2013">2013</option>
                    <option value="2012">2012</option>
                    <option value="2011">2011</option>
                    <option value="2010">2010</option>
                    <option value="2009">2009</option>
                    <option value="2008">2008</option>
                    <option value="2007">2007</option>
                    <option value="2006">2006</option>
                    <option value="2005">2005</option>
                    <option value="2004">2004</option>
                    <option value="2003">2003</option>
                    <option value="2002">2002</option>
                    <option value="2001">2001</option>
                    <option value="2000">2000</option>
                    <option value="1999">1999</option>
                    <option value="1998">1998</option>
                    <option value="1997">1997</option>
                    <option value="1996">1996</option>
                    <option value="1995">1995</option>
                    <option value="1994">1994</option>
                </select>
            </div>
        </div>

        <!-- Text input-->
        <div class="control-group">
            <label class="control-label" for="km">Kilometres</label>
            <div class="controls">
                <input id="km" name="km" type="text" placeholder="" class="input-small" value="<?php echo $cotxe_europiezas->getKms(); ?>">

            </div>
        </div>

        <!-- Select Basic -->
        <div class="control-group" name="tipovehiculo">
            <label class="control-label" for="tipovehiculo">Tipus de vehicle</label>
            <div class="controls">
                <label class="radio inline" for="radios-0">
                    <input type="radio" name="tipovehiculo" id="radios-0" value="utilitario">
                    utilitario
                </label>
                <label class="radio inline" for="radios-1">
                    <input type="radio" name="tipovehiculo" id="radios-1" value="hibrido">
                    Hybrido
                </label>
                <label class="radio inline" for="radios-2">
                    <input type="radio" name="tipovehiculo" id="radios-2" value="berlina">
                    berlina
                </label>
                <label class="radio inline" for="radios-3">
                    <input type="radio" name="tipovehiculo" id="radios-3" value="monovolumen">
                    monovolumen
                </label>
                <label class="radio inline" for="radios-4">
                    <input type="radio" name="tipovehiculo" id="radios-4" value="furgoneta">
                    furgoneta
                </label>
                <label class="radio inline" for="radios-5">
                    <input type="radio" name="tipovehiculo" id="radios-5" value="otros">
                    otros
                </label>                            
                </select>
            </div>
        </div>

        <!-- Select Basic -->
        <div class="control-group">
            <label class="control-label" for="color">Color</label>
            <div class="controls">
                <select id="color" name="color" class="input-xlarge">
                    <option><?php echo $cotxe_europiezas->getColor(); ?></option>
                    <option>blanco</option>
                    <option>amarillo</option>
                    <option>naranja</option>
                    <option>rojo</option>
                    <option>azul</option>
                    <option>azul oscuro</option>
                    <option>verde</option>
                    <option>marron</option>
                    <option>gris</option>
                    <option>gris oscuro</option>
                    <option>negro</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="numpuertas">numpuertas</label>
            <div class="controls">
                <input id="numpuertas" name="numpuertas" type="text" placeholder="" class="input-small" value="<?php echo $cotxe_europiezas->getPuertas(); ?>">
            </div>
        </div>
        <!-- Select Basic -->
        <div class="control-group" name="estadoCambio">
            <label class="control-label" for="estadoCambio">Estat del canvi</label>
            <div class="controls">
                <label class="radio inline" for="radios-0">
                    <input type="radio" name="estadoCambio" id="radios-0" value="Perfecto">
                    Perfecto
                </label>
                <label class="radio inline" for="radios-1">
                    <input type="radio" name="estadoCambio" id="radios-1" value="reutilizable">
                    reutilizable
                </label>
                <label class="radio inline" for="radios-2">
                    <input type="radio" name="estadoCambio" id="radios-2" value="no reciclable">
                    no reciclable
                </label>                         
            </div>
        </div>

        <table class="table table-striped span8">
            <tr>
                <td><h4>Omplir per fer descripcions del cotxe:</h4></td>
                <td></td>
            </tr>

            <tr><td><input type="checkbox" name="parachoques" checked> parachoquesFrontal</td>
                <td><input type="checkbox" name="parachoquesTrasero" checked> parachoquesTrasero</td></tr>

            <tr><td><input type="checkbox" name="lunetaFrontal" checked> lunetaFrontal</td>
                <td><input type="checkbox" name="lunetaTrasera" checked> lunetaTrasera</td>
            </tr>  
            <tr><td><input type="checkbox" name="capo" checked>capo</td>
                <td><input type="checkbox" name="asientos" checked>asientos</td>
            </tr>
            <tr><td><input type="checkbox" name="opticaIzquierda" checked> opticaIzquierda</td>
                <td><input type="checkbox" name="opticaDerecha" checked> opticaDerecha</td>
            </tr>
            <tr>
                <td><input type="checkbox" name="aletaDelanteraIzquierda" checked> aletaDelanteraIzquierda</td>
                <td><input type="checkbox" name="aletaDelanteraDerecha" checked> aletaDelanteraDerecha</td>
            </tr>
            <tr>
                <td><input type="checkbox" name="aletaTraseraIzquierda" checked> aletaTraseraIzquierda</td>
                <td><input type="checkbox" name="aletaTraseraDerecha" checked> aletaTraseraDerecha</td>
            </tr>
            <tr>
                <td><input type="checkbox" name="puertaDelanteraIzquierda" checked> puertaDelanteraIzquierda</td>
                <td><input type="checkbox" name="puertaDelanteraDerecha" checked> puertaDelanteraDerecha</td>
            </tr>
            <tr>
                <td><input type="checkbox" name="puertaTraseraIzquierda" checked> puertaTraseraIzquierda</td>
                <td><input type="checkbox" name="puertaTraseraDerecha" checked> puertaTraseraDerecha</td>
            </tr>
            <tr>
                <td><input type="checkbox" name="retrovisorIzquierdo" checked> retrovisorIzquierdo</td>
                <td><input type="checkbox" name="retrovisorDerecho" checked> retrovisorDerecho</td>
            </tr>
            <tr>
                <td><input type="checkbox" name="faroDelanteroIzquierdo" checked> faroDelanteroIzquierdo</td>
                <td><input type="checkbox" name="faroDelanteroDerecho" checked> faroDelanteroDerecho</td>
            </tr>
            <tr>                                        
                <td><input type="checkbox" name="faroTraseroIzquierdo" checked> faroTraseroIzquierdo</td>
                <td><input type="checkbox" name="faroTraseroDerecho" checked> faroTraseroDerecho</td>                              
            </tr>
            </tbody>                    
        </table>
        <!-- FORM ADD DESCRIPTION END -->
        <input type="submit" value="Enviar" class="btn btn-primary" />
    </form>
    <button class="btn btn-primary" onclick="location.href='form_index.php'">Descartar</button>

    <?php
}

/**
 * Funció per fer l'update a la bdd
 * 
 * @param int $id
 * @param string $short
 * @param string $desc
 */
/*
  function updateProduct($id, $short, $desc) {
  $short_ok = htmlentities($short);
  $desc_ok = htmlentities($desc);
  $query = 'UPDATE `ps_product_lang` SET `description`="' . $desc . '",`description_short`="' . $short . '" WHERE id_product=' . $id;
  $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());

  header("Location: index.php?err=0");
  }
 */
?>
