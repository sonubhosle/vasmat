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
if(isset($_POST['add_event'])) {
    $name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $date = mysqli_real_escape_string($conn, $_POST['event_date']);
    $imagesArr = [];
    
    if(!empty($_FILES['event_images']['name'][0])){
        foreach($_FILES['event_images']['name'] as $key => $imgName){
            $tmp = $_FILES['event_images']['tmp_name'][$key];
            $fileSize = $_FILES['event_images']['size'][$key];
            $newName = time() . "_" . uniqid() . "_" . basename($imgName);
            $targetPath = $eventsUploadDir . $newName;
            
            // Check file size (max 5MB per file)
            if($fileSize > 5 * 1024 * 1024) {
                $error = "Image '$imgName' is too large. Max 5MB per file.";
                continue;
            }
            
            // Check if file is an actual image
            $check = @getimagesize($tmp);
            if($check !== false && move_uploaded_file($tmp, $targetPath)){
                $imagesArr[] = 'events/' . $newName;
            } else {
                $error = "File '$imgName' is not a valid image.";
            }
        }
    }

    $imagesJson = !empty($imagesArr) ? json_encode($imagesArr) : '[]';

    $sql = "INSERT INTO events (event_name, event_date, event_images) VALUES ('$name', '$date', '$imagesJson')";
    if($conn->query($sql)){
        $success = "🎉 Event added successfully!";
    } else {
        $error = "DB Error: ".$conn->error;
    }
}

// ================= DELETE EVENT =================
if(isset($_GET['delete_event'])) {
    $id = intval($_GET['delete_event']);
    $res = $conn->query("SELECT event_images FROM events WHERE id=$id");
    
    if($res && $res->num_rows > 0){
        $row = $res->fetch_assoc();
        $images = json_decode($row['event_images'] ?? '[]', true) ?: [];
        foreach($images as $imgName){
            $filePath = $uploadBaseDir . $imgName;
            if(file_exists($filePath)){
                @unlink($filePath);
            }
        }
    }
    
    if($conn->query("DELETE FROM events WHERE id=$id")){
        $success = "🗑️ Event deleted successfully!";
    } else {
        $error = "Delete failed!";
    }
}

// ================= DELETE SINGLE IMAGE =================
if(isset($_GET['delete_image'])) {
    $event_id = intval($_GET['event_id']);
    $image_to_delete = basename($_GET['img'] ?? '');

    $res = $conn->query("SELECT event_images FROM events WHERE id=$event_id");
    if($res && $res->num_rows > 0){
        $row = $res->fetch_assoc();
        $images = json_decode($row['event_images'] ?? '[]', true) ?: [];

        $images = array_filter($images, fn($img) => $img != $image_to_delete);
        $imagesJson = json_encode(array_values($images));

        $conn->query("UPDATE events SET event_images='$imagesJson' WHERE id=$event_id");
        
        $filePath = $uploadBaseDir . $image_to_delete;
        if(file_exists($filePath)){
            @unlink($filePath);
        }
        $success = "Image deleted successfully!";
    }
}

