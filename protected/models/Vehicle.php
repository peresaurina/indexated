<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Vehicle {

    //<editor-fold desc="ATTRIBUTES">
    // Basic
    
    //[ID_VH] => C0000001 [TIP] => TURISMO [MAR] => Seat 
    //[MOD] => Seat Cordoba 1 F1 [1993-08/96] [VER] => 1.9 D 
    //[A] => 1995 [CM] => SimpleXMLElement Object ( ) 
    //[COL] => VERDE [M] => NO [MAT] => GI1498AY 
    //[CBS] => DIESEL [PTs] => 5 [KM] => 320000 [FRAG] => SI 
    //[FV1] => SF [FV2] => SF [FV3] => SF [FV4] => SF [FV5] => SF 
    //[FV6] => SF [FV7] => SF [FV8] => SF [FV9] => SF [FV10] => SF
    
    
    protected $id_vh;
    protected $id_ps;
    protected $tip;//tipus
    protected $mar;//marca
    protected $model;//model = categoria en prestashop
    //protected $modbase; //model base = subcategoria
    
    protected $ver;//versió
    protected $a; // any
    protected $cm;
    protected $col; // color
    protected $m;
    protected $mat; //matricula
    protected $cbs; //combustible
    protected $pts; //portes
    protected $km; //km
    protected $frag; // fragmentador = no estoc
    protected $fv1;
    protected $fv2;
    protected $fv3;
    protected $fv4;
    protected $fv5;
    protected $fv6;
    protected $fv7;
    protected $fv8;
    protected $fv9;
    protected $fv10;
    protected $url_es;
    protected $updatedAt;
    protected $createdAt;
    
    
    //</editor-fold>
    //<editor-fold desc="CONSTRUCTORS">
    function __construct($id, $fields = null) {
        if ($id) {
            $this->__constructById($id);
        }
        if (is_array($fields) && !is_null($fields)) {
            $this->__constructByFields($fields);
        }
    }

    function __constructById($id) {
        $query = "SELECT * FROM `a_vehicles` WHERE id_vh = '".$id."'";
        //echo $query;
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $this->__constructByFields($row);

    }

    function __constructByFields($fields) {
        // Basic
        $this->id_vh = $fields["id_vh"];
        $this->tip = $fields["tip"];
        $this->mar = $fields["mar"];
        $this->model = $fields["model"];        
        $this->ver = $fields["ver"];
        $this->a = $fields["a"];
        $this->cm = $fields["cm"];
        $this->col = $fields["col"];
        $this->m = $fields["m"];
        $this->mat = $fields["mat"];
        $this->cbs = $fields["cbs"];
        $this->pts = $fields["pts"];
        $this->km = $fields["km"];
        $this->frag = $fields["frag"];
        $this->fv1 = $fields["fv1"];
        $this->fv2 = $fields["fv2"];
        $this->fv3 = $fields["fv3"];
        $this->fv4 = $fields["fv4"];
        $this->fv5 = $fields["fv5"];
        $this->fv6 = $fields["fv6"];
        $this->fv7 = $fields["fv7"];
        $this->fv8 = $fields["fv8"];
        $this->fv9 = $fields["fv9"];
        $this->fv10 = $fields["fv10"];
        $this->url_es = isset($fields["url_es"]) ? $fields["url_es"] : null;
        //print_r($this);
    }

    private function existInDB() {
        $query = "SELECT * FROM `a_vehicles` WHERE id_vh = '" . $this->id_vh . "'";
        //echo "<br>".$query;
        $result = mysql_query($query);
        return mysql_num_rows($result) > 0 ? true : false;
    }

    /**
     * Insert user into Data base.
     * @return boolean 
     */
    public function insertIntoDataBase() {
        
        if (!$this->existInDB()) {        
            $query = "INSERT INTO `a_vehicles` SET " .
                    ($this->id_vh != null ? "id_vh = '" . $this->id_vh . "', " : "") .
                    ($this->tip != null ? "tip = '" . $this->tip . "', " : "") .
                    ($this->mar != null ? "mar = '" . $this->mar . "', " : "") .
                    ($this->model != null ? "model = '" . $this->model . "', " : "") .
                    ($this->ver != null ? "ver = '" . $this->ver . "', " : "") .
                    ($this->a != null ? "a = '" . $this->a . "', " : "") .
                    ($this->cm != null ? "cm = '" . $this->cm . "', " : "") .
                    ($this->col != null ? "col = '" . $this->col . "', " : "") .
                    ($this->m != null ? "m = '" . $this->m . "', " : "") .
                    ($this->mat != null ? "mat = '" . $this->mat . "', " : "") .
                    ($this->cbs != null ? "cbs = '" . $this->cbs . "', " : "") .
                    ($this->pts != null ? "pts = '" . $this->pts . "', " : "") .
                    ($this->km != null ? "km = '" . $this->km . "', " : "") .
                    ($this->frag != null ? "frag = '" . $this->frag . "', " : "") .
                    ($this->fv1 != null ? "fv1 = '" . $this->fv1 . "', " : "") .
                    ($this->fv2 != null ? "fv2 = '" . $this->fv2 . "', " : "") .
                    ($this->fv3 != null ? "fv3 = '" . $this->fv3 . "', " : "") .
                    ($this->fv4 != null ? "fv4 = '" . $this->fv4 . "', " : "") .
                    ($this->fv5 != null ? "fv5 = '" . $this->fv5 . "', " : "") .
                    ($this->fv6 != null ? "fv6 = '" . $this->fv6 . "', " : "") .
                    ($this->fv7 != null ? "fv7 = '" . $this->fv7 . "', " : "") .
                    ($this->fv8 != null ? "fv8 = '" . $this->fv8 . "', " : "") .
                    ($this->fv9 != null ? "fv9 = '" . $this->fv9 . "', " : "") .
                    ($this->fv10 != null ? "fv10 = '" . $this->fv10 . "', " : "") .
                    ($this->url_es != null ? "url_es = '" . $this->url_es . "', " : "") .
                    "updatedAt = NOW(), createdAt = NOW() " ;
            //echo "<br>".$query;
            $result = mysql_query($query);
            $this->id = (int) mysql_insert_id();
            return $result;
        } else {
            //$this->codigo = (int) $this->takeIdentifier();

            $query = "UPDATE `a_vehicles` SET " .
                    ($this->tip != null ? "tip = '" . $this->tip . "', " : "") .
                    ($this->mar != null ? "mar = '" . $this->mar . "', " : "") .
                    ($this->model != null ? "model = '" . $this->model . "', " : "") .
                    ($this->ver != null ? "ver = '" . $this->ver . "', " : "") .
                    ($this->a != null ? "a = '" . $this->a . "', " : "") .
                    ($this->cm != null ? "cm = '" . $this->cm . "', " : "") .
                    ($this->col != null ? "col = '" . $this->col . "', " : "") .
                  //  ($this->m != null ? "m = '" . $this->m . "', " : "") .
                  // ($this->mat != null ? "mat = '" . $this->mat . "', " : "") .
                    ($this->cbs != null ? "cbs = '" . $this->cbs . "', " : "") .
                    ($this->pts != null ? "pts = '" . $this->pts . "', " : "") .
                    ($this->km != null ? "km = '" . $this->km . "', " : "") .
                    ($this->frag != null ? "frag = '" . $this->frag . "', " : "") .
                    ($this->fv1 != null ? "fv1 = '" . $this->fv1 . "', " : "") .
                    ($this->fv2 != null ? "fv2 = '" . $this->fv2 . "', " : "") .
                    ($this->fv3 != null ? "fv3 = '" . $this->fv3 . "', " : "") .
                    ($this->fv4 != null ? "fv4 = '" . $this->fv4 . "', " : "") .
                    ($this->fv5 != null ? "fv5 = '" . $this->fv5 . "', " : "") .
                    ($this->fv6 != null ? "fv6 = '" . $this->fv6 . "', " : "") .
                    ($this->fv7 != null ? "fv7 = '" . $this->fv7 . "', " : "") .
                    ($this->fv8 != null ? "fv8 = '" . $this->fv8 . "', " : "") .
                    ($this->fv9 != null ? "fv9 = '" . $this->fv9 . "', " : "") .
                    ($this->fv10 != null ? "fv10 = '" . $this->fv10 . "', " : "") .
                    ($this->url_es != null ? "url_es = '" . $this->url_es . "', " : "") .
                    "updatedAt = NOW() WHERE id_vh = '" . $this->id_vh."'";
            //echo "<br>".$query;
            return mysql_query($query);
        }
    }
    
    /**
     * Take User identifier by external and partner id.
     * @return integer 
     */
    public function takeIdentifier() {
        $query = "SELECT * FROM `a_vehicles` WHERE id_vh = '" . $this->id_vh . "'";
        $row = mysql_fetch_assoc(mysql_query($query));
        return (int) $row["id"];
    }

    /**
     * Is this User an Administrator?
     * @return type boolean
     */
    public function isThisUserAnAdministrator() {
        $isAdmin = $this->id == 1 || $this->id == 2 || $this->id == 3 || $this->id == 5 || $this->id == 4 || $this->id == 7;
        return $isAdmin;
    }
    
    public function getId_ps() {
        $id_ps = preg_replace('#C#', "", $this->id_vh);
        $id_ps = 8000000 + $id_ps;
        return $id_ps;
    }

    public function getId_vh() {
        return $this->id_vh;
    }

    public function setId_vh($id_vh) {
        $this->id_vh = $id_vh;
    }

    public function getTip() {
        return $this->tip;
    }

    public function setTip($tip) {
        $this->tip = $tip;
    }

    public function getMar() {
        return $this->mar;
    }

    public function setMar($mar) {
        $this->mar = $mar;
    }

    public function getModel() {
        return $this->model;
    }

    public function setModel($model) {
        $this->model = $model;
    }

    public function getVer() {
        return $this->ver;
    }

    public function setVer($ver) {
        $this->ver = $ver;
    }

    public function getA() {
        return $this->a;
    }

    public function setA($a) {
        $this->a = $a;
    }

    public function getCm() {
        return $this->cm;
    }

    public function setCm($cm) {
        $this->cm = $cm;
    }

    public function getCol() {
        return $this->col;
    }

    public function setCol($col) {
        $this->col = $col;
    }

    public function getM() {
        return $this->m;
    }

    public function setM($m) {
        $this->m = $m;
    }

    public function getMat() {
        return $this->mat;
    }

    public function setMat($mat) {        
        $this->mat = $mat;
    }

    public function getCbs() {
        return $this->cbs;
    }

    public function setCbs($cbs) {
        $this->cbs = $cbs;
    }

    public function getPts() {
        return $this->pts;
    }

    public function setPts($pts) {
        $this->pts = $pts;
    }

    public function getKm() {
        return $this->km;
    }

    public function setKm($km) {
        $this->km = $km;
    }

    public function getFrag() {
        return $this->frag;
    }

    public function setFrag($frag) {
        $this->frag = $frag;
    }

    public function getFv1() {
        return $this->fv1;
    }

    public function setFv1($fv1) {
        $this->fv1 = $fv1;
    }

    public function getFv2() {
        return $this->fv2;
    }

    public function setFv2($fv2) {
        $this->fv2 = $fv2;
    }

    public function getFv3() {
        return $this->fv3;
    }

    public function setFv3($fv3) {
        $this->fv3 = $fv3;
    }

    public function getFv4() {
        return $this->fv4;
    }

    public function setFv4($fv4) {
        $this->fv4 = $fv4;
    }

    public function getFv5() {
        return $this->fv5;
    }

    public function setFv5($fv5) {
        $this->fv5 = $fv5;
    }

    public function getFv6() {
        return $this->fv6;
    }

    public function setFv6($fv6) {
        $this->fv6 = $fv6;
    }

    public function getFv7() {
        return $this->fv7;
    }

    public function setFv7($fv7) {
        $this->fv7 = $fv7;
    }

    public function getFv8() {
        return $this->fv8;
    }

    public function setFv8($fv8) {
        $this->fv8 = $fv8;
    }

    public function getFv9() {
        return $this->fv9;
    }

    public function setFv9($fv9) {
        $this->fv9 = $fv9;
    }

    public function getFv10() {
        return $this->fv10;
    }

    public function setFv10($fv10) {
        $this->fv10 = $fv10;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }
    function getUrl_es() {
        return $this->url_es;
    }

    function getCreatedAt() {
        return $this->createdAt;
    }

    function setUrl_es($url_es) {
        $this->url_es = $url_es;
    }

    function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }




}

?>
