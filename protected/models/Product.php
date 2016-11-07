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
class Product {

    //<editor-fold desc="ATTRIBUTES">
    protected $id = null;
    protected $active = null;
    protected $reference = null;
    protected $name = null;
    protected $parentcategory = null;
    protected $rootcategory = null;
    protected $description = null;
    protected $description_short=null;
    protected $metatitle = null;
    protected $metakeywords = null;
    protected $urlrewritten = null;
    protected $imageurl = null;
    protected $idboutique = null;
    protected $publishAt = null;
    protected $destroyAt = null;

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
        $query = "SELECT * FROM `ps_product` WHERE id_product = $id ";
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
        $this->id = $fields["id_product"];
        isset($fields["active"]) ? $this->active = $fields["active"] : null;
        isset($fields["reference"]) ? $this->reference = $fields["reference"] : null;
        /*
        isset($fields["parentcategory"]) ? $this->parentcategory = $fields["parentcategory"] : null;
        isset($fields["rootcategory"]) ? $this->rootcategory = $fields["rootcategory"] : null;
        isset($fields["description"]) ? $this->description = $fields["description"] : null;
        isset($fields["metatitle"]) ? $this->metatitle = $fields["metatitle"] : null;
        isset($fields["metakeywords"]) ? $this->metakeywords = $fields["metakeywords"] : null;
        isset($fields["urlrewritten"]) ? $this->urlrewritten = $fields["urlrewritten"] : null;
        isset($fields["imageurl"]) ? $this->imageurl = $fields["imageurl"] : null;
        isset($fields["idboutique"]) ? $this->idboutique = $fields["idboutique"] : null;
        */
        $query1 = "SELECT * FROM `ps_product_lang` WHERE id_product = ".$this->getId();
        $result1 = mysql_query($query1);
        while ($row1= mysql_fetch_array($result1)){
            $this->description = $row1["description"];
            $this->name = $row1["name"];
            $this->description_short = $row1["description_short"];
        }
        
        //$this->publishAt = $fields["publish_at"];
        //$this->destroyAt = $fields["destroy_at"];
    }

//</editor-fold>
//<editor-fold desc="DATABASE METHODS">
    /**
     * Exists this bid in Data base?
     * @return boolean
     */
    public static function existInPrestashop($code) {
        $query = "SELECT * FROM `ps_product` WHERE id_product = $code ";
        $result = mysql_query($query);
        return @mysql_num_rows($result) > 0 ? true : false;
    }
    
    public static function activeInPrestashop($code) {
        $query = "SELECT * FROM SELECT * FROM  `ps_product`  WHERE active = $code ";
        //cal fer aquesta consulta fent un joint i posant de condició active=''
        $result = @mysql_query($query);
        return @mysql_num_rows($result) > 0 ? true : false;
    }

    public static function products_paradespiece() {
        $query = "SELECT * FROM `ps_product` WHERE id_product > 8000000 order by id_product desc limit 0,50";
        $result = @mysql_query($query);
        return $result;
        /*
        // busquem totes les categories -3 que són despieces
        $id_category_sql = "SELECT * FROM  `ps_category_lang` where id_product";
        $result_id_categories = mysql_query($id_category_sql);
        while ($row = mysql_fetch_array($result_id_categories)) {
            $categories_array[] = $row["id_category"];
        }
        $list_categories = implode(",", $categories_array);
        //llistes productes
        $query = "SELECT * FROM `ps_product` WHERE id_category_default IN ($list_categories) order by id_product desc";
        $result = @mysql_query($query);
        return $result;
        // return @mysql_num_rows($result) > 0 ? true : false;
         * 
         */
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

    public function getActive() {
        return $this->active;
    }

    public function setActive($active) {
        $this->active = $active;
    }

    public function getReference() {
        return $this->reference;
    }

    public function setReference($reference) {
        $this->reference = $reference;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getParentcategory() {
        return $this->parentcategory;
    }

    public function setParentcategory($parentcategory) {
        $this->parentcategory = $parentcategory;
    }

    public function getRootcategory() {
        return $this->rootcategory;
    }

    public function setRootcategory($rootcategory) {
        $this->rootcategory = $rootcategory;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getMetatitle() {
        return $this->metatitle;
    }

    public function setMetatitle($metatitle) {
        $this->metatitle = $metatitle;
    }

    public function getMetakeywords() {
        return $this->metakeywords;
    }

    public function setMetakeywords($metakeywords) {
        $this->metakeywords = $metakeywords;
    }

    public function getUrlrewritten() {
        return $this->urlrewritten;
    }

    public function setUrlrewritten($urlrewritten) {
        $this->urlrewritten = $urlrewritten;
    }

    public function getImageurl() {
        return $this->imageurl;
    }

    public function setImageurl($imageurl) {
        $this->imageurl = $imageurl;
    }

    public function getIdboutique() {
        return $this->idboutique;
    }

    public function setIdboutique($idboutique) {
        $this->idboutique = $idboutique;
    }

    public function getPublishAt() {
        return $this->publishAt;
    }

    public function setPublishAt($publishAt) {
        $this->publishAt = $publishAt;
    }

    public function getDestroyAt() {
        return $this->destroyAt;
    }

    public function setDestroyAt($destroyAt) {
        $this->destroyAt = $destroyAt;
    }
    public function getDescription_short() {
        return $this->description_short;
    }

    public function setDescription_short($description_short) {
        $this->description_short = $description_short;
    }



}

?>
