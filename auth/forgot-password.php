<?php
require_once __DIR__ . '/../includes/auth_helper.php';
require_once __DIR__ . '/../includes/mail_helper.php';

$error = '';
$success = '';
$role = $_GET['role'] ?? 'faculty';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ? AND role = ? LIMIT 1");
        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt_update = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $stmt_update->bind_param("ssi", $token, $expires, $user['id']);
            $stmt_update->execute();
            
            // Send Real Email
            if (sendResetEmail($email, $token, $role)) {
                $success = "A reset link has been sent to your email ($email). Please check your inbox.";
            } else {
                // FALLBACK FOR LOCALHOST DEVELOPMENT
                $link = getLastResetLink();
                if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || $_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
                    $success = "A reset link has been generated. Since this is a local environment, you can use the link below:<br><br><a href='$link' class='text-blue-600 underline break-all'>$link</a>";
                } else {
                    $error = "Failed to send email. Please contact system administrator.";
                }
            }
        } else {
            $error = "No account found with that email for this role.";
        }
        $stmt->close();
    }
}

$theme_color = 'amber-500';
$role_title = 'Faculty';
if ($role === 'admin') { $theme_color = 'blue-600'; $role_title = 'Admin'; }
if ($role === 'superadmin') { $theme_color = 'purple-600'; $role_title = 'SuperAdmin'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | <?= SITE_NAME ?></title>
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
                    <i class="fas fa-key"></i>
                </div>
                <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Reset Password</h1>
                <p class="text-slate-500 font-medium text-sm mt-1"><?= $role_title ?> Account Recovery</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-xs font-bold mb-6 border border-red-100"><?= $error ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-emerald-50 text-emerald-600 p-6 rounded-2xl text-xs font-bold mb-6 border border-emerald-100 text-center">
                    <i class="fas fa-paper-plane text-2xl mb-3 block"></i>
                    <?= $success ?>
                </div>
            <?php else: ?>
                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-2 px-1">Email Address</label>
                        <input type="email" name="email" required 
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-<?= $theme_color ?>/20 focus:border-<?= $theme_color ?> transition-all text-sm font-semibold" 
                               placeholder="Enter your registered email">
                    </div>

                    <button type="submit" 
                            class="w-full py-5 bg-<?= $theme_color ?> text-white font-black rounded-2xl shadow-xl shadow-<?= $theme_color ?>/20 hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest text-sm">
                        Send Recovery Link
                    </button>
                </form>
            <?php endif; ?>

            <div class="mt-8 pt-8 border-t border-slate-50 text-center">
                <a href="<?= $role ?>-login.php" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-all">
                    Back to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
