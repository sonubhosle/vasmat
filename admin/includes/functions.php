<?php
/**
 * Security and Helper Functions for MIT College Project
 */

// CSRF Protection functions
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate a CSRF token and store it in the session
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Check if the provided CSRF token matches the one in the session
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get a CSRF token hidden input field
 */
function csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Sanitize output to prevent XSS
 */
function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate and sanitize file uploads
 * 
 * @param array $file The $_FILES element
 * @param array $allowed_extensions Array of allowed extensions (lowercase)
 * @param string $upload_dir Path to the upload directory
 * @return array [success => bool, filename => string|null, error => string|null]
 */
function secure_upload($file, $allowed_extensions, $upload_dir) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'filename' => null, 'error' => 'File upload error code: ' . $file['error']];
    }

    $filename = basename($file['name']);
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($extension, $allowed_extensions)) {
        return ['success' => false, 'filename' => null, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowed_extensions)];
    }

    // Check MIME type as well for extra security
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file['tmp_name']);
    
    // Minimal MIME type check
    $allowed_mimes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'txt' => 'text/plain',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif'
    ];

    $is_allowed_mime = false;
    foreach ($allowed_extensions as $ext) {
        if (isset($allowed_mimes[$ext]) && $allowed_mimes[$ext] === $mime_type) {
            $is_allowed_mime = true;
            break;
        }
    }
    
    // If it's a generic text file or unknown but in allowed list, we might allow it, 
    // but for PDFs and images we should be strict
    if (!$is_allowed_mime && in_array($extension, ['pdf', 'jpg', 'jpeg', 'png'])) {
         return ['success' => false, 'filename' => null, 'error' => 'File content does not match its extension.'];
    }

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Generate a unique, safe filename
    $new_filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
    $destination = rtrim($upload_dir, '/') . '/' . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $new_filename, 'error' => null];
    } else {
        return ['success' => false, 'filename' => null, 'error' => 'Failed to move uploaded file.'];
    }
}
