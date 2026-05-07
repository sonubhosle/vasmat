<?php
/**
 * Minimalist SMTP Client for PHP
 * Supports AUTH LOGIN, TLS/SSL, and HTML Email.
 */
class SmtpClient {
    private $host;
    private $port;
    private $user;
    private $pass;
    private $secure;
    private $error = '';

    public function __construct($host, $port, $user, $pass, $secure = 'tls') {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->secure = strtolower($secure);
    }

    public function getError() {
        return $this->error;
    }

    public function send($to, $subject, $message, $fromName, $fromEmail) {
        $host = ($this->secure === 'ssl' ? 'ssl://' : '') . $this->host;
        $socket = fsockopen($host, $this->port, $errno, $errstr, 30);

        if (!$socket) {
            $this->error = "Could not connect to SMTP host $host ($errno: $errstr)";
            return false;
        }

        $this->getResponse($socket); // Initial connection response
        $this->sendCommand($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

        if ($this->secure === 'tls') {
            $this->sendCommand($socket, "STARTTLS");
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->error = "Failed to start TLS encryption";
                fclose($socket);
                return false;
            }
            $this->sendCommand($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        }

        if ($this->user) {
            $this->sendCommand($socket, "AUTH LOGIN");
            $this->sendCommand($socket, base64_encode($this->user));
            $this->sendCommand($socket, base64_encode($this->pass));
        }

        $this->sendCommand($socket, "MAIL FROM:<$fromEmail>");
        $this->sendCommand($socket, "RCPT TO:<$to>");
        $this->sendCommand($socket, "DATA");

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "To: <$to>\r\n";
        $headers .= "From: $fromName <$fromEmail>\r\n";
        $headers .= "Subject: $subject\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        $headers .= "X-Mailer: SystemSmtpClient\r\n\r\n";

        fwrite($socket, $headers . $message . "\r\n.\r\n");
        $this->getResponse($socket);

        $this->sendCommand($socket, "QUIT");
        fclose($socket);
        return true;
    }

    private function sendCommand($socket, $cmd) {
        fwrite($socket, $cmd . "\r\n");
        return $this->getResponse($socket);
    }

    private function getResponse($socket) {
        $response = "";
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == " ") break;
        }
        return $response;
    }
}
?>
