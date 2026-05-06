<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);

// Fetch all pending content
$stmt = $conn->query("
    SELECT fc.*, f.name as faculty_name 
    FROM faculty_content fc 
    JOIN faculty f ON fc.faculty_id = f.id 
    WHERE fc.status = 'pending' 
    ORDER BY fc.created_at DESC
");
$pending_items = $stmt->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Content | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="flex">
        <!-- Sidebar - Same as Admin Dashboard -->
        <aside class="w-64 bg-slate-950 min-h-screen text-slate-300 p-6 flex flex-col fixed h-full">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white font-black text-xl">M</div>
                <h1 class="text-white font-black text-sm uppercase tracking-tight">MIT Admin</h1>
            </div>
            <nav class="flex-1 space-y-2">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="approve-content.php" class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 text-amber-500 rounded-xl font-bold text-sm">
                    <i class="fas fa-user-check"></i> Approvals
                </a>
            </nav>
        </aside>

        <main class="flex-1 ml-64 p-10">
            <header class="mb-10">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Content Approvals</h2>
                <p class="text-slate-500 font-medium mt-1">Review faculty submissions (Notes, Syllabus, Circulars).</p>
            </header>

            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Faculty</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Title</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Type</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Date</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (empty($pending_items)): ?>
                            <tr><td colspan="5" class="px-8 py-10 text-center text-slate-400 font-medium">No pending content.</td></tr>
                        <?php else: ?>
                            <?php foreach ($pending_items as $item): ?>
                            <tr class="hover:bg-slate-50 transition-all">
                                <td class="px-8 py-5">
                                    <span class="text-sm font-bold text-slate-700"><?= e($item['faculty_name']) ?></span>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-sm font-medium text-slate-600"><?= e($item['title']) ?></span>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 bg-slate-100 text-[10px] font-black text-slate-500 rounded-full uppercase tracking-widest"><?= e($item['type']) ?></span>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-xs text-slate-400"><?= date('M d, Y', strtotime($item['created_at'])) ?></span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="process-approval.php?id=<?= $item['id'] ?>&action=approve" class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-[10px] font-black uppercase hover:bg-emerald-600 hover:text-white transition-all">Approve</a>
                                        <a href="process-approval.php?id=<?= $item['id'] ?>&action=reject" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-[10px] font-black uppercase hover:bg-red-600 hover:text-white transition-all">Reject</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
