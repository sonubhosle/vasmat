<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);

$success = '';
$error   = '';

// Handle Status Toggle
if (isset($_GET['toggle_status']) && isset($_GET['uid'])) {
    $uid        = intval($_GET['uid']);
    $new_status = $_GET['toggle_status'] === 'active' ? 'inactive' : 'active';
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'faculty'");
    $stmt->bind_param("si", $new_status, $uid);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Access updated to <strong>" . $new_status . "</strong>.";
        logActivity($conn, $_SESSION['user_id'], 'Toggle Status', 'Changed faculty status to ' . $new_status . ' for user ID: ' . $uid);
    } else {
        $_SESSION['error'] = "Failed to update status.";
    }
    $stmt->close();
    header("Location: manage-faculty.php");
    exit;
}

// Search / Filter
$search         = $conn->real_escape_string($_GET['search'] ?? '');
$filter_status  = $_GET['acc_status'] ?? '';
$where = ["1=1"];
if ($search !== '')        $where[] = "(f.name LIKE '%$search%' OR f.email LIKE '%$search%' OR f.designation LIKE '%$search%')";
if ($filter_status === 'active')   $where[] = "u.status = 'active'";
if ($filter_status === 'inactive') $where[] = "u.status = 'inactive'";
if ($filter_status === 'noaccount') $where[] = "u.id IS NULL";
$where_sql = implode(" AND ", $where);

// Counts
function facCount($conn, $extra = '') {
    $r = $conn->query("SELECT COUNT(*) FROM faculty f LEFT JOIN users u ON f.id = u.reference_id WHERE 1=1 $extra");
    return $r ? (int)$r->fetch_row()[0] : 0;
}
$count_all      = facCount($conn);
$count_active   = facCount($conn, "AND u.status = 'active'");
$count_inactive = facCount($conn, "AND u.status = 'inactive'");
$count_noaccount= facCount($conn, "AND u.id IS NULL");

