<?php

/**
 * Categoria Model.
 *
 *  id;Active (0/1);Name*;Parent Category;Root category (0/1);
 *   Description;Meta-title;Meta-keywords;Meta-description;
 *   URL rewritten;Image URL;ID ou nom de la boutique
 * 
 */
class Categoria {

    //<editor-fold desc="ATTRIBUTES">
    protected $id = null;
    protected $active = null;
    protected $name = null;
    protected $isrootcategory = null;
    protected $idparent = null;
    protected $parentcategory = null;
    protected $rootcategory = null;
    protected $description = null;
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
        $query = "SELECT * FROM `ps_category_lang` 
                    INNER JOIN ps_category on ps_category.id_category = ps_category_lang.id_category
                    WHERE ps_category_lang.id_category = '".$id. "'";
        //echo $query;
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_assoc($result);
            //print_r($row);
            // Fill attributes
            $this->__constructByFields($row);
            return true;
        } else {
            return false;
        }
    }

    function __constructByFields($fields) {
        $this->id = $fields["id_category"];
        isset($fields["active"]) ? $this->active = $fields["active"] : null;
        isset($fields["name"]) ? $this->name = $fields["name"] : null;        
        isset($fields["is_root_category"]) ? $this->isrootcategory = $fields["is_root_category"] : null;
        isset($fields["id_parent"]) ? $this->idparent = $fields["id_parent"] : null;
        isset($fields["parentcategory"]) ? $this->parentcategory = $fields["parentcategory"] : null;
        isset($fields["rootcategory"]) ? $this->rootcategory = $fields["rootcategory"] : null;
        isset($fields["description"]) ? $this->description = $fields["description"] : null;
        isset($fields["metatitle"]) ? $this->metatitle = $fields["metatitle"] : null;
        isset($fields["metakeywords"]) ? $this->metakeywords = $fields["metakeywords"] : null;
        isset($fields["urlrewritten"]) ? $this->urlrewritten = $fields["urlrewritten"] : null;
        isset($fields["imageurl"]) ? $this->imageurl = $fields["imageurl"] : null;
        isset($fields["idboutique"]) ? $this->idboutique = $fields["idboutique"] : null;
        
        /*
          $this->publishAt = $fields["publish_at"];
          $this->destroyAt = $fields["destroy_at"];
         * 
         */
    }

    public static function categoria_nophoto() {
        // busquem totes les categories -2 que són peces de recanvi
        // en realitat haurien de les categories actives que no tenen foto,
        // pero no sé pas com mirar-ho....
        $id_category_sql = "SELECT * FROM  `ps_category_lang` 
                            INNER JOIN ps_category on ps_category_lang.id_category = ps_category.id_category 
                            where active = '1'
                            order by level_depth asc";
        $result_id_categories = mysql_query($id_category_sql);
        return $result_id_categories;
        // return @mysql_num_rows($result) > 0 ? true : false;
    }
    
    
    public static function categoria_photo($id_categoria) {
        // busquem totes les categories -2 que són peces de recanvi
        // en realitat haurien de les categories actives que no tenen foto,
        // pero no sé pas com mirar-ho....
        $id_category_sql = "SELECT * FROM  `ps_category_lang` 
                            INNER JOIN ps_category on ps_category_lang.id_category = $id_categoria 
                            order by level_depth asc";
        $result_id_categories = mysql_query($id_category_sql);
        return $result_id_categories;
        // return @mysql_num_rows($result) > 0 ? true : false;
    }

    public static function nom_categoria_model_a100($codmodelo) {
        $query = "SELECT * FROM `A100MOD` WHERE Codigo='" . $codmodelo . "'";
        //echo "<br>".$query."<br>";
        $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());
        $row = mysql_fetch_array($result);
        if (isset($row["Nombre"])) {
            /*
              $model = preg_replace("[", "", $row["Nombre"]);
              $model = preg_replace("#\[(.*)\]#", "", $model);
              $model = preg_replace("#\[(.*)#", "", $model);
              $model = preg_replace("# V1 #", "", $model);
              $model = preg_replace("# V2 #", "", $model);
              $model = preg_replace("# V3 #", "", $model);
              $model = preg_replace("# V4 #", "", $model);
              $model = preg_replace("# V5 #", "", $model);
             */
            return strtoupper(trim($row["Nombre"]));
        } else {
            return '';
        }
    }

    public static function marca_model_de_clvaut($clvaut) {
        
        try {
            //busquem la marca i model base de la peça que tenim seleccionada
            $sql_marca_model = "SELECT A100MOD.Nombre from A100MOD "
                    . "INNER JOIN B100MOD on B100MOD.clvvin = A100MOD.Codigo "
                    . "INNER JOIN entradas on entradas.codmodelo = B100MOD.Codigo "
                    . "INNER JOIN Almacen on Almacen.clvaut = entradas.codigo "
                    . "where entradas.codigo = " . $clvaut;
            $result_marca_model = mysql_query($sql_marca_model);
            $row_marca_model = mysql_fetch_array($result_marca_model);
            $marca_model = $row_marca_model["Nombre"];
        } catch (Exception $e) {
            $marca_model = "";
        }
        
        return $marca_model;
    }
    public static function nom_categoria_peça_marca_model($texto,$clvaut) {
        $categoria_tipus_pieza_nom = sanear_string(quitar_abreviaturas($texto));
        try {
            //busquem la marca i model base de la peça que tenim seleccionada
            $sql_marca_model = "SELECT A100MOD.Nombre from A100MOD "
                    . "INNER JOIN B100MOD on B100MOD.clvvin = A100MOD.Codigo "
                    . "INNER JOIN entradas on entradas.codmodelo = B100MOD.Codigo "
                    . "INNER JOIN Almacen on Almacen.clvaut = entradas.codigo "
                    . "where entradas.codigo = " . $clvaut;
            $result_marca_model = mysql_query($sql_marca_model);
            $row_marca_model = mysql_fetch_array($result_marca_model);
            $categoria_peça_marca_model = ucwords($categoria_tipus_pieza_nom . ' ' . $row_marca_model["Nombre"]);
        } catch (Exception $e) {
            $categoria_peça_marca_model = "";
        }
        
        return $categoria_peça_marca_model;
    }

    public static function nom_categoria_model_b100($codmodelo) {
        //$query = "SELECT * FROM `a100mod` WHERE Codigo='" . $codmodelo . "'";
        $query = "SELECT * FROM `B100MOD` WHERE Codigo='" . $codmodelo . "'";

        //comprovar conexió base de dades
        try {
            $result = mysql_query($query);// or die('Consulta fallida: ' . mysql_error());
        } catch (Exception $e) {
            echo "<br><br><br>MySQL conexio perduda<br><br><br>";
            $con = mysql_connect('bbdd.recambiosya.es', 'ddb34891', 'G7xMFJyVzd');
            mysql_select_db('ddb34891', $con);
            mysql_query("SET NAMES UTF8");
            echo "<br>Reconectat<br><br><br>";
            $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());
        }

        //echo "<br><br><br>" . $query . "<br>";
        $row = mysql_fetch_array($result);
        if (isset($row["clvvin"])) {
            $cod_model_base = $row["clvvin"];
        } else {
            $cod_model_base = $codmodelo;
        }
        
        //un cop sabem el clvvin podem anar a buscar el model base, que serà la categoria
        $query2 = "select * from A100MOD where codigo = '" . $cod_model_base . "'";
        // NO FUNCIONA BÉ : $query2 = "select * from B100MOD where codigo = '" . $cod_model_base . "'";
        //print_r ($query2);
        //echo "<br>";
        $result2 = mysql_query($query2);
        $row2 = mysql_fetch_array($result2);
        //print_r ($row2);
        //echo "<br>";             
        $row["Nombre"] = $row2["Nombre"];
        //echo "model row2:" . $row2["Nombre"];