// ================= UPDATE EVENT =================
if(isset($_POST['update_event'])){
    $id = intval($_POST['event_id']);
    $name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $date = mysqli_real_escape_string($conn, $_POST['event_date']);

    if($conn->query("UPDATE events SET event_name='$name', event_date='$date' WHERE id=$id")){
        $success = "✨ Event updated successfully!";

        // Add more images if uploaded
        if(!empty($_FILES['event_images']['name'][0])){
            $res = $conn->query("SELECT event_images FROM events WHERE id=$id");
            $row = $res->fetch_assoc();
            $images = json_decode($row['event_images'] ?? '[]', true) ?: [];

            foreach($_FILES['event_images']['name'] as $key => $imgName){
                $tmp = $_FILES['event_images']['tmp_name'][$key];
                $fileSize = $_FILES['event_images']['size'][$key];
                $newName = time() . "_" . uniqid() . "_" . basename($imgName);
                $targetPath = $eventsUploadDir . $newName;
                
                // Check file size (max 5MB per file)
                if($fileSize > 5 * 1024 * 1024) {
                    $error = "Image '$imgName' is too large. Max 5MB per file.";
                    continue;
                }
                
                // Check if file is an actual image
                $check = @getimagesize($tmp);
                if($check !== false && move_uploaded_file($tmp, $targetPath)){
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
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
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
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen p-4 md:p-6">

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay">
    <div class="text-center">
        <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-gray-600 font-medium">Processing...</p>
    </div>
</div>

<!-- Floating Add Event Button -->
<button id="addEventBtn" 
        class="fixed bottom-8 right-8 z-40 w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full shadow-2xl flex items-center justify-center hover:from-blue-700 hover:to-indigo-700 transform hover:scale-110 transition-all duration-300 shadow-lg">
    <i class="fas fa-plus text-xl"></i>
</button>

<!-- Add Event Modal -->
<div id="addEventModal" 
     class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-t-2xl border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                    Add New Event
                </h3>
                <button onclick="closeAddEventModal()"
                        class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <!-- Modal Form -->
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-4" onsubmit="showLoading()">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Name</label>
                <input type="text" name="event_name" required
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none"
                       placeholder="Enter event name">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Date</label>
                <input type="date" name="event_date" required
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Images</label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors duration-200 mb-4">
                    <input type="file" name="event_images[]" multiple 
                           class="w-full cursor-pointer"
                           accept="image/*" id="fileInput">
                    <p class="text-gray-500 text-sm mt-2">Max 5MB per file, JPG/PNG/GIF only</p>
                </div>
                <div id="filePreview" class="file-preview-container hidden space-y-2"></div>
            </div>
            
            <button type="submit" name="add_event"
                    class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-700 hover:to-indigo-700 transform hover:-translate-y-1 transition-all duration-200 active:scale-95">
                <span class="flex items-center justify-center">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Add Event
                </span>
            </button>
        </form>
    </div>
</div>

<!-- Success Notification -->
<?php if($success): ?>
<div id="success-notification" class="fixed top-6 right-6 z-50 animate-slide-in-right">
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-xl shadow-2xl border-l-4 border-emerald-700 max-w-md">
        <div class="flex items-center space-x-3">
            <i class="fas fa-check-circle text-xl"></i>
            <span class="font-semibold"><?= htmlspecialchars($success) ?></span>
        </div>
    </div>
</div>
<script>
    setTimeout(() => {
        const notification = document.getElementById('success-notification');
        if(notification) {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.5s';
            setTimeout(() => notification.remove(), 500);
        }
    }, 3000);
</script>
<?php endif; ?>

<!-- Error Notification -->
<?php if($error): ?>
<div id="error-notification" class="fixed top-6 right-6 z-50 animate-slide-in-right">
    <div class="bg-gradient-to-r from-red-500 to-rose-600 text-white px-6 py-4 rounded-xl shadow-2xl border-l-4 border-rose-700 max-w-md">
        <div class="flex items-center space-x-3">
            <i class="fas fa-exclamation-circle text-xl"></i>
            <span class="font-semibold"><?= htmlspecialchars($error) ?></span>
        </div>
    </div>
</div>
<script>
    setTimeout(() => {
        const notification = document.getElementById('error-notification');
        if(notification) {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.5s';
            setTimeout(() => notification.remove(), 500);
        }
    }, 4000);
</script>
<?php endif; ?>

<div class="max-w-7xl mx-auto animate-fade-in">
    <!-- Header -->
    <div class="mb-10 text-center">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">🎉 Events Manager</h1>
        <p class="text-gray-600">Manage your events with style</p>
    </div>

    <!-- All Events Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
            All Events
        </h2>
        
        <?php if($events && $events->num_rows == 0): ?>
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <i class="fas fa-calendar-times text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Events Yet</h3>
                    <p class="text-gray-500 mb-6">Click the + button below to add your first event</p>
                    <button onclick="openAddEventModal()"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-700 hover:to-indigo-700 transform hover:-translate-y-1 transition-all duration-200">
                        <span class="flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i>
                            Add Your First Event
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php 
            if($events):
                while($row = $events->fetch_assoc()): 
                    // Safely decode event_images with null check
                    $images = json_decode($row['event_images'] ?? '[]', true) ?: [];
                    $isPast = strtotime($row['event_date']) < time();
            ?>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <!-- Event Header -->
                <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-1"><?= htmlspecialchars($row['event_name']) ?></h3>
                            <div class="flex items-center text-gray-600">
                                <i class="far fa-calendar-alt mr-1"></i>
                                <span><?= date('F j, Y', strtotime($row['event_date'])) ?></span>
                                <?php if($isPast): ?>
                                    <span class="ml-2 text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">Past</span>
                                <?php else: ?>
                                    <span class="ml-2 text-xs px-2 py-1 bg-green-100 text-green-600 rounded">Upcoming</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <!-- Delete Event Button -->
                            <a href="?delete_event=<?= $row['id'] ?>" 
                               onclick="return confirmDelete()"
                               class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors duration-200 flex items-center">
                                <i class="fas fa-trash mr-1"></i>
                                Delete
                            </a>
                            
                            <!-- Update Event Button -->
                            <button onclick="openModal('modal-<?= $row['id'] ?>')"
                                    class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 flex items-center">
                                <i class="fas fa-edit mr-1"></i>
                                Update
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Event Images -->
                <div class="p-6">
                    <?php if(!empty($images)): ?>
                    <div class="flex flex-wrap gap-3">
                        <?php foreach($images as $img): 
                            $fullPath = '../../upload/' . htmlspecialchars($img);
                            $fileName = basename($img);
                        ?>
                        <div class="relative group">
                            <img src="<?= $fullPath ?>" 
                                 alt="Event Image"
                                 class="w-20 h-20 object-cover rounded-lg shadow-md group-hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                            <a href="?delete_image=1&event_id=<?= $row['id'] ?>&img=<?= urlencode($img) ?>" 
                               onclick="return confirm('Delete this image?')"
                               class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-image text-3xl opacity-30 mb-3"></i>
                        <p>No images uploaded yet</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php 
                endwhile;
            endif; 
            ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Update Event Modals -->
<?php 
if($events):
    $events->data_seek(0); // Reset pointer for modals
    while($row = $events->fetch_assoc()): 
?>
<div id="modal-<?= $row['id'] ?>" 
     class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-t-2xl border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Update Event</h3>
                <button onclick="closeModal('modal-<?= $row['id'] ?>')"
                        class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <!-- Modal Form -->
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-4" onsubmit="showLoading()">
            <input type="hidden" name="event_id" value="<?= $row['id'] ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Name</label>
                <input type="text" name="event_name" value="<?= htmlspecialchars($row['event_name']) ?>" required
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Date</label>
                <input type="date" name="event_date" value="<?= htmlspecialchars($row['event_date']) ?>" required
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Add More Images</label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors duration-200">
                    <input type="file" name="event_images[]" multiple 
                           class="w-full cursor-pointer"
                           accept="image/*" id="updateFileInput-<?= $row['id'] ?>">
                    <p class="text-gray-500 text-sm mt-2">Max 5MB per file, JPG/PNG/GIF only</p>
                </div>
            </div>
            
            <button type="submit" name="update_event"
                    class="w-full py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg shadow-lg hover:from-green-600 hover:to-emerald-700 transform hover:-translate-y-1 transition-all duration-200 active:scale-95">
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
    return confirm('Are you sure you want to delete this event? All images will be permanently deleted.');
}

// File preview for add modal
const fileInput = document.getElementById('fileInput');
const filePreview = document.getElementById('filePreview');

if (fileInput) {
    fileInput.addEventListener('change', function(e) {
        const files = Array.from(this.files);
        filePreview.innerHTML = '';
        
        if (files.length > 0) {
            filePreview.classList.remove('hidden');
            
            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-preview-item flex items-center justify-between p-2 bg-gray-50 rounded';
                    fileItem.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded flex items-center justify-center">
                                <i class="fas fa-image text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700 truncate max-w-xs">${file.name}</p>
                                <p class="text-xs text-gray-500">${fileSize} MB</p>
                            </div>
                        </div>
                        <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    filePreview.appendChild(fileItem);
                };
                reader.readAsDataURL(file);
            });
        } else {
            filePreview.classList.add('hidden');
        }
    });
}

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
        if(modalContent) {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }
    }, 10);
}

