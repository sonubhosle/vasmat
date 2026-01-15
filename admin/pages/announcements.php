<?php
include '../includes/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    exit("Unauthorized");
}

// DB include
$dbPath = file_exists("../includes/db.php") ? "../includes/db.php" : "includes/db.php";
include $dbPath;

$success = "";
$error = "";

// Badge color mapping with icons
$badgeOptions = [
    'hot' => [
        'name' => 'Hot',
        'color' => 'bg-gradient-to-r from-red-500 to-pink-600',
        'icon' => 'fas fa-fire'
    ],
    'event' => [
        'name' => 'Event',
        'color' => 'bg-gradient-to-r from-orange-500 to-amber-600',
        'icon' => 'fas fa-calendar-star'
    ],
    'new' => [
        'name' => 'New',
        'color' => 'bg-gradient-to-r from-blue-500 to-cyan-600',
        'icon' => 'fas fa-star'
    ],
    'important' => [
        'name' => 'Important',
        'color' => 'bg-gradient-to-r from-purple-500 to-indigo-600',
        'icon' => 'fas fa-exclamation-circle'
    ],
    'update' => [
        'name' => 'Update',
        'color' => 'bg-gradient-to-r from-green-500 to-emerald-600',
        'icon' => 'fas fa-sync-alt'
    ],
    'urgent' => [
        'name' => 'Urgent',
        'color' => 'bg-gradient-to-r from-red-600 to-rose-700',
        'icon' => 'fas fa-bell'
    ],
    'notice' => [
        'name' => 'Notice',
        'color' => 'bg-gradient-to-r from-indigo-500 to-violet-600',
        'icon' => 'fas fa-bullhorn'
    ],
    'warning' => [
        'name' => 'Warning',
        'color' => 'bg-gradient-to-r from-yellow-500 to-orange-500',
        'icon' => 'fas fa-exclamation-triangle'
    ],
    'info' => [
        'name' => 'Info',
        'color' => 'bg-gradient-to-r from-slate-500 to-slate-600',
        'icon' => 'fas fa-info-circle'
    ]
];

// ================= ADD =================
if (isset($_POST['add_announcement'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $badge = $conn->real_escape_string($_POST['badge']);
    $is_active = $_POST['is_active'];

    $pdfName = null;

    if (!empty($_FILES['pdf']['name'])) {
        $uploadDir = __DIR__ . "/../../upload/announcements/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $pdfName = time() . '_' . basename($_FILES['pdf']['name']);
        $uploadPath = $uploadDir . $pdfName;
        
        if (move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadPath)) {
            $pdfName = 'announcements/' . $pdfName;
        } else {
            $error = "Failed to upload PDF file.";
        }
    }

    if (empty($error)) {
        $sql = "INSERT INTO announcements (title, description, badge, pdf, is_active) 
                VALUES ('$title','$description','$badge','$pdfName','$is_active')";

        if ($conn->query($sql)) {
            $success = "🎉 Announcement added successfully!";
        } else {
            $error = "Error adding announcement: " . $conn->error;
        }
    }
}

// ================= DELETE =================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $res = $conn->query("SELECT pdf FROM announcements WHERE id=$id");
    if ($row = $res->fetch_assoc()) {
        if ($row['pdf']) {
            @unlink(__DIR__ . "/../../upload/" . $row['pdf']);
        }
    }

    if ($conn->query("DELETE FROM announcements WHERE id=$id")) {
        $success = "🗑️ Announcement deleted successfully!";
    } else {
        $error = "Failed to delete announcement.";
    }
}

// ================= UPDATE =================
if (isset($_POST['update_announcement'])) {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $badge = $conn->real_escape_string($_POST['badge']);
    $is_active = $_POST['is_active'];

    $oldPdf = $_POST['old_pdf'];
    $newPdf = $oldPdf;

    if (!empty($_FILES['pdf']['name'])) {
        $uploadDir = __DIR__ . "/../../upload/announcements/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $newPdfName = time() . '_' . basename($_FILES['pdf']['name']);
        $uploadPath = $uploadDir . $newPdfName;
        
        if (move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadPath)) {
            $newPdf = 'announcements/' . $newPdfName;
            if ($oldPdf) {
                @unlink(__DIR__ . "/../../upload/" . $oldPdf);
            }
        }
    }

    $sql = "UPDATE announcements 
            SET title='$title', description='$description', badge='$badge', pdf='$newPdf', is_active='$is_active' 
            WHERE id=$id";

    if ($conn->query($sql)) {
        $success = "✨ Announcement updated successfully!";
    } else {
        $error = "Failed to update announcement: " . $conn->error;
    }
}

