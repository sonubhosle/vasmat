<?php
require_once __DIR__ . '/includes/header.php';

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

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-amber-600 mb-2 block">Institutional Review</span>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">Faculty <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-600">Approvals</span></h2>
        <p class="text-slate-500 mt-2 text-sm font-medium">Review and authorize faculty registrations for system access.</p>
    </div>
</div>

<?php if ($success): ?>
<div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 animate-fade-in">
    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600">
        <i class="fas fa-check-double"></i>
    </div>
    <p class="text-sm font-bold text-emerald-800"><?= $success ?></p>
</div>
<?php endif; ?>

<!-- Pending Table -->
<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden mb-20">
    <div class="p-8 border-b border-slate-50 bg-slate-50/30">
        <h3 class="text-lg font-black text-slate-900 tracking-tight">Pending Registrations</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Faculty Member</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date Submitted</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Verification</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($pending_faculty)): ?>
                <tr>
                    <td colspan="3" class="px-8 py-20 text-center">
                        <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">No pending faculty applications</p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($pending_faculty as $f): ?>
                    <tr class="hover:bg-slate-50/50 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 bg-slate-50 rounded-xl flex items-center justify-center text-emerald-600 font-black text-xs border border-slate-100 shadow-inner">
                                    <?= strtoupper(substr($f['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900"><?= e($f['name']) ?></p>
                                    <p class="text-[10px] font-medium text-slate-400"><?= e($f['email']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-medium text-slate-600"><?= date('M d, Y', strtotime($f['created_at'])) ?></p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="?action=approve&id=<?= $f['id'] ?>" class="px-5 py-2 bg-emerald-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-sm">Verify Access</a>
                                <a href="?action=reject&id=<?= $f['id'] ?>" class="px-5 py-2 bg-white border border-slate-200 text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm">Reject</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Active Faculty -->
<div class="mb-8 px-4">
    <h3 class="text-xl font-black text-slate-900 tracking-tight uppercase">Authorized Faculty</h3>
    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Verified Personnel List</p>
</div>

<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Faculty Name</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($approved_faculty)): ?>
                <tr>
                    <td colspan="2" class="px-8 py-10 text-center">
                        <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">No active faculty accounts</p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($approved_faculty as $f): ?>
                    <tr class="hover:bg-slate-50/50 transition-all">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center text-xs font-black border border-emerald-100">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900"><?= e($f['name']) ?></p>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest"><?= e($f['email']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <a href="?action=reject&id=<?= $f['id'] ?>" class="text-[10px] font-black text-slate-400 hover:text-rose-500 uppercase tracking-widest transition-all">Deactivate Account</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
