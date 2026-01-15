<?php
include '../includes/header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../includes/db.php';

$success = "";
$error = "";

// Set upload directory for events
$uploadBaseDir = __DIR__ . '/../../upload/';
$eventsUploadDir = $uploadBaseDir . 'events/';

// Create events upload directory if it doesn't exist
if (!file_exists($eventsUploadDir)) {
    mkdir($eventsUploadDir, 0755, true);
}

// Event status helper
function getEventStatus($eventDate) {
    $eventTime = strtotime($eventDate);
    $currentTime = time();
    
    if ($eventTime > $currentTime) {
        return [
            'status' => 'upcoming',
            'color' => 'bg-gradient-to-r from-emerald-500 to-green-600',
            'text' => 'Upcoming',
            'icon' => 'fa-calendar-check'
        ];
    } else {
        return [
            'status' => 'past',
            'color' => 'bg-gradient-to-r from-slate-500 to-slate-600',
            'text' => 'Past Event',
            'icon' => 'fa-calendar-times'
        ];
    }
}

// ================= ADD EVENT =================
if (isset($_POST['add_event'])) {
    $name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $date = mysqli_real_escape_string($conn, $_POST['event_date']);
    $imagesArr = [];

    if (!empty($_FILES['event_images']['name'][0])) {
        foreach ($_FILES['event_images']['name'] as $key => $imgName) {
            $tmp = $_FILES['event_images']['tmp_name'][$key];
            $fileSize = $_FILES['event_images']['size'][$key];
            $newName = time() . "_" . uniqid() . "_" . basename($imgName);
            $targetPath = $eventsUploadDir . $newName;

            // Check file size (max 5MB per file)
            if ($fileSize > 5 * 1024 * 1024) {
                $error = "Image '$imgName' is too large. Max 5MB per file.";
                continue;
            }

            // Check if file is an actual image
            $check = @getimagesize($tmp);
            if ($check !== false && move_uploaded_file($tmp, $targetPath)) {
                $imagesArr[] = 'events/' . $newName;
            } else {
                $error = "File '$imgName' is not a valid image.";
            }
        }
    }

    $imagesJson = !empty($imagesArr) ? json_encode($imagesArr) : '[]';

    $sql = "INSERT INTO events (event_name, event_date, event_images) 
            VALUES ('$name', '$date', '$imagesJson')";
    if ($conn->query($sql)) {
        $success = "🎉 Event added successfully!";
    } else {
        $error = "DB Error: " . $conn->error;
    }
}

// ================= DELETE EVENT =================
if (isset($_GET['delete_event'])) {
    $id = intval($_GET['delete_event']);
    $res = $conn->query("SELECT event_images FROM events WHERE id=$id");

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $images = json_decode($row['event_images'] ?? '[]', true) ?: [];
        foreach ($images as $imgName) {
            $filePath = $uploadBaseDir . $imgName;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }

    if ($conn->query("DELETE FROM events WHERE id=$id")) {
        $success = "🗑️ Event deleted successfully!";
    } else {
        $error = "Delete failed!";
    }
}

// ================= DELETE SINGLE IMAGE =================
if (isset($_GET['delete_image'])) {
    $event_id = intval($_GET['event_id']);
    $image_to_delete = basename($_GET['img'] ?? '');

    $res = $conn->query("SELECT event_images FROM events WHERE id=$event_id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $images = json_decode($row['event_images'] ?? '[]', true) ?: [];

        $images = array_filter($images, fn($img) => $img != $image_to_delete);
        $imagesJson = json_encode(array_values($images));

        $conn->query("UPDATE events SET event_images='$imagesJson' WHERE id=$event_id");

        $filePath = $uploadBaseDir . $image_to_delete;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        $success = "🗑️ Image deleted successfully!";
    }
}

