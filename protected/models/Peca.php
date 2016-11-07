<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Peca {

    //<editor-fold desc="ATTRIBUTES">
    // Basic
    // [codigo] => 18525 [id_vh] => C0001935 [pza] => Airbag Conductor [ref] => SimpleXMLElement Object ( ) 
    // [pvp] => 60,00 [fp1] => SF [fp2] => SF [fp3] => SF [fp4] => SF [fp5] => SF

    protected $codigo;
    protected $id_vh;
    protected $pza;
    protected $ref;
    protected $pvp;
    protected $fp1;
    protected $fp2;
    protected $fp3;
    protected $fp4;
    protected $fp5;
    protected $enestoc;
    protected $url_es;
    protected $updatedtried;
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
        $query = "SELECT * FROM `a_peces` WHERE codigo = $id";
        //echo $query;
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $this->__constructByFields($row);

    }

    function __constructByFields($fields) {
        // Basic
        $this->codigo = $fields["codigo"];
        $this->id_vh = $fields["id_vh"];
        $this->pza = $fields["pza"];
        $this->ref = $fields["ref"];
        $this->pvp = $fields["pvp"];
        $this->fp1 = (isset($fields["fp1"]) ? $fields["fp1"] : "");
        $this->fp2 = (isset($fields["fp2"]) ? $fields["fp2"] : "");
        $this->fp3 = (isset($fields["fp3"]) ? $fields["fp3"] : "");
        $this->fp4 = (isset($fields["fp4"]) ? $fields["fp4"] : "");
        $this->fp5 = (isset($fields["fp5"]) ? $fields["fp5"] : "");
        $this->enestoc = (isset($fields["enestoc"]) ? $fields["enestoc"] : "0");
        $this->url_es = (isset($fields["url_es"]) ? $fields["url_es"] : "");
        $this->updatedtried = (isset($fields["updatedtried"]) ? $fields["updatedtried"] : "");
        
        
    }

    private function existInDB() {
        $query = "SELECT * FROM `a_peces` WHERE codigo = '" . $this->codigo . "'";
        //echo "<br>".$query;
        $result = mysql_query($query);
        return mysql_num_rows($result) > 0 ? true : false;
    }

    public function getCanonical() {
        $query = "SELECT * FROM `ps_simplecanonicalurls` WHERE id = '" . ($this->codigo+1000000) . "'";
        //echo "<br>".$query;
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0){
            $row = mysql_fetch_array($result);
            return $row["url"];
        }else{
            return null;
        }     
        
    }
    
    /**
     * Insert user into Data base.
     * @return boolean 
     */
    public function insertIntoDataBase() {
        if (!$this->existInDB()) {
            $query = "INSERT INTO `a_peces` SET " .
                    ($this->codigo != null ? "codigo = '" . $this->codigo . "', " : "") .
                    ($this->id_vh != null ? "id_vh = '" . $this->id_vh . "', " : "") .
                    ($this->pza != null ? "pza = '" . $this->pza . "', " : "") .
                    ($this->ref != null ? "ref = '" . $this->ref . "', " : "") .
                    ($this->pvp != null ? "pvp = '" . $this->pvp . "', " : "") .
                    ($this->fp1 != null ? "fp1 = '" . $this->fp1 . "', " : "") .
                    ($this->fp2 != null ? "fp2 = '" . $this->fp2 . "', " : "") .
                    ($this->fp3 != null ? "fp3 = '" . $this->fp3 . "', " : "") .
                    ($this->fp4 != null ? "fp4 = '" . $this->fp4 . "', " : "") .
                    ($this->fp5 != null ? "fp5 = '" . $this->fp5 . "', " : "") .
                    ($this->enestoc != null ? "enestoc = '" . $this->enestoc . "', " : "") .
                    ($this->url_es != null ? "url_es = '" . $this->url_es . "', " : "") .
                    ($this->updatedtried != null ? "updatedtried = '" . $this->updatedtried . "', " : "") .
                    "updatedAt = NOW()" ;
            //echo "<br>".$query;
            $result = mysql_query($query);
            $this->id = (int) mysql_insert_id();
            return $result;
        } else {
            //$this->codigo = (int) $this->takeIdentifier();

            $query = "UPDATE `a_peces` SET " .
                    ($this->id_vh != null ? "id_vh = '" . $this->id_vh . "', " : "") .
                    ($this->pza != null ? "pza = '" . $this->pza . "', " : "") .
                    ($this->ref != null ? "ref = '" . $this->ref . "', " : "") .
                    ($this->pvp != null ? "pvp = '" . $this->pvp . "', " : "") .
                    ($this->fp1 != null ? "fp1 = '" . $this->fp1 . "', " : "") .
                    ($this->fp2 != null ? "fp2 = '" . $this->fp2 . "', " : "") .
                    ($this->fp3 != null ? "fp3 = '" . $this->fp3 . "', " : "") .
                    ($this->fp4 != null ? "fp4 = '" . $this->fp4 . "', " : "") .
                    ($this->fp5 != null ? "fp5 = '" . $this->fp5 . "', " : "") .
                    ($this->enestoc != null ? "enestoc = '" . $this->enestoc . "', " : "") .
                    ($this->url_es != null ? "url_es = '" . $this->url_es . "', " : "") .
                    ($this->updatedtried != null ? "updatedtried = '" . $this->updatedtried . "', " : "") .
                    "updatedAt = NOW() WHERE codigo = " . $this->codigo;
            //echo "<br>".$query;
            return mysql_query($query);
        }
    }
    
    /**
     * Take User identifier by external and partner id.
     * @return integer 
     */
    public function takeIdentifier() {
        $query = "SELECT * FROM `a_peces` WHERE codigo = '" . $this->codigo . "'";
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

    //</editor-fold>
    //<editor-fold desc="GETTERS AND SETTERS">
    public function getcodigo() {
        return $this->codigo;
    }

    public function setcodigo($codigo) {
        $this->codigo = $codigo;
    }

    public function getid_ps() {
        $id_ps = $this->codigo + 1000000;
        return $id_ps;
    }
    
    
    public function getid_vh() {
        return $this->id_vh;
    }

    public function setid_vh($id_vh) {
        $this->id_vh = $id_vh;
    }

    public function getpza() {
        return $this->pza;
    }

    public function setpza($pza) {
        $this->pza = $pza;
    }

    public function getref() {
        return $this->ref;
    }

    public function setref($ref) {
        $this->ref = $ref;
    }

    public function getpvp() {
        return $this->pvp;
    }

    public function setpvp($pvp) {
        $this->pvp = $pvp;
    }

    public function getfp1() {
        return $this->fp1;
    }

    public function setfp1($fp1) {
        $this->fp1 = $fp1;
    }

    public function getfp2() {
        return $this->fp2;
    }

    public function setfp2($fp2) {
        $this->fp2 = $fp2;
    }

    public function getfp3() {
        return $this->fp3;
    }

    public function setfp3($fp3) {
        $this->fp3 = $fp3;
    }

    public function getfp4() {
        return $this->fp4;
    }

    public function setfp4($fp4) {
        $this->fp4 = $fp4;
    }

    public function getfp5() {
        return $this->fp5;
    }

    public function setfp5($fp5) {
        $this->fp5 = $fp5;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }
    public function getUpdatedtried() {
        return $this->updatedtried;
    }

    public function setUpdatedtried($updatedtried) {
        $this->updatedtried = $updatedtried;
    }
    function getEnestoc() {
        return $this->enestoc;
    }

    function setEnestoc($enestoc) {
        $this->enestoc = $enestoc;
    }
    function getUrl_es() {
        return $this->url_es;
    }

    function setUrl_es($url_es) {
        $this->url_es = $url_es;
    }

                
    
    //</editor-fold>
}

?>
