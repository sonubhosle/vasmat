<?php
require_once __DIR__ . '/../includes/auth_helper.php';
$error   = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $confirm  = $_POST['confirm']       ?? '';
    if (empty($name) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "An account with this email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'admin', 'pending')");
            $ins->bind_param("sss", $name, $email, $hashed);
            if ($ins->execute()) {
                $success = "Registration submitted! Your account is pending Super Admin approval.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
$csrf_token = generateCSRF();
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Registration &mdash; <?= SITE_NAME ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
  .btn-register{
    background:linear-gradient(90deg,#10b981,#34d399);
    box-shadow:0 6px 20px rgba(16,185,129,.35);
    transition:all .25s ease;
  }
  .btn-register:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(16,185,129,.45);}
  .btn-register:active{transform:translateY(0);}

  @keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
  .fade-up{animation:fadeUp .55s ease both}
  /* icon turns green when input is focused */
  .relative:focus-within > span.input-icon { color: #10b981; transition: color .2s; }
  .input-icon { left: 1rem !important; }
  /* password popup */
  #pwdPopup{
    display:none;
    position:absolute;
    top:calc(100% + 8px);
    left:0; right:0;
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:12px 14px;
    box-shadow:0 8px 24px rgba(0,0,0,.10);
    z-index:50;
    animation: fadePopup .2s ease both;
  }
  #pwdPopup.show{ display:block; }
  @keyframes fadePopup{
    from{opacity:0;transform:translateY(-6px)}
    to  {opacity:1;transform:translateY(0)}
  }
  .req-item{ display:flex; align-items:center; gap:7px; font-size:.75rem; color:#9ca3af; margin-bottom:5px; transition:color .2s; }
  .req-item:last-child{ margin-bottom:0; }
  .req-item.ok{ color:#10b981; }
  .req-dot{ width:7px; height:7px; border-radius:50%; background:#d1d5db; flex-shrink:0; transition:background .2s; }
  .req-item.ok .req-dot{ background:#10b981; }

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
<body class="min-h-screen bg-white flex">
<div id="toast-container" class="toast-container"></div>

<div class="w-full min-h-screen flex flex-col md:flex-row">

  <!-- ══ LEFT: green blob + text ══ -->
  <div class="hidden md:flex md:w-[48%] lg:w-[52%] relative bg-white items-center justify-center overflow-hidden">

    <!-- Full green background -->
    <div class="absolute inset-0 bg-emerald-500"></div>

    <!-- Organic white wave mask on the right edge -->
    <svg class="absolute top-0 right-0 h-full" viewBox="0 0 200 750" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" style="width:100px">
      <path d="M200 0 C60 60, -30 200, 30 375 C90 550, -20 650, 200 750 L200 0 Z" fill="white"/>
    </svg>

    <!-- Decorative dot grid overlay -->
    <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle,rgba(255,255,255,0.5) 1px,transparent 1px);background-size:22px 22px;"></div>

 

    <!-- Text content overlay -->
    <div class="relative z-10 flex flex-col justify-between h-full px-12 py-14 w-full">

      <!-- Middle: headline + steps -->
      <div class="space-y-8">
        <!-- Badge -->
        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/25 px-3.5 py-1.5 text-xs font-semibold text-white uppercase tracking-widest">
          <span class="w-2 h-2 rounded-full bg-amber-300 animate-pulse inline-block"></span>
          Pending Admin Registration
        </span>

        <!-- Headline -->
        <div>
          <h1 class="text-white font-extrabold text-5xl leading-tight tracking-tight drop-shadow-sm">
            Join the admin
            <br><span class="text-emerald-100">Portal today.</span><br>
          </h1>
          <p class="mt-4 text-white/75 text-base leading-relaxed max-w-md">
            Request secure access to <?= SITE_NAME ?>. Your account will be reviewed by a Super Administrator before activation.
          </p>
        </div>


      </div>

      </div>
  </div>


  <!-- ══ RIGHT: register form ══ -->
  <div class="flex flex-1 items-center justify-center bg-white px-8 py-10 sm:px-14">
          <div class="mt-8 text-center absolute -top-3 right-3">
        <a href="../index.php" class="text-xs  border border-emerald-200 bg-emerald-50 rounded-full px-4 py-2 text-slate-700 hover:text-emerald-700 transition-colors font-semibold tracking-wider">
         Back to Website
        </a>
      </div>

    <div class="w-full max-w-sm fade-up">

      <!-- Avatar -->
      <div class="flex justify-center mb-4">
        <div class="w-18 h-18 w-[72px] h-[72px] rounded-full bg-emerald-50 border-4 border-emerald-200 flex items-center justify-center shadow-md">
          <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
            <circle cx="20" cy="14" r="9" fill="#10b981"/>
            <ellipse cx="20" cy="35" rx="14" ry="9" fill="#10b981" opacity=".6"/>
          </svg>
        </div>
      </div>

      <!-- Heading -->
      <h1 class="text-center font-extrabold text-2xl text-gray-800 tracking-widest uppercase mb-1">Create Account</h1>
      <p class="text-center text-gray-400 text-xs mb-6">Request administrator access &mdash; pending approval</p>

      <!-- Toast Triggers -->
      <?php if ($success): ?>
      <script>
        document.addEventListener('DOMContentLoaded', () => {
          showToast("<?= htmlspecialchars($success) ?>", 'success');
        });
      </script>
      <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-center">
        <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-3">
          <i class="fas fa-check text-emerald-600 text-lg"></i>
        </div>
        <p class="font-bold text-emerald-800 text-sm mb-1">Request Submitted!</p>
        <p class="text-emerald-700 text-xs leading-relaxed"><?= htmlspecialchars($success) ?></p>
        <a href="admin-login.php"
           class="btn-register mt-4 inline-block px-8 py-2.5 rounded-full text-white font-bold text-xs uppercase tracking-widest">
          Go to Login
        </a>
      </div>
      <?php endif; ?>

      <?php if ($error): ?>
      <script>
        document.addEventListener('DOMContentLoaded', () => {
          showToast("<?= htmlspecialchars($error) ?>", 'error');
        });
      </script>
      <?php endif; ?>

      <!-- Form -->
      <form method="POST" action="" novalidate class="space-y-5">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <!-- Full name -->
        <div class="relative">
          <span class="input-icon absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-user"></i></span>
          <input name="name" type="text" required autocomplete="name"
            placeholder="Full Name"
            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
            class="ul-input">
        </div>

        <!-- Email -->
        <div class="relative">
          <span class="input-icon absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-envelope"></i></span>
          <input name="email" type="email" required autocomplete="email"
            placeholder="Email Address"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            class="ul-input">
        </div>

        <!-- Password -->
        <div class="relative">
          <div class="relative" id="pwdWrap">
            <span class="input-icon absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-lock"></i></span>
            <input id="password" name="password" type="password" required autocomplete="new-password"
              placeholder="Password (min. 8 chars)"
              class="ul-input pr-10">
            <button type="button" id="togglePwd"
              class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-emerald-500 transition-colors text-sm">
              <i id="pwdIcon" class="fas fa-eye"></i>
            </button>
          </div>

          <!-- Requirements popup — inline below input -->
          <div id="pwdPopup">
            <p class="text-gray-500 font-semibold text-xs mb-2 uppercase tracking-wider">Password must have</p>
            <div class="req-item" id="req-len"><span class="req-dot"></span> At least 8 characters</div>
            <div class="req-item" id="req-upper"><span class="req-dot"></span> One uppercase letter</div>
            <div class="req-item" id="req-num"><span class="req-dot"></span> One number</div>
            <div class="req-item" id="req-special"><span class="req-dot"></span> One special character</div>
          </div>

        </div>

        <!-- Confirm password -->
        <div class="relative">
          <span class="input-icon absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-lock"></i></span>
          <input id="confirm" name="confirm" type="password" required autocomplete="new-password"
            placeholder="Confirm Password"
            class="ul-input pr-10">
          <span id="matchIcon" class="absolute right-4 top-1/2 -translate-y-1/2 text-sm hidden"></span>
        </div>

        <!-- Submit -->
        <button type="submit" id="submitBtn"
          class="btn-register w-full py-3 rounded-full text-white font-bold text-sm uppercase tracking-widest mt-2">
          Request Access
        </button>
      </form>
      <?php endif; ?>

      <!-- Login link -->
      <p class="mt-6 text-center text-xs text-gray-400">
        Already have an account?
        <a href="admin-login.php" class="text-emerald-600 font-semibold hover:underline ml-1">Sign In</a>
      </p>

    </div>
  </div>

</div>


<script>
  const pwdIn  = document.getElementById('password');
  const pwdIc  = document.getElementById('pwdIcon');
  const popup  = document.getElementById('pwdPopup');

  /* ── Password visibility toggle ── */
  document.getElementById('togglePwd').addEventListener('click', function(){
    pwdIn.type = pwdIn.type === 'password' ? 'text' : 'password';
    pwdIc.className = pwdIn.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
  });

  /* ── Show popup on focus, hide on blur ── */
  pwdIn.addEventListener('focus', () => popup.classList.add('show'));
  pwdIn.addEventListener('blur',  () => popup.classList.remove('show'));

  /* ── Live requirement + strength checker ── */
  pwdIn.addEventListener('input', function(){
    const v = this.value;

    // Individual requirement checks
    function check(id, condition){
      document.getElementById(id).classList.toggle('ok', condition);
    }
    check('req-len',     v.length >= 8);
    check('req-upper',   /[A-Z]/.test(v));
    check('req-num',     /[0-9]/.test(v));
    check('req-special', /[^A-Za-z0-9]/.test(v));

    // Strength bar removed
    checkMatch();
  });

  /* ── Confirm password match indicator ── */
  const confIn  = document.getElementById('confirm');
  const matchIc = document.getElementById('matchIcon');
  function checkMatch(){
    if(!confIn.value) return;
    const ok = pwdIn.value === confIn.value;
    matchIc.className = ok
      ? 'absolute right-4 top-1/2 -translate-y-1/2 text-sm fas fa-check text-emerald-500'
      : 'absolute right-4 top-1/2 -translate-y-1/2 text-sm fas fa-times text-red-400';
    matchIc.classList.remove('hidden');
  }
  confIn.addEventListener('input', checkMatch);

  /* ── Submit loading state ── */
  document.querySelector('form') && document.querySelector('form').addEventListener('submit', function(){
    const b = document.getElementById('submitBtn');
    if(b){ b.disabled=true; b.textContent='Submitting…'; b.style.opacity='.75'; }
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
