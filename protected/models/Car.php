<?php

/**
 * Description of Cars
 *
 * @author pgarriga
 */
include('Owner.php');
include('CarDetails.php');

class Car
{

    //<editor-fold desc="ATTRIBUTES">
    //protected $owner;
    protected $provincia;
    protected $brand;
    protected $model;
    protected $matricula;
    protected $archived;
    protected $price;
    protected $registration;
    protected $year;
    protected $color;
    protected $mileage;
    protected $fuelType;
    protected $engine;
    protected $description;
    protected $desc_prestashop;
    protected $comment;
    protected $insertDate;
    protected $url;
    protected $images = array();
    protected $imagesUrl = array();
    protected $details = array();
    protected $sellpieces;
    protected $car_descrition_pieces;
    // Private
    private $id;
    private $createdAt;
    private $updatedAt;
    
    
    //més camps
    /**
     * title => a vegades no tenim ni marca ni model separat, sinó un titol
     * imatges (es un array)
     * URL => el link per on el podem trobar i consultar

     */
    //</editor-fold>
    //<editor-fold desc="CONSTRUCTORS">
    function __construct($id, $fields = null) {
        
        if ($id && is_null($fields)) {
            $this->__constructById($id);
        }elseif (is_array($fields) && !is_null($fields)) {
            $this->__constructByFields($fields);
            $this->insertIntoDataBase();
        }else{
            $this->__constructByFields($fields);
        }
         
    }

    function __constructById($id) {
        $query = "SELECT * FROM `tbl_car` WHERE id = $id";
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
                
        // Images
        $queryImages = "SELECT * FROM `tbl_car_image` WHERE car = $id";
        $resultImages = mysql_query($queryImages);
        while ($rowImage = mysql_fetch_assoc($resultImages)) {
            $row["images"][] = $rowImage;
        }
        // Car Details
        /**
        $queryDetails = "SELECT * FROM `tbl_car_details` WHERE car = $id";
        $resultDetails = mysql_query($queryDetails);
        while ($rowDetails = mysql_fetch_assoc($resultDetails)) {
            $row["details"][] = $rowDetails;
        }        
        **/
        
        $this->__constructByFields($row);
    }

    function __constructByFields($fields) {
        // Private
        $this->id = isset($fields["id"]) ? $fields["id"] : null;
        $this->createdAt = isset($fields["created_at"]) ? $fields["created_at"] : null;
        $this->updatedAt = isset($fields["updated_at"]) ? $fields["updated_at"] : null;
        // Basic  
        //$this->owner = new Owner((int) $fields["owner"]);
        $this->provincia = trim($fields["provincia"]);
        $this->brand = trim($fields["brand"]);
        $this->model = trim($fields["model"]);
        $this->matricula = trim($fields["matricula"]);
        $this->archived = ($fields["archived"]== ("on" || "1") ? 1 : 0);
        $this->sellpieces = ($fields["sellpieces"]== ("on" || "1") ? 1 : 0);
        $this->price = $fields["price"];
        $this->registration = $fields["registration"];
        $this->year = (int) $fields["year"];
        $this->color = $fields["color"];
        $this->mileage = (int) $fields["mileage"];
        $this->engine = $fields["engine"];
        $this->fuelType = trim($fields["fuel_type"]);
        $this->description = $fields["description"];
        $this->desc_prestashop = $fields["desc_prestashop"];
        $this->comment = $fields["comment"];
        $this->insertDate = $fields["insert_date"];
        $this->url = $fields["url"];
        
        // Details
        /**
        if (isset($fields["details"]) && is_array($fields["details"])){
            $details = new CarDetails($this->id,$fields["details"]);
        }
        **/
        
        // Images
        
        if (isset($fields["images"]) && is_array($fields["images"])) {
            foreach ($fields["images"] as $image) {
                if (is_object($var)) {
                    //$this->images[] = $image;
                } else {
                    $image["car"] = $this->id;
                    //$image = new CarImage(null, $image);
                    $image = new CarImage($this->id);
                    //$image->insertIntoDataBase();
                    //$this->images[] = $image;
                }
            }
        }else{
            $this->images = null;
        }
    }
    
    public static function scrapped_cars(){
        $query = "SELECT * FROM tbl_car";
        $result = mysql_query($query);
        return mysql_num_rows($result);
    }
    
    public static function getUrls() {
        $url="";
        if (isset($fields["images"])) {
            $query = "SELECT * FROM tbl_car_image WHERE car = '" . $id . "'";
            $result = mysql_query($query);
            while ($row = mysql_fetch_array($result)){
                $url = $url.",".$row["url"];
            }                      
        } else {
            $url ="no tenim imatges";
        }
        return $url;
    }
    
