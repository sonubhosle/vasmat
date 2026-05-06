<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);

$success = '';
$error = '';

// Handle Status Toggle
if (isset($_GET['toggle_status']) && isset($_GET['uid'])) {
    $uid = intval($_GET['uid']);
    $new_status = $_GET['toggle_status'] === 'active' ? 'inactive' : 'active';
    
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'faculty'");
    $stmt->bind_param("si", $new_status, $uid);
    if ($stmt->execute()) {
        $success = "Faculty login access updated to " . $new_status;
        logActivity($conn, $_SESSION['user_id'], 'Toggle Status', 'Changed faculty status to ' . $new_status . ' for user ID: ' . $uid);
    } else {
        $error = "Failed to update status.";
    }
}

// Fetch all faculty with their login status
$faculty_list = $conn->query("
    SELECT f.*, u.id as user_id, u.status as user_status 
    FROM faculty f 
    LEFT JOIN users u ON f.id = u.reference_id 
    ORDER BY f.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<header class="flex justify-between items-center mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Faculty Management</h2>
        <p class="text-slate-500 font-medium mt-1">Manage staff accounts and login credentials.</p>
    </div>
</header>

<?php if ($success): ?>
    <div class="bg-green-50 text-green-600 p-4 rounded-2xl text-sm font-bold mb-6 border border-green-100 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
        <i class="fas fa-check-circle"></i> <?= $success ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50">
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Faculty Name</th>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Designation</th>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Login Access</th>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            <?php foreach ($faculty_list as $f): ?>
            <tr class="hover:bg-slate-50/50 transition-all group">
                <td class="px-8 py-6">
                    <div>
                        <p class="text-sm font-bold text-slate-800"><?= e($f['name']) ?></p>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-tight mt-0.5"><?= e($f['email']) ?></p>
                    </div>
                </td>
                <td class="px-8 py-6">
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-lg"><?= e($f['designation']) ?></span>
                </td>
                <td class="px-8 py-6">
                    <?php if ($f['user_id']): ?>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 <?= $f['user_status'] === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' ?> text-[10px] font-black rounded-full uppercase tracking-widest">
                                <?= $f['user_status'] ?>
                            </span>
                            <a href="?toggle_status=<?= $f['user_status'] ?>&uid=<?= $f['user_id'] ?>" 
                               class="text-[10px] font-black text-slate-400 hover:text-amber-600 underline uppercase tracking-widest transition-all">
                                Toggle Access
                            </a>
                        </div>
                    <?php else: ?>
                        <span class="px-3 py-1 bg-slate-100 text-slate-400 text-[10px] font-black rounded-full uppercase tracking-widest">No Account</span>
                    <?php endif; ?>
                </td>
                <td class="px-8 py-6 text-right">
                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                        <button onclick='viewDetails(<?= json_encode($f) ?>)' title="View Profile" class="w-10 h-10 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition-all flex items-center justify-center">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                        <button title="Edit Profile" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 hover:bg-blue-500 hover:text-white transition-all flex items-center justify-center">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <button title="Delete" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6">
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
        <div class="p-10">
            <div class="flex justify-between items-start mb-8">
                <div class="w-16 h-16 bg-amber-500 rounded-3xl flex items-center justify-center text-white text-2xl shadow-lg shadow-amber-500/20">
                    <i class="fas fa-user-tie"></i>
                </div>
                <button onclick="closeModal()" class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 hover:bg-slate-100 transition-all flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modalContent" class="space-y-6">
                <!-- Dynamic content here -->
            </div>
        </div>
        <div class="bg-slate-50 p-6 flex justify-end">
            <button onclick="closeModal()" class="px-8 py-3 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all">Close Profile</button>
        </div>
    </div>
</div>

<script>
function viewDetails(faculty) {
    const content = `
        <div>
            <h3 class="text-2xl font-black text-slate-900 tracking-tight">${faculty.name}</h3>
            <p class="text-slate-400 font-bold text-[10px] uppercase tracking-widest mt-1">${faculty.designation}</p>
        </div>
        <div class="grid grid-cols-2 gap-6 pt-6 border-t border-slate-100">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Education</p>
                <p class="text-sm font-bold text-slate-700">${faculty.education || 'Not Provided'}</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Experience</p>
                <p class="text-sm font-bold text-slate-700">${faculty.experience || 'Not Provided'}</p>
            </div>
            <div class="col-span-2">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Contact Email</p>
                <p class="text-sm font-bold text-slate-700">${faculty.email}</p>
            </div>
        </div>
        <div class="bg-amber-50 p-4 rounded-2xl border border-amber-100">
            <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">Account Status</p>
            <p class="text-sm font-bold text-amber-900">${faculty.user_status ? faculty.user_status.toUpperCase() : 'NO ACCOUNT'}</p>
        </div>
    `;
    document.getElementById('modalContent').innerHTML = content;
    const modal = document.getElementById('detailsModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    const modal = document.getElementById('detailsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

window.onclick = function(event) {
    const modal = document.getElementById('detailsModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include 'includes/footer.php'; ?>