// ================= FETCH =================
$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
$totalAnnouncements = $conn->query("SELECT COUNT(*) as count FROM announcements")->fetch_assoc()['count'];
$activeAnnouncements = $conn->query("SELECT COUNT(*) as count FROM announcements WHERE is_active = 1")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom Animations */
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
            50% { opacity: 0.5; }
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

        .animate-pulse-custom {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Custom Checkbox */
        .custom-checkbox {
            position: relative;
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 2px solid #d1d5db;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .custom-checkbox.checked {
            background: #3b82f6;
            border-color: #3b82f6;
        }

        .custom-checkbox.checked::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 12px;
            font-weight: bold;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Custom Select Dropdown */
        .custom-select {
            position: relative;
            cursor: pointer;
        }

        .custom-select-options {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            max-height: 300px;
            overflow-y: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            z-index: 100;
            opacity: 0;
            transform: translateY(-10px);
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .custom-select.open .custom-select-options {
            opacity: 1;
            transform: translateY(0);
            pointer-events: all;
        }

        .custom-select-option {
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .custom-select-option:hover {
            background: #f8fafc;
        }

        .custom-select-option.selected {
            background: #eff6ff;
        }

        /* File Upload Area */
        .file-upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .file-upload-area:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .file-upload-area.dragover {
            border-color: #3b82f6;
            background: #dbeafe;
            transform: scale(1.02);
        }

        .file-preview {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 12px;
            margin-top: 8px;
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glass Effect */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Loading Spinner */
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f1f5f9;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Card Glow */
        .card-glow {
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .card-glow:hover {
            box-shadow: 0 8px 40px rgba(0,0,0,0.12);
        }

        /* Badge Pulse */
        .badge-pulse {
            position: relative;
        }

        .badge-pulse::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: inherit;
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center transition-all duration-300 hidden">
        <div class="text-center">
            <div class="loading-spinner mb-4"></div>
            <p class="text-slate-600 font-medium">Processing...</p>
        </div>
    </div>

    <!-- Success Notification -->
    <?php if ($success): ?>
    <div id="successToast" class="fixed bottom-6 right-6 z-50 animate-slide-in-right">
        <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white px-6 py-4 rounded-xl shadow-2xl border-l-4 border-emerald-700 max-w-md flex items-center gap-3">
            <i class="fas fa-check-circle text-xl flex-shrink-0"></i>
            <span class="font-semibold"><?= htmlspecialchars($success) ?></span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-white/80 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Error Notification -->
    <?php if ($error): ?>
    <div id="errorToast" class="fixed bottom-6 right-6 z-50 animate-slide-in-right">
        <div class="bg-gradient-to-r from-rose-500 to-red-600 text-white px-6 py-4 rounded-xl shadow-2xl border-l-4 border-red-700 max-w-md flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-xl flex-shrink-0"></i>
            <span class="font-semibold"><?= htmlspecialchars($error) ?></span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-white/80 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <div class="w-full ">
        <!-- Header -->
        <div class="mb-10 animate-fade-in-up">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 mb-2 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-bullhorn text-white text-xl"></i>
                        </div>
                        Announcements Management
                    </h1>
                    <p class="text-slate-600">Manage and publish announcements for your platform</p>
                </div>
                <button onclick="openAddModal()"
                        class="px-6 py-3.5 bg-gradient-to-r from-amber-400 to-amber-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-3 group">
                    <i class="fas fa-plus"></i>
                    Add New Announcement
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform duration-200"></i>
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 card-glow border border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-bullhorn text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 font-medium">Total Announcements</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $totalAnnouncements ?></h3>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2 text-sm text-slate-500">
                            <i class="fas fa-arrow-up text-green-500"></i>
                            <span>Updated in real-time</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 card-glow border border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-emerald-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 font-medium">Active</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $activeAnnouncements ?></h3>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2 text-sm text-slate-500">
                            <i class="fas fa-eye text-blue-500"></i>
                            <span>Visible to users</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 card-glow border border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clock text-purple-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 font-medium">Last Updated</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">
                                <?= date('F j, Y') ?>
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2 text-sm text-slate-500">
                            <i class="fas fa-history text-purple-500"></i>
                            <span><?= date('h:i A') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="animate-fade-in-up" style="animation-delay: 0.2s;">
            <?php if ($announcements->num_rows == 0): ?>
                <!-- Empty State -->
                <div class="text-center py-16 bg-white/50 backdrop-blur-sm rounded-2xl border-2 border-dashed border-slate-200">
                    <div class="max-w-md mx-auto">
                        <div class="w-32 h-32 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-bullhorn text-slate-400 text-5xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-700 mb-3">No Announcements Yet</h3>
                        <p class="text-slate-500 mb-8">Start by adding your first announcement to share with your users</p>
                        <button onclick="openAddModal()"
                                class="px-8 py-3.5 bg-gradient-to-r from-amber-400 to-amber-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Create First Announcement
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Announcements Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while ($row = $announcements->fetch_assoc()): 
                        $badge = $badgeOptions[$row['badge']] ?? $badgeOptions['info'];
                        $statusColor = $row['is_active'] ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 'text-rose-600 bg-rose-50 border-rose-100';
                        $statusIcon = $row['is_active'] ? 'fa-check-circle' : 'fa-pause-circle';
                    ?>
                    <div class="group bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100 card-glow animate-fade-in">
                        <!-- Badge Header -->
                        <div class="p-5 <?= $badge['color'] ?> text-white relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-24 h-24 opacity-10 transform translate-x-8 -translate-y-8">
                                <i class="fas <?= $badge['icon'] ?> text-6xl"></i>
                            </div>
                            <div class="relative z-10">
                                <div class="flex justify-between items-center mb-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fas <?= $badge['icon'] ?>"></i>
                                        <span class="font-bold text-sm uppercase tracking-wider">
                                            <?= strtoupper($badge['name']) ?>
                                        </span>
                                    </div>
                                    <span class="text-xs opacity-90 bg-white/20 px-2 py-1 rounded-full">
                                        <?= date('M d, Y', strtotime($row['created_at'])) ?>
                                    </span>
                                </div>
                                <div class="h-1 w-12 bg-white/30 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-slate-800 mb-3 line-clamp-2 group-hover:text-slate-900 transition-colors duration-200">
                                <?= htmlspecialchars($row['title']) ?>
                            </h3>
                            
                            <?php if (!empty($row['description'])): ?>
                            <p class="text-slate-600 text-sm mb-4 line-clamp-3 leading-relaxed">
                                <?= htmlspecialchars($row['description']) ?>
                            </p>
                            <?php endif; ?>

                            <!-- PDF Attachment -->
                            <?php if (!empty($row['pdf'])): ?>
                            <div class="mb-4">
                                <a href="../../upload/<?= htmlspecialchars($row['pdf']) ?>" 
                                   target="_blank"
                                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-red-50 to-rose-50 text-rose-700 rounded-xl hover:from-red-100 hover:to-rose-100 transition-all duration-200 border border-rose-100 hover:border-rose-200 hover:shadow-sm group">
                                    <i class="fas fa-file-pdf text-lg"></i>
                                    <span class="font-medium">View PDF Attachment</span>
                                    <i class="fas fa-external-link-alt text-xs opacity-70 group-hover:translate-x-0.5 transition-transform duration-200"></i>
                                </a>
                            </div>
                            <?php endif; ?>

                            <!-- Footer -->
                            <div class="pt-4 border-t border-slate-100">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="px-3 py-1.5 rounded-lg text-sm font-medium <?= $statusColor ?> border inline-flex items-center gap-1.5">
                                        <i class="fas <?= $statusIcon ?>"></i>
                                        <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                    <span class="text-xs text-slate-500 flex items-center gap-1">
                                        <i class="far fa-clock"></i>
                                        <?= date('h:i A', strtotime($row['created_at'])) ?>
                                    </span>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <button onclick='openEditModal(<?= json_encode($row) ?>)'
                                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 rounded-xl font-medium hover:from-blue-100 hover:to-indigo-100 transition-all duration-200 border border-blue-100 hover:border-blue-200 flex items-center justify-center gap-2 group">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                        <i class="fas fa-chevron-right text-xs opacity-70 group-hover:translate-x-0.5 transition-transform duration-200"></i>
                                    </button>
                                    <a href="?page=announcements&delete=<?= $row['id'] ?>" 
                                       onclick="return confirmDelete()"
                                       class="flex-1 px-4 py-2.5 bg-gradient-to-r from-rose-50 to-red-50 text-rose-700 rounded-xl font-medium hover:from-rose-100 hover:to-red-100 transition-all duration-200 border border-rose-100 hover:border-rose-200 flex items-center justify-center gap-2 group">
                                        <i class="fas fa-trash-alt"></i>
                                        Delete
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
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-amber-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            Create New Announcement
                        </h3>
                        <p class="text-slate-600 text-sm mt-1">Fill in the details below to create a new announcement</p>
                    </div>
                    <button onclick="closeAddModal()"
                            class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-800 flex items-center justify-center transition-all duration-200 border border-slate-200">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body with Scroll -->
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <form method="POST" enctype="multipart/form-data" class="p-6 space-y-6" onsubmit="showLoading()" id="addForm">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-heading text-blue-600 text-xs"></i>
                            </div>
                            Title
                            <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="title" required
                                class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm"
                                placeholder="Enter announcement title">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                <i class="fas fa-pen"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <div class="w-6 h-6 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-align-left text-emerald-600 text-xs"></i>
                            </div>
                            Description
                        </label>
                        <textarea name="description" rows="4"
                            class="w-full px-4 py-3.5 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300 bg-white shadow-sm resize-none custom-scrollbar"
                            placeholder="Enter announcement description (optional)"></textarea>
                    </div>
                    
                    <!-- Badge & Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Custom Badge Select -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <div class="w-6 h-6 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-tag text-purple-600 text-xs"></i>
                                </div>
                                Badge Type
                                <span class="text-rose-500">*</span>
                            </label>
                            <div class="custom-select" id="badgeSelect">
                                <div class="custom-select-trigger w-full px-4 py-3.5 border border-slate-200 rounded-xl text-slate-800 bg-white shadow-sm flex items-center justify-between cursor-pointer hover:border-slate-300 transition-all duration-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-r from-slate-500 to-slate-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-tag text-white text-sm"></i>
                                        </div>
                                        <span class="font-medium">Select Badge</span>
                                    </div>
                                    <i class="fas fa-chevron-down text-slate-400 transition-transform duration-200"></i>
                                </div>
                                <input type="hidden" name="badge" value="">
                                <div class="custom-select-options mt-2">
                                    <?php foreach ($badgeOptions as $key => $option): ?>
                                    <div class="custom-select-option" data-value="<?= $key ?>">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 <?= $option['color'] ?> rounded-lg flex items-center justify-center">
                                                <i class="<?= $option['icon'] ?> text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800"><?= $option['name'] ?></div>
                                                <div class="text-xs text-slate-500"><?= ucfirst($key) ?> badge</div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Select -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <div class="w-6 h-6 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-toggle-on text-amber-600 text-xs"></i>
                                </div>
                                Status
                            </label>
                            <div class="custom-select" id="statusSelect">
                                <div class="custom-select-trigger w-full px-4 py-3.5 border border-slate-200 rounded-xl text-slate-800 bg-white shadow-sm flex items-center justify-between cursor-pointer hover:border-slate-300 transition-all duration-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-green-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-check-circle text-white text-sm"></i>
                                        </div>
                                        <span class="font-medium">Active</span>
                                    </div>
                                    <i class="fas fa-chevron-down text-slate-400 transition-transform duration-200"></i>
                                </div>
                                <input type="hidden" name="is_active" value="1">
                                <div class="custom-select-options mt-2">
                                    <div class="custom-select-option selected" data-value="1">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-green-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-check-circle text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800">Active</div>
                                                <div class="text-xs text-slate-500">Visible to users</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-select-option" data-value="0">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-gradient-to-r from-rose-500 to-red-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-times-circle text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800">Inactive</div>
                                                <div class="text-xs text-slate-500">Hidden from users</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <div class="w-6 h-6 bg-rose-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-pdf text-rose-600 text-xs"></i>
                            </div>
                            PDF Attachment (Optional)
                        </label>
                        <div class="file-upload-area relative" id="fileUploadArea">
                            <input type="file" name="pdf" id="pdfUpload" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                accept=".pdf"
                                onchange="handleFileSelect(event)">
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-rose-100 to-pink-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-cloud-upload-alt text-rose-500 text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-slate-700 mb-2">Upload PDF File</h4>
                                <p class="text-slate-500 text-sm mb-4">Drag & drop your PDF file here or click to browse</p>
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-lg text-slate-600 text-sm">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Max size: 10MB</span>
                                </div>
                            </div>
                        </div>
                        <div id="filePreview" class="hidden"></div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex gap-3">
                            <button type="submit" name="add_announcement"
                                class="flex-1 px-6 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 active:scale-95 flex items-center justify-center gap-2 group">
                                <i class="fas fa-save"></i>
                                Create Announcement
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
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-edit text-white"></i>
                            </div>
                            Edit Announcement
                        </h3>
                        <p class="text-slate-600 text-sm mt-1">Update the announcement details below</p>
                    </div>
                    <button onclick="closeEditModal()"
                            class="w-10 h-10 rounded-xl bg-white/80 hover:bg-white text-slate-600 hover:text-slate-800 flex items-center justify-center transition-all duration-200 border border-slate-200">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body with Scroll -->
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <form method="POST" enctype="multipart/form-data" class="p-6 space-y-6" onsubmit="showLoading()" id="editForm">
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="old_pdf" id="edit_old_pdf">
                    
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-heading text-blue-600 text-xs"></i>
                            </div>
                            Title
                            <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="title" id="edit_title" required
                                class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                <i class="fas fa-pen"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <div class="w-6 h-6 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-align-left text-emerald-600 text-xs"></i>
                            </div>
                            Description
                        </label>
                        <textarea name="description" id="edit_description" rows="4"
                            class="w-full px-4 py-3.5 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300 bg-white shadow-sm resize-none custom-scrollbar"></textarea>
                    </div>
                    
                    <!-- Badge & Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Custom Badge Select -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <div class="w-6 h-6 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-tag text-purple-600 text-xs"></i>
                                </div>
                                Badge Type
                                <span class="text-rose-500">*</span>
                            </label>
                            <div class="custom-select" id="editBadgeSelect">
                                <div class="custom-select-trigger w-full px-4 py-3.5 border border-slate-200 rounded-xl text-slate-800 bg-white shadow-sm flex items-center justify-between cursor-pointer hover:border-slate-300 transition-all duration-200">
                                    <div class="flex items-center gap-3">
                                        <div id="editBadgePreview" class="w-8 h-8 bg-gradient-to-r from-slate-500 to-slate-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-tag text-white text-sm"></i>
                                        </div>
                                        <span id="editBadgeText" class="font-medium">Select Badge</span>
                                    </div>
                                    <i class="fas fa-chevron-down text-slate-400 transition-transform duration-200"></i>
                                </div>
                                <input type="hidden" name="badge" id="edit_badge" value="">
                                <div class="custom-select-options mt-2">
                                    <?php foreach ($badgeOptions as $key => $option): ?>
                                    <div class="custom-select-option" data-value="<?= $key ?>">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 <?= $option['color'] ?> rounded-lg flex items-center justify-center">
                                                <i class="<?= $option['icon'] ?> text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800"><?= $option['name'] ?></div>
                                                <div class="text-xs text-slate-500"><?= ucfirst($key) ?> badge</div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Select -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <div class="w-6 h-6 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-toggle-on text-amber-600 text-xs"></i>
                                </div>
                                Status
                            </label>
                            <div class="custom-select" id="editStatusSelect">
                                <div class="custom-select-trigger w-full px-4 py-3.5 border border-slate-200 rounded-xl text-slate-800 bg-white shadow-sm flex items-center justify-between cursor-pointer hover:border-slate-300 transition-all duration-200">
                                    <div class="flex items-center gap-3">
                                        <div id="editStatusPreview" class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-green-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-check-circle text-white text-sm"></i>
                                        </div>
                                        <span id="editStatusText" class="font-medium">Active</span>
                                    </div>
                                    <i class="fas fa-chevron-down text-slate-400 transition-transform duration-200"></i>
                                </div>
                                <input type="hidden" name="is_active" id="edit_status" value="1">
                                <div class="custom-select-options mt-2">
                                    <div class="custom-select-option" data-value="1">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-green-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-check-circle text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800">Active</div>
                                                <div class="text-xs text-slate-500">Visible to users</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-select-option" data-value="0">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-gradient-to-r from-rose-500 to-red-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-times-circle text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800">Inactive</div>
                                                <div class="text-xs text-slate-500">Hidden from users</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current PDF & File Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <div class="w-6 h-6 bg-rose-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-pdf text-rose-600 text-xs"></i>
                            </div>
                            PDF Attachment
                        </label>
                        
                        <!-- Current PDF -->
                        <div id="currentPdfContainer" class="mb-4 hidden">
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-rose-100 to-pink-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-file-pdf text-rose-500"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-800" id="currentPdfName"></div>
                                            <div class="text-xs text-slate-500">Current attachment</div>
                                        </div>
                                    </div>
                                    <a href="#" target="_blank" id="currentPdfLink" 
                                       class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors duration-200 text-sm">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- New PDF Upload -->
                        <div class="file-upload-area relative" id="editFileUploadArea">
                            <input type="file" name="pdf" id="editPdfUpload" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                accept=".pdf"
                                onchange="handleEditFileSelect(event)">
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-sync-alt text-blue-500 text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-slate-700 mb-2">Update PDF File</h4>
                                <p class="text-slate-500 text-sm mb-4">Upload new PDF to replace existing or keep current</p>
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-lg text-slate-600 text-sm">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Max size: 10MB</span>
                                </div>
                            </div>
                        </div>
                        <div id="editFilePreview" class="hidden"></div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex gap-3">
                            <button type="submit" name="update_announcement"
                                class="flex-1 px-6 py-3.5 bg-gradient-to-r from-emerald-500 to-green-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 active:scale-95 flex items-center justify-center gap-2 group">
                                <i class="fas fa-save"></i>
                                Update Announcement
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
            document.getElementById('loadingOverlay').classList.remove('hidden');
            return true;
        }

        // Hide loading on page load
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.getElementById('loadingOverlay').classList.add('hidden');
            }, 500);
        });

        // Confirmation for delete
        function confirmDelete() {
            return Swal.fire({
                title: 'Are you sure?',
                text: "This announcement will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                backdrop: 'rgba(0,0,0,0.4)'
            }).then((result) => {
                return result.isConfirmed;
            });
        }

        // Custom Select Functionality
        class CustomSelect {
            constructor(element) {
                this.element = element;
                this.trigger = element.querySelector('.custom-select-trigger');
                this.options = element.querySelectorAll('.custom-select-option');
                this.hiddenInput = element.querySelector('input[type="hidden"]');
                this.init();
            }

            init() {
                this.trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggle();
                });

                this.options.forEach(option => {
                    option.addEventListener('click', () => {
                        this.select(option);
                    });
                });

                // Close when clicking outside
                document.addEventListener('click', () => {
                    this.close();
                });

                // Stop propagation inside options
                this.element.querySelector('.custom-select-options').addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }

            toggle() {
                this.element.classList.toggle('open');
                const icon = this.trigger.querySelector('.fa-chevron-down');
                if (this.element.classList.contains('open')) {
                    icon.style.transform = 'rotate(180deg)';
                } else {
                    icon.style.transform = 'rotate(0deg)';
                }
            }

            open() {
                this.element.classList.add('open');
                this.trigger.querySelector('.fa-chevron-down').style.transform = 'rotate(180deg)';
            }

            close() {
                this.element.classList.remove('open');
                this.trigger.querySelector('.fa-chevron-down').style.transform = 'rotate(0deg)';
            }

            select(option) {
                const value = option.dataset.value;
                const text = option.querySelector('.font-medium').textContent;
                const icon = option.querySelector('.fas, .far')?.className || 'fas fa-tag';
                const color = option.querySelector('div.w-8.h-8').className;

                // Update trigger content
                const triggerContent = this.trigger.querySelector('.flex.items-center.gap-3');
                triggerContent.innerHTML = `
                    <div class="w-8 h-8 ${color} rounded-lg flex items-center justify-center">
                        <i class="${icon} text-white text-sm"></i>
                    </div>
                    <span class="font-medium">${text}</span>
                `;

                // Update hidden input
                this.hiddenInput.value = value;

                // Update selected state
                this.options.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');

                this.close();
            }

            setValue(value) {
                const option = this.element.querySelector(`.custom-select-option[data-value="${value}"]`);
                if (option) {
                    this.select(option);
                }
            }
        }

        // Initialize custom selects
        const badgeSelect = new CustomSelect(document.getElementById('badgeSelect'));
        const statusSelect = new CustomSelect(document.getElementById('statusSelect'));
        const editBadgeSelect = new CustomSelect(document.getElementById('editBadgeSelect'));
        const editStatusSelect = new CustomSelect(document.getElementById('editStatusSelect'));

        // File upload handling
        function handleFileSelect(event) {
            const file = event.target.files[0];
            const uploadArea = document.getElementById('fileUploadArea');
            const preview = document.getElementById('filePreview');
            
            if (file && file.type === 'application/pdf') {
                uploadArea.classList.add('dragover');
                
                preview.innerHTML = `
                    <div class="file-preview flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-rose-100 to-pink-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-pdf text-rose-500 text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800">${file.name}</div>
                                <div class="text-xs text-slate-500">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                            </div>
                        </div>
                        <button type="button" onclick="removeFile()" class="text-rose-500 hover:text-rose-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                preview.classList.remove('hidden');
                
                // Reset the drag effect after a delay
                setTimeout(() => {
                    uploadArea.classList.remove('dragover');
                }, 1000);
            } else {
                alert('Please select a PDF file.');
                event.target.value = '';
            }
        }

        function handleEditFileSelect(event) {
            const file = event.target.files[0];
            const uploadArea = document.getElementById('editFileUploadArea');
            const preview = document.getElementById('editFilePreview');
            
            if (file && file.type === 'application/pdf') {
                uploadArea.classList.add('dragover');
                
                preview.innerHTML = `
                    <div class="file-preview flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-rose-100 to-pink-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-pdf text-rose-500 text-xl"></i>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800">${file.name}</div>
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
                alert('Please select a PDF file.');
                event.target.value = '';
            }
        }

        function removeFile() {
            document.getElementById('pdfUpload').value = '';
            document.getElementById('filePreview').classList.add('hidden');
            document.getElementById('filePreview').innerHTML = '';
        }

        function removeEditFile() {
            document.getElementById('editPdfUpload').value = '';
            document.getElementById('editFilePreview').classList.add('hidden');
            document.getElementById('editFilePreview').innerHTML = '';
        }

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
                    if (file && file.type === 'application/pdf') {
                        const input = area.querySelector('input[type="file"]');
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        input.files = dataTransfer.files;
                        
                        // Trigger change event
                        const event = new Event('change', { bubbles: true });
                        input.dispatchEvent(event);
                    } else {
                        alert('Please drop a PDF file.');
                    }
                });
            });
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
            
            // Reset form
            document.getElementById('addForm').reset();
            removeFile();
            
            // Set default badge
            badgeSelect.setValue('info');
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
            document.getElementById('edit_title').value = data.title;
            document.getElementById('edit_description').value = data.description;
            document.getElementById('edit_old_pdf').value = data.pdf || '';
            
            // Set badge
            editBadgeSelect.setValue(data.badge);
            
            // Set status
            editStatusSelect.setValue(data.is_active.toString());
            
            // Handle PDF preview
            const currentPdfContainer = document.getElementById('currentPdfContainer');
            const currentPdfLink = document.getElementById('currentPdfLink');
            const currentPdfName = document.getElementById('currentPdfName');
            
            if (data.pdf) {
                const pdfPath = data.pdf.split('/').pop();
                currentPdfName.textContent = pdfPath;
                currentPdfLink.href = `../../upload/${data.pdf}`;
                currentPdfContainer.classList.remove('hidden');
            } else {
                currentPdfContainer.classList.add('hidden');
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

        // Initialize drag and drop
        setupDragAndDrop();

        // Auto-hide notifications
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const notifications = document.querySelectorAll('#successToast, #errorToast');
                notifications.forEach(notification => {
                    notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 500);
                });
            }, 4000);
        });

        // Add smooth animations to cards on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        // Observe all cards
        document.querySelectorAll('.card-glow').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease-out';
            observer.observe(card);
        });

        // Add line clamp utility
        const style = document.createElement('style');
        style.textContent = `
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
            .line-clamp-4 {
                display: -webkit-box;
                -webkit-line-clamp: 4;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>