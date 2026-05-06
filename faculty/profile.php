<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole('faculty');

$faculty_id = $_SESSION['reference_id'];
$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch existing faculty data
$stmt = $conn->prepare("SELECT * FROM faculty WHERE id = ?");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$faculty = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $designation = trim($_POST['designation'] ?? '');
    $education = trim($_POST['education'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($designation) || empty($education)) {
        $error = "Designation and Education are required.";
    } else {
        // Handle Photo Upload
        $photo_path = $faculty['photo'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['photo'];
            $filename = time() . '_' . basename($file['name']);
            $target_dir = "../upload/photos/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            
            if (move_uploaded_file($file['tmp_name'], $target_dir . $filename)) {
                $photo_path = $filename;
            }
        }

        $stmt = $conn->prepare("UPDATE faculty SET designation = ?, education = ?, experience = ?, phone = ?, photo = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $designation, $education, $experience, $phone, $photo_path, $faculty_id);
        
        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
            // Refresh data
            $faculty['designation'] = $designation;
            $faculty['education'] = $education;
            $faculty['experience'] = $experience;
            $faculty['phone'] = $phone;
            $faculty['photo'] = $photo_path;
        } else {
            $error = "Update failed: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | <?= SITE_NAME ?></title>
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
                <h1 class="text-white font-black text-sm uppercase tracking-tight">MIT Faculty</h1>
            </div>
            <nav class="flex-1 space-y-2">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="upload.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-cloud-upload-alt"></i> Upload Content
                </a>
                <a href="profile.php" class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 text-amber-500 rounded-xl font-bold text-sm transition-all">
                    <i class="fas fa-user-circle"></i> Profile Settings
                </a>
            </nav>
        </aside>

        <main class="flex-1 ml-64 p-10">
            <header class="mb-10">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Profile Settings</h2>
                <p class="text-slate-500 font-medium mt-1">Keep your professional information up to date.</p>
            </header>

            <div class="max-w-4xl">
                <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <?php if ($error): ?>
                        <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-sm font-bold mb-6 border border-red-100"><?= $error ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="bg-emerald-50 text-emerald-600 p-4 rounded-2xl text-sm font-bold mb-6 border border-emerald-100"><?= $success ?></div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-10">
                        <div class="flex items-center gap-8 pb-10 border-b border-slate-50">
                            <div class="relative group">
                                <div class="w-32 h-32 bg-slate-100 rounded-[2rem] overflow-hidden border-4 border-white shadow-lg">
                                    <?php if ($faculty['photo']): ?>
                                        <img src="../upload/photos/<?= $faculty['photo'] ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-4xl">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <label class="absolute bottom-0 right-0 w-10 h-10 bg-amber-500 text-white rounded-xl flex items-center justify-center cursor-pointer shadow-lg hover:scale-110 transition-all border-4 border-white">
                                    <i class="fas fa-camera text-xs"></i>
                                    <input type="file" name="photo" class="hidden">
                                </label>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900"><?= e($faculty['name']) ?></h3>
                                <p class="text-slate-500 font-medium"><?= e($faculty['email']) ?></p>
                                <span class="inline-block mt-2 px-3 py-1 bg-slate-100 text-[10px] font-black text-slate-500 uppercase tracking-widest rounded-full">Faculty Member</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-3 px-1">Designation</label>
                                <input type="text" name="designation" value="<?= e($faculty['designation']) ?>" 
                                       class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-amber-500/20 text-sm font-semibold">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-3 px-1">Phone Number</label>
                                <input type="text" name="phone" value="<?= e($faculty['phone']) ?>" 
                                       class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-amber-500/20 text-sm font-semibold">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-3 px-1">Education Details</label>
                                <textarea name="education" rows="3" 
                                          class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-amber-500/20 text-sm font-semibold"><?= e($faculty['education']) ?></textarea>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-3 px-1">Experience (Years/Summary)</label>
                                <textarea name="experience" rows="3" 
                                          class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-amber-500/20 text-sm font-semibold"><?= e($faculty['experience']) ?></textarea>
                            </div>
                        </div>

                        <button type="submit" 
                                class="w-full py-5 bg-slate-900 text-white font-black rounded-2xl shadow-xl hover:bg-slate-800 transition-all uppercase tracking-widest text-sm">
                            Save Profile Changes
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
