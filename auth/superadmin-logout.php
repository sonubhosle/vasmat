<?php
require_once __DIR__ . '/../includes/auth_helper.php';

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'superadmin') {
    logActivity($conn, $_SESSION['user_id'], 'SuperAdmin Logout', 'Super Administrator logged out');
}

session_unset();
session_destroy();

header("Location: superadmin-login.php");
exit;
?>
