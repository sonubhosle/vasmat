<?php
include 'includes/header.php';

$success = '';
$error = '';

$selected_status = $_GET['status'] ?? 'all';

// Handle Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    
    // Verify ownership
    $check = $conn->prepare("SELECT id FROM faculty_content WHERE id = ? AND faculty_id = ?");
    $check->bind_param("ii", $id, $faculty_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        if ($action === 'delete') {
            $stmt = $conn->prepare("DELETE FROM faculty_content WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $success = "Content deleted successfully.";
                logActivity($conn, $_SESSION['user_id'], 'Delete Content', 'Faculty deleted content ID: ' . $id);
            }
        } elseif ($action === 'toggle_status') {
            // Faculty can UNPUBLISH (set to pending), but NOT self-approve
            $stmt = $conn->prepare("UPDATE faculty_content SET status = 'pending' WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $success = "Content has been unpublished and sent for admin review.";
            }
        }
    } else {
        $error = "Unauthorized action.";
    }
}

// Fetch counts for tabs
function getMyStatusCount($conn, $fid, $s) {
    $where = $s === 'all' ? "" : " AND status = '$s'";
    $r = $conn->query("SELECT COUNT(*) FROM faculty_content WHERE faculty_id = $fid $where");
    return $r ? (int)$r->fetch_row()[0] : 0;
}

$count_all      = getMyStatusCount($conn, $faculty_id, 'all');
$count_pending  = getMyStatusCount($conn, $faculty_id, 'pending');
$count_approved = getMyStatusCount($conn, $faculty_id, 'approved');
$count_rejected = getMyStatusCount($conn, $faculty_id, 'rejected');

// Fetch uploads with filter
$sql = "SELECT * FROM faculty_content WHERE faculty_id = ?";
if ($selected_status !== 'all') {
    $sql .= " AND status = '$selected_status'";
}
$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$uploads = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
    <div>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">My <span class="text-amber-500">Archive</span></h2>
        <p class="text-slate-500 font-medium mt-2 text-sm">Manage your published materials and track their status.</p>
    </div>
    <a href="upload.php" class="px-8 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl flex items-center gap-3">
        <i class="fas fa-plus"></i> New Submission
    </a>
</header>

<!-- Status Tabs (Admin Style) -->
<div class="flex gap-3 mb-8 flex-wrap">
    <?php
    $tabs = [
        ['status' => 'all',      'label' => 'All Items', 'count' => $count_all,      'icon' => 'fa-layer-group'],
        ['status' => 'pending',  'label' => 'Pending',   'count' => $count_pending,  'icon' => 'fa-clock'],
        ['status' => 'approved', 'label' => 'Approved',  'count' => $count_approved, 'icon' => 'fa-circle-check'],
        ['status' => 'rejected', 'label' => 'Rejected',  'count' => $count_rejected, 'icon' => 'fa-circle-xmark'],
    ];
    foreach($tabs as $tab):
        $isActive = $selected_status === $tab['status'];
    ?>
    <a href="?status=<?= $tab['status'] ?>" class="flex items-center gap-2.5 px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all <?= $isActive ? 'tab-active' : 'tab-inactive' ?>">
        <i class="fas <?= $tab['icon'] ?>"></i>
        <?= $tab['label'] ?>
        <span class="px-2 py-0.5 rounded-lg text-[9px] <?= $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' ?>"><?= $tab['count'] ?></span>
    </a>
    <?php endforeach; ?>
</div>

<?php if ($success): ?>
    <div class="bg-emerald-50 text-emerald-600 p-5 rounded-2xl text-sm font-bold mb-8 border border-emerald-100 flex items-center gap-4">
        <i class="fas fa-check-circle text-lg"></i> <?= $success ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="bg-rose-50 text-rose-600 p-5 rounded-2xl text-sm font-bold mb-8 border border-rose-100 flex items-center gap-4">
        <i class="fas fa-exclamation-circle text-lg"></i> <?= $error ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Material Info</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Category</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Status</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Uploaded On</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-right">Operations</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($uploads)): ?>
                    <tr>
                        <td colspan="5" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-20 h-20 bg-slate-50 text-slate-200 rounded-3xl flex items-center justify-center text-3xl">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <div>
                                    <p class="text-slate-900 font-black uppercase text-xs tracking-widest mb-1">No <?= $selected_status !== 'all' ? $selected_status : '' ?> items found</p>
                                    <p class="text-slate-400 text-[10px] font-bold">Try changing filters or upload new content.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($uploads as $row): ?>
                    <tr class="hover:bg-slate-50/50 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center group-hover:bg-amber-100 group-hover:text-amber-600 transition-all shadow-sm">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 mb-1 group-hover:text-primary-600 transition-colors"><?= e($row['title']) ?></p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest truncate max-w-[200px]"><?= e($row['description']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-4 py-1.5 bg-slate-100 text-slate-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-slate-200"><?= e($row['type']) ?></span>
                        </td>
                        <td class="px-8 py-6">
                            <?php 
                            $s = $row['status'];
                            $class = $s === 'approved' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : ($s === 'pending' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-rose-50 text-rose-600 border-rose-100');
                            ?>
                            <span class="px-4 py-1.5 <?= $class ?> text-[10px] font-black rounded-full uppercase tracking-widest border">
                                <i class="fas <?= $s === 'approved' ? 'fa-check-circle' : ($s === 'pending' ? 'fa-clock' : 'fa-times-circle') ?> mr-1.5"></i>
                                <?= $s ?>
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-xs font-bold text-slate-500"><?= date('M d, Y', strtotime($row['created_at'])) ?></span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-3 scale-95 group-hover:scale-100 transition-all">
                                <a href="../<?= $row['file_path'] ?>" target="_blank" class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center hover:bg-primary-500 transition-all shadow-lg shadow-slate-900/10" title="View Document">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <?php if ($row['status'] === 'approved'): ?>
                                <a href="?action=toggle_status&id=<?= $row['id'] ?>&status=<?= $selected_status ?>" class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Unpublish (Hide)">
                                    <i class="fas fa-eye-slash text-xs"></i>
                                </a>
                                <?php endif; ?>
                                <a href="?action=delete&id=<?= $row['id'] ?>&status=<?= $selected_status ?>" onclick="return confirm('Permanently delete this material?')" class="w-10 h-10 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm" title="Delete Permanent">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
