<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set("error_log", "logs/errors.log"); //guardar os erros no ficheiro criado para erros

error_reporting(E_ALL); // para reportar todos os erros PHP

ob_start(); // output buffering ligado

// Assign file paths to PHP constants
// __FILE__ returns the current path to this file
// dirname() returns the path to the parent directory
define("VENDOR_PATH", dirname(__FILE__)); //vendor
define("PUBLIC_PATH", dirname(VENDOR_PATH) . '/public'); //exame2.test/public

// Assign the root URL to a PHP constant
$public_end = strpos($_SERVER['SCRIPT_NAME'], '/public');
$doc_root = substr($_SERVER['SCRIPT_NAME'], 0, $public_end);

define("WWW_ROOT", $doc_root); // index

require_once('functions.php');
require_once('status_error_functions.php');
require_once('db_credentials.php');
require_once('database_functions.php');
require_once('validation_functions.php');
require_once('databaseobject.php');
require_once('autoload.php'); // Autoload para o Guzzle
require_once('blacklist_ips.php'); 

// Carrega Controllers conforme são chamados
function my_autoload($className) {
    $filename = dirname(__DIR__) . '/' . str_replace("\\", '/', $className) . ".php";
    $filename = strtolower($filename);
    
   // var_dump($filename);
    if (file_exists($filename)) {
 
        include($filename);
        if (class_exists($className)) {
            return true;
        }
    }
    return false;
}
 
spl_autoload_register('my_autoload');
 


// Database 
$database = db_connect();
Vendor\DatabaseObject::set_database($database);

session_start();


?>