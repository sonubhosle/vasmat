<?php 
include "includes/header.php"; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_doc'])) {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $year = $_POST['year'];
    
    // Simple file upload logic
    $target_dir = "../uploads/naac/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $file_name = time() . "_" . basename($_FILES["file"]["name"]);
    $target_file = $target_dir . $file_name;
    $db_path = "uploads/naac/" . $file_name;
    
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO naac_docs (title, category, year, file_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $category, $year, $db_path);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Document uploaded successfully!";
        } else {
            $_SESSION['error'] = "DB Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "File upload failed.";
    }
    header("Location: naac_admin.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM naac_docs WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Document deleted!";
    } else {
        $_SESSION['error'] = "Delete failed.";
    }
    $stmt->close();
    header("Location: naac_admin.php");
    exit();
}
?>

<main class="p-8">
    <div class="flex justify-between items-center mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">NAAC Document Management</h2>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Manage institutional reports and statutory filings</p>
        </div>
        <button onclick="openModal()" class="px-8 py-4 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-xl hover:bg-amber-500 transition-all flex items-center gap-3">
            <i class="fas fa-plus"></i> Upload New Doc
        </button>
    </div>

    <!-- Toast Alerts managed by global header -->

    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-900/5 border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50/50">
                <tr>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Document Title</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Category</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Academic Year</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php 
                $docs = $conn->query("SELECT * FROM naac_docs ORDER BY id DESC");
                if ($docs && $docs->num_rows > 0):
                    while($d = $docs->fetch_assoc()):
                ?>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-3">
                            <i class="far fa-file-pdf text-rose-500 text-lg"></i>
                            <span class="font-bold text-slate-700"><?= $d['title'] ?></span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black uppercase rounded-lg border border-amber-100"><?= $d['category'] ?></span>
                    </td>
                    <td class="px-8 py-6 text-sm font-bold text-slate-500"><?= $d['year'] ?></td>
                    <td class="px-8 py-6 text-right space-x-2">
                        <a href="../<?= $d['file_path'] ?>" target="_blank" class="w-10 h-10 inline-flex items-center justify-center bg-blue-50 text-blue-500 rounded-xl hover:bg-blue-500 hover:text-white transition-all"><i class="fas fa-eye"></i></a>
                        <a href="?delete=<?= $d['id'] ?>" onclick="return confirm('Delete document?')" class="w-10 h-10 inline-flex items-center justify-center bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php 
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center">
                        <div class="opacity-20 mb-4 text-4xl"><i class="fas fa-folder-open"></i></div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">No documents found in repository</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-500 ease-out">
    <div id="modalContent" class="bg-white p-10 rounded-[3.5rem] w-full max-w-lg shadow-2xl relative transform -translate-y-24 -translate-x-24 scale-90 opacity-0 transition-all duration-700 ease-out">
        <button onclick="closeModal()" class="absolute top-8 right-8 text-slate-300 hover:text-slate-900 transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>
        
        <h3 class="text-2xl font-black text-slate-900 mb-6 uppercase tracking-tight">Upload <span class="text-amber-500">Document</span></h3>
        
        <form id="uploadForm" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div class="space-y-1.5">
                <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest ml-4">Document Title</label>
                <input type="text" name="title" required placeholder="e.g. AQAR Report 2023" class="w-full px-8 py-4 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 font-bold text-sm transition-all duration-300" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest ml-4">Category</label>
                    <div class="relative" id="catDropdown">
                        <button type="button" onclick="toggleDropdown('cat')" class="w-full px-8 py-4 bg-slate-50 border border-slate-100 rounded-3xl text-sm font-bold text-slate-700 flex items-center justify-between hover:bg-slate-100 transition-all outline-none">
                            <span id="catLabel">Select Category</span>
                            <i class="fas fa-chevron-down text-slate-300 text-[10px] transition-transform" id="catChevron"></i>
                        </button>
                        <input type="hidden" name="category" id="catValue" required>
                        <div id="catMenu" class="absolute top-full left-0 right-0 mt-3 bg-white border border-slate-100 rounded-[2rem] shadow-2xl z-50 overflow-hidden py-2 opacity-0 pointer-events-none scale-95 origin-top transition-all duration-300 ease-out">
                            <?php foreach(['SSR', 'AQAR', 'Minutes', 'Policy', 'Disclosure', 'Certificate'] as $cat): ?>
                            <div onclick="selectOption('cat','<?= $cat ?>','<?= $cat ?>')" class="px-8 py-2.5 text-xs font-bold text-slate-500 hover:bg-slate-50 hover:text-slate-900 cursor-pointer transition-all">
                                <?= $cat ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest ml-4">Year</label>
                    <input type="text" name="year" required placeholder="2023-24" class="w-full px-8 py-4 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 font-bold text-sm transition-all duration-300" />
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[9px] font-black uppercase text-slate-400 tracking-widest ml-4">PDF Document</label>
                <div class="relative group">
                    <input type="file" name="file" id="fileInput" accept=".pdf" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="handleFileSelect(this)" />
                    <div id="dropZone" class="w-full py-4 px-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[2rem] flex items-center gap-4 group-hover:border-amber-400 group-hover:bg-amber-50/30 transition-all duration-500">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-slate-300 group-hover:text-amber-500 group-hover:scale-110 transition-all duration-500 shrink-0">
                            <i class="fas fa-cloud-arrow-up text-lg"></i>
                        </div>
                        <div class="text-left">
                            <p id="fileStatus" class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Click or Drag PDF</p>
                            <p class="text-[8px] font-bold text-slate-300 uppercase tracking-tighter">Max: 10MB</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-2">
                <button type="submit" name="upload_doc" class="flex-1 py-5 bg-slate-900 text-white rounded-[2rem] text-[10px] font-black uppercase tracking-widest hover:bg-amber-500 transition-all shadow-xl shadow-slate-900/20 active:scale-[0.98]">
                    Upload to Repository
                </button>
            </div>
        </form>
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

    /* Custom Input Focus Ring */
    input:focus, textarea:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
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
}

