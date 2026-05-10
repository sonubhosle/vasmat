<?php
require_once __DIR__ . '/../../includes/auth_helper.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

checkRole(['admin', 'superadmin']);

$success = '';
$error = '';

// Handle File Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_calendar'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $_SESSION['error'] = "CSRF token validation failed.";
    } else {
        $title = trim($_POST['title']);
        $academic_year = trim($_POST['academic_year']);
        $file = $_FILES['calendar_file'];

        if (empty($title) || empty($academic_year) || empty($file['name'])) {
            $_SESSION['error'] = "All fields are required.";
        } else {
            $upload_dir = __DIR__ . '/../../upload/academic_calendars/';
            $upload_result = secure_upload($file, ['pdf'], $upload_dir);

            if ($upload_result['success']) {
                $file_path = 'upload/academic_calendars/' . $upload_result['filename'];
                $stmt = $conn->prepare("INSERT INTO academic_calendars (title, academic_year, file_path) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $title, $academic_year, $file_path);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Academic Calendar uploaded successfully.";
                    logActivity($conn, $_SESSION['user_id'], 'Upload Calendar', "Uploaded academic calendar: $title ($academic_year)");
                } else {
                    $_SESSION['error'] = "Database error: " . $conn->error;
                }
                $stmt->close();
            } else {
                $_SESSION['error'] = $upload_result['error'];
            }
        }
    }
    header("Location: academic_calendar.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Get file path first to delete it from disk
    $stmt = $conn->prepare("SELECT file_path, title FROM academic_calendars WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $file_path = __DIR__ . '/../../' . $row['file_path'];
        $title = $row['title'];
        
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        $del_stmt = $conn->prepare("DELETE FROM academic_calendars WHERE id = ?");
        $del_stmt->bind_param("i", $delete_id);
        if ($del_stmt->execute()) {
            $_SESSION['success'] = "Academic Calendar deleted successfully.";
            logActivity($conn, $_SESSION['user_id'], 'Delete Calendar', "Deleted academic calendar: $title");
        } else {
            $_SESSION['error'] = "Failed to delete from database.";
        }
    }
    $stmt->close();
    header("Location: academic_calendar.php");
    exit;
}

// Handle Toggle Status
if (isset($_GET['toggle_id'])) {
    $toggle_id = intval($_GET['toggle_id']);
    $new_status = intval($_GET['status']) ? 0 : 1;
    
    $stmt = $conn->prepare("UPDATE academic_calendars SET is_active = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_status, $toggle_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Calendar status updated.";
    } else {
        $_SESSION['error'] = "Failed to update status.";
    }
    $stmt->close();
    header("Location: academic_calendar.php");
    exit;
}

