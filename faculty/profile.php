<?php
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch full faculty data (including extended fields)
$stmt = $conn->prepare("SELECT * FROM faculty WHERE id = ?");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$faculty = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $designation = trim($_POST['designation'] ?? '');
        $education = trim($_POST['education'] ?? '');
        $experience = trim($_POST['experience'] ?? '');
        $achievements = trim($_POST['achievements'] ?? '');
        $about = trim($_POST['about'] ?? '');
        $dob = $_POST['dob'] ?: null;
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        // Handle Photo Upload
        $photo_path = $faculty['photo'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['photo'];
            $filename = time() . '_fphoto_' . basename($file['name']);
            $target_dir = "../upload/photos/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            if (move_uploaded_file($file['tmp_name'], $target_dir . $filename)) {
                $photo_path = $filename;
            }
        }

        // Handle Resume Upload
        $resume_path = $faculty['resume'];
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['resume'];
            $filename = time() . '_resume_' . basename($file['name']);
            $target_dir = "../upload/resumes/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            if (move_uploaded_file($file['tmp_name'], $target_dir . $filename)) {
                $resume_path = $filename;
            }
        }

        $stmt = $conn->prepare("UPDATE faculty SET designation = ?, education = ?, experience = ?, achievements = ?, about = ?, dob = ?, phone = ?, email = ?, photo = ?, resume = ? WHERE id = ?");
        $stmt->bind_param("ssssssssssi", $designation, $education, $experience, $achievements, $about, $dob, $phone, $email, $photo_path, $resume_path, $faculty_id);
        
        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
            // Refresh data
            $stmt_refresh = $conn->prepare("SELECT * FROM faculty WHERE id = ?");
            $stmt_refresh->bind_param("i", $faculty_id);
            $stmt_refresh->execute();
            $faculty = $stmt_refresh->get_result()->fetch_assoc();
            $stmt_refresh->close();
        } else {
            $error = "Update failed: " . $conn->error;
        }
        $stmt->close();
    } elseif (isset($_POST['change_password'])) {
        $current_pass = $_POST['current_password'] ?? '';
        $new_pass = $_POST['new_password'] ?? '';
        $confirm_pass = $_POST['confirm_password'] ?? '';

        if ($new_pass !== $confirm_pass) {
            $error = "New passwords do not match.";
        } else {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            
            if (password_verify($current_pass, $user['password'])) {
                $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update->bind_param("si", $hashed, $user_id);
                if ($update->execute()) {
                    $success = "Password changed successfully.";
                } else {
                    $error = "Failed to update password.";
                }
            } else {
                $error = "Current password is incorrect.";
            }
        }
    }
}
?>

<div class="mb-10">
    <h2 class="text-4xl font-black text-slate-900 tracking-tight">Account <span class="text-amber-500">Settings</span></h2>
    <p class="text-slate-500 font-medium mt-2 text-sm">Update your professional profile and security credentials.</p>
</div>

<?php if ($success): ?>
    <div class="bg-emerald-50 text-emerald-600 p-5 rounded-2xl text-sm font-bold mb-8 border border-emerald-100 flex items-center gap-4">
        <i class="fas fa-check-circle text-lg"></i>
        <?= $success ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="bg-rose-50 text-rose-600 p-5 rounded-2xl text-sm font-bold mb-8 border border-rose-100 flex items-center gap-4">
        <i class="fas fa-exclamation-circle text-lg"></i>
        <?= $error ?>
    </div>
<?php endif; ?>

