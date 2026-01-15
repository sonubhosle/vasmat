<?php
 
error_reporting(E_ALL);
ini_set('display_errors', 1);


include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/db.php';

$success = "";
$error = "";

$uploadDir = __DIR__ . '/../../upload/faculty/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Faculty type colors and icons
$facultyTypes = [
    'teaching' => [
        'name' => 'Teaching',
        'color' => 'bg-gradient-to-r from-blue-500 to-indigo-600',
        'icon' => 'fas fa-chalkboard-teacher',
        'badge' => 'bg-blue-100 text-blue-800'
    ],
    'non-teaching' => [
        'name' => 'Non Teaching',
        'color' => 'bg-gradient-to-r from-green-500 to-emerald-600',
        'icon' => 'fas fa-user-tie',
        'badge' => 'bg-green-100 text-green-800'
    ],
    'visiting' => [
        'name' => 'Visiting',
        'color' => 'bg-gradient-to-r from-purple-500 to-violet-600',
        'icon' => 'fas fa-user-clock',
        'badge' => 'bg-purple-100 text-purple-800'
    ],
    'guest' => [
        'name' => 'Guest',
        'color' => 'bg-gradient-to-r from-amber-500 to-orange-600',
        'icon' => 'fas fa-user-graduate',
        'badge' => 'bg-amber-100 text-amber-800'
    ]
];

// ================= ADD =================
if(isset($_POST['add_faculty'])){
    $name = $conn->real_escape_string($_POST['name']);
    $designation = $conn->real_escape_string($_POST['designation']);
    $education = $conn->real_escape_string($_POST['education']);
    $experience = $conn->real_escape_string($_POST['experience']);
    $faculty_type = $conn->real_escape_string($_POST['faculty_type']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $photoName = "";

    if(!empty($_FILES['photo']['name'])){
        $clean = str_replace(" ", "_", $_FILES['photo']['name']);
        $photoName = time() . "_" . $clean;
        move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $photoName);
    }

    $sql = "INSERT INTO faculty (name, designation, education, experience, faculty_type, email, phone, photo, is_active)
            VALUES ('$name','$designation','$education','$experience','$faculty_type','$email','$phone','$photoName',$is_active)";

    if($conn->query($sql)){
        $success = "🎉 Faculty added successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// ================= DELETE =================
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);

    $res = $conn->query("SELECT photo FROM faculty WHERE id=$id");
    if($row = $res->fetch_assoc()){
        if($row['photo']){
            @unlink($uploadDir . $row['photo']);
        }
    }

    if($conn->query("DELETE FROM faculty WHERE id=$id")){
        $success = "🗑️ Faculty deleted successfully!";
    } else {
        $error = "Failed to delete faculty.";
    }
}

