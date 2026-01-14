<?php include '../includes/header.php'; ?>
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../includes/db.php';

$success = "";
$error = "";

// ================= ADD EVENT =================
if(isset($_POST['add_event'])) {
    $name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $date = mysqli_real_escape_string($conn, $_POST['event_date']);
    $imagesArr = [];
    $uploadDir = __DIR__ . '/../../upload/';

    // Create upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if(!empty($_FILES['event_images']['name'][0])){
        foreach($_FILES['event_images']['name'] as $key => $imgName){
            $tmp = $_FILES['event_images']['tmp_name'][$key];
            $newName = time() . "_" . basename($imgName);
            $targetPath = $uploadDir . $newName;
            
            // Check if file is an actual image
            $check = getimagesize($tmp);
            if($check !== false && move_uploaded_file($tmp, $targetPath)){
                $imagesArr[] = $newName;
            }
        }
    }

    $imagesJson = !empty($imagesArr) ? json_encode($imagesArr) : '[]';

    $sql = "INSERT INTO events (event_name, event_date, event_images) VALUES ('$name', '$date', '$imagesJson')";
    if($conn->query($sql)){
        $success = "Event added successfully!";
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
            $filePath = __DIR__ . '/../../upload/' . $imgName;
            if(file_exists($filePath)){
                @unlink($filePath);
            }
        }
    }
    
    if($conn->query("DELETE FROM events WHERE id=$id")){
        $success = "Event deleted successfully!";
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
        
        $filePath = __DIR__ . '/../../upload/' . $image_to_delete;
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
        $success = "Event updated successfully!";

        // Add more images if uploaded
        if(!empty($_FILES['event_images']['name'][0])){
            $res = $conn->query("SELECT event_images FROM events WHERE id=$id");
            $row = $res->fetch_assoc();
            $images = json_decode($row['event_images'] ?? '[]', true) ?: [];

            $uploadDir = __DIR__ . '/../../upload/';
            foreach($_FILES['event_images']['name'] as $key => $imgName){
                $tmp = $_FILES['event_images']['tmp_name'][$key];
                $newName = time() . "_" . basename($imgName);
                $targetPath = $uploadDir . $newName;
                
                // Check if file is an actual image
                $check = getimagesize($tmp);
                if($check !== false && move_uploaded_file($tmp, $targetPath)){
                    $images[] = $newName;
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
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen p-6 md:p-8">

<!-- Floating Add Event Button -->
<button id="addEventBtn" 
        class="fixed bottom-8 right-8 z-40 w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full shadow-2xl flex items-center justify-center hover:from-blue-700 hover:to-indigo-700 transform hover:scale-110 transition-all duration-300 animate-bounce shadow-lg">
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
    </svg>
</button>

<!-- Add Event Modal -->
<div id="addEventModal" 
     class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-t-2xl border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Event
                </h3>
                <button onclick="closeAddEventModal()"
                        class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Modal Form -->
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
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
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors duration-200">
                    <input type="file" name="event_images[]" multiple 
                           class="w-full cursor-pointer"
                           accept="image/*">
                    <p class="text-gray-500 text-sm mt-1">Click or drag to upload multiple images</p>
                </div>
            </div>
            
            <button type="submit" name="add_event"
                    class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-700 hover:to-indigo-700 transform hover:-translate-y-1 transition-all duration-200 active:scale-95">
                <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Event
                </span>
            </button>
        </form>
    </div>
</div>

<!-- Success Notification -->
<?php if($success): ?>
<div id="success-notification" class="fixed top-6 right-6 z-50 animate-slideInRight">
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-xl shadow-2xl border-l-4 border-emerald-700 max-w-md transform transition-all duration-300 hover:scale-105">
        <div class="flex items-center space-x-3">
            <div class="animate-bounce">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span class="font-semibold"><?= htmlspecialchars($success) ?></span>
        </div>
    </div>
</div>
<script>
    setTimeout(() => {
        const notification = document.getElementById('success-notification');
        if(notification) {
            notification.classList.add('animate-slideOutRight');
            setTimeout(() => notification.remove(), 500);
        }
    }, 3000);
</script>
<?php endif; ?>

<!-- Error Notification -->
<?php if($error): ?>
<div id="error-notification" class="fixed top-6 right-6 z-50 animate-shake">
    <div class="bg-gradient-to-r from-red-500 to-rose-600 text-white px-6 py-4 rounded-xl shadow-2xl border-l-4 border-rose-700 max-w-md">
        <div class="flex items-center space-x-3">
            <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-semibold"><?= htmlspecialchars($error) ?></span>
        </div>
    </div>
</div>
<script>
    setTimeout(() => {
        const notification = document.getElementById('error-notification');
        if(notification) {
            notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(() => notification.remove(), 500);
        }
    }, 4000);
</script>
<?php endif; ?>

<div class="w-full">
    <!-- Header -->
    <div class="mb-10 text-center">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">🎉 Events Manager</h1>
        <p class="text-gray-600">Manage your events with style</p>
    </div>

    <!-- All Events Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            All Events
        </h2>
        
        <?php if($events && $events->num_rows == 0): ?>
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Events Yet</h3>
                    <p class="text-gray-500 mb-6">Click the + button below to add your first event</p>
                    <button onclick="openAddEventModal()"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-700 hover:to-indigo-700 transform hover:-translate-y-1 transition-all duration-200">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Your First Event
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="grid md:grid-cols-2 gap-6">
            <?php 
            if($events):
                while($row = $events->fetch_assoc()): 
                    // Safely decode event_images with null check
                    $images = json_decode($row['event_images'] ?? '[]', true) ?: [];
            ?>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <!-- Event Header -->
                <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-1"><?= htmlspecialchars($row['event_name']) ?></h3>
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span><?= htmlspecialchars($row['event_date']) ?></span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <!-- Delete Event Button -->
                            <a href="?delete_event=<?= $row['id'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this event?')"
                               class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </a>
                            
                            <!-- Update Event Button -->
                            <button onclick="openModal('modal-<?= $row['id'] ?>')"
                                    class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Update
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Event Images -->
                <div class="p-6">
                    <?php if(!empty($images)): ?>
                    <div class="flex flex-wrap gap-4">
                        <?php foreach($images as $img): ?>
                        <div class="relative group">
                            <img src="../../upload/<?= htmlspecialchars($img) ?>" 
                                 alt="Event Image"
                                 class="w-24 h-24 object-cover rounded-xl shadow-md group-hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                            <a href="?delete_image=1&event_id=<?= $row['id'] ?>&img=<?= urlencode($img) ?>" 
                               onclick="return confirm('Are you sure you want to delete this image?')"
                               class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
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
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Modal Form -->
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
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
                           accept="image/*">
                    <p class="text-gray-500 text-sm mt-1">Optional: Add more images</p>
                </div>
            </div>
            
            <button type="submit" name="update_event"
                    class="w-full py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg shadow-lg hover:from-green-600 hover:to-emerald-700 transform hover:-translate-y-1 transition-all duration-200 active:scale-95">
                <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
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
// Add Event Modal Functions
function openAddEventModal() {
    const modal = document.getElementById('addEventModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Animate modal content
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
    
    // Animate out
    if(modalContent) {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
    }
    
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 300);
}

// Update Event Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if(!modal) return;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Animate modal content
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
    
    // Animate out
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
            }, 300);
        }
    }
});

// Add Tailwind animation utilities
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100px);
        }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    .animate-slideInRight {
        animation: slideInRight 0.5s ease-out forwards;
    }
    
    .animate-slideOutRight {
        animation: slideOutRight 0.5s ease-out forwards;
    }
    
    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
`;
document.head.appendChild(style);
</script>
</body>
</html>