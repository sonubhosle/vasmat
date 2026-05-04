<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole('faculty');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $type = $_POST['type'] ?? '';
    $faculty_id = $_SESSION['reference_id'];

    if (empty($title) || empty($type) || !isset($_FILES['file'])) {
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
                $stmt = $conn->prepare("INSERT INTO faculty_content (faculty_id, title, file_path, type, status) VALUES (?, ?, ?, ?, 'pending')");
                $stmt->bind_param("isss", $faculty_id, $title, $db_path, $type);
                
                if ($stmt->execute()) {
                    $success = "Content uploaded successfully and is pending approval.";
                    logActivity($conn, $_SESSION['user_id'], 'Upload', 'Faculty uploaded content: ' . $title);
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Content | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="flex">
        <!-- Sidebar - Same as Dashboard -->
        <aside class="w-64 bg-slate-900 min-h-screen text-slate-300 p-6 flex flex-col fixed h-full">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white font-black text-xl">M</div>
                <div>
                    <h1 class="text-white font-black text-sm uppercase tracking-tight">MIT College</h1>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Faculty Portal</p>
                </div>
            </div>
            <nav class="flex-1 space-y-2">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="upload.php" class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 text-amber-500 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-cloud-upload-alt"></i> Upload Content
                </a>
                <a href="my-uploads.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-file-alt"></i> My Uploads
                </a>
                <a href="profile.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-user-circle"></i> Profile Settings
                </a>
            </nav>
            <div class="mt-auto">
                <a href="../auth/logout.php" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-400/10 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-10">
            <header class="mb-10">
                <a href="dashboard.php" class="text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-amber-500 transition-all flex items-center gap-2 mb-4">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Upload New Content</h2>
                <p class="text-slate-500 font-medium mt-1">Submit notes, syllabus or timetables for review.</p>
            </header>

            <div class="max-w-2xl">
                <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <?php if ($error): ?>
                        <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-sm font-bold mb-6 border border-red-100 flex items-center gap-3">
                            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="bg-green-50 text-green-600 p-4 rounded-2xl text-sm font-bold mb-6 border border-green-100 flex items-center gap-3">
                            <i class="fas fa-check-circle"></i> <?= $success ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-8">
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-3 px-1">Content Title</label>
                            <input type="text" name="title" required 
                                   class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm font-semibold text-slate-700" 
                                   placeholder="e.g. Data Structures Unit 1 Notes">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-3 px-1">Content Type</label>
                            <div class="grid grid-cols-3 gap-4">
                                <label class="cursor-pointer group">
                                    <input type="radio" name="type" value="notes" class="hidden peer" checked>
                                    <div class="p-4 border-2 border-slate-100 rounded-2xl text-center peer-checked:border-amber-500 peer-checked:bg-amber-50 transition-all group-hover:border-amber-200">
                                        <i class="fas fa-book text-slate-400 group-hover:text-amber-500 transition-all peer-checked:text-amber-600 mb-2 block"></i>
                                        <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Notes</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer group">
                                    <input type="radio" name="type" value="syllabus" class="hidden peer">
                                    <div class="p-4 border-2 border-slate-100 rounded-2xl text-center peer-checked:border-amber-500 peer-checked:bg-amber-50 transition-all group-hover:border-amber-200">
                                        <i class="fas fa-list-alt text-slate-400 group-hover:text-amber-500 transition-all peer-checked:text-amber-600 mb-2 block"></i>
                                        <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Syllabus</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer group">
                                    <input type="radio" name="type" value="timetable" class="hidden peer">
                                    <div class="p-4 border-2 border-slate-100 rounded-2xl text-center peer-checked:border-amber-500 peer-checked:bg-amber-50 transition-all group-hover:border-amber-200">
                                        <i class="fas fa-calendar-alt text-slate-400 group-hover:text-amber-500 transition-all peer-checked:text-amber-600 mb-2 block"></i>
                                        <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Timetable</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-3 px-1">Select File (PDF/DOC)</label>
                            <div class="relative group">
                                <input type="file" name="file" required 
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="w-full px-5 py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[2rem] flex flex-col items-center justify-center group-hover:border-amber-300 transition-all">
                                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-slate-400 mb-3 shadow-sm group-hover:text-amber-500 transition-all">
                                        <i class="fas fa-file-upload text-xl"></i>
                                    </div>
                                    <span class="text-sm font-bold text-slate-500">Drag & drop or click to browse</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Max Size: 5MB</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" 
                                class="w-full py-5 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-black rounded-2xl shadow-xl shadow-amber-500/20 hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest text-sm">
                            Submit for Approval
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
