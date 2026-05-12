<?php
include 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $type = $_POST['type'] ?? 'notes'; // Default to notes

    if (empty($title) || empty($description) || empty($type) || !isset($_FILES['file'])) {
        $error = "Please fill in all fields and select a file.";
    } else {
        $file = $_FILES['file'];
        $filename = time() . '_' . basename($file['name']);
        $target_dir = "../upload/faculty/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $target_path = $target_dir . $filename;
        $file_type = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
        
        // Allowed file types
        $allowed = ['pdf', 'doc', 'docx'];
        
        if (!in_array($file_type, $allowed)) {
            $error = "Only PDF and DOC files are allowed.";
        } elseif ($file['size'] > 5000000) { // 5MB limit
            $error = "File size exceeds 5MB limit.";
        } else {
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                $db_path = "upload/faculty/" . $filename;
                $stmt = $conn->prepare("INSERT INTO faculty_content (faculty_id, title, description, file_path, type, status) VALUES (?, ?, ?, ?, ?, 'pending')");
                $stmt->bind_param("issss", $faculty_id, $title, $description, $db_path, $type);
                
                if ($stmt->execute()) {
                    $success = "Content uploaded successfully and is pending approval.";
                    logActivity($conn, $_SESSION['user_id'], 'Upload', 'Faculty uploaded ' . $type . ': ' . $title);
                    
                    // Notify Admins
                    $admins = $conn->query("SELECT id FROM users WHERE role IN ('admin', 'superadmin')");
                    while($adm = $admins->fetch_assoc()) {
                        addNotification($conn, $adm['id'], 'New Content Submission', $faculty_user['name'] . ' submitted a new ' . $type . ': ' . $title);
                    }
                } else {
                    $error = "Database error: " . $conn->error;
                }
                $stmt->close();
            } else {
                $error = "Failed to upload file.";
            }
        }
    }
}
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
    <div class="max-w-3xl">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-100 text-amber-600 text-[10px] font-black uppercase tracking-[0.2em] mb-4">
            <i class="fas fa-info-circle"></i> Resource Hub
        </div>
        <h2 class="text-3xl font-black text-slate-900 ">Material <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">Upload</span></h2>
        <p class="text-slate-500 font-medium mt-4 text-sm">Publish syllabus, notes, or academic notices to the portal.</p>
    </div>
    <a href="my-uploads.php" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-900 hover:text-white transition-all flex items-center gap-2 shadow-sm">
        <i class="fas fa-history"></i> Upload History
    </a>
</div>

            <div class="max-w-4xl">
                <!-- Content Tabs (Admin Style) -->
                <div class="flex gap-3 mb-10 flex-wrap">
                    <button type="button" onclick="switchTab('notes')" id="tab-notes" 
                            class="upload-tab flex items-center gap-2.5 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all tab-active shadow-xl shadow-slate-900/10">
                        <i class="fas fa-book"></i> Study Notes
                    </button>
                    <button type="button" onclick="switchTab('syllabus')" id="tab-syllabus" 
                            class="upload-tab flex items-center gap-2.5 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all tab-inactive">
                        <i class="fas fa-graduation-cap"></i> Syllabus
                    </button>
                    <button type="button" onclick="switchTab('timetable')" id="tab-timetable" 
                            class="upload-tab flex items-center gap-2.5 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all tab-inactive">
                        <i class="fas fa-calendar-alt"></i> Timetable
                    </button>
                    <button type="button" onclick="switchTab('circulars')" id="tab-circulars" 
                            class="upload-tab flex items-center gap-2.5 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all tab-inactive">
                        <i class="fas fa-bullhorn"></i> Circulars
                    </button>
                </div>

                <div class="bg-white p-12 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/50">
                    <?php if ($error): ?>
                        <div class="bg-rose-50 text-rose-600 p-5 rounded-2xl text-sm font-bold mb-8 border border-rose-100 flex items-center gap-4">
                            <i class="fas fa-exclamation-circle text-lg"></i> <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="bg-emerald-50 text-emerald-600 p-5 rounded-2xl text-sm font-bold mb-8 border border-emerald-100 flex items-center gap-4">
                            <i class="fas fa-check-circle text-lg"></i> <?= $success ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-10">
                        <input type="hidden" name="type" id="content-type" value="notes">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3 md:col-span-2">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Material Title</label>
                                <div class="relative">
                                    <i class="fas fa-heading absolute left-5 top-1/2 -translate-y-1/2 text-slate-400" id="title-icon"></i>
                                    <input type="text" name="title" required 
                                           class="w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-semibold outline-none" 
                                           placeholder="e.g. Advanced Operating Systems - Unit 1">
                                </div>
                            </div>

                            <div class="space-y-3 md:col-span-2">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Detailed Description</label>
                                <textarea name="description" required rows="3" 
                                          class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-medium outline-none resize-none" 
                                          placeholder="Provide a brief summary of the content for students..."></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1 mb-3">Document Attachment</label>
                                <div class="relative group">
                                    <input type="file" name="file" required onchange="updateFileName(this)"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                    <div class="w-full px-6 py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[2rem] flex flex-col items-center justify-center group-hover:border-amber-300 group-hover:bg-amber-50/30 transition-all">
                                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-slate-400 mb-4 shadow-sm group-hover:text-amber-500 group-hover:scale-110 transition-all border border-slate-100">
                                            <i class="fas fa-file-upload text-xl"></i>
                                        </div>
                                        <p id="file-label" class="text-xs font-black text-slate-600 transition-all uppercase tracking-widest">Select PDF or DOC File</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-2">Max 5MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-10">
                            <button type="submit" 
                                    class="w-full py-6 bg-slate-900 text-white font-black rounded-3xl shadow-2xl hover:bg-slate-800 hover:scale-[1.01] active:scale-95 transition-all uppercase tracking-[0.2em] text-sm flex items-center justify-center gap-3">
                                <i class="fas fa-rocket"></i> Submit <span id="btn-text">Study Notes</span> for Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function switchTab(type) {
            // Update hidden input
            document.getElementById('content-type').value = type;
            
            // Update Tab UI
            document.querySelectorAll('.upload-tab').forEach(tab => {
                tab.classList.remove('tab-active');
                tab.classList.add('tab-inactive');
            });
            document.getElementById('tab-' + type).classList.remove('tab-inactive');
            document.getElementById('tab-' + type).classList.add('tab-active');
            
            // Update Helper Texts
            const labels = {
                'notes': 'Study Notes',
                'syllabus': 'Syllabus',
                'timetable': 'Timetable',
                'circulars': 'Circulars'
            };
            document.getElementById('btn-text').innerText = labels[type];
            
            // Optional: Change icon based on type
            const iconMap = {
                'notes': 'fa-book',
                'syllabus': 'fa-graduation-cap',
                'timetable': 'fa-calendar-alt',
                'circulars': 'fa-bullhorn'
            };
            document.getElementById('title-icon').className = 'fas ' + iconMap[type] + ' absolute left-5 top-1/2 -translate-y-1/2 text-slate-400';
        }

        function updateFileName(input) {
            const label = document.getElementById('file-label');
            if (input.files && input.files[0]) {
                label.innerText = "Selected: " + input.files[0].name;
                label.classList.add('text-amber-600');
            }
        }
    </script>
<?php include 'includes/footer.php'; ?>
