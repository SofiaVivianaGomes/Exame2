<?php

namespace App\Controllers; //local onde está inserido
use Vendor\View;
use App\Log;

class LogsController {

    /*
     * Método: validate() 
     * Pârametros: $args (vazio ou indexes)
     * Retorno: $errors (vazio ou errors)
     * 
     * Objetivo: Validar os dados como username, data, pedido e ip
     *
    */
    public static function validate($args) 
    {
        $errors = []; 

        $username = $args['username'] ?? '';
        $date = $args['date'] ?? '';
        $request = $args['request'] ?? '';
        $ip = $args['ip'] ? $args['ip'] : '';

        if(is_blank($username)) {
            $errors['username'] = 'Invalid username.';
        }
        
        if(is_blank($date)) {
            $errors['date'] = 'Invalid date.';
        }

        if(is_blank($request)) {
            $errors['request'] = 'Invalid request.';
        }

        if(is_blank($ip)) {
            $errors['ip'] = 'Invalid IP.';
        }

        //return $errors;
        if (empty($errors)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Método: logs() 
     * Pârametros: -
     * Retorno: true / false
     * 
     * Objetivo: Registar o acesso aos logs
     *
    */
    public function logs()
    {
        $registed = self::register_log('Accessed logs page.');
        $logs = json_decode(Log::all_logs()); // buscar todos os logs à db;
        //$this->show_logs($logs); // mandar para a view
        return $registed;
    }

    /*
     * Método: add_log() 
     * Pârametros: $data (dados)
     * Retorno: $added (true / false, depende se for adicionado o log à db)
     * 
     * Objetivo: Adicionar o log à db
     *
    */
    public static function add_log($data) : bool //strict type
    { 
        // Validate data to save to table 'logs'
        $errors = self::validate($data); 

        if (!empty($errors)) {

            $added = false;
            //echo "Not added";

            // file save

        } else {
            // get id from username
            $user = UserController::find_by_username($data['username']);
            $username_id = intval($user->id);
            $data['username_id'] = $username_id; //add the id of the username
            
            $added = Log::add_log($data); // add to table
        }       

        return $added;   
    }

    /*
     * Método: register_log() 
     * Pârametros: $request (pedido a registar no log)
     * Retorno: $added (true / false, depende se for adicionado o log à db) do método add_log()
     * 
     * Objetivo: Regista os dados para o log
     *
    */
    public static function register_log($request) {
        $data = ['username' => $_SESSION['username'], 'date' => date("Y-m-d H:i:s", time()), 'request' => $request, 'ip' => $_SERVER['REMOTE_ADDR']];

        return self::add_log($data);
    }
    // $_SERVER['REMOTE_ADDR'] - próprio IP

    /*
    public function show_logs($data)
    {
        View::render('header');
        View::render('logs', $data);
        View::render('footer');
    }
    */   

}

?>





