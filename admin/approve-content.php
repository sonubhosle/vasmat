<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);

// Filter logic
$where = ["1=1"];
$selected_faculty = intval($_GET['faculty'] ?? 0);
$selected_type    = $conn->real_escape_string($_GET['type'] ?? '');
$selected_status  = $conn->real_escape_string($_GET['status'] ?? 'pending');

if ($selected_faculty > 0)  $where[] = "fc.faculty_id = $selected_faculty";
if ($selected_type !== '')   $where[] = "fc.type = '$selected_type'";
$where[] = "fc.status = '$selected_status'";
$where_clause = implode(" AND ", $where);

// Counts per status for tabs
function countStatus($conn, $s) {
    $r = $conn->query("SELECT COUNT(*) FROM faculty_content WHERE status='$s'");
    return $r ? (int)$r->fetch_row()[0] : 0;
}
$count_pending  = countStatus($conn, 'pending');
$count_approved = countStatus($conn, 'approved');
$count_rejected = countStatus($conn, 'rejected');

$pending_items = $conn->query("
    SELECT fc.*, f.name as faculty_name 
    FROM faculty_content fc 
    JOIN faculty f ON fc.faculty_id = f.id 
    WHERE $where_clause
    ORDER BY fc.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$faculties = $conn->query("SELECT id, name FROM faculty ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="max-w-3xl mb-8">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-100 text-amber-600 text-[10px] font-black uppercase tracking-[0.2em] mb-4">
        <i class="fas fa-info-circle"></i> Review Queue
    </div>
    <h2 class="text-3xl font-black text-slate-900 ">Content <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">Approvals</span></h2>
    <p class="text-slate-400 text-sm font-medium mt-4">Review and manage faculty-submitted notes, syllabuses and timetables.</p>
</div>

<!-- Status Tabs -->
<div class="flex gap-3 mb-8 flex-wrap">
    <?php
    $tabs = [
        ['status' => 'pending',  'label' => 'Pending',  'count' => $count_pending,  'color' => 'amber'],
        ['status' => 'approved', 'label' => 'Approved', 'count' => $count_approved, 'color' => 'emerald'],
        ['status' => 'rejected', 'label' => 'Rejected', 'count' => $count_rejected, 'color' => 'rose'],
    ];
    foreach($tabs as $tab):
        $isActive = $selected_status === $tab['status'];
        $params = http_build_query(array_merge($_GET, ['status' => $tab['status']]));
    ?>
    <a href="?<?= $params ?>" class="flex items-center gap-2.5 px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all <?= $isActive ? 'bg-slate-900 text-white shadow-xl shadow-slate-900/20' : 'bg-white border border-slate-200 hover:border-slate-300 hover:text-slate-800' ?>">
        <?php if($tab['color'] === 'amber'): ?><i class="fas fa-clock"></i><?php elseif($tab['color'] === 'emerald'): ?><i class="fas fa-circle-check"></i><?php else: ?><i class="fas fa-circle-xmark"></i><?php endif; ?>
        <?= $tab['label'] ?>
        <span class="px-2 py-0.5 rounded-lg text-[9px] <?= $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' ?>"><?= $tab['count'] ?></span>
    </a>
    <?php endforeach; ?>
</div>

<!-- Custom Filter Bar -->
<div class="bg-white border border-slate-100 rounded-3xl p-5 mb-8 shadow-sm">
    <form method="GET" id="filterForm">
        <input type="hidden" name="status" value="<?= htmlspecialchars($selected_status) ?>">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            <!-- Faculty Custom Dropdown -->
            <div>
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Filter by Faculty</label>
                <div class="relative" id="facultyDropdown">
                    <button type="button" onclick="toggleDropdown('faculty')" class="w-full bg-slate-50 rounded-2xl px-5 py-3.5 text-xs font-bold text-slate-700 flex items-center justify-between hover:bg-slate-100 transition-all">
                        <span id="facultyLabel">
                            <?php
                            if($selected_faculty > 0) {
                                foreach($faculties as $f) { if($f['id'] == $selected_faculty) echo e($f['name']); }
                            } else { echo 'All Faculty'; }
                            ?>
                        </span>
                        <i class="fas fa-chevron-down text-slate-400 text-[10px] transition-transform" id="facultyChevron"></i>
                    </button>
                    <input type="hidden" name="faculty" id="facultyValue" value="<?= $selected_faculty ?>">
                    <div id="facultyMenu" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-2xl shadow-2xl z-50 overflow-hidden max-h-56 overflow-y-auto">
                        <div onclick="selectOption('faculty', '', 'All Faculty')" class="px-5 py-3 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-slate-300"></span> All Faculty
                        </div>
                        <?php foreach($faculties as $fac): ?>
                        <div onclick="selectOption('faculty', '<?= $fac['id'] ?>', '<?= addslashes(e($fac['name'])) ?>')" class="px-5 py-3 text-xs font-bold text-slate-600 hover:bg-amber-50 hover:text-amber-700 cursor-pointer transition-all flex items-center gap-3 <?= $selected_faculty == $fac['id'] ? 'bg-amber-50 text-amber-700' : '' ?>">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span> <?= e($fac['name']) ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Type Custom Dropdown -->
            <div>
                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Filter by Type</label>
                <div class="relative" id="typeDropdown">
                    <button type="button" onclick="toggleDropdown('type')" class="w-full bg-slate-50 rounded-2xl px-5 py-3.5 text-xs font-bold text-slate-700 flex items-center justify-between hover:bg-slate-100 transition-all">
                        <span id="typeLabel">
                            <?php
                            $typeLabels = ['notes' => 'Notes', 'syllabus' => 'Syllabus', 'timetable' => 'Timetable'];
                            echo $selected_type ? ($typeLabels[$selected_type] ?? ucfirst($selected_type)) : 'All Types';
                            ?>
                        </span>
                        <i class="fas fa-chevron-down text-slate-400 text-[10px] transition-transform" id="typeChevron"></i>
                    </button>
                    <input type="hidden" name="type" id="typeValue" value="<?= htmlspecialchars($selected_type) ?>">
                    <div id="typeMenu" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-2xl shadow-2xl z-50 overflow-hidden">
                        <?php
                        $typeOptions = ['' => ['label' => 'All Types', 'icon' => 'fa-layer-group', 'color' => 'slate'], 'notes' => ['label' => 'Notes', 'icon' => 'fa-book', 'color' => 'blue'], 'syllabus' => ['label' => 'Syllabus', 'icon' => 'fa-graduation-cap', 'color' => 'purple'], 'timetable' => ['label' => 'Timetable', 'icon' => 'fa-calendar-alt', 'color' => 'amber']];
                        foreach($typeOptions as $val => $opt):
                        ?>
                        <div onclick="selectOption('type', '<?= $val ?>', '<?= $opt['label'] ?>')" class="px-5 py-3 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all flex items-center gap-3 <?= $selected_type === $val ? 'bg-slate-50' : '' ?>">
                            <i class="fas <?= $opt['icon'] ?> text-<?= $opt['color'] ?>-400 w-4 text-center"></i>
                            <?= $opt['label'] ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Apply Button -->
            <div class="flex items-end gap-3">
                <button type="submit" class="flex-1 bg-slate-900 text-white rounded-2xl py-3.5 text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/10 flex items-center justify-center gap-2">
                    <i class="fas fa-filter"></i> Apply Filter
                </button>
                <a href="approve-content.php" class="h-[46px] w-[46px] bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center hover:bg-slate-200 transition-all" title="Reset">
                    <i class="fas fa-rotate-right text-sm"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Results Count -->
<p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 px-1">
    <?= count($pending_items) ?> item<?= count($pending_items) != 1 ? 's' : '' ?> · <?= ucfirst($selected_status) ?> Queue
</p>

<!-- Content Table -->
<div class="bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Material Info</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Submitted By</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Category</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Date</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($pending_items)): ?>
                    <tr>
                        <td colspan="5" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-20 h-20 bg-slate-50 text-slate-200 rounded-3xl flex items-center justify-center text-3xl">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <div>
                                    <p class="text-slate-900 font-black uppercase text-xs tracking-widest mb-1">Queue is empty</p>
                                    <p class="text-slate-400 text-[10px] font-bold">No <?= $selected_status ?> submissions found.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pending_items as $item):
                        $typeMap = [
                            'notes'     => ['icon' => 'fa-book',           'bg' => 'bg-blue-50',   'text' => 'text-blue-600'],
                            'syllabus'  => ['icon' => 'fa-graduation-cap', 'bg' => 'bg-purple-50', 'text' => 'text-purple-600'],
                            'timetable' => ['icon' => 'fa-calendar-alt',   'bg' => 'bg-amber-50',  'text' => 'text-amber-600'],
                        ];
                        $tm = $typeMap[$item['type']] ?? ['icon' => 'fa-file-alt', 'bg' => 'bg-slate-50', 'text' => 'text-slate-500'];
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 <?= $tm['bg'] ?> <?= $tm['text'] ?> rounded-2xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                    <i class="fas <?= $tm['icon'] ?>"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 mb-1 leading-tight"><?= e($item['title']) ?></p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest truncate max-w-[250px]"><?= e($item['description'] ?: 'No description provided') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center text-white text-[10px] font-black"><?= strtoupper(substr($item['faculty_name'], 0, 1)) ?></div>
                                <span class="text-xs font-bold text-slate-700"><?= e($item['faculty_name']) ?></span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-4 py-1.5 bg-slate-100 text-slate-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-slate-200"><?= ucfirst($item['type']) ?></span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-xs font-bold text-slate-500"><?= date('d M Y', strtotime($item['created_at'])) ?></span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-3 scale-95 group-hover:scale-100 transition-all">
                                <button onclick='viewDetails(<?= json_encode($item) ?>)' class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all shadow-sm" title="View Full Details">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                                <?php if ($selected_status !== 'approved'): ?>
                                <a href="process-approval.php?id=<?= $item['id'] ?>&action=approve" class="w-10 h-10 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all shadow-lg shadow-emerald-500/10" title="Approve">
                                    <i class="fas fa-check text-xs"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($selected_status !== 'rejected'): ?>
                                <a href="process-approval.php?id=<?= $item['id'] ?>&action=reject" class="w-10 h-10 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm" title="Reject">
                                    <i class="fas fa-xmark text-xs"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] overflow-hidden shadow-2xl animate-in fade-in zoom-in-95 duration-300">
        <div class="p-10">
            <div class="flex justify-between items-center mb-8">
                <div id="modalIcon" class="w-14 h-14 rounded-2xl flex items-center justify-center text-xl shadow-lg"></div>
                <button onclick="closeModal()" class="w-10 h-10 rounded-2xl bg-slate-50 text-slate-400 hover:bg-slate-100 transition-all flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <span id="modalBadge" class="text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl inline-block mb-4"></span>
            <h3 id="modalTitle" class="text-2xl font-black text-slate-900 tracking-tight mb-1"></h3>
            <p id="modalFaculty" class="text-slate-400 font-bold uppercase text-[10px] tracking-widest mb-6"></p>

            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 mb-6">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Description</p>
                <p id="modalDescription" class="text-sm font-medium text-slate-600 leading-relaxed"></p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <a id="modalFileLink" target="_blank" class="bg-slate-900 text-white h-12 rounded-2xl flex items-center justify-center gap-2 font-black uppercase text-[9px] tracking-widest hover:bg-slate-800 transition-all">
                    <i class="fas fa-file-arrow-down"></i> Download
                </a>
                <div class="grid grid-cols-2 gap-2">
                    <a id="modalApproveBtn" class="bg-emerald-500 text-white h-12 rounded-2xl flex items-center justify-center font-black uppercase text-[9px] tracking-widest hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20">
                        <i class="fas fa-check"></i>
                    </a>
                    <a id="modalRejectBtn" class="bg-rose-500 text-white h-12 rounded-2xl flex items-center justify-center font-black uppercase text-[9px] tracking-widest hover:bg-rose-600 transition-all shadow-lg shadow-rose-500/20">
                        <i class="fas fa-xmark"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Custom Dropdown Logic
