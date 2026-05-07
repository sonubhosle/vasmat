<?php
require_once __DIR__ . '/../includes/auth_helper.php';

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    logActivity($conn, $_SESSION['user_id'], 'Admin Logout', 'Administrator logged out');
}

session_unset();
session_destroy();

header("Location: admin-login.php");
exit;
?>
