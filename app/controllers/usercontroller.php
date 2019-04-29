<?php

namespace App\Controllers; //local onde está inserido
use Vendor\View;
use App\User;

class UserController {
    
    private $errors, $name, $username, $password;

    /*
     * Método: validate() 
     * Pârametros: $data ($_POST / NULL), $registered (true / false)
     * Retorno: $errors (vazio ou errors)
     * 
     * Objetivo: Validar os campos name (se for registo), username, password
     *
    */
    public function validate($data, $registered) 
    {
        $this->errors = []; 

        if (array_key_exists('name', $data)) {
            // Registo
            $this->name = $data['name'] ?? '';
            
            if (is_blank($this->name)) {
                $this->errors['name'] = 'Invalid name.';
            }
        } 

        $this->username = $data['username'] ?? '';
        $this->password = $data['password'] ?? '';

        if(is_blank($this->username)) {
            $this->errors['username'] = 'Invalid username.';
        }

        if(!$registered && !$this->has_unique_username($this->username)) {
            $this->errors['username'] = 'Invalid username.';
        }

        if(is_blank($this->password)) {
            $this->errors['password'] = 'Invalid password.';
        }
        
        return $this->errors;
    }

    /*
     * Método: login() 
     * Pârametros: $data ($_POST / NULL)
     * Retorno: 200 OK / false (depende se fez um header location)
     * 
     * Objetivo: Validar os campos name (se for registo), username, password
     *
    */
    public function login($data = NULL)
    {        
        // Se está logged in não vale a pena voltar a fazer login
        if (isset($_SESSION['username'])) { 
            LogsController::register_log('Tried to access login page while logged in. Sent to indexes page.');
            header("Location: /indexes"); 
        }

        if ($data === NULL) { // Não foram mandados dados

            //$this->show_login();            

        } else {

            //var_dump($data);
            
            // Validar a data para o login
            $validated = $this->validate($data, true); //está registado

            if (!empty($this->errors) && $validated == false) {
                //View::render('login', $this);
            } else {
                
                if($this->log_user()) {
                    LogsController::register_log('Logged in.');
                    header("Location: /indexes");
                } else {
                    $this->errors[] = "Invalid login.";
                    //View::render('login', $this);
                }

            }
        }

        return false;
    }

    public function register($data = NULL)
    {
        // Se está logged in não vale a pena voltar a fazer login
        if (isset($_SESSION['username'])) { 
            LogsController::register_log('Tried to access registration page while logged in. Sent to indexes page.');
            header("Location: /indexes"); 
        }

        /* 
        depois criar tabela auth com id e hash 
        
        método:
        quando se faz o register gerar uma hash md5 com a data e o username que faz um URL, mandar o URL pelo slack
        e depois verificar com o get se quando voltam a fazer o login com o username é igual à tabela e username inserido
        
        */
        
        if ($data === NULL) {
            //$this->show_register();
        } else { 

            // Validate data to register
            $validated = $this->validate($data, false); //isn't registered yet

            if (!empty($this->errors) && $validated == false) {
                //View::render('register', $this);
            } else { //preencheu tudo

                $hash = $_GET['h'] ?? '';
                
                if (is_blank($hash)) { 
                    $this->errors[] = "Invalid registration.";
                    //View::render('register', $this);
                } else {
                    
                    $authorized = AuthController::verify($this->username, $hash);

                    if($authorized) {

                        // If registered, go to indexes page
                        if ($this->register_user()) {

                            if($this->log_user()) {                        
                                LogsController::register_log('Registered and logged in.');
                                header("Location: /indexes");
                            }
                            
                        } else {
                            $this->errors[] = "Invalid registration.";
                            //View::render('register', $this);
                        }

                    } else {

                        $this->errors[] = "Invalid registration.";
                        //View::render('register', $this);
                        
                    }
                }

            }

        }
    }

    public function log_user() : bool //strict type
    { 
        $logged = User::log_user($this->username, $this->password);

        if ($logged) {
            // prevent session fixation attacks
            session_regenerate_id(); //regenerate the session everytime there's a login 
            $_SESSION['username'] = $this->username; // save session variable
        }

        return $logged;   
    }

    public function register_user() 
    {
        $user = new User;
        $user->name = $this->name;
        $user->username = $this->username;
        $user->password = $this->password;
        $user->register = date("Y-m-d H:i:s", time());
        $user->login = $user->register;

        return $user->save();
    }

    /*
    public function create_hash() {

        AuthController::generate_hash($this->username);

    }
    */

    public function logout() 
    {
        LogsController::register_log('Logged out.');

        unset($_SESSION['username']);

        unset($this->name);        
        unset($this->username);
        unset($this->password);

        header("Location: /login");
    }

    // has_unique_username('johnqpublic')
    // * Validates uniqueness of admins.username
    // * For new records, provide only the username.
    // * For existing records, provide current ID as second argument
    //   has_unique_username('johnqpublic', 4)
    function has_unique_username($username, $current_id="0") {
        
        // Need to re-write for OOP
        $user = User::find_by_username($username);
        if ($user === false || $user->id == $current_id) {
            // é único
            return true;
        } else {
            // não é único
            return false;
        }
    }

    public static function find_by_username($username) {
        return User::find_by_username($username);
    }

    /*
    public function show_login() 
    {
        View::render('login');
    }

    public function show_register() 
    {
        View::render('register');
    }
    */
    

}

?>





