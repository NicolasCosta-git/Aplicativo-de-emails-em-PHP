<?php

require "Exception.php";
require "OAuth.php";
require "PHPMailer.php";
require "POP3.php";
require "SMTP.php";

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;


// objeto email, usado para recuperar msg, assunto e destinatário
class Email
{

    private $para = null;
    private $assunto = null;
    private $mensagem = null;

    function __construct($para, $assunto, $mensagem)
    {
        $this->para = $para;
        $this->assunto = $assunto;
        $this->mensagem = $mensagem;
    }

    function __set($variavel, $valor)
    {
        $this->$variavel = $valor;
    }

    function __get($variavel)
    {
        return $this->$variavel;
    }

    function verificarMensagem()
    {
        if (empty($this->para) || empty($this->assunto) || empty($this->mensagem)) { 
            return false;
        } else {
            return true;
        };
    }
}

$msg = new Email($_POST['para'], $_POST['assunto'], $_POST['mensagem']);

if (!$msg->verificarMensagem()) {
    header('location: index.php?erro');
    die(); 
}

$mail = new PHPMailer(true);

try {
    //Configuração do servidor
    $mail->SMTPDebug = false;                                                      // ativa ou desativa debug, true ou false
    $mail->isSMTP();                                                              // enviar usando o SMTP
    $mail->Host       = 'smtp.gmail.com';                                        // Endereço do servidor smtp a ser usado, smtp.gmail.com é o do google
    $mail->SMTPAuth   = true;                                                   // Usar a autenticação SMTP
    $mail->Username   = 'email do remetente';                                  // Usuário do email
    $mail->Password   = 'senha do email';                                     // Senha do email. deve aparece um email solicitando o acesso 
    $mail->SMTPSecure = 'tls';                                               // Tipo de criptografia 
    $mail->Port       = 587;                                                // Porta do serviço escolhido, 587 é o do google
    $mail->setFrom('email do remetente', 'nome ou Usuário do remetente');  // escolha o email e nome do remetente a ser mostrado no email
    $mail->addAddress($msg->__get('para'));                               // destinatário, recuperado do objeto Email
    $mail->isHTML(true);                                                 // define o formato do email para html
    $mail->Subject = $msg->__get('assunto');                            // recupera o assunto do objeto Email
    $mail->Body    = $msg->__get('mensagem');                          // recupera a mensagem do objeto email
    $mail->AltBody = '';                                              // corpo alternativo, para usar em caso do navegador não suporte html

    $mail->send();
    header("location: enviado.php");
} catch (Exception $e) {
    header("location: erro.php");
};
