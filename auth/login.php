<?php
require_once __DIR__ . '/../includes/auth_helper.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // verifyCSRF($_POST['csrf_token'] ?? ''); // Enable after adding CSRF to form

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Check users table
        $stmt = $conn->prepare("SELECT id, name, email, password, role, status, reference_id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'pending') {
                $error = "Your account is pending admin approval. Please wait for confirmation.";
            } elseif ($user['status'] === 'rejected') {
                $error = "Your registration request was rejected. Please contact the administrator.";
            } elseif ($user['status'] !== 'active') {
                $error = "Your account is currently inactive.";
            } else {
                // Set sessions
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['reference_id'] = $user['reference_id'];
                
                logActivity($conn, $user['id'], 'Login', 'User logged in successfully');

                // Redirect based on role
                switch ($user['role']) {
                    case 'superadmin':
                        header("Location: ../superadmin/dashboard.php");
                        break;
                    case 'admin':
                        header("Location: ../admin/index.php");
                        break;
                    case 'faculty':
                        header("Location: ../faculty/dashboard.php");
                        break;
                }
                exit;
            }
        } else {
            $error = "Invalid email or password.";
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
    <title>Login | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6 bg-[url('https://images.unsplash.com/photo-1541339907198-e08756ebafe3?auto=format&fit=crop&q=80&w=2070')] bg-cover bg-center bg-fixed">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    
    <div class="w-full max-w-md relative z-10">
        <div class="glass p-8 rounded-[2rem] shadow-2xl">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-500 rounded-2xl text-white text-2xl shadow-lg mb-4">
                    <i class="fas fa-university"></i>
                </div>
                <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Staff Login</h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Access your dashboard</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm font-bold mb-6 border border-red-100 flex items-center gap-3">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2 px-1">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="email" name="email" required 
                               class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm font-semibold text-slate-700" 
                               placeholder="name@college.edu">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2 px-1">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="password" name="password" required 
                               class="w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm font-semibold text-slate-700" 
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between px-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-tight">Remember me</span>
                    </label>
                    <a href="forgot-password.php" class="text-xs font-bold text-amber-600 hover:text-amber-700 uppercase tracking-tight">Forgot Password?</a>
                </div>

                <button type="submit" 
                        class="w-full py-4 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-black rounded-2xl shadow-xl shadow-amber-500/20 hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest text-sm">
                    Sign In
                </button>
            </form>

            <div class="mt-10 pt-8 border-t border-slate-100 text-center">
                <a href="../index.php" class="text-xs font-bold text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Back to Website
                </a>
            </div>
        </div>
        
        <p class="text-center text-slate-300 text-[10px] font-bold uppercase tracking-widest mt-8">
            © <?= date('Y') ?> MIT College. All rights reserved.
        </p>
    </div>
</body>
</html>
