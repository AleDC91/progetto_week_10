<?php 
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendWelcomeEmail($emailTo, $firstName, $lastName){


$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                    
    $mail->isSMTP();                                           
    $mail->Host       = 'sandbox.smtp.mailtrap.io';    
    $mail->SMTPAuth   = true;                    
    $mail->Username   = '3fbd567e8eeb43';     
    $mail->Password   = '086e1e80adca38';                     
    $mail->Port       = 2525;


    //Recipients
    $mail->setFrom('libreria@app.com', 'Libreria');
    $mail->addAddress($emailTo, $firstName . " " . $lastName);     

    //Content
    $mail->isHTML(true);                                  
    $mail->Subject = 'Benvenuto in Libreria!';
    $mail->Body    = "<h1>Grazie <b>$firstName $lastName</b> per esserti registrato al sito della libreria!</h1>";
    $mail->AltBody = 'Grazie per esserti registrato al sito della libreria';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}}
?>