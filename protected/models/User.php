<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class User {

    //<editor-fold desc="ATTRIBUTES">
    // Basic
    protected $id;
    protected $name;
    protected $lastName;
    protected $dateIni;
    protected $dateEnd;
    protected $idrent;
    protected $email;
    protected $telf;

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
        $query = "SELECT * FROM `tbl_user` WHERE id = $id";
//        echo $query;
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $this->__constructByFields($row);

    }

    function __constructByFields($fields) {
        // Basic
        $this->id = $fields["id"];
        $this->name = $fields["name"];
        $this->lastName = $fields["lastname"];
        $this->dateIni = $fields["dateini"];
        $this->dateEnd = $fields["dateend"];
        $this->idrent = $fields["idrent"];
        $this->email = $fields["email"];
    }

    //</editor-fold>
    //<editor-fold desc="DB METHODS">
    /**
     * Exists this user in Data base?
     * @return boolean
     */
    private function existInDB() {
        $query = "SELECT * FROM `tbl_user` WHERE id = '" . $this->id . "'";
//        echo "<br>".$query;
        $result = mysql_query($query);
        return mysql_num_rows($result) > 0 ? true : false;
    }

    /**
     * Insert user into Data base.
     * @return boolean 
     */
    public function insertIntoDataBase() {
        if (!$this->existInDB()) {
            $query = "INSERT INTO `tbl_user` SET " .
                    ($this->id != null ? "id = '" . $this->id . "', " : "") .
                    ($this->name != null ? "name = '" . $this->name . "', " : "") .
                    ($this->lastName != null ? "lastname = '" . $this->lastName . "', " : "") .
                    ($this->dateIni != null ? "dateini = '" . $this->dateIni . "', " : "") .
                    ($this->dateEnd != null ? "dateend = '" . $this->dateEnd . "', " : "") .
                    ($this->idrent != null ? "idrent = '" . $this->idrent . "', " : "") .
                    ($this->email != null ? "email = '" . $this->email . "', " : "") .
                    "created_at = NOW(), updated_at = NOW()";
//            echo "<br>".$query;
            $result = mysql_query($query);
            $this->id = (int) mysql_insert_id();
            return $result;
        } else {
            $this->id = (int) $this->takeIdentifier();

            $query = "UPDATE `tbl_user` SET " .
                    ($this->id != null ? "id = '" . $this->id . "', " : "") .
                    ($this->name != null ? "name = '" . $this->name . "', " : "") .
                    ($this->lastName != null ? "lastname = '" . $this->lastName . "', " : "") .
                    ($this->dateIni != null ? "dateini = '" . $this->dateIni . "', " : "") .
                    ($this->dateEnd != null ? "dateend = '" . $this->dateEnd . "', " : "") .
                    ($this->idrent != null ? "idrent = '" . $this->idrent . "', " : "") .
                    ($this->email != null ? "email = '" . $this->email . "', " : "") .
                    "updated_at = NOW() WHERE id = " . $this->id;
            return mysql_query($query);
        }
    }
    
    /**
     * Take User identifier by external and partner id.
     * @return integer 
     */
    public function takeIdentifier() {
        $query = "SELECT * FROM `tbl_user` WHERE id = '" . $this->id . "'";
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
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function getDateIni() {
        return $this->dateIni;
    }

    public function setDateIni($dateIni) {
        $this->dateIni = $dateIni;
    }

    public function getDateEnd() {
        return $this->dateEnd;
    }

    public function setDateEnd($dateEnd) {
        $this->dateEnd = $dateEnd;
    }

    public function getIdrent() {
        return $this->idrent;
    }

    public function setIdrent($idrent) {
        $this->idrent = $idrent;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }
    public function getTelf() {
        return $this->telf;
    }

    public function setTelf($telf) {
        $this->telf = $telf;
    }

    
    //</editor-fold>
}

?>
