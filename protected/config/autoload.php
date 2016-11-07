<?php

/**
 * Autoload config file
 * 
 * @author pgarriga
 * @author Porpra Augmented SL
 * @name Autoload Config
 * @category Porpra
 * @package twitypromotions
 * @subpackage config
 */

function __autoload($modelName)
{    
    $file = "../models/$modelName.php";

    if (file_exists($file)) {
        require_once($file);
        return true;
    }
    return false;
}

spl_autoload_register('__autoload');
?>
