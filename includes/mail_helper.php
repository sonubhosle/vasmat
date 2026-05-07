<?php
/**
 * Simple Mail Helper
 * Support for both native mail() and True SMTP via SmtpClient.
 */

// Load config for BASE_URL, SITE_NAME and SMTP settings
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/smtp_client.php';

// Global to store the last link for local debugging/development
$last_generated_link = "";

function sendResetEmail($to, $token, $role) {
    global $last_generated_link;
    
    $subject = "Password Reset Request - " . SITE_NAME;
    $resetLink = BASE_URL . "auth/reset-password.php?token=" . $token . "&role=" . $role;
    $last_generated_link = $resetLink; 

    // HTML Message
    $message = "
    <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
        <h2 style='color: #4f46e5;'>Password Reset Request</h2>
        <p>A password reset has been requested for your account at " . SITE_NAME . ".</p>
        <p>Please click the link below to set a new password:</p>
        <p><a href='$resetLink' style='background: #4f46e5; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a></p>
        <p>If you did not request this, please ignore this email.</p>
    </div>";

    // If SMTP credentials are provided, use SmtpClient
    if (defined('SMTP_USER') && SMTP_USER !== 'your-email@gmail.com') {
        $smtp = new SmtpClient(SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, SMTP_SECURE);
        $fromEmail = 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'college.edu');
        return $smtp->send($to, $subject, $message, SITE_NAME, SMTP_USER);
    }

    // Otherwise fallback to mail()
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: " . SITE_NAME . " <noreply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ">\r\n";

    return @mail($to, $subject, $message, $headers);
}

function getLastResetLink() {
    global $last_generated_link;
    return $last_generated_link;
}
?>