// ================= UPDATE EVENT =================
if (isset($_POST['update_event'])) {
    $id = intval($_POST['event_id']);
    $name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $date = mysqli_real_escape_string($conn, $_POST['event_date']);

    if ($conn->query("UPDATE events SET event_name='$name', event_date='$date' WHERE id=$id")) {
        $success = "✨ Event updated successfully!";

        // Add more images if uploaded
        if (!empty($_FILES['event_images']['name'][0])) {
            $res = $conn->query("SELECT event_images FROM events WHERE id=$id");
            $row = $res->fetch_assoc();
            $images = json_decode($row['event_images'] ?? '[]', true) ?: [];

            foreach ($_FILES['event_images']['name'] as $key => $imgName) {
                $tmp = $_FILES['event_images']['tmp_name'][$key];
                $fileSize = $_FILES['event_images']['size'][$key];
                $newName = time() . "_" . uniqid() . "_" . basename($imgName);
                $targetPath = $eventsUploadDir . $newName;

                // Check file size (max 5MB per file)
                if ($fileSize > 5 * 1024 * 1024) {
                    $error = "Image '$imgName' is too large. Max 5MB per file.";
                    continue;
                }

                // Check if file is an actual image
                $check = @getimagesize($tmp);
                if ($check !== false && move_uploaded_file($tmp, $targetPath)) {
                    $images[] = 'events/' . $newName;
                } else {
                    $error = "File '$imgName' is not a valid image.";
                }
            }

            $imagesJson = json_encode($images);
            $conn->query("UPDATE events SET event_images='$imagesJson' WHERE id=$id");
        }

    } else {
        $error = "Update failed!";
    }
}

