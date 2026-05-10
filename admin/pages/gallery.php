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
        if ($stmt->execute()) {
            $_SESSION['success'] = "Photo added successfully!";
        } else {
            $_SESSION['error'] = "DB Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Upload failed: " . $upload['error'];
    }
    header("Location: gallery.php");
    exit;
}

// Logic: Update Photo
if (isset($_POST['edit_photo'])) {
    $id = (int)$_POST['photo_id'];
    $caption = $_POST['caption'];
    $hasError = false;
    
    if (!empty($_FILES['photo']['name'])) {
        $upload = secure_upload($_FILES['photo'], ['jpg', 'jpeg', 'png', 'gif', 'webp'], $uploadDir);
        if ($upload['success']) {
            $stmt = $conn->prepare("UPDATE gallery SET image = ?, caption = ? WHERE id = ?");
            $stmt->bind_param("ssi", $upload['filename'], $caption, $id);
        } else {
            $_SESSION['error'] = "Upload failed: " . $upload['error'];
            $hasError = true;
        }
    } else {
        $stmt = $conn->prepare("UPDATE gallery SET caption = ? WHERE id = ?");
        $stmt->bind_param("si", $caption, $id);
    }
    
    if (!$hasError) {
        if($stmt->execute()) {
            $_SESSION['success'] = "Photo updated!";
        } else {
            $_SESSION['error'] = "Update failed: " . $conn->error;
        }
        $stmt->close();
    }
    header("Location: gallery.php");
    exit;
}

// Logic: Delete Photo
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn->query("DELETE FROM gallery WHERE id=$id")) {
        $_SESSION['success'] = "Photo removed from gallery!";
    } else {
        $_SESSION['error'] = "Delete failed.";
    }
    header("Location: gallery.php");
    exit;
}

$photos = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
$totalPhotos = $photos ? $photos->num_rows : 0;
?>

<div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-primary-600 mb-2 block animate-in fade-in slide-in-from-left-4 duration-500">Visual Archives</span>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight animate-in fade-in slide-in-from-left-4 duration-700 delay-100">Photo <span class="text-primary-500">Gallery</span></h2>
    </div>
    <button onclick="openAddModal()" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/10 flex items-center gap-3 active:scale-95">
        <i class="fas fa-camera"></i> Add New Photo
    </button>
</div>

