<?php

//@ini_set('display_errors', 'on');
//include('lib/PSWebServiceLibrary.php');
//require_once("protected/config/main.php");

//define('DEBUG', false);
//define('PS_SHOP_PATH', 'http://localhost/prestashop/');
//define('PS_WS_AUTH_KEY', 'HUIOC79YUPH1XLHXZZ6QXRI84PI397RC');

/*
 * Description of Category
 * 
 * @todo:
 *          1. Incloure marca al meta-keywords (__construct())
 *          2. Omplir: metakeywords, urlrewriten, parentcategory
 * 
 * @autor ramoncarreras
 */

class PS_Categoria {

    private $active = null;
    private $name = null;
    private $parentcategory = null;
    private $rootcategory = null;
    private $description = null;
    private $metatitle = null;
    private $metakeywords = null;
    private $metadescription = null;
    private $urlrewritten = null;
    private $is_root_category = null;
    private $language_id = null;
    private $conn = null;
    private $reg = null;

    /**
     * Constructor classe Category.
     * @param string $name
     */
    
    function __construct($fields) {
        $this->__constructByFields($fields);
        /*
        if ($id) {
            return $this->__constructById($id);
        } else if (is_array($fields) && !is_null($fields)) {
            return $this->__constructByFields($fields);
        } else {
            return false;
        }*/
    }
    
    function __constructById($name) {
        /*
        $this->active = "1";  // 1 per activarla (0 / 1)
        $this->name = strtoupper($name) . "-3"; //MARCA-2  (ex: AUDI-2 RENAULT-2)
        // ?!*!¡¿!¡*x
        //$this->parentcategory = "3"; // n'hi ha 3: peces noves, peces de segona ma, cotxes (necessio ID)
        $this->rootcategory = "0"; // 0 -> No és categoría principal  (0 / 1)        
        $this->description = "Recambios y piezas de segunda mano para el modelo X de la marca $name";
        $this->metatitle = $name . ",pieza segunda mano,recambio usado";
        $this->metakeywords = "";
        $this->metadescription = "Piezas de segunda mano para " . $name . " RecambiosYa"; //desription.
        $this->urlrewritten = "";
        // ?!*!¡¿!¡*x
        $this->is_root_category = "0";
        $this->language_id = "1";
        //$this->conn = new Conn($mysql_host_d, $mysql_user_d, $mysql_password_d, $mysql_database_d); // Connexió amb la bdd
        //$this->reg = new Reg();
        //$this->reg->add("o", "Categoria \"$this->name\" instanciada correctament.");
         * */
         
    }

    function __constructByFields($fields) {
        //echo "<br>Construct by fields<br>";
        echo "<br>PS_Categoria:ContrstuctbyFields:<br>";
        print_r($fields);
        isset($fields["name"]) ? $this->name = $fields["name"] : null;
        $this->active = "1";
        //isset($fields["parentcategory"]) ? $this->parentcategory = $fields["parentcategory"] : null; 
        $this->parentcategory = $this->categoryIdInPrestashop($fields["parentcategory"]);
        isset($fields["rootcategory"]) ? $this->is_root_category = $fields["rootcategory"] : null;
        isset($fields["description"]) ? $this->description = $fields["description"] : null;
        isset($fields["metakeywords"]) ? $this->metakeywords = $fields["metakeywords"] : null;
        isset($fields["metatitle"]) ? $this->metatitle = $fields["metatitle"] : null;
        isset($fields["metadescription"]) ? $this->metadescription = $fields["metadescription"] : null;
        isset($fields["urlrewritten"]) ? $this->urlrewritten = $fields["urlrewritten"] : null;
        isset($fields["is_root_category"]) ? $this->modelo = $fields["rootcategory"] : 0;
        //isset($fields["language_id"]) ? $this->metakeywords = $fields["metakeywords"] : null;
        $this->language_id = "1";        
        
        //$this->conn = new Conn($mysql_host_d, $mysql_user_d, $mysql_password_d, $mysql_database_d); // Connexió amb la bdd
        //$this->reg = new Reg();
        //$this->reg->add("o", "Categoria \"$this->name\" instanciada correctament.");
    }
    /**
     * ********\ O /*********
     * ********\ K /*********
     * 
     * Mètode per verificar categoria. Fa el següent:
     *  1. Crea i activa categoria si no existeix (a la bdd de Prestashop).
     *  2. Activa categoria si existeix però està desactivada
     *  3. Retorna true si la categoria ha sigut verificada correctament
     *  i false si no ha sigut així.
     * 
     * @return boolean
     * @access public
     */
    public function verifyCategory() {
        //$this->reg->add("i", "Verificant categoria \"$this->name\":");
        //echo "<br>verifyCategory<br>";
        if ($this->existsInPrestashop()) {
            //Si existeix...
            //1. COMPROVEM SI ESTÀ ACTIU
            if ($this->isActive()) {
                //$this->reg->add("o", "$this->name existent i activa.");
                echo "<br>existent i activa";
                return true;
            } else {
                //2. SI NO ESTÀ ACTIU, ACTIVEM
                if ($this->activate()) {
                    //$this->reg->add("o", "Categoria $this->name activada.");
                    //echo "<br>activada.<br>";
                    return true;
                } else {
                    //$this->reg->add("e", "La categoria $this->name existeix però no s'ha pogut activar.");
                    //echo "<br>existeix però no s'ha pogut activa";
                    return false;
                }
            }
        } else {
            // mysql_close();
            // mysql_connect();
            // 
            //Si no existeix, la creem:
            //1. LOG:
            //$this->reg->add("e", "$this->name no existeix.");
            //$this->reg->add("i", "Creant categoria \"$this->name\" ...");            
            //2. COMPROVAR CAMPS CRÍTICS
            //echo "pre inserim categoria a la bbdd<br>";
            /*
            if (!$this->existsLanguage()){
                echo "no existeix language<br>";
                return false;
            }
             * 
             */
            if(!$this->existsParentCategory()&&($this->is_root_category==0)){
                echo "<br>no existeix categoria pare ".$this->is_root_category;
                return false;
            }
            //3. INSERIR A PS
            echo "<br>intentem categoria a la bbdd";
            return $this->insertIntoPSDB();
        }
    }

