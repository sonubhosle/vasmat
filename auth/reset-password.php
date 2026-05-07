<?php
require_once __DIR__ . '/../includes/auth_helper.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';
$role = $_GET['role'] ?? '';

if (empty($token)) {
    header("Location: login.php");
    exit;
}

// Verify token
$stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW() AND role = ? LIMIT 1");
$stmt->bind_param("ss", $token, $role);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    $error = "Invalid or expired reset link. Please request a new one.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($password) || strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt_update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $stmt_update->bind_param("si", $hashed, $user['id']);
        
        if ($stmt_update->execute()) {
            $success = "Password reset successfully! You can now login.";
        } else {
            $error = "Failed to update password.";
        }
        $stmt_update->close();
    }
}

$theme_color = 'amber-500';
if ($role === 'admin') { $theme_color = 'blue-600'; }
if ($role === 'superadmin') { $theme_color = 'purple-600'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md">
        <div class="bg-white p-10 rounded-[2.5rem] shadow-xl border border-slate-100">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-<?= $theme_color ?>/10 text-<?= $theme_color ?> rounded-2xl text-2xl mb-4">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Create New Password</h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Security Update</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-xs font-bold mb-6 border border-red-100 flex items-center gap-3">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-emerald-50 text-emerald-600 p-6 rounded-2xl text-xs font-bold mb-6 border border-emerald-100 text-center">
                    <i class="fas fa-check-circle text-2xl mb-3 block"></i>
                    <?= $success ?>
                    <a href="<?= $role ?>-login.php" class="block mt-4 py-3 bg-emerald-600 text-white rounded-xl uppercase tracking-widest text-[10px]">Go to Login</a>
                </div>
            <?php elseif ($user): ?>
                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-2 px-1">New Password</label>
                        <input type="password" name="password" required 
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-<?= $theme_color ?>/20 focus:border-<?= $theme_color ?> transition-all text-sm font-semibold" 
                               placeholder="Min. 6 characters">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-2 px-1">Confirm New Password</label>
                        <input type="password" name="confirm_password" required 
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-<?= $theme_color ?>/20 focus:border-<?= $theme_color ?> transition-all text-sm font-semibold" 
                               placeholder="Repeat your password">
                    </div>

                    <button type="submit" 
                            class="w-full py-5 bg-<?= $theme_color ?> text-white font-black rounded-2xl shadow-xl shadow-<?= $theme_color ?>/20 hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest text-sm">
                        Update Password
                    </button>
                </form>
            <?php else: ?>
                <div class="text-center">
                    <a href="forgot-password.php?role=<?= $role ?>" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-all">
                        <i class="fas fa-arrow-left"></i> Request New Link
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
