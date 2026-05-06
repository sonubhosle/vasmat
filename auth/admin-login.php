<?php
require_once __DIR__ . '/../includes/auth_helper.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                $error = "Your account is currently inactive.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                
                logActivity($conn, $user['id'], 'Admin Login', 'Admin logged in successfully');
                header("Location: ../admin/dashboard.php");
                exit;
            }
        } else {
            $error = "Invalid admin credentials.";
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
    <title>Admin Login | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-6 bg-[url('https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=2070')] bg-cover bg-center">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
    
    <div class="w-full max-w-md relative z-10">
        <div class="glass p-10 rounded-[2.5rem] shadow-2xl border border-slate-800/50">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl text-white text-2xl shadow-lg mb-4">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Admin Portal</h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Management authentication</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-sm font-bold mb-6 border border-red-100 flex items-center gap-3">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div>
                    <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-2 px-1">Admin Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="email" name="email" required 
                               class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm font-semibold text-slate-700" 
                               placeholder="admin@college.edu">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-2 px-1">Security Key</label>
                    <div class="relative">
                        <i class="fas fa-key absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="password" name="password" required 
                               class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm font-semibold text-slate-700" 
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between px-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-tight">Keep session</span>
                    </label>
                    <a href="forgot-password.php?role=admin" class="text-[10px] font-black text-blue-600 hover:text-blue-700 uppercase tracking-tight">Recover Access</a>
                </div>

                <button type="submit" 
                        class="w-full py-5 bg-blue-600 text-white font-black rounded-2xl shadow-xl shadow-blue-600/20 hover:bg-blue-700 hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest text-sm">
                    Enter Dashboard
                </button>
            </form>

            <div class="mt-10 pt-8 border-t border-slate-100 text-center">
                <a href="../index.php" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