// Custom Dropdown Logic
function toggleDropdown(id) {
    const menu = document.getElementById(id + 'Menu');
    const chevron = document.getElementById(id + 'Chevron');
    const isActive = menu.classList.contains('opacity-100');
    
    // Close all other menus first if any
    document.querySelectorAll('[id$="Menu"]').forEach(m => {
        m.classList.remove('opacity-100', 'pointer-events-auto', 'scale-100');
        m.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
    });
    document.querySelectorAll('[id$="Chevron"]').forEach(c => c.style.transform = 'rotate(0deg)');

    if(!isActive) {
        menu.classList.remove('opacity-0', 'pointer-events-none', 'scale-95');
        menu.classList.add('opacity-100', 'pointer-events-auto', 'scale-100');
        chevron.style.transform = 'rotate(180deg)';
    }
}

function selectOption(id, val, label) {
    document.getElementById(id + 'Value').value = val;
    document.getElementById(id + 'Label').textContent = label;
    document.getElementById(id + 'Label').classList.add('text-slate-900');
    
    const menu = document.getElementById(id + 'Menu');
    const chevron = document.getElementById(id + 'Chevron');
    menu.classList.remove('opacity-100', 'pointer-events-auto', 'scale-100');
    menu.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
    chevron.style.transform = 'rotate(0deg)';
}

// File Select Logic
function handleFileSelect(input) {
    const status = document.getElementById('fileStatus');
    const dropZone = document.getElementById('dropZone');
    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        status.textContent = fileName;
        status.classList.remove('text-slate-400');
        status.classList.add('text-amber-600');
        dropZone.classList.add('border-amber-400', 'bg-amber-50/50');
    }
}

// Close dropdown on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('#catDropdown')) {
        const menu = document.getElementById('catMenu');
        const chevron = document.getElementById('catChevron');
        if(menu) {
            menu.classList.remove('opacity-100', 'pointer-events-auto', 'scale-100');
            menu.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
        }
        if(chevron) chevron.style.transform = 'rotate(0deg)';
    }
});

// Close modal on outside click
document.addEventListener('click', function(event) {
    const modal = document.getElementById('uploadModal');
    if (event.target === modal) {
        closeModal();
    }
});
</script>

<?php include "includes/footer.php"; ?>
