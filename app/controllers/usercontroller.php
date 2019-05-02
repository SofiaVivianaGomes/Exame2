<?php

namespace App\Controllers; //local onde está inserido
use App\User;

class UserController {
    
    private $errors, $name, $username, $password, $valid;

    /*
     * Método: validate() 
     * Pârametros: $data ($_POST / NULL), $registered (true / false)
     * Retorno: $errors (vazio ou errors)
     * 
     * Objetivo: Validar os campos name (se for registo), username, password
     *
    */
    public function validate($data, $registered, $to_alter) 
    {
        $this->errors = []; 

        if($to_alter) {

            if (is_blank($data['new_password'])) {
                $this->errors['new_password'] = 'Invalid new password.';
            }

        } else {

            if (array_key_exists('name', $data)) {
                // Registo
                $this->name = $data['name'] ?? '';
                
                if (is_blank($this->name)) {
                    $this->errors['name'] = 'Invalid name.';
                }
            } 
    
            $this->username = $data['username'] ?? '';
            //$this->password = $data['password'] ?? '';
    
            if(is_blank($this->username)) {
                $this->errors['username'] = 'Invalid username.';
            }
    
            if(!$registered && !$this->has_unique_username($this->username)) {
                $this->errors['username'] = 'Invalid username.';
            }
    
            //if(is_blank($this->password)) {
            //    $this->errors['password'] = 'Invalid password.';
            //}

        }
        
        //return $errors;
        if (empty($this->errors)) {
            return false;
        } else {
            return true;
        }
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
            $validated = $this->validate($data, true, false); //está registado

            if (!empty($this->errors) || $validated == false) {
                //View::render('login', $this);
            } else {
                
                if($this->log_user()) {
                    LogsController::register_log('Logged in.');
                    //header("Location: /indexes");
                    return password_hash($this->password, PASSWORD_DEFAULT);
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
            $validated = $this->validate($data, false, false); //isn't registered yet

            if (!empty($this->errors) || $validated == false) {
                //View::render('register', $this);
            } else { //preencheu tudo

                $hash = $_GET['h'] ?? '';
                
                if (is_blank($hash)) { 
                    $this->errors[] = "Invalid registration.";
                    //View::render('register', $this);
                } else {

                    $authorized = EmailListController::verify($this->username); // verificar se está na lista
            
                    if($authorized) {

                        $random = randomPassword();

                        // Se registado, fazer logo o login e ir para a página de indexes
                        if ($this->register_user($random)) {

                            LogsController::register_log('Registered');

                           return SwiftMailerController::sendMail($this->username, $random);
                            
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
        return false;
    }

    /*
     * Método: alter() 
     * Pârametros: $data ($_POST / NULL)
     * Retorno: 200 OK / false (depende se fez um header location)
     * 
     * Objetivo: Validar os campos name (se for registo), username, password
     *
    */
    public function alter($data=NULL, $hash=NULL)
    {        
        if ($hash !== NULL) {

            if ($data === NULL) { // Não foram mandados dados

                //$this->show_login();            

            } else {

                //var_dump($data);
                
                // Validar a data para o login
                $validated = $this->validate($data, true, true); 

                if ($validated) {
                    //View::render('login', $this);
                } else {
                    
                    if($this->alter_user()) {
                        LogsController::register_log('Altered password.');
                        header("Location: /indexes");
                    } else {
                        $this->errors[] = "Invalid new password.";
                        //View::render('login', $this);
                    }

                }
            }

        }
        return false;
    }

    public function log_user() : bool //strict type
    { 
        $logged = User::log_user($this->username, $this->password);

        if ($logged) {
            // prevent session fixation attacks
            session_regenerate_id(); // regenerar a sessão sempre que há um login
            $_SESSION['username'] = $this->username; //guardar sessão do user

            // Após login, criar o token
            $csrf = create_csrf_token();
            $_SESSION['csrf_token'] = $csrf;
        }

        return $logged;   
    }

    public function register_user($random) 
    {
        $user = new User;
        $user->name = $this->name;
        $user->username = $this->username;
        $user->password = password_hash($random, PASSWORD_DEFAULT); //bcrypt
        $user->register = date("Y-m-d H:i:s", time());
        $user->login = $user->register;
        $user->valid = true;

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

    public function alter_user($new_password) : bool //strict type
    { 
        $altered = User::alter_user($this->username, $new_password);

        if ($altered) {
            $this->password = $new_password;
        }

        return $altered;   
    }

    public function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 5; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
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





