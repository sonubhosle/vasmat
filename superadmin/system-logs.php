<?php
require_once __DIR__ . '/includes/header.php';

$success = '';

// Handle Clear Logs
if (isset($_POST['clear_logs'])) {
    if ($conn->query("TRUNCATE TABLE activity_logs")) {
        $success = "All activity logs have been cleared.";
        logActivity($conn, $_SESSION['user_id'], 'System Logs', 'Cleared all activity logs');
    }
}

// Pagination Logic
$limit = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $limit;

// Fetch total count
$total_result = $conn->query("SELECT COUNT(*) FROM activity_logs");
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit);

// Fetch paginated logs
$logs = $conn->query("
    SELECT al.*, u.name as user_name, u.role as user_role 
    FROM activity_logs al 
    LEFT JOIN users u ON al.user_id = u.id 
    ORDER BY al.created_at DESC
    LIMIT $limit OFFSET $offset
")->fetch_all(MYSQLI_ASSOC);
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-amber-600 mb-2 block">System Forensics</span>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">Activity <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-fuchsia-600">Audit Logs</span></h2>
        <p class="text-slate-500 mt-2 text-sm font-medium">Trace every administrative action and security event within the ecosystem.</p>
    </div>
    <form action="" method="POST" onsubmit="return confirm('CRITICAL: Clear all audit data? This action is irreversible.');">
        <button type="submit" name="clear_logs" class="px-6 py-3 bg-white border border-slate-200 text-rose-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-500 hover:text-white hover:border-rose-500 transition-all shadow-sm">
            <i class="fas fa-trash-can mr-2"></i> Purge All Logs
        </button>
    </form>
</div>

<?php if ($success): ?>
<div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 animate-fade-in">
    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600">
        <i class="fas fa-broom"></i>
    </div>
    <p class="text-sm font-bold text-emerald-800"><?= $success ?></p>
</div>
<?php endif; ?>

<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Temporal Node</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Principal Identity</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Protocol Action</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Terminal Source</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="4" class="px-8 py-24 text-center">
                        <i class="fas fa-terminal text-4xl text-slate-200 mb-4 block"></i>
                        <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Buffer Empty: No activities recorded</p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                    <tr class="hover:bg-slate-50/50 transition-all group">
                        <td class="px-8 py-6">
                            <p class="text-xs font-bold text-slate-600"><?= date('H:i:s', strtotime($log['created_at'])) ?></p>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest"><?= date('d M, Y', strtotime($log['created_at'])) ?></p>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 font-black text-xs border border-slate-100 shadow-inner">
                                    <?= strtoupper(substr($log['user_name'] ?? 'S', 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900"><?= e($log['user_name'] ?? 'System') ?></p>
                                    <p class="text-[9px] font-black text-primary-600 uppercase tracking-widest"><?= e($log['user_role'] ?? 'Kernel') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-800"><?= e($log['action']) ?></span>
                                <span class="text-[10px] text-slate-400 italic mt-0.5"><?= e($log['description']) ?></span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <code class="text-[10px] bg-slate-50 px-2 py-1 rounded text-amber-600 font-mono border border-slate-100"><?= e($log['ip_address']) ?></code>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <?php if ($total_pages > 1): ?>
    <div class="px-8 py-6 bg-slate-50/30 border-t border-slate-50 flex flex-col sm:flex-row items-center justify-between gap-6">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
            Showing <span class="text-slate-900"><?= $offset + 1 ?>-<?= min($offset + $limit, $total_rows) ?></span> of <span class="text-slate-900"><?= $total_rows ?></span> logs
        </p>
        <div class="flex items-center gap-2">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
            <?php endif; ?>

            <?php
            $start_loop = max(1, $page - 2);
            $end_loop = min($total_pages, $page + 2);
            for ($i = $start_loop; $i <= $end_loop; $i++): ?>
                <a href="?page=<?= $i ?>" class="w-10 h-10 flex items-center justify-center <?= $i == $page ? 'bg-amber-500 text-white border-amber-500 shadow-lg shadow-amber-500/20' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?> rounded-xl text-xs font-black transition-all">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
