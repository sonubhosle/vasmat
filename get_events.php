<?php include './includes/header.php'; ?>
<?php
require __DIR__ . '/admin/includes/db.php';

// Check if a specific event is requested
$selectedEventId = isset($_GET['event']) ? intval($_GET['event']) : null;

if ($selectedEventId) {
    // Fetch only the specific event
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $selectedEventId);
    $stmt->execute();
    $events_result = $stmt->get_result();
} else {
    // Fetch all events
    $events_result = $conn->query("SELECT * FROM events ORDER BY event_date DESC, id DESC");
}

$events = [];
if($events_result && $events_result->num_rows > 0) {
    while($row = $events_result->fetch_assoc()) {
        $date = new DateTime($row['event_date']);
        
        // Decode images safely
        $images = json_decode($row['event_images'] ?? '[]', true) ?: [];
        
        // Prepare event data
        $event_data = [
            'id' => $row['id'],
            'code' => 'EV-' . str_pad($row['id'], 3, '0', STR_PAD_LEFT),
            'title' => htmlspecialchars($row['event_name']),
            'desc' => htmlspecialchars($row['event_name'] . " held on " . $date->format('F j, Y')),
            'date' => $date->format('Y-m-d'),
            'date_display' => $date->format('F j, Y'),
            'all_images' => $images,
            'month_year' => $date->format('F Y')
        ];
        
        $events[] = $event_data;
    }
}
?>


    <div class="px-6 py-4 ">
        <!-- Header -->
        <div class="mb-12 animate-fade-in-up flex items-center justify-between">
            <div>
                <div class="flex items-center gap-4 mb-4">
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-500">Activity</span>
                    <?php if($selectedEventId): ?>
                    <a href="get_events.php" class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 hover:text-amber-500 transition-colors">
                        ← Back to All Events
                    </a>
                    <?php endif; ?>
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-slate-900 mb-6">
                    <?php if($selectedEventId): ?>
                        Event Details
                    <?php else: ?>
                        College <span class="italic font-serif underline decoration-amber-400/30">Events</span>
                    <?php endif; ?>
                </h2>
                <p class="text-slate-500 text-lg max-w-2xl">Discover engaging activities, academic highlights, and memorable moments on campus.</p>
            </div>
            
            <div class="hidden sm:block">
                <div class="px-6 py-3 bg-slate-900 text-white font-bold rounded-2xl uppercase tracking-widest text-xs shadow-lg shadow-slate-900/20">
                    <?php echo count($events) ?> <?= $selectedEventId ? 'Event' : 'Events' ?>
                </div>
            </div>
        </div>

    <!-- Events Display -->
    <?php if(empty($events)): ?>
        <!-- No Events State -->
        <section class="py-20 px-6 text-center ">
            <div class="glass-card rounded-[3rem] p-16 border-2 border-dashed border-slate-200">
                <div class="w-24 h-24 mx-auto mb-8 rounded-full bg-slate-50 flex items-center justify-center shadow-inner">
                    <i class="fas fa-calendar-alt text-slate-300 text-4xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-4">
                    <?= $selectedEventId ? 'Event Not Found' : 'No Events Yet' ?>
                </h3>
                <p class="text-slate-500 mb-8 max-w-md mx-auto">
                    <?= $selectedEventId ? 'The requested event could not be found.' : 'Check back later for upcoming events and activities at MIT College.' ?>
                </p>
                <?php if($selectedEventId): ?>
                    <a href="get_events.php" 
                       class="px-8 py-3.5 bg-slate-900 hover:bg-amber-500 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-lg transition-all inline-flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        View All Events
                    </a>
                <?php endif; ?>
            </div>
        </section>
    <?php else: ?>
        <!-- All Events Sections - Your Original Layout -->
        <div class="w-full pb-20">
            <?php 
            // If showing single event, only show that one
            $eventsToShow = $selectedEventId ? [$events[0]] : $events;
            
            foreach ($eventsToShow as $index => $event): 
                $event_number = $index + 1;
            ?>
            <section class="mb-16 glass-card rounded-[3rem] p-8 md:p-12 shadow-xl shadow-slate-200/50 border-0">
                <!-- Event Header -->
                <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-slate-100 pb-8">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-black uppercase tracking-widest mb-4">
                            <?php if($selectedEventId): ?>
                                <i class="fas fa-calendar-alt"></i> Event <?= $event['code'] ?>
                            <?php else: ?>
                                <i class="fas fa-calendar-alt"></i> Event <?= $event_number ?>
                            <?php endif; ?>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-black text-slate-800 uppercase tracking-tight leading-tight mb-2">
                            <?php echo $event['title']; ?>
                        </h2>
                        <div class="flex items-center gap-3 text-slate-500 font-medium text-sm">
                            <i class="fas fa-calendar-day"></i> <?php echo $event['date_display']; ?>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right flex items-center gap-4">
                            <div class="px-4 py-2 bg-slate-50 rounded-xl text-slate-600 text-[10px] font-black tracking-widest uppercase border border-slate-100">
                                <i class="fas fa-images mr-2 text-slate-400"></i>
                                <?php echo count($event['all_images']); ?> images
                            </div>
                            <?php if($selectedEventId): ?>
                            <a href="get_events.php" 
                               class="w-10 h-10 bg-slate-900 text-white rounded-full flex items-center justify-center hover:bg-amber-500 transition-colors shadow-lg shadow-slate-900/20 active:scale-95">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Event Images Masonry Grid -->
                <?php if(!empty($event['all_images'])): ?>
                <div class="mb-8">
                    <div class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6">
                        <?php foreach ($event['all_images'] as $img_index => $image): 
                            $image_url = 'upload/' . $image;
                            $caption = htmlspecialchars($event['title']) . ' - Image ' . ($img_index + 1);
                        ?>
                        <div class="break-inside-avoid rounded-3xl overflow-hidden group relative cursor-pointer shadow-md shadow-slate-200/50 hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500 hover:-translate-y-2 border-0"
                             onclick="openLightbox('<?php echo $image_url; ?>', '<?php echo addslashes($caption); ?>')">
                            <img src="<?php echo $image_url; ?>" 
                                 alt="<?php echo $caption; ?>"
                                 class="w-full h-auto object-cover transition-transform duration-700 group-hover:scale-110">
                            
                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6 pointer-events-none">
                                <div class="translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                    <h4 class="text-white font-black text-sm mb-1"><?= $caption ?></h4>
                                    <div class="flex items-center gap-2 text-amber-400 text-[10px] font-black uppercase tracking-widest">
                                        <i class="fas fa-camera"></i> View
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php else: ?>
                <div class="text-center py-16 border-2 border-dashed border-slate-200 rounded-3xl mb-8 bg-slate-50/50">
                    <i class="fas fa-image text-slate-300 text-5xl mb-4"></i>
                    <p class="text-slate-800 font-bold text-lg">No Visuals Available</p>
                    <p class="text-slate-500 text-sm mt-1">This event doesn't have any photos yet.</p>
                </div>
                <?php endif; ?>

                <?php if(!$selectedEventId): ?>
                <!-- View Event Button -->
                <div class="text-center mt-6">
                    <a href="?event=<?= $event['id'] ?>" 
                       class="inline-flex items-center gap-3 px-8 py-3.5 bg-slate-900 text-white font-black text-[11px] uppercase tracking-widest rounded-2xl shadow-lg shadow-slate-900/20 hover:bg-amber-500 transition-all duration-300 hover:-translate-y-1">
                        View Full Gallery
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <?php endif; ?>
                
            </section>
            
            <?php if(!$selectedEventId && $index < count($eventsToShow) - 1): ?>
            <!-- Separator between events in all events view -->
            <div class="my-16 border-t border-slate-200 relative">
                <div class="absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white px-6 py-2">
                    <span class="text-xs font-black uppercase tracking-widest text-slate-400">Next Event</span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.7s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<!-- Simple Lightbox -->
