<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole('superadmin');

$success = '';
$error = '';

// Handle Approval/Rejection
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $user_id = (int)$_GET['id'];
    $new_status = ($action === 'approve') ? 'active' : 'rejected';

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'faculty'");
    $stmt->bind_param("si", $new_status, $user_id);
    
    if ($stmt->execute()) {
        $success = "Faculty account has been " . $new_status . ".";
        logActivity($conn, $_SESSION['user_id'], 'SuperAdmin Faculty Approval', 'Account ID ' . $user_id . ' ' . $new_status);
        
        if ($new_status === 'active') {
            $stmt_check = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
            $stmt_check->bind_param("i", $user_id);
            $stmt_check->execute();
            $user_data = $stmt_check->get_result()->fetch_assoc();
            
            if ($user_data) {
                // Ensure faculty record exists
                $stmt_fac = $conn->prepare("INSERT INTO faculty (name, email, is_active) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE is_active = 1");
                $stmt_fac->bind_param("ss", $user_data['name'], $user_data['email']);
                $stmt_fac->execute();
                
                $faculty_id = $conn->insert_id ?: $conn->query("SELECT id FROM faculty WHERE email = '".$user_data['email']."'")->fetch_row()[0];
                $stmt_link = $conn->prepare("UPDATE users SET reference_id = ? WHERE id = ?");
                $stmt_link->bind_param("ii", $faculty_id, $user_id);
                $stmt_link->execute();
            }
        }
    } else {
        $error = "Error processing request.";
    }
}

// Fetch Pending Faculty
$pending_faculty = $conn->query("SELECT * FROM users WHERE role = 'faculty' AND status = 'pending' ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Fetch Approved Faculty
$approved_faculty = $conn->query("SELECT * FROM users WHERE role = 'faculty' AND status = 'active' ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Approvals | <?= SITE_NAME ?></title>
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
                <a href="faculty-approvals.php" class="flex items-center gap-3 px-4 py-3 bg-amber-500 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-amber-500/20">Faculty Approvals</a>
            </nav>
        </aside>

        <main class="flex-1 ml-64 p-10">
            <header class="mb-10">
                <h2 class="text-3xl font-black text-white tracking-tight">Faculty Approvals</h2>
                <p class="text-slate-500 font-medium mt-1">Super Admin override for faculty registrations.</p>
            </header>

            <?php if ($success): ?><div class="bg-emerald-500/20 text-emerald-400 p-4 rounded-xl mb-6 border border-emerald-500/30 font-bold"><?= $success ?></div><?php endif; ?>

            <div class="bg-slate-800/30 backdrop-blur-md rounded-[2.5rem] border border-slate-700/50 overflow-hidden shadow-2xl">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-900/50">
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Name</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Email</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Date</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        <?php if (empty($pending_faculty)): ?>
                            <tr><td colspan="4" class="px-8 py-10 text-center text-slate-600 font-medium">No pending requests.</td></tr>
                        <?php else: ?>
                            <?php foreach ($pending_faculty as $f): ?>
                            <tr class="hover:bg-slate-800/40 transition-all">
                                <td class="px-8 py-5 font-bold text-slate-200"><?= e($f['name']) ?></td>
                                <td class="px-8 py-5 text-sm"><?= e($f['email']) ?></td>
                                <td class="px-8 py-5 text-xs text-slate-400"><?= date('M d, Y', strtotime($f['created_at'])) ?></td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="?action=approve&id=<?= $f['id'] ?>" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase hover:bg-emerald-700 transition-all">Approve</a>
                                        <a href="?action=reject&id=<?= $f['id'] ?>" class="px-4 py-2 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase hover:bg-red-700 transition-all">Reject</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Approved Faculty Section -->
            <header class="mb-10 mt-20">
                <h2 class="text-2xl font-black text-white tracking-tight">Active Faculty Accounts</h2>
                <p class="text-slate-500 font-medium mt-1">List of all currently approved and active faculty members.</p>
            </header>

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
                        <?php if (empty($approved_faculty)): ?>
                            <tr><td colspan="4" class="px-8 py-10 text-center text-slate-600 font-medium">No active faculty found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($approved_faculty as $f): ?>
                            <tr class="hover:bg-slate-800/40 transition-all">
                                <td class="px-8 py-5 font-bold text-slate-200"><?= e($f['name']) ?></td>
                                <td class="px-8 py-5 text-sm"><?= e($f['email']) ?></td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">Active</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <a href="?action=reject&id=<?= $f['id'] ?>" class="px-4 py-2 bg-slate-700 text-slate-300 rounded-xl text-[10px] font-black uppercase hover:bg-red-600 hover:text-white transition-all">Deactivate</a>
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
