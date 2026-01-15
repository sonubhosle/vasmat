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

    $sql = "INSERT INTO events (event_name, event_date, event_images) VALUES ('$name', '$date', '$imagesJson')";
    if ($conn->query($sql)) {
        $success = "Event added successfully!";
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
        $success = "Event deleted successfully!";
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
        $success = "Image deleted successfully!";
    }
}

// ================= UPDATE EVENT =================
if (isset($_POST['update_event'])) {
    $id = intval($_POST['event_id']);
    $name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $date = mysqli_real_escape_string($conn, $_POST['event_date']);

    if ($conn->query("UPDATE events SET event_name='$name', event_date='$date' WHERE id=$id")) {
        $success = "Event updated successfully!";

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
$events = $conn->query("SELECT * FROM events ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Events Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        .animate-slide-in-right {
            animation: slideInRight 0.3s ease-out;
        }

        .animate-pulse-custom {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
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

        /* File upload preview */
        .file-preview-container {
            max-height: 200px;
            overflow-y: auto;
        }

        .file-preview-item {
            transition: all 0.3s ease;
        }

        .file-preview-item:hover {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen p-4 md:p-6">

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="text-slate-600 font-medium">Processing...</p>
        </div>
    </div>


    <!-- Add Event Modal -->
    <div id="addEventModal"
        class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-xl transform transition-all duration-300 scale-95 opacity-0">
            <!-- Modal Header -->
            <div class="p-5 border-b border-slate-200 ">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-600 flex items-center gap-2">
                        Create Event
                    </h3>
                    <button onclick="closeAddEventModal()"
                        class="text-slate-500 w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center border border-slate-200  hover:text-slate-700 transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Form -->
            <form method="POST" enctype="multipart/form-data" class="p-6 space-y-4" onsubmit="showLoading()">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Event Name</label>
                        <input type="text" name="event_name" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500/50 transition-all ease-out duration-300 bg-white shadow-sm"
                            placeholder="Enter event name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Event Date</label>
                        <input type="date" name="event_date" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500/50 transition-all ease-out duration-300 bg-white shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-3">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-image text-lg text-slate-500"></i>
                            Event Images
                        </span>
                    </label>

                    <!-- Upload Area -->
                    <div class="group relative">
                        <div class="border-2 flex justify-center items-center gap-10 border-dashed border-slate-300 rounded-xl p-4 text-center 
                    hover:border-amber-400 hover:bg-blue-50/20 transition-all duration-300
                    bg-gradient-to-br from-white to-slate-50/50 cursor-pointer
                    shadow-[0_1px_2px_rgba(0,0,0,0.05)] hover:shadow-[0_2px_8px_rgba(59,130,246,0.1)]">

                            <div class="">
                                <!-- Upload Icon with Animation -->
                                <div class="w-16 h-16 mx-auto mb-4 relative">
                                    <div class="absolute inset-0 bg-gradient-to-r from-amber-100 to-amber-200 
                            rounded-full opacity-60 group-hover:opacity-80 transition-opacity"></div>
                                    <div class="relative w-full h-full flex items-center justify-center">

                                        <i class="fas fa-cloud-upload-alt absolute text-4xl text-amber-400
                                 animate-pulse-custom"></i>
                                    </div>
                                </div>

                                <!-- Upload Text -->


                            </div>
                            <!-- Original Input (invisible but clickable) -->
                            <input type="file" name="event_images[]" multiple
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*"
                                id="fileInput">

                            <div class="">
                                <div class="space-y-1 mb-3">
                                    <p
                                        class="text-base font-semibold text-slate-700 group-hover:text-amber-400 transition-colors">
                                        Drop your images here
                                    </p>
                                    <p class="text-sm text-slate-500">
                                        or <span class="text-emerald-500 font-medium">click to browse</span>
                                    </p>
                                </div>
                                <!-- Format Badges -->
                                <div class="flex items-center justify-center gap-2 mt-4">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium 
                           rounded-full border border-blue-200">
                                        JPG
                                    </span>
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium 
                           rounded-full border border-purple-200">
                                        PNG
                                    </span>
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-medium 
                           rounded-full border border-emerald-200">
                                        GIF
                                    </span>
                                </div>

                                <!-- Size Info -->
                                <p class="text-xs text-slate-400 mt-3">
                                    Maximum 5MB per file
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
                <button type="submit" name="add_event"
                    class="w-full py-3 bg-gradient-to-r from-amber-400 to-amber-500 text-white font-semibold rounded-xl  ">
                    <span class="flex items-center justify-center">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Add Event
                    </span>
                </button>
            </form>
        </div>
    </div>

    <!-- Success Notification -->
    <?php if ($success): ?>
        <div id="success-notification" class="fixed bottom-6 right-6 z-50 animate-slide-in-right">
            <div
                class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-xl shadow-2xl border-l-4 border-emerald-700 max-w-md">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-check-circle text-xl"></i>
                    <span class="font-semibold"><?= htmlspecialchars($success) ?></span>
                </div>
            </div>
        </div>
        <script>
            setTimeout(() => {
                const notification = document.getElementById('success-notification');
                if (notification) {
                    notification.style.opacity = '0';
                    notification.style.transition = 'opacity 0.5s';
                    setTimeout(() => notification.remove(), 500);
                }
            }, 3000);
        </script>
    <?php endif; ?>

    <!-- Error Notification -->
    <?php if ($error): ?>
        <div id="error-notification" class="fixed bottom-6 right-6 z-50 animate-slide-in-right">
            <div
                class="bg-gradient-to-r from-red-500 to-rose-600 text-white px-6 py-4 rounded-xl shadow-2xl border-l-4 border-rose-700 max-w-md">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                    <span class="font-semibold"><?= htmlspecialchars($error) ?></span>
                </div>
            </div>
        </div>
        <script>
            setTimeout(() => {
                const notification = document.getElementById('error-notification');
                if (notification) {
                    notification.style.opacity = '0';
                    notification.style.transition = 'opacity 0.5s';
                    setTimeout(() => notification.remove(), 500);
                }
            }, 4000);
        </script>
    <?php endif; ?>

    <div class="w-full animate-fade-in">
        <!-- Header -->


        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-lg flex items-center gap-2 font-bold text-slate-700 mb-2">
                    <i class="fas fa-calendar-alt "></i>
                    Events Manager
                </h1>
                <div class="bg-gradient-to-r from-amber-400 to-amber-600 rounded-full h-1.5 w-24"></div>
            </div>
            <button id="addEventBtn"
                class="= z-40 px-5 py-2.5 flex gap-2 items-center bg-gradient-to-r from-amber-400 to-amber-500 text-white rounded-xl    transition-all duration-300 ">
                <i class="fas fa-plus text-xl"></i> Add Event
            </button>
        </div>

        <!-- All Events Section -->
        <div class="mb-6">

            <?php if ($events && $events->num_rows == 0): ?>
                <div class="text-center py-16">
                    <div class="w-full">
                        <i class="fas fa-calendar-times text-5xl text-slate-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-slate-700 mb-2">No Events Yet</h3>
                        <p class="text-slate-500 mb-6">Add Events to get started</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php
                    if ($events):
                        while ($row = $events->fetch_assoc()):
                            // Safely decode event_images with null check
                            $images = json_decode($row['event_images'] ?? '[]', true) ?: [];
                            $isPast = strtotime($row['event_date']) < time();
                            ?>
                            <div
                                class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-200 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                <!-- Event Header -->
                                <div class="bg-gradient-to-r from-slate-50 to-white p-3  border-b">
                                    <div class="flex gap-5 items-start">
                                        <div class="">
                                            <?php if (!empty($images)):
                                                $firstImage = $images[0];
                                                $fullPath = '../../upload/' . htmlspecialchars($firstImage); ?>
                                                <div class="relative group  flex items-center">
                                                    <img src="<?= $fullPath ?>" alt="Event Image"
                                                        class="w-32 h-32 object-cover rounded-xl  ">
                                                    <a href="?delete_image=1&event_id=<?= $row['id'] ?>&img=<?= urlencode($firstImage) ?>"
                                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-lg hover:bg-red-600">
                                                        <i class="fas fa-times"></i></a>
                                                    <?php if (count($images) > 1): ?>
                                                        <div
                                                            class="absolute -bottom-2 -right-2 bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold shadow-lg">
                                                            +<?= count($images) - 1 ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-center py-8 text-slate-500">
                                                    <i class="fas fa-image text-4xl opacity-30 mb-3"></i>
                                                    <p class="text-slate-400">No images </p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex gap-4">
                                            <div class="info">
                                                <h3 class="text-[15px] font-bold text-slate-800 mb-1">
                                                    <?= htmlspecialchars($row['event_name']) ?>
                                                </h3>
                                                <div class="text-slate-600 text-[13px]">
                                                    <div class="flex items-center gap-1">
                                                        <i class="far fa-calendar-alt "></i>
                                                        <span><?= date('F j, Y', strtotime($row['event_date'])) ?></span>
                                                    </div>

                                                    <div class="mt-1 text-slate-600 text-[13px] flex gap-1 items-center ">
                                                        <i class="fas fa-images "></i>
                                                        <?= count($images) ?> image<?= count($images) > 1 ? 's' : '' ?> uploaded
                                                    </div>
                                                    <div class="mt-2">
                                                        <?php if ($isPast): ?>
                                                            <span
                                                                class=" text-xs px-2 py-1 bg-slate-100 text-slate-600 rounded">Past</span>
                                                        <?php else: ?>
                                                            <span
                                                                class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded">Upcoming</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="absolute top-2 right-2 flex space-x-2">
                                                <!-- Delete Event Button -->
                                                <a href="?delete_event=<?= $row['id'] ?>" "
                                                                                            class=" w-10 h-10 bg-red-50
                                        text-red-600 border border-red-100 rounded-xl hover:bg-red-100 transition-colors
                                        duration-200 flex items-center justify-center">
                                                    <i class="fas fa-trash "></i>
                                                </a>
                                                <!-- Update Event Button -->
                                                <button onclick="openModal('modal-<?= $row['id'] ?>')"
                                                    class="w-10 h-10 bg-blue-50 border border-blue-100 text-blue-600 rounded-xl hover:bg-blue-100 transition-colors duration-200 flex items-center justify-center">
                                                    <i class="fas fa-edit "></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php endwhile; endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Update Event Modals -->
    <?php
    if ($events):
        $events->data_seek(0); // Reset pointer for modals
        while ($row = $events->fetch_assoc()):
            ?>
            <div id="modal-<?= $row['id'] ?>"
                class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                <div
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-xl transform transition-all duration-300 scale-95 opacity-0">
                    <!-- Modal Header -->
                    <div class="p-5 border-b border-slate-200 ">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-slate-600 flex items-center gap-2">
                                Update Event
                            </h3>
                            <button onclick="closeModal('modal-<?= $row['id'] ?>')"
                                class="text-slate-500 w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center border border-slate-200 hover:text-slate-700 transition-colors duration-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Form -->
                    <form method="POST" enctype="multipart/form-data" class="p-6 space-y-4" onsubmit="showLoading()">
                        <input type="hidden" name="event_id" value="<?= $row['id'] ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Event Name</label>
                                <input type="text" name="event_name" value="<?= htmlspecialchars($row['event_name']) ?>"
                                    required
                                    class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500/50 transition-all ease-out duration-300 bg-white shadow-sm"
                                    placeholder="Enter event name">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Event Date</label>
                                <input type="date" name="event_date" value="<?= htmlspecialchars($row['event_date']) ?>"
                                    required
                                    class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500/50 transition-all ease-out duration-300 bg-white shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-3">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-image text-lg text-slate-500"></i>
                                    Add More Images
                                </span>
                            </label>

                            <!-- Upload Area -->
                            <div class="group relative">
                                <div class="border-2 flex justify-center items-center gap-10 border-dashed border-slate-300 rounded-xl p-4 text-center 
                        hover:border-amber-400 hover:bg-blue-50/20 transition-all duration-300
                        bg-gradient-to-br from-white to-slate-50/50 cursor-pointer
                        shadow-[0_1px_2px_rgba(0,0,0,0.05)] hover:shadow-[0_2px_8px_rgba(59,130,246,0.1)]">

                                    <div class="">
                                        <!-- Upload Icon with Animation -->
                                        <div class="w-16 h-16 mx-auto mb-4 relative">
                                            <div class="absolute inset-0 bg-gradient-to-r from-amber-100 to-amber-200 
                                    rounded-full opacity-60 group-hover:opacity-80 transition-opacity"></div>
                                            <div class="relative w-full h-full flex items-center justify-center">
                                                <i class="fas fa-cloud-upload-alt absolute text-4xl text-amber-400
                                        animate-pulse-custom"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Original Input (invisible but clickable) -->
                                    <input type="file" name="event_images[]" multiple
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*"
                                        id="updateFileInput-<?= $row['id'] ?>">

                                    <div class="">
                                        <div class="space-y-1 mb-3">
                                            <p
                                                class="text-base font-semibold text-slate-700 group-hover:text-amber-400 transition-colors">
                                                Drop your images here
                                            </p>
                                            <p class="text-sm text-slate-500">
                                                or <span class="text-emerald-500 font-medium">click to browse</span>
                                            </p>
                                        </div>

                                        <!-- Format Badges -->
                                        <div class="flex items-center justify-center gap-2 mt-4">
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium 
                                    rounded-full border border-blue-200">
                                                JPG
                                            </span>
                                            <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium 
                                    rounded-full border border-purple-200">
                                                PNG
                                            </span>
                                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-medium 
                                    rounded-full border border-emerald-200">
                                                GIF
                                            </span>
                                        </div>

                                        <!-- Size Info -->
                                        <p class="text-xs text-slate-400 mt-3">
                                            Maximum 5MB per file
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Images Display (Optional - if you want to show existing images) -->
                            <?php if (!empty($row['image_path'])):
                                $images = json_decode($row['image_path'], true);
                                if (is_array($images) && !empty($images)): ?>
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-slate-700 mb-2">
                                            <i class="fas fa-images mr-2 text-slate-500"></i>
                                            Current Images
                                        </label>
                                        <div class="flex flex-wrap gap-2 p-3 bg-slate-50 rounded-xl">
                                            <?php foreach ($images as $index => $image): ?>
                                                <div class="relative">
                                                    <img src="<?= htmlspecialchars($image) ?>" alt="Event Image"
                                                        class="w-16 h-16 object-cover rounded-lg border border-slate-200">
                                                    <span
                                                        class="absolute -top-1 -right-1 bg-slate-700 text-white text-xs px-1.5 py-0.5 rounded-full">
                                                        <?= $index + 1 ?>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; endif; ?>
                        </div>

                        <button type="submit" name="update_event"
                            class="w-full py-3 bg-gradient-to-r from-green-400 to-emerald-500 text-white font-semibold rounded-xl shadow-lg hover:from-green-500 hover:to-emerald-600 transform hover:-translate-y-0.5 transition-all duration-200 active:scale-95">
                            <span class="flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i>
                                Update Event
                            </span>
                        </button>
                    </form>
                </div>
            </div>
            <?php
        endwhile;
    endif;
    ?>

    <script>
        function showLoading() {
            document.getElementById('loadingOverlay').classList.add('active');
            return true;
        }

        window.addEventListener('load', () => {
            setTimeout(() => {
                document.getElementById('loadingOverlay').classList.remove('active');
            }, 500);
        });



        function removeFile(index) {
            const dt = new DataTransfer();
            const files = Array.from(fileInput.files);

            files.splice(index, 1);

            files.forEach(file => {
                dt.items.add(file);
            });

            fileInput.files = dt.files;
            fileInput.dispatchEvent(new Event('change'));
        }

        // Add Event Modal Functions
        function openAddEventModal() {
            const modal = document.getElementById('addEventModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                const modalContent = modal.querySelector('.bg-white');
                if (modalContent) {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }
            }, 10);
        }

        function closeAddEventModal() {
            const modal = document.getElementById('addEventModal');
            const modalContent = modal.querySelector('.bg-white');

            if (modalContent) {
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
            }

            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                // Reset form
                document.querySelector('#addEventModal form').reset();

            }, 300);
        }

        // Update Event Modal Functions
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                const modalContent = modal.querySelector('.bg-white');
                if (modalContent) {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }
            }, 10);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            const modalContent = modal.querySelector('.bg-white');

            if (modalContent) {
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
            }

            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        // Event Listeners
        document.getElementById('addEventBtn').addEventListener('click', openAddEventModal);

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
                        modal.classList.remove('flex');
                        modal.classList.add('hidden');
                        // Reset add modal form
                        if (modal.id === 'addEventModal') {
                            document.querySelector('#addEventModal form').reset();

                        }
                    }, 300);
                }
            }
        });

        // Close modals with ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.fixed:not(.hidden)');
                if (openModal) {
                    const modalContent = openModal.querySelector('.bg-white');
                    if (modalContent) {
                        modalContent.classList.remove('scale-100', 'opacity-100');
                        modalContent.classList.add('scale-95', 'opacity-0');
                    }

                    setTimeout(() => {
                        openModal.classList.remove('flex');
                        openModal.classList.add('hidden');
                        // Reset add modal form
                        if (openModal.id === 'addEventModal') {
                            document.querySelector('#addEventModal form').reset();

                        }
                    }, 300);
                }
            }
        });

        // File validation for update modals
        document.querySelectorAll('input[type="file"]').forEach(input => {
            if (!input.id.includes('fileInput')) {
                input.addEventListener('change', function () {
                    const files = Array.from(this.files);
                    let isValid = true;

                    files.forEach(file => {
                        const fileSize = file.size / 1024 / 1024; // MB
                        if (fileSize > 5) {
                            alert(`File "${file.name}" is too large. Max 5MB per file.`);
                            isValid = false;
                        }

                        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        if (!validTypes.includes(file.type)) {
                            alert(`File "${file.name}" is not a valid image type.`);
                            isValid = false;
                        }
                    });

                    if (!isValid) {
                        this.value = '';
                    }
                });
            }
        });
    </script>
</body>

</html>