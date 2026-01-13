<?php
session_start();
include './includes/db.php';

if(isset($_SESSION['admin_id'])){
    header("Location: index.php");
    exit();
}

if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if($admin && password_verify($password, $admin['password'])){
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        
        // Set flag for success modal
        $login_success = true;
        // Don't redirect immediately - let JavaScript handle it after showing modal
    } else {
        $error = "Invalid email or password!";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <?php if(isset($error)): ?>
    <div id="error-toast" class="fixed top-4 right-4 z-50 animate-fadeIn">
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl shadow-lg">
            <div class="flex items-center">
                <i class="bx bx-error-circle text-xl mr-3"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="h-screen w-full flex items-center justify-center relative overflow-hidden bg-white">
        <!-- Background elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-amber-50/30 rounded-full blur-[80px] -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-emerald-50/20 rounded-full blur-[100px] -ml-48 -mb-48"></div>

        <div class="w-full h-full min-h-screen flex items-center justify-center relative z-10">
            <div class="w-full flex flex-col">
                <div class="flex flex-col md:flex-row w-full">
                    <!-- Left side - College Info -->
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
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                                            <i class="bx bx-check text-emerald-600 text-lg"></i>
                                        </div>
                                        <span class="text-slate-700 font-medium">Secure Access</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center mr-3">
                                            <i class="bx bx-shield text-amber-600 text-lg"></i>
                                        </div>
                                        <span class="text-slate-700 font-medium">Admin Privileges</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right side - Login Form -->
                    <div class="w-full md:w-[55%] flex flex-col justify-center p-10 lg:p-24 relative overflow-hidden">
                        <div class="w-full max-w-md mx-auto">
                            <div class="mb-12 text-center md:text-left">
                             
                                
                                <h2 class="text-4xl  font-extrabold text-slate-900 mb-3 tracking-tight">
                                    <span class="bg-gradient-to-r from-amber-500 to-orange-600 bg-clip-text text-transparent">
                                        Admin Login
                                    </span>
                                </h2>
                                <p class="text-slate-600 font-medium text-lg">
                                    Access the
                                    <span class="font-bold bg-gradient-to-r from-amber-500 to-orange-600 bg-clip-text text-transparent">
                                        MIT College
                                    </span>
                                    management dashboard
                                </p>
                            </div>

                            <form method="POST" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2.5" for="email">
                                        Admin Email
                                    </label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-500 transition-colors">
                                            <i class="bx bx-envelope text-[22px] mt-1"></i>
                                        </div>
                                        <input id="email" name="email" type="email" required
                                            class="block w-full pl-11 pr-4 py-3.5 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all bg-white shadow-sm"
                                            placeholder="admin@mitcollege.edu" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2.5" for="password">
                                        Security Password
                                    </label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-500 transition-colors">
                                            <i class="bx bx-lock text-[22px] mb-0.5"></i>
                                        </div>
                                        <input id="password" name="password" type="password" required
                                            class="block w-full pl-11 pr-12 py-3.5 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all bg-white shadow-sm"
                                            placeholder="••••••••" />
                                        <span onclick="togglePassword('password')" 
                                              class="absolute right-4 top-1/2 transform -translate-y-1/2 cursor-pointer text-slate-400 hover:text-amber-500 transition-colors">
                                            👁️
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="remember" class="w-4 h-4 text-amber-600 border-slate-300 rounded focus:ring-amber-500">
                                        <label for="remember" class="ml-2 text-sm text-slate-600">Remember me</label>
                                    </div>
                                    <a href="./pages/forgot-password.php"
                                        class="text-sm font-semibold text-amber-600 hover:text-amber-700 transition-colors underline-offset-4 hover:underline">
                                        Forgot Password?
                                    </a>
                                </div>

                                <button name="login" type="submit"
                                    class="w-full bg-gradient-to-r from-amber-400 to-amber-600 hover:from-amber-500 hover:to-amber-700 text-white font-semibold py-3.5 rounded-2xl transition-all duration-300 shadow-lg shadow-amber-200 hover:shadow-xl hover:shadow-amber-300/50 active:scale-[0.98] flex items-center justify-center gap-3 text-lg">
                                    <i class="bx bx-log-in"></i>
                                    Sign In
                                </button>
                                
                                <p class="mt-6 text-slate-600 text-center text-sm">
                                    Don't have an account?
                                    <a href="register.php" class="text-amber-600 hover:text-amber-700 font-semibold ml-1 transition-colors duration-200">
                                        Register Here
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
    <?php if(isset($login_success) && $login_success): ?>
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
                
                <!-- Welcome Message -->
                <h3 class="text-2xl font-bold text-slate-900 mb-2">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h3>
                <p class="text-slate-600 mb-1">Login successful</p>
                <p class="text-sm text-slate-500 mb-6">Redirecting to admin dashboard...</p>
                
                <!-- Progress Bar -->
                <div class="mb-8">
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div id="redirect-progress" class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full" style="width: 0%; animation: progress 3s linear forwards;"></div>
                    </div>
                    <p class="text-sm text-slate-500 mt-2">Loading your dashboard...</p>
                </div>
                
                
                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button onclick="redirectToDashboard()" class="w-full bg-gradient-to-r from-emerald-400 to-emerald-600 text-white font-semibold py-3 rounded-2xl hover:shadow-lg transition-all">
                        Go to Dashboard Now
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
    // Toggle password visibility
    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
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

    function redirectToDashboard() {
        window.location.href = 'index.php';
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

    // Auto redirect after 3 seconds if success modal is shown
    <?php if(isset($login_success) && $login_success): ?>
    setTimeout(() => {
        window.location.href = 'index.php';
    }, 3000);
    <?php endif; ?>

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Auto hide error toast after 5 seconds
        const errorToast = document.getElementById('error-toast');
        if (errorToast) {
            setTimeout(() => {
                errorToast.style.opacity = '0';
                errorToast.style.transform = 'translateY(-10px)';
                setTimeout(() => errorToast.remove(), 300);
            }, 5000);
        }
        
        // If there's an error in PHP, show it as toast
        <?php if(isset($error)): ?>
        showErrorToast('<?php echo htmlspecialchars($error); ?>');
        <?php endif; ?>
    });
    </script>
</body>
</html>