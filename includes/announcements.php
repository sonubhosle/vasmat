<?php
include __DIR__ . '/../admin/includes/db.php';

// ── Badge color mapping (used in both PHP logic and inline styles) ──
$badgeStyles = [
    'urgent'    => ['bg' => '#ef4444', 'glow' => 'rgba(239,68,68,0.35)',  'icon' => 'fa-triangle-exclamation'],
    'exam'      => ['bg' => '#8b5cf6', 'glow' => 'rgba(139,92,246,0.35)', 'icon' => 'fa-pen-ruler'],
    'holiday'   => ['bg' => '#10b981', 'glow' => 'rgba(16,185,129,0.35)', 'icon' => 'fa-umbrella-beach'],
    'result'    => ['bg' => '#06b6d4', 'glow' => 'rgba(6,182,212,0.35)',  'icon' => 'fa-chart-column'],
    'admission' => ['bg' => '#f43f5e', 'glow' => 'rgba(244,63,94,0.35)', 'icon' => 'fa-user-plus'],
    'event'     => ['bg' => '#7c3aed', 'glow' => 'rgba(124,58,237,0.35)', 'icon' => 'fa-calendar-star'],
    'update'    => ['bg' => '#f59e0b', 'glow' => 'rgba(245,158,11,0.35)', 'icon' => 'fa-circle-info'],
    'notice'    => ['bg' => '#3b82f6', 'glow' => 'rgba(59,130,246,0.35)', 'icon' => 'fa-clipboard-list'],
    'workshop'  => ['bg' => '#ec4899', 'glow' => 'rgba(236,72,153,0.35)', 'icon' => 'fa-screwdriver-wrench'],
    'placement' => ['bg' => '#14b8a6', 'glow' => 'rgba(20,184,166,0.35)', 'icon' => 'fa-briefcase'],
];

// Fetch Announcements from database
$announcements_query = $conn->query("SELECT * FROM announcements WHERE status = 'approved' AND is_active = 1 ORDER BY created_at DESC");
$announcements_items = [];

if ($announcements_query && $announcements_query->num_rows > 0) {
    while($row = $announcements_query->fetch_assoc()) {
        $badge = strtolower(trim($row['badge'] ?? 'update'));
        $isNew = (strtotime($row['created_at']) > strtotime('-7 days'));
        
        // Get only filename, remove any folder prefix
        $pdfFile = basename($row['pdf']);
        
        // Correct path: upload/announcements/filename.pdf
        $fileUrl = "upload/announcements/" . $pdfFile;
        
        $announcements_items[] = [
            "text"  => htmlspecialchars($row['title']),
            "isNew" => $isNew,
            "type"  => "announcement",
            "badge" => $badge,
            "date"  => date("M d", strtotime($row['created_at'])),
            "link"  => !empty($pdfFile) ? $fileUrl : "#"
        ];
    }
}

// Fetch Events from database
$events_query = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
$events_items = [];

if ($events_query && $events_query->num_rows > 0) {
    while($row = $events_query->fetch_assoc()) {
        $date = new DateTime($row['event_date']);
        $isNew = (strtotime($row['event_date']) > strtotime('now'));
        
        $events_items[] = [
            "text"  => htmlspecialchars($row['event_name']),
            "isNew" => $isNew,
            "type"  => "event",
            "badge" => "event",
            "date"  => $date->format("M d"),
            "link"  => "get_events.php?event=" . $row['id']
        ];
    }
}

// Combine both announcements and events
$UPDATES = array_merge($announcements_items, $events_items);

// Sort by date (most recent first)
usort($UPDATES, function($a, $b) {
    return strtotime($b['date'] ?? 'now') - strtotime($a['date'] ?? 'now');
});

// Repeat to make the marquee loop smoothly
if (!empty($UPDATES)) {
    $loopedUpdates = array_merge($UPDATES, $UPDATES, $UPDATES);
} else {
    $UPDATES = [
        ["text" => "No announcements or events at the moment", "isNew" => false, "type" => "info", "badge" => "update", "date" => date("M d")]
    ];
    $loopedUpdates = array_merge($UPDATES, $UPDATES, $UPDATES);
}

// Helper: get badge style
function getBadgeStyle($badge, $badgeStyles) {
    return $badgeStyles[$badge] ?? $badgeStyles['update'];
}
?>

<!-- ═══════════════════════════════════════════════════════════════
     ANNOUNCEMENTS MARQUEE — Premium Redesign
     ═══════════════════════════════════════════════════════════════ -->
