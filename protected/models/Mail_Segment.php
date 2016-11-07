<?php

/**
 * Description of Owner
 *
 * @peresaurina
 */
class Mail_Segment {

    //<editor-fold desc="ATTRIBUTES">
    protected $id;
    protected $name;    
    protected $email;
    protected $marca_model;
    protected $comment;
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
            $query = "SELECT * FROM `a_mail_segment` WHERE id = '" . $id . "'";
            $result = mysql_query($query);
            $row = mysql_fetch_assoc($result);
            $this->__constructByFields($row);
        }
    }

    function __constructByFields($fields) {
        $this->id = isset($fields["id"]) ? $fields["id"] : null;
        $this->name = isset($fields["name"]) ? $fields["name"] : null;
        $this->email = isset($fields["email"]) ? $fields["email"] : null;
        $this->marca_model = isset($fields["marca_model"]) ? $fields["marca_model"] : null;
        $this->comment = isset($fields["comment"]) ? $fields["comment"] : null;
        $this->createdAt = isset($fields["createdAt"]) ? $fields["createdAt"] : null;        
    }

    //</editor-fold>
    //<editor-fold desc="DB METHODS">
    /**
     * Exists this owner in the Data base?
     * @return boolean
     */
    
    public function insertIntoDataBase() {
        //insertem inici de feina
        $query = 'INSERT INTO `a_mail_segment` SET ' .
                ($this->name != null ? 'name = "' . $this->name . '", ' : '') .
                ($this->email != null ? 'email = "' . $this->email . '", ' : '') .
                ($this->comment != null ? 'comment = "' .  $this->comment . '", ' : '') .
                ($this->marca_model != null ? 'marca_model = "' .  $this->marca_model . '", ' : '') .
                'createdAt = NOW()';
        $result = mysql_query($query);
        //echo "Mail_segment.php: ".$query;
        $this->id = (int) mysql_insert_id();
        return $result;
    }
    
    public function existInDB($id) {
        $query = "SELECT * FROM `a_mail_segment` WHERE orderid = '" . $id . "'";
        //echo "Claims.php : ".$query;
        $result = mysql_query($query);
        if (mysql_num_rows($result)>0){
            return "1";
        }else{
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
        return $this->comment;
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

    
    function setComment($comment) {
        $this->comment = $comment;
    }

    function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }
    function getMarca_model() {
        return $this->marca_model;
    }

    function setMarca_model($marca_model) {
        $this->marca_model = $marca_model;
    }


}

?>