    public static function scrapped_cars_not_revised_list(){
        //seleccionar els bars que no tenen registre a tbl car details
        $query = "SELECT tbl_car.id, tbl_car.brand, tbl_car.model, tbl_car.matricula, tbl_car.insert_date,count(tbl_car_details.id) AS revisado 
            FROM tbl_car
            LEFT JOIN tbl_car_details ON(tbl_car.id=tbl_car_details.car)
            GROUP BY tbl_car.id
            ORDER BY count(tbl_car_details.id) ASC, tbl_car.insert_date DESC
            Limit 0,50";
        $result = mysql_query($query);
        return $result;
    }
    public static function scrapped_cars_list(){
        //seleccionar els bars que no tenen registre a tbl car details
        //$query = "SELECT tbl_car.id, tbl_car.brand, tbl_car.model, tbl_car.insert_date,'?' AS revisado 
        $query = "SELECT * ,  '?' AS revisado
                    FROM tbl_car
                    WHERE archived !=  '1'
                    ORDER BY tbl_car.id DESC 
                    LIMIT 0 , 30";
        $result = mysql_query($query);
        return $result;
    }
    
    //</editor-fold>
    //<editor-fold desc="DB METHODS">
    public static function exist($url){
        $query = "SELECT * FROM tbl_car WHERE url = '$url'";
        $result = mysql_query($query);    
        return mysql_num_rows($result) > 0;
        
        if(mysql_num_rows($result) > 0){
            $existeix="1";
        }else{
            $existeix="0";
        }
        return $existeix;
    }
    /**
     * Exists this owner in the Data base?
     * @return boolean
     */
    private function existInDB() {
        return !is_null($this->id);
    }
    
    public function CarexistInDB($id) {
        $query = "SELECT * FROM `tbl_car` where id='".$id."'";
        $result = mysql_query($query);
        if(mysql_num_rows($result) > 0){
            $existeix="1";
        }else{
            $existeix="0";
        }
        return $existeix;        
    }

    /**
     * Insert owner into Data base.
     * @return boolean 
     */
    public function insertIntoDataBase() {
        //print_r($this->id);
        //print_r("<br>");
        
        if (($this->CarexistInDB($this->id))=="0") {
            $query = "INSERT INTO `tbl_car` SET " .
                    ($this->id != null ? "id = '" . $this->id. "', " : "") .
                    ($this->provincia != null ? "provincia = '" . $this->provincia. "', " : "") .
                    ($this->brand != null ? "brand = '" . $this->brand . "', " : "") .
                    ($this->model != null ? "model = '" . $this->model . "', " : "") .  
                    ($this->matricula != null ? "matricula = '" . $this->matricula . "', " : "") .
                    ($this->archived != null ? "archived = '" . $this->archived . "', " : "") .                    
                    ($this->sellpieces != null ? "sellpieces = '" . $this->sellpieces . "', " : "") .                    
                    ($this->price != null ? "price = '" . $this->price . "', " : "") .
                    ($this->registration != null ? "registration = '" . $this->registration . "', " : "") .
                    ($this->year != null ? "year = " . $this->year . ", " : "") .
                    ($this->color != null ? "color = '" . $this->color . "', " : "") .
                    ($this->mileage != null ? "mileage = '" . $this->mileage . "', " : "") .
                    ($this->engine != null ? "engine = '" . $this->engine . "', " : "") .
                    ($this->fuelType != null ? "fuel_type = '" . $this->fuelType . "', " : "") .
                    ($this->description != null ? "description = '" . $this->description . "', " : "") .
                    ($this->desc_prestashop != null ? "desc_prestashop = '" . $this->desc_prestashop . "', " : "") .
                    ($this->comment != null ? "comment = '" . $this->comment . "', " : "") .
                    ($this->insertDate != null ? "insert_date = '" . $this->insertDate . "', " : "") .
                    ($this->url != null ? "url = '" . $this->url . "', " : "") .
                    "created_at = NOW()";
            $result = mysql_query($query);
            $this->id = (int) mysql_insert_id();
            //print_r($query."<br>idCar:".$this->id."<br><br>");
            return $result;
        } else {
            //$this->id = (int) $this->takeIdentifier();
            $query = "UPDATE `tbl_car` SET " .
                    ($this->provincia != null ? "provincia = '" . $this->provincia . "', " : "") .
                    ($this->brand != null ? "brand = '" . $this->brand . "', " : "") .
                    ($this->model != null ? "model = '" . $this->model . "', " : "") .
                    ($this->matricula != null ? "matricula = '" . $this->matricula . "', " : "") .
                    ($this->archived != null ? "archived = '" . $this->archived . "', " : "") .  
                    ($this->price != null ? "price = '" . $this->price . "', " : "") .
                    ($this->sellpieces != null ? "sellpieces = '" . $this->sellpieces . "', " : "") .
                    ($this->registration != null ? "registration = '" . $this->registration . "', " : "") .
                    ($this->year != null ? "year = " . $this->year . ", " : "") .
                    ($this->color != null ? "color = '" . $this->color . "', " : "") .
                    ($this->mileage != null ? "mileage = '" . $this->mileage . "', " : "") .
                    ($this->engine != null ? "engine = '" . $this->engine . "', " : "") .
                    ($this->fuelType != null ? "fuel_type = '" . $this->fuelType . "', " : "") .
                    ($this->description != null ? "description = '" . $this->description . "', " : "") .
                    ($this->desc_prestashop != null ? "desc_prestashop = '" . $this->desc_prestashop . "', " : "") .
                    ($this->comment != null ? "comment = '" . $this->comment . "', " : "") .
                    ($this->insertDate != null ? "insert_date = '" . $this->insertDate . "', " : "") .
                    ($this->url != null ? "url = '" . $this->url . "', " : "") .
                    "updated_at = NOW() WHERE id = " . $this->id;
            //print_r($query);
            return mysql_query($query); 
            
        }        
    }

