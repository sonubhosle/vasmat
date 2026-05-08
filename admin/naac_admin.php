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
        $stmt->execute();
        $success = "Document uploaded successfully!";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM naac_docs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
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
        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="px-8 py-4 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-xl hover:bg-amber-500 transition-all flex items-center gap-3">
            <i class="fas fa-plus"></i> Upload New Doc
        </button>
    </div>

    <?php if(isset($success)): ?>
        <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl mb-8 font-bold border border-emerald-100 flex items-center gap-3">
            <i class="fas fa-check-circle"></i> <?= $success ?>
        </div>
    <?php endif; ?>

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
<div id="uploadModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-[100] animate-in fade-in duration-300">
    <div class="bg-white p-12 rounded-[4rem] w-full max-w-lg shadow-2xl relative">
        <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="absolute top-8 right-8 text-slate-300 hover:text-slate-900 transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>
        
        <h3 class="text-3xl font-black text-slate-900 mb-8 uppercase tracking-tight">Upload <span class="text-amber-500">Document</span></h3>
        
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-4">Document Title</label>
                <input type="text" name="title" required placeholder="e.g. AQAR Report 2023" class="w-full px-8 py-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:border-amber-400 font-bold text-sm" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-4">Category</label>
                    <select name="category" class="w-full px-8 py-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:border-amber-400 font-bold text-sm">
                        <option>SSR</option><option>AQAR</option><option>Minutes</option><option>Policy</option><option>Disclosure</option><option>Certificate</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-4">Year</label>
                    <input type="text" name="year" placeholder="2023-24" class="w-full px-8 py-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none focus:border-amber-400 font-bold text-sm" />
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-4">PDF Document</label>
                <div class="relative">
                    <input type="file" name="file" accept=".pdf" required class="w-full px-8 py-5 bg-slate-50 border border-slate-100 rounded-3xl outline-none font-bold text-xs" />
                </div>
            </div>
            <div class="flex gap-4 pt-6">
                <button type="submit" name="upload_doc" class="flex-1 py-6 bg-slate-900 text-white rounded-[2rem] text-[10px] font-black uppercase tracking-widest hover:bg-amber-500 transition-all shadow-xl shadow-slate-900/20">
                    Upload to Repository
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>
