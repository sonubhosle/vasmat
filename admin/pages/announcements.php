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

// Badge color mapping
$badgeColors = [
    'hot' => 'bg-gradient-to-r from-red-500 to-pink-600',
    'event' => 'bg-gradient-to-r from-orange-500 to-amber-600',
    'new' => 'bg-gradient-to-r from-blue-500 to-cyan-600',
    'important' => 'bg-gradient-to-r from-purple-500 to-indigo-600',
    'update' => 'bg-gradient-to-r from-green-500 to-emerald-600',
    'urgent' => 'bg-gradient-to-r from-red-600 to-rose-700',
    'notice' => 'bg-gradient-to-r from-indigo-500 to-violet-600',
    'warning' => 'bg-gradient-to-r from-yellow-500 to-orange-500',
    'info' => 'bg-gradient-to-r from-gray-500 to-slate-600'
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

        /* Smooth transitions */
        .transition-all-300 {
            transition: all 0.3s ease;
        }

        .transition-transform-200 {
            transition: transform 0.2s ease;
        }

        /* Glass morphism */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Loading overlay */
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

        /* Card hover effects */
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="text-gray-600 font-medium">Loading...</p>
        </div>
    </div>

    <!-- Success Notification -->
    <?php if ($success): ?>
    <div id="successToast" class="fixed top-6 right-6 z-50 animate-slide-in-right">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-xl shadow-2xl border-l-4 border-emerald-700 max-w-md">
            <div class="flex items-center space-x-3">
                <i class="fas fa-check-circle text-xl"></i>
                <span class="font-semibold"><?= htmlspecialchars($success) ?></span>
            </div>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('successToast');
            toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    </script>
    <?php endif; ?>

    <!-- Error Notification -->
    <?php if ($error): ?>
    <div id="errorToast" class="fixed top-6 right-6 z-50 animate-slide-in-right">
        <div class="bg-gradient-to-r from-red-500 to-rose-600 text-white px-6 py-4 rounded-xl shadow-2xl border-l-4 border-rose-700 max-w-md">
            <div class="flex items-center space-x-3">
                <i class="fas fa-exclamation-circle text-xl"></i>
                <span class="font-semibold"><?= htmlspecialchars($error) ?></span>
            </div>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('errorToast');
            toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    </script>
    <?php endif; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-10 animate-fade-in-up">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-bullhorn text-blue-600 mr-3"></i>
                        Announcements Management
                    </h1>
                    <p class="text-gray-600">Manage all college announcements and notices</p>
                </div>
                <button onclick="openAddModal()"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-700 hover:to-indigo-700 transform hover:-translate-y-1 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Add New Announcement
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Announcements</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $totalAnnouncements ?></h3>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-bullhorn text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Active</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $activeAnnouncements ?></h3>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Last Updated</p>
                            <h3 class="text-lg font-bold text-gray-800 mt-1">
                                <?= date('M d, Y') ?>
                            </h3>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="animate-fade-in-up" style="animation-delay: 0.2s;">
            <?php if ($announcements->num_rows == 0): ?>
                <!-- Empty State -->
                <div class="text-center py-16 bg-white rounded-2xl shadow-lg border border-gray-100">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-bullhorn text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-3">No Announcements Yet</h3>
                        <p class="text-gray-500 mb-8">Get started by adding your first announcement</p>
                        <button onclick="openAddModal()"
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-700 hover:to-indigo-700 transform hover:-translate-y-1 transition-all duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Create Announcement
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Announcements Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while ($row = $announcements->fetch_assoc()): 
                        $badgeColor = $badgeColors[$row['badge']] ?? 'bg-gradient-to-r from-gray-500 to-slate-600';
                        $statusColor = $row['is_active'] ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100';
                        $statusIcon = $row['is_active'] ? 'fa-check-circle' : 'fa-times-circle';
                    ?>
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 card-hover animate-fade-in">
                        <!-- Badge Header -->
                        <div class="p-4 <?= $badgeColor ?> text-white">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-sm uppercase tracking-wider">
                                    <?= strtoupper($row['badge']) ?>
                                </span>
                                <span class="text-xs opacity-90">
                                    <?= date('M d, Y', strtotime($row['created_at'])) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-3 line-clamp-2">
                                <?= htmlspecialchars($row['title']) ?>
                            </h3>
                            
                            <?php if (!empty($row['description'])): ?>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                <?= htmlspecialchars($row['description']) ?>
                            </p>
                            <?php endif; ?>

                            <!-- PDF Badge -->
                            <?php if (!empty($row['pdf'])): ?>
                            <div class="mb-4">
                                <a href="../../upload/<?= htmlspecialchars($row['pdf']) ?>" 
                                   target="_blank"
                                   class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors duration-200">
                                    <i class="fas fa-file-pdf"></i>
                                    <span class="text-sm font-medium">View PDF</span>
                                </a>
                            </div>
                            <?php endif; ?>

                            <!-- Status -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium <?= $statusColor ?>">
                                    <i class="fas <?= $statusIcon ?> mr-1"></i>
                                    <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                                <span class="text-xs text-gray-500">
                                    <?= date('h:i A', strtotime($row['created_at'])) ?>
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2 pt-4 border-t border-gray-100">
                                <button onclick='openEditModal(<?= json_encode($row) ?>)'
                                        class="flex-1 px-4 py-2.5 bg-blue-50 text-blue-600 rounded-xl font-medium hover:bg-blue-100 transition-colors duration-200 flex items-center justify-center gap-2">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </button>
                                <a href="?page=announcements&delete=<?= $row['id'] ?>" 
                                   onclick="return confirmDelete()"
                                   class="flex-1 px-4 py-2.5 bg-red-50 text-red-600 rounded-xl font-medium hover:bg-red-100 transition-colors duration-200 flex items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt"></i>
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all duration-300 scale-95 opacity-0">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-t-2xl border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                        Add New Announcement
                    </h3>
                    <button onclick="closeAddModal()"
                            class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Form -->
            <form method="POST" enctype="multipart/form-data" class="p-6 space-y-4" onsubmit="showLoading()">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none"
                           placeholder="Enter announcement title">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none"
                              placeholder="Enter announcement description (optional)"></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Badge Type <span class="text-red-500">*</span>
                        </label>
                        <select name="badge" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none">
                            <option value="">Select Badge</option>
                            <option value="hot">Hot</option>
                            <option value="event">Event</option>
                            <option value="new">New</option>
                            <option value="important">Important</option>
                            <option value="update">Update</option>
                            <option value="urgent">Urgent</option>
                            <option value="notice">Notice</option>
                            <option value="warning">Warning</option>
                            <option value="info">Info</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="is_active"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">PDF File (Optional)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors duration-200">
                        <input type="file" name="pdf" 
                               class="w-full cursor-pointer"
                               accept=".pdf">
                        <p class="text-gray-500 text-sm mt-2">Drag & drop or click to upload PDF</p>
                    </div>
                </div>
                
                <div class="flex gap-4 pt-4">
                    <button type="submit" name="add_announcement"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-700 hover:to-indigo-700 transform hover:-translate-y-1 transition-all duration-200 active:scale-95">
                        <i class="fas fa-save mr-2"></i>
                        Save Announcement
                    </button>
                    <button type="button" onclick="closeAddModal()"
                            class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all duration-300 scale-95 opacity-0">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-t-2xl border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-edit text-blue-600 mr-2"></i>
                        Edit Announcement
                    </h3>
                    <button onclick="closeEditModal()"
                            class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Form -->
            <form method="POST" enctype="multipart/form-data" class="p-6 space-y-4" onsubmit="showLoading()">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="old_pdf" id="edit_old_pdf">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="edit_title" required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="edit_description" rows="3"
                              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none"></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Badge Type <span class="text-red-500">*</span>
                        </label>
                        <select name="badge" id="edit_badge" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none">
                            <option value="hot">Hot</option>
                            <option value="event">Event</option>
                            <option value="new">New</option>
                            <option value="important">Important</option>
                            <option value="update">Update</option>
                            <option value="urgent">Urgent</option>
                            <option value="notice">Notice</option>
                            <option value="warning">Warning</option>
                            <option value="info">Info</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="is_active" id="edit_status"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">PDF File (Optional)</label>
                    <div id="currentPdf" class="mb-2"></div>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors duration-200">
                        <input type="file" name="pdf" 
                               class="w-full cursor-pointer"
                               accept=".pdf">
                        <p class="text-gray-500 text-sm mt-2">Upload new PDF to replace existing</p>
                    </div>
                </div>
                
                <div class="flex gap-4 pt-4">
                    <button type="submit" name="update_announcement"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:from-green-600 hover:to-emerald-700 transform hover:-translate-y-1 transition-all duration-200 active:scale-95">
                        <i class="fas fa-save mr-2"></i>
                        Update Announcement
                    </button>
                    <button type="button" onclick="closeEditModal()"
                            class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Loading overlay
        function showLoading() {
            document.getElementById('loadingOverlay').classList.add('active');
            return true;
        }

        // Hide loading on page load
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.getElementById('loadingOverlay').classList.remove('active');
            }, 500);
        });

        // Confirmation for delete
        function confirmDelete() {
            return confirm('Are you sure you want to delete this announcement? This action cannot be undone.');
        }

        // Add Modal Functions
        function openAddModal() {
            const modal = document.getElementById('addModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                const modalContent = modal.querySelector('.bg-white');
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeAddModal() {
            const modal = document.getElementById('addModal');
            const modalContent = modal.querySelector('.bg-white');
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.querySelector('#addModal form').reset();
            }, 300);
        }

        // Edit Modal Functions
        function openEditModal(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_title').value = data.title;
            document.getElementById('edit_description').value = data.description;
            document.getElementById('edit_badge').value = data.badge;
            document.getElementById('edit_status').value = data.is_active;
            document.getElementById('edit_old_pdf').value = data.pdf || '';

            // Show current PDF if exists
            const currentPdfDiv = document.getElementById('currentPdf');
            if (data.pdf) {
                currentPdfDiv.innerHTML = `
                    <div class="flex items-center gap-2 p-2 bg-blue-50 rounded-lg">
                        <i class="fas fa-file-pdf text-red-500"></i>
                        <span class="text-sm text-gray-700">Current: ${data.pdf.split('/').pop()}</span>
                    </div>
                `;
            } else {
                currentPdfDiv.innerHTML = '';
            }

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
            }, 300);
        }

        // Close modals when clicking outside
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('bg-black')) {
                const modal = e.target.closest('.fixed');
                if (modal) {
                    const modalContent = modal.querySelector('.bg-white');
                    if (modalContent) {
                        modalContent.classList.remove('scale-100', 'opacity-100');
                        modalContent.classList.add('scale-95', 'opacity-0');
                    }
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        // Reset forms
                        if (modal.id === 'addModal') {
                            document.querySelector('#addModal form').reset();
                        }
                    }, 300);
                }
            }
        });

        // Close modals with ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });

        // File upload preview
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const fileName = this.files[0]?.name;
                if (fileName) {
                    const parent = this.closest('div');
                    const p = parent.querySelector('p');
                    if (p) {
                        p.textContent = `Selected: ${fileName}`;
                        p.classList.add('text-blue-600', 'font-medium');
                    }
                }
            });
        });

        // Smooth scroll to top on page reload
        window.onbeforeunload = function() {
            window.scrollTo(0, 0);
        };

        // Add animation to cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);

        // Observe all announcement cards
        document.querySelectorAll('.card-hover').forEach(card => {
            observer.observe(card);
        });

        // Add CSS utility classes
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
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>