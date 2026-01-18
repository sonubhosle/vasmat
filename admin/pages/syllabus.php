<?php
include '../includes/db.php';
include '../includes/header.php';
$success = "";
$error = "";

// ADD SYLLABUS
if (isset($_POST['add_syllabus'])) {
    $subject = $conn->real_escape_string($_POST['subject_name']);
    $uploaded_by = $conn->real_escape_string($_POST['uploaded_by']);
    $year = $conn->real_escape_string($_POST['academic_year']);

    $uploadDir = __DIR__ . '/../../upload/syllabus/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . "_" . $_FILES['syllabus_file']['name'];
    $tmp = $_FILES['syllabus_file']['tmp_name'];

    if (move_uploaded_file($tmp, $uploadDir . $fileName)) {
        $sql = "INSERT INTO syllabus (subject_name, uploaded_by, academic_year, syllabus_file)
                VALUES ('$subject', '$uploaded_by', '$year', '$fileName')";
        if ($conn->query($sql)) {
            $success = "📚 Syllabus uploaded successfully!";
        } else {
            $error = "Database error!";
        }
    } else {
        $error = "File upload failed!";
    }
}

// DELETE SYLLABUS
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $res = $conn->query("SELECT syllabus_file FROM syllabus WHERE id=$id");
    if($row = $res->fetch_assoc()){
        @unlink(__DIR__ . '/../../upload/syllabus/' . $row['syllabus_file']);
    }

    if ($conn->query("DELETE FROM syllabus WHERE id=$id")) {
        $success = "🗑️ Syllabus deleted successfully!";
    } else {
        $error = "Delete failed!";
    }
}

// UPDATE SYLLABUS
if (isset($_POST['update_syllabus'])) {
    $id = intval($_POST['id']);
    $subject = $conn->real_escape_string($_POST['subject_name']);
    $uploaded_by = $conn->real_escape_string($_POST['uploaded_by']);
    $year = $conn->real_escape_string($_POST['academic_year']);

    $fileUpdate = "";

    if (!empty($_FILES['syllabus_file']['name'])) {
        $res = $conn->query("SELECT syllabus_file FROM syllabus WHERE id=$id");
        if($row = $res->fetch_assoc()){
            @unlink(__DIR__ . '/../../upload/syllabus/' . $row['syllabus_file']);
        }

        $uploadDir = __DIR__ . '/../../upload/syllabus/';
        $fileName = time() . "_" . $_FILES['syllabus_file']['name'];
        $tmp = $_FILES['syllabus_file']['tmp_name'];
        move_uploaded_file($tmp, $uploadDir . $fileName);
        $fileUpdate = ", syllabus_file='$fileName'";
    }

    $sql = "UPDATE syllabus SET 
            subject_name='$subject',
            uploaded_by='$uploaded_by',
            academic_year='$year'
            $fileUpdate
            WHERE id=$id";

    if($conn->query($sql)){
        $success = "✨ Syllabus updated successfully!";
    } else {
        $error = "Update failed!";
    }
}

