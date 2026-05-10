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
                $error = "Incorrect password. Please try again.";
            }
        } else {
            $error = "No account found with these credentials.";
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
<title>SuperAdmin Login &mdash; <?= SITE_NAME ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>
tailwind.config = {
  theme: { extend: {
    fontFamily: { sans: ['Poppins','sans-serif'] },
    colors: {
      brand: {
        50: '#fffbeb', 100: '#fef3c7', 200: '#fde68a', 300: '#fcd34d',
        400: '#fbbf24', 500: '#f59e0b', 600: '#d97706', 700: '#b45309',
        800: '#92400e', 900: '#78350f', 950: '#451a03'
      }
    }
  }}
}
</script>
<style>
  *{font-family:'Poppins',sans-serif}
  html, body { height: 100%; overflow: hidden; }
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
    border-color: #fbbf24;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.1);
  }
  .ul-input::placeholder{color:#9ca3af;font-size:.85rem;}
  .btn-login{
    background: linear-gradient(90deg, #d97706, #fbbf24);
    box-shadow: 0 6px 20px rgba(217, 119, 6, 0.35);
    transition: all 0.25s ease;
  }
  .btn-login:hover{transform:translateY(-2px);box-shadow: 0 10px 28px rgba(217, 119, 6, 0.45);}
  .btn-login:active{transform:translateY(0);}
  @keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
  .fade-up{animation:fadeUp .55s ease both}
  /* icon turns gold when input is focused */
  .relative:focus-within > span.input-icon { color: #d97706; transition: color .2s; }
  .input-icon { left: 1rem !important; }
</style>
</head>
<body class="h-full bg-white overflow-hidden">

<div class="w-full h-full flex flex-col md:flex-row">

  <!-- ══ LEFT: slate/gold panel + text ══ -->
  <div class="hidden md:flex md:w-[48%] lg:w-[52%] relative bg-slate-950 flex-col justify-between overflow-hidden px-14 py-14">

    <!-- Organic white wave on the right edge -->
    <svg class="absolute top-0 right-0 h-full" viewBox="0 0 200 750" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" style="width:100px">
      <path d="M200 0 C60 60, -30 200, 30 375 C90 550, -20 650, 200 750 L200 0 Z" fill="white"/>
    </svg>

    <!-- Dot grid overlay -->
    <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle,rgba(251,191,36,0.5) 1px,transparent 1px);background-size:22px 22px;"></div>

    <!-- Middle: headline + feature list -->
    <div class="relative z-10 space-y-8">
      <!-- Badge -->
      <span class="inline-flex items-center gap-2 rounded-full bg-white/5 border border-white/10 px-3.5 py-1.5 text-xs font-semibold text-white uppercase tracking-widest">
        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse inline-block"></span>
        Master Root Authority
      </span>

      <!-- Headline -->
      <div>
        <h1 class="text-white font-extrabold text-5xl leading-tight tracking-tight drop-shadow-sm">
          System
           <br><span class="text-amber-500">Architecture.</span><br>
        </h1>
        <p class="mt-4 text-slate-400 text-base leading-relaxed max-w-md">
          Authorized access only. Full system control, user management, and security protocols are active.
        </p>
      </div>

    </div>
  </div>

  <!-- ══ RIGHT: login form ══ -->
  <div class="flex flex-1 items-center justify-center bg-white px-8 py-12 sm:px-14 relative">
    
    <!-- Back button -->
    <div class="mt-8 text-center absolute -top-3 right-3">
      <a href="../index.php" class="text-xs border border-brand-200 bg-brand-50 rounded-full px-4 py-2 text-slate-700 hover:text-brand-700 transition-colors font-semibold tracking-wider">
        Back to Website
      </a>
    </div>

    <div class="w-full max-w-sm fade-up">

      <!-- Avatar -->
      <div class="flex justify-center mb-5">
        <div class="w-20 h-20 rounded-full bg-slate-950 border-4 border-amber-500/20 flex items-center justify-center shadow-md">
          <svg width="46" height="46" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="23" cy="17" r="10" fill="#f59e0b"/>
            <ellipse cx="23" cy="40" rx="16" ry="10" fill="#f59e0b" opacity=".6"/>
          </svg>
        </div>
      </div>

      <!-- Heading -->
      <h1 class="text-center font-extrabold text-3xl text-gray-800 tracking-widest uppercase mb-8">MASTER LOGIN</h1>

      <!-- Error -->
      <?php if ($error): ?>
      <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700 font-medium">
        <i class="fas fa-circle-exclamation flex-shrink-0"></i><?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <!-- Form -->
      <form method="POST" action="" novalidate class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <!-- Email -->
        <div class="relative">
          <span class="input-icon absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-user-shield"></i></span>
          <input id="email" name="email" type="email" required autocomplete="email"
            placeholder="Master Email"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            class="ul-input">
        </div>

        <!-- Password -->
        <div class="relative">
          <span class="input-icon absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-key"></i></span>
          <input id="password" name="password" type="password" required autocomplete="current-password"
            placeholder="Master Key"
            class="ul-input pr-10">
          <button type="button" id="togglePwd" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-amber-600 transition-colors text-sm">
            <i id="pwdIcon" class="fas fa-eye"></i>
          </button>
        </div>

        <!-- Forgot -->
        <div class="text-right -mt-2">
          <a href="forgot-password.php?role=superadmin" class="text-xs text-gray-400 hover:text-amber-600 font-medium transition-colors">Forgot Master Key?</a>
        </div>

        <!-- Submit -->
        <button type="submit" id="submitBtn"
          class="btn-login w-full py-3 rounded-full text-white font-bold text-sm uppercase tracking-widest">
          AUTHORIZE LOGIN
        </button>
      </form>

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
    b.disabled = true; b.textContent = 'AUTHORIZING...'; b.style.opacity = '.75';
  });
</script>
</body>
</html>
