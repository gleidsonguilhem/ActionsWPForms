<?php
require 'C:\wamp64\www\DevPaesToth\homolog\wordpress\busca\PHPMailer-master\PHPMailer-master\src\SMTP.php';
require 'C:\wamp64\www\DevPaesToth\homolog\wordpress\busca\PHPMailer-master\PHPMailer-master\src\Exception.php';
require 'C:\wamp64\www\DevPaesToth\homolog\wordpress\busca\PHPMailer-master\PHPMailer-master\src\PHPMailer.php';

use PHPMailer\PHPMailer\PHPMailer;

    function sendEmail($nome) {

        $nome_n = utf8_encode($nome) ;

        $outlook_mail = new PHPMailer;
        
        $outlook_mail->IsSMTP();
        // Send email using Outlook SMTP server
        $outlook_mail->Host = 'smtp-mail.outlook.com';
        // port for Send email
        $outlook_mail->Port = 587;
        $outlook_mail->SMTPSecure = 'tls';
        $outlook_mail->SMTPDebug = 1;
        $outlook_mail->SMTPAuth = true;
        $outlook_mail->Username = '';
        $outlook_mail->Password = '';
        
        $outlook_mail->From = '';
        $outlook_mail->FromName = 'Policastro - Notificacao';// frome name
        $outlook_mail->AddAddress('', '');  // Add a recipient  to name
        //$outlook_mail->AddAddress('');  // Name is optional
        
        $outlook_mail->IsHTML(true); // Set email format to HTML
        
        $outlook_mail->Subject = "Policastro - Aprovacao";
        $outlook_mail->Body    = "Solicitacao Aprovada<br><br>" .
                                  "Solicitante: ". $nome_n . "<br><br>" .
                                  "
                                    <a href=''>Ver Solicitacao</a><br><br>
                                    At.,<br><br>
                                    
                                    Equipe Policastro
                                  ";
        $outlook_mail->AltBody = '';
        
        if(!$outlook_mail->Send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $outlook_mail->ErrorInfo;
        exit;
        }
        else
        {
        echo 'Message of Send email using Outlook SMTP server has been sent';
        }
    }
?>