/*
        if (isset($row["Nombre"])) {
            $model = strtoupper($row["Nombre"]);
            $model = preg_replace("# V1 #", " ", $model);
            $model = preg_replace("# V2 #", " ", $model);
            $model = preg_replace("# V3 #", " ", $model);
            $model = preg_replace("# V4 #", " ", $model);
            $model = preg_replace("# V5 #", " ", $model);
            $model = preg_replace("# F1 #", " ", $model);
            $model = preg_replace("# F2 #", " ", $model);
            $model = preg_replace("# F3 #", " ", $model);
            $model = preg_replace("# F4 #", " ", $model);
            $model = preg_replace("# F5 #", " ", $model);
            $model = preg_replace("# ASTRA [A-Z] #", " ASTRA ", $model);
            $model = preg_replace("# COMBO [A-Z] #", " COMBO ", $model);
            $model = preg_replace("# CORSA [A-Z] #", " CORSA ", $model);
            $model = preg_replace("# OMEGA [A-Z] #", " OMEGA ", $model);
            $model = preg_replace("# VECTRA [A-Z] #", " VECTRA ", $model);

            $model = preg_replace("# V40 [A-Z] #", " V40 ", $model);
            $model = preg_replace("# LANOS [A-Z] #", " LANOS ", $model);
            $model = preg_replace("# V40 [A-Z] #", " V40 ", $model);
            $model = preg_replace("# V40 [A-Z] #", " V40 ", $model);


            $model = preg_replace("# C 8 #", " C8 ", $model);
            $model = preg_replace("# C 4 #", " C4 ", $model);
            $model = preg_replace("# C 3 #", " C3 ", $model);
            $model = preg_replace("# C 2 #", " C2 ", $model);
            $model = preg_replace("# C 1 #", " C1 ", $model);

            $model = preg_replace("# CLIO [1-9] #", " CLIO ", $model);
            $model = preg_replace("# ESPACE [1-9] #", " ESPACE ", $model);
            $model = preg_replace("# MEGANE [1-9] #", " MEGANE ", $model);
            $model = preg_replace("# TRAFIC [1-9] #", " TRAFIC ", $model);
            $model = preg_replace("# TWINGO [1-9] #", " TWINGO ", $model);
            $model = preg_replace("# KANGOO [1-9] #", " KANGOO ", $model);
            $model = preg_replace("# LAGUNA [1-9] #", " LAGUNA ", $model);
            $model = preg_replace("# SCENIC [1-9] #", " SCENIC ", $model);
            $model = preg_replace("# IBIZA [1-9] #", " IBIZA ", $model);
            $model = preg_replace("# FOCUS [1-9] #", " FOCUS ", $model);
            $model = preg_replace("# FIESTA [1-9] #", " FIESTA ", $model);
            $model = preg_replace("# CHEROKEE [1-9] #", " CHEROKEE ", $model);
            $model = preg_replace("# ESCORT [1-9] #", " ESCORT ", $model);
            $model = preg_replace("# MONDEO [1-9] #", " MONDEO ", $model);
            $model = preg_replace("# TRANSIT [1-9] #", " TRANSIT ", $model);
            $model = preg_replace("# DUCATO [1-9] #", " DUCATO ", $model);
            $model = preg_replace("# CARENS [1-9] #", " CARENS ", $model);
            $model = preg_replace("# EXPERT [1-9] #", " EXPERT ", $model);
            $model = preg_replace("# PARTNER [1-9] #", " PARTNER ", $model);
            $model = preg_replace("# ZAFIRA [1-9] #", " ZAFIRA ", $model);
            $model = preg_replace("# ALMERA [1-9] #", " ALMERA ", $model);
            $model = preg_replace("# MICRA [1-9] #", " MICRA ", $model);
            $model = preg_replace("# PRIMERA [1-9] #", " PRIMERA ", $model);
            $model = preg_replace("# COLT [1-9] #", " COLT ", $model);
            $model = preg_replace("# NUBIRA [1-9] #", " NUBIRA ", $model);
            $model = preg_replace("# BERLINGO [1-9] #", " BERLINGO ", $model);
            $model = preg_replace("# JUMPY [1-9] #", " JUMPY ", $model);
            $model = preg_replace("# SAXO [1-9] #", " SAXO ", $model);
            $model = preg_replace("# XANTIA [1-9] #", " XANTIA ", $model);
            $model = preg_replace("# XSARA [1-9] #", " XSARA ", $model);
            $model = preg_replace("# COUPE [1-9] #", " COUPE ", $model);
            $model = preg_replace("# ACCORD [1-9] #", " ACCORD ", $model);
            $model = preg_replace("# DOBLO [1-9] #", " DOBLO ", $model);
            $model = preg_replace("# FIORINO [1-9] #", " FIORINO ", $model);
            $model = preg_replace("# CIVIC [1-9] #", " CIVIC ", $model);
            $model = preg_replace("# PUNTO [1-9] #", " PUNTO ", $model);
            $model = preg_replace("# SEICENTO [1-9] #", " SEICENTO ", $model);
            $model = preg_replace("# MATIZ [1-9] #", " MATIZ ", $model);
            $model = preg_replace("# LANOS [1-9] #", " LANOS ", $model);
            $model = preg_replace("# VOYAGER [1-9] #", " VOYAGER ", $model);
            $model = preg_replace("# AURIS [1-9] #", " AURIS ", $model);
            $model = preg_replace("# ACCENT [1-9] #", " ACCENT ", $model);
            $model = preg_replace("# ORION [1-9] #", " ORION ", $model);
            $model = preg_replace("# PROBE [1-9] #", " PROBE ", $model);
            $model = preg_replace("# SCUDO [1-9] #", " SCUDO ", $model);
            $model = preg_replace("# UNO [1-9] #", " UNO ", $model);
            $model = preg_replace("# ATOS [1-9] #", " ATOS ", $model);
            $model = preg_replace("# MERIVA [1-9] #", " MERIVA ", $model);
            //$model = preg_replace("# xxxx [1-9] #", " xxxx ", $model);


            $model = preg_replace("# YARIS [1-9] #", " CARENS ", $model);
            $model = preg_replace("# BEETLE [1-9] #", " BEETLE ", $model);
            $model = preg_replace("# PASSAT [1-9] #", " PASSAT ", $model);
            $model = preg_replace("# FABIA [1-9] #", " FABIA ", $model);
            $model = preg_replace("# OCTAVIA [1-9] #", " OCTAVIA ", $model);
            $model = preg_replace("# ALHAMBRA [1-9] #", " ALHAMBRA ", $model);
            $model = preg_replace("# AROSA [1-9] #", " AROSA ", $model);
            $model = preg_replace("# CORDOBA [1-9] #", " CORDOBA ", $model);
            $model = preg_replace("# LEON [1-9] #", " LEON ", $model);
            $model = preg_replace("# TOLEDO [1-9] #", " TOLEDO ", $model);
            $model = preg_replace("#OPEL ASTRA GTC#", "OPEL ASTRA", $model);
            $model = preg_replace("#OPEL ASTRA V1#", "OPEL ASTRA", $model);
            $model = preg_replace("#VOLKSWAGEN GOLF III#", "VOLKSWAGEN GOLF", $model);
            $model = preg_replace("#HYUNDAI GETZ 2#", "HYUNDAI GETZ", $model);
            $model = preg_replace("#M. BENZ A W168#", "M. BENZ A", $model);

            //$model = preg_replace("# [0-9] \[#", " [", $model);
            $model = preg_replace("#\[(.*)\]#", " ", $model);
            $model = preg_replace("#\[(.*)#", " ", $model);
            return strtoupper(trim($model));
        } else {
            return '';
        }
 * 
 */
        return strtoupper(trim($row["Nombre"]));
    }