<div id="announcementBar" class="relative overflow-hidden" style="background: linear-gradient(135deg, #1e1b2e 0%, #1a1433 40%, #0f172a 100%);">

    <!-- Animated background particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="ann-particle ann-p1"></div>
        <div class="ann-particle ann-p2"></div>
        <div class="ann-particle ann-p3"></div>
    </div>

    <!-- Subtle top highlight line -->
    <div class="absolute top-0 left-0 right-0 h-[1px]" style="background: linear-gradient(90deg, transparent, rgba(245,158,11,0.5), transparent);"></div>

    <div class="flex items-stretch">

        <!-- ─── Left Icon Badge ─── -->
        <div class="relative z-20 shrink-0 flex items-center justify-center px-4 sm:px-5" 
             style="background: linear-gradient(135deg, rgba(245,158,11,0.15) 0%, rgba(245,158,11,0.05) 100%); border-right: 1px solid rgba(245,158,11,0.15);">
            
            <!-- Pulsing ring behind icon -->
            <span class="absolute w-10 h-10 rounded-full ann-icon-pulse" style="background: rgba(245,158,11,0.08);"></span>
            
            <div class="relative flex items-center gap-2.5">
                <span class="relative flex items-center justify-center w-9 h-9 rounded-xl" 
                      style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 0 20px rgba(245,158,11,0.3);">
                    <i class="fas fa-bullhorn text-white text-sm"></i>
                </span>
                <span class="hidden sm:block text-[10px] font-black uppercase tracking-[0.2em] text-amber-400/80">
                    Live
                </span>
            </div>
        </div>

        <!-- ─── Marquee Container ─── -->
        <div class="flex-1 overflow-hidden py-3 sm:py-3.5 flex items-center relative">
            
            <!-- Fade overlays -->
            <div class="absolute left-0 top-0 bottom-0 w-16 z-10" 
                 style="background: linear-gradient(90deg, #1a1433 0%, transparent 100%);"></div>
            <div class="absolute right-0 top-0 bottom-0 w-16 z-10" 
                 style="background: linear-gradient(270deg, #0f172a 0%, transparent 100%);"></div>

            <!-- Marquee Track -->
            <div class="flex ann-marquee-track" id="marqueeTrack">
                <?php foreach($loopedUpdates as $idx => $item): 
                    $style = getBadgeStyle($item['badge'], $badgeStyles);
                    $isClickable = !empty($item['link']) && $item['link'] !== '#';
                ?>
                    <div class="ann-item flex items-center whitespace-nowrap px-3 sm:px-4 group <?= $isClickable ? 'cursor-pointer' : '' ?>"
                         <?= $isClickable ? "onclick=\"window.open('" . htmlspecialchars($item['link'], ENT_QUOTES) . "', '_blank')\"" : '' ?>>
                        
                        <!-- Type indicator dot -->
                        <span class="relative flex items-center justify-center w-2 h-2 mr-3 shrink-0">
                            <span class="absolute w-full h-full rounded-full animate-ping opacity-40" 
                                  style="background: <?= $style['bg'] ?>;"></span>
                            <span class="relative w-1.5 h-1.5 rounded-full" 
                                  style="background: <?= $style['bg'] ?>; box-shadow: 0 0 6px <?= $style['glow'] ?>;"></span>
                        </span>

                        <!-- Date chip -->
                        <span class="mr-3 shrink-0 inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold tracking-wider"
                              style="background: rgba(245,158,11,0.1); color: #fbbf24; border: 1px solid rgba(245,158,11,0.15);">
                            <i class="far fa-clock mr-1 text-[8px] opacity-70"></i>
                            <?= $item['date'] ?? date("M d") ?>
                        </span>

                        <!-- NEW badge (animated glow) -->
                        <?php if($item["isNew"]): ?>
                            <span class="mr-2.5 shrink-0 inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black tracking-widest ann-new-badge"
                                  style="background: linear-gradient(135deg, #f59e0b, #ef4444); color: #fff; box-shadow: 0 0 12px rgba(245,158,11,0.4), 0 0 4px rgba(239,68,68,0.3);">
                                <span class="relative flex h-1.5 w-1.5 mr-1.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-60"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-white"></span>
                                </span>
                                NEW
                            </span>
                        <?php endif; ?>

                        <!-- Custom badge from database -->
                        <span class="mr-3 shrink-0 inline-flex items-center gap-1.5 px-2.5 py-[3px] rounded-md text-[10px] font-bold uppercase tracking-wider ann-badge-hover"
                              style="background: <?= $style['bg'] ?>18; color: <?= $style['bg'] ?>; border: 1px solid <?= $style['bg'] ?>30;">
                            <i class="fas <?= $style['icon'] ?> text-[8px]"></i>
                            <?= htmlspecialchars($item['badge']) ?>
                        </span>

                        <!-- Title text -->
                        <span class="text-[13px] font-medium tracking-wide transition-colors duration-300 ann-item-text"
                              style="color: rgba(226,232,240,0.85);">
                            <?= htmlspecialchars($item["text"]) ?>
                        </span>

                        <!-- External link icon -->
                        <?php if($isClickable): ?>
                            <span class="ml-2.5 shrink-0 transition-all duration-300 ann-link-icon" 
                                  style="color: rgba(148,163,184,0.5);">
                                <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                            </span>
                        <?php endif; ?>

                        <!-- Diamond separator -->
                        <span class="mx-5 sm:mx-7 flex items-center gap-1.5 shrink-0 opacity-30">
                            <span class="w-[3px] h-[3px] rounded-full" style="background: #f59e0b;"></span>
                            <span class="w-1 h-1 rotate-45" style="background: #f59e0b;"></span>
                            <span class="w-[3px] h-[3px] rounded-full" style="background: #f59e0b;"></span>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <!-- Subtle bottom highlight line -->
    <div class="absolute bottom-0 left-0 right-0 h-[1px]" style="background: linear-gradient(90deg, transparent, rgba(245,158,11,0.3), transparent);"></div>
