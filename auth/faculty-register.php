<?php
require_once __DIR__ . '/../includes/auth_helper.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $type = $_POST['faculty_type'] ?? 'teaching';

    if (empty($name) || empty($email) || empty($password) || empty($type)) {
        $error = "Please fill in all fields.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "This email is already registered.";
        } else {
            // Start Transaction
            $conn->begin_transaction();
            try {
                // 1. Create Faculty Record
                $stmt_fac = $conn->prepare("INSERT INTO faculty (name, email, faculty_type, status) VALUES (?, ?, ?, 'pending')");
                $stmt_fac->bind_param("sss", $name, $email, $type);
                $stmt_fac->execute();
                $faculty_id = $conn->insert_id;

                // 2. Create User Record
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt_usr = $conn->prepare("INSERT INTO users (name, email, password, role, status, reference_id) VALUES (?, ?, ?, 'faculty', 'pending', ?)");
                $stmt_usr->bind_param("sssi", $name, $email, $hashed, $faculty_id);
                $stmt_usr->execute();

                $conn->commit();
                $success = "Registration successful! Your application as " . ($type == 'teaching' ? 'Teaching' : 'Non-Teaching') . " staff is now awaiting admin approval.";
                
                // Log Activity
                logActivity($conn, 0, 'Faculty Registration', "New registration: $name ($type)");
                
                // Notify Admins
                $admins = $conn->query("SELECT id FROM users WHERE role IN ('admin', 'superadmin')");
                while($adm = $admins->fetch_assoc()) {
                    addNotification($conn, $adm['id'], 'New Faculty Registration', "$name has applied for a $type position.");
                }

            } catch (Exception $e) {
                $conn->rollback();
                $error = "Registration failed: " . $e->getMessage();
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
<title>Faculty Registration &mdash; <?= SITE_NAME ?></title>
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
        50:'#f5f3ff',100:'#ede9fe',200:'#ddd6fe',300:'#c4b5fd',
        400:'#a78bfa',500:'#8b5cf6',600:'#7c3aed',700:'#6d28d9',800:'#5b21b6',900:'#4c1d95'
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
    appearance: none;
  }
  .ul-input:focus{
    border-color: #8b5cf6;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
  }
  .ul-input::placeholder{color:#9ca3af;font-size:.85rem;}
  .btn-register{
    background:linear-gradient(90deg,#8b5cf6,#a78bfa);
    box-shadow:0 6px 20px rgba(139, 92, 246, 0.35);
    transition:all .25s ease;
  }
  .btn-register:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(139, 92, 246, 0.45);}
  .btn-register:active{transform:translateY(0);}
  @keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
  .fade-up{animation:fadeUp .55s ease both}
  /* icon turns violet when input is focused */
  .relative:focus-within > span.input-icon { color: #8b5cf6; transition: color .2s; }
  .input-icon { left: 1rem !important; }
  /* Custom Dropdown */
  .custom-select-container { position: relative; width: 100%; }
  .custom-select-trigger {
    display: flex; align-items: center; justify-content: space-between;
    cursor: pointer; transition: all 0.3s ease;
  }
  .custom-options {
    position: absolute; top: calc(100% + 8px); left: 0; right: 0;
    background: #fff; border: 1px solid #e5e7eb; border-radius: 0.75rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    opacity: 0; visibility: hidden; transform: translateY(-10px) scale(0.95);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 100; overflow: hidden;
  }
  .custom-options.show {
    opacity: 1; visibility: visible; transform: translateY(0) scale(1);
  }
  .custom-option {
    padding: 0.75rem 1.25rem; font-size: 0.85rem; color: #4b5563;
    transition: all 0.2s; cursor: pointer; border-bottom: 1px solid #f3f4f6;
  }
  .custom-option:last-child { border-bottom: none; }
  .custom-option:hover { background: #f5f3ff; color: #7c3aed; padding-left: 1.5rem; }
  .custom-option.selected { background: #f3f4f6; color: #111827; font-weight: 600; }

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
  .toast-success .toast-icon { background: #f5f3ff; color: #8b5cf6; }
  .toast-error .toast-icon { background: #fef2f2; color: #ef4444; }
</style>
</head>
<body class="h-full bg-white overflow-hidden">
<div id="toast-container" class="toast-container"></div>

<div class="w-full h-full flex flex-col md:flex-row">

  <!-- ══ LEFT: blue panel + text ══ -->
  <div class="hidden md:flex md:w-[48%] lg:w-[52%] relative bg-brand-600 flex-col justify-between overflow-hidden px-14 py-14">

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
        <span class="w-2 h-2 rounded-full bg-violet-200 animate-pulse inline-block"></span>
        Join Our Academic Community
      </span>

      <!-- Headline -->
      <div>
        <h1 class="text-white font-extrabold text-5xl leading-tight tracking-tight drop-shadow-sm">
          Faculty
           <br><span class="text-violet-100">Application.</span><br>
        </h1>
        <p class="mt-4 text-white/75 text-base leading-relaxed max-w-md">
          Register to join our esteemed faculty. Your application will be reviewed by the administration for verification.
        </p>
      </div>

    </div>
  </div>

  <!-- ══ RIGHT: register form ══ -->
  <div class="flex flex-1 items-center justify-center bg-white px-8 py-10 sm:px-14 relative">
    
    <!-- Back button -->
    <div class="mt-8 text-center absolute -top-3 right-3">
      <a href="../index.php" class="text-xs border border-brand-200 bg-brand-50 rounded-full px-4 py-2 text-slate-700 hover:text-brand-700 transition-colors font-semibold tracking-wider">
        Back to Website
      </a>
    </div>

    <div class="w-full max-w-sm fade-up">

      <!-- Avatar -->
      <div class="flex justify-center mb-5">
        <div class="w-20 h-20 rounded-full bg-violet-50 border-4 border-violet-200 flex items-center justify-center shadow-md">
          <svg width="46" height="46" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="23" cy="17" r="10" fill="#8b5cf6"/>
            <ellipse cx="23" cy="40" rx="16" ry="10" fill="#8b5cf6" opacity=".6"/>
          </svg>
        </div>
      </div>

      <!-- Heading -->
      <h1 class="text-center font-extrabold text-3xl text-gray-800 tracking-widest uppercase mb-1">REGISTER</h1>
      <p class="text-center text-gray-400 text-xs mb-8 uppercase tracking-widest">Faculty Enrollment</p>

      <!-- Toast Triggers -->
      <?php if ($success): ?>
      <script>
        document.addEventListener('DOMContentLoaded', () => {
          showToast("<?= htmlspecialchars($success) ?>", 'success');
        });
      </script>
      <div class="mb-5 rounded-xl bg-blue-50 border border-blue-200 px-5 py-6 text-center">
        <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-check text-blue-600 text-2xl"></i>
        </div>
        <p class="font-bold text-blue-800 text-sm mb-2">Application Received!</p>
        <p class="text-blue-700 text-xs leading-relaxed"><?= htmlspecialchars($success) ?></p>
        <a href="faculty-login.php"
           class="btn-register mt-6 inline-block px-10 py-3 rounded-full text-white font-bold text-xs uppercase tracking-widest">
          Go to Login
        </a>
      </div>
      <?php else: ?>

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

        <!-- Faculty Type -->
        <div class="relative">
          <span class="input-icon absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 text-sm z-10"><i class="fas fa-briefcase"></i></span>
          
          <div class="custom-select-container" id="facultyTypeContainer">
            <input type="hidden" name="faculty_type" id="faculty_type_input" value="<?= htmlspecialchars($_POST['faculty_type'] ?? '') ?>">
            
            <div class="ul-input custom-select-trigger" id="selectTrigger">
              <span id="triggerText"><?= (($_POST['faculty_type'] ?? '') == 'non-teaching') ? 'Non-Teaching Staff' : ((($_POST['faculty_type'] ?? '') == 'teaching') ? 'Teaching Staff' : 'Select Faculty Type') ?></span>
              <i class="fas fa-chevron-down text-xs transition-transform duration-300" id="arrowIcon"></i>
            </div>

            <div class="custom-options" id="selectOptions">
              <div class="custom-option" data-value="teaching">Teaching Staff</div>
              <div class="custom-option" data-value="non-teaching">Non-Teaching Staff</div>
            </div>
          </div>
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
          <span class="input-icon absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-lock"></i></span>
          <input id="password" name="password" type="password" required autocomplete="new-password"
            placeholder="Password (min. 8 chars)"
            class="ul-input pr-10">
          <button type="button" id="togglePwd"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-violet-500 transition-colors text-sm">
            <i id="pwdIcon" class="fas fa-eye"></i>
          </button>
        </div>

        <!-- Submit -->
        <button type="submit" id="submitBtn"
          class="btn-register w-full py-3 rounded-full text-white font-bold text-sm uppercase tracking-widest mt-2">
          Request Access
        </button>
      </form>
      <?php endif; ?>

      <!-- Login link -->
      <p class="mt-8 text-center text-xs text-gray-400">
        Already have an account?
        <a href="faculty-login.php" class="text-violet-600 font-semibold hover:underline ml-1">Sign In</a>
      </p>

    </div>
  </div>

</div>

<script>
  // Password toggle
  if(document.getElementById('togglePwd')){
    document.getElementById('togglePwd').addEventListener('click', function(){
      const p = document.getElementById('password'), i = document.getElementById('pwdIcon');
      p.type = p.type === 'password' ? 'text' : 'password';
      i.className = p.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    });
  }
  // Custom Dropdown Logic
  const container = document.getElementById('facultyTypeContainer');
  const trigger = document.getElementById('selectTrigger');
  const options = document.getElementById('selectOptions');
  const input = document.getElementById('faculty_type_input');
  const triggerText = document.getElementById('triggerText');
  const arrow = document.getElementById('arrowIcon');

  if(trigger) {
    trigger.addEventListener('click', () => {
      options.classList.toggle('show');
      arrow.style.transform = options.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
      trigger.style.borderColor = options.classList.contains('show') ? '#8b5cf6' : '#e5e7eb';
      trigger.style.boxShadow = options.classList.contains('show') ? '0 0 0 4px rgba(139, 92, 246, 0.1)' : 'none';
    });

    document.querySelectorAll('.custom-option').forEach(opt => {
      opt.addEventListener('click', function() {
        const val = this.getAttribute('data-value');
        const text = this.textContent;
        
        input.value = val;
        triggerText.textContent = text;
        triggerText.classList.remove('text-gray-400');
        
        options.classList.remove('show');
        arrow.style.transform = 'rotate(0deg)';
        trigger.style.borderColor = '#e5e7eb';
        trigger.style.boxShadow = 'none';

        document.querySelectorAll('.custom-option').forEach(o => o.classList.remove('selected'));
        this.classList.add('selected');
      });
    });

    // Close on outside click
    window.addEventListener('click', (e) => {
      if (!container.contains(e.target)) {
        options.classList.remove('show');
        arrow.style.transform = 'rotate(0deg)';
        trigger.style.borderColor = '#e5e7eb';
        trigger.style.boxShadow = 'none';
      }
    });
  }

  // Submit loading state
  if(document.querySelector('form')){
    document.querySelector('form').addEventListener('submit', function(){
      const b = document.getElementById('submitBtn');
      if(b){ b.disabled=true; b.textContent='Submitting…'; b.style.opacity='.75'; }
    });
  }

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