<div id="lightbox" class="fixed inset-0 z-[1000] p-6 flex flex-col items-center justify-center transition-all duration-500 opacity-0 bg-slate-950/95 backdrop-blur-xl">
    <button onclick="closeLightbox()" class="absolute top-8 right-8 w-14 h-14 bg-white/10 text-white rounded-2xl flex items-center justify-center hover:bg-white/20 transition-all active:scale-95 group">
        <i class="fas fa-times text-2xl group-hover:rotate-90 transition-transform"></i>
    </button>
    
    <div class="max-w-6xl w-full h-full flex flex-col items-center justify-center gap-8">
        <div class="relative w-full h-full flex items-center justify-center">
            <img id="lightboxImg" src="" class="max-w-full max-h-[80vh] rounded-[2rem] shadow-2xl object-contain border-4 border-white/5 bg-slate-900/50">
        </div>
        <div class="text-center">
            <h4 id="lightboxCaption" class="text-2xl font-black text-white mb-2 tracking-tight"></h4>
            <span class="px-4 py-1.5 bg-amber-500 text-slate-900 text-[11px] font-black uppercase tracking-widest rounded-full">MIT Events</span>
        </div>
    </div>
</div>

<script>
    function openLightbox(src, caption) {
        const lightbox = document.getElementById('lightbox');
        document.getElementById('lightboxImg').src = src;
        document.getElementById('lightboxCaption').textContent = caption;
        lightbox.classList.remove('hidden');
        setTimeout(() => lightbox.classList.add('opacity-100'), 10);
    }

    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        lightbox.classList.remove('opacity-100');
        setTimeout(() => lightbox.classList.add('hidden'), 500);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeLightbox();
    });
</script>

    <?php include './includes/footer.php'; ?>
