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


    <!-- Hero Section - Your Original Style -->
    <section class="pt-20 pb-12 px-6">
        <div class="w-full flex justify-between">
            <div class="">
                <div class="flex items-center gap-4 mb-4">
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-400">Activity</span>
                    <?php if($selectedEventId): ?>
                    <a href="get_events.php" class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 hover:text-amber-400 transition-colors">
                        ← Back to All Events
                    </a>
                    <?php endif; ?>
                </div>
                <h2 class="text-4xl font-black text-slate-900 mb-6">
                    <?php if($selectedEventId): ?>
                        Event Details
                    <?php else: ?>
                        College <span class="italic font-serif">Events</span>
                    <?php endif; ?>
                </h2>
            </div>
            <div class="flex flex-wrap gap-4 items-center">
                <div class="px-6 py-3 bg-amber-400 text-slate-900 font-bold rounded-2xl uppercase tracking-widest text-sm">
                    <?php echo count($events) ?> <?= $selectedEventId ? 'Event' : 'Events' ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Events Display -->
    <?php if(empty($events)): ?>
        <!-- No Events State - Your Original Style -->
        <section class="py-32 px-6 text-center">
            <div class="max-w-2xl mx-auto">
                <div class="w-32 h-32 mx-auto mb-8 rounded-full bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-slate-400 text-5xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-4">
                    <?= $selectedEventId ? 'Event Not Found' : 'No Events Yet' ?>
                </h3>
                <p class="text-slate-600 mb-8">
                    <?= $selectedEventId ? 'The requested event could not be found.' : 'Check back later for upcoming events and activities at MIT College.' ?>
                </p>
                <?php if($selectedEventId): ?>
                    <a href="get_events.php" 
                       class="px-8 py-3.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 inline-flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        View All Events
                    </a>
                <?php endif; ?>
            </div>
        </section>
    <?php else: ?>
        <!-- All Events Sections - Your Original Layout -->
        <div class="w-full px-6 py-12">
            <?php 
            // If showing single event, only show that one
            $eventsToShow = $selectedEventId ? [$events[0]] : $events;
            
            foreach ($eventsToShow as $index => $event): 
                $event_number = $index + 1;
            ?>
            <section class="mb-20">
                <!-- Event Header - Your Original Style -->
                <div class="mb-10">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="text-amber-400 font-black text-sm uppercase tracking-widest mb-2">
                                <?php if($selectedEventId): ?>
                                    Event <?= $event['code'] ?> • <?php echo $event['date_display']; ?>
                                <?php else: ?>
                                    Event <?= $event_number ?> • <?php echo $event['date_display']; ?>
                                <?php endif; ?>
                            </div>
                            <h2 class="text-4xl font-black text-slate-800 uppercase tracking-tight leading-tight">
                                <?php echo $event['title']; ?>
                            </h2>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div class="text-slate-600 text-sm font-medium mb-1">
                                    <i class="fas fa-images mr-2 text-amber-500"></i>
                                    <?php echo count($event['all_images']); ?> images
                                </div>
                                <?php if($selectedEventId): ?>
                                <a href="get_events.php" 
                                   class="text-xs text-slate-400 hover:text-amber-500 transition-colors flex items-center gap-1">
                                    <i class="fas fa-arrow-left"></i>
                                    Back to All
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="h-1 w-32 bg-amber-400"></div>
                </div>

                <!-- Event Images Masonry Grid - Your Original Style -->
                <?php if(!empty($event['all_images'])): ?>
                <div class="mb-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        <?php foreach ($event['all_images'] as $img_index => $image): 
                            $image_url = 'upload/' . $image;
                            $image_number = $img_index + 1;
                        ?>
                        <div class="masonry-item group">
                            <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer hover:-translate-y-1">
                                <div class="relative overflow-hidden aspect-square">
                                    <img src="<?php echo $image_url; ?>" 
                                         alt="<?php echo $event['title']; ?> - Image <?php echo $image_number; ?>"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                                </div>
                                <div class="p-4">
                                    <p class="text-sm text-slate-600 text-center">Image <?= $image_number ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php else: ?>
                <div class="text-center py-16 border-2 border-dashed border-slate-200 rounded-3xl mb-8">
                    <i class="fas fa-image text-slate-300 text-6xl mb-6"></i>
                    <p class="text-slate-500 font-medium text-lg">No images uploaded for this event</p>
                    <p class="text-slate-400 text-sm mt-2">Check back later for event photos</p>
                </div>
                <?php endif; ?>

                <?php if(!$selectedEventId): ?>
                <!-- View Event Button - Only show in all events view -->
                <div class="text-center mt-10">
                    <a href="?event=<?= $event['id'] ?>" 
                       class="inline-flex items-center gap-3 px-8 py-3.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fas fa-expand-alt"></i>
                        View Full Event
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

    <?php include './includes/footer.php'; ?>