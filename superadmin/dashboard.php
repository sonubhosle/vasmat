<?php
require_once __DIR__ . '/includes/header.php';

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
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);
?>

<!-- Header Section -->
<div class="mb-12">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <span class="text-[10px] font-black uppercase tracking-[0.4em] text-amber-600 mb-2 block">System Intelligence</span>
            <h2 class="text-4xl font-black text-slate-900 tracking-tight">Root <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-amber-700">Overview</span></h2>
            <p class="text-slate-500 mt-2 text-sm font-medium">Welcome back, Master Administrator.</p>
        </div>
        
        <div class="flex items-center gap-4 bg-white p-2 pr-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-amber-600 shadow-inner">
                <i class="fas fa-microchip text-lg"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">System Time</p>
                <p class="text-xs font-bold text-slate-800 leading-none"><?= date('H:i:s • d M, Y') ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Stats Bento Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="stat-card group flex items-center gap-5">
        <div class="w-14 h-14 flex-shrink-0 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-user-shield text-xl"></i>
        </div>
        <div class="min-w-0">
            <p class="text-3xl font-black text-slate-900 leading-none mb-1"><?= $admin_count ?></p>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest truncate">Admins</p>
        </div>
    </div>

    <div class="stat-card group flex items-center gap-5">
        <div class="w-14 h-14 flex-shrink-0 bg-gradient-to-br from-emerald-500 to-teal-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-users-gear text-xl"></i>
        </div>
        <div class="min-w-0">
            <p class="text-3xl font-black text-slate-900 leading-none mb-1"><?= $faculty_count ?></p>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest truncate">Faculties</p>
        </div>
    </div>

    <div class="stat-card group flex items-center gap-5">
        <div class="w-14 h-14 flex-shrink-0 bg-gradient-to-br from-amber-400 to-orange-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-terminal text-xl"></i>
        </div>
        <div class="min-w-0">
            <p class="text-3xl font-black text-amber-600 leading-none mb-1"><?= $log_count ?></p>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest truncate">System Logs</p>
        </div>
    </div>

    <div class="stat-card group flex items-center gap-5">
        <div class="w-14 h-14 flex-shrink-0 bg-gradient-to-br from-rose-500 to-red-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-rose-500/20 group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-bolt text-xl"></i>
        </div>
        <div class="min-w-0">
            <p class="text-3xl font-black text-slate-900 leading-none mb-1">99.9%</p>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest truncate">Uptime</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Activity Table -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden h-full">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Access Terminal</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Live Activity Feed</p>
                </div>
                <a href="system-logs.php" class="group flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-300 shadow-sm">
                    Full Audit <i class="fas fa-arrow-right text-[8px] group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Master/User</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">System Action</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="3" class="px-8 py-24 text-center">
                                    <i class="fas fa-ghost text-4xl text-slate-100 mb-4 block"></i>
                                    <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">No activity detected</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                            <tr class="hover:bg-slate-50/50 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-11 h-11 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 font-black text-xs border border-slate-100">
                                            <?= strtoupper(substr($log['user_name'] ?? 'S', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900"><?= e($log['user_name'] ?? 'System') ?></p>
                                            <p class="text-[9px] font-black text-amber-600 uppercase tracking-widest"><?= e($log['user_role'] ?? 'System') ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700"><?= e($log['action']) ?></span>
                                        <span class="text-[10px] text-slate-400 line-clamp-1 italic"><?= e($log['description']) ?></span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right font-mono text-[10px] text-slate-400">
                                    <?= date('H:i:s • d/m/y', strtotime($log['created_at'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Info -->
    <div class="space-y-8">
        <!-- Infrastructure Card -->
        <div class="bg-secondary p-8 rounded-[2.5rem] text-white shadow-2xl shadow-slate-900/10 border border-slate-800 relative overflow-hidden group">
            <div class="absolute -right-12 -bottom-12 opacity-5 group-hover:scale-110 transition-transform duration-500">
                <i class="fas fa-fingerprint text-[15rem]"></i>
            </div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/20">
                        <i class="fas fa-shield-check text-amber-400"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-amber-400 uppercase tracking-[0.2em]">Master Key Status</p>
                        <h4 class="text-xs font-bold text-white/70 tracking-tight">Encryption Active</h4>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5 backdrop-blur-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-white/90">Mainframe Core</span>
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                <span class="text-[9px] font-black text-emerald-400 uppercase">Synchronized</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Controls -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <h3 class="text-lg font-black text-slate-900 tracking-tight mb-8">Quick Terminal</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <a href="manage-admins.php" class="p-5 bg-slate-50 rounded-2xl border border-slate-100 hover:border-amber-200 transition-all group">
                    <i class="fas fa-user-plus text-slate-300 group-hover:text-amber-500 mb-3 block text-xl"></i>
                    <p class="text-[9px] font-black text-slate-400 group-hover:text-slate-600 uppercase tracking-widest">New Admin</p>
                </a>
                <a href="system-logs.php" class="p-5 bg-slate-50 rounded-2xl border border-slate-100 hover:border-blue-200 transition-all group">
                    <i class="fas fa-file-invoice text-slate-300 group-hover:text-blue-500 mb-3 block text-xl"></i>
                    <p class="text-[9px] font-black text-slate-400 group-hover:text-slate-600 uppercase tracking-widest">Audit Logs</p>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
