<?php

/**
 * Description of Owner
 *
 * @peresaurina
 */
class Peticio {

    //<editor-fold desc="ATTRIBUTES">
    protected $id;
    protected $name;
    protected $phone;
    protected $email;
    protected $product_id;
    protected $product_name;
    protected $comment;
    protected $createdAt;

    // Private
    //private $createdAt;
    //private $updatedAt;
    //</editor-fold>
    //<editor-fold desc="CONSTRUCTORS">
    function __construct($id, $fields) {
        if ($id != null) {
            $this->__constructById($id);
        }
        if (is_array($fields) && !is_null($fields)) {
            $this->__constructByFields($fields);
        }
    }

    function __constructById($id) {
        if ($id != null) {
            $query = "SELECT * FROM `a_peticio` WHERE id = '" . $id . "'";
            $result = mysql_query($query);
            $row = mysql_fetch_assoc($result);
            $this->__constructByFields($row);
        }
    }

    function __constructByFields($fields) {
        $this->id = isset($fields["id"]) ? (int) $fields["id"] : null;
        $this->name = isset($fields["name"]) ?  $fields["name"] : null;
        $this->phone = isset($fields["phone"]) ?  $fields["phone"] : null;
        $this->email = isset($fields["email"]) ?  $fields["email"] : null;
        $this->product_id = isset($fields["product_id"]) ?  $fields["product_id"] : null;
        $this->product_name = isset($fields["product_name"]) ?  $fields["product_name"] : null;
        $this->comment = isset($fields["comment"]) ?  $fields["comment"] : null;
        $this->createdAt = isset($fields["createdAt"]) ?  $fields["createdAt"] : null; 
        print_r($this);
    }

    //</editor-fold>
    //<editor-fold desc="DB METHODS">
    /**
     * Exists this owner in the Data base?
     * @return boolean
     */
    
    public function insertIntoDataBase() {
        //insertem inici de feina
        $query = 'INSERT INTO `a_peticio` SET ' .
                ($name != null ? 'name = "' . $this->name . '", ' : '') .
                ($phone != null ? 'phone = "' . $this->phone . '", ' : '') .
                ($email != null ? 'email = "' . $this->email . '", ' : '') .
                ($product_id != null ? 'product_id = "' .  $this->product_id . '", ' : '') .
                ($product_name != null ? 'product_name = "' . $this->product_name . '", ' : '') .
                'createdAt = NOW()';
        $result = mysql_query($query);
        echo "Peticio.php: ".$query;
        $this->id = (int) mysql_insert_id();
        return $result;
    }
    
    public function existInDB($id) {
        $query = "SELECT * FROM `a_peticio` WHERE orderid = '" . $id . "'";
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

    function getPhone() {
        return $this->phone;
    }

    function getEmail() {
        return $this->email;
    }

    function getProduct_id() {
        return $this->product_id;
    }

    function getProduct_name() {
        return $this->product_name;
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

    function setPhone($phone) {
        $this->phone = $phone;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setProduct_id($product_id) {
        $this->product_id = $product_id;
    }

    function setProduct_name($product_name) {
        $this->product_name = $product_name;
    }

    function setComment($comment) {
        $this->comment = $comment;
    }

    function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

}

?>
