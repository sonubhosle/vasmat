<?php
require_once __DIR__ . '/../includes/auth_helper.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role, status, reference_id FROM users WHERE email = ? AND role = 'faculty' LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'pending') {
                $error = "Your account is pending admin approval.";
            } elseif ($user['status'] === 'rejected') {
                $error = "Your registration request was rejected.";
            } elseif ($user['status'] !== 'active') {
                $error = "Your account is currently inactive.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['reference_id'] = $user['reference_id'];
                
                logActivity($conn, $user['id'], 'Faculty Login', 'Faculty logged in successfully');
                header("Location: ../faculty/dashboard.php");
                exit;
            }
        } else {
            $error = "Invalid faculty credentials.";
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
    <title>Faculty Login | <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #f59e0b;
            --primary-hover: #d97706;
            --bg-light: #fffcf5;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --white: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background-color: var(--bg-light); color: var(--text-dark); min-height: 100vh; overflow-x: hidden; }
        .main-container { display: flex; min-height: 100vh; width: 100%; }

        .left-panel {
            flex: 1.2;
            position: relative;
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            overflow: hidden;
        }

        .left-content { position: relative; z-index: 10; max-width: 600px; }
        .brand-pill { display: inline-flex; align-items: center; padding: 0.5rem 1rem; background: rgba(245, 158, 11, 0.1); color: var(--primary); border-radius: 100px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2rem; }
        .left-content h1 { font-size: 4rem; font-weight: 900; line-height: 1.1; margin-bottom: 1.5rem; color: var(--text-dark); letter-spacing: -0.02em; }
        .left-content h1 span { color: var(--primary); }
        .left-content p { font-size: 1.25rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 3rem; }

        .right-panel { flex: 0.8; background: var(--white); display: flex; align-items: center; justify-content: center; padding: 4rem; position: relative; }
        .auth-card { width: 100%; max-width: 400px; }
        .auth-header { margin-bottom: 2.5rem; text-align: center; }
        .auth-header h2 { font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; color: var(--text-dark); }
        .auth-header p { color: var(--text-muted); font-size: 0.95rem; }

        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 700; color: var(--text-dark); text-transform: uppercase; letter-spacing: 0.025em; }
        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1rem; }
        .input-wrapper input { width: 100%; padding: 1rem 1.25rem 1rem 3.5rem; background: #f8fafc; border: 2px solid transparent; border-radius: 12px; font-size: 1rem; font-weight: 500; color: var(--text-dark); transition: all 0.3s ease; }
        .input-wrapper input:focus { outline: none; background: var(--white); border-color: var(--primary); box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1); }

        .submit-btn { width: 100%; padding: 1.125rem; background: var(--primary); color: var(--white); border: none; border-radius: 12px; font-size: 1rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.2); margin-top: 1rem; }
        .submit-btn:hover { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(245, 158, 11, 0.3); }

        .alert { padding: 1rem 1.25rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; }
        .alert-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

        .footer-links { margin-top: 2rem; display: flex; justify-content: center; align-items: center; }
        .footer-links a { color: var(--text-muted); text-decoration: none; font-size: 0.875rem; font-weight: 600; transition: color 0.3s; }
        .footer-links a:hover { color: var(--primary); }

        .fade-in { animation: fadeIn 0.6s ease forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="main-container">
        <section class="left-panel">
            <div class="left-content">
                <div class="brand-pill">Faculty Knowledge Base</div>
                <h1>Teaching <span>Excellence</span> Portal</h1>
                <p>Welcome, Instructor. Access your personalized dashboard to manage courses, students, and academic records.</p>
            </div>
        </section>

        <section class="right-panel">
            <div class="auth-card fade-in">
                <div class="auth-header">
                    <h2>Faculty <span>Login</span></h2>
                    <p>Enter your credentials to access your teaching suite.</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
                <?php endif; ?>

                <form action="" method="POST" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="form-group">
                        <label>Faculty Email</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" required placeholder="faculty@college.edu">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex justify-end mb-6">
                        <a href="forgot-password.php?role=faculty" class="text-xs font-bold text-amber-600 hover:underline">Forgot password?</a>
                    </div>

                    <button type="submit" class="submit-btn">Sign In to Dashboard</button>
                </form>

                <div class="footer-links">
                    <p class="text-sm text-slate-500 font-medium">New to faculty? <a href="faculty-register.php" class="text-amber-600 font-bold hover:underline">Apply for Account</a></p>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
