<?php
include 'includes/header.php';

// Safe query helper — returns 0 if the table doesn't exist or query fails
function safeCount($conn, $sql) {
    $r = $conn->query($sql);
    return ($r && $r !== false) ? (int)$r->fetch_row()[0] : 0;
}
function safeRows($conn, $sql) {
    $r = $conn->query($sql);
    return ($r && $r !== false) ? $r->fetch_all(MYSQLI_ASSOC) : [];
}

// Fetch stats
$total_faculty        = safeCount($conn, "SELECT COUNT(*) FROM faculty");
$active_faculty       = safeCount($conn, "SELECT COUNT(*) FROM users WHERE role = 'faculty' AND status = 'active'");
$pending_approvals    = safeCount($conn, "SELECT COUNT(*) FROM faculty_content WHERE status = 'pending'");
$total_announcements  = safeCount($conn, "SELECT COUNT(*) FROM announcements");
$active_announcements = safeCount($conn, "SELECT COUNT(*) FROM announcements WHERE is_active = 1");
$total_events         = safeCount($conn, "SELECT COUNT(*) FROM events");
$total_notes          = safeCount($conn, "SELECT COUNT(*) FROM faculty_content WHERE type = 'notes' AND status = 'approved'");
$total_syllabus       = safeCount($conn, "SELECT COUNT(*) FROM faculty_content WHERE type = 'syllabus' AND status = 'approved'");

