<?php
require_once __DIR__ . '/../includes/auth_helper.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'faculty';
            $status = 'pending';

            // Insert into users table
            $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("sssss", $name, $email, $hashed_password, $role, $status);
            
            if ($stmt_insert->execute()) {
                $success = "Registration successful! Your account is pending admin approval.";
            } else {
                $error = "Registration failed. Please try again.";
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Registration | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6 bg-[url('https://images.unsplash.com/photo-1523050853064-85a175e4bb17?auto=format&fit=crop&q=80&w=2070')] bg-cover bg-center">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    
    <div class="w-full max-w-lg relative z-10">
        <div class="glass p-10 rounded-[2.5rem] shadow-2xl">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-500 rounded-2xl text-white text-2xl shadow-lg mb-4">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Faculty Registration</h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Join the MIT College academic team</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-sm font-bold mb-6 border border-red-100 flex items-center gap-3">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-emerald-50 text-emerald-600 p-6 rounded-2xl text-sm font-bold mb-6 border border-emerald-100 text-center">
                    <i class="fas fa-check-circle text-3xl mb-3 block"></i>
                    <?= $success ?>
                    <div class="mt-6">
                        <a href="login.php" class="px-8 py-3 bg-emerald-600 text-white rounded-xl uppercase tracking-widest text-xs">Back to Login</a>
                    </div>
                </div>
            <?php else: ?>
                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-2 px-1">Full Name</label>
                        <input type="text" name="name" required 
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-semibold" 
                               placeholder="e.g. Dr. John Doe">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-2 px-1">Official Email</label>
                        <input type="email" name="email" required 
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-semibold" 
                               placeholder="name@mitcollege.edu">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-2 px-1">Password</label>
                            <input type="password" name="password" required 
                                   class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-semibold" 
                                   placeholder="••••••••">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-2 px-1">Confirm</label>
                            <input type="password" name="confirm_password" required 
                                   class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-semibold" 
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full py-5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-black rounded-2xl shadow-xl shadow-emerald-500/20 hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest text-sm">
                        Request Registration
                    </button>
                </form>

                <div class="mt-8 pt-8 border-t border-slate-100 text-center">
                    <p class="text-sm text-slate-500 font-medium">Already have an account? 
                        <a href="login.php" class="text-emerald-600 font-black uppercase tracking-tight ml-1">Login</a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
