<?php
include '../includes/db.php';
include '../includes/header.php';
$success = "";
$error = "";

// ADD NOTE
if (isset($_POST['add_note'])) {
    $class = $conn->real_escape_string($_POST['class']);
    $subject = $conn->real_escape_string($_POST['subject_name']);
    $desc = $conn->real_escape_string($_POST['description']);
    $semester = $conn->real_escape_string($_POST['semester']);
    $created_by = $conn->real_escape_string($_POST['created_by']);

    $uploadDir = __DIR__ . '/../../uploads/notes/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . "_" . $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];

    if (move_uploaded_file($tmp, $uploadDir . $fileName)) {
        $sql = "INSERT INTO notes (class, subject_name, description, file_path, semester, created_by)
                VALUES ('$class', '$subject', '$desc', '$fileName', '$semester', '$created_by')";
        if ($conn->query($sql)) {
            $success = "📚 Note added successfully!";
        } else {
            $error = "Database error!";
        }
    } else {
        $error = "File upload failed!";
    }
}

// DELETE NOTE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $res = $conn->query("SELECT file_path FROM notes WHERE id=$id");
    if($row = $res->fetch_assoc()){
        @unlink(__DIR__ . '/../../uploads/notes/' . $row['file_path']);
    }

    if ($conn->query("DELETE FROM notes WHERE id=$id")) {
        $success = "🗑️ Note deleted successfully!";
    } else {
        $error = "Delete failed!";
    }
}

// UPDATE NOTE
if (isset($_POST['update_note'])) {
    $id = intval($_POST['id']);
    $class = $conn->real_escape_string($_POST['class']);
    $subject = $conn->real_escape_string($_POST['subject_name']);
    $desc = $conn->real_escape_string($_POST['description']);
    $semester = $conn->real_escape_string($_POST['semester']);
    $created_by = $conn->real_escape_string($_POST['created_by']);

    $fileUpdate = "";

    if (!empty($_FILES['file']['name'])) {
        $res = $conn->query("SELECT file_path FROM notes WHERE id=$id");
        if($row = $res->fetch_assoc()){
            @unlink(__DIR__ . '/../../uploads/notes/' . $row['file_path']);
        }

        $uploadDir = __DIR__ . '/../../uploads/notes/';
        $fileName = time() . "_" . $_FILES['file']['name'];
        $tmp = $_FILES['file']['tmp_name'];
        move_uploaded_file($tmp, $uploadDir . $fileName);
        $fileUpdate = ", file_path='$fileName'";
    }

    $sql = "UPDATE notes SET 
            class='$class',
            subject_name='$subject',
            description='$desc',
            semester='$semester',
            created_by='$created_by'
            $fileUpdate
            WHERE id=$id";

    if($conn->query($sql)){
        $success = "✨ Note updated successfully!";
    } else {
        $error = "Update failed!";
    }
}

