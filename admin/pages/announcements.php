<?php
require_once __DIR__ . '/../../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);
include '../includes/header.php';

$success = "";
$error = "";

// Badge color mapping with icons
$badgeOptions = [
    'hot' => ['name' => 'Hot', 'color' => 'bg-gradient-to-r from-red-500 to-pink-600', 'icon' => 'fas fa-fire'],
    'event' => ['name' => 'Event', 'color' => 'bg-gradient-to-r from-orange-500 to-amber-600', 'icon' => 'fas fa-calendar-star'],
    'new' => ['name' => 'New', 'color' => 'bg-gradient-to-r from-blue-500 to-cyan-600', 'icon' => 'fas fa-star'],
    'important' => ['name' => 'Important', 'color' => 'bg-gradient-to-r from-purple-500 to-indigo-600', 'icon' => 'fas fa-exclamation-circle'],
    'update' => ['name' => 'Update', 'color' => 'bg-gradient-to-r from-green-500 to-emerald-600', 'icon' => 'fas fa-sync-alt'],
    'urgent' => ['name' => 'Urgent', 'color' => 'bg-gradient-to-r from-red-600 to-rose-700', 'icon' => 'fas fa-bell'],
    'notice' => ['name' => 'Notice', 'color' => 'bg-gradient-to-r from-indigo-500 to-violet-600', 'icon' => 'fas fa-bullhorn'],
    'warning' => ['name' => 'Warning', 'color' => 'bg-gradient-to-r from-yellow-500 to-orange-500', 'icon' => 'fas fa-exclamation-triangle'],
    'info' => ['name' => 'Info', 'color' => 'bg-gradient-to-r from-slate-500 to-slate-600', 'icon' => 'fas fa-info-circle']
];

// Logic for Add/Update/Delete (Simplified for brevity but maintaining functionality)
if (isset($_POST['add_announcement'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $badge = $_POST['badge'];
    $is_active = (int)$_POST['is_active'];
    $pdfName = null;
    if (!empty($_FILES['pdf']['name'])) {
        $uploadDir = __DIR__ . "/../../upload/announcements/";
        $upload = secure_upload($_FILES['pdf'], ['pdf'], $uploadDir);
        if ($upload['success']) $pdfName = 'announcements/' . $upload['filename'];
        else $error = "Upload failed: " . $upload['error'];
    }
    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO announcements (title, description, badge, pdf, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $description, $badge, $pdfName, $is_active);
        if ($stmt->execute()) $success = "Announcement published!"; else $error = "DB Error";
    }
}

if (isset($_POST['edit_announcement'])) {
    $id = intval($_POST['announcement_id']);
    $title = $_POST['title'];
    $description = $_POST['description'];
    $badge = $_POST['badge'];
    $is_active = (int)$_POST['is_active'];
    
    // Handle PDF update
    $pdfUpdateSql = "";
    if (!empty($_FILES['pdf']['name'])) {
        $uploadDir = __DIR__ . "/../../upload/announcements/";
        $upload = secure_upload($_FILES['pdf'], ['pdf'], $uploadDir);
        if ($upload['success']) {
            $pdfName = 'announcements/' . $upload['filename'];
            $pdfUpdateSql = ", pdf = '$pdfName'";
        } else {
            $error = "Upload failed: " . $upload['error'];
        }
    }

    if (empty($error)) {
        $stmt = $conn->prepare("UPDATE announcements SET title=?, description=?, badge=?, is_active=? $pdfUpdateSql WHERE id=?");
        $stmt->bind_param("sssii", $title, $description, $badge, $is_active, $id);
        if ($stmt->execute()) $success = "Announcement updated!"; else $error = "Update failed";
    }
}
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) $success = "Deleted successfully!"; else $error = "Delete failed";
}

$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
$totalPosts = $conn->query("SELECT COUNT(*) FROM announcements")->fetch_row()[0];
$activeAlerts = $conn->query("SELECT COUNT(*) FROM announcements WHERE is_active = 1")->fetch_row()[0];
?>

<div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-primary-600 mb-2 block animate-in fade-in slide-in-from-left-4 duration-500">Communication Hub</span>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight animate-in fade-in slide-in-from-left-4 duration-700 delay-100">System <span class="text-primary-500">Notices</span></h2>
    </div>
    <button onclick="document.getElementById('add_modal').classList.remove('hidden')" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/10 flex items-center gap-3 active:scale-95">
        <i class="fas fa-plus"></i> New Announcement
    </button>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="stat-card">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-xl border border-blue-100 float-anim">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Posts</p>
                <h3 class="text-3xl font-black text-slate-900"><?= $totalPosts ?></h3>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-xl border border-emerald-100 float-anim">
                <i class="fas fa-satellite-dish"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Alerts</p>
                <h3 class="text-3xl font-black text-slate-900"><?= $activeAlerts ?></h3>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-xl border border-amber-100 float-anim">
                <i class="fas fa-signal"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Global Reach</p>
                <h3 class="text-3xl font-black text-slate-900">100%</h3>
            </div>
        </div>
    </div>
