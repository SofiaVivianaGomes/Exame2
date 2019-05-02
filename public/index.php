<?php

$path = __DIR__ . '/vendor/boot.php';
$path = str_replace("/public", "", $path);
require_once $path;

$controller = $_GET['ct'] ?? '';
$method = $_GET['mt'] ?? '';
$data = NULL;
$csrf = '';

if ($_POST) {
    $data = $_POST;
}


//por defeito vai para o login 
if(!$controller) {

    $ns = "App\Controllers\UserController"; //namespace
    $method = "login";
    $ct = new $ns;
    $ct->$method();
    
} else {
    
    if(isset($_SESSION['csrf_token'])) {
        $csrf = $_SESSION['csrf_token'];
    }

    if((!empty($csrf)  && csrf_token_is_valid($csrf)) || ($controller == 'usercontroller' && $method == 'register') || ($controller == 'authcontroller' && $method == 'auth')) {
        $ns = "App\Controllers\\" . $controller; //namespace
        $ct = new $ns;
    
        echo $ct->$method($data);  // como o método não retorna nada tenho de fazer echo

    } else {
        echo json_encode('Invalid Request');
    }

}

?>

