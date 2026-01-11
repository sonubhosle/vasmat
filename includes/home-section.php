<?php
include 'admin/includes/db.php';

// Fetch Announcements
$announcements_result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");

// Fetch News
$news_result = $conn->query("SELECT * FROM news ORDER BY event_date DESC");
?>



<section class="py-24 px-6 bg-white relative overflow-hidden ">
    <!-- Dynamic Background Elements -->
    <div class="absolute inset-0 dot-pattern opacity-[0.05]"></div>
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-amber-50 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-blue-50 blur-[120px] rounded-full pointer-events-none"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">

            <!-- COLUMN 1: PRINCIPAL'S DESK -->
            <div
                class="bg-slate-50 text-center rounded-[48px] p-8 flex flex-col border border-slate-100  transition-all duration-500 ">

                <div class="relative mb-8  flex items-center justify-center ">
                    <?php
                    $principal_image = "uploads/principal.jpg";
                    ?>
                    <img src="<?= file_exists($principal_image) ? $principal_image : 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=600&auto=format&fit=crop' ?>"
                        alt="Principal" class="w-24 h-24 object-cover object-top  rounded-xl " />

                </div>
                <h1 class="text-xl font-bold text-slate-800 mb-4">Principals Desk</h1>
                <p class="text-slate-600 font-medium leading-relaxed italic mb-8 flex-1">
                    "Welcome to Mit College, an institution where excellence is a tradition."
                </p>

                <a href="principal-statement.php"
                    class="w-full py-4 bg-white border border-slate-200 rounded-2xl text-[12px] font-black uppercase tracking-widest text-slate-900 hover:bg-slate-900 hover:text-white transition-all">
                    PRINCIPAL DESK
                </a>
            </div>

            <!-- COLUMN 2: ANNOUNCEMENTS WITH GLOWING TEXT BADGES -->
            <div class="bg-white rounded-[48px] p-8 flex flex-col relative overflow-hidden group  border border-slate-100">
                <div class="absolute top-0 right-0 w-40 h-40 bg-amber-600/10 blur-[80px] rounded-full pointer-events-none"></div>
                <div class="flex items-center justify-between mb-6 relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-amber-500 rounded-full animate-pulse shadow-[0_0_8px_#ef4444]"></div>
                        <h3 class="text-slate-900 font-black uppercase text-xs tracking-[0.2em]">Announcements</h3>
                    </div>
                    <i class="fas fa-bullhorn text-lg text-amber-500"></i>
                </div>

                <div class="marquee-container flex-1 relative z-10">
                    <div class="marquee-content">
                        <?php  $badge_colors = [
                                  'hot' => '#ef4444',
                                  'event' => '#f97316',
                                  'new' => '#94a3b8',
                                  'important' => '#a855f7',
                                  'update' => '#38bdf8',
                                  'urgent' => '#dc2626',
                                  'notice' => '#6366f1',
                                  'warning' => '#f59e0b',
                                   'info' => '#22c55e'
                                ];

                                  $announcement_items = [];

                                if ($announcements_result && $announcements_result->num_rows > 0):
                                while($row = $announcements_result->fetch_assoc()):
                                $raw_badge = trim($row['badge'] ?? 'update');
                                $key = strtolower($raw_badge);
                                $badge_text = strtoupper($raw_badge);
                                $color = $badge_colors[$key] ?? '#64748b';

                               $date = date("M d, Y", strtotime($row['created_at'] ?? 'now'));

                                $announcement_items[] = [
                               'badge' => $badge_text,
                               'color' => $color,
                               'date' => $date,
                               'title' => htmlspecialchars($row['title']),
                               'pdf' => $row['pdf'] ?? null
                             ];
                               endwhile;
                               endif;

                              if(empty($announcement_items)):
                                  $announcement_items[] = [
                                      'badge' => 'NO UPDATES',
                                      'color' => '#64748b',
                                      'date' => '--',
                                      'title' => 'No announcements available',
                                      'pdf' => null
                                  ];
                                    endif;

                                  foreach($announcement_items as $item): ?>
                        <div class="flex items-center gap-3 py-1 px-2 transition-colors cursor-pointer  ">
                            <span style="color: <?= $item['color'] ?>;"
                                class="badge-glow text-[10px] font-bold uppercase tracking-wider">
                                <?= $item['badge'] ?>
                            </span>

                            <div class="flex-1 min-w-0">
                                <?php if(!empty($item['pdf'])): ?>
                                <a href="uploads/<?= $item['pdf'] ?>" target="_blank"
                                    class="text-slate-800 text-[8px] font-semibold hover:text-amber-600 transition-colors truncate block">
                                    <?= $item['title'] ?>
                                </a>
                                <?php else: ?>
                                <div class="text-slate-800 text-[12px] font-semibold truncate">
                                    <?= $item['title'] ?>
                                </div>
                                <?php endif; ?>
                            </div>

                            <span class="flex-shrink-0 text-[10px] font-bold text-slate-500 uppercase whitespace-nowrap">
                                <?= $item['date'] ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100 relative z-10">
                    <a href="announcements.php"
                        class="flex items-center justify-center gap-2 w-full px-5 py-3 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-amber-600 transition-all shadow-lg hover:scale-[1.02]">
                        <span>Read More</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- COLUMN 3: NEWS & EVENTS -->
            <div
                class="bg-slate-50 rounded-[48px] p-8 flex flex-col border border-slate-100  transition-all duration-500  ">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="text-slate-900 font-black uppercase text-xs tracking-[0.2em]">Resources</h3>
                    <i class="fas fa-sparkles text-lg text-blue-500 animate-spin" style="animation-duration: 3s;"></i>
                </div>


                <div class="flex flex-wrap gap-2">
                    <?php
    $links = [
        ['title' => 'Calendar', 'icon' => 'fas fa-calendar-alt', 'link' => 'calendar.php'],
        ['title' => 'Scholarship', 'icon' => 'fas fa-graduation-cap', 'link' => 'scholarship.php'],
         ['title' => 'Help', 'icon' => 'fas fa-question-circle', 'link' => 'help.php'],
        ['title' => 'E-Library', 'icon' => 'fas fa-book-open', 'link' => 'library.php'],
        ['title' => 'Faculty', 'icon' => 'fas fa-users', 'link' => 'faculty.php'],
        ['title' => 'Support', 'icon' => 'fas fa-question-circle', 'link' => 'support.php'],
        ['title' => 'Results', 'icon' => 'fas fa-chart-line', 'link' => 'results.php'],
         ['title' => 'Addmission', 'icon' => 'fas fa-user-graduate', 'link' => 'admission.php'],
    ];
    
    foreach($links as $link):
    ?>
                    <a href="<?= $link['link'] ?>"
                        class="text-[11px] bg-white flex items-center gap-2 rounded-xl border border-slate-100 px-4 py-3 font-semibold text-slate-700 hover:text-amber-600 transition-colors whitespace-nowrap">
                        <i class="<?= $link['icon'] ?> text-[10px]"></i>

                        <?= $link['title'] ?>
                    </a>
                    <?php endforeach; ?>
                </div>


            </div>

        </div>
    </div>