    /**
     * ********\ O /*********
     * ********\ K /*********
     * 
     * Mètode per saber si existeix la categoria al PrestaShop.
     *  
     * @return boolean
     * @access private
     */
    private function existsInPrestashop() { //Aquest $name serà del tipus: MARCA-2
        $sql_select = "SELECT * FROM `ps_category_lang` WHERE name = '" . $this->name . "' ";
        $result = mysql_query($sql_select);
        //echo "PS_Categoria.php : result";
        //print_r($result);
        return @mysql_num_rows($result) > 0 ? true : false;
    }
    
    private function categoryIdInPrestashop($nameCategory) { //Aquest $name serà del tipus: MARCA-2
        $sql_select = "SELECT * FROM `ps_category_lang` WHERE name = '" . $nameCategory . "' ";
        $result = mysql_query($sql_select);
        $row = mysql_fetch_array($result);
        if (isset($row["id_category"])){
            return $row["id_category"];
        }else{
            return '0';
        }        
    }
    
    /**
     * ********\ O /*********
     * ********\ K /*********
     * 
     * Mètode per verificar que existeix el language_id a al botiga.
     * 
     * @return boolean
     * @access public
     */
    public function existsLanguage() {
        $sql_select = "SELECT * FROM `ps_category_lang` WHERE id_lang = '" . $this->language_id . "' ";
        //echo $sql_select;
        $result = mysql_query($sql_select);
        if (@mysql_num_rows($result) > 0)
            return true;
        else {
            //$this->reg->add("e", "La llengua que intentes inserir (id=$this->language_id) no està definida al prestashop.");
            return false;
        }
//        return @mysql_num_rows($result) > 0 ? true : false;
    }

    /**
     * ********\ O /*********
     * ********\ K /*********
     * 
     * Mètode per verificar que existeix la categoria pare.
     * 
     * @return boolean
     * @access public
     */
    public function existsParentCategory() {
        $sql_select = "SELECT * FROM `ps_category` WHERE id_category = '" . $this->parentcategory . "' ";
        //$sql_select = "SELECT * FROM `ps_category_lang` WHERE name = '" . $this->parentcategory . "' ";
        //echo $sql_select."<br>";
        $result = mysql_query($sql_select);
        if (@mysql_num_rows($result) > 0)
            return true;
        else {
            //$this->reg->add("e", "La categoria pare que intentes establir (id=$this->parentcategory) no està definida al prestashop.");
            return false;
        }
//        return @mysql_num_rows($result) > 0 ? true : false;
    }

    /**
     * ********\ O /*********
     * ********\ K /*********
     * 
     * Mètode per obtenir la última id utilitzada. (a les categories)
     * 
     * @return int maxId
     * @access public
     */
    public static function idmaxcategory() {
        $sql_select = "SELECT max(id_category) as maxid FROM `ps_category`";
        $result = mysql_query($sql_select);
        $row = mysql_fetch_array($result);
        return $row["maxid"];
    }

    /**
     * ********\ O /*********
     * ********\ K /*********
     * 
     * Mètode per saber si la categoria està activada al PS.
     * 
     * @param type $name
     * @return boolean
     * @access private
     */
    private function isActive() {
        $sql_select = "SELECT `active` FROM `ps_category` WHERE `id_category` = (SELECT distinct(`id_category`) FROM `ps_category_lang` WHERE `name`='$this->name')";
        $result = mysql_query($sql_select);
        $line = mysql_fetch_row($result);
        return $line[0] > 0 ? true : false;
    }

