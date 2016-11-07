<?php

/** CSV de PRODUCT
  id                      Active (0/1)                        Name*
 *  Categories (x,y,z,...)  Price tax excl. Or Price tax excl   Tax rules id        
  Wholesale price         On sale (0/1)                       Discount amount
 *  Discount percent        Discount from (yyy-mm-dd)           Discount to (yyy-mm-dd)
 *  Reference #             Supplier reference #                Supplier        
 *  Manufacturer            EAN13                               UPC        
 *  Ecotax                  Weight                              Quantity
 *  Short description       Description                         Tags (x,y,z,...)        
 *  Meta-title              Meta-keywords                       Meta-description        
 *  URL rewritten           Text when in-stock                  Text if back-order allowed
  available to order       date update product                 showprice
 *  Image URLs (x,y,z,...)  Delete existent image               Feature
 *  Only available online   Condition                           idtienda
 * */

include('Categoria.php');
//include('Europ_Entradas.php');

class Europ_Entradas {

    //<editor-fold desc="ATTRIBUTES">
    protected $codigo = null;
    protected $fentrada = null;
    protected $codempleado = null;
    protected $tipo = null;
    protected $tipo2 = null;
    protected $codmarca = null;
    protected $marca = null;
    protected $codmodelo = null;
    protected $modelo = null;
    protected $modelo_categoria = null;
    protected $version = null;
    protected $matricula = null;
    protected $fmatriculacion = null;
    protected $combustible = null;
    protected $combustible2 = null;    
    protected $bastidor = null;
    protected $estado = null;
    protected $otrosdatos = null;
    protected $admatriculacion = null;
    protected $entregatitular = null;
    /***** deixem uns camps sense entrar de la taula ***/
    protected $color = null;
    protected $metalizado = null;
    protected $carroceria = null;
    //0 >"" 1 > SIN DEFINIR  2>COUPE - 3>COMBI -  4>BERLINA 3P - 5> BERLINA 4P - 6>  Berlina 5P  - 7 >CABRIO  -8 < limousina - 9 > MOnovolumen 10 >Ranchera 11 >SUB 12>otro tipo
    protected $kms = null;
    protected $peso = null;
    protected $puertas = null;
    protected $cod_motor = null;
    protected $fragmentadora = null;
    protected $foto = null;
    protected $foto1 = null;
    protected $foto2 = null;
    protected $foto3 = null;
    protected $foto4 = null;
    protected $foto5 = null;
    protected $foto6 = null;
    protected $foto7 = null;
    protected $foto8 = null;
    protected $foto9 = null;
    protected $fotos = null;
    protected $descripcion_para_piezas = null;
    protected $categoria = null;
    
    function __construct($id, $fields = null) {
        if ($id) {
            return $this->__constructById($id);
        } else if (is_array($fields) && !is_null($fields)) {
            return $this->__constructByFields($fields);
        } else {
            return false;
        }
    }