//</editor-fold>
//<editor-fold desc="DATABASE METHODS">
    /**
     * Exists this bid in Data base?
     * @return boolean
     */
    public static function existInPrestashop($nomCategoria) {
        $query = "SELECT * FROM `ps_category_lang` WHERE name = '" . $nomCategoria . "' ";
        $result = mysql_query($query);
        return @mysql_num_rows($result) > 0 ? true : false;
    }
    
    public static function CategoryIdPrestashop($nomCategoria) {
        $query = "SELECT * FROM `ps_category_lang` WHERE name = '" . $nomCategoria . "' ";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);
        return $row["id_category"];        
    }
    
    public static function idmaxcategoria() {
        $query = "SELECT max(id_category) as maxid FROM `ps_category`";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);
        return $row["maxid"];
    }

    public static function activeInPrestashop($nomCategoria) {
        $query = "SELECT * FROM SELECT * FROM  `ps_category`  WHERE id_category = $idCategory ";
        //cal fer aquesta consulta fent un joint i posant de condició active=''
        $result = @mysql_query($query);
        return @mysql_num_rows($result) > 0 ? true : false;
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
    function getIsrootcategory() {
        return $this->isrootcategory;
    }

    function setIsrootcategory($isrootcategory) {
        $this->isrootcategory = $isrootcategory;
    }
    function getIdparent() {
        return $this->idparent;
    }

    function setIdparent($idparent) {
        $this->idparent = $idparent;
    }

}

?>