    /**
     * ********\ O /*********
     * ********\ K /*********
     * 
     * Mètode per a activar la categoría a PS.
     * 
     * @return boolean
     * @access private
     */
    private function activate() {
        $sql_update = "UPDATE `ps_category` SET `active`= 1 WHERE `id_category` = (SELECT 
            distinct(`id_category`) as id FROM `ps_category_lang` WHERE `name`= '" . $this->name . "')";
        $result =  mysql_query($sql_update);
        return $result ? true : false;
    }

    /**
     * ********\ O /*********
     * ********\ K /*********
     * 
     * Mètode per inserir la categoría a la bdd de PS.
     * 
     * @return bool
     */
    private function insertIntoPSDB() {
        //echo "<br>insertintopsdb<br>";
        //UTILITZANT WEBSERVICES
        $webService = new PrestaShopWebservice(PS_SHOP_PATH , PS_WS_AUTH_KEY , false);
        $url = PS_SHOP_PATH . 'api/categories?schema=blank';
        echo "<br>".$url."<br>";
        $xml = $webService->get(array('url' => PS_SHOP_PATH . 'api/categories?schema=blank'));
        $resources = $xml->children()->children();
        unset($resources->id);
        unset($resources->position);
        unset($resources->id_shop_default);
        unset($resources->date_add);
        unset($resources->date_upd);
        unset($resources->id_boutique);
        $resources->active = $this->active;
        $resources->id_parent = $this->parentcategory;
        $resources->id_parent['xlink:href'] = PS_SHOP_PATH . 'api/categories/' . $this->parentcategory;
        $resources->is_root_category = $this->is_root_category;
        $node = dom_import_simplexml($resources->name->language[0][0]);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($this->name));
        $resources->name->language[0][0] = $this->name;
        $resources->name->language[0][0]['id'] = $this->language_id;
        $resources->name->language[0][0]['xlink:href'] = PS_SHOP_PATH . 'api/languages/' . $this->language_id;
        $node = dom_import_simplexml($resources->description->language[0][0]);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($this->description));
        $resources->description->language[0][0] = $this->description;
        $resources->description->language[0][0]['id'] = $this->language_id;
        $resources->description->language[0][0]['xlink:href'] = PS_SHOP_PATH . 'api/languages/' . $this->language_id;
        $node = dom_import_simplexml($resources->link_rewrite->language[0][0]);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($this->urlrewritten));
        $resources->link_rewrite->language[0][0] = $this->urlrewritten;
        $resources->link_rewrite->language[0][0]['id'] = $this->language_id;
        $resources->link_rewrite->language[0][0]['xlink:href'] = PS_SHOP_PATH . 'api/languages/' . $this->language_id;
        $node = dom_import_simplexml($resources->meta_title->language[0][0]);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($this->metatitle));
        $resources->meta_title->language[0][0] = $this->metatitle;
        $resources->meta_title->language[0][0]['id'] = $this->language_id;
        $resources->meta_title->language[0][0]['xlink:href'] = PS_SHOP_PATH . 'api/languages/' . $this->language_id;
        $node = dom_import_simplexml($resources->meta_description->language[0][0]);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($this->metadescription));
        $resources->meta_description->language[0][0] = $this->metadescription;
        $resources->meta_description->language[0][0]['id'] = $this->language_id;
        $resources->meta_description->language[0][0]['xlink:href'] = PS_SHOP_PATH . 'api/languages/' . $this->language_id;
        $node = dom_import_simplexml($resources->meta_keywords->language[0][0]);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($this->metakeywords));
        $resources->meta_keywords->language[0][0] = $this->metakeywords;
        $resources->meta_keywords->language[0][0]['id'] = $this->language_id;
        $resources->meta_keywords->language[0][0]['xlink:href'] = PS_SHOP_PATH . 'api/languages/' . $this->language_id;
        try {
            echo "<br>dins try<br>";
            print_r($resources);
            $opt = array('resource' => 'categories');
            $opt['postXml'] = $xml->asXML();
            //echo "<br>POSTXML finalitzat<br>";
            $xml = $webService->add($opt);
            //$this->reg->add("o", "Categoria $this->name creada correctament.");
            echo "<br>webservices add fet";
            echo "<br>--------------------------------<br><br><br>";
            
            
            return true;
        } catch (PrestaShopWebserviceException $ex) {
            echo "<br>excepcio try<br>";
            echo $ex->getMessage();
            echo "<br>--------------------------------<br><br><br>";
            //$this->reg->add("e", "Error al crear la categoria $this->name");
            return false;
        }
    }

    //******************************************//
    //**********  GETTERS && SETTERS  **********//
    //******************************************//

    
    public function getActive() {
        return $this->active;
    }

    public function setActive($active) {
        $this->active = $active;
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

    public function getMetadescription() {
        return $this->metadescription;
    }

    public function setMetadescription($metadescription) {
        $this->metadescription = $metadescription;
    }

    public function getUrlrewritten() {
        return $this->urlrewritten;
    }

    public function setUrlrewritten($urlrewritten) {
        $this->urlrewritten = $urlrewritten;
    }

    public function getIs_root_category() {
        return $this->is_root_category;
    }

    public function setIs_root_category($is_root_category) {
        $this->is_root_category = $is_root_category;
    }

    public function getLanguage_id() {
        return $this->language_id;
    }

    public function setLanguage_id($language_id) {
        $this->language_id = $language_id;
    }

    public function getConn() {
        return $this->conn;
    }

    public function setConn($conn) {
        $this->conn = $conn;
    }

    public function getReg() {
        return $this->reg;
    }

    public function setReg($reg) {
        $this->reg = $reg;
    }
}