<?php

/**
 * Description of Owner
 *
 * @peresaurina
 */
class Mail_Europ {

    //<editor-fold desc="ATTRIBUTES">
    protected $id;
    protected $email;
    protected $comptador;
    protected $updatedAt;
    protected $createdAt;

    // Private
    //private $createdAt;
    //private $updatedAt;
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
        if ($id != null) {
            $query = "SELECT * FROM `a_mail_europ` WHERE id = '" . $id . "'";
            $result = mysql_query($query);
            $row = mysql_fetch_assoc($result);
            $this->__constructByFields($row);
        }
    }

    function __constructByFields($fields) {
        $this->id = isset($fields["id"]) ? $fields["id"] : null;
        $this->email = isset($fields["email"]) ? $fields["email"] : null;
        $this->comptador = isset($fields["comptador"]) ? $fields["comptador"] : null;
        $this->updatedAt = isset($fields["updatedAt"]) ? $fields["updatedAt"] : null;
        $this->createdAt = isset($fields["createdAt"]) ? $fields["createdAt"] : null;
    }

    //</editor-fold>
    //<editor-fold desc="DB METHODS">
    /**
     * Exists this owner in the Data base?
     * @return boolean
     */
    public function insertIntoDataBase() {

        if (!$this->existInDB()) {
            //insertem inici de feina
            $query = 'INSERT INTO `a_mail_europ` SET ' .
                    ($this->email != null ? 'email = "' . $this->email . '", ' : '') .
                    ' comptador = "0",' .
                    ' updatedAt = NOW(),' .
                    ' createdAt = NOW()';
        } else {
            $query = 'UPDATE `a_mail_europ` SET ' .
                    ' comptador = comptador + 1' .
                    ', updatedAt = NOW()' .
                    ' WHERE email = "' . $this->email . '"';
        }
        $result = mysql_query($query);
        
        echo "<br>Mail_Europ.php. Mail afegit:  " . $this->email;
        $this->id = (int) mysql_insert_id();
        return $result;
    }

    public function existInDB() {
        $query = "SELECT * FROM `a_mail_europ` WHERE email = '" . $this->email . "'";
        echo "<br>Mail_Europ.php : ".$query;
        $result = mysql_query($query);
        if ($result){
            if (mysql_num_rows($result) > 0) {
                return "1";
            } else {
                return "0";
            }
        } else {
            return "0";
        }
    }

    //</editor-fold>
    //<editor-fold desc="GETTERS & SETTERS">
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getEmail() {
        return $this->email;
    }

    function getComment() {
        return $this->updatedAt;
    }

    function getCreatedAt() {
        return $this->createdAt;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setComment($updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    function getMarca_model() {
        return $this->comptador;
    }

    function setMarca_model($comptador) {
        $this->comptador = $comptador;
    }

}

?>
