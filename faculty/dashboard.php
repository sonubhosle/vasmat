<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole('faculty');

$faculty_id = $_SESSION['reference_id'];
$user_name = $_SESSION['user_name'];

// Fetch stats
$stats = [
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0
];

$stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM faculty_content WHERE faculty_id = ? GROUP BY status");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $stats[$row['status']] = $row['count'];
}
$stmt->close();

// Fetch recent uploads
$uploads = [];
$stmt = $conn->prepare("SELECT * FROM faculty_content WHERE faculty_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$uploads = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 min-h-screen text-slate-300 p-6 flex flex-col fixed h-full">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white font-black text-xl">M</div>
                <div>
                    <h1 class="text-white font-black text-sm uppercase tracking-tight">MIT College</h1>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Faculty Portal</p>
                </div>
            </div>

            <nav class="flex-1 space-y-2">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 text-amber-500 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="upload.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-cloud-upload-alt"></i> Upload Content
                </a>
                <a href="my-uploads.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-file-alt"></i> My Uploads
                </a>
                <a href="profile.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-user-circle"></i> Profile Settings
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
            <!-- Header -->
            <header class="flex justify-between items-center mb-10">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">Welcome, <?= e($user_name) ?>!</h2>
                    <p class="text-slate-500 font-medium mt-1">Here's what's happening with your uploads today.</p>
                </div>
                <div class="flex items-center gap-4">
                    <button class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-600 hover:border-amber-500 transition-all shadow-sm">
                        <i class="fas fa-bell"></i>
                    </button>
                    <div class="flex items-center gap-3 bg-white p-2 pr-4 rounded-2xl border border-slate-200 shadow-sm">
                        <div class="w-8 h-8 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-700">Faculty</span>
                    </div>
                </div>
            </header>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <h3 class="text-slate-500 font-black text-[10px] uppercase tracking-[0.2em] mb-1">Pending Approval</h3>
                    <p class="text-3xl font-black text-slate-900"><?= $stats['pending'] ?></p>
                </div>
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <div class="w-12 h-12 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <h3 class="text-slate-500 font-black text-[10px] uppercase tracking-[0.2em] mb-1">Approved Content</h3>
                    <p class="text-3xl font-black text-slate-900"><?= $stats['approved'] ?></p>
                </div>
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <div class="w-12 h-12 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-times-circle text-xl"></i>
                    </div>
                    <h3 class="text-slate-500 font-black text-[10px] uppercase tracking-[0.2em] mb-1">Rejected Items</h3>
                    <p class="text-3xl font-black text-slate-900"><?= $stats['rejected'] ?></p>
                </div>
            </div>

            <!-- Content Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Uploads Table -->
                <div class="lg:col-span-2 bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Recent Uploads</h3>
                        <a href="my-uploads.php" class="text-xs font-bold text-amber-500 uppercase tracking-widest hover:text-amber-600 transition-all">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Title</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Type</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Date</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php if (empty($uploads)): ?>
                                    <tr>
                                        <td colspan="4" class="px-8 py-10 text-center text-slate-400 font-medium">No uploads yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($uploads as $upload): ?>
                                    <tr class="hover:bg-slate-50 transition-all cursor-pointer">
                                        <td class="px-8 py-5">
                                            <span class="text-sm font-bold text-slate-700"><?= e($upload['title']) ?></span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="px-3 py-1 bg-slate-100 text-[10px] font-black text-slate-600 rounded-full uppercase tracking-widest"><?= e($upload['type']) ?></span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="text-xs font-bold text-slate-500"><?= date('M d, Y', strtotime($upload['created_at'])) ?></span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <?php
                                            $status_class = [
                                                'pending' => 'bg-amber-50 text-amber-600',
                                                'approved' => 'bg-green-50 text-green-600',
                                                'rejected' => 'bg-red-50 text-red-600'
                                            ];
                                            ?>
                                            <span class="px-3 py-1 <?= $status_class[$upload['status']] ?> text-[10px] font-black rounded-full uppercase tracking-widest">
                                                <?= $upload['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="space-y-6">
                    <div class="bg-gradient-to-br from-amber-500 to-orange-500 p-8 rounded-[2rem] text-white shadow-xl shadow-amber-500/20">
                        <h3 class="text-xl font-black mb-4 leading-tight">Need to upload new material?</h3>
                        <p class="text-amber-100 text-sm font-medium mb-6">Upload syllabus, notes or timetables for your students to see.</p>
                        <a href="upload.php" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-amber-600 font-black text-xs uppercase tracking-widest rounded-xl hover:bg-slate-100 transition-all">
                            <i class="fas fa-plus"></i> New Upload
                        </a>
                    </div>

                    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight mb-6">System Notice</h3>
                        <div class="space-y-4">
                            <div class="flex gap-4">
                                <div class="w-8 h-8 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center shrink-0">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                                    All content must be approved by the Admin before it appears on the public website.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
