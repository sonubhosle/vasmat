<?php
require_once 'includes/auth_helper.php';

$email = 'master@college.edu';
$pass = 'password123';
$hashed = password_hash($pass, PASSWORD_DEFAULT);

// Update or Insert the Super Admin
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES ('Super Admin', ?, ?, 'superadmin', 'active') ON DUPLICATE KEY UPDATE password = ?, status = 'active', role = 'superadmin'");
$stmt->bind_param("sss", $email, $hashed, $hashed);

if ($stmt->execute()) {
    // Verify what's actually in the database now
    $check = $conn->query("SELECT * FROM users WHERE email = '$email'")->fetch_assoc();
    
    echo "<h2 style='color:green'>SUCCESS! Admin account repaired.</h2>";
    echo "<p><b>Database Record Found:</b></p>";
    echo "<pre>" . print_r($check, true) . "</pre>";
    
    echo "<p>You can now login with:</p>";
    echo "<ul><li><b>URL</b>: <a href='auth/superadmin-login.php'>auth/superadmin-login.php</a></li>";
    echo "<li><b>Email</b>: $email</li>";
    echo "<li><b>Password</b>: $pass</li></ul>";
} else {
    echo "<h2 style='color:red'>Error: " . $conn->error . "</h2>";
}
?>
