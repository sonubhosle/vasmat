<?php
require_once __DIR__ . '/../../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);
$page_title = "Gallery Hub";
include __DIR__ . '/../includes/header.php';

$success = "";
$error = "";
$uploadDir = __DIR__ . '/../../upload/gallery/';

// Logic
if (isset($_POST['add_photo'])) {
    $caption = $_POST['caption'];
    $upload = secure_upload($_FILES['photo'], ['jpg', 'jpeg', 'png', 'gif'], $uploadDir);
    if ($upload['success']) {
        $stmt = $conn->prepare("INSERT INTO gallery (image, caption) VALUES (?, ?)");
        $stmt->bind_param("ss", $upload['filename'], $caption);
        if ($stmt->execute()) $success = "Photo added!"; else $error = "DB Error";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM gallery WHERE id=$id");
    $success = "Deleted!";
}

$photos = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
?>

<div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-primary-600 mb-2 block">Visual Archives</span>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">Photo <span class="text-primary-500">Gallery</span></h2>
    </div>
    <button onclick="document.getElementById('add_modal').classList.remove('hidden')" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/10 flex items-center gap-3">
        <i class="fas fa-camera"></i> Add New Memory
    </button>
</div>

<!-- Gallery Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
    <?php while($photo = $photos->fetch_assoc()): ?>
    <div class="group bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col hover:shadow-xl transition-all duration-500 animate-in fade-in zoom-in-95 duration-500">
        <div class="aspect-square overflow-hidden relative">
            <img src="../../upload/gallery/<?= htmlspecialchars($photo['image']) ?>" 
                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                <a href="?delete=<?= $photo['id'] ?>" 
                   onclick="return confirm('Remove permanently?')"
                   class="w-12 h-12 bg-rose-500 text-white rounded-2xl flex items-center justify-center hover:bg-rose-600 transition-all transform scale-90 group-hover:scale-100 duration-300 shadow-lg">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
        </div>
        <div class="p-6">
            <p class="text-sm font-black text-slate-800 mb-1 line-clamp-1"><?= e($photo['caption'] ?: 'Campus Life') ?></p>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"><?= date('F d, Y', strtotime($photo['created_at'])) ?></p>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<!-- Add Modal -->
<div id="add_modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
    <div class="bg-white rounded-[3rem] w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-300">
        <form method="POST" enctype="multipart/form-data" class="p-10">
            <h3 class="text-2xl font-black text-slate-900 mb-8 tracking-tight">Upload Photo</h3>
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">File</label>
                    <input type="file" name="photo" required accept="image/*" class="w-full text-xs text-slate-500 file:bg-primary-50 file:border-none file:rounded-full file:px-4 file:py-2 file:mr-4 file:text-primary-700 file:font-black">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Caption</label>
                    <input type="text" name="caption" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-bold text-slate-700" placeholder="Describe the photo...">
                </div>
            </div>
            <div class="mt-10 flex gap-4">
                <button type="submit" name="add_photo" class="flex-1 bg-slate-900 text-white rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all">Publish to Gallery</button>
                <button type="button" onclick="document.getElementById('add_modal').classList.add('hidden')" class="px-8 bg-slate-100 text-slate-600 rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
