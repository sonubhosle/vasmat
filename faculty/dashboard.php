<?php
include 'includes/header.php';

// Safe query helper
function safeCount($conn, $sql) {
    $r = $conn->query($sql);
    return ($r && $r !== false) ? (int)$r->fetch_row()[0] : 0;
}

// Stats for this specific faculty
$my_total_uploads = safeCount($conn, "SELECT COUNT(*) FROM faculty_content WHERE faculty_id = $faculty_id");
$my_approved      = safeCount($conn, "SELECT COUNT(*) FROM faculty_content WHERE faculty_id = $faculty_id AND status = 'approved'");
$my_pending       = safeCount($conn, "SELECT COUNT(*) FROM faculty_content WHERE faculty_id = $faculty_id AND status = 'pending'");
$my_rejected      = safeCount($conn, "SELECT COUNT(*) FROM faculty_content WHERE faculty_id = $faculty_id AND status = 'rejected'");

// Recent activity
$recent_uploads = $conn->query("
    SELECT * FROM faculty_content 
    WHERE faculty_id = $faculty_id 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

?>

<!-- Dashboard Welcome Header -->
<div class="mb-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="max-w-3xl mb-5">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-100 text-amber-600 text-[10px] font-black uppercase tracking-[0.2em] mb-4">
                <i class="fas fa-info-circle"></i> Instructor Overview
            </div>
            <h2 class="text-3xl font-black text-slate-900 ">Faculty <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">Console</span></h2>
            <p class="text-slate-500 mt-2 text-sm font-medium">Manage your educational resources and track student engagement.</p>
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
    <!-- Total Uploads -->
    <div class="stat-card group flex items-center gap-4">
        <div class="w-12 h-12 flex-shrink-0 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-folder-open text-base"></i>
        </div>
        <div class="min-w-0">
            <p class="text-2xl font-black text-slate-900 leading-none mb-1"><?= $my_total_uploads ?></p>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">Total Items</p>
        </div>
    </div>

    <!-- Approved -->
    <div class="stat-card group flex items-center gap-4">
        <div class="w-12 h-12 flex-shrink-0 bg-gradient-to-br from-emerald-400 to-teal-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-check-circle text-base"></i>
        </div>
        <div class="min-w-0">
            <p class="text-2xl font-black text-slate-900 leading-none mb-1"><?= $my_approved ?></p>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">Live Content</p>
        </div>
    </div>

    <!-- Pending -->
    <div class="stat-card group flex items-center gap-4">
        <div class="w-12 h-12 flex-shrink-0 bg-gradient-to-br from-amber-400 to-orange-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-clock text-base"></i>
        </div>
        <div class="min-w-0">
            <p class="text-2xl font-black text-slate-900 leading-none mb-1"><?= $my_pending ?></p>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">In Review</p>
        </div>
    </div>

    <!-- Rejected -->
    <div class="stat-card group flex items-center gap-4">
        <div class="w-12 h-12 flex-shrink-0 bg-gradient-to-br from-rose-500 to-pink-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-rose-500/20 group-hover:scale-110 transition-all duration-300">
            <i class="fas fa-exclamation-triangle text-base"></i>
        </div>
        <div class="min-w-0">
            <p class="text-2xl font-black text-slate-900 leading-none mb-1"><?= $my_rejected ?></p>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest truncate">Fix Required</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Submissions -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden h-full">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Recent Activity</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Status Tracking</p>
                </div>
                <a href="my-uploads.php" class="group flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-300 shadow-sm">
                    View Full History <i class="fas fa-arrow-right text-[8px] group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Document Title</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Category</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Preview</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (empty($recent_uploads)): ?>
                            <tr>
                                <td colspan="4" class="px-8 py-24 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-200 border border-slate-100">
                                            <i class="fas fa-cloud-upload-alt text-2xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-slate-900 font-black uppercase text-[10px] tracking-widest mb-1">No Uploads Yet</p>
                                            <a href="upload.php" class="text-primary-600 text-[10px] font-black uppercase tracking-widest hover:underline">Start Uploading Now</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_uploads as $item): ?>
                            <tr class="hover:bg-slate-50/50 transition-all group">
                                <td class="px-8 py-6">
                                    <p class="text-sm font-bold text-slate-700 line-clamp-1 group-hover:text-primary-600 transition-colors"><?= e($item['title']) ?></p>
                                    <p class="text-[9px] text-slate-400 font-medium tracking-tight"><?= date('M d, Y', strtotime($item['created_at'])) ?></p>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex items-center px-3 py-1 bg-slate-100 text-[9px] font-black text-slate-600 rounded-lg uppercase tracking-widest border border-slate-200"><?= e($item['type']) ?></span>
                                </td>
                                <td class="px-8 py-6">
                                    <?php 
                                    $s = $item['status'];
                                    $sc = $s === 'approved' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : ($s === 'pending' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-rose-50 text-rose-600 border-rose-100');
                                    ?>
                                    <span class="inline-flex items-center px-3 py-1 <?= $sc ?> text-[9px] font-black rounded-lg uppercase tracking-widest border"><?= $s ?></span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <a href="../<?= $item['file_path'] ?>" target="_blank" class="w-9 h-9 bg-slate-900 text-white rounded-xl inline-flex items-center justify-center hover:bg-primary-500 transition-all shadow-lg shadow-slate-900/10">
                                        <i class="fas fa-eye text-[10px]"></i>
                                    </a>
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
        <!-- Quick Action Card -->
        <div class="bg-gradient-to-br from-secondary to-slate-800 p-8 rounded-[2.5rem] text-white shadow-2xl shadow-slate-900/40 relative overflow-hidden group">
            <div class="absolute right-0 bottom-0 opacity-10 group-hover:scale-110 transition-transform -mb-10 -mr-10">
                <i class="fas fa-cloud-arrow-up text-[12rem]"></i>
            </div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/20">
                        <i class="fas fa-plus text-primary-400"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-primary-400 uppercase tracking-[0.2em]">Quick Action</p>
                        <h4 class="text-xs font-bold text-white/70 tracking-tight">Resource Management</h4>
                    </div>
                </div>

                <div class="space-y-4">
                    <a href="upload.php" class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5 backdrop-blur-sm hover:bg-white/10 transition-all group/link">
                        <span class="text-xs font-bold text-white/90">Add New Content</span>
                        <i class="fas fa-chevron-right text-[10px] text-primary-400 group-hover/link:translate-x-1 transition-transform"></i>
                    </a>
                    <a href="profile.php" class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5 backdrop-blur-sm hover:bg-white/10 transition-all group/link">
                        <span class="text-xs font-bold text-white/90">Update Profile</span>
                        <i class="fas fa-chevron-right text-[10px] text-primary-400 group-hover/link:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Security & Status Card -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-black text-slate-900 tracking-tight">Portal Help</h3>
                <i class="fas fa-shield-check text-slate-200 text-xl"></i>
            </div>
            
            <div class="space-y-6">
                <div class="flex gap-4">
                    <div class="w-8 h-8 bg-amber-50 text-amber-500 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fas fa-lock text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight mb-1">Privacy First</p>
                        <p class="text-[10px] text-slate-500 font-medium leading-relaxed">Change your password regularly in the profile settings to keep your account secure.</p>
                    </div>
                </div>
                
                <div class="flex gap-4">
                    <div class="w-8 h-8 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fas fa-circle-info text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight mb-1">Approval Process</p>
                        <p class="text-[10px] text-slate-500 font-medium leading-relaxed">Admin reviews happen every 24 hours. Your content will be live once approved.</p>
                    </div>
                </div>
            </div>

            <div class="mt-10 p-5 bg-rose-50 rounded-3xl border border-rose-100">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-rose-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-rose-500/20 shrink-0">
                        <i class="fas fa-power-off text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-rose-900 leading-snug">Always logout after your session ends to protect student data.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
