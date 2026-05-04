<?php
include '../includes/header.php';
include_once '../../admin/includes/functions.php';

$success = "";
$error = "";

// ADD PHOTO
if (isset($_POST['add_photo'])) {
    $caption = $_POST['caption'];
    
    $uploadDir = __DIR__ . '/../../upload/gallery/';
    $upload = secure_upload($_FILES['photo'], ['jpg', 'jpeg', 'png', 'gif'], $uploadDir);

    if ($upload['success']) {
        $fileName = $upload['filename'];
        $stmt = $conn->prepare("INSERT INTO gallery (image, caption) VALUES (?, ?)");
        $stmt->bind_param("ss", $fileName, $caption);
        if ($stmt->execute()) {
            $success = "📸 Photo added to gallery successfully!";
        } else {
            $error = "Database error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error = "Upload failed: " . $upload['error'];
    }
}

// DELETE PHOTO
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = $conn->prepare("SELECT image FROM gallery WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($row = $result->fetch_assoc()){
        $filePath = __DIR__ . '/../../upload/gallery/' . $row['image'];
        if(file_exists($filePath)) @unlink($filePath);
        
        $del_stmt = $conn->prepare("DELETE FROM gallery WHERE id=?");
        $del_stmt->bind_param("i", $id);
        $del_stmt->execute();
        $success = "🗑️ Photo removed from gallery.";
    }
    $stmt->close();
}

$photos = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800">Gallery Management</h1>
            <p class="text-slate-500">Upload and manage campus photos</p>
        </div>
        <button onclick="document.getElementById('addPhotoModal').classList.remove('hidden')" 
                class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 hover:bg-slate-800 transition-all">
            <i class='bx bx-plus-circle'></i> Add New Photo
        </button>
    </div>

    <?php if ($success): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl font-bold flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
            <i class='bx bx-check-circle text-xl'></i> <?= $success ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-600 rounded-2xl font-bold flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
            <i class='bx bx-error-circle text-xl'></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php while($photo = $photos->fetch_assoc()): ?>
            <div class="group bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl transition-all relative">
                <div class="aspect-square overflow-hidden relative">
                    <img src="../../upload/gallery/<?= htmlspecialchars($photo['image']) ?>" 
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                        <a href="?delete=<?= $photo['id'] ?>" 
                           onclick="return confirm('Remove this photo permanently?')"
                           class="w-10 h-10 bg-rose-500 text-white rounded-xl flex items-center justify-center hover:bg-rose-600 transition-all">
                            <i class='bx bx-trash'></i>
                        </a>
                    </div>
                </div>
                <div class="p-4 bg-white">
                    <p class="text-sm font-bold text-slate-700 line-clamp-2"><?= htmlspecialchars($photo['caption'] ?: 'Untitled Photot') ?></p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2"><?= date('d M Y', strtotime($photo['created_at'])) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Add Photo Modal -->
<div id="addPhotoModal" class="hidden fixed inset-0 z-50  items-center justify-center p-6">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="bg-white rounded-[2.5rem] p-10 w-full max-w-xl relative animate-in zoom-in-95 duration-300">
        <h2 class="text-3xl font-black text-slate-900 mb-2">Upload Photo</h2>
        <p class="text-slate-500 mb-8 whitespace-nowrap">Add a new image to the public gallery.</p>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Photo File</label>
                <input type="file" name="photo" required 
                       class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
            </div>

            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Caption (Optional)</label>
                <input type="text" name="caption" placeholder="Describe this photo..." 
                       class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:outline-none focus:ring-4 focus:ring-amber-400/10 focus:border-amber-400 transition-all font-medium">
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" onclick="this.closest('#addPhotoModal').classList.add('hidden')" 
                        class="flex-1 px-8 py-4 border border-slate-200 text-slate-500 font-bold rounded-2xl hover:bg-slate-50 transition-all uppercase tracking-widest text-xs">Cancel</button>
                <button type="submit" name="add_photo" 
                        class="flex-1 px-8 py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-slate-800 shadow-xl shadow-slate-900/20 transition-all uppercase tracking-widest text-xs">Upload Now</button>
            </div>
        </form>
    </div>
</div>

<?php 
// Active page styling helper
?>
<script>
    document.querySelectorAll('.menu-item').forEach(item => {
        if(item.dataset.page === 'gallery') {
            item.classList.add('bg-white/10', 'text-amber-400', 'border-r-4', 'border-amber-400');
        }
    });
</script>
