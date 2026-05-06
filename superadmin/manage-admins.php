<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole('superadmin');

$success = '';
$error = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    $status = ($action === 'approve') ? 'active' : 'rejected';
    
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'admin'");
    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        $success = "Admin account $status successfully.";
        logActivity($conn, $_SESSION['user_id'], 'Admin Approval', "Account ID $id set to $status");
    } else {
        $error = "Update failed.";
    }
}

$admins = $conn->query("SELECT * FROM users WHERE role = 'admin' ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-slate-900 min-h-screen text-slate-300">
    <div class="flex">
        <!-- Sidebar - Same as Super Admin Dashboard -->
        <aside class="w-64 bg-slate-950 min-h-screen p-6 flex flex-col fixed h-full border-r border-slate-800">
            <h1 class="text-white font-black text-sm uppercase tracking-tight mb-12">System Control</h1>
            <nav class="flex-1 space-y-2">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm transition-all">Overview</a>
                <a href="manage-admins.php" class="flex items-center gap-3 px-4 py-3 bg-amber-500 text-white rounded-xl font-bold text-sm">Admins</a>
                <a href="faculty-approvals.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm transition-all">Faculty Approvals</a>
            </nav>
        </aside>

        <main class="flex-1 ml-64 p-10">
            <header class="mb-10">
                <h2 class="text-3xl font-black text-white tracking-tight">Admin Management</h2>
                <p class="text-slate-500 font-medium">Approve or Revoke administrative privileges.</p>
            </header>

            <?php if ($success): ?><div class="bg-emerald-500/20 text-emerald-400 p-4 rounded-xl mb-6 border border-emerald-500/30 font-bold"><?= $success ?></div><?php endif; ?>

            <div class="bg-slate-800/30 backdrop-blur-md rounded-[2.5rem] border border-slate-700/50 overflow-hidden shadow-2xl">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-900/50">
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Name</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Email</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        <?php foreach ($admins as $a): ?>
                        <tr class="hover:bg-slate-800/40 transition-all">
                            <td class="px-8 py-5 font-bold text-slate-200"><?= e($a['name']) ?></td>
                            <td class="px-8 py-5 text-sm"><?= e($a['email']) ?></td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest <?= $a['status'] === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' ?>">
                                    <?= $a['status'] ?>
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <?php if ($a['status'] === 'pending'): ?>
                                    <a href="?action=approve&id=<?= $a['id'] ?>" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase hover:bg-emerald-700">Approve</a>
                                <?php endif; ?>
                                <a href="?action=reject&id=<?= $a['id'] ?>" class="px-4 py-2 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase hover:bg-red-700 ml-2">Reject</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