$faculty_list = $conn->query("
    SELECT f.*, u.id as user_id, u.status as user_status
    FROM faculty f
    LEFT JOIN users u ON f.id = u.reference_id
    WHERE $where_sql
    ORDER BY f.name ASC
")->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-primary-600 mb-2 block">Staff Management</span>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">Faculty <span class="text-primary-500">Registry</span></h2>
        <p class="text-slate-400 text-sm font-medium mt-2">Manage faculty profiles and portal login access.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="bg-white border border-slate-100 rounded-2xl px-5 py-3 shadow-sm text-center">
            <p class="text-2xl font-black text-slate-900"><?= $count_all ?></p>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Staff</p>
        </div>
        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl px-5 py-3 text-center">
            <p class="text-2xl font-black text-emerald-700"><?= $count_active ?></p>
            <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Active</p>
        </div>
    </div>
</div>

<!-- Toast Alerts managed by global header -->

<!-- Filter Bar -->
<div class="bg-white border border-slate-100 rounded-3xl p-5 mb-8 shadow-sm">
    <form method="GET" class="flex flex-col sm:flex-row gap-4 items-end">

        <!-- Search -->
        <div class="flex-1">
            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Search Faculty</label>
            <div class="relative">
                <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Name, email or designation..."
                    class="w-full bg-slate-50 rounded-2xl py-3.5 pl-11 pr-5 text-xs font-bold text-slate-700 placeholder:text-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-400/30 transition-all">
            </div>
        </div>

        <!-- Access Status Custom Dropdown -->
        <div class="sm:w-56">
            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Access Status</label>
            <div class="relative" id="statusDropdown">
                <button type="button" onclick="toggleDropdown('status')" class="w-full bg-slate-50 rounded-2xl px-5 py-3.5 text-xs font-bold text-slate-700 flex items-center justify-between hover:bg-slate-100 transition-all">
                    <span id="statusLabel">
                        <?php
                        $statusLabels = ['' => 'All Staff', 'active' => 'Active', 'inactive' => 'Inactive', 'noaccount' => 'No Account'];
                        echo $statusLabels[$filter_status] ?? 'All Staff';
                        ?>
                    </span>
                    <i class="fas fa-chevron-down text-slate-400 text-[10px] transition-transform" id="statusChevron"></i>
                </button>
                <input type="hidden" name="acc_status" id="statusValue" value="<?= htmlspecialchars($filter_status) ?>">
                <div id="statusMenu" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-2xl shadow-2xl z-50 overflow-hidden">
                    <?php foreach(['' => ['l'=>'All Staff','icon'=>'fa-users','c'=>'slate'], 'active' => ['l'=>'Active','icon'=>'fa-circle-check','c'=>'emerald'], 'inactive' => ['l'=>'Inactive','icon'=>'fa-ban','c'=>'rose'], 'noaccount' => ['l'=>'No Account','icon'=>'fa-user-slash','c'=>'amber']] as $val => $opt): ?>
                    <div onclick="selectOption('status','<?= $val ?>','<?= $opt['l'] ?>')" class="px-5 py-3 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all flex items-center gap-3 <?= $filter_status === $val ? 'bg-slate-50 text-slate-900' : '' ?>">
                        <i class="fas <?= $opt['icon'] ?> text-<?= $opt['c'] ?>-400 w-4 text-center"></i>
                        <?= $opt['l'] ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-2">
            <button type="submit" class="bg-slate-900 text-white rounded-2xl px-6 py-3.5 text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/10 flex items-center gap-2">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="manage-faculty.php" class="w-[46px] h-[46px] bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center hover:bg-slate-200 transition-all" title="Reset">
                <i class="fas fa-rotate-right text-sm"></i>
            </a>
        </div>
    </form>
</div>

<!-- Results count -->
<p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 px-1"><?= count($faculty_list) ?> result<?= count($faculty_list) != 1 ? 's' : '' ?> found</p>

<!-- Faculty Table -->
<?php if(empty($faculty_list)): ?>
<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm py-24 text-center">
    <div class="w-20 h-20 bg-slate-50 border border-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-200">
        <i class="fas fa-user-slash text-4xl"></i>
    </div>
    <p class="text-slate-900 font-black uppercase text-xs tracking-widest mb-2">No Faculty Found</p>
    <p class="text-slate-400 text-xs font-medium">Try adjusting your search or filter.</p>
</div>
<?php else: ?>
<div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">Faculty</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest hidden sm:table-cell">Designation</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest hidden md:table-cell">Education</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">Access</th>
                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach($faculty_list as $f):
                    $hasAccount = !empty($f['user_id']);
                    $isActive   = $f['user_status'] === 'active';
                    $initials   = strtoupper(substr($f['name'], 0, 2));
                    $gradients  = [
                        'bg-gradient-to-br from-blue-500 to-indigo-600',
                        'bg-gradient-to-br from-violet-500 to-purple-600',
                        'bg-gradient-to-br from-rose-400 to-pink-600',
                        'bg-gradient-to-br from-amber-400 to-orange-500',
                        'bg-gradient-to-br from-teal-400 to-emerald-600',
                    ];
                    $grad = $gradients[ord($f['name'][0]) % count($gradients)];
                ?>
                <tr class="hover:bg-slate-50/50 transition-all group">
                    <!-- Avatar + Name -->
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 <?= $grad ?> rounded-xl flex items-center justify-center text-white text-xs font-black shadow-md flex-shrink-0">
                                <?= $initials ?>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-black text-slate-900 leading-tight truncate"><?= e($f['name']) ?></p>
                                <p class="text-[9px] font-bold text-slate-400 truncate"><?= e($f['email']) ?></p>
                            </div>
                        </div>
                    </td>

                    <!-- Designation -->
                    <td class="px-6 py-4 hidden sm:table-cell">
                        <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg">
                            <?= e($f['designation'] ?? '—') ?>
                        </span>
                    </td>

                    <!-- Education -->
                    <td class="px-6 py-4 hidden md:table-cell">
                        <p class="text-xs font-medium text-slate-500 truncate max-w-[160px]"><?= e($f['education'] ?? '—') ?></p>
                    </td>

                    <!-- Access Badge -->
                    <td class="px-6 py-4">
                        <?php if($hasAccount): ?>
                        <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl <?= $isActive ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-500 border border-rose-100' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $isActive ? 'bg-emerald-500 animate-pulse' : 'bg-rose-400' ?>"></span>
                            <?= $isActive ? 'Active' : 'Inactive' ?>
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl bg-slate-100 text-slate-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> No Account
                        </span>
                        <?php endif; ?>
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick='viewDetails(<?= json_encode($f) ?>)'
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-900 hover:text-white transition-all text-[9px] font-black uppercase tracking-widest">
                                <i class="fas fa-eye"></i> <span class="hidden lg:inline">View</span>
                            </button>

                            <?php if($hasAccount): ?>
                            <a href="?toggle_status=<?= $f['user_status'] ?>&uid=<?= $f['user_id'] ?>"
                               onclick="return confirm('Toggle access for <?= addslashes(e($f['name'])) ?>?')"
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all <?= $isActive ? 'bg-rose-100 text-rose-600 hover:bg-rose-500 hover:text-white shadow-sm shadow-rose-100' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-500 hover:text-white shadow-sm shadow-emerald-100' ?>">
                                <i class="fas <?= $isActive ? 'fa-ban' : 'fa-unlock' ?>"></i>
                                <span class="hidden lg:inline"><?= $isActive ? 'Disable' : 'Enable' ?></span>
                            </a>
                            <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-50 text-slate-300 text-[9px] font-black uppercase tracking-widest cursor-not-allowed">
                                <i class="fas fa-user-slash"></i> <span class="hidden lg:inline">No Portal</span>
                            </span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>


<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-6 opacity-0 pointer-events-none transition-all duration-500 ease-out">
    <div id="modalContent" class="bg-white rounded-[2.5rem] w-full max-w-md shadow-2xl overflow-hidden transform -translate-y-24 -translate-x-24 scale-90 opacity-0 transition-all duration-700 ease-out">
        <div class="p-10">
            <div class="flex justify-between items-center mb-8">
                <div id="modalAvatar" class="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-xl"></div>
                <button onclick="closeModal()" class="w-10 h-10 rounded-2xl bg-slate-50 text-slate-400 hover:bg-slate-100 transition-all flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <h3 id="modalName" class="text-2xl font-black text-slate-900 tracking-tight mb-1"></h3>
            <p id="modalDesig" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-8"></p>

            <div class="space-y-4 mb-8">
                <div class="flex items-start gap-4 p-4 bg-slate-50 rounded-2xl">
                    <i class="fas fa-envelope text-slate-400 mt-0.5"></i>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Email</p>
                        <p id="modalEmail" class="text-xs font-bold text-slate-700"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-4 bg-slate-50 rounded-2xl">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Education</p>
                        <p id="modalEdu" class="text-xs font-bold text-slate-700"></p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Experience</p>
                        <p id="modalExp" class="text-xs font-bold text-slate-700"></p>
                    </div>
                </div>
                <div id="modalStatusBadge" class="p-4 rounded-2xl flex items-center gap-3">
                    <i id="modalStatusIcon" class="fas text-lg"></i>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest mb-0.5 opacity-70">Portal Access</p>
                        <p id="modalStatus" class="text-xs font-black uppercase tracking-widest"></p>
                    </div>
                </div>
            </div>

            <button onclick="closeModal()" class="w-full bg-slate-900 text-white rounded-2xl py-4 text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all">
                Close Profile
            </button>
        </div>
    </div>
</div>

<style>
    .modal-active {
        opacity: 1 !important;
        pointer-events: auto !important;
    }
    .modal-active #modalContent {
        opacity: 1 !important;
        transform: translate(0, 0) scale(1) !important;
        transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1) !important;
    }
