<?php
/**
 * Global Configuration
 */

// Site Settings
define('SITE_NAME', 'MIT College of Computer Science & IT');
define('BASE_URL', 'http://localhost/vasmat/');

// Database Settings (Redirecting to central db file)
require_once __DIR__ . '/../admin/includes/db.php';

// Session Settings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Kolkata');

// SMTP Settings (For Real Email Support)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'sbhosle1011@gmail.com');
define('SMTP_PASS', 'yljg itwv foox snan');
define('SMTP_SECURE', 'tls'); // 'tls' or 'ssl'
?>
