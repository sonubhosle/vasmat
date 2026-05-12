<?php
require_once __DIR__ . '/../../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);
include __DIR__ . '/../includes/header.php';

$uploadDir = __DIR__ . '/../../upload/faculty/';

// Faculty mapping
$facultyTypes = [
    'teaching' => ['name' => 'Teaching', 'color' => 'bg-gradient-to-r from-blue-500 to-indigo-600', 'icon' => 'fas fa-chalkboard-teacher', 'badge' => 'bg-blue-100 text-blue-800'],
    'non-teaching' => ['name' => 'Non Teaching', 'color' => 'bg-gradient-to-r from-green-500 to-emerald-600', 'icon' => 'fas fa-user-tie', 'badge' => 'bg-green-100 text-green-800'],
    'visiting' => ['name' => 'Visiting', 'color' => 'bg-gradient-to-r from-purple-500 to-violet-600', 'icon' => 'fas fa-user-clock', 'badge' => 'bg-purple-100 text-purple-800'],
    'guest' => ['name' => 'Guest', 'color' => 'bg-gradient-to-r from-amber-500 to-orange-600', 'icon' => 'fas fa-user-graduate', 'badge' => 'bg-amber-100 text-amber-800']
];

// Logic (Simplified but functional)
if(isset($_POST['add_faculty'])){
    $name = $_POST['name']; $designation = $_POST['designation']; $education = $_POST['education']; $faculty_type = $_POST['faculty_type'];
    $photoName = "";
    $hasError = false;
    if(!empty($_FILES['photo']['name'])){
        $upload = secure_upload($_FILES['photo'], ['jpg', 'jpeg', 'png'], $uploadDir);
        if ($upload['success']) {
            $photoName = $upload['filename'];
        } else {
            $_SESSION['error'] = "Upload failed: " . $upload['error'];
            $hasError = true;
        }
    }
    if (!$hasError) {
        $stmt = $conn->prepare("INSERT INTO faculty (name, designation, education, faculty_type, photo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $designation, $education, $faculty_type, $photoName);
        if($stmt->execute()) {
            $_SESSION['success'] = "Faculty added!";
        } else {
            $_SESSION['error'] = "DB Error: " . $conn->error;
        }
        $stmt->close();
    }
    header("Location: faculty.php");
    exit;
}

if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    if ($conn->query("DELETE FROM faculty WHERE id=$id")) {
        $_SESSION['success'] = "Deleted!";
    } else {
        $_SESSION['error'] = "Delete failed.";
    }
    header("Location: faculty.php");
    exit;
}

$faculty = $conn->query("SELECT * FROM faculty ORDER BY id DESC");
$totalFaculty = $conn->query("SELECT COUNT(*) as count FROM faculty")->fetch_assoc()['count'];
?>

<div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div class="max-w-3xl">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-100 text-amber-600 text-[10px] font-black uppercase tracking-[0.2em] mb-4">
            <i class="fas fa-info-circle"></i> HR Registry
        </div>
        <h2 class="text-3xl font-black text-slate-900 ">Faculty <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">Profiles</span></h2>
        <p class="text-slate-400 text-sm font-medium mt-4">Manage institutional staffing, designations, and professional biographies.</p>
    </div>
    <button onclick="openModal()" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-amber-500 transition-all shadow-xl shadow-slate-900/10 flex items-center gap-3">
        <i class="fas fa-plus"></i> New Faculty Member
    </button>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
    <div class="stat-card">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-lg float-anim">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Staff</p>
                <h3 class="text-2xl font-black text-slate-900"><?= $totalFaculty ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Faculty Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
    <?php while($row = $faculty->fetch_assoc()): 
        $type = $facultyTypes[$row['faculty_type']] ?? $facultyTypes['teaching'];
    ?>
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col hover:shadow-xl transition-all duration-500 group animate-in fade-in zoom-in-95 duration-500">
        <div class="h-40 overflow-hidden relative">
            <?php if($row['photo']): ?>
                <img src="/vasmat/upload/faculty/<?= htmlspecialchars($row['photo']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
            <?php else: ?>
                <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-user text-slate-300 text-4xl"></i>
                </div>
            <?php endif; ?>
            <div class="absolute bottom-4 left-4">
                <span class="px-3 py-1.5 rounded-xl <?= $type['badge'] ?> text-[8px] font-black uppercase tracking-widest border border-white/50 shadow-sm">
                    <i class="<?= $type['icon'] ?> mr-1"></i> <?= $type['name'] ?>
                </span>
            </div>
        </div>
        <div class="p-8">
            <h4 class="text-lg font-black text-slate-900 mb-1 tracking-tight group-hover:text-primary-600 transition-colors"><?= e($row['name']) ?></h4>
            <p class="text-xs font-bold text-primary-500 uppercase tracking-widest mb-4"><?= e($row['designation']) ?></p>
            <div class="flex items-start gap-2 mb-6">
                <i class="fas fa-graduation-cap text-slate-300 text-sm mt-1"></i>
                <p class="text-xs text-slate-500 leading-relaxed"><?= e($row['education']) ?></p>
            </div>
            
            <div class="flex gap-2 pt-6 border-t border-slate-50">
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete?')" class="flex-1 bg-rose-50 text-rose-500 rounded-xl py-2.5 text-center text-xs font-bold hover:bg-rose-500 hover:text-white transition-all">
                    <i class="fas fa-trash-alt mr-2"></i> Delete
                </a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<!-- Add Modal -->
<div id="add_modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-6 opacity-0 pointer-events-none transition-all duration-500 ease-out">
    <div id="modalContent" class="bg-white rounded-[3rem] w-full max-w-xl shadow-2xl overflow-hidden transform -translate-y-24 -translate-x-24 scale-90 opacity-0 transition-all duration-700 ease-out">
        <form method="POST" enctype="multipart/form-data" class="p-10">
            <h3 class="text-2xl font-black text-slate-900 mb-8 tracking-tight">Add New Faculty</h3>
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Name</label>
                        <input type="text" name="name" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-all" placeholder="Enter name...">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Type</label>
                        <select name="faculty_type" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-bold text-slate-700">
                            <?php foreach($facultyTypes as $k => $v): ?>
                                <option value="<?= $k ?>"><?= $v['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Designation</label>
                    <input type="text" name="designation" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-bold text-slate-700" placeholder="e.g. HOD IT">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Education</label>
                    <textarea name="education" rows="2" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-bold text-slate-700 resize-none" placeholder="Qualifications..."></textarea>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Photo</label>
                    <input type="file" name="photo" class="w-full text-xs text-slate-500 file:bg-primary-50 file:border-none file:rounded-full file:px-4 file:py-2 file:mr-4 file:text-primary-700 file:font-black">
                </div>
            </div>
            <div class="mt-10 flex gap-4">
                <button type="submit" name="add_faculty" class="flex-1 bg-slate-900 text-white rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all">Save Profile</button>
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