// ================= UPDATE =================
if(isset($_POST['update_faculty'])){
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $designation = $conn->real_escape_string($_POST['designation']);
    $education = $conn->real_escape_string($_POST['education']);
    $experience = $conn->real_escape_string($_POST['experience']);
    $faculty_type = $conn->real_escape_string($_POST['faculty_type']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $photoUpdate = "";

    if(!empty($_FILES['photo']['name'])){
        $res = $conn->query("SELECT photo FROM faculty WHERE id=$id");
        if($old = $res->fetch_assoc()){
            if($old['photo']){
                @unlink($uploadDir . $old['photo']);
            }
        }

        $clean = str_replace(" ", "_", $_FILES['photo']['name']);
        $newName = time() . "_" . $clean;
        move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $newName);
        $photoUpdate = ", photo='$newName'";
    }

    $sql = "UPDATE faculty 
            SET name='$name', designation='$designation', education='$education', 
                experience='$experience', faculty_type='$faculty_type', 
                email='$email', phone='$phone', is_active=$is_active
                $photoUpdate
            WHERE id=$id";

    if($conn->query($sql)){
        $success = "✨ Faculty updated successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// ================= FETCH =================
$faculty = $conn->query("SELECT * FROM faculty ORDER BY id DESC");
$totalFaculty = $conn->query("SELECT COUNT(*) as count FROM faculty")->fetch_assoc()['count'];
$activeFaculty = $conn->query("SELECT COUNT(*) as count FROM faculty WHERE is_active = 1")->fetch_assoc()['count'];
$teachingFaculty = $conn->query("SELECT COUNT(*) as count FROM faculty WHERE faculty_type = 'teaching'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Management</title>
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

        .gradient-text {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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

        .custom-select {
            position: relative;
            cursor: pointer;
        }

        .custom-select-options {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
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

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        Faculty Management
                    </h1>
                    <p class="text-slate-600">Manage teaching and non-teaching faculty members</p>
                </div>
                <button onclick="openAddModal()"
                        class="px-6 py-3.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-3 group">
                    <i class="fas fa-user-plus"></i>
                    Add New Faculty
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform duration-200"></i>
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 card-hover">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 font-medium">Total Faculty</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $totalFaculty ?></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 card-hover">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-emerald-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 font-medium">Active</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $activeFaculty ?></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 card-hover">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chalkboard-teacher text-purple-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 font-medium">Teaching</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $teachingFaculty ?></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-100 card-hover">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-amber-100 to-amber-200 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-tie text-amber-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 font-medium">Non-Teaching</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $totalFaculty - $teachingFaculty ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="animate-fade-in-up" style="animation-delay: 0.2s;">
            <?php if ($faculty->num_rows == 0): ?>
                <!-- Empty State -->
                <div class="text-center py-16 bg-white/50 backdrop-blur-sm rounded-2xl border-2 border-dashed border-slate-200">
                    <div class="max-w-md mx-auto">
                        <div class="w-32 h-32 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-users text-slate-400 text-5xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-700 mb-3">No Faculty Members Yet</h3>
                        <p class="text-slate-500 mb-8">Start by adding your first faculty member</p>
                        <button onclick="openAddModal()"
                                class="px-8 py-3.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 inline-flex items-center gap-2">
                            <i class="fas fa-user-plus"></i>
                            Add First Faculty
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Faculty Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php while($row = $faculty->fetch_assoc()): 
                        $facultyType = $facultyTypes[$row['faculty_type']] ?? $facultyTypes['teaching'];
                        $statusColor = $row['is_active'] ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 'text-rose-600 bg-rose-50 border-rose-100';
                        $statusIcon = $row['is_active'] ? 'fa-check-circle' : 'fa-user-slash';
                    ?>
                    <div class="group bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100 card-hover animate-fade-in">
                        <!-- Photo Section -->
                        <div class="relative h-48 overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                            <?php if($row['photo']): ?>
                                <img src="/mit-college/upload/faculty/<?= htmlspecialchars($row['photo']) ?>" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-user text-slate-300 text-6xl"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Faculty Type Badge -->
                            <div class="absolute top-4 right-4 px-3 py-1.5 <?= $facultyType['badge'] ?> rounded-full text-xs font-semibold flex items-center gap-1.5">
                                <i class="<?= $facultyType['icon'] ?>"></i>
                                <?= $facultyType['name'] ?>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="absolute top-4 left-4 px-3 py-1.5 <?= $statusColor ?> rounded-full text-xs font-semibold border">
                                <i class="fas <?= $statusIcon ?> mr-1"></i>
                                <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <!-- Name & Designation -->
                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-slate-800 line-clamp-1 group-hover:text-blue-600 transition-colors duration-200">
                                    <?= htmlspecialchars($row['name']) ?>
                                </h3>
                                <p class="text-sm text-blue-600 font-medium mt-1"><?= htmlspecialchars($row['designation']) ?></p>
                            </div>

                            <!-- Education -->
                            <div class="mb-4">
                                <div class="flex items-start gap-2">
                                    <i class="fas fa-graduation-cap text-slate-400 mt-1 flex-shrink-0"></i>
                                    <p class="text-sm text-slate-600 line-clamp-2"><?= htmlspecialchars($row['education']) ?></p>
                                </div>
                            </div>

                            <!-- Experience -->
                            <?php if(!empty($row['experience'])): ?>
                            <div class="mb-4">
                                <div class="flex items-start gap-2">
                                    <i class="fas fa-briefcase text-slate-400 mt-1 flex-shrink-0"></i>
                                    <p class="text-sm text-slate-600 line-clamp-2"><?= htmlspecialchars($row['experience']) ?></p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Contact Info -->
                            <div class="space-y-2 mb-4">
                                <?php if(!empty($row['email'])): ?>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-envelope text-slate-400 text-sm"></i>
                                    <p class="text-xs text-slate-600 truncate"><?= htmlspecialchars($row['email']) ?></p>
                                </div>
                                <?php endif; ?>
                                
                                <?php if(!empty($row['phone'])): ?>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-phone text-slate-400 text-sm"></i>
                                    <p class="text-xs text-slate-600"><?= htmlspecialchars($row['phone']) ?></p>
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
                                <a href="?delete=<?= $row['id'] ?>" 
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
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-plus text-white"></i>
                            </div>
                            Add New Faculty
                        </h3>
                        <p class="text-slate-600 text-sm mt-1">Fill in the faculty member details</p>
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
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="name" required
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm"
                                    placeholder="Enter full name">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Designation -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Designation <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="designation" required
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm"
                                    placeholder="Professor, Lecturer, etc.">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Education -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Education <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <textarea name="education" rows="2" required
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm resize-none"
                                    placeholder="PhD in Computer Science, M.Tech, etc."></textarea>
                                <div class="absolute left-4 top-4 text-slate-400">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Experience -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Experience
                            </label>
                            <div class="relative">
                                <textarea name="experience" rows="2"
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm resize-none"
                                    placeholder="10+ years of teaching experience"></textarea>
                                <div class="absolute left-4 top-4 text-slate-400">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Faculty Type -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Faculty Type <span class="text-rose-500">*</span>
                            </label>
                            <div class="custom-select" id="facultyTypeSelect">
                                <div class="custom-select-trigger w-full px-4 py-3.5 border border-slate-200 rounded-xl text-slate-800 bg-white shadow-sm flex items-center justify-between cursor-pointer hover:border-slate-300 transition-all duration-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-chalkboard-teacher text-white text-sm"></i>
                                        </div>
                                        <span class="font-medium">Teaching</span>
                                    </div>
                                    <i class="fas fa-chevron-down text-slate-400 transition-transform duration-200"></i>
                                </div>
                                <input type="hidden" name="faculty_type" value="teaching">
                                <div class="custom-select-options mt-2">
                                    <?php foreach ($facultyTypes as $key => $type): ?>
                                    <div class="custom-select-option" data-value="<?= $key ?>">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 <?= $type['color'] ?> rounded-lg flex items-center justify-center">
                                                <i class="<?= $type['icon'] ?> text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800"><?= $type['name'] ?></div>
                                                <div class="text-xs text-slate-500"><?= ucfirst($key) ?> faculty</div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Status
                            </label>
                            <div class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl bg-white">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" checked 
                                           class="w-5 h-5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                    <span class="text-slate-700 font-medium">Active</span>
                                </label>
                                <div class="text-xs text-slate-500">
                                    <i class="fas fa-info-circle"></i> Active faculty will be visible
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Email
                            </label>
                            <div class="relative">
                                <input type="email" name="email"
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm"
                                    placeholder="faculty@email.com">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Phone
                            </label>
                            <div class="relative">
                                <input type="tel" name="phone"
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm"
                                    placeholder="+91 1234567890">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-phone"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Photo Upload -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Profile Photo (Optional)
                            </label>
                            <div class="file-upload-area relative" id="fileUploadArea">
                                <input type="file" name="photo" id="photoUpload" 
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    accept="image/*"
                                    onchange="handleFileSelect(event)">
                                <div class="p-8 text-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-camera text-blue-500 text-2xl"></i>
                                    </div>
                                    <h4 class="font-semibold text-slate-700 mb-2">Upload Profile Photo</h4>
                                    <p class="text-slate-500 text-sm mb-4">Drag & drop image or click to browse</p>
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-lg text-slate-600 text-sm">
                                        <i class="fas fa-image"></i>
                                        <span>JPG, PNG, Max 5MB</span>
                                    </div>
                                </div>
                            </div>
                            <div id="filePreview" class="hidden mt-3"></div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex gap-3">
                            <button type="submit" name="add_faculty"
                                class="flex-1 px-6 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 active:scale-95 flex items-center justify-center gap-2 group">
                                <i class="fas fa-save"></i>
                                Add Faculty
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
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-green-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-edit text-white"></i>
                            </div>
                            Edit Faculty
                        </h3>
                        <p class="text-slate-600 text-sm mt-1">Update faculty member details</p>
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
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="name" id="edit_name" required
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Designation -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Designation <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="designation" id="edit_designation" required
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Education -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Education <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <textarea name="education" id="edit_education" rows="2" required
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm resize-none"></textarea>
                                <div class="absolute left-4 top-4 text-slate-400">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Experience -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Experience
                            </label>
                            <div class="relative">
                                <textarea name="experience" id="edit_experience" rows="2"
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm resize-none"></textarea>
                                <div class="absolute left-4 top-4 text-slate-400">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Faculty Type -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Faculty Type <span class="text-rose-500">*</span>
                            </label>
                            <div class="custom-select" id="editFacultyTypeSelect">
                                <div class="custom-select-trigger w-full px-4 py-3.5 border border-slate-200 rounded-xl text-slate-800 bg-white shadow-sm flex items-center justify-between cursor-pointer hover:border-slate-300 transition-all duration-200">
                                    <div class="flex items-center gap-3">
                                        <div id="editFacultyTypePreview" class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-chalkboard-teacher text-white text-sm"></i>
                                        </div>
                                        <span id="editFacultyTypeText" class="font-medium">Teaching</span>
                                    </div>
                                    <i class="fas fa-chevron-down text-slate-400 transition-transform duration-200"></i>
                                </div>
                                <input type="hidden" name="faculty_type" id="edit_faculty_type" value="teaching">
                                <div class="custom-select-options mt-2">
                                    <?php foreach ($facultyTypes as $key => $type): ?>
                                    <div class="custom-select-option" data-value="<?= $key ?>">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 <?= $type['color'] ?> rounded-lg flex items-center justify-center">
                                                <i class="<?= $type['icon'] ?> text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-800"><?= $type['name'] ?></div>
                                                <div class="text-xs text-slate-500"><?= ucfirst($key) ?> faculty</div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Status
                            </label>
                            <div class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl bg-white">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" id="edit_active"
                                           class="w-5 h-5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                    <span class="text-slate-700 font-medium">Active</span>
                                </label>
                                <div class="text-xs text-slate-500">
                                    <i class="fas fa-info-circle"></i> Active faculty will be visible
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Email
                            </label>
                            <div class="relative">
                                <input type="email" name="email" id="edit_email"
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Phone
                            </label>
                            <div class="relative">
                                <input type="tel" name="phone" id="edit_phone"
                                    class="w-full px-4 py-3.5 pl-12 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white shadow-sm">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-phone"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Current Photo -->
                        <div id="currentPhotoContainer" class="md:col-span-2 hidden">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Current Photo
                            </label>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-slate-100 to-slate-200 overflow-hidden flex items-center justify-center">
                                        <img id="currentPhotoImg" src="" class="w-full h-full object-cover">
                                    </div>
                                    <div class="text-sm text-slate-600">
                                        Current profile photo will be replaced
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- New Photo Upload -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                New Profile Photo (Optional)
                            </label>
                            <div class="file-upload-area relative" id="editFileUploadArea">
                                <input type="file" name="photo" id="editPhotoUpload" 
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    accept="image/*"
                                    onchange="handleEditFileSelect(event)">
                                <div class="p-8 text-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-sync-alt text-blue-500 text-2xl"></i>
                                    </div>
                                    <h4 class="font-semibold text-slate-700 mb-2">Update Profile Photo</h4>
                                    <p class="text-slate-500 text-sm mb-4">Upload new photo to replace existing</p>
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-lg text-slate-600 text-sm">
                                        <i class="fas fa-image"></i>
                                        <span>JPG, PNG, Max 5MB</span>
                                    </div>
                                </div>
                            </div>
                            <div id="editFilePreview" class="hidden mt-3"></div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex gap-3">
                            <button type="submit" name="update_faculty"
                                class="flex-1 px-6 py-3.5 bg-gradient-to-r from-emerald-500 to-green-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 active:scale-95 flex items-center justify-center gap-2 group">
                                <i class="fas fa-save"></i>
                                Update Faculty
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
            return confirm('Are you sure you want to delete this faculty member? This action cannot be undone.');
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

                document.addEventListener('click', () => {
                    this.close();
                });

                this.element.querySelector('.custom-select-options').addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }

            toggle() {
                this.element.classList.toggle('open');
                const icon = this.trigger.querySelector('.fa-chevron-down');
                icon.style.transform = this.element.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
            }

            close() {
                this.element.classList.remove('open');
                this.trigger.querySelector('.fa-chevron-down').style.transform = 'rotate(0deg)';
            }

            select(option) {
                const value = option.dataset.value;
                const text = option.querySelector('.font-medium').textContent;
                const preview = option.querySelector('div.w-8.h-8').cloneNode(true);
                const textSpan = option.querySelector('.font-medium').cloneNode(true);

                const triggerContent = this.trigger.querySelector('.flex.items-center.gap-3');
                triggerContent.innerHTML = '';
                triggerContent.appendChild(preview);
                triggerContent.appendChild(textSpan);

                this.hiddenInput.value = value;

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
        const facultyTypeSelect = new CustomSelect(document.getElementById('facultyTypeSelect'));
        const editFacultyTypeSelect = new CustomSelect(document.getElementById('editFacultyTypeSelect'));

        // File upload handling
        function handleFileSelect(event) {
            const file = event.target.files[0];
            const uploadArea = document.getElementById('fileUploadArea');
            const preview = document.getElementById('filePreview');
            
            if (file && file.type.startsWith('image/')) {
                uploadArea.classList.add('dragover');
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <div class="file-preview flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg overflow-hidden">
                                    <img src="${e.target.result}" class="w-full h-full object-cover">
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
                };
                reader.readAsDataURL(file);
                
                setTimeout(() => {
                    uploadArea.classList.remove('dragover');
                }, 1000);
            } else {
                alert('Please select an image file (JPG, PNG).');
                event.target.value = '';
            }
        }

        function handleEditFileSelect(event) {
            const file = event.target.files[0];
            const uploadArea = document.getElementById('editFileUploadArea');
            const preview = document.getElementById('editFilePreview');
            
            if (file && file.type.startsWith('image/')) {
                uploadArea.classList.add('dragover');
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <div class="file-preview flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg overflow-hidden">
                                    <img src="${e.target.result}" class="w-full h-full object-cover">
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
                };
                reader.readAsDataURL(file);
                
                setTimeout(() => {
                    uploadArea.classList.remove('dragover');
                }, 1000);
            } else {
                alert('Please select an image file (JPG, PNG).');
                event.target.value = '';
            }
        }

        function removeFile() {
            document.getElementById('photoUpload').value = '';
            document.getElementById('filePreview').classList.add('hidden');
            document.getElementById('filePreview').innerHTML = '';
        }

        function removeEditFile() {
            document.getElementById('editPhotoUpload').value = '';
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
            facultyTypeSelect.setValue('teaching');
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
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_designation').value = data.designation;
            document.getElementById('edit_education').value = data.education;
            document.getElementById('edit_experience').value = data.experience || '';
            document.getElementById('edit_email').value = data.email || '';
            document.getElementById('edit_phone').value = data.phone || '';
            document.getElementById('edit_active').checked = data.is_active == 1;
            
            // Set faculty type
            editFacultyTypeSelect.setValue(data.faculty_type);
            
            // Handle current photo
            const currentPhotoContainer = document.getElementById('currentPhotoContainer');
            const currentPhotoImg = document.getElementById('currentPhotoImg');
            
            if (data.photo) {
                currentPhotoImg.src = `/mit-college/upload/faculty/${data.photo}`;
                currentPhotoContainer.classList.remove('hidden');
            } else {
                currentPhotoContainer.classList.add('hidden');
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
                    if (file && file.type.startsWith('image/')) {
                        const input = area.querySelector('input[type="file"]');
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        input.files = dataTransfer.files;
                        
                        const event = new Event('change', { bubbles: true });
                        input.dispatchEvent(event);
                    } else {
                        alert('Please drop an image file (JPG, PNG).');
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