// Fetch Calendars
$calendars = $conn->query("SELECT * FROM academic_calendars ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);


include '../includes/header.php';
?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-primary-600 mb-2 block">Academic Resources</span>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">Academic <span class="text-primary-500">Calendars</span></h2>
        <p class="text-slate-400 text-sm font-medium mt-2">Upload and manage institutional academic schedules.</p>
    </div>
    <button onclick="openModal()" class="bg-slate-900 text-white rounded-2xl px-6 py-4 text-xs font-black uppercase tracking-widest hover:bg-primary-600 hover:scale-105 transition-all shadow-xl shadow-slate-900/10 flex items-center gap-3">
        <i class="fas fa-plus"></i> Upload New Calendar
    </button>
</div>

<!-- Toast Alerts managed by global header -->

<!-- Calendars List -->
<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <?php if (empty($calendars)): ?>
    <div class="py-24 text-center">
        <div class="w-20 h-20 bg-slate-50 border border-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-200">
            <i class="fas fa-calendar-xmark text-4xl"></i>
        </div>
        <p class="text-slate-900 font-black uppercase text-xs tracking-widest mb-2">No Calendars Uploaded</p>
        <p class="text-slate-400 text-xs font-medium">Get started by uploading your first academic calendar.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Calendar Details</th>
                    <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Academic Year</th>
                    <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($calendars as $cal): ?>
                <tr class="hover:bg-slate-50/50 transition-all group">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center text-lg shadow-sm">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-900 leading-tight"><?= e($cal['title']) ?></p>
                                <p class="text-[10px] font-bold text-slate-400 mt-1">Uploaded on <?= date('M d, Y', strtotime($cal['created_at'])) ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest">
                            <?= e($cal['academic_year']) ?>
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <a href="?toggle_id=<?= $cal['id'] ?>&status=<?= $cal['is_active'] ?>" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest transition-all <?= $cal['is_active'] ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-100 text-slate-400 border border-slate-200' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $cal['is_active'] ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' ?>"></span>
                            <?= $cal['is_active'] ? 'Active' : 'Inactive' ?>
                        </a>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="../../<?= $cal['file_path'] ?>" target="_blank" class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="View PDF">
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                            <a href="?delete_id=<?= $cal['id'] ?>" onclick="return confirm('Are you sure you want to delete this calendar?')" class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm" title="Delete">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-500 ease-out">
    <div id="modalContent" class="bg-white rounded-[2.5rem] w-full max-w-md shadow-2xl overflow-hidden transform -translate-y-24 -translate-x-24 scale-90 opacity-0 transition-all duration-700 ease-out">
        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Upload Calendar</h3>
                    <p class="text-slate-400 text-[10px] font-medium uppercase tracking-widest">New PDF Schedule</p>
                </div>
                <button onclick="closeModal()" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-500 hover:text-white transition-all flex items-center justify-center">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <?= csrf_field() ?>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Calendar Title</label>
                        <input type="text" name="title" required placeholder="Title..." class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/10 focus:border-primary-500 transition-all">
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Academic Year</label>
                        <input type="text" name="academic_year" required placeholder="e.g. 2024-25" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/10 focus:border-primary-500 transition-all">
                    </div>
                </div>

                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">PDF Document</label>
                    <div class="relative group">
                        <input type="file" name="calendar_file" id="calendar_file" accept=".pdf" required onchange="previewFile()" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div id="dropzone" class="bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center group-hover:border-primary-500 group-hover:bg-primary-50/30 transition-all">
                            <div id="previewArea">
                                <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center mx-auto mb-2 text-slate-400 group-hover:text-primary-500 transition-all">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Select PDF</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" name="upload_calendar" class="w-full bg-slate-900 text-white rounded-2xl py-4 text-[10px] font-black uppercase tracking-widest hover:bg-primary-600 hover:shadow-xl transition-all active:scale-[0.98]">
                        Upload Calendar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    #uploadModal.active {
        opacity: 1;
        pointer-events: auto;
    }
    #uploadModal.active #modalContent {
        opacity: 1;
        transform: translate(0, 0) scale(1);
        transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1);
    }
</style>

<script>
function openModal() {
    const modal = document.getElementById('uploadModal');
    if(modal) modal.classList.add('active');
}

function closeModal() {
    const modal = document.getElementById('uploadModal');
    if(modal) modal.classList.remove('active');
    // Reset preview
    document.getElementById('previewArea').innerHTML = `
        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center mx-auto mb-2 text-slate-400 group-hover:text-primary-500 transition-all">
            <i class="fas fa-file-pdf"></i>
        </div>
        <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Select PDF</p>
    `;
}

function previewFile() {
    const fileInput = document.getElementById('calendar_file');
    const previewArea = document.getElementById('previewArea');
    
    if (fileInput.files && fileInput.files[0]) {
        const file = fileInput.files[0];
        previewArea.innerHTML = `
            <div class="w-10 h-10 bg-emerald-50 text-emerald-500 rounded-xl shadow-sm flex items-center justify-center mx-auto mb-2 transition-all animate-bounce">
                <i class="fas fa-check"></i>
            </div>
            <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest truncate px-4">${file.name}</p>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tight mt-1">${(file.size / 1024).toFixed(1)} KB</p>
        `;
    }
}

// Close modal on outside click
document.addEventListener('click', function(event) {
    const modal = document.getElementById('uploadModal');
    if (event.target === modal) {
        closeModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?>
