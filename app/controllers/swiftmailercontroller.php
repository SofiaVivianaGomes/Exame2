<?php

namespace App\Controllers; //local onde está inserido

class SwiftMailerController {

    private static $username='academiaphp2019@gmail.com';
    private static $password='academiaphp2019';
    private static $smtp_server='smtp.gmail.com';

    public static function sendMail($dest, $title, $body) {

        try {
            // Preparar mensagem email:
            $message = new Swift_Message();

            $from =  [self::$username => 'Sofia'];
            $destination = [$dest];

            $message->setFrom($from);
            $message->setTo($destination);

            $message->setSubject($title);
            $message->setBody($body); // mandar credenciais
            //$message->setBody('This message was sent using the Swift Mailer SMTP transport');
            
        
            // Criar transporte:
            $transport = new Swift_SmtpTransport(self::$smtp_server,465,'ssl');
            $transport->setUsername(self::$username);
            $transport->setPassword(self::$password);
        
            $mailer = new Swift_Mailer($transport);
            $result = $mailer->send($message);
            
            if ($result) {
                echo "Number of emails sent: $result";
            } else {
                echo "Couldn't send email";
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }
}