<?php

namespace App\Services;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mail = null;

    function __construct(){
        $this->mail = new PHPMailer();
        $this->mail->isSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPSecure = "tls";
        $this->mail->Host = "smtp.gmail.com";
        $this->mail->Port = 587;

        $this->mail->Username = "mussebrahiam11@gmail.com";
        $this->mail->Password = "xmuiphywhagssjdw";
    }

    public function sendMail(
        string $titulo,
        string $nombre,
        string $correo,
        string $asunto,
        string $body
    )
    {
        $this->mail->setFrom("mussebrahiam11@gmail.com", $titulo);
        $this->mail->addAddress($correo, $nombre);
        $this->mail->Subject = $asunto;
        $this->mail->Body = $body;
        $this->mail->isHTML(true);
        $this->mail->CharSet = "UTF-8";

        return $this->mail->send();
    }
}
