<?php
include 'includes/header.php';

// Fetch stats
$total_faculty = $conn->query("SELECT COUNT(*) FROM faculty")->fetch_row()[0];
$active_faculty = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'faculty' AND status = 'active'")->fetch_row()[0];

$pending_approvals = $conn->query("SELECT COUNT(*) FROM faculty_content WHERE status = 'pending'")->fetch_row()[0];

$total_announcements = $conn->query("SELECT COUNT(*) FROM announcements")->fetch_row()[0];
$active_announcements = $conn->query("SELECT COUNT(*) FROM announcements WHERE is_active = 1")->fetch_row()[0];

$total_events = $conn->query("SELECT COUNT(*) FROM events")->fetch_row()[0];

$total_notes = $conn->query("SELECT COUNT(*) FROM faculty_content WHERE type = 'notes' AND status = 'approved'")->fetch_row()[0];
$total_syllabus = $conn->query("SELECT COUNT(*) FROM faculty_content WHERE type = 'syllabus' AND status = 'approved'")->fetch_row()[0];

// Fetch pending content for approval queue
$pending_items = $conn->query("
    SELECT fc.*, f.name as faculty_name 
    FROM faculty_content fc 
    JOIN faculty f ON fc.faculty_id = f.id 
    WHERE fc.status = 'pending' 
    ORDER BY fc.created_at ASC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

?>

<header class="flex justify-between items-end mb-12">
    <div>
        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-amber-500 mb-2 block">System Management</span>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">Admin <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">Overview</span></h2>
    </div>
    
    <div class="flex items-center gap-6">
        <div class="text-right hidden md:block">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Academic Year</p>
            <p class="text-sm font-bold text-slate-700">2024 - 2025</p>
        </div>
        <div class="h-10 w-[1px] bg-slate-200 hidden md:block"></div>
        <div class="flex items-center gap-4 bg-white p-2 pr-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center text-xs">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Principal Admin</p>
                <p class="text-xs font-bold text-slate-800 leading-none"><?= e($_SESSION['user_name']) ?></p>
            </div>
        </div>
    </div>
</header>

<!-- Stats Bento Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    <!-- Faculty Card -->
    <div class="stat-card p-8 rounded-[2.5rem] flex flex-col justify-between relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-all"></div>
        <div class="flex justify-between items-start mb-8">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl shadow-sm border border-blue-100">
                <i class="fas fa-users-crown"></i>
            </div>
            <span class="text-[10px] font-black text-green-500 bg-green-50 px-2 py-1 rounded-lg uppercase tracking-widest"><?= round(($active_faculty/$total_faculty)*100) ?>% Active</span>
        </div>
        <div>
            <p class="text-3xl font-black text-slate-900 mb-1"><?= $total_faculty ?></p>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Faculty Members</p>
        </div>
    </div>

    <!-- Approvals Card -->
    <div class="stat-card p-8 rounded-[2.5rem] flex flex-col justify-between relative overflow-hidden group border-amber-100/50">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/10 transition-all"></div>
        <div class="flex justify-between items-start mb-8">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-xl shadow-sm border border-amber-100">
                <i class="fas fa-file-signature"></i>
            </div>
            <?php if($pending_approvals > 0): ?>
                <span class="w-2 h-2 bg-amber-500 rounded-full animate-ping"></span>
            <?php endif; ?>
        </div>
        <div>
            <p class="text-3xl font-black text-amber-500 mb-1"><?= $pending_approvals ?></p>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pending Approvals</p>
        </div>
    </div>

    <!-- Content Card -->
    <div class="stat-card p-8 rounded-[2.5rem] flex flex-col justify-between relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/5 rounded-full blur-2xl group-hover:bg-purple-500/10 transition-all"></div>
        <div class="flex justify-between items-start mb-8">
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-xl shadow-sm border border-purple-100">
                <i class="fas fa-book-open-reader"></i>
            </div>
            <span class="text-[10px] font-black text-purple-400 uppercase tracking-widest"><?= $total_notes + $total_syllabus ?> Items</span>
        </div>
        <div>
            <p class="text-3xl font-black text-slate-900 mb-1"><?= $active_announcements ?></p>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Live Announcements</p>
        </div>
    </div>

    <!-- Events Card -->
    <div class="stat-card p-8 rounded-[2.5rem] flex flex-col justify-between relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-all"></div>
        <div class="flex justify-between items-start mb-8">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-xl shadow-sm border border-indigo-100">
                <i class="fas fa-calendar-lines"></i>
            </div>
        </div>
        <div>
            <p class="text-3xl font-black text-slate-900 mb-1"><?= $total_events ?></p>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Campus Events</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Approval Queue Section -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden h-full">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Recent Submissions</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Faculty Content Queue</p>
                </div>
                <a href="approve-content.php" class="px-5 py-2 bg-slate-50 text-slate-900 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Faculty</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Content</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Type</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (empty($pending_items)): ?>
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-200">
                                            <i class="fas fa-inbox text-2xl"></i>
                                        </div>
                                        <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">Queue is clear</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pending_items as $item): ?>
                            <tr class="hover:bg-slate-50/50 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 font-black text-xs border border-slate-200">
                                            <?= substr($item['faculty_name'], 0, 1) ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800"><?= e($item['faculty_name']) ?></p>
                                            <p class="text-[9px] font-black text-slate-400 uppercase">Department Faculty</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-sm font-bold text-slate-700 line-clamp-1"><?= e($item['title']) ?></p>
                                    <p class="text-[10px] text-slate-400 font-medium"><?= date('M d, Y', strtotime($item['created_at'])) ?></p>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 bg-slate-100 text-[9px] font-black text-slate-500 rounded-lg uppercase tracking-widest"><?= e($item['type']) ?></span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <a href="process-approval.php?id=<?= $item['id'] ?>&action=approve" class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20">
                                            <i class="fas fa-check text-xs"></i>
                                        </a>
                                        <a href="../<?= $item['file_path'] ?>" target="_blank" class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center hover:bg-slate-800 transition-all">
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

    <!-- Quick Actions & Secondary Info -->
    <div class="space-y-8">
        <div class="bg-gradient-to-br from-indigo-600 to-violet-700 p-8 rounded-[2.5rem] text-white shadow-2xl shadow-indigo-500/30 relative overflow-hidden group">
            <div class="absolute right-0 bottom-0 opacity-10 group-hover:scale-110 transition-transform">
                <i class="fas fa-shield-halved text-9xl -mb-8 -mr-8"></i>
            </div>
            <p class="text-[10px] font-black text-indigo-200 uppercase tracking-[0.3em] mb-2">System Status</p>
            <h3 class="text-2xl font-black mb-6">Security & <br>Performance</h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-white/10 rounded-2xl backdrop-blur-sm">
                    <span class="text-xs font-bold">Database Health</span>
                    <span class="text-[10px] font-black bg-emerald-400 text-slate-900 px-2 py-1 rounded-lg uppercase">Excellent</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-white/10 rounded-2xl backdrop-blur-sm">
                    <span class="text-xs font-bold">Server Load</span>
                    <span class="text-[10px] font-black bg-emerald-400 text-slate-900 px-2 py-1 rounded-lg uppercase">Low</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <h3 class="text-lg font-black text-slate-900 tracking-tight mb-6">Resources Distribution</h3>
            <div class="space-y-6">
                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <span class="text-xs font-bold text-slate-600">Study Notes</span>
                        <span class="text-xs font-black text-slate-400"><?= $total_notes ?></span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full" style="width: 75%"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <span class="text-xs font-bold text-slate-600">Syllabus Files</span>
                        <span class="text-xs font-black text-slate-400"><?= $total_syllabus ?></span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-500 rounded-full" style="width: 45%"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <span class="text-xs font-bold text-slate-600">Announcements</span>
                        <span class="text-xs font-black text-slate-400"><?= $total_announcements ?></span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-500 rounded-full" style="width: 60%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>