// FETCH
$notes = $conn->query("SELECT * FROM notes ORDER BY id DESC");
$totalNotes = $conn->query("SELECT COUNT(*) as count FROM notes")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        .animate-slide-in-right {
            animation: slideInRight 0.3s ease-out;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
        }

        .file-upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .file-upload-area:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .loading-overlay.active {
            opacity: 1;
            pointer-events: all;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="text-slate-600 font-medium">Processing...</p>
        </div>
    </div>

    <!-- Success Notification -->
    <?php if ($success): ?>
    <div id="successToast" class="fixed bottom-6 right-6 z-50 animate-slide-in-right">
        <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white px-6 py-4 rounded-xl shadow-2xl border-l-4 border-emerald-700 max-w-md">
            <div class="flex items-center space-x-3">
                <i class="fas fa-check-circle text-xl"></i>
                <span class="font-semibold"><?= htmlspecialchars($success) ?></span>
            </div>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('successToast');
            if(toast) {
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => toast.remove(), 500);
            }
        }, 3000);
    </script>
    <?php endif; ?>

    <!-- Error Notification -->
    <?php if ($error): ?>
    <div id="errorToast" class="fixed bottom-6 right-6 z-50 animate-slide-in-right">
        <div class="bg-gradient-to-r from-rose-500 to-red-600 text-white px-6 py-4 rounded-xl shadow-2xl border-l-4 border-red-700 max-w-md">
            <div class="flex items-center space-x-3">
                <i class="fas fa-exclamation-circle text-xl"></i>
                <span class="font-semibold"><?= htmlspecialchars($error) ?></span>
            </div>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('errorToast');
            if(toast) {
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => toast.remove(), 500);
            }
        }, 4000);
    </script>
    <?php endif; ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-10 animate-fade-in-up">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 mb-2 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-book text-white text-xl"></i>
                        </div>
                        Notes Management
                    </h1>
                    <p class="text-slate-600">Upload and manage study materials and notes</p>
                </div>
                <button onclick="openAddModal()"
                        class="px-6 py-3.5 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-3 group">
                    <i class="fas fa-plus"></i>
                    Add New Note
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform duration-200"></i>
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 card-hover">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-book text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 font-medium">Total Notes</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $totalNotes ?></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 card-hover">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-file-pdf text-emerald-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 font-medium">PDF Files</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $totalNotes ?></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 card-hover">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-amber-100 to-amber-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-amber-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 font-medium">Classes</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $conn->query("SELECT COUNT(DISTINCT class) as count FROM notes")->fetch_assoc()['count'] ?></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 card-hover">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-rose-100 to-rose-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-subject text-rose-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 font-medium">Subjects</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $conn->query("SELECT COUNT(DISTINCT subject_name) as count FROM notes")->fetch_assoc()['count'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="animate-fade-in-up" style="animation-delay: 0.2s;">
            <?php if ($notes->num_rows == 0): ?>
                <!-- Empty State -->
                <div class="text-center py-16 bg-white/50 backdrop-blur-sm rounded-2xl border-2 border-dashed border-slate-200">
                    <div class="max-w-md mx-auto">
                        <div class="w-32 h-32 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-book text-slate-400 text-5xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-700 mb-3">No Notes Available</h3>
                        <p class="text-slate-500 mb-8">Start by uploading your first study material</p>
                        <button onclick="openAddModal()"
                                class="px-8 py-3.5 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Upload First Note
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Notes Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while($row = $notes->fetch_assoc()): 
                        $fileExt = strtolower(pathinfo($row['file_path'], PATHINFO_EXTENSION));
                        $fileIcon = $fileExt == 'pdf' ? 'fa-file-pdf' : ($fileExt == 'doc' || $fileExt == 'docx' ? 'fa-file-word' : 'fa-file');
                        $fileColor = $fileExt == 'pdf' ? 'text-red-500' : ($fileExt == 'doc' || $fileExt == 'docx' ? 'text-blue-500' : 'text-slate-500');
                    ?>
                    <div class="group bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100 card-hover animate-fade-in">
                        <!-- File Type Header -->
                        <div class="p-5 bg-gradient-to-r from-violet-500 to-purple-600 text-white">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                        <i class="fas <?= $fileIcon ?> text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg line-clamp-1"><?= htmlspecialchars($row['subject_name']) ?></h3>
                                        <p class="text-sm opacity-90"><?= htmlspecialchars($row['class']) ?></p>
                                    </div>
                                </div>
                                <span class="text-xs bg-white/20 px-2 py-1 rounded-full">
                                    <?= htmlspecialchars($row['semester']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <!-- Description -->
                            <?php if(!empty($row['description'])): ?>
                            <div class="mb-4">
                                <p class="text-sm text-slate-600 line-clamp-2"><?= htmlspecialchars($row['description']) ?></p>
                            </div>
                            <?php endif; ?>

                            <!-- File Info -->
                            <div class="mb-4 p-3 bg-slate-50 rounded-lg border border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 <?= $fileColor ?> bg-white rounded-lg flex items-center justify-center border">
                                        <i class="fas <?= $fileIcon ?> text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-800 truncate"><?= htmlspecialchars($row['file_path']) ?></p>
                                        <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-user"></i>
                                                <?= htmlspecialchars($row['created_by']) ?>
                                            </span>
                                            <span>•</span>
                                            <span><?= date('M d, Y', strtotime($row['created_at'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="pt-4 border-t border-slate-100 flex gap-2">
                                <a href="../../uploads/notes/<?= htmlspecialchars($row['file_path']) ?>" 
                                   target="_blank"
                                   class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 rounded-xl font-medium hover:from-blue-100 hover:to-indigo-100 transition-all duration-200 border border-blue-100 hover:border-blue-200 flex items-center justify-center gap-2 group">
                                    <i class="fas fa-eye"></i>
                                    View File
                                    <i class="fas fa-external-link-alt text-xs opacity-70 group-hover:translate-x-0.5 transition-transform duration-200"></i>
                                </a>
                                
                                <div class="flex gap-2">
                                    <button onclick='openEditModal(<?= json_encode($row) ?>)'
                                            class="px-4 py-2.5 bg-gradient-to-r from-amber-50 to-orange-50 text-amber-700 rounded-xl font-medium hover:from-amber-100 hover:to-orange-100 transition-all duration-200 border border-amber-100 hover:border-amber-200 flex items-center justify-center gap-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <a href="?delete=<?= $row['id'] ?>" 
                                       onclick="return confirmDelete()"
                                       class="px-4 py-2.5 bg-gradient-to-r from-rose-50 to-red-50 text-rose-700 rounded-xl font-medium hover:from-rose-100 hover:to-red-100 transition-all duration-200 border border-rose-100 hover:border-rose-200 flex items-center justify-center gap-2">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-violet-50 to-purple-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            Add New Note
                        </h3>
                        <p class="text-slate-600 text-sm mt-1">Upload study material or notes</p>
                    </div>
                    <button onclick="closeAddModal()"
                            class="w-10 h-10 rounded-xl bg-white/80 hover:bg-white text-slate-600 hover:text-slate-800 flex items-center justify-center transition-all duration-200 border border-slate-200">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="flex-1 overflow-y-auto p-6">
                <form method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="showLoading()" id="addForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Class -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Class <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="class" required
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm"
                                    placeholder="e.g., BCA, BBA, B.Com">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Subject -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Subject Name <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="subject_name" required
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm"
                                    placeholder="e.g., Mathematics, Physics">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Semester -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Semester
                            </label>
                            <div class="relative">
                                <input type="text" name="semester"
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm"
                                    placeholder="e.g., Semester I, Year 1">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Created By -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Created By
                            </label>
                            <div class="relative">
                                <input type="text" name="created_by"
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm"
                                    placeholder="Faculty name or department">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Description
                            </label>
                            <div class="relative">
                                <textarea name="description" rows="3"
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm resize-none"
                                    placeholder="Brief description of the note content"></textarea>
                                <div class="absolute left-4 top-4 text-slate-400">
                                    <i class="fas fa-align-left"></i>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                File <span class="text-rose-500">*</span>
                            </label>
                            <div class="file-upload-area relative" id="fileUploadArea">
                                <input type="file" name="file" id="fileUpload" required
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    accept=".pdf,.doc,.docx,.txt,.ppt,.pptx"
                                    onchange="handleFileSelect(event)">
                                <div class="p-8 text-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-violet-100 to-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-cloud-upload-alt text-violet-500 text-2xl"></i>
                                    </div>
                                    <h4 class="font-semibold text-slate-700 mb-2">Upload Study Material</h4>
                                    <p class="text-slate-500 text-sm mb-4">Drag & drop file or click to browse</p>
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-lg text-slate-600 text-sm">
                                        <i class="fas fa-file"></i>
                                        <span>PDF, DOC, PPT, TXT - Max 10MB</span>
                                    </div>
                                </div>
                            </div>
                            <div id="filePreview" class="hidden mt-3"></div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex gap-3">
                            <button type="submit" name="add_note"
                                class="flex-1 px-6 py-3.5 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 active:scale-95 flex items-center justify-center gap-2 group">
                                <i class="fas fa-save"></i>
                                Upload Note
                                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform duration-200"></i>
                            </button>
                            <button type="button" onclick="closeAddModal()"
                                class="px-6 py-3.5 border-2 border-slate-200 text-slate-700 font-medium rounded-xl hover:bg-slate-50 transition-all duration-200 flex items-center gap-2">
                                <i class="fas fa-times"></i>
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-amber-50 to-orange-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-edit text-white"></i>
                            </div>
                            Edit Note
                        </h3>
                        <p class="text-slate-600 text-sm mt-1">Update note details</p>
                    </div>
                    <button onclick="closeEditModal()"
                            class="w-10 h-10 rounded-xl bg-white/80 hover:bg-white text-slate-600 hover:text-slate-800 flex items-center justify-center transition-all duration-200 border border-slate-200">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="flex-1 overflow-y-auto p-6">
                <form method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="showLoading()" id="editForm">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Class -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Class <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="class" id="edit_class" required
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Subject -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Subject Name <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="subject_name" id="edit_subject" required
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Semester -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Semester
                            </label>
                            <div class="relative">
                                <input type="text" name="semester" id="edit_semester"
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Created By -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Created By
                            </label>
                            <div class="relative">
                                <input type="text" name="created_by" id="edit_created_by"
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Description
                            </label>
                            <div class="relative">
                                <textarea name="description" id="edit_desc" rows="3"
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm resize-none"></textarea>
                                <div class="absolute left-4 top-4 text-slate-400">
                                    <i class="fas fa-align-left"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Current File -->
                        <div id="currentFileContainer" class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Current File
                            </label>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center border">
                                            <i class="fas fa-file text-slate-500 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-800" id="currentFileName"></div>
                                            <div class="text-xs text-slate-500">Current uploaded file</div>
                                        </div>
                                    </div>
                                    <a href="#" target="_blank" id="currentFileLink" 
                                       class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors duration-200 text-sm">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- New File Upload -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                New File (Optional)
                            </label>
                            <div class="file-upload-area relative" id="editFileUploadArea">
                                <input type="file" name="file" id="editFileUpload"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    accept=".pdf,.doc,.docx,.txt,.ppt,.pptx"
                                    onchange="handleEditFileSelect(event)">
                                <div class="p-8 text-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-sync-alt text-blue-500 text-2xl"></i>
                                    </div>
                                    <h4 class="font-semibold text-slate-700 mb-2">Update File</h4>
                                    <p class="text-slate-500 text-sm mb-4">Upload new file to replace existing</p>
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-lg text-slate-600 text-sm">
                                        <i class="fas fa-file"></i>
                                        <span>PDF, DOC, PPT, TXT - Max 10MB</span>
                                    </div>
                                </div>
                            </div>
                            <div id="editFilePreview" class="hidden mt-3"></div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex gap-3">
                            <button type="submit" name="update_note"
                                class="flex-1 px-6 py-3.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 active:scale-95 flex items-center justify-center gap-2 group">
                                <i class="fas fa-save"></i>
                                Update Note
                                <i class="fas fa-check group-hover:scale-110 transition-transform duration-200"></i>
                            </button>
                            <button type="button" onclick="closeEditModal()"
                                class="px-6 py-3.5 border-2 border-slate-200 text-slate-700 font-medium rounded-xl hover:bg-slate-50 transition-all duration-200 flex items-center gap-2">
                                <i class="fas fa-times"></i>
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Loading overlay
        function showLoading() {
            document.getElementById('loadingOverlay').classList.add('active');
            return true;
        }

        window.addEventListener('load', () => {
            setTimeout(() => {
                document.getElementById('loadingOverlay').classList.remove('active');
            }, 500);
        });

        // Confirmation for delete
        function confirmDelete() {
            return confirm('Are you sure you want to delete this note? This action cannot be undone.');
        }

        // File upload handling
        function handleFileSelect(event) {
            const file = event.target.files[0];
            const uploadArea = document.getElementById('fileUploadArea');
            const preview = document.getElementById('filePreview');
            
            if (file) {
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'];
                
                if (allowedTypes.includes(file.type) || file.name.match(/\.(pdf|doc|docx|txt|ppt|pptx)$/i)) {
                    uploadArea.classList.add('dragover');
                    
                    const fileIcon = file.name.match(/\.pdf$/i) ? 'fa-file-pdf text-red-500' : 
                                   file.name.match(/\.(doc|docx)$/i) ? 'fa-file-word text-blue-500' : 
                                   'fa-file text-slate-500';
                    
                    preview.innerHTML = `
                        <div class="file-preview flex items-center justify-between bg-white p-3 rounded-lg border border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                                    <i class="fas ${fileIcon} text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-slate-800 truncate max-w-xs">${file.name}</div>
                                    <div class="text-xs text-slate-500">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                                </div>
                            </div>
                            <button type="button" onclick="removeFile()" class="text-rose-500 hover:text-rose-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    preview.classList.remove('hidden');
                    
                    setTimeout(() => {
                        uploadArea.classList.remove('dragover');
                    }, 1000);
                } else {
                    alert('Please select a valid file (PDF, DOC, DOCX, TXT, PPT, PPTX).');
                    event.target.value = '';
                }
            }
        }

        function handleEditFileSelect(event) {
            const file = event.target.files[0];
            const uploadArea = document.getElementById('editFileUploadArea');
            const preview = document.getElementById('editFilePreview');
            
            if (file) {
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'];
                
                if (allowedTypes.includes(file.type) || file.name.match(/\.(pdf|doc|docx|txt|ppt|pptx)$/i)) {
                    uploadArea.classList.add('dragover');
                    
                    const fileIcon = file.name.match(/\.pdf$/i) ? 'fa-file-pdf text-red-500' : 
                                   file.name.match(/\.(doc|docx)$/i) ? 'fa-file-word text-blue-500' : 
                                   'fa-file text-slate-500';
                    
                    preview.innerHTML = `
                        <div class="file-preview flex items-center justify-between bg-white p-3 rounded-lg border border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                                    <i class="fas ${fileIcon} text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-slate-800 truncate max-w-xs">${file.name}</div>
                                    <div class="text-xs text-slate-500">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                                </div>
                            </div>
                            <button type="button" onclick="removeEditFile()" class="text-rose-500 hover:text-rose-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    preview.classList.remove('hidden');
                    
                    setTimeout(() => {
                        uploadArea.classList.remove('dragover');
                    }, 1000);
                } else {
                    alert('Please select a valid file (PDF, DOC, DOCX, TXT, PPT, PPTX).');
                    event.target.value = '';
                }
            }
        }

        function removeFile() {
            document.getElementById('fileUpload').value = '';
            document.getElementById('filePreview').classList.add('hidden');
            document.getElementById('filePreview').innerHTML = '';
        }

        function removeEditFile() {
            document.getElementById('editFileUpload').value = '';
            document.getElementById('editFilePreview').classList.add('hidden');
            document.getElementById('editFilePreview').innerHTML = '';
        }

        // Modal Functions
        function openAddModal() {
            const modal = document.getElementById('addModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                const modalContent = modal.querySelector('.bg-white');
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
            
            document.getElementById('addForm').reset();
            removeFile();
        }

        function closeAddModal() {
            const modal = document.getElementById('addModal');
            const modalContent = modal.querySelector('.bg-white');
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('addForm').reset();
                removeFile();
            }, 300);
        }

        function openEditModal(data) {
            // Set form values
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_class').value = data.class;
            document.getElementById('edit_subject').value = data.subject_name;
            document.getElementById('edit_desc').value = data.description;
            document.getElementById('edit_semester').value = data.semester;
            document.getElementById('edit_created_by').value = data.created_by;
            
            // Handle current file
            const currentFileName = document.getElementById('currentFileName');
            const currentFileLink = document.getElementById('currentFileLink');
            
            if (data.file_path) {
                currentFileName.textContent = data.file_path;
                currentFileLink.href = `../../uploads/notes/${data.file_path}`;
                document.getElementById('currentFileContainer').classList.remove('hidden');
            } else {
                document.getElementById('currentFileContainer').classList.add('hidden');
            }
            
            // Reset file preview
            removeEditFile();
            
            // Show modal
            const modal = document.getElementById('editModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                const modalContent = modal.querySelector('.bg-white');
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            const modalContent = modal.querySelector('.bg-white');
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('editForm').reset();
                removeEditFile();
            }, 300);
        }

        // Close modals with ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });

        // Drag and drop for file upload
        function setupDragAndDrop() {
            const uploadAreas = ['fileUploadArea', 'editFileUploadArea'];
            
            uploadAreas.forEach(areaId => {
                const area = document.getElementById(areaId);
                if (!area) return;
                
                area.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    area.classList.add('dragover');
                });
                
                area.addEventListener('dragleave', () => {
                    area.classList.remove('dragover');
                });
                
                area.addEventListener('drop', (e) => {
                    e.preventDefault();
                    area.classList.remove('dragover');
                    
                    const file = e.dataTransfer.files[0];
                    if (file) {
                        const input = area.querySelector('input[type="file"]');
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        input.files = dataTransfer.files;
                        
                        const event = new Event('change', { bubbles: true });
                        input.dispatchEvent(event);
                    }
                });
            });
        }

        // Initialize drag and drop
        setupDragAndDrop();

        // Auto-hide notifications
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const notifications = document.querySelectorAll('#successToast, #errorToast');
                notifications.forEach(notification => {
                    if (notification) {
                        notification.style.opacity = '0';
                        setTimeout(() => notification.remove(), 500);
                    }
                });
            }, 4000);
        });

        // Add animations to cards
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.card-hover').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease-out';
            observer.observe(card);
        });
    </script>
</body>
</html>