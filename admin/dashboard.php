<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);

// Fetch stats
$faculty_count = $conn->query("SELECT COUNT(*) FROM faculty")->fetch_row()[0];
$pending_count = $conn->query("SELECT COUNT(*) FROM faculty_content WHERE status = 'pending'")->fetch_row()[0];
$total_notes = $conn->query("SELECT COUNT(*) FROM notes")->fetch_row()[0];

// Fetch pending content for approval queue
$pending_items = $conn->query("
    SELECT fc.*, f.name as faculty_name 
    FROM faculty_content fc 
    JOIN faculty f ON fc.faculty_id = f.id 
    WHERE fc.status = 'pending' 
    ORDER BY fc.created_at ASC 
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-950 min-h-screen text-slate-300 p-6 flex flex-col fixed h-full">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white font-black text-xl">M</div>
                <div>
                    <h1 class="text-white font-black text-sm uppercase tracking-tight">MIT College</h1>
                    <p class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">Admin Control</p>
                </div>
            </div>

            <nav class="flex-1 space-y-2">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 text-amber-500 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-chart-pie"></i> Overview
                </a>
                <a href="approve-content.php" class="flex items-center justify-between px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm transition-all">
                    <span class="flex items-center gap-3"><i class="fas fa-tasks"></i> Approvals</span>
                    <?php if ($pending_count > 0): ?>
                        <span class="bg-amber-500 text-white text-[10px] px-2 py-0.5 rounded-full"><?= $pending_count ?></span>
                    <?php endif; ?>
                </a>
                <a href="manage-faculty.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-users"></i> Faculty Management
                </a>
                <a href="manage-content.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-bullhorn"></i> Announcements
                </a>
                <a href="events.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-calendar-star"></i> Events
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
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">Admin Dashboard</h2>
                    <p class="text-slate-500 font-medium mt-1">Management Overview & System Health</p>
                </div>
                <div class="flex gap-4">
                    <div class="bg-white p-2 pr-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Logged in as</p>
                            <p class="text-xs font-bold text-slate-700"><?= e($_SESSION['user_name']) ?></p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <p class="text-slate-400 font-black text-[10px] uppercase tracking-widest mb-2">Total Faculty</p>
                    <p class="text-3xl font-black text-slate-900"><?= $faculty_count ?></p>
                </div>
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <p class="text-slate-400 font-black text-[10px] uppercase tracking-widest mb-2">Pending Approvals</p>
                    <p class="text-3xl font-black text-amber-500"><?= $pending_count ?></p>
                </div>
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <p class="text-slate-400 font-black text-[10px] uppercase tracking-widest mb-2">Total Notes</p>
                    <p class="text-3xl font-black text-slate-900"><?= $total_notes ?></p>
                </div>
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-indigo-500/20">
                    <p class="text-indigo-100 font-black text-[10px] uppercase tracking-widest mb-2">System Status</p>
                    <p class="text-xl font-black">Running Smooth</p>
                </div>
            </div>

            <!-- Approval Queue -->
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Approval Queue</h3>
                    <a href="approve-content.php" class="text-xs font-bold text-amber-500 uppercase tracking-widest hover:text-amber-600 transition-all">View All Queue</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Faculty</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Content Title</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Type</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Date</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if (empty($pending_items)): ?>
                                <tr>
                                    <td colspan="5" class="px-8 py-10 text-center text-slate-400 font-medium">Approval queue is empty.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pending_items as $item): ?>
                                <tr class="hover:bg-slate-50 transition-all">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400 font-bold text-xs">
                                                <?= substr($item['faculty_name'], 0, 1) ?>
                                            </div>
                                            <span class="text-sm font-bold text-slate-700"><?= e($item['faculty_name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-sm font-bold text-slate-700"><?= e($item['title']) ?></span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="px-3 py-1 bg-slate-100 text-[10px] font-black text-slate-600 rounded-full uppercase tracking-widest"><?= e($item['type']) ?></span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-xs font-bold text-slate-500"><?= date('M d, Y', strtotime($item['created_at'])) ?></span>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="process-approval.php?id=<?= $item['id'] ?>&action=approve" class="w-9 h-9 bg-green-50 text-green-600 rounded-xl flex items-center justify-center hover:bg-green-600 hover:text-white transition-all shadow-sm" title="Approve">
                                                <i class="fas fa-check text-xs"></i>
                                            </a>
                                            <a href="process-approval.php?id=<?= $item['id'] ?>&action=reject" class="w-9 h-9 bg-red-50 text-red-600 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Reject">
                                                <i class="fas fa-times text-xs"></i>
                                            </a>
                                            <a href="../<?= $item['file_path'] ?>" target="_blank" class="w-9 h-9 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Preview">
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
        </main>
    </div>
</body>
</html>
