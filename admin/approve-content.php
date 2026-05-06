<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);

// Filter logic
$where = ["1=1"];
if (!empty($_GET['faculty'])) {
    $faculty_id = intval($_GET['faculty']);
    $where[] = "fc.faculty_id = $faculty_id";
}
if (!empty($_GET['type'])) {
    $type = $conn->real_escape_string($_GET['type']);
    $where[] = "fc.type = '$type'";
}
if (!empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $where[] = "fc.status = '$status'";
} else {
    $where[] = "fc.status = 'pending'"; // Default to pending
}

$where_clause = implode(" AND ", $where);

// Fetch filtered content
$pending_items = $conn->query("
    SELECT fc.*, f.name as faculty_name 
    FROM faculty_content fc 
    JOIN faculty f ON fc.faculty_id = f.id 
    WHERE $where_clause
    ORDER BY fc.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

// Fetch all faculties for filter dropdown
$faculties = $conn->query("SELECT id, name FROM faculty ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<div class="mb-10 flex justify-between items-end">
    <div>
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Content Approvals</h2>
        <p class="text-slate-500 font-medium mt-1">Review faculty submissions (Notes, Syllabus, Circulars).</p>
    </div>
    
    <form action="" method="GET" class="flex gap-4 bg-white p-4 rounded-[2rem] border border-slate-100 shadow-sm">
        <div class="flex flex-col gap-1">
            <label class="text-[9px] font-black text-slate-400 uppercase px-1">Faculty</label>
            <select name="faculty" class="bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 focus:ring-amber-500 min-w-[150px]">
                <option value="">All Faculty</option>
                <?php foreach($faculties as $fac): ?>
                    <option value="<?= $fac['id'] ?>" <?= (isset($_GET['faculty']) && $_GET['faculty'] == $fac['id']) ? 'selected' : '' ?>><?= e($fac['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-[9px] font-black text-slate-400 uppercase px-1">Type</label>
            <select name="type" class="bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 focus:ring-amber-500 min-w-[120px]">
                <option value="">All Types</option>
                <option value="notes" <?= (isset($_GET['type']) && $_GET['type'] == 'notes') ? 'selected' : '' ?>>Notes</option>
                <option value="syllabus" <?= (isset($_GET['type']) && $_GET['type'] == 'syllabus') ? 'selected' : '' ?>>Syllabus</option>
                <option value="timetable" <?= (isset($_GET['type']) && $_GET['type'] == 'timetable') ? 'selected' : '' ?>>Timetable</option>
            </select>
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-[9px] font-black text-slate-400 uppercase px-1">Status</label>
            <select name="status" class="bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 focus:ring-amber-500 min-w-[120px]">
                <option value="pending" <?= (!isset($_GET['status']) || $_GET['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= (isset($_GET['status']) && $_GET['status'] == 'approved') ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= (isset($_GET['status']) && $_GET['status'] == 'rejected') ? 'selected' : '' ?>>Rejected</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="bg-slate-900 text-white px-6 h-10 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/10">Filter</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50">
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Type</th>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Title & Faculty</th>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Submission Date</th>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            <?php if (empty($pending_items)): ?>
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-200">
                                <i class="fas fa-inbox text-4xl"></i>
                            </div>
                            <div>
                                <p class="text-slate-900 font-bold">No items found</p>
                                <p class="text-slate-400 text-xs font-medium mt-1">There are no submissions matching your filters.</p>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pending_items as $item): 
                    $typeIcon = 'fa-file-alt';
                    $typeColor = 'bg-slate-100 text-slate-500';
                    if($item['type'] == 'notes') { $typeIcon = 'fa-book'; $typeColor = 'bg-blue-50 text-blue-600'; }
                    elseif($item['type'] == 'syllabus') { $typeIcon = 'fa-graduation-cap'; $typeColor = 'bg-purple-50 text-purple-600'; }
                    elseif($item['type'] == 'timetable') { $typeIcon = 'fa-calendar-alt'; $typeColor = 'bg-amber-50 text-amber-600'; }
                ?>
                <tr class="hover:bg-slate-50/50 transition-all group">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 <?= $typeColor ?> rounded-xl flex items-center justify-center text-lg">
                                <i class="fas <?= $typeIcon ?>"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400"><?= e($item['type']) ?></span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div>
                            <p class="text-sm font-bold text-slate-800 line-clamp-1"><?= e($item['title']) ?></p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-tight mt-0.5">By <?= e($item['faculty_name']) ?></p>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-xs font-bold text-slate-500"><?= date('F d, Y', strtotime($item['created_at'])) ?></span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                            <button onclick='viewDetails(<?= json_encode($item) ?>)' class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all">View Details</button>
                            <a href="process-approval.php?id=<?= $item['id'] ?>&action=approve" class="px-4 py-2 bg-emerald-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20">Approve</a>
                            <a href="process-approval.php?id=<?= $item['id'] ?>&action=reject" class="px-4 py-2 bg-red-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 transition-all shadow-lg shadow-red-500/20">Reject</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6">
    <div class="bg-white w-full max-w-xl rounded-[2.5rem] overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-300">
        <div class="p-10">
            <div class="flex justify-between items-start mb-8">
                <div id="modalIcon" class="w-16 h-16 rounded-3xl flex items-center justify-center text-2xl shadow-lg"></div>
                <button onclick="closeModal()" class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 hover:bg-slate-100 transition-all flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-6">
                <div>
                    <h3 id="modalTitle" class="text-2xl font-black text-slate-900 tracking-tight"></h3>
                    <p id="modalFaculty" class="text-slate-400 font-bold uppercase text-[10px] tracking-widest mt-1"></p>
                </div>
                
                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Description</p>
                    <p id="modalDescription" class="text-sm font-medium text-slate-600 leading-relaxed"></p>
                </div>
                
                <div class="flex gap-4">
                    <a id="modalFileLink" target="_blank" class="flex-1 bg-slate-900 text-white h-14 rounded-2xl flex items-center justify-center gap-3 font-black uppercase text-xs tracking-widest hover:bg-slate-800 transition-all">
                        <i class="fas fa-file-download text-lg"></i>
                        Download File
                    </a>
                </div>
            </div>
        </div>
        <div class="bg-slate-50 p-6 flex justify-center gap-4">
            <a id="modalApproveBtn" class="px-8 py-3 bg-emerald-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition-all">Approve Now</a>
            <a id="modalRejectBtn" class="px-8 py-3 bg-red-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 transition-all">Reject Now</a>
        </div>
    </div>
</div>

<script>
function viewDetails(item) {
    const modal = document.getElementById('detailsModal');
    document.getElementById('modalTitle').innerText = item.title;
    document.getElementById('modalFaculty').innerText = 'Submitted by ' + item.faculty_name;
    document.getElementById('modalDescription').innerText = item.description || 'No description provided.';
    document.getElementById('modalFileLink').href = '/mit-college/' + item.file_path;
    document.getElementById('modalApproveBtn').href = 'process-approval.php?id=' + item.id + '&action=approve';
    document.getElementById('modalRejectBtn').href = 'process-approval.php?id=' + item.id + '&action=reject';
    
    const iconDiv = document.getElementById('modalIcon');
    iconDiv.className = 'w-16 h-16 rounded-3xl flex items-center justify-center text-2xl shadow-lg ';
    if(item.type === 'notes') iconDiv.classList.add('bg-blue-50', 'text-blue-600');
    else if(item.type === 'syllabus') iconDiv.classList.add('bg-purple-50', 'text-purple-600');
    else iconDiv.classList.add('bg-amber-50', 'text-amber-600');
    
    iconDiv.innerHTML = `<i class="fas ${item.type === 'notes' ? 'fa-book' : (item.type === 'syllabus' ? 'fa-graduation-cap' : 'fa-calendar-alt')}"></i>`;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    const modal = document.getElementById('detailsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modal on click outside
window.onclick = function(event) {
    const modal = document.getElementById('detailsModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include 'includes/footer.php'; ?>