function closeAddEventModal() {
    const modal = document.getElementById('addEventModal');
    const modalContent = modal.querySelector('.bg-white');
    
    if(modalContent) {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
    }
    
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        // Reset form
        document.querySelector('#addEventModal form').reset();
        filePreview.classList.add('hidden');
        filePreview.innerHTML = '';
    }, 300);
}

// Update Event Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if(!modal) return;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    setTimeout(() => {
        const modalContent = modal.querySelector('.bg-white');
        if(modalContent) {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }
    }, 10);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if(!modal) return;
    
    const modalContent = modal.querySelector('.bg-white');
    
    if(modalContent) {
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
            if(modalContent) {
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
            }
            
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                // Reset add modal form
                if (modal.id === 'addEventModal') {
                    document.querySelector('#addEventModal form').reset();
                    filePreview.classList.add('hidden');
                    filePreview.innerHTML = '';
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
            if(modalContent) {
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
            }
            
            setTimeout(() => {
                openModal.classList.remove('flex');
                openModal.classList.add('hidden');
                // Reset add modal form
                if (openModal.id === 'addEventModal') {
                    document.querySelector('#addEventModal form').reset();
                    filePreview.classList.add('hidden');
                    filePreview.innerHTML = '';
                }
            }, 300);
        }
    }
});

// File validation for update modals
document.querySelectorAll('input[type="file"]').forEach(input => {
    if (!input.id.includes('fileInput')) {
        input.addEventListener('change', function() {
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