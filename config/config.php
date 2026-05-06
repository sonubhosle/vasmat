<?php
/**
 * Global Configuration
 */

// Site Settings
define('SITE_NAME', 'MIT College of Computer Science & IT');
define('BASE_URL', 'http://localhost/mit-college/');

// Database Settings (Redirecting to central db file)
require_once __DIR__ . '/../admin/includes/db.php';

// Session Settings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Kolkata');
?>
