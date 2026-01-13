<?php
session_start();
include 'includes/db.php';

if(isset($_POST['register'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(strlen($password) < 6){
        $error = "Password must be at least 6 characters!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM admins WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if($result->num_rows > 0){
            $error = "Email already exists!";
            $check->close();
        } else {
            $check->close();
            
            $stmt = $conn->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed);
            
            if($stmt->execute()){
                $registration_success = true;
                $success_message = "Registration successful! Redirecting to login...";
            } else {
                $error = "Registration failed. Please try again.";
            }
            
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @keyframes checkmark {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes progress {
            0% { width: 0%; }
            100% { width: 100%; }
        }
    </style>
</head>
<body>
    <?php if(isset($error)): ?>
    <div id="error-toast" class="fixed top-4 right-4 z-50">
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl shadow-lg animate-fadeIn">
            <div class="flex items-center">
                <i class="bx bx-error-circle text-xl mr-3"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="h-screen w-full flex items-center justify-center relative overflow-hidden bg-white">
        <div class="absolute top-0 right-0 w-64 h-64 bg-amber-50/30 rounded-full blur-[80px] -mr-32 -mt-32"></div>

        <div class="w-full h-full min-h-screen flex items-center justify-center relative z-10">
            <div class="w-full flex flex-col md:row">
                <div class="flex flex-col md:flex-row w-full">
                    <div class="w-full md:w-[45%] relative text-white flex flex-col justify-center p-10 lg:p-20 overflow-hidden">
                        <div class="relative z-10">
                            <div class="mb-10">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-14 h-14 text-3xl font-extrabold bg-gradient-to-br from-amber-400 to-amber-600 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30 shadow-inner">
                                        M
                                    </div>
                                    <div class="h-px flex-grow bg-white/20"></div>
                                </div>

                                <h3 class="text-xl md:text-2xl font-semibold text-slate-700 tracking-wide mb-2">Yuvak Pratishthan's</h3>
                                <h1 class="text-4xl text-slate-700 font-black mb-6 tracking-tighter leading-[0.9] drop-shadow-2xl">
                                    MIT <span class="bg-gradient-to-r from-amber-400 to-amber-600 bg-clip-text text-transparent">
                                        COLLEGE
                                    </span>
                                </h1>
                                <div class="h-1.5 w-24 bg-gradient-to-r from-amber-400 to-amber-600 rounded-full mb-10"></div>
                            </div>

                            <div class="space-y-10 max-w-md">
                                <p class="text-sm md:text-xl text-slate-600 leading-relaxed">
                                    The administrative gateway for campus operations, faculty management, and student success tracking.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full md:w-[55%] flex flex-col justify-center p-10 lg:p-24 relative overflow-hidden">
                        <div class="w-full max-w-md mx-auto">
                            <div class="mb-6 text-center md:text-left">
                                <h2 class="text-4xl font-extrabold text-slate-900 mb-3 tracking-tight">
                                    <span class="bg-gradient-to-r from-amber-500 to-orange-600 bg-clip-text text-transparent">
                                        Admin Registration
                                    </span>
                                </h2>
                                <p class="text-slate-600 font-medium text-lg">
                                    Join the
                                    <span class="font-bold bg-gradient-to-r from-amber-500 to-orange-600 bg-clip-text text-transparent">
                                        MIT College
                                    </span>
                                    management dashboard
                                </p>
                            </div>

                            <form method="POST" onsubmit="return validatePassword()" class="space-y-4">
                                <!-- Name and Email side by side -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Name -->
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2.5" for="name">
                                            Name
                                        </label>
                                        <div class="relative group">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-500 transition-colors">
                                                <i class="bx bx-user text-[22px] mt-1"></i>
                                            </div>
                                            <input id="name" name="name" type="text" required
                                                class="block w-full pl-11 pr-4 py-3 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all bg-white shadow-sm"
                                                placeholder="Admin Name" />
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2.5" for="email">
                                            Admin Email
                                        </label>
                                        <div class="relative group">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-500 transition-colors">
                                                <i class="bx bx-envelope text-[22px] mt-1"></i>
                                            </div>
                                            <input id="email" name="email" type="email" required
                                                class="block w-full pl-11 pr-4 py-3 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all bg-white shadow-sm"
                                                placeholder="admin@mitcollege.edu" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Password full width -->
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2.5" for="password">
                                        Security Password
                                    </label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-500 transition-colors">
                                            <i class="bx bx-lock text-[22px] mb-0.5"></i>
                                        </div>
                                        <input id="password" name="password" type="password" required
                                            class="block w-full pl-11 pr-12 py-3 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all bg-white shadow-sm"
                                            placeholder="Password (min. 6 characters)" oninput="checkPasswordStrength()" />
                                        <span onclick="togglePassword('password')" 
                                              class="absolute right-4 top-1/2 transform -translate-y-1/2 cursor-pointer text-slate-400 hover:text-amber-500 transition-colors">
                                            👁️
                                        </span>
                                    </div>
                                    
                                    <!-- Password Strength Meter -->
                                    <div class="mt-4">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span id="strength-text" class="font-medium text-slate-600">Password strength</span>
                                            <span id="password-length" class="font-semibold text-slate-700">0/12</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div id="strength-bar" class="h-full rounded-full transition-all duration-300" style="width: 0%;"></div>
                                        </div>
                                        
                                        <div id="password-hints" class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-2">
                                            <div id="length-hint" class="flex items-center text-xs">
                                                <div class="w-2 h-2 rounded-full bg-slate-300 mr-2"></div>
                                                <span class="text-slate-500">At least 6 characters</span>
                                            </div>
                                            <div id="uppercase-hint" class="flex items-center text-xs">
                                                <div class="w-2 h-2 rounded-full bg-slate-300 mr-2"></div>
                                                <span class="text-slate-500">One uppercase letter</span>
                                            </div>
                                            <div id="number-hint" class="flex items-center text-xs">
                                                <div class="w-2 h-2 rounded-full bg-slate-300 mr-2"></div>
                                                <span class="text-slate-500">One number</span>
                                            </div>
                                            <div id="special-hint" class="flex items-center text-xs">
                                                <div class="w-2 h-2 rounded-full bg-slate-300 mr-2"></div>
                                                <span class="text-slate-500">One special character</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4">
                                    <button name="register" type="submit"
                                        class="w-full bg-gradient-to-r from-amber-400 to-amber-600 hover:from-amber-500 hover:to-amber-700 text-white font-semibold py-3.5 rounded-2xl transition-all duration-300 shadow-lg shadow-amber-200 hover:shadow-xl hover:shadow-amber-300/50 active:scale-[0.98] flex items-center justify-center gap-3 text-lg">
                                        <i class="bx bx-user-plus"></i>
                                        Create Account
                                    </button>
                                </div>

                                <!-- Login Link -->
                                <p class="mt-6 text-slate-600 text-center text-sm">
                                    Already have an account?
                                    <a href="login.php" class="text-amber-600 hover:text-amber-700 font-semibold ml-1 transition-colors duration-200">
                                        Login Here
                                    </a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <?php if(isset($registration_success) && $registration_success): ?>
    <div id="success-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm animate-fadeIn">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
            <div class="p-8 text-center">
                <!-- Success Icon -->
                <div class="relative mb-6">
                    <div class="w-24 h-24 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-full mx-auto flex items-center justify-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center">
                            <i class="bx bx-check text-white text-4xl" style="animation: checkmark 0.6s ease-out;"></i>
                        </div>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-28 h-28 border-4 border-emerald-200 border-t-emerald-500 rounded-full animate-spin"></div>
                    </div>
                </div>
                
                <!-- Success Message -->
                <h3 class="text-2xl font-bold text-slate-900 mb-3">Registration Successful!</h3>
                <p class="text-slate-600 mb-6">Your admin account has been created successfully.</p>
                
                <!-- Progress Bar -->
                <div class="mb-8">
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div id="redirect-progress" class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full" style="width: 0%; animation: progress 3s linear forwards;"></div>
                    </div>
                    <p class="text-sm text-slate-500 mt-2">Redirecting to login page...</p>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button onclick="redirectNow()" class="w-full bg-gradient-to-r from-emerald-400 to-emerald-600 text-white font-semibold py-3 rounded-2xl hover:shadow-lg transition-all">
                        Go to Login Now
                    </button>
                    <button onclick="closeModal()" class="w-full border border-slate-200 text-slate-600 font-medium py-3 rounded-2xl hover:bg-slate-50 transition-all">
                        Stay Here
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
    // Password Strength Functions
    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function checkPasswordStrength() {
        const password = document.getElementById('password').value;
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');
        const passwordLength = document.getElementById('password-length');

        // Update length counter
        passwordLength.textContent = password.length + '/12';

        // Check requirements
        const hasMinLength = password.length >= 6;
        const hasUppercase = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

        // Update hints with colors and icons
        updateHint('length-hint', hasMinLength);
        updateHint('uppercase-hint', hasUppercase);
        updateHint('number-hint', hasNumber);
        updateHint('special-hint', hasSpecial);

        // Calculate score
        let score = 0;
        if (password.length >= 6) score++;
        if (password.length >= 8) score++;
        if (hasUppercase) score++;
        if (hasNumber) score++;
        if (hasSpecial) score++;

        // Set strength level
        let width = 0;
        let color = '#d1d5db';
        let text = 'Very Weak';

        if (password.length === 0) {
            width = 0;
            text = 'Password strength';
        } else if (score === 1) {
            width = 20;
            color = '#ef4444'; // red
            text = 'Very Weak';
        } else if (score === 2) {
            width = 40;
            color = '#f97316'; // orange
            text = 'Weak';
        } else if (score === 3) {
            width = 60;
            color = '#eab308'; // yellow
            text = 'Fair';
        } else if (score === 4) {
            width = 80;
            color = '#3b82f6'; // blue
            text = 'Good';
        } else if (score >= 5) {
            width = 100;
            color = '#10b981'; // green
            text = 'Strong';
        }

        // Update UI
        strengthBar.style.width = width + '%';
        strengthBar.style.backgroundColor = color;
        strengthText.textContent = text;
        strengthText.style.color = color;

        return score;
    }

    function updateHint(elementId, isValid) {
        const element = document.getElementById(elementId);
        const dot = element.querySelector('div');
        const text = element.querySelector('span');
        
        if (dot && text) {
            if (isValid) {
                dot.style.backgroundColor = '#10b981';
                dot.style.transform = 'scale(1.2)';
                text.style.color = '#10b981';
                text.style.fontWeight = '600';
            } else {
                dot.style.backgroundColor = '#cbd5e1';
                dot.style.transform = 'scale(1)';
                text.style.color = '#64748b';
                text.style.fontWeight = '400';
            }
        }
    }

    function validatePassword() {
        const password = document.getElementById('password').value;
        
        if (password.length < 6) {
            showErrorToast('Password must be at least 6 characters long!');
            return false;
        }
        
        return true;
    }

    // Success Modal Functions
    function closeModal() {
        const modal = document.getElementById('success-modal');
        if (modal) {
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.95)';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    }

    function redirectNow() {
        window.location.href = 'login.php';
    }

    function showErrorToast(message) {
        // Remove existing error toast
        const existingToast = document.getElementById('error-toast');
        if (existingToast) existingToast.remove();
        
        // Create new toast
        const toast = document.createElement('div');
        toast.id = 'error-toast';
        toast.className = 'fixed top-4 right-4 z-50 animate-fadeIn';
        toast.innerHTML = `
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl shadow-lg">
                <div class="flex items-center">
                    <i class="bx bx-error-circle text-xl mr-3"></i>
                    <span>${message}</span>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px)';
                setTimeout(() => toast.remove(), 300);
            }
        }, 5000);
    }

    // Auto redirect after 5 seconds if success modal is shown
    <?php if(isset($registration_success) && $registration_success): ?>
    setTimeout(() => {
        window.location.href = 'login.php';
    }, 5000);
    <?php endif; ?>

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        checkPasswordStrength();
        
        // Auto hide error toast after 5 seconds
        const errorToast = document.getElementById('error-toast');
        if (errorToast) {
            setTimeout(() => {
                errorToast.style.opacity = '0';
                errorToast.style.transform = 'translateY(-10px)';
                setTimeout(() => errorToast.remove(), 300);
            }, 5000);
        }
    });
    </script>
</body>
</html>