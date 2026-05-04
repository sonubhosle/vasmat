<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);

$success = '';
$error = '';

// Handle Faculty Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_faculty'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = 'faculty';

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $conn->begin_transaction();
        try {
            // 1. Insert into faculty table (minimal info for now, can be expanded)
            $stmt = $conn->prepare("INSERT INTO faculty (name, designation, email, faculty_type) VALUES (?, ?, ?, 'teaching')");
            $stmt->bind_param("sss", $name, $designation, $email);
            $stmt->execute();
            $faculty_id = $conn->insert_id;
            
            // 2. Insert into users table for login
            $stmt_user = $conn->prepare("INSERT INTO users (name, email, password, role, reference_id) VALUES (?, ?, ?, ?, ?)");
            $stmt_user->bind_param("ssssi", $name, $email, $hashed_password, $role, $faculty_id);
            $stmt_user->execute();
            
            $conn->commit();
            $success = "Faculty added successfully with login credentials.";
            logActivity($conn, $_SESSION['user_id'], 'Add Faculty', 'Created faculty and user account for: ' . $email);
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error adding faculty: " . $e->getMessage();
        }
    }
}

// Fetch all faculty with their login status
$faculty_list = $conn->query("
    SELECT f.*, u.id as user_id, u.status as user_status 
    FROM faculty f 
    LEFT JOIN users u ON f.id = u.reference_id 
    ORDER BY f.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Faculty | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="flex">
        <!-- Sidebar - Same as Dashboard -->
        <aside class="w-64 bg-slate-950 min-h-screen text-slate-300 p-6 flex flex-col fixed h-full">
            <!-- Sidebar content same as dashboard... omitting for brevity in this tool call but should be present -->
            <div class="flex items-center gap-3 mb-12">
                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white font-black text-xl">M</div>
                <div>
                    <h1 class="text-white font-black text-sm uppercase tracking-tight">MIT College</h1>
                </div>
            </div>
            <nav class="flex-1 space-y-2">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-chart-pie"></i> Overview
                </a>
                <a href="approve-content.php" class="flex items-center justify-between px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm transition-all">
                    <span class="flex items-center gap-3"><i class="fas fa-tasks"></i> Approvals</span>
                </a>
                <a href="manage-faculty.php" class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 text-amber-500 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-users"></i> Faculty Management
                </a>
            </nav>
            <div class="mt-auto">
                <a href="../auth/logout.php" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-400/10 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-10">
            <header class="flex justify-between items-center mb-10">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">Faculty Management</h2>
                    <p class="text-slate-500 font-medium mt-1">Manage staff accounts and login credentials.</p>
                </div>
                <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="px-6 py-3 bg-amber-500 text-white font-black text-xs uppercase tracking-widest rounded-xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/20">
                    <i class="fas fa-plus mr-2"></i> Add New Faculty
                </button>
            </header>

            <?php if ($success): ?>
                <div class="bg-green-50 text-green-600 p-4 rounded-2xl text-sm font-bold mb-6 border border-green-100 flex items-center gap-3">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Name</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Designation</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Email</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Login Access</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($faculty_list as $f): ?>
                        <tr class="hover:bg-slate-50 transition-all">
                            <td class="px-8 py-5">
                                <span class="text-sm font-bold text-slate-700"><?= e($f['name']) ?></span>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-xs font-bold text-slate-500"><?= e($f['designation']) ?></span>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-xs font-medium text-slate-600"><?= e($f['email']) ?></span>
                            </td>
                            <td class="px-8 py-5">
                                <?php if ($f['user_id']): ?>
                                    <span class="px-3 py-1 <?= $f['user_status'] === 'active' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' ?> text-[10px] font-black rounded-full uppercase tracking-widest">
                                        <?= $f['user_status'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-slate-100 text-slate-400 text-[10px] font-black rounded-full uppercase tracking-widest">No Access</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <button class="text-slate-400 hover:text-amber-500 transition-all"><i class="fas fa-edit"></i></button>
                                <button class="ml-4 text-slate-400 hover:text-red-500 transition-all"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add Faculty Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[1000] flex items-center justify-center p-6">
        <div class="bg-white w-full max-w-lg rounded-[2.5rem] p-10 shadow-2xl relative">
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="absolute top-8 right-8 text-slate-400 hover:text-slate-900">
                <i class="fas fa-times text-xl"></i>
            </button>
            
            <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-8">Add New Faculty</h3>
            
            <form action="" method="POST" class="space-y-6">
                <input type="hidden" name="add_faculty" value="1">
                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Full Name</label>
                    <input type="text" name="name" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm font-semibold">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Email Address</label>
                        <input type="email" name="email" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Designation</label>
                        <input type="text" name="designation" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm font-semibold">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Login Password</label>
                    <input type="password" name="password" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm font-semibold">
                </div>
                
                <button type="submit" class="w-full py-5 bg-amber-500 text-white font-black rounded-2xl shadow-xl shadow-amber-500/20 hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest text-sm mt-4">
                    Create Faculty Account
                </button>
            </form>
        </div>
    </div>
</body>
</html>
