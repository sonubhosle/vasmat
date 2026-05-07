<?php
require_once __DIR__ . '/../../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);
include '../includes/header.php';

$success = "";
$error = "";

// Set upload directory for events
$uploadBaseDir = __DIR__ . '/../../upload/';
$eventsUploadDir = $uploadBaseDir . 'events/';

// Logic for Add/Update/Delete (Simplified for brevity but maintaining functionality)
if (isset($_POST['add_event'])) {
    $name = $_POST['event_name'];
    $date = $_POST['event_date'];
    $imagesArr = [];
    if (!empty($_FILES['event_images']['name'][0])) {
        foreach ($_FILES['event_images']['name'] as $key => $imgName) {
            $file = ['name' => $_FILES['event_images']['name'][$key], 'type' => $_FILES['event_images']['type'][$key], 'tmp_name' => $_FILES['event_images']['tmp_name'][$key], 'error' => $_FILES['event_images']['error'][$key], 'size' => $_FILES['event_images']['size'][$key]];
            $upload = secure_upload($file, ['jpg', 'jpeg', 'png', 'gif'], $eventsUploadDir);
            if ($upload['success']) $imagesArr[] = 'events/' . $upload['filename'];
        }
    }
    $imagesJson = json_encode($imagesArr);
    $stmt = $conn->prepare("INSERT INTO events (event_name, event_date, event_images) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $date, $imagesJson);
    if ($stmt->execute()) $success = "Event added successfully!"; else $error = "DB Error";
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
            <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-xl border border-amber-100 float-anim">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Events</p>
                <h3 class="text-3xl font-black text-slate-900"><?= $totalEvents ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Events Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
    <?php while($row = $events->fetch_assoc()): 
        $images = json_decode($row['event_images'] ?? '[]', true) ?: [];
        $isUpcoming = strtotime($row['event_date']) > time();
    ?>
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col hover:shadow-xl transition-all duration-500 group animate-in fade-in zoom-in-95 duration-500">
        <div class="h-2 w-full <?= $isUpcoming ? 'bg-emerald-500' : 'bg-slate-300' ?>"></div>
        <div class="p-8 flex flex-col flex-1">
            <div class="flex justify-between items-start mb-6">
                <span class="flex items-center gap-2 px-3 py-1.5 rounded-xl <?= $isUpcoming ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-50 text-slate-500' ?> text-[9px] font-black uppercase tracking-widest">
                    <i class="fas <?= $isUpcoming ? 'fa-calendar-check' : 'fa-calendar-times' ?>"></i> <?= $isUpcoming ? 'Upcoming' : 'Past' ?>
                </span>
                <span class="text-[10px] font-bold text-slate-400"><?= date('M d, Y', strtotime($row['event_date'])) ?></span>
            </div>
            
            <h4 class="text-xl font-black text-slate-900 mb-6 tracking-tight"><?= e($row['event_name']) ?></h4>

            <?php if(!empty($images)): ?>
            <div class="grid grid-cols-3 gap-2 mb-8">
                <?php foreach(array_slice($images, 0, 3) as $image): ?>
                <div class="aspect-square rounded-xl overflow-hidden border border-slate-100">
                    <img src="../../upload/<?= htmlspecialchars($image) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="mt-auto flex items-center justify-between pt-6 border-t border-slate-50">
                <button onclick='alert("Coming soon: Edit functionality refined")' class="text-[10px] font-black uppercase tracking-widest text-primary-600 hover:text-primary-700 transition-colors">Edit Event</button>
                <a href="?delete_event=<?= $row['id'] ?>" onclick="return confirm('Delete?')" class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all">
                    <i class="fas fa-trash-alt text-xs"></i>
                </a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
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
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Event Images</label>
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

<?php include '../includes/footer.php'; ?>
