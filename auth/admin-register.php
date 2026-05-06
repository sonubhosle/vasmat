<?php
require_once __DIR__ . '/../includes/auth_helper.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'admin', 'pending')");
            $stmt_insert->bind_param("sss", $name, $email, $hashed);
            if ($stmt_insert->execute()) {
                $success = "Registration submitted! Please wait for Super Admin approval.";
            } else {
                $error = "Registration failed.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-blue-900 min-h-screen flex items-center justify-center p-6 bg-[url('https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=2070')] bg-cover bg-center">
    <div class="absolute inset-0 bg-slate-900/80"></div>
    <div class="w-full max-w-md relative z-10">
        <div class="bg-white p-10 rounded-[2.5rem] shadow-2xl">
            <h1 class="text-2xl font-black text-slate-900 uppercase text-center mb-8">Admin Application</h1>
            
            <?php if ($success): ?>
                <div class="bg-emerald-50 text-emerald-600 p-6 rounded-2xl text-center font-bold">
                    <?= $success ?>
                </div>
            <?php else: ?>
                <?php if ($error): ?><div class="bg-red-50 text-red-600 p-4 rounded-xl mb-4 text-xs font-bold"><?= $error ?></div><?php endif; ?>
                <form action="" method="POST" class="space-y-6">
                    <input type="text" name="name" required placeholder="Full Name" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl">
                    <input type="email" name="email" required placeholder="Organization Email" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl">
                    <input type="password" name="password" required placeholder="Password" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl">
                    <button type="submit" class="w-full py-5 bg-blue-600 text-white font-black rounded-2xl shadow-xl hover:bg-blue-700 transition-all uppercase tracking-widest text-sm">Submit Request</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
