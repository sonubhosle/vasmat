<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole('superadmin');

// Fetch stats
$admin_count = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetch_row()[0];
$faculty_count = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'faculty'")->fetch_row()[0];
$log_count = $conn->query("SELECT COUNT(*) FROM activity_logs")->fetch_row()[0];

// Fetch recent logs
$logs = $conn->query("
    SELECT al.*, u.name as user_name, u.role as user_role 
    FROM activity_logs al 
    LEFT JOIN users u ON al.user_id = u.id 
    ORDER BY al.created_at DESC 
    LIMIT 15
")->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-slate-900 min-h-screen text-slate-300">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-950 min-h-screen p-6 flex flex-col fixed h-full border-r border-slate-800">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-lg shadow-amber-500/20">M</div>
                <div>
                    <h1 class="text-white font-black text-sm uppercase tracking-tight">MIT College</h1>
                    <p class="text-[10px] font-bold text-amber-500/60 uppercase tracking-widest">Super Admin</p>
                </div>
            </div>

            <nav class="flex-1 space-y-2">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 bg-amber-500 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-amber-500/20">
                    <i class="fas fa-shield-alt"></i> System Overview
                </a>
                <a href="manage-admins.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-user-shield"></i> Manage Admins
                </a>
                <a href="system-logs.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm transition-all text-slate-400">
                    <i class="fas fa-terminal"></i> Activity Logs
                </a>
                <a href="database-backup.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm transition-all text-slate-400">
                    <i class="fas fa-database"></i> Database
                </a>
            </nav>

            <div class="mt-auto">
                <a href="../auth/logout.php" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-400/10 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-10 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-slate-800 via-slate-900 to-slate-950">
            <header class="flex justify-between items-center mb-10">
                <div>
                    <h2 class="text-3xl font-black text-white tracking-tight">System Control Center</h2>
                    <p class="text-slate-500 font-medium mt-1">Full override access & activity monitoring.</p>
                </div>
                <div class="flex gap-4">
                    <div class="bg-slate-800/50 backdrop-blur-md p-2 pr-6 rounded-2xl border border-slate-700 shadow-xl flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-500/20 text-amber-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Master User</p>
                            <p class="text-xs font-bold text-white"><?= e($_SESSION['user_name']) ?></p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-slate-800/40 backdrop-blur-md p-8 rounded-[2.5rem] border border-slate-700/50">
                    <p class="text-slate-500 font-black text-[10px] uppercase tracking-widest mb-2">Admins</p>
                    <p class="text-3xl font-black text-white"><?= $admin_count ?></p>
                </div>
                <div class="bg-slate-800/40 backdrop-blur-md p-8 rounded-[2.5rem] border border-slate-700/50">
                    <p class="text-slate-500 font-black text-[10px] uppercase tracking-widest mb-2">Faculty Users</p>
                    <p class="text-3xl font-black text-white"><?= $faculty_count ?></p>
                </div>
                <div class="bg-slate-800/40 backdrop-blur-md p-8 rounded-[2.5rem] border border-slate-700/50">
                    <p class="text-slate-500 font-black text-[10px] uppercase tracking-widest mb-2">Total Logs</p>
                    <p class="text-3xl font-black text-amber-500"><?= $log_count ?></p>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-rose-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-red-500/20">
                    <p class="text-red-100 font-black text-[10px] uppercase tracking-widest mb-2">Server Load</p>
                    <p class="text-xl font-black">Minimal</p>
                </div>
            </div>

            <!-- System Logs -->
            <div class="bg-slate-800/30 backdrop-blur-md rounded-[2.5rem] border border-slate-700/50 overflow-hidden shadow-2xl">
                <div class="p-8 border-b border-slate-700/50 flex justify-between items-center bg-slate-800/50">
                    <h3 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-3">
                        <i class="fas fa-terminal text-amber-500"></i> Recent Activity Logs
                    </h3>
                    <button class="text-xs font-bold text-slate-400 hover:text-white transition-all uppercase tracking-widest border border-slate-700 px-4 py-2 rounded-xl">Clear All Logs</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-900/50">
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Timestamp</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">User</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Role</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Action</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Description</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="6" class="px-8 py-10 text-center text-slate-600 font-medium">No activity recorded yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                <tr class="hover:bg-slate-800/40 transition-all">
                                    <td class="px-8 py-5">
                                        <span class="text-xs font-bold text-slate-400"><?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?></span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-sm font-bold text-slate-200"><?= e($log['user_name'] ?? 'System') ?></span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <?php
                                        $role_colors = [
                                            'superadmin' => 'text-amber-500',
                                            'admin' => 'text-blue-400',
                                            'faculty' => 'text-green-400'
                                        ];
                                        ?>
                                        <span class="text-[10px] font-black uppercase tracking-widest <?= $role_colors[$log['user_role']] ?? 'text-slate-500' ?>"><?= e($log['user_role'] ?? 'N/A') ?></span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="px-3 py-1 bg-slate-700/50 text-[10px] font-black text-slate-300 rounded-full uppercase tracking-widest border border-slate-600/50"><?= e($log['action']) ?></span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-xs font-medium text-slate-400"><?= e($log['description']) ?></span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <code class="text-[10px] bg-slate-900 px-2 py-1 rounded text-amber-500/70"><?= e($log['ip_address']) ?></code>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