// Fetch pending content for approval queue
$pending_items = safeRows($conn, "
    SELECT fc.*, f.name as faculty_name 
    FROM faculty_content fc 
    JOIN faculty f ON fc.faculty_id = f.id 
    WHERE fc.status = 'pending' 
    ORDER BY fc.created_at ASC 
    LIMIT 5
");

?>

<!-- Dashboard Welcome Header -->
<div class="mb-12">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <span class="text-[10px] font-black uppercase tracking-[0.4em] text-primary-600 mb-2 block">Enterprise Management</span>
            <h2 class="text-4xl font-black text-slate-900 tracking-tight">Dashboard <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-500 to-orange-600">Overview</span></h2>
            <p class="text-slate-500 mt-2 text-sm font-medium">Welcome back, <?= explode(' ', e($_SESSION['user_name']))[0] ?></p>
        </div>
        
        <div class="flex items-center gap-4 bg-white p-2 pr-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-primary-600 shadow-inner">
                <i class="fas fa-calendar-day text-lg"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1"><?= date('l') ?></p>
                <p class="text-xs font-bold text-slate-800 leading-none"><?= date('d M, Y') ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Stats Bento Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">

    <!-- Faculty Card -->
    <div class="stat-card group flex items-center gap-4">
        <div class="w-12 h-12 flex-shrink-0 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-users text-base"></i>
        </div>
        <div class="min-w-0">
            <p class="text-2xl font-black text-slate-900 leading-none mb-1"><?= $total_faculty ?></p>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">Total Faculty</p>
            <?php if($total_faculty > 0): ?>
            <span class="text-[8px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg mt-1 inline-block"><?= round(($active_faculty / max($total_faculty,1)) * 100) ?>% Active</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Approvals Card -->
    <div class="stat-card group flex items-center gap-4">
        <div class="w-12 h-12 flex-shrink-0 bg-gradient-to-br from-amber-400 to-orange-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-clipboard-check text-base"></i>
        </div>
        <div class="min-w-0">
            <p class="text-2xl font-black text-slate-900 leading-none mb-1"><?= $pending_approvals ?></p>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">Pending</p>
            <?php if($pending_approvals > 0): ?>
            <span class="text-[8px] font-black text-amber-600 bg-amber-50 px-2 py-0.5 rounded-lg mt-1 inline-flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-ping inline-block"></span> Urgent
            </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Resources Card -->
    <div class="stat-card group flex items-center gap-4">
        <div class="w-12 h-12 flex-shrink-0 bg-gradient-to-br from-purple-500 to-fuchsia-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/20 group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-book-open text-base"></i>
        </div>
        <div class="min-w-0">
            <p class="text-2xl font-black text-slate-900 leading-none mb-1"><?= $active_announcements ?></p>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">Announcements</p>
            <span class="text-[8px] font-black text-purple-600 bg-purple-50 px-2 py-0.5 rounded-lg mt-1 inline-block"><?= $total_notes + $total_syllabus ?> resources</span>
        </div>
    </div>

    <!-- Events Card -->
    <div class="stat-card group flex items-center gap-4">
        <div class="w-12 h-12 flex-shrink-0 bg-gradient-to-br from-emerald-400 to-teal-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-calendar text-base"></i>
        </div>
        <div class="min-w-0">
            <p class="text-2xl font-black text-slate-900 leading-none mb-1"><?= $total_events ?></p>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">Campus Events</p>
        </div>
    </div>

</div>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Submissions -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden h-full">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Recent Submissions</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Faculty Content Queue</p>
                </div>
                <a href="approve-content.php" class="group flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-300 shadow-sm">
                    View All Queues <i class="fas fa-arrow-right text-[8px] group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Faculty Member</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Content Title</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Category</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (empty($pending_items)): ?>
                            <tr>
                                <td colspan="4" class="px-8 py-24 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-200 border border-slate-100 shadow-inner">
                                            <i class="fas fa-cloud-check text-3xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-slate-900 font-black uppercase text-xs tracking-widest mb-1">Queue is Clear</p>
                                            <p class="text-slate-400 text-[10px] font-bold">No pending content approvals at the moment.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pending_items as $item): ?>
                            <tr class="hover:bg-slate-50/50 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-11 h-11 bg-gradient-to-br from-slate-100 to-slate-200 rounded-xl flex items-center justify-center text-slate-600 font-black text-xs border border-slate-200 shadow-sm">
                                            <?= strtoupper(substr($item['faculty_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900"><?= e($item['faculty_name']) ?></p>
                                            <p class="text-[9px] font-black text-primary-600 uppercase tracking-tighter">Department Faculty</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-sm font-bold text-slate-700 line-clamp-1 group-hover:text-primary-600 transition-colors"><?= e($item['title']) ?></p>
                                    <p class="text-[10px] text-slate-400 font-medium tracking-tight"><?= date('M d, Y • h:i A', strtotime($item['created_at'])) ?></p>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex items-center px-3 py-1 bg-slate-100 text-[9px] font-black text-slate-600 rounded-lg uppercase tracking-widest border border-slate-200"><?= e($item['type']) ?></span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-3 scale-90 group-hover:scale-100 transition-all opacity-0 group-hover:opacity-100">
                                        <a href="process-approval.php?id=<?= $item['id'] ?>&action=approve" class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20" title="Approve">
                                            <i class="fas fa-check text-xs"></i>
                                        </a>
                                        <a href="../<?= $item['file_path'] ?>" target="_blank" class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20" title="Preview">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    </div>
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
        <!-- System Health Card -->
        <div class="bg-gradient-to-br from-secondary to-slate-800 p-8 rounded-[2.5rem] text-white shadow-2xl shadow-slate-900/40 relative overflow-hidden group">
            <div class="absolute right-0 bottom-0 opacity-10 group-hover:scale-110 transition-transform -mb-10 -mr-10">
                <i class="fas fa-shield-halved text-[12rem]"></i>
            </div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/20">
                        <i class="fas fa-server text-primary-400"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-primary-400 uppercase tracking-[0.2em]">System Status</p>
                        <h4 class="text-xs font-bold text-white/70 tracking-tight">Active Infrastructure</h4>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5 backdrop-blur-sm hover:bg-white/10 transition-colors">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-white/90">Database Health</span>
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                <span class="text-[9px] font-black text-emerald-400 uppercase">Optimal</span>
                            </span>
                        </div>
                    </div>
                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5 backdrop-blur-sm hover:bg-white/10 transition-colors">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-white/90">Storage Usage</span>
                            <span class="text-[9px] font-black text-primary-400 uppercase tracking-widest">32% Full</span>
                        </div>
                        <div class="mt-3 w-full h-1.5 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full bg-primary-500 rounded-full" style="width: 32%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resource Distribution -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-black text-slate-900 tracking-tight">Quick Insights</h3>
                <i class="fas fa-chart-pie-simple text-slate-200 text-xl"></i>
            </div>
            
            <div class="space-y-8">
                <div>
                    <div class="flex justify-between items-center mb-2 px-1">
                        <span class="text-xs font-black text-slate-500 uppercase tracking-widest">Study Notes</span>
                        <span class="text-xs font-bold text-slate-900"><?= $total_notes ?> Files</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden p-0.5">
                        <div class="h-full bg-blue-500 rounded-full shadow-sm" style="width: <?= min(100, ($total_notes/100)*100) ?>%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-2 px-1">
                        <span class="text-xs font-black text-slate-500 uppercase tracking-widest">Syllabus</span>
                        <span class="text-xs font-bold text-slate-900"><?= $total_syllabus ?> Files</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden p-0.5">
                        <div class="h-full bg-purple-500 rounded-full shadow-sm" style="width: <?= min(100, ($total_syllabus/100)*100) ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2 px-1">
                        <span class="text-xs font-black text-slate-500 uppercase tracking-widest">Announcements</span>
                        <span class="text-xs font-bold text-slate-900"><?= $total_announcements ?> Live</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden p-0.5">
                        <div class="h-full bg-amber-500 rounded-full shadow-sm" style="width: <?= min(100, ($total_announcements/50)*100) ?>%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-10 p-5 bg-primary-50 rounded-3xl border border-primary-100">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-primary-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/20 shrink-0">
                        <i class="fas fa-lightbulb-on text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-primary-900 leading-snug">Did you know? You can approve multiple items directly from the detailed queue.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>