// FETCH DATA
$syllabus = $conn->query("SELECT * FROM syllabus ORDER BY id DESC");
$totalSyllabus = $conn->query("SELECT COUNT(*) as count FROM syllabus")->fetch_assoc()['count'];
$totalDownloads = $conn->query("SELECT SUM(download_count) as total FROM syllabus")->fetch_assoc()['total'];
$totalSubjects = $conn->query("SELECT COUNT(DISTINCT subject_name) as count FROM syllabus")->fetch_assoc()['count'];
$currentYear = date('Y');
$currentYearCount = $conn->query("SELECT COUNT(*) as count FROM syllabus WHERE academic_year LIKE '%$currentYear%'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syllabus Management</title>
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

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
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

        .animate-pulse {
            animation: pulse 2s ease-in-out infinite;
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

        .file-upload-area.dragover {
            border-color: #3b82f6;
            background: #eff6ff;
            transform: scale(1.02);
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

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
        }

        .download-badge {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .view-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .floating-button {
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .floating-button:hover {
            box-shadow: 0 15px 50px rgba(102, 126, 234, 0.4);
            transform: translateY(-3px);
        }

        .syllabus-card {
            border-left: 4px solid;
            border-image: linear-gradient(to bottom, #667eea, #764ba2) 1;
        }

        .modal-enter {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
        }

        .modal-enter-active {
            opacity: 1;
            transform: scale(1) translateY(0);
            transition: all 0.3s ease-out;
        }

        .modal-exit {
            opacity: 1;
            transform: scale(1) translateY(0);
        }

        .modal-exit-active {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
            transition: all 0.3s ease-in;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-violet-600 border-t-transparent rounded-full animate-spin mb-4"></div>
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
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                        Syllabus Management
                    </h1>
                    <p class="text-slate-600">Upload and manage academic syllabi efficiently</p>
                </div>
                <button onclick="openAddModal()"
                        class="px-6 py-3.5 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-3 group floating-button">
                    <i class="fas fa-plus"></i>
                    Upload Syllabus
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform duration-200"></i>
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="stat-card rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <p class="text-sm opacity-90 mb-1">Total Syllabi</p>
                            <h3 class="text-3xl font-bold"><?= $totalSyllabus ?></h3>
                        </div>
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <i class="fas fa-file-alt text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <p class="text-sm opacity-90 mb-1">Total Downloads</p>
                            <h3 class="text-3xl font-bold"><?= $totalDownloads ?: '0' ?></h3>
                        </div>
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <i class="fas fa-download text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <p class="text-sm opacity-90 mb-1">Subjects</p>
                            <h3 class="text-3xl font-bold"><?= $totalSubjects ?></h3>
                        </div>
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <i class="fas fa-book text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <p class="text-sm opacity-90 mb-1">Current Year</p>
                            <h3 class="text-3xl font-bold"><?= $currentYearCount ?></h3>
                        </div>
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-white text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="animate-fade-in-up" style="animation-delay: 0.2s;">
            <?php if ($syllabus->num_rows == 0): ?>
                <!-- Empty State -->
                <div class="text-center py-16 bg-white/50 backdrop-blur-sm rounded-2xl border-2 border-dashed border-slate-200">
                    <div class="max-w-md mx-auto">
                        <div class="w-32 h-32 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-graduation-cap text-slate-400 text-5xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-700 mb-3">No Syllabus Available</h3>
                        <p class="text-slate-500 mb-8">Start by uploading your first syllabus</p>
                        <button onclick="openAddModal()"
                                class="px-8 py-3.5 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 inline-flex items-center gap-2 floating-button">
                            <i class="fas fa-plus"></i>
                            Upload First Syllabus
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Syllabus Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while($row = $syllabus->fetch_assoc()): 
                        $fileExt = strtolower(pathinfo($row['syllabus_file'], PATHINFO_EXTENSION));
                        $fileIcon = $fileExt == 'pdf' ? 'fa-file-pdf' : 'fa-file';
                        $fileColor = $fileExt == 'pdf' ? 'text-red-500' : 'text-slate-500';
                    ?>
                    <div class="group syllabus-card bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100 card-hover animate-fade-in">
                        <!-- Header -->
                        <div class="p-5 bg-gradient-to-r from-violet-50 to-purple-50 border-b border-slate-100">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-lg text-slate-800 line-clamp-1"><?= htmlspecialchars($row['subject_name']) ?></h3>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-xs bg-violet-100 text-violet-700 px-2 py-1 rounded-full">
                                            <i class="fas fa-user mr-1"></i><?= htmlspecialchars($row['uploaded_by']) ?>
                                        </span>
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                            <i class="fas fa-calendar mr-1"></i><?= htmlspecialchars($row['academic_year']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ml-3">
                                    <div class="w-10 h-10 <?= $fileColor ?> bg-white rounded-lg flex items-center justify-center border border-slate-200 shadow-sm">
                                        <i class="fas <?= $fileIcon ?> text-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <!-- File Info -->
                            <div class="mb-4 p-3 bg-slate-50 rounded-lg border border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 <?= $fileColor ?> bg-white rounded-lg flex items-center justify-center border">
                                            <i class="fas <?= $fileIcon ?> text-xl"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-800 truncate"><?= htmlspecialchars($row['syllabus_file']) ?></p>
                                        <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-download"></i>
                                                <?= $row['download_count'] ?> downloads
                                            </span>
                                            <span>•</span>
                                            <span><?= date('M d, Y', strtotime($row['created_at'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="text-center p-2 bg-emerald-50 rounded-lg border border-emerald-100">
                                    <div class="text-emerald-700 font-semibold"><?= $row['download_count'] ?: '0' ?></div>
                                    <div class="text-xs text-emerald-600">Downloads</div>
                                </div>
                                <div class="text-center p-2 bg-violet-50 rounded-lg border border-violet-100">
                                    <div class="text-violet-700 font-semibold"><?= htmlspecialchars($row['academic_year']) ?></div>
                                    <div class="text-xs text-violet-600">Academic Year</div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="pt-4 border-t border-slate-100 flex gap-2">
                                <a href="../../upload/syllabus/<?= htmlspecialchars($row['syllabus_file']) ?>" 
                                   target="_blank"
                                   onclick="updateDownloadCount(<?= $row['id'] ?>)"
                                   class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 flex items-center justify-center gap-2 group view-badge">
                                    <i class="fas fa-eye"></i>
                                    View Syllabus
                                    <i class="fas fa-external-link-alt text-xs opacity-90 group-hover:translate-x-0.5 transition-transform duration-200"></i>
                                </a>
                                
                                <div class="flex gap-2">
                                    <button onclick='openEditModal(<?= json_encode($row) ?>)'
                                            class="px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-medium hover:from-amber-600 hover:to-orange-700 transition-all duration-200 flex items-center justify-center gap-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <a href="?delete=<?= $row['id'] ?>" 
                                       onclick="return confirmDelete()"
                                       class="px-4 py-2.5 bg-gradient-to-r from-rose-500 to-red-600 text-white rounded-xl font-medium hover:from-rose-600 hover:to-red-700 transition-all duration-200 flex items-center justify-center gap-2">
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
    <div id="addModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto max-h-[90vh] overflow-hidden flex flex-col modal-enter">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-violet-50 to-purple-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            Upload Syllabus
                        </h3>
                        <p class="text-slate-600 text-sm mt-1">Add new academic syllabus</p>
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
                    <!-- Subject Name -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Subject Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="subject_name" required
                                class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm"
                                placeholder="e.g., Computer Science, Mathematics">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Uploaded By -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Uploaded By <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="uploaded_by" required
                                class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm"
                                placeholder="e.g., Department Head, Professor">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Year -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Academic Year <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="academic_year" required
                                class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm"
                                placeholder="e.g., 2024-25, 2023-2024">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Syllabus File (PDF) <span class="text-rose-500">*</span>
                        </label>
                        <div class="file-upload-area relative" id="fileUploadArea">
                            <input type="file" name="syllabus_file" id="fileUpload" required
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                accept=".pdf"
                                onchange="handleFileSelect(event)">
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-violet-100 to-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-slate-700 mb-2">Upload Syllabus PDF</h4>
                                <p class="text-slate-500 text-sm mb-4">Drag & drop PDF file or click to browse</p>
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-lg text-slate-600 text-sm">
                                    <i class="fas fa-file-pdf text-red-500"></i>
                                    <span>PDF format only - Max 10MB</span>
                                </div>
                            </div>
                        </div>
                        <div id="filePreview" class="hidden mt-3"></div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex gap-3">
                            <button type="submit" name="add_syllabus"
                                class="flex-1 px-6 py-3.5 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 active:scale-95 flex items-center justify-center gap-2 group">
                                <i class="fas fa-upload"></i>
                                Upload Syllabus
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
    <div id="editModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto max-h-[90vh] overflow-hidden flex flex-col modal-enter">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-amber-50 to-orange-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-edit text-white"></i>
                            </div>
                            Edit Syllabus
                        </h3>
                        <p class="text-slate-600 text-sm mt-1">Update syllabus details</p>
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
                    
                    <!-- Subject Name -->
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

                    <!-- Uploaded By -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Uploaded By <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="uploaded_by" id="edit_uploaded_by" required
                                class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Year -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Academic Year <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="academic_year" id="edit_year" required
                                class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500 transition-all duration-300 bg-white shadow-sm">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Current File -->
                    <div id="currentFileContainer" class="">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Current File
                        </label>
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center border">
                                        <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-800 text-sm" id="currentFileName"></div>
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
                    <div class="">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            New File (Optional)
                        </label>
                        <div class="file-upload-area relative" id="editFileUploadArea">
                            <input type="file" name="syllabus_file" id="editFileUpload"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                accept=".pdf"
                                onchange="handleEditFileSelect(event)">
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-sync-alt text-blue-500 text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-slate-700 mb-2">Update PDF File</h4>
                                <p class="text-slate-500 text-sm mb-4">Upload new PDF to replace existing</p>
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-lg text-slate-600 text-sm">
                                    <i class="fas fa-file-pdf text-red-500"></i>
                                    <span>PDF format only - Max 10MB</span>
                                </div>
                            </div>
                        </div>
                        <div id="editFilePreview" class="hidden mt-3"></div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex gap-3">
                            <button type="submit" name="update_syllabus"
                                class="flex-1 px-6 py-3.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 active:scale-95 flex items-center justify-center gap-2 group">
                                <i class="fas fa-save"></i>
                                Update Syllabus
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

        // Update download count
        function updateDownloadCount(id) {
            fetch('syllabus.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=download&id=' + id
            });
        }

        // Confirmation for delete
        function confirmDelete() {
            return confirm('Are you sure you want to delete this syllabus? This action cannot be undone.');
        }

        // File upload handling
        function handleFileSelect(event) {
            const file = event.target.files[0];
            const uploadArea = document.getElementById('fileUploadArea');
            const preview = document.getElementById('filePreview');
            
            if (file) {
                // Check if PDF
                if (file.type === 'application/pdf' || file.name.match(/\.pdf$/i)) {
                    uploadArea.classList.add('dragover');
                    
                    preview.innerHTML = `
                        <div class="file-preview flex items-center justify-between bg-white p-3 rounded-lg border border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-slate-800 truncate max-w-xs">${file.name}</div>
                                    <div class="text-xs text-slate-500">${(file.size / 1024 / 1024).toFixed(2)} MB • PDF</div>
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
                    alert('Please select a PDF file only.');
                    event.target.value = '';
                }
            }
        }

        function handleEditFileSelect(event) {
            const file = event.target.files[0];
            const uploadArea = document.getElementById('editFileUploadArea');
            const preview = document.getElementById('editFilePreview');
            
            if (file) {
                // Check if PDF
                if (file.type === 'application/pdf' || file.name.match(/\.pdf$/i)) {
                    uploadArea.classList.add('dragover');
                    
                    preview.innerHTML = `
                        <div class="file-preview flex items-center justify-between bg-white p-3 rounded-lg border border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-slate-800 truncate max-w-xs">${file.name}</div>
                                    <div class="text-xs text-slate-500">${(file.size / 1024 / 1024).toFixed(2)} MB • PDF</div>
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
                    alert('Please select a PDF file only.');
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

        // Modal Functions with animations
        function openAddModal() {
            const modal = document.getElementById('addModal');
            modal.classList.remove('hidden');
            modal.querySelector('.bg-white').classList.remove('modal-enter');
            setTimeout(() => {
                modal.querySelector('.bg-white').classList.add('modal-enter-active');
            }, 10);
            
            document.getElementById('addForm').reset();
            removeFile();
        }

        function closeAddModal() {
            const modal = document.getElementById('addModal');
            const modalContent = modal.querySelector('.bg-white');
            modalContent.classList.remove('modal-enter-active');
            modalContent.classList.add('modal-exit-active');
            setTimeout(() => {
                modal.classList.add('hidden');
                modalContent.classList.remove('modal-exit-active');
                modalContent.classList.add('modal-enter');
                document.getElementById('addForm').reset();
                removeFile();
            }, 300);
        }

        function openEditModal(data) {
            // Set form values
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_subject').value = data.subject_name;
            document.getElementById('edit_uploaded_by').value = data.uploaded_by;
            document.getElementById('edit_year').value = data.academic_year;
            
            // Handle current file
            const currentFileName = document.getElementById('currentFileName');
            const currentFileLink = document.getElementById('currentFileLink');
            
            if (data.syllabus_file) {
                currentFileName.textContent = data.syllabus_file;
                currentFileLink.href = `../../upload/syllabus/${data.syllabus_file}`;
                document.getElementById('currentFileContainer').classList.remove('hidden');
            } else {
                document.getElementById('currentFileContainer').classList.add('hidden');
            }
            
            // Reset file preview
            removeEditFile();
            
            // Show modal
            const modal = document.getElementById('editModal');
            modal.classList.remove('hidden');
            modal.querySelector('.bg-white').classList.remove('modal-enter');
            setTimeout(() => {
                modal.querySelector('.bg-white').classList.add('modal-enter-active');
            }, 10);
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            const modalContent = modal.querySelector('.bg-white');
            modalContent.classList.remove('modal-enter-active');
            modalContent.classList.add('modal-exit-active');
            setTimeout(() => {
                modal.classList.add('hidden');
                modalContent.classList.remove('modal-exit-active');
                modalContent.classList.add('modal-enter');
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

        // Close modals when clicking outside
        document.addEventListener('click', (e) => {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            
            if (!addModal.classList.contains('hidden') && e.target === addModal) {
                closeAddModal();
            }
            
            if (!editModal.classList.contains('hidden') && e.target === editModal) {
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

        // Stats animation on hover
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-5px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
            });
        });
    </script>
</body>
</html>