</div>

<!-- Announcements Table -->
<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Announcement</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Category</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Date</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Status</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if($announcements->num_rows > 0): ?>
                    <?php while($row = $announcements->fetch_assoc()): 
                        $badge = $badgeOptions[$row['badge']] ?? $badgeOptions['info'];
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-6 max-w-md">
                            <div class="flex items-center gap-4">
                                <?php if($row['pdf']): ?>
                                <a href="/vasmat/upload/<?= $row['pdf'] ?>" target="_blank" class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm shrink-0">
                                    <i class="fas fa-file-pdf text-xs"></i>
                                </a>
                                <?php else: ?>
                                <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-200 flex items-center justify-center border border-dashed border-slate-200 shrink-0">
                                    <i class="fas fa-file text-xs"></i>
                                </div>
                                <?php endif; ?>
                                <div>
                                    <h4 class="text-sm font-black text-slate-900 mb-0.5 line-clamp-1"><?= e($row['title']) ?></h4>
                                    <p class="text-[10px] text-slate-400 font-medium line-clamp-1"><?= e($row['description']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl <?= $badge['color'] ?> text-white text-[9px] font-black uppercase tracking-widest shadow-md">
                                <i class="<?= $badge['icon'] ?>"></i> <?= $badge['name'] ?>
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-xs font-bold text-slate-700"><?= date('M d, Y', strtotime($row['created_at'])) ?></span>
                        </td>
                     
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full <?= $row['is_active'] ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' ?>"></span>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400"><?= $row['is_active'] ? 'Active' : 'Hidden' ?></span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2 transition-opacity">
                                <button onclick='openEditAnnouncementModal(<?= json_encode($row) ?>)' class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-primary-500 hover:text-white transition-all">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete?')" class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center text-slate-200 text-3xl mb-4">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                                <h3 class="text-lg font-black text-slate-900">No Announcements</h3>
                                <p class="text-xs text-slate-400">Click "New Announcement" to start communicating.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal (Modern Version) -->
<div id="add_modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
    <div class="bg-white rounded-[3rem] w-full max-w-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-300">
        <form method="POST" enctype="multipart/form-data" class="p-10">
            <h3 class="text-2xl font-black text-slate-900 mb-8 tracking-tight">Post Announcement</h3>
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Title</label>
                    <input type="text" name="title" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-all" placeholder="Announcement headline...">
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="relative">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Category Badge</label>
                        <!-- Custom Smooth Dropdown -->
                        <div class="relative" id="badge_dropdown_container">
                            <input type="hidden" name="badge" id="selected_badge" value="info">
                            <button type="button" onclick="toggleBadgeDropdown()" id="dropdown_button" class="w-full bg-slate-50 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 flex items-center justify-between group transition-all hover:bg-slate-100">
                                <span id="selected_badge_label" class="flex items-center gap-2">
                                    <i class="fas fa-info-circle text-slate-500"></i> Info
                                </span>
                                <i class="fas fa-chevron-down text-[10px] text-slate-300 group-hover:text-slate-500 transition-transform duration-300" id="dropdown_arrow"></i>
                            </button>
                            
                            <div id="dropdown_menu" class="absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-[110] hidden opacity-0 scale-95 transition-all duration-300 origin-top">
                                <div class="p-2 max-h-60 overflow-y-auto">
                                    <?php foreach($badgeOptions as $k => $v): ?>
                                    <button type="button" onclick="selectBadge('<?= $k ?>', '<?= $v['name'] ?>', '<?= $v['icon'] ?>')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 transition-colors group">
                                        <div class="w-8 h-8 rounded-lg <?= $v['color'] ?> text-white flex items-center justify-center text-xs shadow-sm">
                                            <i class="<?= $v['icon'] ?>"></i>
                                        </div>
                                        <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900"><?= $v['name'] ?></span>
                                    </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">PDF File (Optional)</label>
                        <input type="file" name="pdf" accept=".pdf" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Message</label>
                    <textarea name="description" rows="4" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-all resize-none" placeholder="Provide details..."></textarea>
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="is_active" value="1" checked class="w-5 h-5 rounded-lg border-slate-200 text-primary-500 focus:ring-primary-500">
                        <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition-colors">Visible to public immediately</span>
                    </label>
                </div>
            </div>
            <div class="mt-10 flex gap-4">
                <button type="submit" name="add_announcement" class="flex-1 bg-slate-900 text-white rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">Publish Now</button>
                <button type="button" onclick="document.getElementById('add_modal').classList.add('hidden')" class="px-8 bg-slate-100 text-slate-600 rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit_modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
    <div class="bg-white rounded-[3rem] w-full max-w-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-300">
        <form method="POST" enctype="multipart/form-data" class="p-10">
            <input type="hidden" name="announcement_id" id="edit_announcement_id">
            <h3 class="text-2xl font-black text-slate-900 mb-8 tracking-tight">Update Announcement</h3>
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Title</label>
                    <input type="text" name="title" id="edit_title" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-all" placeholder="Announcement headline...">
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="relative">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Category Badge</label>
                        <!-- Custom Smooth Dropdown for Edit -->
                        <div class="relative" id="edit_badge_dropdown_container">
                            <input type="hidden" name="badge" id="edit_selected_badge">
                            <button type="button" onclick="toggleEditBadgeDropdown()" id="edit_dropdown_button" class="w-full bg-slate-50 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 flex items-center justify-between group transition-all hover:bg-slate-100">
                                <span id="edit_selected_badge_label" class="flex items-center gap-2">Select Badge</span>
                                <i class="fas fa-chevron-down text-[10px] text-slate-300 group-hover:text-slate-500 transition-transform duration-300" id="edit_dropdown_arrow"></i>
                            </button>
                            
                            <div id="edit_dropdown_menu" class="absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-[110] hidden opacity-0 scale-95 transition-all duration-300 origin-top">
                                <div class="p-2 max-h-60 overflow-y-auto">
                                    <?php foreach($badgeOptions as $k => $v): ?>
                                    <button type="button" onclick="selectEditBadge('<?= $k ?>', '<?= $v['name'] ?>', '<?= $v['icon'] ?>')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 transition-colors group">
                                        <div class="w-8 h-8 rounded-lg <?= $v['color'] ?> text-white flex items-center justify-center text-xs shadow-sm">
                                            <i class="<?= $v['icon'] ?>"></i>
                                        </div>
                                        <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900"><?= $v['name'] ?></span>
                                    </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Update PDF (Optional)</label>
                        <input type="file" name="pdf" accept=".pdf" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Message</label>
                    <textarea name="description" id="edit_description" rows="4" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-all resize-none" placeholder="Provide details..."></textarea>
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="w-5 h-5 rounded-lg border-slate-200 text-primary-500 focus:ring-primary-500">
                        <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition-colors">Visible to public</span>
                    </label>
                </div>
            </div>
            <div class="mt-10 flex gap-4">
                <button type="submit" name="edit_announcement" class="flex-1 bg-slate-900 text-white rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">Update Now</button>
                <button type="button" onclick="document.getElementById('edit_modal').classList.add('hidden')" class="px-8 bg-slate-100 text-slate-600 rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
// Badge Options Data for JS
const badgeData = <?= json_encode($badgeOptions) ?>;

function toggleBadgeDropdown() {
    const menu = document.getElementById('dropdown_menu');
    const arrow = document.getElementById('dropdown_arrow');
    toggleGenericDropdown(menu, arrow);
}

function toggleEditBadgeDropdown() {
    const menu = document.getElementById('edit_dropdown_menu');
    const arrow = document.getElementById('edit_dropdown_arrow');
    toggleGenericDropdown(menu, arrow);
}

function toggleGenericDropdown(menu, arrow) {
    if (menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
        setTimeout(() => {
            menu.classList.remove('opacity-0', 'scale-95');
            menu.classList.add('opacity-100', 'scale-100');
        }, 10);
        arrow.style.transform = 'rotate(180deg)';
    } else {
        closeGenericDropdown(menu, arrow);
    }
}

function closeGenericDropdown(menu, arrow) {
    menu.classList.remove('opacity-100', 'scale-100');
    menu.classList.add('opacity-0', 'scale-95');
    arrow.style.transform = 'rotate(0deg)';
    setTimeout(() => menu.classList.add('hidden'), 300);
}

function selectBadge(id, name, icon) {
    document.getElementById('selected_badge').value = id;
    document.getElementById('selected_badge_label').innerHTML = `<i class="${icon} text-slate-500"></i> ${name}`;
    closeGenericDropdown(document.getElementById('dropdown_menu'), document.getElementById('dropdown_arrow'));
}

function selectEditBadge(id, name, icon) {
    document.getElementById('edit_selected_badge').value = id;
    document.getElementById('edit_selected_badge_label').innerHTML = `<i class="${icon} text-slate-500"></i> ${name}`;
    closeGenericDropdown(document.getElementById('edit_dropdown_menu'), document.getElementById('edit_dropdown_arrow'));
}

function openEditAnnouncementModal(data) {
    document.getElementById('edit_announcement_id').value = data.id;
    document.getElementById('edit_title').value = data.title;
    document.getElementById('edit_description').value = data.description;
    document.getElementById('edit_is_active').checked = data.is_active == 1;
    
    // Set Badge
    const badge = badgeData[data.badge] || badgeData['info'];
    selectEditBadge(data.badge, badge.name, badge.icon);
    
    document.getElementById('edit_modal').classList.remove('hidden');
}

// Close dropdowns on outside click
document.addEventListener('click', function(event) {
    if (!document.getElementById('badge_dropdown_container').contains(event.target)) {
        closeGenericDropdown(document.getElementById('dropdown_menu'), document.getElementById('dropdown_arrow'));
    }
    if (!document.getElementById('edit_badge_dropdown_container').contains(event.target)) {
        closeGenericDropdown(document.getElementById('edit_dropdown_menu'), document.getElementById('edit_dropdown_arrow'));
    }
});
</script>

<?php include '../includes/footer.php'; ?>
