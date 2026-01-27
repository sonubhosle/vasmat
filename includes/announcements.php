<?php
include __DIR__ . '/../admin/includes/db.php';

// Fetch Announcements from database
$announcements_query = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
$announcements_items = [];

if ($announcements_query && $announcements_query->num_rows > 0) {
    while($row = $announcements_query->fetch_assoc()) {
        $badge = trim($row['badge'] ?? 'update');
        $isNew = (strtotime($row['created_at']) > strtotime('-7 days'));
        
        // FIXED: Get only filename, remove any folder prefix
        $pdfFile = basename($row['pdf']);
        
        // CORRECT PATH: upload/announcements/filename.pdf
        $fileUrl = "upload/announcements/" . $pdfFile;
        
        $announcements_items[] = [
            "text" => htmlspecialchars($row['title']),
            "isNew" => $isNew,
            "type" => "announcement",
            "badge" => $badge,
            "date" => date("M d", strtotime($row['created_at'])),
            "link" => !empty($pdfFile) ? $fileUrl : "#"
        ];
    }
}

// Fetch Events from database
$events_query = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
$events_items = [];

if ($events_query && $events_query->num_rows > 0) {
    while($row = $events_query->fetch_assoc()) {
        $date = new DateTime($row['event_date']);
        $isNew = (strtotime($row['event_date']) > strtotime('now')); // Mark as new if future event
        
        $events_items[] = [
            "text" => htmlspecialchars($row['event_name']),
            "isNew" => $isNew,
            "type" => "event",
            "date" => $date->format("M d"),
            "link" => "get_events.php?event=" . $row['id']
        ];
    }
}

// Combine both announcements and events
$UPDATES = array_merge($announcements_items, $events_items);

// Sort by date (most recent first)
usort($UPDATES, function($a, $b) {
    return strtotime($b['date'] ?? 'now') - strtotime($a['date'] ?? 'now');
});

// Repeat to make the marquee loop smoothly (only if we have items)
if (!empty($UPDATES)) {
    $loopedUpdates = array_merge($UPDATES, $UPDATES, $UPDATES);
} else {
    // Fallback if no data
    $UPDATES = [
        ["text" => "No announcements or events at the moment", "isNew" => false, "type" => "info", "date" => date("M d")]
    ];
    $loopedUpdates = array_merge($UPDATES, $UPDATES, $UPDATES);
}
?>

<div class="bg-white border-b border-slate-100 shadow-sm">
    <div class="flex items-stretch overflow-hidden">
        
        <!-- Icon Section -->
        <div class="relative z-10 shrink-0 w-12  flex items-center justify-center -skew-x-12 shadow-lg">
            <span class="text-amber-400 transform skew-x-12">
                <i class='fas fa-bullhorn text-lg sm:text-xl'></i>
            </span>
        </div>

        <!-- Marquee Container -->
        <div class="flex-1 overflow-hidden py-3 sm:py-4 flex items-center relative bg-gradient-to-r from-white via-amber-50/10 to-white">
            <!-- Gradient overlays for fade effect -->
            <div class="absolute left-0 top-0 bottom-0 w-12 bg-gradient-to-r from-white to-transparent z-10"></div>
            <div class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-white to-transparent z-10"></div>

            <div class="flex animate-marquee">
                <?php foreach($loopedUpdates as $item): ?>
                    <div class="flex items-center whitespace-nowrap px-6  group cursor-pointer"
                         onclick="<?= !empty($item['link']) && $item['link'] != '#' ? "window.open('{$item['link']}', '_blank')" : "void(0)" ?>">
                        
                        <!-- Indicator dot with color based on type -->
                        <span class="w-2 h-2 rounded-full 
                            <?= $item['type'] == 'announcement' ? 'bg-blue-500' : 'bg-green-500' ?> 
                            mr-3 sm:mr-4 group-hover:scale-125 transition-all duration-300"></span>

                        <!-- Date -->
                        <span class="text-xs font-bold text-amber-600 mr-3 sm:mr-4 tracking-wider">
                            <?= $item['date'] ?? date("M d") ?>
                        </span>


                        <!-- New Badge -->
                        <?php if($item["isNew"]): ?>
                            <span class="mr-3 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500 text-white animate-pulse shadow-[0_0_8px_rgba(245,158,11,0.5)]">
                                NEW
                            </span>
                        <?php endif; ?>

                        <!-- Custom Badge from database -->
                        <?php if(!empty($item['badge'])): ?>
                            <span class="mr-3 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-400 text-white border border-slate-200 uppercase">
                                <?= $item['badge'] ?>
                            </span>
                        <?php endif; ?>

                        <!-- Text -->
                        <span class="text-sm font-medium text-slate-700 group-hover:text-amber-600 transition-colors duration-300 tracking-wide">
                            <?= htmlspecialchars($item["text"]) ?>
                        </span>

                        <!-- Arrow icon for clickable items -->
                        <?php if(!empty($item['link']) && $item['link'] != '#'): ?>
                            <span class="ml-3 text-slate-400 group-hover:text-amber-500 transition-colors duration-300">
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </span>
                        <?php endif; ?>

                        <!-- Separator -->
                        <span class="mx-6 sm:mx-8 text-slate-200">|</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
    </div>
</div>

<style>
@keyframes marquee {
    0% { transform: translateX(0%); }
    100% { transform: translateX(-33.333%); }
}

.animate-marquee {
    display: flex;
    animation: marquee <?= count($UPDATES) * 2 ?>s linear infinite;
    animation-play-state: running;
}

.animate-marquee:hover {
    animation-play-state: paused;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    @keyframes marquee {
        0% { transform: translateX(0%); }
        100% { transform: translateX(-50%); }
    }
    
    .animate-marquee {
        animation: marquee <?= count($UPDATES) * 1.5 ?>s linear infinite;
    }
}
</style>

<script>
// Pause on hover for better readability
document.querySelectorAll('.animate-marquee').forEach(marquee => {
    marquee.addEventListener('mouseenter', () => {
        marquee.style.animationPlayState = 'paused';
    });
    
    marquee.addEventListener('mouseleave', () => {
        marquee.style.animationPlayState = 'running';
    });
});

// Click handling for items
document.querySelectorAll('[onclick]').forEach(item => {
    item.addEventListener('click', function(e) {
        const onclickAttr = this.getAttribute('onclick');
        if (onclickAttr && onclickAttr !== "void(0)") {
            eval(onclickAttr);
        }
    });
});

// Touch support for mobile
let touchStartX = 0;
let touchEndX = 0;

document.querySelector('.animate-marquee').addEventListener('touchstart', e => {
    touchStartX = e.changedTouches[0].screenX;
});

document.querySelector('.animate-marquee').addEventListener('touchend', e => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
});

function handleSwipe() {
    const swipeThreshold = 50;
    
    if (touchEndX < touchStartX - swipeThreshold) {
        // Swipe left - pause
        document.querySelector('.animate-marquee').style.animationPlayState = 'paused';
        setTimeout(() => {
            document.querySelector('.animate-marquee').style.animationPlayState = 'running';
        }, 2000);
    }
    
    if (touchEndX > touchStartX + swipeThreshold) {
        // Swipe right - pause
        document.querySelector('.animate-marquee').style.animationPlayState = 'paused';
        setTimeout(() => {
            document.querySelector('.animate-marquee').style.animationPlayState = 'running';
        }, 2000);
    }
}
</script>