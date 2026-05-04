<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);

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
        logActivity($conn, $_SESSION['user_id'], 'Faculty Approval', 'Account ID ' . $user_id . ' ' . $new_status);
        
        // If approved, create a skeleton entry in the faculty table if not exists
        if ($new_status === 'active') {
            $stmt_check = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
            $stmt_check->bind_param("i", $user_id);
            $stmt_check->execute();
            $user_data = $stmt_check->get_result()->fetch_assoc();
            
            if ($user_data) {
                $stmt_fac = $conn->prepare("INSERT INTO faculty (name, email, is_active) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE is_active = 1");
                $stmt_fac->bind_param("ss", $user_data['name'], $user_data['email']);
                $stmt_fac->execute();
                
                // Link the user to the faculty record
                $faculty_id = $conn->insert_id;
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
<body class="bg-slate-50 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-950 min-h-screen text-slate-300 p-6 flex flex-col fixed h-full">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white font-black text-xl">M</div>
                <h1 class="text-white font-black text-sm uppercase tracking-tight">MIT Admin</h1>
            </div>
            <nav class="flex-1 space-y-2">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="faculty-approvals.php" class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 text-amber-500 rounded-xl font-bold text-sm">
                    <i class="fas fa-user-check"></i> Faculty Approvals
                </a>
                <a href="manage-faculty.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm">
                    <i class="fas fa-users"></i> Faculty List
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-10">
            <header class="mb-10">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Faculty Approvals</h2>
                <p class="text-slate-500 font-medium mt-1">Review and approve new faculty registration requests.</p>
            </header>

            <?php if ($success): ?>
                <div class="bg-green-50 text-green-600 p-4 rounded-2xl text-sm font-bold mb-6 border border-green-100"><?= $success ?></div>
            <?php endif; ?>

            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Name</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Email</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Applied Date</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (empty($pending_faculty)): ?>
                            <tr><td colspan="4" class="px-8 py-10 text-center text-slate-400 font-medium">No pending requests.</td></tr>
                        <?php else: ?>
                            <?php foreach ($pending_faculty as $f): ?>
                            <tr class="hover:bg-slate-50 transition-all">
                                <td class="px-8 py-5"><span class="text-sm font-bold text-slate-700"><?= e($f['name']) ?></span></td>
                                <td class="px-8 py-5"><span class="text-xs font-medium text-slate-600"><?= e($f['email']) ?></span></td>
                                <td class="px-8 py-5"><span class="text-xs text-slate-400"><?= date('M d, Y', strtotime($f['created_at'])) ?></span></td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="?action=approve&id=<?= $f['id'] ?>" class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-[10px] font-black uppercase hover:bg-emerald-600 hover:text-white transition-all">Approve</a>
                                        <a href="?action=reject&id=<?= $f['id'] ?>" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-[10px] font-black uppercase hover:bg-red-600 hover:text-white transition-all">Reject</a>
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
