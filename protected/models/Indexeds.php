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
    protected $google_url1;
    protected $updatedAt;
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
        $this->google_url1 = isset($fields["google_url1"]) ? $fields["google_url1"] : null;
        $this->google_index = isset($fields["google_index"]) ? $fields["google_index"] : 0;
        //$this->updatedAt = isset($fields["updatedAt"]) ? $fields["updatedAt"] : null;
        //$this->createdAt = isset($fields["createdAt"]) ? $fields["createdAt"] : null;
        
    }

    //</editor-fold>
    //<editor-fold desc="DB METHODS">
    /**
     * Exists this owner in the Data base?
     * @return boolean
     */
    public function insertIntoDataBase() {

       // if (!existUrlDB($this->url)) {
            echo "<br> url no existeix a la bbdd";

            $query = 'INSERT INTO `indexeds` SET ' .
                    ($this->url != null ? 'url = "' . $this->url . '", ' : '') .
                    ($this->google_index != null ? 'google_index = "' . $this->google_index . '", ' : '') .
                    ($this->google_url1 != null ? 'google_url1 = "' . $this->google_url1 . '", ' : '') .
                    'updatedAt = NOW(), createdAt = NOW()';

                    echo "<br>";
            print_r($query);

            $result = mysql_query($query);
            //$this->id = (int) mysql_insert_id();
            
            return $result;
     /*
        } else {
            echo "<br> url EXISTEIX a la bbdd"; 
            $query = 'UPDATE `indexeds` SET ' .
                    ($this->url != null ? 'url = "' . $this->url . '", ' : '') .
                    ($this->google_index != null ? 'google_index = "' . $this->google_index . '", ' : '') .
                    ($this->google_url1 != null ? 'google_url1 = "' . $this->google_url1 . '", ' : '') .
                    'updatedAt = NOW()
                    WHERE id = ' . $this->id;
                    echo "<br>";
            print_r($query);
            $result = mysql_query($query);
            echo "<br>";
            print_r($query);
            return $result;
        }
        */
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
        try {
            $result = mysql_query($query);
            if (mysql_num_rows($result) > 0) {
                return "1";
            } else {
                return "0";
            }
        } catch (Exception $e) {
            return "0";
        }
    }

    public function getUrlid($url) {
        $query = "SELECT * FROM `indexeds` WHERE url = '" . $url . "'";
        //echo "Claims.php : ".$query;
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_array($result);
            return $row["id"];
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

    function getGoogle_url1() {
        return $this->google_url1;
    }

    function setGoogle_url1($google_url1) {
        $this->google_url1 = $google_url1;
    }

}

?>
