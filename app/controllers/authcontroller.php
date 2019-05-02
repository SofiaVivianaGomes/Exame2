<?php

namespace App\Controllers; //local onde está inserido
use App\Auth;

class AuthController {
    
    private $errors, $name, $email, $username, $password;

    /*
     * Método: auth() 
     * Pârametros: $data ($_POST or NULL)
     * Retorno: json / false
     * 
     * Objetivo: Quando se faz o register, gera uma hash bcrypt com a data e o username que faz um URL, em que este é mandado pelo Slack
     * 
     * e depois verifica com o get se quando voltam a fazer o login com o username é igual à tabela e username inserido
     *
    */
    public function auth($data = NULL) {

        if ($data === NULL) {
            //self::show_auth();
        } else { 

            $username = $data['username'] ?? '';

            if(!is_blank($username)) { 

                $hashGenerated = self::generate_hash($username);
                //$hashGenerated = true;

                if($hashGenerated) {
                    //$data = "<p>Authorized: " . $username . "</p>";
                    //self::show_auth($data);
                    return true;
                }

            } else {
                //não tem username
                //$data = "<p>Invalid username.</p>";
                //self::show_auth($data);
            }

        }
        return false;
    }

    /*
     * Método: generate_hash() 
     * Pârametros: $username
     * Retorno: true / false (depende se gerou a hash e mandou pelo Slack)
     * 
     * Objetivo: Gera uma hash bcrypt com a data e o username que faz um URL, em que este é mandado pelo Slack
     * 
    */
    public static function generate_hash($username) {

        $hash = password_hash($username . date('d-m-Y H:i:s'), PASSWORD_DEFAULT); //bcrypt

        $date = date("Y-m-d H:i:s", time());

        $added = Auth::add_hash($username, $hash, $date);

        if ($added) {
            return SwiftMailerController::sendMail($username);
            //return self::send_slack_message($hash);
        } 

        return false;

    }

    /*
     * Método: verify() 
     * Pârametros: $username, $hash
     * Retorno: true / false (depende se verificou que existe)
     * 
     * Objetivo: Verifica se o username e hash estão na DB
     * 
    */
    public static function verify($username, $hash) {

        //$hash = self::generate_hash($username);

        $verified_username_hash = Auth::verify_username_hash($username, $hash);
        $verified_date = Auth::verify_date($username, $hash);

        return ($verified_username_hash && $verified_date);

    } 

    /*
     * Método: send_slack_message() 
     * Pârametros: $hash
     * Retorno: true / false (depende se mandou a mensagem)
     * 
     * Objetivo: Mandar uma mensagem para o Slack com a hash
     * 
    */
    /*
    public static function send_slack_message($hash) 
    {
        $message = "Link to register:
                    http://exame2.test/register?h=" . $hash; 

        $data = http_build_query([
            "token" => "xoxb-101259630004-520202224629-DPHnA8VClMhljp6nkOQzR5BH",
            "channel" => "#botecho", // Canal para onde querem enviar
            "text" => $message,
            "username" => "AcademiaPHP Bot",
        ]);

        
        $url = "https://slack.com/api/"."chat.postMessage"; // chat.postMessage é o método

        // Criar a stream
        $opts = array(
            'http'=>array(
                'method'=>'POST',
                'header'=>"Content-type: application/x-www-form-urlencoded\r\n",
                'content' => $data        
            )
        );

        // Cria um contexto stream
        // (um contexto é um conjunto de parâmetros e opções específicas 
        // do wrapper que modificam ou aprimoram o comportamento de um fluxo)
        $context = stream_context_create($opts);

        // Abre o ficheiro usando os headers HTTP criados 
        // file_get_contents() - lê o ficheiro inteiro e passa para string
        $file = file_get_contents($url, false, $context);

        if($file) {
            return true;
        } else {
            return false;
        }
    }
    */

    /*
    public function show_auth($data=NULL) 
    {
        View::render('auth', $data);
    }
    */

}

?>





