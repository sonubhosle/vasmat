<?php
/**
 * Authentication and Authorization Helper Functions
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Check if a user is logged in and has the required role
 * 
 * @param array|string $allowed_roles Roles that are allowed to access the page
 * @return void Redirects if not authorized
 */
function checkRole($allowed_roles) {
    if (!isset($_SESSION['user_id'])) {
        $requestUri = $_SERVER['REQUEST_URI'];
        if (strpos($requestUri, '/superadmin/') !== false) {
            header("Location: " . BASE_URL . "auth/superadmin-login.php");
        } elseif (strpos($requestUri, '/admin/') !== false) {
            header("Location: " . BASE_URL . "auth/admin-login.php");
        } else {
            header("Location: " . BASE_URL . "auth/faculty-login.php");
        }
        exit;
    }

    if (is_string($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }

    if (!in_array($_SESSION['role'], $allowed_roles)) {
        // Unauthorized access based on role
        $_SESSION['error'] = "You do not have permission to access this page.";
        
        // Redirect to their respective dashboard
        switch ($_SESSION['role']) {
            case 'superadmin':
                header("Location: " . BASE_URL . "superadmin/dashboard.php");
                break;
            case 'admin':
                header("Location: " . BASE_URL . "admin/index.php");
                break;
            case 'faculty':
                header("Location: " . BASE_URL . "faculty/dashboard.php");
                break;
            default:
                header("Location: " . BASE_URL . "index.php");
        }
        exit;
    }
}

/**
 * Verify CSRF Token
 */
function verifyCSRF($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
}

/**
 * Generate CSRF Token
 */
function generateCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Sanitize Output
 */
if (!function_exists('e')) {
    function e($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Activity Logging
 */
function logActivity($conn, $user_id, $action, $description = '') {
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $action, $description, $ip);
    $stmt->execute();
    $stmt->close();
}
/**
 * Add Notification
 */
function addNotification($conn, $user_id, $title, $message) {
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $message);
    $stmt->execute();
    $stmt->close();
}
?>
