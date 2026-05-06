<?php
require_once __DIR__ . '/../includes/auth_helper.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ? AND role = 'superadmin' LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                if ($user['status'] !== 'active') {
                    $error = "Your account is currently inactive.";
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];
                    
                    logActivity($conn, $user['id'], 'SuperAdmin Login', 'SuperAdmin logged in successfully');
                    header("Location: ../superadmin/dashboard.php");
                    exit;
                }
            } else {
                $error = "Incorrect password. Please type it carefully.";
            }
        } else {
            $error = "No SuperAdmin account found with that email.";
        }
        $stmt->close();
    }
}
$csrf_token = generateCSRF();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin Login | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center p-6 bg-[url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=2072')] bg-cover bg-center">
    <div class="absolute inset-0 bg-black/60"></div>
    
    <div class="w-full max-w-md relative z-10">
        <div class="glass p-10 rounded-[2.5rem] shadow-2xl border border-slate-800 shadow-purple-500/10">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-tr from-purple-600 to-blue-600 rounded-2xl text-white text-2xl shadow-lg mb-4">
                    <i class="fas fa-crown"></i>
                </div>
                <h1 class="text-2xl font-black text-white uppercase tracking-tight">SuperAdmin</h1>
                <p class="text-slate-400 font-medium text-sm mt-1">System level authentication</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-950/30 text-red-400 p-4 rounded-2xl text-xs font-bold mb-6 border border-red-900/50 flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 px-1">Master Email</label>
                    <div class="relative">
                        <i class="fas fa-user-shield absolute left-5 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                        <input type="email" name="email" required 
                               class="w-full pl-12 pr-5 py-4 bg-slate-900/50 border border-slate-800 rounded-2xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all text-sm font-semibold text-white placeholder-slate-600" 
                               placeholder="master@college.edu">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 px-1">Master Password</label>
                    <div class="relative">
                        <i class="fas fa-fingerprint absolute left-5 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                        <input type="password" name="password" required 
                               class="w-full pl-12 pr-5 py-4 bg-slate-900/50 border border-slate-800 rounded-2xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all text-sm font-semibold text-white placeholder-slate-600" 
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between px-1">
                    <a href="forgot-password.php?role=superadmin" class="text-[10px] font-black text-purple-400 hover:text-purple-300 uppercase tracking-tight">Reset Master Key</a>
                </div>

                <button type="submit" 
                        class="w-full py-5 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-black rounded-2xl shadow-xl shadow-purple-600/20 hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest text-sm">
                    Authorize Access
                </button>
            </form>

            <div class="mt-10 pt-8 border-t border-slate-800 text-center">
                <a href="../index.php" class="text-[10px] font-black text-slate-500 hover:text-slate-400 uppercase tracking-widest transition-all">
                    Back to Terminal
                </a>
            </div>
        </div>
    </div>
</body>
</html>
