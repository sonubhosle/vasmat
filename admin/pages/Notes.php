<?php
require_once __DIR__ . '/../../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);
$page_title = "Study Notes";
include __DIR__ . '/../includes/header.php';

$success = "";
$error = "";
$uploadDir = __DIR__ . '/../../upload/notes/';

// Simple Logic
if (isset($_POST['add_note'])) {
    $class = $_POST['class']; $subject = $_POST['subject_name'];
    $upload = secure_upload($_FILES['file'], ['pdf', 'doc', 'docx', 'txt', 'ppt', 'pptx'], $uploadDir);
    if ($upload['success']) {
        $stmt = $conn->prepare("INSERT INTO notes (class, subject_name, file_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $class, $subject, $upload['filename']);
        if ($stmt->execute()) $success = "Note uploaded!"; else $error = "DB Error";
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM notes WHERE id=$id");
    $success = "Deleted!";
}

$notes = $conn->query("SELECT * FROM notes ORDER BY id DESC");
?>

<div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-primary-600 mb-2 block">Academic Resources</span>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">Study <span class="text-primary-500">Notes</span></h2>
    </div>
    <button onclick="document.getElementById('add_modal').classList.remove('hidden')" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/10 flex items-center gap-3">
        <i class="fas fa-plus"></i> Upload New Note
    </button>
</div>

<!-- Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php while($row = $notes->fetch_assoc()): 
        $ext = pathinfo($row['file_path'], PATHINFO_EXTENSION);
        $icon = $ext == 'pdf' ? 'fa-file-pdf text-rose-500' : 'fa-file-word text-blue-500';
    ?>
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col hover:shadow-xl transition-all duration-500 group animate-in fade-in zoom-in-95 duration-500">
        <div class="p-8">
            <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500">
                <i class="fas <?= $icon ?> text-2xl"></i>
            </div>
            <h4 class="text-lg font-black text-slate-900 mb-1 tracking-tight"><?= e($row['subject_name']) ?></h4>
            <p class="text-xs font-bold text-primary-500 uppercase tracking-widest mb-6"><?= e($row['class']) ?></p>
            
            <div class="flex gap-2 pt-6 border-t border-slate-50">
                <a href="/vasmat/upload/notes/<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="flex-1 bg-slate-900 text-white rounded-xl py-3 text-center text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all">View File</a>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete?')" class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all">
                    <i class="fas fa-trash-alt text-xs"></i>
                </a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<!-- Add Modal -->
<div id="add_modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
    <div class="bg-white rounded-[3rem] w-full max-w-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-300">
        <form method="POST" enctype="multipart/form-data" class="p-10">
            <h3 class="text-2xl font-black text-slate-900 mb-8 tracking-tight">Upload Note</h3>
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Class</label>
                    <input type="text" name="class" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-bold text-slate-700" placeholder="e.g. BCA III">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Subject</label>
                    <input type="text" name="subject_name" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-bold text-slate-700" placeholder="e.g. Database Systems">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">File</label>
                    <input type="file" name="file" required class="w-full text-xs text-slate-500 file:bg-primary-50 file:border-none file:rounded-full file:px-4 file:py-2 file:mr-4 file:text-primary-700 file:font-black">
                </div>
            </div>
            <div class="mt-10 flex gap-4">
                <button type="submit" name="add_note" class="flex-1 bg-slate-900 text-white rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all">Upload Now</button>
                <button type="button" onclick="document.getElementById('add_modal').classList.add('hidden')" class="px-8 bg-slate-100 text-slate-600 rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>