    function __constructById($id) {
        $query = "SELECT * FROM `entradas` WHERE codigo = $id ";
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_assoc($result);
            // Fill attributes
            $this->__constructByFields($row);
            return true;
        } else {
            return false;
        }
    }

    function __constructByFields($fields) {
        $this->codigo = $fields["codigo"];
        isset($fields["fentrada"]) ? $this->fentrada = $fields["fentrada"] : null;
        isset($fields["codempleado"]) ? $this->codempleado = $fields["codempleado"] : null;        
        isset($fields["tipo"]) ? $this->tipo = $fields["tipo"] : null;
        isset($fields["tipo2"]) ? $this->tipo2 = $fields["tipo2"] : null;
        isset($fields["codmarca"]) ? $this->codmarca = $fields["codmarca"] : null;
        isset($fields["marca"]) ? $this->marca = $fields["marca"] : null;
        isset($fields["codmodelo"]) ? $this->codmodelo = $fields["codmodelo"] : null;
        isset($fields["modelo"]) ? $this->modelo = $fields["modelo"] : null;
        isset($fields["version"]) ? $this->version = $fields["version"] : null;
        isset($fields["matricula"]) ? $this->matricula = $fields["matricula"] : null;        
        isset($fields["fmatriculacion"]) ? $this->fmatriculacion = $fields["fmatriculacion"] : null;
        isset($fields["combustible"]) ? $this->combustible = $fields["combustible"] : null;
        isset($fields["combustible2"]) ? $this->combustible2 = $fields["combustible2"] : null;
        isset($fields["bastidor"]) ? $this->bastidor = $fields["bastidor"] : null;
        isset($fields["estado"]) ? $this->estado = $fields["estado"] : null;
        isset($fields["otrosdatos"]) ? $this->otrosdatos = $fields["otrosdatos"] : null;
        isset($fields["admatriculacion"]) ? $this->admatriculacion = $fields["admatriculacion"] : null;
        isset($fields["entregatitular"]) ? $this->entregatitular = $fields["entregatitular"] : null;        
        isset($fields["color"]) ? $this->color = strtoupper($fields["color"]) : null;
        isset($fields["metalizado"]) ? $this->metalizado = $fields["metalizado"] : null;
        isset($fields["carroceria"]) ? $this->carroceria = $fields["carroceria"] : null;
        (isset($fields["KMS"]) && $fields["KMS"] != '0') ? $this->kms = $fields["KMS"] : "-";
        isset($fields["PESO"]) ? $this->peso = $fields["PESO"] : null;
        isset($fields["PUERTAS"]) ? $this->puertas = $fields["PUERTAS"] : null;
        isset($fields["COD_MOTOR"]) ? $this->cod_motor = $fields["COD_MOTOR"] : null;
        isset($fields["fragmentadora"]) ? $this->fragmentadora = $fields["fragmentadora"] : null;
        isset($fields["foto"]) ? $this->foto = $fields["foto"] : null;   
        isset($fields["FOTO1"]) ? $this->foto1 = $fields["FOTO1"] : null;        
        isset($fields["FOTO2"]) ? $this->foto2 = $fields["FOTO2"] : null;
        isset($fields["FOTO3"]) ? $this->foto3 = $fields["FOTO3"] : null;
        isset($fields["FOTO4"]) ? $this->foto4 = $fields["FOTO4"] : null;
        isset($fields["FOTO5"]) ? $this->foto5 = $fields["FOTO5"] : null;
        isset($fields["FOTO6"]) ? $this->foto6 = $fields["FOTO6"] : null;
        isset($fields["FOTO7"]) ? $this->foto7 = $fields["FOTO7"] : null;
        isset($fields["FOTO8"]) ? $this->foto8 = $fields["FOTO8"] : null;
        isset($fields["FOTO9"]) ? $this->foto9 = $fields["FOTO9"] : null;
        $this->descripcion_para_piezas = "Pieza de segunda mano del modelo ".$this->getModelo().",".$this->getMarca();
        $this->descripcion_para_piezas = $this->descripcion_para_piezas." Recambio usado listo para ser servido. ";
        $this->descripcion_para_piezas = $this->descripcion_para_piezas."Si lo desea le podemos enviar fotografias más concretas de la pieza deseada.";    
        $this->categoria = Categoria::nom_categoria_model_b100($this->codmodelo);
    }   

    public function addLog($sender, $errorCode) {
        return false;
    }
    
    public function getCodigo() {
        return $this->codigo;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    public function getFentrada() {
        return $this->fentrada;
    }

    public function setFentrada($fentrada) {
        $this->fentrada = $fentrada;
    }

    public function getCodempleado() {
        return $this->codempleado;
    }

    public function setCodempleado($codempleado) {
        $this->codempleado = $codempleado;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function getTipo2() {
        return $this->tipo2;
    }

    public function setTipo2($tipo2) {
        $this->tipo2 = $tipo2;
    }

    public function getCodmarca() {
        return $this->codmarca;
    }

    public function setCodmarca($codmarca) {
        $this->codmarca = $codmarca;
    }

    public function getMarca() {
        return $this->marca;
    }

    public function setMarca($marca) {
        $this->marca = $marca;
    }

    public function getCodmodelo() {
        return $this->codmodelo;
    }

    public function setCodmodelo($codmodelo) {
        $this->codmodelo = $codmodelo;
    }

    public function getModelo() {
        return $this->modelo;
    }

    public function setModelo($modelo) {
        $this->modelo = $modelo;
    }
    
    public function getModelo_categoria() {
        return $this->modelo_categoria;
    }

    public function setModelo_categoria($modelo_categoria) {
        $this->modelo_categoria = $modelo_categoria;
    }

        public function getVersion() {
        return $this->version;
    }

    public function setVersion($version) {
        $this->version = $version;
    }

    public function getMatricula() {
        return $this->matricula;
    }

    public function setMatricula($matricula) {
        $this->matricula = $matricula;
    }

    public function getFmatriculacion() {
        return $this->fmatriculacion;
    }

    public function setFmatriculacion($fmatriculacion) {
        $this->fmatriculacion = $fmatriculacion;
    }

    public function getCombustible() {
        if ($this->combustible == 1){
            return "Diesel";
        }else{
            return "Gasolina";
        }
        //return $this->combustible;
    }

    public function setCombustible($combustible) {
        $this->combustible = $combustible;
    }

    public function getCombustible2() {
        return $this->combustible2;
    }

    public function setCombustible2($combustible2) {
        $this->combustible2 = $combustible2;
    }

    public function getBastidor() {
        return $this->bastidor;
    }

    public function setBastidor($bastidor) {
        $this->bastidor = $bastidor;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function getOtrosdatos() {
        return $this->otrosdatos;
    }

    public function setOtrosdatos($otrosdatos) {
        $this->otrosdatos = $otrosdatos;
    }

    public function getAdmatriculacion() {
        return $this->admatriculacion;
    }

    public function setAdmatriculacion($admatriculacion) {
        $this->admatriculacion = $admatriculacion;
    }

    public function getEntregatitular() {
        return $this->entregatitular;
    }

    public function setEntregatitular($entregatitular) {
        $this->entregatitular = $entregatitular;
    }

    public function getColor() {
        return $this->color;
    }

    public function setColor($color) {
        $this->color = $color;
    }

    public function getMetalizado() {
        return $this->metalizado;
    }

    public function setMetalizado($metalizado) {
        $this->metalizado = $metalizado;
    }

    public function getCarroceria() {
        return $this->carroceria;
    }

    public function setCarroceria($carroceria) {
        $this->carroceria = $carroceria;
    }

    public function getKms() {
        return $this->kms;
    }

    public function setKms($kms) {
        $this->kms = $kms;
    }

    public function getPeso() {
        return $this->peso;
    }

    public function setPeso($peso) {
        $this->peso = $peso;
    }

    public function getPuertas() {
        return $this->puertas;
    }

    public function setPuertas($puertas) {
        $this->puertas = $puertas;
    }

    public function getCod_motor() {
        return $this->cod_motor;
    }

    public function setCod_motor($cod_motor) {
        $this->cod_motor = $cod_motor;
    }
    
    public function getFOTO() {
        return $this->foto;
    }

    public function setFOTO($FOTO) {
        $this->foto = $FOTO;
    }

    public function getFOTO1() {
        return $this->foto1;
    }

    public function setFOTO1($FOTO1) {
        $this->foto1 = $FOTO1;
    }

    public function getFOTO2() {
        return $this->foto2;
    }

    public function setFOTO2($FOTO2) {
        $this->foto2 = $FOTO2;
    }

    public function getFOTO3() {
        return $this->foto3;
    }

    public function setFOTO3($FOTO3) {
        $this->FOTO3 = $FOTO3;
    }

    public function getFOTO4() {
        return $this->foto4;
    }

    public function setFOTO4($FOTO4) {
        $this->foto4 = $FOTO4;
    }

    public function getFOTO5() {
        return $this->foto5;
    }

    public function setFOTO5($FOTO5) {
        $this->foto5 = $FOTO5;
    }

    public function getFOTO6() {
        return $this->foto6;
    }

    public function setFOTO6($FOTO6) {
        $this->foto6 = $FOTO6;
    }

    public function getFOTO7() {
        return $this->foto7;
    }

    public function setFOTO7($FOTO7) {
        $this->foto7 = $FOTO7;
    }

    public function getFOTO8() {
        return $this->foto8;
    }

    public function setFOTO8($FOTO8) {
        $this->foto8 = $FOTO8;
    }

    public function getFOTO9() {
        return $this->foto9;
    }

    public function setFOTO9($FOTO9) {
        $this->foto9 = $FOTO9;
    }
    public function getDescripcion_para_piezas() {
        return $this->descripcion_para_piezas;
    }

    public function setDescripcion_para_piezas($descripcion_para_piezas) {
        $this->descripcion_para_piezas = $descripcion_para_piezas;
    }
    public function getCategoria() {
        return $this->categoria;
    }

    public function setCategoria($categoria) {
        $this->categoria = $categoria;
    }
    public function getFragmentadora() {
        return $this->fragmentadora;
    }

    public function setFragmentadora($fragmentadora) {
        $this->fragmentadora = $fragmentadora;
    }

    public function getdesc_prestashop($fields){                
        $txt='';
        
        $txt = " Vehiculo de la marca <b>".$this->getMarca()."</b>.";    
                    
        if ($fields["tipovehiculo"] != null) {
            $txt = $txt." ".$fields["tipovehiculo"].".";
        }
        if ($fields["modelo"] != null) {
            $txt = $txt." Modelo <b>".$fields["modelo"]."</b>";       
            }
            
        if ($fields["year"] != null) {
            $txt = $txt." del ".$fields["year"];       
            }
        if ($fields["combustible"] != null) {
            $txt = $txt." (".$fields["combustible"]." ).";       
            }
        if ($this->getColor() != null) {
            $txt = $txt." Recambios para xapa de color <b>".$this->getColor()."</b>. ";       
            }
        $txt = $txt." <br><br>Segun la opinion de nuestro experto en coches de segunda mano, recambios y siniestros, podemos destacar los siguientes aspectos del vehiculo para piezas de recambio usadas <br>";
        if ($fields["estadoMotor"] != null) {
            $txt = $txt." El estado evaluado del <b>motor</b> es ".$fields["estadoMotor"]."<br> ";             
            }
        if ($fields["estadoCambio"] != null) {
            $txt = $txt." El estado del <b>cambio de velocidades</b> es ".$fields["estadoCambio"]."<br> ";             
            }

        $txt = $txt." <br>Componentes o <b>piezas de recambio</b> que se pueden utilizar, segun nuestro experto <br><br>";

        if($fields["parachoques"] =="1"){
            $txt = $txt."<b>Parachoques frontal</b> en correcto estado para recambio.<br>";
        }else{
            //$txt = $txt."<b>Parachoques frontal</b> no recomendado para recambio.<br>";
        }

        if($fields["parachoquesTrasero"] =="1"){
            $txt = $txt."<b>Parachoques trasero</b> en correcto estado para recambio.<br>";
        }else{
            //$txt = $txt."<b>Parachoques trasero</b> no recomendado para recambio.<br>";
        }
        if($fields["capo"] =="1"){
            $txt = $txt."<b>Capo</b> en correcto estado para recambio.<br>";
        }else{
            //$txt = $txt."<b>Capo</b> no recomendado para recambio.<br>";
        }

        if($fields["opticaDerecha"] =="1"){
            $txt = $txt."Recambio <b>optica derecha</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Optica derecha</b> no utilizable para recambio.<br>";
        }
        if($fields["opticaIzquierda"] =="1"){
            $txt = $txt."Recambio <b>optica izquierda</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Optica izquierda</b> no utilizable para recambio.<br>";
        }
        if($fields["aletaDelanteraDerecha"] =="1"){
            $txt = $txt."Recambio <b>de aleta delantera derecha</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Aleta delantera derecha</b> no utilizable para recambio.<br>";
        }
        if($fields["aletaDelanteraIzquierda"] =="1"){
            $txt = $txt."Recambio <b>de aleta delantera izquierda</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Aleta delantera izquierda</b> no utilizable para recambio.<br>";
        }
        if($fields["aletaTraseraDerecha"] =="1"){
            $txt = $txt."Recambio <b>de aleta trasera derecha</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Aleta trasera derecha</b> no utilizable para recambio.<br>";
        }
        if($fields["aletaTraseraIzquierda"] =="1"){
            $txt = $txt."Recambio <b>de aleta trasera izquierda</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Aleta trasera izquierda</b> no utilizable para recambio.<br>";
        }
        if($fields["puertaDelanteraDerecha"] =="1"){
            $txt = $txt."Recambio <b>de puerta delantera derecha</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Puerta delantera derecha</b> no utilizable para recambio.<br>";
        }
        if($fields["puertaTraseraIzquierda"] =="1"){
            $txt = $txt."Recambio <b>de puerta delantera izquierda</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Puerta delantera izquierda</b> no utilizable para recambio.<br>";
        }
        if($fields["puertaTraseraDerecha"] =="1"){
            $txt = $txt."Recambio <b>de puerta trasera derecha</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Puerta trasera derecha</b> no utilizable para recambio.<br>";
        }
        if($fields["puertaTraseraIzquierda"] =="1"){
            $txt = $txt."Recambio <b>de puerta trasera izquierda</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Puerta trasera izquierda</b> no utilizable para recambio.<br>";
        }
        if($fields["retrovisorDerecho"] =="1"){
            $txt = $txt."Recambio <b>de retrovisor derecho</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Retrovisor derecho</b> no utilizable para recambio.<br>";
        }
        if($fields["retrovisorIzquierdo"] =="1"){
            $txt = $txt."Recambio <b>de retrovisor izquierdo</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Retrovisor izquierdo</b> no utilizable para recambio.<br>";
        }
        if($fields["faroDelanteroDerecho"] =="1"){
            $txt = $txt."Recambio <b>de faro delantero derecho</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Faro delantero derecho</b> no utilizable para recambio.<br>";
        }
        if($fields["faroDelanteroIzquierdo"] =="1"){
            $txt = $txt."Recambio <b>de faro delantero izquierdo</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Faro delantero izquierdo</b> no utilizable para recambio.<br>";
        }
        if($fields["faroTraseroDerecho"] =="1"){
            $txt = $txt."Recambio <b>de faro trasero derecho</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Piloto trasero derecho</b> no utilizable para recambio.<br>";
        }
        if($fields["faroTraseroIzquierdo"] =="1"){
            $txt = $txt."Recambio <b>de faro trasero izquierdo</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Piloto trasero izquierdo</b> no utilizable para recambio.<br>";
        }
        if($fields["lunetaFrontal"] == "1"){
            $txt = $txt."Recambio <b>de luneta frontal</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Luneta frontal</b> no utilizable para recambio.<br>";
        }
        if($fields["lunetaTrasera"] =="1"){
            $txt = $txt."Recambio <b>de luneta trasera</b> en buen estado.<br>";
        }else{
            //$txt = $txt."<b>Luneta trasera</b> no utilizable para recambio.<br><br><br>";
        }
        
        return $txt;
    }
    public function getFotos_urls(){
        $ids_presta_array = null;
        if (isset($this->foto)&&($this->foto != ""))
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/imatges/" . $this->foto;
        if (isset($this->foto2)&&($this->foto2 != ""))
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/imatges/" . $this->foto2;
        if (isset($this->foto3)&&($this->foto3 != ""))
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/imatges/" . $this->foto3;
        if (isset($this->foto4)&&($this->foto4 != ""))
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/imatges/" . $this->foto4;
        if (isset($this->foto5)&&($this->foto5 != ""))
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/imatges/" . $this->foto5;
        if (isset($this->foto6)&&($this->foto6 != ""))
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/imatges/" . $this->foto6;
        if (isset($this->foto7)&&($this->foto7 != ""))
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/imatges/" . $this->foto7;
        if (isset($this->foto8)&&($this->foto8 != ""))
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/imatges/" . $this->foto8;
        if (isset($this->foto9)&&($this->foto9 != ""))
            $ids_presta_array[] = "http://meet-greets.com/recambiosya/imatges/" . $this->foto9;
        
        return $ids_presta_array;
    }


}

?>