</div>

<!-- ═══════════════════════════════════════════════════════════════
     STYLES
     ═══════════════════════════════════════════════════════════════ -->
<style>
/* ── Marquee Animation (smooth infinite scroll) ── */
@keyframes annMarquee {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-33.333%); }
}

.ann-marquee-track {
    display: flex;
    animation: annMarquee <?= max(count($UPDATES) * 3, 15) ?>s linear infinite;
    will-change: transform;
}

.ann-marquee-track:hover {
    animation-play-state: paused;
}

/* ── Mobile: slightly faster ── */
@media (max-width: 640px) {
    .ann-marquee-track {
        animation-duration: <?= max(count($UPDATES) * 2, 10) ?>s;
    }
}

/* ── Hover effects on items ── */
.ann-item:hover .ann-item-text {
    color: #fbbf24 !important;
}

.ann-item:hover .ann-link-icon {
    color: #f59e0b !important;
    transform: translateX(2px) translateY(-1px);
}

.ann-item:hover .ann-badge-hover {
    filter: brightness(1.3);
    transform: scale(1.05);
    transition: all 0.3s ease;
}

.ann-badge-hover {
    transition: all 0.3s ease;
}

/* ── NEW badge subtle glow pulse ── */
@keyframes annNewGlow {
    0%, 100% { box-shadow: 0 0 8px rgba(245,158,11,0.3), 0 0 2px rgba(239,68,68,0.2); }
    50%      { box-shadow: 0 0 16px rgba(245,158,11,0.5), 0 0 6px rgba(239,68,68,0.4); }
}

.ann-new-badge {
    animation: annNewGlow 2s ease-in-out infinite;
}

/* ── Pulsing icon ring ── */
@keyframes annIconPulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50%      { transform: scale(1.6); opacity: 0; }
}

.ann-icon-pulse {
    animation: annIconPulse 3s ease-in-out infinite;
}

/* ── Floating background particles ── */
@keyframes annFloat1 {
    0%, 100% { transform: translate(0, 0) scale(1);   opacity: 0.03; }
    50%      { transform: translate(80px, -20px) scale(1.5); opacity: 0.06; }
}
@keyframes annFloat2 {
    0%, 100% { transform: translate(0, 0) scale(1);   opacity: 0.02; }
    50%      { transform: translate(-60px, 15px) scale(1.3); opacity: 0.05; }
}
@keyframes annFloat3 {
    0%, 100% { transform: translate(0, 0) scale(1);   opacity: 0.03; }
    50%      { transform: translate(40px, 10px) scale(1.2);  opacity: 0.04; }
}

.ann-particle {
    position: absolute;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(245,158,11,0.2), transparent 70%);
    pointer-events: none;
}

.ann-p1 { width: 120px; height: 120px; top: -30px; left: 20%;  animation: annFloat1 8s ease-in-out infinite; }
.ann-p2 { width: 80px;  height: 80px;  top: 10px;  left: 55%;  animation: annFloat2 10s ease-in-out infinite; }
.ann-p3 { width: 100px; height: 100px; top: -20px; left: 80%;  animation: annFloat3 12s ease-in-out infinite; }
</style>

<!-- ═══════════════════════════════════════════════════════════════
     SCRIPT
     ═══════════════════════════════════════════════════════════════ -->
<script>
(function() {
    const marquee = document.getElementById('marqueeTrack');
    if (!marquee) return;

    // ── Pause on hover ──
    marquee.addEventListener('mouseenter', () => {
        marquee.style.animationPlayState = 'paused';
    });
    marquee.addEventListener('mouseleave', () => {
        marquee.style.animationPlayState = 'running';
    });

    // ── Touch swipe support ──
    let touchStartX = 0;
    marquee.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
        marquee.style.animationPlayState = 'paused';
    }, { passive: true });

    marquee.addEventListener('touchend', e => {
        const diff = Math.abs(e.changedTouches[0].screenX - touchStartX);
        if (diff < 10) {
            // Tap — find the closest item and trigger its click
            const target = e.target.closest('.ann-item[onclick]');
            if (target) {
                const fn = target.getAttribute('onclick');
                if (fn) new Function(fn)();
            }
        }
        // Resume after a short pause
        setTimeout(() => {
            marquee.style.animationPlayState = 'running';
        }, 2500);
    }, { passive: true });

    // ── Accessibility: respect prefers-reduced-motion ──
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        marquee.style.animationPlayState = 'paused';
    }
})();
</script>