    //</editor-fold>
    //<editor-fold desc="GETTERS & SETTERS">
    
    public function getProvincia() {
        return $this->provincia;
    }

    public function setProvincia($provincia) {
        $this->provincia = $provincia;
    }

        public function getBrand() {
        return $this->brand;
    }

    public function getModel() {
        return $this->model;
    }
    
    
    public function getArchived() {
        return $this->archived;
    }

    public function setArchived($archived) {
        $this->archived = $archived;
    }

        public function getPrice() {
        return $this->price;
    }
    
    public function getRegistration() {
        return $this->registration;
    }

    public function getYear() {
        return $this->year;
    }

    public function getMileage() {
        return $this->mileage;
    }

    public function getFuelType() {
        return $this->fuelType;
    }

    public function getEngine() {
        return $this->engine;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getComment() {
        return $this->comment;
    }

    public function getInsertDate() {
        return $this->insertDate;
    }

    public function getId() {
        return $this->id;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function getImages() {
        return $this->images;
    }
    public function getDetails() {
        return $this->details;
    }

    public function getUrl() {
        return $this->url;
    }
    
    public function setBrand($brand) {
        $this->brand = $brand;
    }

    public function setModel($model) {
        $this->model = $model;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function setRegistration($registration) {
        $this->registration = $registration;
    }

    public function setYear($year) {
        $this->year = $year;
    }

    public function setColor($color) {
        $this->color = $color;
    }

    public function setMileage($mileage) {
        $this->mileage = $mileage;
    }

    public function setFuelType($fuelType) {
        $this->fuelType = $fuelType;
    }

    public function setEngine($engine) {
        $this->engine = $engine;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setComment($comment) {
        $this->comment = $comment;
    }

    public function setInsertDate($insertDate) {
        $this->insertDate = $insertDate;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getDesc_prestashop() {
        return $this->desc_prestashop;
    }

    public function setDesc_prestashop($desc_prestashop) {
        $this->desc_prestashop = $desc_prestashop;
    }

    public function setImages($images) {
        /*
        if (is_array($images)) {
            foreach ($images as $image) {
                if (is_object($image)) {
                    //$this->images[] = $image;
                } else {
                    $image["car"] = $this->id;
                    $image = new CarImage(null, $image);
                    //$image->insertIntoDataBase();
                    //$this->images[] = $image;
                }
            }
        }*/
    }
    
    public function addimage($urlimage){        
        if (!$this->existInDB_image($urlimage) && $this->id != null) {
            $query = "INSERT INTO `tbl_car_image` SET " .
                    ($this->id != null ? "car = " . $this->id . ", " : "") .
                    ($this->url != null ? "url = '" . $urlimage . "', " : "") .
                    "created_at = NOW(), updated_at = NOW()";
            $result = mysql_query($query);                        
            return $result;
        } 
    }
    public function getImagesUrl() {
        $query = "SELECT * FROM `tbl_car_image` WHERE car = '".$this->id."'";
        $result = mysql_query($query);
        $imagesUrl = null;
        while ($row = mysql_fetch_assoc($result)){
            $imagesUrl[]= new CarImage($row["id"]);
        }        
        return $imagesUrl;
    }

    public function setImagesUrl($imagesUrl) {
        $this->imagesUrl = $imagesUrl;
    }

        
    private function existInDB_image($urlimage) {
        $query = "SELECT * FROM tbl_car_image WHERE url = '" . $urlimage . "'";
        $result = mysql_query($query);
        return mysql_num_rows($result) > 0;
    }
    
    public function getSellpieces() {
        return $this->sellpieces;
    }

    public function setSellpieces($sellpieces) {
        $this->sellpieces = $sellpieces;
    }
    
    public function getMatricula() {
        return $this->matricula;
    }

    public function setMatricula($matricula) {
        $this->matricula = $matricula;
    }

        /**
    public function setDetails($cardetails) {
        if (is_array($cardetails)) {
           
                if (is_object($cardetails)) {
                    $this->details[] = $details;
                } else {
                    $details["car"] = $this->id;
                    $details = new CarDetails(null, $details);
                    $details->insertIntoDataBase();
                    $this->$details[] = $details;
                }           
        }
    }
     ***/ 
     
    //</editor-fold>
}

?>
