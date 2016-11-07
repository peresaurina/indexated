<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Models {

    //<editor-fold desc="ATTRIBUTES">
    // Basic
    
    //[ID_VH] => C0000001 [TIP] => TURISMO [MAR] => Seat 
    //[MOD] => Seat Cordoba 1 F1 [1993-08/96] [VER] => 1.9 D 
    //[A] => 1995 [CM] => SimpleXMLElement Object ( ) 
    //[COL] => VERDE [M] => NO [MAT] => GI1498AY 
    //[CBS] => DIESEL [PTs] => 5 [KM] => 320000 [FRAG] => SI 
    //[FV1] => SF [FV2] => SF [FV3] => SF [FV4] => SF [FV5] => SF 
    //[FV6] => SF [FV7] => SF [FV8] => SF [FV9] => SF [FV10] => SF
    
    
    protected $mar;//marca
    protected $model;//model = categoria en prestashop CAMP CLAU
    protected $modbase; //model base = subcategoria
    protected $categoria_ps;
    protected $updatedAt;
    
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
        $query = "SELECT * FROM `a_mods` WHERE model = '".$id."'";
//        echo $query;
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $this->__constructByFields($row);

    }

    function __constructByFields($fields) {
        // Basic
        $this->mar = $fields["mar"];
        $this->modbase = $fields["modbase"];
        $this->model = $fields["model"];
        $this->categoria_ps = $fields["categoria_ps"];
        //print_r($this);
    }

    private function existInDB() {
        $query = "SELECT * FROM `a_mods` WHERE model = '" . $this->model . "'";
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
            $query = "INSERT INTO `a_mods` SET " .
                    ($this->id_vh != null ? "id_vh = '" . $this->id_vh . "', " : "") .
                    ($this->tip != null ? "tip = '" . $this->tip . "', " : "") .
                    ($this->mar != null ? "mar = '" . $this->mar . "', " : "") .
                    ($this->modbase != null ? "modbase = '" . $this->modbase . "', " : "") .
                    ($this->model != null ? "model = '" . $this->model . "', " : "") .
                    ($this->categoria_ps != null ? "categoria_ps = '" . $this->categoria_ps . "', " : "") .
                    "updatedAt = NOW()" ;
            //echo "<br>".$query;
            $result = mysql_query($query);
            $this->id = (int) mysql_insert_id();
            return $result;
        } else {
            //$this->codigo = (int) $this->takeIdentifier();

            $query = "UPDATE `a_mods` SET " .
                    ($this->tip != null ? "tip = '" . $this->tip . "', " : "") .
                    ($this->mar != null ? "mar = '" . $this->mar . "', " : "") .
                    ($this->modbase != null ? "modbase = '" . $this->modbase . "', " : "") .
                    ($this->model != null ? "model = '" . $this->model . "', " : "") .
                    ($this->categoria_ps != null ? "categoria_ps = '" . $this->categoria_ps . "', " : "") .
                    "updatedAt = NOW() WHERE model = '" . $this->model."'";
            //echo "<br>".$query;
            return mysql_query($query);
        }
    }
    
    /**
     * Take User identifier by external and partner id.
     * @return integer 
     */
    public function takeIdentifier() {
        $query = "SELECT * FROM `a_mods` WHERE id_vh = '" . $this->model . "'";
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

   

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }
    public function getModbase() {
        return $this->modbase;
    }

    public function setModbase($modbase) {
        $this->modbase = $modbase;
    }
    public function getCategoria_ps() {
        return $this->categoria_ps;
    }

    public function setCategoria_ps($categoria_ps) {
        $this->categoria_ps = $categoria_ps;
    }




}

?>
