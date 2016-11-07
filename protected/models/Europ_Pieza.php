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
include('Europ_Entradas.php');

class Europ_Pieza{
    //taula Almace
    //<editor-fold desc="ATTRIBUTES">
    protected $id = null;
    protected $clvaut = null;
    protected $clvpie = null;
    protected $refer = null;
    protected $nota = null;
    protected $costo = null;
    protected $operario = null;
    protected $tipo = null;
    protected $texto = null;
    protected $viuda = null;
    protected $recibo = null;
    protected $albaran = null;
    protected $foto = null;
    protected $mdlo = null;    
    protected $ubx = null;
    protected $uby = null;
    protected $estanteria = null;
    protected $cod_motor = null;
    protected $n_entrega = null;
    protected $id_contenedor = null;
    protected $foto2 = null;
    protected $foto3 = null;
    protected $foto4 = null;
    protected $foto5 = null;
    protected $inclux = null;
    protected $cotxe;
        
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
        $query = "SELECT * FROM `Almacen` WHERE codigo = $id ";
        //echo $query;
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
        $this->id = $fields["codigo"];
        isset($fields["clvaut"]) ? $this->clvaut = $fields["clvaut"] : null;
        isset($fields["clvpie"]) ? $this->clvpie = $fields["clvpie"] : null;
        isset($fields["refer"]) ? $this->refer = $fields["refer"] : null;
        isset($fields["nota"]) ? $this->nota = $fields["nota"] : null;
        isset($fields["costo"]) ? $this->costo = $fields["costo"] : null;
        isset($fields["operario"]) ? $this->operario = $fields["operario"] : null;
        isset($fields["tipo"]) ? $this->tipo = $fields["tipo"] : null;
        isset($fields["texto"]) ? $this->texto = $fields["texto"] : null;
        isset($fields["viuda"]) ? $this->viuda = $fields["viuda"] : null;
        isset($fields["recibo"]) ? $this->recibo = $fields["recibo"] : null;
        isset($fields["albaran"]) ? $this->albaran = $fields["albaran"] : null;
        isset($fields["foto"]) ? $this->foto = $fields["foto"] : null;
        
        isset($fields["mdlo"]) ? $this->mdlo = $fields["mdlo"] : null;
        isset($fields["UBx"]) ? $this->ubx = $fields["UBx"] : null;
        isset($fields["UBy"]) ? $this->uby = $fields["UBy"] : null;
        isset($fields["Estanteria"]) ? $this->estanteria = $fields["Estanteria"] : null;
        isset($fields["COD_MOTOR"]) ? $this->cod_motor = $fields["COD_MOTOR"] : null;
    
        isset($fields["N_ENTREGA"]) ? $this->n_entrega = $fields["N_ENTREGA"] : null;
        isset($fields["ID_CONTENEDOR"]) ? $this->id_contenedor = $fields["ID_CONTENEDOR"] : null;
        isset($fields["foto2"]) ? $this->foto2 = $fields["foto2"] : null;
        isset($fields["foto3"]) ? $this->foto3 = $fields["foto3"] : null;
        isset($fields["foto4"]) ? $this->foto4 = $fields["foto4"] : null;
        isset($fields["foto5"]) ? $this->foto5 = $fields["foto5"] : null;
        isset($fields["INCLUX"]) ? $this->inclux = $fields["INCLUX"] : null;
   
        $this->cotxe = new Europ_Entradas($this->clvaut);       
        
    }

    

    public function addLog($sender, $errorCode) {
        return false;
    }

//<editor-fold desc="GETTERS & SETTERS">
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getClvaut() {
        return $this->clvaut;
    }

    public function setClvaut($clvaut) {
        $this->clvaut = $clvaut;
    }

    public function getClvpie() {
        return $this->clvpie;
    }

    public function setClvpie($clvpie) {
        $this->clvpie = $clvpie;
    }

    public function getRefer() {
        return $this->refer;
    }

    public function setRefer($refer) {
        $this->refer = $refer;
    }

    public function getNota() {
        return $this->nota;
    }

    public function setNota($nota) {
        $this->nota = $nota;
    }

    public function getCosto() {
        return $this->costo;
    }

    public function setCosto($costo) {
        $this->costo = $costo;
    }

    public function getOperario() {
        return $this->operario;
    }

    public function setOperario($operario) {
        $this->operario = $operario;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function getTexto() {
        return $this->texto;
    }

    public function setTexto($texto) {
        $this->texto = $texto;
    }

    public function getViuda() {
        return $this->viuda;
    }

    public function setViuda($viuda) {
        $this->viuda = $viuda;
    }

    public function getRecibo() {
        return $this->recibo;
    }

    public function setRecibo($recibo) {
        $this->recibo = $recibo;
    }

    public function getAlbaran() {
        return $this->albaran;
    }

    public function setAlbaran($albaran) {
        $this->albaran = $albaran;
    }

    public function getFoto() {
        return $this->foto;
    }

    public function setFoto($foto) {
        $this->foto = $foto;
    }

    public function getMdlo() {
        return $this->mdlo;
    }

    public function setMdlo($mdlo) {
        $this->mdlo = $mdlo;
    }

    public function getUbx() {
        return $this->ubx;
    }

    public function setUbx($ubx) {
        $this->ubx = $ubx;
    }

    public function getUby() {
        return $this->uby;
    }

    public function setUby($uby) {
        $this->uby = $uby;
    }

    public function getEstanteria() {
        return $this->estanteria;
    }

    public function setEstanteria($estanteria) {
        $this->estanteria = $estanteria;
    }

    public function getCod_motor() {
        return $this->cod_motor;
    }

    public function setCod_motor($cod_motor) {
        $this->cod_motor = $cod_motor;
    }

    public function getN_entrega() {
        return $this->n_entrega;
    }

    public function setN_entrega($n_entrega) {
        $this->n_entrega = $n_entrega;
    }

    public function getId_contenedor() {
        return $this->id_contenedor;
    }

    public function setId_contenedor($id_contenedor) {
        $this->id_contenedor = $id_contenedor;
    }

    public function getFoto2() {
        return $this->foto2;
    }

    public function setFoto2($foto2) {
        $this->foto2 = $foto2;
    }

    public function getFoto3() {
        return $this->foto3;
    }

    public function setFoto3($foto3) {
        $this->foto3 = $foto3;
    }

    public function getFoto4() {
        return $this->foto4;
    }

    public function setFoto4($foto4) {
        $this->foto4 = $foto4;
    }

    public function getFoto5() {
        return $this->foto5;
    }

    public function setFoto5($foto5) {
        $this->foto5 = $foto5;
    }

    public function getInclux() {
        return $this->inclux;
    }

    public function setInclux($inclux) {
        $this->inclux = $inclux;
    }

    public function getCotxe() {
        return $this->cotxe;
    }

    public function setCotxe($cotxe) {
        $this->cotxe = $cotxe;
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
        
        return $ids_presta_array;
    }



}

?>