</style>

<script>
function toggleDropdown(name) {
    const menu = document.getElementById(name + 'Menu');
    const chevron = document.getElementById(name + 'Chevron');
    const isOpen = !menu.classList.contains('hidden');
    document.querySelectorAll('[id$="Menu"]').forEach(m => m.classList.add('hidden'));
    document.querySelectorAll('[id$="Chevron"]').forEach(c => c.style.transform = '');
    if (!isOpen) {
        menu.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
    }
}
function selectOption(name, value, label) {
    document.getElementById(name + 'Value').value = value;
    document.getElementById(name + 'Label').innerText = label;
    document.getElementById(name + 'Menu').classList.add('hidden');
    document.getElementById(name + 'Chevron').style.transform = '';
}
document.addEventListener('click', function(e) {
    const dd = document.getElementById('statusDropdown');
    if(dd && !dd.contains(e.target)) {
        document.getElementById('statusMenu').classList.add('hidden');
        document.getElementById('statusChevron').style.transform = '';
    }
});

function viewDetails(f) {
    const colors = ['from-blue-500 to-indigo-600','from-violet-500 to-purple-600','from-rose-400 to-pink-600','from-amber-400 to-orange-500','from-teal-400 to-emerald-600'];
    const clr = colors[f.name.charCodeAt(0) % colors.length];
    const avatar = document.getElementById('modalAvatar');
    avatar.className = `w-16 h-16 rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-xl bg-gradient-to-br ${clr}`;
    avatar.innerText = f.name.substring(0, 2).toUpperCase();

    document.getElementById('modalName').innerText = f.name;
    document.getElementById('modalDesig').innerText = f.designation || '—';
    document.getElementById('modalEmail').innerText = f.email;
    document.getElementById('modalEdu').innerText = f.education || 'Not provided';
    document.getElementById('modalExp').innerText = f.experience || 'Not provided';

    const badge   = document.getElementById('modalStatusBadge');
    const icon    = document.getElementById('modalStatusIcon');
    const status  = document.getElementById('modalStatus');
    if (!f.user_id) {
        badge.className = 'p-4 rounded-2xl flex items-center gap-3 bg-slate-100';
        icon.className  = 'fas fa-user-slash text-slate-400 text-lg';
        status.innerText = 'No Portal Account';
        status.className = 'text-xs font-black uppercase tracking-widest text-slate-500';
    } else if (f.user_status === 'active') {
        badge.className  = 'p-4 rounded-2xl flex items-center gap-3 bg-emerald-50';
        icon.className   = 'fas fa-circle-check text-emerald-500 text-lg';
        status.innerText = 'Portal Active';
        status.className = 'text-xs font-black uppercase tracking-widest text-emerald-700';
    } else {
        badge.className  = 'p-4 rounded-2xl flex items-center gap-3 bg-rose-50';
        icon.className   = 'fas fa-ban text-rose-400 text-lg';
        status.innerText = 'Portal Inactive';
        status.className = 'text-xs font-black uppercase tracking-widest text-rose-600';
    }

    const modal = document.getElementById('detailsModal');
    modal.classList.add('modal-active');
}

function closeModal() {
    const modal = document.getElementById('detailsModal');
    modal.classList.remove('modal-active');
}
document.getElementById('detailsModal').addEventListener('click', function(e) {
    if(e.target === this) closeModal();
});
</script>

<?php include 'includes/footer.php'; ?>
