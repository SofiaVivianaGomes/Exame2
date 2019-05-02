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

        return json_encode($errors);
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
        $errors = json_decode(self::validate($data)); 

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

    public static function report_logs_CSV()
    {
        $registed = self::register_log('Reported logs to CSV.');
        $logs = json_decode(Log::all_logs()); // buscar todos os logs à db;

        $saved = saveCSV($post);

        //$this->show_logs($logs); // mandar para a view
        return $registed;
    }

    public static function saveCSV($post) {

        $path = $_SERVER['DOCUMENT_ROOT'] . '/exame2.test/files/info.csv';
    
        $fileExists = file_exists($path);
    
        $columns = [];
        $line = [];
    
        //se o ficheiro não existir, posso escrever o nome das colunas
        if(!$fileExists) {
            $columns = array_keys($post);
            
            //echo "Columns: ";
            //var_dump($columns);
    
            $fp = fopen($path, "w"); //cria e escreve
            fputcsv($fp, $columns, ';');
            fclose($fp);
        }
    
        foreach ($post as $key => $value) {
            //se não houver valor
            if(empty($value)){
                $line[] .= 'No info';
            } 
            //se for um array, junta-se tudo numa string separada por vírgula
            elseif (is_array($value) ){
                $line[] .= implode(', ', $value);
            }
            //se for o valor apenas se põe o valor
            else {
                $line[] .= $value;
            }
                    
        }
        
        //echo  "Line: " . var_dump($line);
    
        $fp = fopen($path, "a"); //append, continua a escrever no ficheiro
        fputcsv($fp, $line, ';');
        fclose($fp);
        
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





