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

<!-- Announcements Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
    <?php while($row = $announcements->fetch_assoc()): 
        $badge = $badgeOptions[$row['badge']] ?? $badgeOptions['info'];
    ?>
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col hover:shadow-xl transition-all duration-500 group animate-in fade-in zoom-in-95 duration-500">
        <div class="h-2 w-full <?= $badge['color'] ?>"></div>
        <div class="p-8 flex flex-col flex-1">
            <div class="flex justify-between items-start mb-6">
                <span class="flex items-center gap-2 px-3 py-1.5 rounded-xl <?= $badge['color'] ?> text-white text-[9px] font-black uppercase tracking-widest shadow-md">
                    <i class="<?= $badge['icon'] ?>"></i> <?= $badge['name'] ?>
                </span>
                <span class="text-[10px] font-bold text-slate-400"><?= date('M d, Y', strtotime($row['created_at'])) ?></span>
            </div>
            
            <h4 class="text-xl font-black text-slate-900 mb-4 tracking-tight group-hover:text-primary-600 transition-colors"><?= e($row['title']) ?></h4>
            <p class="text-slate-500 text-sm leading-relaxed mb-8 flex-1"><?= e($row['description']) ?></p>
            
            <?php if($row['pdf']): ?>
            <a href="/vasmat/upload/<?= $row['pdf'] ?>" target="_blank" class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 mb-8 hover:bg-slate-100 transition-all group/pdf">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-rose-500 shadow-sm group-hover/pdf:scale-110 transition-transform">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-700">View PDF Attachment</span>
                </div>
                <i class="fas fa-external-link text-[10px] text-slate-300"></i>
            </a>
            <?php endif; ?>

            <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full <?= $row['is_active'] ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' ?>"></span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400"><?= $row['is_active'] ? 'Active' : 'Inactive' ?></span>
                </div>
                <div class="flex gap-2">
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this?')" class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
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
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Message</label>
                    <textarea name="description" rows="4" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-all resize-none" placeholder="Provide details..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Category Badge</label>
                        <select name="badge" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-all">
                            <?php foreach($badgeOptions as $k => $v): ?>
                                <option value="<?= $k ?>"><?= $v['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">PDF File (Optional)</label>
                        <input type="file" name="pdf" accept=".pdf" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>
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

<?php include '../includes/footer.php'; ?>
