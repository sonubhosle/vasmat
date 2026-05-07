<?php
require_once __DIR__ . '/../../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);
include '../includes/header.php';

$success = "";
$error = "";

// Set upload directory for events
$uploadBaseDir = __DIR__ . '/../../upload/';
$eventsUploadDir = $uploadBaseDir . 'events/';

// Logic for Add/Update/Delete
if (isset($_POST['add_event'])) {
    $name = $_POST['event_name'];
    $date = $_POST['event_date'];
    $imagesArr = [];
    if (!empty($_FILES['event_images']['name'][0])) {
        // Limit to 5 images
        $fileCount = min(count($_FILES['event_images']['name']), 5);
        for ($i = 0; $i < $fileCount; $i++) {
            $file = [
                'name' => $_FILES['event_images']['name'][$i],
                'type' => $_FILES['event_images']['type'][$i],
                'tmp_name' => $_FILES['event_images']['tmp_name'][$i],
                'error' => $_FILES['event_images']['error'][$i],
                'size' => $_FILES['event_images']['size'][$i]
            ];
            $upload = secure_upload($file, ['jpg', 'jpeg', 'png', 'gif'], $eventsUploadDir);
            if ($upload['success']) $imagesArr[] = 'events/' . $upload['filename'];
        }
    }
    $imagesJson = json_encode($imagesArr);
    $stmt = $conn->prepare("INSERT INTO events (event_name, event_date, event_images) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $date, $imagesJson);
    if ($stmt->execute()) $success = "Event added successfully!"; else $error = "DB Error";
}

if (isset($_POST['edit_event'])) {
    $id = intval($_POST['event_id']);
    $name = $_POST['event_name'];
    $date = $_POST['event_date'];
    
    // Check if new images are uploaded
    if (!empty($_FILES['event_images']['name'][0])) {
        $imagesArr = [];
        $fileCount = min(count($_FILES['event_images']['name']), 5);
        for ($i = 0; $i < $fileCount; $i++) {
            $file = [
                'name' => $_FILES['event_images']['name'][$i],
                'type' => $_FILES['event_images']['type'][$i],
                'tmp_name' => $_FILES['event_images']['tmp_name'][$i],
                'error' => $_FILES['event_images']['error'][$i],
                'size' => $_FILES['event_images']['size'][$i]
            ];
            $upload = secure_upload($file, ['jpg', 'jpeg', 'png', 'gif'], $eventsUploadDir);
            if ($upload['success']) $imagesArr[] = 'events/' . $upload['filename'];
        }
        $imagesJson = json_encode($imagesArr);
        $stmt = $conn->prepare("UPDATE events SET event_name=?, event_date=?, event_images=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $date, $imagesJson, $id);
    } else {
        $stmt = $conn->prepare("UPDATE events SET event_name=?, event_date=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $date, $id);
    }
    
    if ($stmt->execute()) $success = "Event updated successfully!"; else $error = "Update failed";
}

if (isset($_GET['delete_event'])) {
    $id = intval($_GET['delete_event']);
    $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) $success = "Event deleted!"; else $error = "Delete failed";
}

$events = $conn->query("SELECT * FROM events ORDER BY event_date DESC, id DESC");
$totalEvents = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
?>

<div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-primary-600 mb-2 block animate-in fade-in slide-in-from-left-4 duration-500">Event Management</span>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight animate-in fade-in slide-in-from-left-4 duration-700 delay-100">College <span class="text-primary-500">Events</span></h2>
    </div>
    <button onclick="document.getElementById('add_modal').classList.remove('hidden')" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/10 flex items-center gap-3 active:scale-95">
        <i class="fas fa-plus"></i> Add New Event
    </button>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="stat-card">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-xl border border-amber-100 ">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Events</p>
                <h3 class="text-3xl font-black text-slate-900"><?= $totalEvents ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Events Table -->
<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Event Details</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Date</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Status</th>
                    <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if($events->num_rows > 0): ?>
                    <?php while($row = $events->fetch_assoc()): 
                        $images = json_decode($row['event_images'] ?? '[]', true) ?: [];
                        $isUpcoming = strtotime($row['event_date']) > time();
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <?php if(!empty($images)): ?>
                                    <div class="w-14 h-14 rounded-2xl overflow-hidden border-2 border-primary-100 shadow-sm">
                                        <img src="../../upload/<?= htmlspecialchars($images[0]) ?>" class="w-full h-full object-cover">
                                    </div>
                                <?php else: ?>
                                    <div class="w-14 h-14 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center text-lg font-bold border-2 border-primary-100">
                                        <?= substr(e($row['event_name']), 0, 1) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h4 class="text-sm font-black text-slate-900 mb-0.5"><?= e($row['event_name']) ?></h4>
                                    <p class="text-[10px] text-slate-400 font-medium">ID: #<?= $row['id'] ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-700"><?= date('M d, Y', strtotime($row['event_date'])) ?></span>
                                <span class="text-[10px] text-slate-400 font-medium"><?= date('l', strtotime($row['event_date'])) ?></span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl <?= $isUpcoming ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' ?> text-[9px] font-black uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 rounded-full <?= $isUpcoming ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' ?>"></span>
                                <?= $isUpcoming ? 'Upcoming' : 'Past Event' ?>
                            </span>
                        </td>

                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2 transition-opacity">
                                <button onclick='openEditModal(<?= $row['id'] ?>, "<?= addslashes($row['event_name']) ?>", "<?= $row['event_date'] ?>")' class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-primary-500 hover:text-white transition-all">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <a href="?delete_event=<?= $row['id'] ?>" onclick="return confirm('Delete?')" class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center text-slate-200 text-3xl mb-4">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                                <h3 class="text-lg font-black text-slate-900">No Events Found</h3>
                                <p class="text-xs text-slate-400">Click the "Add New Event" button to get started.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="add_modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
    <div class="bg-white rounded-[3rem] w-full max-w-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-300">
        <form method="POST" enctype="multipart/form-data" class="p-10">
            <h3 class="text-2xl font-black text-slate-900 mb-8 tracking-tight">Add New Event</h3>
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Event Name</label>
                    <input type="text" name="event_name" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-all" placeholder="Enter name...">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Event Date</label>
                    <input type="date" name="event_date" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-all">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Event Images (Max 5)</label>
                    <input type="file" name="event_images[]" multiple accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>
            </div>
            <div class="mt-10 flex gap-4">
                <button type="submit" name="add_event" class="flex-1 bg-slate-900 text-white rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">Create Event</button>
                <button type="button" onclick="document.getElementById('add_modal').classList.add('hidden')" class="px-8 bg-slate-100 text-slate-600 rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit_modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
    <div class="bg-white rounded-[3rem] w-full max-w-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-300">
        <form method="POST" enctype="multipart/form-data" class="p-10">
            <input type="hidden" name="event_id" id="edit_event_id">
            <h3 class="text-2xl font-black text-slate-900 mb-8 tracking-tight">Edit Event</h3>
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Event Name</label>
                    <input type="text" name="event_name" id="edit_event_name" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-all" placeholder="Enter name...">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Event Date</label>
                    <input type="date" name="event_date" id="edit_event_date" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-all">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Event Images (Max 5, replaces existing)</label>
                    <input type="file" name="event_images[]" multiple accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>
            </div>
            <div class="mt-10 flex gap-4">
                <button type="submit" name="edit_event" class="flex-1 bg-slate-900 text-white rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">Update Event</button>
                <button type="button" onclick="document.getElementById('edit_modal').classList.add('hidden')" class="px-8 bg-slate-100 text-slate-600 rounded-2xl py-4 font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name, date) {
    document.getElementById('edit_event_id').value = id;
    document.getElementById('edit_event_name').value = name;
    document.getElementById('edit_event_date').value = date;
    document.getElementById('edit_modal').classList.remove('hidden');
}
</script>

<?php include '../includes/footer.php'; ?>