</section>

<?php
if (isset($announcements_result)) $announcements_result->free();
if (isset($news_result)) $news_result->free();
?>



<section class="relative  py-10 px-6 overflow-hidden">
    <div
        class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-full -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-15%] right-[-10%] w-[600px] h-[600px] bg-amber-100/50 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[10%] left-[-15%] w-[500px] h-[500px] bg-blue-100/30 rounded-full blur-[100px]">
        </div>
    </div>

    <div class="max-w-6xl mx-auto flex flex-col items-center text-center">
        <div
            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-slate-100 premium-shadow text-slate-500 text-xs font-bold mb-8 animate-in fade-in slide-in-from-bottom-2">
            <ShieldCheck size={16} class="text-amber-500" />
            <span class="uppercase tracking-widest text-[10px]">Campus Integrity Protocol 2024</span>
        </div>

        <h1
            class="text-5xl  font-black text-slate-900 leading-[0.95] tracking-tighter mb-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
            CAMPUS IS YOUR <br />
            <span class="text-amber-500 relative inline-block">
                SAFE SPACE.
                <svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 358 8" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 5.5C118.5 2 237.5 1.5 357 7" stroke="#EF4444" strokeWidth="2" strokeLinecap="round" />
                </svg>
            </span>
        </h1>

        <p
            class="text-lg  text-slate-500 max-w-2xl mx-auto mb-12 leading-relaxed font-medium animate-in fade-in slide-in-from-bottom-6 duration-1000">
            A zero-tolerance ecosystem designed to empower students. Confidential reporting, instant tracking, and
            unwavering support.
        </p>

        <div
            class="flex flex-col sm:flex-row gap-5 justify-center items-center w-full max-w-lg mb-20 animate-in fade-in slide-in-from-bottom-8 duration-1000">
            <button
                class="w-full sm:w-auto px-10 py-3 bg-gradient-to-br from-amber-400 to-amber-600  text-white font-semibold rounded-xl btn-hover-effect shadow-2xl  flex items-center justify-center gap-3 text-[13px]">
                 Report Incident
            </button>
        </div>

        <div
            class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-16 opacity-40 grayscale hover:grayscale-0 transition-all duration-700">
            <div class="flex flex-col items-center gap-2">
                <i class='bx bx-shield text-[22px]'></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Anti-Abuse</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <i class='bx bx-heart text-[22px]'></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Welfare</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <i class='bx bx-user text-[22px]'></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Unity</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <i class='bx bx-check-shield text-[22px]'></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Security</span>
            </div>
        </div>
    </div>
</section>