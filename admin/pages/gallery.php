<?php
require_once __DIR__ . '/../../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);
$page_title = "Gallery Hub";
include __DIR__ . '/../includes/header.php';

// Ensure table exists
$conn->query("CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$success = "";
$error = "";
$uploadDir = __DIR__ . '/../../upload/gallery/';

// Create directory if not exists
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Logic: Add Photo
if (isset($_POST['add_photo'])) {
    $caption = $_POST['caption'];
    $upload = secure_upload($_FILES['photo'], ['jpg', 'jpeg', 'png', 'gif', 'webp'], $uploadDir);
    if ($upload['success']) {
        $stmt = $conn->prepare("INSERT INTO gallery (image, caption) VALUES (?, ?)");
        $stmt->bind_param("ss", $upload['filename'], $caption);
        if ($stmt->execute()) $success = "Photo added successfully!"; else $error = "DB Error: " . $conn->error;
    } else {
        $error = "Upload failed: " . $upload['error'];
    }
}

// Logic: Update Photo
if (isset($_POST['edit_photo'])) {
    $id = (int)$_POST['photo_id'];
    $caption = $_POST['caption'];
    
    if (!empty($_FILES['photo']['name'])) {
        $upload = secure_upload($_FILES['photo'], ['jpg', 'jpeg', 'png', 'gif', 'webp'], $uploadDir);
        if ($upload['success']) {
            $stmt = $conn->prepare("UPDATE gallery SET image = ?, caption = ? WHERE id = ?");
            $stmt->bind_param("ssi", $upload['filename'], $caption, $id);
        } else {
            $error = "Upload failed: " . $upload['error'];
        }
    } else {
        $stmt = $conn->prepare("UPDATE gallery SET caption = ? WHERE id = ?");
        $stmt->bind_param("si", $caption, $id);
    }
    
    if (empty($error)) {
        if($stmt->execute()) $success = "Photo updated!";
        else $error = "Update failed: " . $conn->error;
    }
}

// Logic: Delete Photo
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM gallery WHERE id=$id");
    $success = "Photo removed from gallery!";
}

$photos = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
$totalPhotos = $photos ? $photos->num_rows : 0;
?>

<div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-primary-600 mb-2 block animate-in fade-in slide-in-from-left-4 duration-500">Visual Archives</span>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight animate-in fade-in slide-in-from-left-4 duration-700 delay-100">Photo <span class="text-primary-500">Gallery</span></h2>
    </div>
    <button onclick="document.getElementById('add_modal').classList.remove('hidden')" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/10 flex items-center gap-3 active:scale-95">
        <i class="fas fa-camera"></i> Add New Photo
    </button>
</div>

<!-- Alerts -->
<?php if($success): ?>
    <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
        <i class="fas fa-check-circle"></i>
        <span class="text-xs font-bold"><?= $success ?></span>
    </div>
<?php endif; ?>

<?php if($error): ?>
    <div class="mb-8 p-4 bg-rose-50 border border-rose-100 text-rose-600 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
        <i class="fas fa-exclamation-circle"></i>
        <span class="text-xs font-bold"><?= $error ?></span>
    </div>
<?php endif; ?>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="stat-card">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center text-xl border border-indigo-100">
                <i class="fas fa-images"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Assets</p>
                <h3 class="text-3xl font-black text-slate-900"><?= $totalPhotos ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Gallery Table -->
<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Preview</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Caption / Title</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Uploaded On</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if($totalPhotos > 0): ?>
                    <?php while($row = $photos->fetch_assoc()): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-2">
                            <div class="w-14 h-14 rounded-2xl overflow-hidden border-2 border-slate-100 shadow-sm group relative cursor-zoom-in">
                                <img src="../../upload/gallery/<?= htmlspecialchars($row['image']) ?>" class="w-full h-full object-cover">
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <h4 class="text-sm font-black text-slate-900 mb-0.5"><?= e($row['caption'] ?: 'Untitled Memory') ?></h4>
                            <p class="text-[10px] text-slate-400 font-medium">Filename: <?= htmlspecialchars($row['image']) ?></p>
                        </td>
                        <td class="px-4 py-2">
                            <span class="text-xs font-bold text-slate-700"><?= date('M d, Y', strtotime($row['created_at'])) ?></span>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick='openEditModal(<?= json_encode($row) ?>)' class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-primary-500 hover:text-white transition-all">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this photo?')" class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center text-slate-200 text-3xl mb-4">
                                    <i class="fas fa-image"></i>
                                </div>
                                <h3 class="text-lg font-black text-slate-900">Gallery is Empty</h3>
                                <p class="text-xs text-slate-400">Start uploading photos to build your visual archives.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="add_modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
    <div class="bg-white rounded-[3rem] w-full max-w-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-300">
        <form method="POST" enctype="multipart/form-data" class="p-10">
            <h3 class="text-2xl font-black text-slate-900 mb-8 tracking-tight">Add New Photo</h3>
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Choose Photo</label>
                    <input type="file" name="photo" required accept="image/jpeg, image/png, image/gif, image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Caption / Title</label>
                    <input type="text" name="caption" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-all" placeholder="Enter photo title...">
                </div>
            </div>
            <div class="mt-10 flex gap-4">
                <button type="submit" name="add_photo" class="flex-1 bg-slate-900 text-white rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">Publish to Gallery</button>
                <button type="button" onclick="document.getElementById('add_modal').classList.add('hidden')" class="px-8 bg-slate-100 text-slate-600 rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit_modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
    <div class="bg-white rounded-[3rem] w-full max-w-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-300">
        <form method="POST" enctype="multipart/form-data" class="p-10">
            <input type="hidden" name="photo_id" id="edit_photo_id">
            <h3 class="text-2xl font-black text-slate-900 mb-8 tracking-tight">Update Photo</h3>
            <div class="space-y-6">
                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl mb-4">
                    <img id="edit_preview" src="" class="w-20 h-20 object-cover rounded-xl shadow-sm">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Current Image</p>
                        <p class="text-xs font-bold text-slate-600">Keep it or upload a new one below</p>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Replace Photo (Optional)</label>
                    <input type="file" name="photo" accept="image/jpeg, image/png, image/gif, image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Caption / Title</label>
                    <input type="text" name="caption" id="edit_caption" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-all" placeholder="Enter photo title...">
                </div>
            </div>
            <div class="mt-10 flex gap-4">
                <button type="submit" name="edit_photo" class="flex-1 bg-slate-900 text-white rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">Update Photo</button>
                <button type="button" onclick="document.getElementById('edit_modal').classList.add('hidden')" class="px-8 bg-slate-100 text-slate-600 rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(data) {
    document.getElementById('edit_photo_id').value = data.id;
    document.getElementById('edit_caption').value = data.caption;
    document.getElementById('edit_preview').src = '../../upload/gallery/' + data.image;
    document.getElementById('edit_modal').classList.remove('hidden');
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
