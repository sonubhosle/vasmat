<?php
require_once __DIR__ . '/../includes/auth_helper.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                $error = "Your account is currently inactive.";
            } else {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role']      = $user['role'];
                logActivity($conn, $user['id'], 'Admin Login', 'Admin logged in successfully');
                header("Location: ../admin/index.php");
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
<html lang="en" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login &mdash; <?= SITE_NAME ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>
tailwind.config = {
  theme: { extend: {
    fontFamily: { sans: ['Poppins','sans-serif'] },
    colors: {
      emerald: {
        50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',
        400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b'
      }
    }
  }}
}
</script>
<style>
  *{font-family:'Poppins',sans-serif}
  .ul-input{
    border: 1.5px solid #e5e7eb;
    border-radius: 9999px;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    width: 100%;
    font-size: 0.9rem;
    color: #1f2937;
    outline: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .ul-input:focus{
    border-color: #10b981;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
  }
  .ul-input::placeholder{color:#9ca3af;font-size:.85rem;}
  .btn-login{
    background:linear-gradient(90deg,#10b981,#34d399);
    box-shadow:0 6px 20px rgba(16,185,129,.35);
    transition:all .25s ease;
  }
  .btn-login:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(16,185,129,.45);}
  .btn-login:active{transform:translateY(0);}
  @keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
  .fade-up{animation:fadeUp .55s ease both}
  /* icon turns green when input is focused */
  .relative:focus-within > span.input-icon { color: #10b981; transition: color .2s; }
  .input-icon { left: 1rem !important; }

  /* Toast Notifications */
  .toast-container {
    position: fixed;
    top: 2rem;
    right: 2rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    pointer-events: none;
  }
  .toast-item {
    pointer-events: auto;
    min-width: 300px;
    max-width: 450px;
    background: white;
    padding: 1.25rem;
    border-radius: 1.5rem;
    box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    gap: 1rem;
    transform: translateX(120%);
    transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    opacity: 0;
  }
  .toast-item.show {
    transform: translateX(0);
    opacity: 1;
  }
  .toast-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
  }
  .toast-success .toast-icon { background: #ecfdf5; color: #10b981; }
  .toast-error .toast-icon { background: #fef2f2; color: #ef4444; }
</style>
</head>
<body class="h-full bg-white flex items-center justify-center min-h-screen">
<div id="toast-container" class="toast-container"></div>

<div class="w-full min-h-screen flex flex-col md:flex-row">

  <!-- ══ LEFT: green panel + text ══ -->
  <div class="hidden md:flex md:w-[48%] lg:w-[52%] relative bg-emerald-500 flex-col justify-between overflow-hidden px-14 py-14">

    <!-- Organic white wave on the right edge -->
    <svg class="absolute top-0 right-0 h-full" viewBox="0 0 200 750" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" style="width:100px">
      <path d="M200 0 C60 60, -30 200, 30 375 C90 550, -20 650, 200 750 L200 0 Z" fill="white"/>
    </svg>

    <!-- Dot grid overlay -->
    <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle,rgba(255,255,255,0.5) 1px,transparent 1px);background-size:22px 22px;"></div>

   


    <!-- Middle: headline + feature list -->
    <div class="relative z-10 space-y-8">
      <!-- Badge -->
      <span class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/25 px-3.5 py-1.5 text-xs font-semibold text-white uppercase tracking-widest">
        <span class="w-2 h-2 rounded-full bg-emerald-200 animate-pulse inline-block"></span>
        Secure Admin Portal
      </span>

      <!-- Headline -->
      <div>
        <h1 class="text-white font-extrabold text-5xl leading-tight tracking-tight drop-shadow-sm">
          Login to your 
           <br><span class="text-emerald-100">Dashboard.</span><br>
        </h1>
        <p class="mt-4 text-white/75 text-base leading-relaxed max-w-md">
          Access the central control panel for <?= SITE_NAME ?>. Manage users, content, and compliance tools securely.
        </p>
      </div>

    </div>
  </div>

  <!-- ══ RIGHT: login form ══ -->
  <div class="flex flex-1 items-center justify-center bg-white px-8 py-12 sm:px-14">
       <div class="mt-8 text-center absolute -top-3 right-3">
        <a href="../index.php" class="text-xs  border border-emerald-200 bg-emerald-50 rounded-full px-4 py-2 text-slate-700 hover:text-emerald-700 transition-colors font-semibold tracking-wider">
         Back to Website
        </a>
      </div>
    <div class="w-full max-w-sm fade-up">

      <!-- Avatar -->
      <div class="flex justify-center mb-5">
        <div class="w-20 h-20 rounded-full bg-emerald-50 border-4 border-emerald-200 flex items-center justify-center shadow-md">
          <svg width="46" height="46" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="23" cy="17" r="10" fill="#10b981"/>
            <ellipse cx="23" cy="40" rx="16" ry="10" fill="#10b981" opacity=".6"/>
          </svg>
        </div>
      </div>

      <!-- Heading -->
      <h1 class="text-center font-extrabold text-3xl text-gray-800 tracking-widest uppercase mb-8">WELCOME</h1>

      <!-- Toast Trigger -->
      <?php if ($error): ?>
      <script>
        document.addEventListener('DOMContentLoaded', () => {
          showToast("<?= htmlspecialchars($error) ?>", 'error');
        });
      </script>
      <?php endif; ?>

      <!-- Form -->
      <form method="POST" action="" novalidate class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <!-- Email -->
        <div class="relative">
          <span class="input-icon absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-user"></i></span>
          <input id="email" name="email" type="email" required autocomplete="email"
            placeholder="Username / Email"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            class="ul-input">
        </div>

        <!-- Password -->
        <div class="relative">
          <span class="input-icon absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-lock"></i></span>
          <input id="password" name="password" type="password" required autocomplete="current-password"
            placeholder="Password"
            class="ul-input pr-10">
          <button type="button" id="togglePwd" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-emerald-600 transition-colors text-sm">
            <i id="pwdIcon" class="fas fa-eye"></i>
          </button>
        </div>

        <!-- Forgot -->
        <div class="text-right -mt-2">
          <a href="forgot-password.php?role=admin" class="text-xs text-gray-400 hover:text-emerald-600 font-medium transition-colors">Forgot Password?</a>
        </div>

        <!-- Submit -->
        <button type="submit" id="submitBtn"
          class="btn-login w-full py-3 rounded-full text-white font-bold text-sm uppercase tracking-widest">
          Login
        </button>
      </form>

      <!-- Register link -->
      <p class="mt-8 text-center text-xs text-gray-400">
        Need an account?
        <a href="admin-register.php" class="text-emerald-600 font-semibold hover:underline ml-1">Register</a>
      </p>


    </div>
  </div>

</div>

<script>
  // Password toggle
  document.getElementById('togglePwd').addEventListener('click', function(){
    const p = document.getElementById('password'), i = document.getElementById('pwdIcon');
    p.type = p.type === 'password' ? 'text' : 'password';
    i.className = p.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
  });
  // Submit loading state
  document.querySelector('form').addEventListener('submit', function(){
    const b = document.getElementById('submitBtn');
    b.disabled = true; b.textContent = 'Signing in…'; b.style.opacity = '.75';
  });

  function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast-item toast-${type}`;
    const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle' };
    toast.innerHTML = `
      <div class="toast-icon"><i class="fas ${icons[type] || 'fa-info-circle'}"></i></div>
      <div class="flex-1">
        <p class="text-xs font-black text-slate-900 uppercase tracking-widest mb-0.5">${type}</p>
        <p class="text-[11px] font-bold text-slate-500 leading-snug">${message}</p>
      </div>
    `;
    container.appendChild(toast);
    toast.offsetHeight;
    toast.classList.add('show');
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 600);
    }, 4000);
  }
</script>
</body>
</html>