// ================= FETCH EVENTS =================
$events = $conn->query("SELECT * FROM events ORDER BY event_date DESC, id DESC");
$totalEvents = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$upcomingEvents = $conn->query("SELECT COUNT(*) as count FROM events WHERE event_date > NOW()")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management</title>
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

        .file-upload-area.dragover {
            border-color: #3b82f6;
            background: #dbeafe;
            transform: scale(1.02);
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

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
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
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-calendar-alt text-white text-xl"></i>
                        </div>
                        Events Management
                    </h1>
                    <p class="text-slate-600">Manage and showcase college events</p>
                </div>
                <button onclick="openAddModal()"
                        class="px-6 py-3.5 bg-gradient-to-r from-amber-400 to-amber-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-3 group">
                    <i class="fas fa-plus"></i>
                    Add New Event
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform duration-200"></i>
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 card-hover">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-amber-100 to-amber-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-amber-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 font-medium">Total Events</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $totalEvents ?></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 card-hover">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar-check text-emerald-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 font-medium">Upcoming</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $upcomingEvents ?></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 card-hover">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-images text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 font-medium">Total Images</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1">
                                <?php 
                                    $totalImages = 0;
                                    $eventsForStats = $conn->query("SELECT event_images FROM events");
                                    while($row = $eventsForStats->fetch_assoc()) {
                                        $images = json_decode($row['event_images'] ?? '[]', true) ?: [];
                                        $totalImages += count($images);
                                    }
                                    echo $totalImages;
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="animate-fade-in-up" style="animation-delay: 0.2s;">
            <?php if ($events->num_rows == 0): ?>
                <!-- Empty State -->
                <div class="text-center py-16 bg-white/50 backdrop-blur-sm rounded-2xl border-2 border-dashed border-slate-200">
                    <div class="max-w-md mx-auto">
                        <div class="w-32 h-32 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-calendar-plus text-slate-400 text-5xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-700 mb-3">No Events Yet</h3>
                        <p class="text-slate-500 mb-8">Start by adding your first event</p>
                        <button onclick="openAddModal()"
                                class="px-8 py-3.5 bg-gradient-to-r from-amber-400 to-amber-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Create First Event
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Events Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while($row = $events->fetch_assoc()): 
                        $images = json_decode($row['event_images'] ?? '[]', true) ?: [];
                        $status = getEventStatus($row['event_date']);
                    ?>
                    <div class="group bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100 card-hover animate-fade-in">
                        <!-- Event Header with Status -->
                        <div class="p-5 <?= $status['color'] ?> text-white">
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-2">
                                    <i class="fas <?= $status['icon'] ?>"></i>
                                    <span class="font-bold text-sm uppercase tracking-wider"><?= $status['text'] ?></span>
                                </div>
                                <span class="text-xs opacity-90 bg-white/20 px-2 py-1 rounded-full">
                                    <?= date('M d, Y', strtotime($row['event_date'])) ?>
                                </span>
                            </div>
                            <h3 class="text-lg font-bold line-clamp-1"><?= htmlspecialchars($row['event_name']) ?></h3>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <!-- Images Gallery -->
                            <div class="mb-4">
                                <?php if(!empty($images)): ?>
                                    <div class="grid grid-cols-3 gap-2">
                                        <?php foreach(array_slice($images, 0, 3) as $index => $image): ?>
                                            <div class="relative rounded-lg overflow-hidden aspect-square">
                                                <img src="../../upload/<?= htmlspecialchars($image) ?>" 
                                                     alt="Event Image" 
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <?php if($index == 2 && count($images) > 3): ?>
                                                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                                        <span class="text-white font-bold text-sm">+<?= count($images) - 3 ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-2 text-center">
                                        <i class="fas fa-images mr-1"></i>
                                        <?= count($images) ?> image<?= count($images) > 1 ? 's' : '' ?> uploaded
                                    </p>
                                <?php else: ?>
                                    <div class="text-center py-4 bg-slate-50 rounded-lg">
                                        <i class="fas fa-image text-slate-300 text-2xl mb-2"></i>
                                        <p class="text-slate-400 text-sm">No images uploaded</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Actions -->
                            <div class="pt-4 border-t border-slate-100 flex gap-2">
                                <button onclick='openEditModal(<?= json_encode($row) ?>)'
                                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 rounded-xl font-medium hover:from-blue-100 hover:to-indigo-100 transition-all duration-200 border border-blue-100 hover:border-blue-200 flex items-center justify-center gap-2 group">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                    <i class="fas fa-chevron-right text-xs opacity-70 group-hover:translate-x-0.5 transition-transform duration-200"></i>
                                </button>
                                <a href="?delete_event=<?= $row['id'] ?>" 
                                   onclick="return confirmDelete()"
                                   class="flex-1 px-4 py-2.5 bg-gradient-to-r from-rose-50 to-red-50 text-rose-700 rounded-xl font-medium hover:from-rose-100 hover:to-red-100 transition-all duration-200 border border-rose-100 hover:border-rose-200 flex items-center justify-center gap-2 group">
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
    <div id="addModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-amber-50 to-orange-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-amber-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            Add New Event
                        </h3>
                        <p class="text-slate-600 text-sm mt-1">Fill in the event details</p>
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
                    <!-- Event Name -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Event Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="event_name" required
                                class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all duration-300 bg-white shadow-sm"
                                placeholder="Enter event name">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                <i class="fas fa-tag"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Event Date -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Event Date <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="date" name="event_date" required
                                class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all duration-300 bg-white shadow-sm">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Images Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Event Images
                        </label>
                        <div class="file-upload-area relative" id="fileUploadArea">
                            <input type="file" name="event_images[]" id="fileUpload" multiple
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                accept="image/*"
                                onchange="handleFileSelect(event)">
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-amber-100 to-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-cloud-upload-alt text-amber-500 text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-slate-700 mb-2">Upload Event Images</h4>
                                <p class="text-slate-500 text-sm mb-4">Drag & drop images or click to browse</p>
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-lg text-slate-600 text-sm">
                                    <i class="fas fa-image"></i>
                                    <span>JPG, PNG, GIF - Max 5MB per file</span>
                                </div>
                            </div>
                        </div>
                        <div id="filePreview" class="hidden mt-3 grid grid-cols-3 gap-2"></div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex gap-3">
                            <button type="submit" name="add_event"
                                class="flex-1 px-6 py-3.5 bg-gradient-to-r from-amber-400 to-amber-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 active:scale-95 flex items-center justify-center gap-2 group">
                                <i class="fas fa-save"></i>
                                Create Event
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
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-edit text-white"></i>
                            </div>
                            Edit Event
                        </h3>
                        <p class="text-slate-600 text-sm mt-1">Update event details</p>
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
                    <input type="hidden" name="event_id" id="edit_id">
                    
                    <!-- Event Name -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Event Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="event_name" id="edit_name" required
                                class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                <i class="fas fa-tag"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Event Date -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Event Date <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="date" name="event_date" id="edit_date" required
                                class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Current Images -->
                    <div id="currentImagesContainer" class="hidden">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Current Images
                        </label>
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                            <div id="currentImagesGrid" class="grid grid-cols-3 gap-2 mb-3"></div>
                            <div class="text-sm text-slate-600">
                                <i class="fas fa-info-circle mr-1"></i>
                                These images will remain unless you add new ones
                            </div>
                        </div>
                    </div>

                    <!-- New Images Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Add More Images
                        </label>
                        <div class="file-upload-area relative" id="editFileUploadArea">
                            <input type="file" name="event_images[]" id="editFileUpload" multiple
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                accept="image/*"
                                onchange="handleEditFileSelect(event)">
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-sync-alt text-blue-500 text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-slate-700 mb-2">Upload More Images</h4>
                                <p class="text-slate-500 text-sm mb-4">Add new images to existing ones</p>
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-lg text-slate-600 text-sm">
                                    <i class="fas fa-image"></i>
                                    <span>JPG, PNG, GIF - Max 5MB per file</span>
                                </div>
                            </div>
                        </div>
                        <div id="editFilePreview" class="hidden mt-3 grid grid-cols-3 gap-2"></div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex gap-3">
                            <button type="submit" name="update_event"
                                class="flex-1 px-6 py-3.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 active:scale-95 flex items-center justify-center gap-2 group">
                                <i class="fas fa-save"></i>
                                Update Event
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
            return confirm('Are you sure you want to delete this event? This action cannot be undone.');
        }

        // File upload handling
        function handleFileSelect(event) {
            const files = Array.from(event.target.files);
            const uploadArea = document.getElementById('fileUploadArea');
            const preview = document.getElementById('filePreview');
            
            if (files.length > 0) {
                uploadArea.classList.add('dragover');
                preview.innerHTML = '';
                
                files.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'relative rounded-lg overflow-hidden aspect-square';
                            div.innerHTML = `
                                <img src="${e.target.result}" class="w-full h-full object-cover">
                                <button type="button" onclick="removeFile(${index})" 
                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">
                                    <i class="fas fa-times"></i>
                                </button>
                            `;
                            preview.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    }
                });
                
                preview.classList.remove('hidden');
                
                setTimeout(() => {
                    uploadArea.classList.remove('dragover');
                }, 1000);
            }
        }

        function handleEditFileSelect(event) {
            const files = Array.from(event.target.files);
            const uploadArea = document.getElementById('editFileUploadArea');
            const preview = document.getElementById('editFilePreview');
            
            if (files.length > 0) {
                uploadArea.classList.add('dragover');
                preview.innerHTML = '';
                
                files.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'relative rounded-lg overflow-hidden aspect-square';
                            div.innerHTML = `
                                <img src="${e.target.result}" class="w-full h-full object-cover">
                                <button type="button" onclick="removeEditFile(${index})" 
                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">
                                    <i class="fas fa-times"></i>
                                </button>
                            `;
                            preview.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    }
                });
                
                preview.classList.remove('hidden');
                
                setTimeout(() => {
                    uploadArea.classList.remove('dragover');
                }, 1000);
            }
        }

        function removeFile(index) {
            const input = document.getElementById('fileUpload');
            const dt = new DataTransfer();
            const files = Array.from(input.files);
            
            files.splice(index, 1);
            files.forEach(file => dt.items.add(file));
            
            input.files = dt.files;
            input.dispatchEvent(new Event('change'));
        }

        function removeEditFile(index) {
            const input = document.getElementById('editFileUpload');
            const dt = new DataTransfer();
            const files = Array.from(input.files);
            
            files.splice(index, 1);
            files.forEach(file => dt.items.add(file));
            
            input.files = dt.files;
            input.dispatchEvent(new Event('change'));
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
            document.getElementById('filePreview').classList.add('hidden');
            document.getElementById('filePreview').innerHTML = '';
        }

        function closeAddModal() {
            const modal = document.getElementById('addModal');
            const modalContent = modal.querySelector('.bg-white');
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('addForm').reset();
                document.getElementById('filePreview').classList.add('hidden');
                document.getElementById('filePreview').innerHTML = '';
            }, 300);
        }

        function openEditModal(data) {
            // Set form values
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_name').value = data.event_name;
            document.getElementById('edit_date').value = data.event_date;
            
            // Handle current images
            const currentImagesContainer = document.getElementById('currentImagesContainer');
            const currentImagesGrid = document.getElementById('currentImagesGrid');
            
            try {
                const images = JSON.parse(data.event_images || '[]');
                if (images.length > 0) {
                    currentImagesGrid.innerHTML = '';
                    images.forEach((image, index) => {
                        const div = document.createElement('div');
                        div.className = 'relative rounded-lg overflow-hidden aspect-square';
                        div.innerHTML = `
                            <img src="../../upload/${image}" class="w-full h-full object-cover">
                            <a href="?delete_image=1&event_id=${data.id}&img=${encodeURIComponent(image)}"
                               class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                <i class="fas fa-times"></i>
                            </a>
                        `;
                        currentImagesGrid.appendChild(div);
                    });
                    currentImagesContainer.classList.remove('hidden');
                } else {
                    currentImagesContainer.classList.add('hidden');
                }
            } catch (e) {
                currentImagesContainer.classList.add('hidden');
            }
            
            // Reset file preview
            document.getElementById('editFilePreview').classList.add('hidden');
            document.getElementById('editFilePreview').innerHTML = '';
            
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
                document.getElementById('editFilePreview').classList.add('hidden');
                document.getElementById('editFilePreview').innerHTML = '';
            }, 300);
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
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        const input = area.querySelector('input[type="file"]');
                        const dataTransfer = new DataTransfer();
                        Array.from(files).forEach(file => dataTransfer.items.add(file));
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

        // Close modals with ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
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