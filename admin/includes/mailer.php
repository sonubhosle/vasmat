<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendResetEmail($to, $link){
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        // 🔴 CHANGE THESE
        $mail->Username   = 'yourgmail@gmail.com';
        $mail->Password   = 'your-app-password';

        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('yourgmail@gmail.com', 'Admin Panel');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset';
        $mail->Body    = "
            <h3>Password Reset</h3>
            <p>Click the link below:</p>
            <a href='$link'>$link</a>
            <p>Expires in 1 hour.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
