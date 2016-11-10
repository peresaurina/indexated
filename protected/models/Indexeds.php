<?php

/**
 * Description of Owner
 *
 * @peresaurina
 */
class Indexeds {

    //<editor-fold desc="ATTRIBUTES">
    protected $id;
    protected $url;
    protected $google_index;
    protected $createdAt;

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
            $query = "SELECT * FROM `indexeds` WHERE id = '" . $id . "'";
            $result = mysql_query($query);
            $row = mysql_fetch_assoc($result);
            $this->__constructByFields($row);
        }
    }

    function __constructByFields($fields) {
        $this->id = isset($fields["id"]) ? (int) $fields["id"] : null;
        $this->url = isset($fields["url"]) ? $fields["url"] : null;
        $this->google_index = isset($fields["google_index"]) ? $fields["google_index"] : 0;
        $this->createdAt = isset($fields["createdAt"]) ? $fields["createdAt"] : null;
        //print_r($this);
    }

    //</editor-fold>
    //<editor-fold desc="DB METHODS">
    /**
     * Exists this owner in the Data base?
     * @return boolean
     */
    public function insertIntoDataBase() {
        $query = 'INSERT INTO `indexeds` SET ' .
                ($this->url != null ? 'url = "' . $this->url . '", ' : '') .
                ($this->google_index != null ? 'google_index = "' . $this->google_index . '", ' : '') .
                'createdAt = NOW()';
        $result = mysql_query($query);
        $this->id = (int) mysql_insert_id();
        print_r($query);
        return $result;
    }

    public function existInDB($id) {
        $query = "SELECT * FROM `indexeds` WHERE id = '" . $id . "'";
        //echo "Claims.php : ".$query;
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
            return "1";
        } else {
            return "0";
        }
    }

    public function existUrlDB($url) {
        $query = "SELECT * FROM `indexeds` WHERE url = '" . $url . "'";
        //echo "Claims.php : ".$query;
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
            return "1";
        } else {
            return "0";
        }
    }

    //</editor-fold>
    //<editor-fold desc="GETTERS & SETTERS">
    function getId() {
        return $this->id;
    }

    function getUrl() {
        return $this->url;
    }

    function getCreatedAt() {
        return $this->createdAt;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setUrl($url) {
        $this->url = $url;
    }

    function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }
    function getGoogle_index() {
        return $this->google_index;
    }

    function setGoogle_index($google_index) {
        $this->google_index = $google_index;
    }


}

?>
