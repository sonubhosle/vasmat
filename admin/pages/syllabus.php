<?php
require_once __DIR__ . '/../../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);
$page_title = "Curriculum Management";
include __DIR__ . '/../includes/header.php';

$uploadDir = __DIR__ . '/../../upload/syllabus/';

// Simple Logic
if (isset($_POST['add_syllabus'])) {
    $subject = $_POST['subject_name']; $year = $_POST['academic_year'];
    $upload = secure_upload($_FILES['syllabus_file'], ['pdf'], $uploadDir);
    if ($upload['success']) {
        $stmt = $conn->prepare("INSERT INTO syllabus (subject_name, academic_year, syllabus_file) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $subject, $year, $upload['filename']);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Syllabus uploaded!";
        } else {
            $_SESSION['error'] = "DB Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Upload failed: " . $upload['error'];
    }
    header("Location: syllabus.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($conn->query("DELETE FROM syllabus WHERE id=$id")) {
        $_SESSION['success'] = "Deleted successfully!";
    } else {
        $_SESSION['error'] = "Delete failed.";
    }
    header("Location: syllabus.php");
    exit;
}

$syllabus = $conn->query("SELECT * FROM syllabus ORDER BY id DESC");
?>

<div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-primary-600 mb-2 block">Curriculum Control</span>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">Academic <span class="text-primary-500">Syllabus</span></h2>
    </div>
    <button onclick="openModal()" class="bg-indigo-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/10 flex items-center gap-3 active:scale-95">
        <i class="fas fa-file-export"></i> Upload Syllabus
    </button>
</div>

<!-- List View / Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php while($row = $syllabus->fetch_assoc()): ?>
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col hover:shadow-xl transition-all duration-500 group animate-in fade-in zoom-in-95 duration-500">
        <div class="p-8">
            <div class="flex justify-between items-start mb-6">
                <div class="w-12 h-12 bg-red-50 text-red-500 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest"><?= e($row['academic_year']) ?></span>
            </div>
            
            <h4 class="text-lg font-black text-slate-900 mb-6 tracking-tight leading-tight"><?= e($row['subject_name']) ?></h4>
            
            <div class="flex items-center gap-4 pt-6 border-t border-slate-50">
                <a href="/vasmat/upload/syllabus/<?= htmlspecialchars($row['syllabus_file']) ?>" target="_blank" class="flex-1 bg-slate-900 text-white rounded-xl py-3 text-center text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all">Download PDF</a>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete?')" class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all">
                    <i class="fas fa-trash-alt text-xs"></i>
                </a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<!-- Add Modal -->
<div id="add_modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-6 opacity-0 pointer-events-none transition-all duration-500 ease-out">
    <div id="modalContent" class="bg-white rounded-[3rem] w-full max-w-lg shadow-2xl overflow-hidden transform -translate-y-24 -translate-x-24 scale-90 opacity-0 transition-all duration-700 ease-out">
        <form method="POST" enctype="multipart/form-data" class="p-10">
            <h3 class="text-2xl font-black text-slate-900 mb-8 tracking-tight">Add Syllabus</h3>
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Subject Name</label>
                    <input type="text" name="subject_name" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-bold text-slate-700" placeholder="e.g. Data Structures">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Academic Year</label>
                    <input type="text" name="academic_year" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-bold text-slate-700" placeholder="e.g. 2024-25">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">PDF File</label>
                    <input type="file" name="syllabus_file" required accept=".pdf" class="w-full text-xs text-slate-500 file:bg-indigo-50 file:border-none file:rounded-full file:px-4 file:py-2 file:mr-4 file:text-indigo-700 file:font-black">
                </div>
            </div>
            <div class="mt-10 flex gap-4">
                <button type="submit" name="add_syllabus" class="flex-1 bg-indigo-600 text-white rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all">Upload File</button>
                <button type="button" onclick="closeModal()" class="px-8 bg-slate-100 text-slate-600 rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
    .modal-active {
        opacity: 1 !important;
        pointer-events: auto !important;
    }
    .modal-active #modalContent {
        opacity: 1 !important;
        transform: translate(0, 0) scale(1) !important;
        transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1) !important;
    }
</style>

<script>
function openModal() {
    document.getElementById('add_modal').classList.add('modal-active');
}
function closeModal() {
    document.getElementById('add_modal').classList.remove('modal-active');
}
window.onclick = function(e) {
    if(e.target.id === 'add_modal') closeModal();
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>