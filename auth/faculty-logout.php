<?php
require_once __DIR__ . '/../includes/auth_helper.php';

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'faculty') {
    logActivity($conn, $_SESSION['user_id'], 'Faculty Logout', 'Faculty member logged out');
}

session_unset();
session_destroy();

header("Location: faculty-login.php");
exit;
?>
