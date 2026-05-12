<?php
require_once __DIR__ . '/includes/header.php';

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

<div class="max-w-3xl mb-10">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-100 text-amber-600 text-[10px] font-black uppercase tracking-[0.2em] mb-4">
        <i class="fas fa-info-circle"></i> System Security
    </div>
    <h2 class="text-3xl font-black text-slate-900 ">Manage <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">Admins</span></h2>
    <p class="text-slate-500 mt-4 text-sm font-medium">Authorize or revoke institutional administrative privileges.</p>
</div>

<?php if ($success): ?>
<div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 animate-fade-in">
    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
        <i class="fas fa-check-circle"></i>
    </div>
    <p class="text-sm font-bold text-emerald-800"><?= $success ?></p>
</div>
<?php endif; ?>

<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Administrator</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Contact Identity</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Access Status</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Root Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($admins)): ?>
                <tr>
                    <td colspan="4" class="px-8 py-24 text-center">
                        <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">No administrative accounts found</p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($admins as $a): ?>
                    <tr class="hover:bg-slate-50/50 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 bg-slate-50 rounded-xl flex items-center justify-center text-blue-600 font-black text-xs border border-slate-100 shadow-inner">
                                    <?= strtoupper(substr($a['name'], 0, 1)) ?>
                                </div>
                                <p class="text-sm font-bold text-slate-900"><?= e($a['name']) ?></p>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-medium text-slate-600"><?= e($a['email']) ?></p>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter mt-1">Registered: <?= date('d M Y', strtotime($a['created_at'])) ?></p>
                        </td>
                        <td class="px-8 py-6">
                            <?php if ($a['status'] === 'active'): ?>
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black rounded-lg uppercase border border-emerald-100">Authorized</span>
                            <?php elseif ($a['status'] === 'pending'): ?>
                                <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[9px] font-black rounded-lg uppercase border border-amber-100">Pending Audit</span>
                            <?php else: ?>
                                <span class="px-3 py-1 bg-rose-50 text-rose-600 text-[9px] font-black rounded-lg uppercase border border-rose-100"><?= e($a['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-3">
                                <?php if ($a['status'] === 'pending'): ?>
                                    <a href="?action=approve&id=<?= $a['id'] ?>" class="px-5 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-sm">Authorize</a>
                                <?php endif; ?>
                                <a href="?action=reject&id=<?= $a['id'] ?>" class="px-5 py-2 bg-white border border-slate-200 text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm">Revoke</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
