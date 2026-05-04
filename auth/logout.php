<?php
require_once __DIR__ . '/../includes/auth_helper.php';

if (isset($_SESSION['user_id'])) {
    logActivity($conn, $_SESSION['user_id'], 'Logout', 'User logged out');
}

session_unset();
session_destroy();

header("Location: login.php");
exit;
?>
