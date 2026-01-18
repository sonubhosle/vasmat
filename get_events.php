
  <?php include  ('./includes/header.php'); ?>
    <?php
    require __DIR__ . '/admin/includes/db.php';
    
    // Fetch all events from database
    $events_result = $conn->query("SELECT * FROM events ORDER BY event_date DESC, id DESC");
    
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
    
    // Check if lightbox should be shown
    $selectedEvent = null;
    $selectedImageIndex = 0;
    if (isset($_GET['event'])) {
        $selectedId = intval($_GET['event']);
        // Find the selected event
        foreach($events as $event) {
            if ($event['id'] === $selectedId) {
                $selectedEvent = $event;
                if (isset($_GET['image'])) {
                    $selectedImageIndex = intval($_GET['image']);
                }
                break;
            }
        }
    }
    ?>

    <!-- Hero Section -->
    <section class="pt-20 pb-12 px-6 bg-gradient-to-br  text-white">
        <div class="w-full flex justify-between">
                <div class="">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-400">Activity</span>
                        </div>
                        <h2 class="text-4xl  font-black text-slate-900 mb-6 ">
                           College <span class="italic font-serif">Events</span>
                        </h2>

                </div>
            <div class="flex flex-wrap gap-4 items-center">
                <div class="px-6 py-3 bg-amber-400 text-slate-900 font-bold rounded-2xl uppercase tracking-widest text-sm">
                    <?php echo count($events) ?> Events
                </div>
            </div>
        </div>
    </section>

    <!-- Events Display -->
    <?php if(empty($events)): ?>
        <!-- No Events State -->
        <section class="py-32 px-6 text-center">
            <div class="max-w-2xl mx-auto">
                <div class="w-32 h-32 mx-auto mb-8 rounded-full bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-slate-400 text-5xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-4">No Events Yet</h3>
                <p class="text-slate-600 mb-8">Check back later for upcoming events and activities at MIT College.</p>
            </div>
        </section>
    <?php else: ?>
        <!-- All Events Sections -->
        <div class="w-full px-6 py-12">
            <?php foreach ($events as $index => $event): 
                $event_number = $index + 1;
            ?>
            <section class="mb-20">
                <!-- Event Header -->
                <div class="mb-10">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="text-amber-400 font-black text-sm uppercase tracking-widest mb-2">
                                Event  • <?php echo $event['date_display']; ?>
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
                             
                            </div>
                        </div>
                    </div>
                    
                    <div class="h-1 w-32 bg-amber-400"></div>
                </div>

                <!-- Event Images Masonry Grid -->
                <?php if(!empty($event['all_images'])): ?>
               <div class="mb-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 ">
        <?php foreach ($event['all_images'] as $img_index => $image): 
            $image_url = 'upload/' . $image;
            $image_number = $img_index + 1;
        ?>
        <div class="masonry-item group">
            <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer">
                <div class="relative overflow-hidden">
                    <img src="<?php echo $image_url; ?>" 
                         alt="<?php echo $event['title']; ?> - Image <?php echo $image_number; ?>"
                         class="w-full h-auto"">
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

                
            </section>
            
           
            
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

 


    <script>
        <?php if ($selectedEvent): ?>
        document.body.style.overflow = 'hidden';
        <?php else: ?>
        document.body.style.overflow = 'auto';
        <?php endif; ?>

        // Close lightbox on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && window.location.search.includes('event')) {
                window.location.href = window.location.pathname;
            }
        });

        // Close lightbox when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.id === 'lightbox') {
                window.location.href = window.location.pathname;
            }
        });
    </script>

    <?php include ('./includes/footer.php'); ?>