function toggleDropdown(name) {
    const menu = document.getElementById(name + 'Menu');
    const chevron = document.getElementById(name + 'Chevron');
    const isOpen = !menu.classList.contains('hidden');
    // Close all others
    ['faculty', 'type'].forEach(n => {
        document.getElementById(n + 'Menu').classList.add('hidden');
        document.getElementById(n + 'Chevron').style.transform = '';
    });
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

// Close dropdowns on outside click
document.addEventListener('click', function(e) {
    ['faculty', 'type'].forEach(name => {
        const dd = document.getElementById(name + 'Dropdown');
        if(dd && !dd.contains(e.target)) {
            document.getElementById(name + 'Menu').classList.add('hidden');
            document.getElementById(name + 'Chevron').style.transform = '';
        }
    });
});

// Modal Logic
function viewDetails(item) {
    const modal = document.getElementById('detailsModal');
    const typeMap = {
        notes:     { icon: 'fa-book',           bg: 'bg-blue-50',   text: 'text-blue-600',   badge: 'bg-blue-100 text-blue-700' },
        syllabus:  { icon: 'fa-graduation-cap', bg: 'bg-purple-50', text: 'text-purple-600', badge: 'bg-purple-100 text-purple-700' },
        timetable: { icon: 'fa-calendar-alt',   bg: 'bg-amber-50',  text: 'text-amber-600',  badge: 'bg-amber-100 text-amber-700' },
    };
    const tm = typeMap[item.type] || { icon: 'fa-file-alt', bg: 'bg-slate-50', text: 'text-slate-500', badge: 'bg-slate-100 text-slate-600' };

    const iconEl = document.getElementById('modalIcon');
    iconEl.className = `w-14 h-14 rounded-2xl flex items-center justify-center text-xl shadow-lg ${tm.bg} ${tm.text}`;
    iconEl.innerHTML = `<i class="fas ${tm.icon}"></i>`;

    const badge = document.getElementById('modalBadge');
    badge.className = `text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl inline-block mb-4 ${tm.badge}`;
    badge.innerText = item.type;

    document.getElementById('modalTitle').innerText = item.title;
    document.getElementById('modalFaculty').innerText = 'Submitted by ' + item.faculty_name;
    document.getElementById('modalDescription').innerText = item.description || 'No description provided.';
    document.getElementById('modalFileLink').href = '/vasmat/' + item.file_path;
    document.getElementById('modalApproveBtn').href = 'process-approval.php?id=' + item.id + '&action=approve';
    document.getElementById('modalRejectBtn').href = 'process-approval.php?id=' + item.id + '&action=reject';

    // Hide approval/rejection buttons if already in that state
    document.getElementById('modalApproveBtn').style.display = '<?= $selected_status ?>' === 'approved' ? 'none' : 'flex';
    document.getElementById('modalRejectBtn').style.display = '<?= $selected_status ?>' === 'rejected' ? 'none' : 'flex';

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    const modal = document.getElementById('detailsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('detailsModal').addEventListener('click', function(e) {
    if(e.target === this) closeModal();
});
</script>

<?php include 'includes/footer.php'; ?>
