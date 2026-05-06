<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole('superadmin');

$success = '';

// Handle Clear Logs
if (isset($_POST['clear_logs'])) {
    if ($conn->query("TRUNCATE TABLE activity_logs")) {
        $success = "All activity logs have been cleared.";
        logActivity($conn, $_SESSION['user_id'], 'System Logs', 'Cleared all activity logs');
    }
}

// Fetch all logs
$logs = $conn->query("
    SELECT al.*, u.name as user_name, u.role as user_role 
    FROM activity_logs al 
    LEFT JOIN users u ON al.user_id = u.id 
    ORDER BY al.created_at DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Logs | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-slate-900 min-h-screen text-slate-300">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-950 min-h-screen p-6 flex flex-col fixed h-full border-r border-slate-800">
            <h1 class="text-white font-black text-sm uppercase tracking-tight mb-12">System Control</h1>
            <nav class="flex-1 space-y-2">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm">Dashboard</a>
                <a href="manage-admins.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm">Admins</a>
                <a href="faculty-approvals.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm">Faculty Approvals</a>
                <a href="system-logs.php" class="flex items-center gap-3 px-4 py-3 bg-amber-500 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-amber-500/20">System Logs</a>
                <a href="database-backup.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm transition-all">Database Backup</a>
            </nav>
        </aside>

        <main class="flex-1 ml-64 p-10">
            <header class="flex justify-between items-center mb-10">
                <div>
                    <h2 class="text-3xl font-black text-white tracking-tight">System Logs</h2>
                    <p class="text-slate-500 font-medium mt-1">Monitor all administrative and faculty activities.</p>
                </div>
                <form action="" method="POST" onsubmit="return confirm('Are you sure you want to clear all logs? This cannot be undone.');">
                    <button type="submit" name="clear_logs" class="px-6 py-3 bg-red-600/10 text-red-500 border border-red-600/20 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all">
                        Clear All Logs
                    </button>
                </form>
            </header>

            <?php if ($success): ?><div class="bg-emerald-500/20 text-emerald-400 p-4 rounded-xl mb-6 border border-emerald-500/30 font-bold"><?= $success ?></div><?php endif; ?>

            <div class="bg-slate-800/30 backdrop-blur-md rounded-[2.5rem] border border-slate-700/50 overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-900/50">
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Timestamp</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">User</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Action</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Description</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            <?php if (empty($logs)): ?>
                                <tr><td colspan="5" class="px-8 py-10 text-center text-slate-600 font-medium">No logs available.</td></tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                <tr class="hover:bg-slate-800/40 transition-all">
                                    <td class="px-8 py-5 text-xs text-slate-400"><?= $log['created_at'] ?></td>
                                    <td class="px-8 py-5">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-200"><?= e($log['user_name'] ?? 'System') ?></span>
                                            <span class="text-[10px] uppercase tracking-tighter text-slate-500 font-black"><?= e($log['user_role'] ?? 'N/A') ?></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="px-3 py-1 bg-slate-900 rounded-full text-[10px] font-black uppercase text-amber-500 tracking-widest"><?= e($log['action']) ?></span>
                                    </td>
                                    <td class="px-8 py-5 text-sm"><?= e($log['description']) ?></td>
                                    <td class="px-8 py-5"><code class="text-[10px] text-slate-500"><?= e($log['ip_address']) ?></code></td>
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