<!-- Toast Alerts managed by global header -->

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
<div id="add_modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-6 opacity-0 pointer-events-none transition-all duration-500 ease-out">
    <div id="add_modal_content" class="bg-white rounded-[3rem] w-full max-w-lg shadow-2xl overflow-hidden transform -translate-y-24 -translate-x-24 scale-90 opacity-0 transition-all duration-700 ease-out">
        <form method="POST" enctype="multipart/form-data" class="p-6">
            <h3 class="text-xl font-black text-slate-900 mb-6 tracking-tight">Add New Photo</h3>
            <div class="space-y-4">
                <div class="relative group">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block px-1">Choose Photo</label>
                    <input type="file" name="photo" id="photoInput" required accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="handleFileSelect(this, 'fileStatus', 'dropZone')">
                    <div id="dropZone" class="w-full py-4 px-6 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl flex items-center gap-4 group-hover:border-primary-400 group-hover:bg-primary-50/30 transition-all duration-500">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-slate-300 group-hover:text-primary-500 group-hover:scale-110 transition-all duration-500 shrink-0">
                            <i class="fas fa-camera text-lg"></i>
                        </div>
                        <div class="text-left overflow-hidden">
                            <p id="fileStatus" class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1 truncate">Click or Drag Image</p>
                            <p class="text-[8px] font-bold text-slate-300 uppercase tracking-tighter">JPG, PNG, WebP (Max 5MB)</p>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block px-1">Caption / Title</label>
                    <input type="text" name="caption" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-3.5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all duration-300" placeholder="Enter photo title...">
                </div>
            </div>
            <div class="mt-8 flex gap-4">
                <button type="submit" name="add_photo" class="flex-1 bg-slate-900 text-white rounded-2xl py-3.5 font-black text-[10px] uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">Publish to Gallery</button>
                <button type="button" onclick="closeAddModal()" class="px-8 bg-slate-100 text-slate-600 rounded-2xl py-3.5 font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit_modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-6 opacity-0 pointer-events-none transition-all duration-500 ease-out">
    <div id="edit_modal_content" class="bg-white rounded-[3rem] w-full max-w-lg shadow-2xl overflow-hidden transform -translate-y-24 -translate-x-24 scale-90 opacity-0 transition-all duration-700 ease-out">
        <form method="POST" enctype="multipart/form-data" class="p-6">
            <input type="hidden" name="photo_id" id="edit_photo_id">
            <h3 class="text-xl font-black text-slate-900 mb-6 tracking-tight">Update Photo</h3>
            <div class="space-y-4">
                <div class="flex items-center gap-4 p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                    <img id="edit_preview" src="" class="w-14 h-14 object-cover rounded-xl shadow-sm">
                    <div class="overflow-hidden">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Current Preview</p>
                        <p class="text-[10px] font-bold text-slate-600 truncate">Keep or Replace below</p>
                    </div>
                </div>
                <div class="relative group">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block px-1">Replace Photo (Optional)</label>
                    <input type="file" name="photo" id="editPhotoInput" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="handleFileSelect(this, 'editFileStatus', 'editDropZone')">
                    <div id="editDropZone" class="w-full py-4 px-6 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl flex items-center gap-4 group-hover:border-primary-400 group-hover:bg-primary-50/30 transition-all duration-500">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-slate-300 group-hover:text-primary-500 group-hover:scale-110 transition-all duration-500 shrink-0">
                            <i class="fas fa-camera text-lg"></i>
                        </div>
                        <div class="text-left overflow-hidden">
                            <p id="editFileStatus" class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1 truncate">Replace Asset</p>
                            <p class="text-[8px] font-bold text-slate-300 uppercase tracking-tighter">JPG, PNG, WebP (Max 5MB)</p>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block px-1">Caption / Title</label>
                    <input type="text" name="caption" id="edit_caption" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-3.5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all duration-300" placeholder="Enter photo title...">
                </div>
            </div>
            <div class="mt-8 flex gap-4">
                <button type="submit" name="edit_photo" class="flex-1 bg-slate-900 text-white rounded-2xl py-3.5 font-black text-[10px] uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">Update Photo</button>
                <button type="button" onclick="closeEditModal()" class="px-8 bg-slate-100 text-slate-600 rounded-2xl py-3.5 font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
    .modal-active {
        opacity: 1 !important;
        pointer-events: auto !important;
    }
    .modal-active > div {
        opacity: 1 !important;
        transform: translate(0, 0) scale(1) !important;
        transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1) !important;
    }
    /* Custom Input Focus Ring */
    input:focus, textarea:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
    }
</style>

<script>
// File Select Logic
function handleFileSelect(input, statusId, dropZoneId) {
    const status = document.getElementById(statusId);
    const dropZone = document.getElementById(dropZoneId);
    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        status.textContent = fileName;
        status.classList.remove('text-slate-400');
        status.classList.add('text-primary-600');
        dropZone.classList.add('border-primary-400', 'bg-primary-50/50');
    }
}

function openAddModal() {
    const modal = document.getElementById('add_modal');
    modal.classList.add('modal-active');
}
function closeAddModal() {
    const modal = document.getElementById('add_modal');
    modal.classList.remove('modal-active');
}
function openEditModal(data) {
    document.getElementById('edit_photo_id').value = data.id;
    document.getElementById('edit_caption').value = data.caption;
    document.getElementById('edit_preview').src = '../../upload/gallery/' + data.image;
    const modal = document.getElementById('edit_modal');
    modal.classList.add('modal-active');
}
function closeEditModal() {
    const modal = document.getElementById('edit_modal');
    modal.classList.remove('modal-active');
}

// Close on outside click
window.onclick = function(event) {
    if (event.target.id === 'add_modal') closeAddModal();
    if (event.target.id === 'edit_modal') closeEditModal();
}

// ESC to close
document.addEventListener('keydown', (e) => {
    if(e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
