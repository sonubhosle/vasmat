<?php
require_once __DIR__ . '/../includes/auth_helper.php';

$error = '';
$success = '';
$active_tab = 'login'; // 'login' or 'register'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';
    $active_tab = $action;

    if ($action === 'login') {
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
                    header("Location: ../admin/index.php");
                    exit;
                }
            } else {
                $error = "Invalid admin credentials.";
            }
            $stmt->close();
        }
    } elseif ($action === 'register') {
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
                    $active_tab = 'login'; // Switch back to login on success
                } else {
                    $error = "Registration failed.";
                }
            }
        }
    }
}
$csrf_token = generateCSRF();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg-light: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --white: #ffffff;
            --glass: rgba(255, 255, 255, 0.8);
            --border: rgba(226, 232, 240, 0.8);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .main-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Left Panel - Branding & Info */
        .left-panel {
            flex: 1.2;
            position: relative;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            overflow: hidden;
        }

        .left-content {
            position: relative;
            z-index: 10;
            max-width: 600px;
        }

        .brand-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            border-radius: 100px;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 2rem;
        }

        .left-content h1 {
            font-size: 4rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: var(--text-dark);
            letter-spacing: -0.02em;
        }

        .left-content h1 span {
            color: var(--primary);
        }

        .left-content p {
            font-size: 1.25rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 3rem;
        }

        .feature-list {
            list-style: none;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 600;
            font-size: 1rem;
            color: var(--text-dark);
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background: var(--white);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Animated Blobs */
        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle at center, rgba(79, 70, 229, 0.15) 0%, rgba(79, 70, 229, 0) 70%);
            border-radius: 50%;
            filter: blur(60px);
            z-index: 1;
        }

        .blob-1 {
            top: -100px;
            left: -100px;
            animation: move-1 20s infinite alternate linear;
        }

        .blob-2 {
            bottom: -150px;
            right: -100px;
            animation: move-2 25s infinite alternate linear;
            background: radial-gradient(circle at center, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0) 70%);
        }

        @keyframes move-1 {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(100px, 150px) scale(1.2); }
        }

        @keyframes move-2 {
            from { transform: translate(0, 0) rotate(0deg); }
            to { transform: translate(-100px, -50px) rotate(180deg); }
        }

        /* Right Panel - Auth Forms */
        .right-panel {
            flex: 0.8;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem;
            position: relative;
        }

        .auth-card {
            width: 100%;
            max-width: 400px;
        }

        .auth-header {
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .auth-header h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .auth-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .tabs {
            display: flex;
            background: #f1f5f9;
            padding: 0.25rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .tab-btn {
            flex: 1;
            border: none;
            background: none;
            padding: 0.75rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-muted);
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            background: var(--white);
            color: var(--primary);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
        }

        .input-wrapper input {
            width: 100%;
            padding: 1rem 1.25rem 1rem 3.5rem;
            background: var(--bg-light);
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            color: var(--text-dark);
            transition: all 0.3s ease;
        }

        .input-wrapper input:focus {
            outline: none;
            background: var(--white);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 1.125rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2);
            margin-top: 1rem;
        }

        .submit-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.3);
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fee2e2;
        }

        .alert-success {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #dcfce7;
        }

        .footer-links {
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .left-panel {
                padding: 3rem;
            }
            .left-content h1 {
                font-size: 3rem;
            }
        }

        @media (max-width: 992px) {
            .main-container {
                flex-direction: column;
            }
            .left-panel {
                flex: none;
                padding: 4rem 2rem;
                min-height: 40vh;
            }
            .right-panel {
                flex: 1;
                padding: 3rem 1.5rem;
            }
            .blob {
                width: 300px;
                height: 300px;
            }
        }

        /* Animation utilities */
        .fade-in { animation: fadeIn 0.6s ease forwards; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hidden { display: none; }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Left Section: Info -->
        <section class="left-panel">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            
            <div class="left-content">
                <div class="brand-pill">Unified Admin Gateway</div>
                <h1>System <span>Management</span> Portal</h1>
                <p>Welcome back, Administrator. Access the powerful tools to manage and monitor your platform effectively.</p>
                
                <ul class="feature-list">
                    <li class="feature-item">
                        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                        User Analytics
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                        Security Controls
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon"><i class="fas fa-cogs"></i></div>
                        System Logs
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon"><i class="fas fa-database"></i></div>
                        Data Backups
                    </li>
                </ul>
            </div>
        </section>

        <!-- Right Section: Auth Forms -->
        <section class="right-panel">
            <div class="auth-card fade-in">
                <div class="auth-header">
                    <h2>Admin <span>Access</span></h2>
                    <p id="auth-subtitle">Welcome back! Please enter your details.</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= $success ?>
                    </div>
                <?php endif; ?>

                <div class="tabs">
                    <button type="button" class="tab-btn <?= $active_tab === 'login' ? 'active' : '' ?>" onclick="switchTab('login')">Login</button>
                    <button type="button" class="tab-btn <?= $active_tab === 'register' ? 'active' : '' ?>" onclick="switchTab('register')">Register</button>
                </div>

                <!-- Login Form -->
                <form id="login-form" action="" method="POST" class="<?= $active_tab === 'login' ? '' : 'hidden' ?>">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group">
                        <label>Admin Email</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" required placeholder="admin@example.com">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="footer-links" style="margin-bottom: 1.5rem; margin-top: 0;">
                        <a href="forgot-password.php?role=admin">Forgot password?</a>
                    </div>

                    <button type="submit" class="submit-btn">Login to Dashboard</button>
                </form>

                <!-- Register Form -->
                <form id="register-form" action="" method="POST" class="<?= $active_tab === 'register' ? '' : 'hidden' ?>">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="action" value="register">

                    <div class="form-group">
                        <label>Full Name</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" name="name" required placeholder="John Doe">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Work Email</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" required placeholder="admin@example.com">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" required placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Create Account</button>
                </form>

                <div class="footer-links" style="justify-content: center; margin-top: 3rem;">
                    <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Main Site</a>
                </div>
            </div>
        </section>
    </div>

    <script>
        function switchTab(tab) {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const tabBtns = document.querySelectorAll('.tab-btn');
            const subtitle = document.getElementById('auth-subtitle');

            tabBtns.forEach(btn => btn.classList.remove('active'));

            if (tab === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                tabBtns[0].classList.add('active');
                subtitle.innerText = "Welcome back! Please enter your details.";
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                tabBtns[1].classList.add('active');
                subtitle.innerText = "Join the admin team. Submit your details.";
            }

            // Sync hidden inputs if any (optional)
        }
    </script>
</body>
</html>