<div class="max-w-5xl space-y-12">
    <!-- Profile Edit Form -->
    <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/50">
        <div class="flex items-center gap-4 mb-10 border-b border-slate-50 pb-8">
            <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center shadow-inner">
                <i class="fas fa-user-edit"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900">Profile Details</h3>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Public Information</p>
            </div>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="update_profile" value="1">
            
            <div class="flex flex-col md:flex-row items-center gap-10 mb-12 pb-12 border-b border-slate-50">
                <div class="relative group">
                    <div class="w-40 h-40 bg-slate-100 rounded-[2.5rem] overflow-hidden border-8 border-white shadow-2xl relative">
                        <img id="profile-preview" src="<?= $faculty['photo'] ? '../upload/photos/'.$faculty['photo'] : 'https://ui-avatars.com/api/?name='.urlencode($faculty['name']).'&background=f59e0b&color=fff&size=200' ?>" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                            <i class="fas fa-camera text-white text-3xl"></i>
                        </div>
                    </div>
                    <label class="absolute -bottom-2 -right-2 w-12 h-12 bg-amber-500 text-white rounded-2xl flex items-center justify-center cursor-pointer shadow-xl hover:scale-110 transition-all border-4 border-white active:scale-95">
                        <i class="fas fa-plus text-sm"></i>
                        <input type="file" name="photo" class="hidden" onchange="previewImage(this, 'profile-preview')">
                    </label>
                </div>
                <div class="text-center md:text-left flex-1">
                    <h3 class="text-3xl font-black text-slate-900 leading-tight"><?= e($faculty['name']) ?></h3>
                    <p class="text-slate-500 font-medium text-lg"><?= e($faculty['designation'] ?: 'Faculty Member') ?></p>
                    <div class="flex flex-wrap gap-3 mt-6 justify-center md:justify-start">
                        <span class="px-4 py-2 bg-slate-100 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] rounded-xl border border-slate-200">ID: #<?= str_pad($faculty_id, 4, '0', STR_PAD_LEFT) ?></span>
                        <span class="px-4 py-2 bg-amber-50 text-[10px] font-black text-amber-600 uppercase tracking-[0.2em] rounded-xl border border-amber-100">Verified Professional</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Professional Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="email" name="email" value="<?= e($faculty['email']) ?>" required 
                               class="w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-semibold outline-none">
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Contact Number</label>
                    <div class="relative">
                        <i class="fas fa-phone-alt absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="phone" value="<?= e($faculty['phone']) ?>" 
                               class="w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-semibold outline-none">
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Current Designation</label>
                    <div class="relative">
                        <i class="fas fa-briefcase absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="designation" value="<?= e($faculty['designation']) ?>" required 
                               class="w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-semibold outline-none">
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Date of Birth <span class="text-slate-300">(Optional)</span></label>
                    <div class="relative">
                        <i class="fas fa-calendar absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="date" name="dob" value="<?= e($faculty['dob']) ?>" 
                               class="w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-semibold outline-none">
                    </div>
                </div>

                <div class="col-span-2 space-y-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">About Me / Professional Bio</label>
                    <textarea name="about" rows="3" placeholder="Briefly describe your academic background and interests..." 
                              class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-medium outline-none resize-none"><?= e($faculty['about']) ?></textarea>
                </div>

                <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Academic Education</label>
                        <textarea name="education" rows="3" placeholder="e.g. Ph.D. in Computer Science" 
                                  class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-medium outline-none resize-none"><?= e($faculty['education']) ?></textarea>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Teaching Experience</label>
                        <textarea name="experience" rows="3" placeholder="e.g. 10 Years at MIT College" 
                                  class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-medium outline-none resize-none"><?= e($faculty['experience']) ?></textarea>
                    </div>
                </div>

                <div class="col-span-2 space-y-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Notable Achievements</label>
                    <textarea name="achievements" rows="3" placeholder="Awards, Publications, or Projects..." 
                              class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-medium outline-none resize-none"><?= e($faculty['achievements']) ?></textarea>
                </div>

                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1 mb-3">Professional Resume <span class="text-slate-300">(PDF only)</span></label>
                    <div class="relative group">
                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-slate-200 rounded-[2rem] bg-slate-50 hover:bg-amber-50 hover:border-amber-200 transition-all cursor-pointer group">
                            <div class="flex flex-col items-center justify-center pt-2 pb-2">
                                <i class="fas fa-file-pdf text-2xl text-slate-300 group-hover:text-amber-500 mb-2 transition-colors"></i>
                                <p class="text-[9px] font-black text-slate-500 group-hover:text-amber-600 transition-colors uppercase tracking-widest">
                                    <?= $faculty['resume'] ? 'Change Resume' : 'Upload Professional Resume' ?>
                                </p>
                            </div>
                            <input type="file" name="resume" class="hidden" accept=".pdf">
                        </label>
                    </div>
                </div>
            </div>

            <div class="pt-10 border-t border-slate-50 mt-10">
                <button type="submit" 
                        class="w-full py-6 bg-slate-900 text-white font-black rounded-3xl shadow-2xl hover:bg-slate-800 hover:scale-[1.01] active:scale-95 transition-all uppercase tracking-[0.2em] text-sm flex items-center justify-center gap-3">
                    <i class="fas fa-save"></i> Save Profile Data
                </button>
            </div>
        </form>
    </div>

    <!-- Password Change Section (Now Below Profile) -->
    <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/50">
        <div class="flex items-center gap-4 mb-10 border-b border-slate-50 pb-8">
            <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center shadow-inner">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900">Security Credentials</h3>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Update Login Password</p>
            </div>
        </div>

        <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <input type="hidden" name="change_password" value="1">
            
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Current Password</label>
                <input type="password" name="current_password" required 
                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-sm font-semibold outline-none">
            </div>

            <div class="space-y-3">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">New Password</label>
                <input type="password" name="new_password" required 
                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-sm font-semibold outline-none">
            </div>

            <div class="space-y-3">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Confirm Password</label>
                <input type="password" name="confirm_password" required 
                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-sm font-semibold outline-none">
            </div>

            <div class="md:col-span-3 pt-4">
                <button type="submit" 
                        class="w-full py-5 bg-rose-500 text-white font-black rounded-2xl shadow-xl shadow-rose-500/20 hover:bg-rose-600 hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest text-xs">
                    Update Security Credentials
                </button>
            </div>
        </form>
    </div>

    <!-- Help Banner -->
    <div class="bg-slate-900 p-10 rounded-[3rem] text-white shadow-2xl relative overflow-hidden group">
        <div class="absolute right-0 bottom-0 opacity-10 group-hover:scale-110 transition-transform -mb-10 -mr-10">
            <i class="fas fa-user-shield text-[12rem]"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="max-w-xl">
                <h4 class="text-xl font-black mb-3">Public Verification Notice</h4>
                <p class="text-slate-400 text-sm font-medium leading-relaxed">Your professional profile data is synchronized with the public institutional website. All changes are visible once your account is verified by the college administration.</p>
            </div>
            <div class="flex items-center gap-4 bg-white/5 p-6 rounded-3xl border border-white/10">
                <div class="w-12 h-12 bg-primary-500/20 text-primary-400 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-info-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Profile Status</p>
                    <p class="text-sm font-black text-white">Active & Public</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
