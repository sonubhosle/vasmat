<?php
include 'admin/includes/db.php';

// Fetch Announcements
$announcements_result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>

<section class="py-10 sm:py-16 px-4 sm:px-6  relative overflow-hidden bg-slate-100">
    <!-- Dynamic Background Elements -->
 
    <div class="max-w-7xl mx-auto relative z-10">

    
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-stretch">

            <div class="home-card-desk group bg-white rounded-[32px] sm:rounded-[48px] p-6 sm:p-8 flex flex-col border border-slate-200 transition-all duration-500 hover:shadow-xl hover:shadow-amber-500/5 hover:border-amber-100">

                <!-- Decorative accent -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-24 h-1 rounded-b-full bg-gradient-to-r from-amber-400 to-orange-400 opacity-0 group-hover:opacity-100 transition-all duration-500"></div>

                <div class="relative mb-6 flex flex-col items-center">
                    <!-- Image with ring -->
                    <div class="relative">
                        <div class="absolute -inset-1 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-400 opacity-20 blur-sm group-hover:opacity-40 transition-all duration-500"></div>
                        <?php $principal_image = "uploads/principal.jpg"; ?>
                        <img src="<?= file_exists($principal_image) ? $principal_image : 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=600&auto=format&fit=crop' ?>"
                             alt="Principal"
                             class="relative w-24 h-24 object-cover object-top rounded-2xl ring-2 ring-white shadow-lg" />
                    </div>

                    <!-- Name badge -->
                    <div class="mt-4 text-center">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            <span class="text-[10px] font-black uppercase tracking-[0.15em] text-amber-700">Principal</span>
                        </div>
                    </div>
                </div>

                <h2 class="text-lg font-bold text-slate-800 mb-3 text-center">Principal's Desk</h2>
                <p class="text-sm text-slate-500 font-medium leading-relaxed italic mb-6 flex-1 text-center">
                    "Welcome to MIT College, an institution where excellence is a tradition and every student is a future leader."
                </p>

                <a href="principal-statement.php"
                   class="group/btn flex items-center justify-center gap-2 w-full py-3.5 bg-white border-2 border-slate-200 rounded-2xl text-[11px] font-black uppercase tracking-[0.15em] text-slate-800 hover:bg-gradient-to-r hover:from-amber-500 hover:to-orange-500 hover:text-white hover:border-transparent hover:shadow-lg hover:shadow-amber-500/20 transition-all duration-300">
                    <i class="fas fa-book-open text-[10px] opacity-60"></i>
                    <span>Read Statement</span>
                    <i class="fas fa-arrow-right text-[9px] opacity-0 -translate-x-2 group-hover/btn:opacity-100 group-hover/btn:translate-x-0 transition-all duration-300"></i>
                </a>
            </div>

            <div class="home-card-announce relative bg-white rounded-[32px] sm:rounded-[48px] p-6 sm:p-8 flex flex-col overflow-hidden border border-slate-200 transition-all duration-500 hover:shadow-xl hover:shadow-amber-500/5 hover:border-amber-100">
                
                <!-- Corner glow -->
                <div class="absolute top-0 right-0 w-40 h-40 bg-amber-500/10 blur-[80px] rounded-full pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-blue-500/5 blur-[60px] rounded-full pointer-events-none"></div>

                <!-- Header -->
                <div class="flex items-center justify-between mb-6 relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="relative flex items-center justify-center">
                            <span class="absolute w-6 h-6 rounded-full bg-amber-400/20 animate-ping"></span>
                            <span class="relative w-2.5 h-2.5 rounded-full bg-gradient-to-br from-amber-400 to-red-400 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
                        </div>
                        <h3 class="text-slate-900 font-black uppercase text-xs tracking-[0.2em]">Announcements</h3>
                    </div>
                    <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-amber-50 border border-amber-100">
                        <i class="fas fa-bullhorn text-sm text-amber-500"></i>
                    </div>
                </div>

                <!-- Announcement Items (vertical marquee) -->
                <div class="marquee-container flex-1 relative z-10 overflow-hidden" style="max-height: 280px;">
                    <div class="marquee-content">
                        <?php
                        $badge_colors = [
                            'hot'       => ['bg' => '#ef4444', 'light' => '#fef2f2'],
                            'event'     => ['bg' => '#7c3aed', 'light' => '#f5f3ff'],
                            'new'       => ['bg' => '#10b981', 'light' => '#ecfdf5'],
                            'important' => ['bg' => '#f59e0b', 'light' => '#fffbeb'],
                            'update'    => ['bg' => '#3b82f6', 'light' => '#eff6ff'],
                            'urgent'    => ['bg' => '#ef4444', 'light' => '#fef2f2'],
                            'notice'    => ['bg' => '#6366f1', 'light' => '#eef2ff'],
                            'warning'   => ['bg' => '#f59e0b', 'light' => '#fffbeb'],
                            'info'      => ['bg' => '#06b6d4', 'light' => '#ecfeff'],
                            'exam'      => ['bg' => '#8b5cf6', 'light' => '#f5f3ff'],
                            'result'    => ['bg' => '#14b8a6', 'light' => '#f0fdfa'],
                            'admission' => ['bg' => '#f43f5e', 'light' => '#fff1f2'],
                            'placement' => ['bg' => '#059669', 'light' => '#ecfdf5'],
                            'workshop'  => ['bg' => '#ec4899', 'light' => '#fdf2f8'],
                            'holiday'   => ['bg' => '#22c55e', 'light' => '#f0fdf4'],
                        ];

                        $announcement_items = [];

                        if ($announcements_result && $announcements_result->num_rows > 0):
                            while($row = $announcements_result->fetch_assoc()):
                                $raw_badge = trim($row['badge'] ?? 'update');
                                $key = strtolower($raw_badge);
                                $badge_text = strtoupper($raw_badge);
                                $colors = $badge_colors[$key] ?? ['bg' => '#64748b', 'light' => '#f8fafc'];

                                $date = date("M d", strtotime($row['created_at'] ?? 'now'));
                                $isNew = (strtotime($row['created_at'] ?? 'now') > strtotime('-7 days'));

                                $announcement_items[] = [
                                    'badge' => $badge_text,
                                    'bg'    => $colors['bg'],
                                    'light' => $colors['light'],
                                    'date'  => $date,
                                    'title' => htmlspecialchars($row['title']),
                                    'pdf'   => $row['pdf'] ?? null,
                                    'isNew' => $isNew,
                                ];
                            endwhile;
                        endif;

                        if (empty($announcement_items)):
                            $announcement_items[] = [
                                'badge' => 'INFO',
                                'bg'    => '#64748b',
                                'light' => '#f8fafc',
                                'date'  => '--',
                                'title' => 'No announcements available',
                                'pdf'   => null,
                                'isNew' => false,
                            ];
                        endif;

                        foreach($announcement_items as $item): ?>
                        <div class="group/ann flex items-start gap-3 py-3 px-3 rounded-xl hover:bg-slate-50/80 transition-all duration-200 cursor-pointer border-b border-slate-50 last:border-0">
                            
                            <!-- Badge pill -->
                            <div class="shrink-0 mt-0.5">
                                <span class="inline-flex items-center gap-1 px-2 py-[3px] rounded-md text-[9px] font-bold uppercase tracking-wider"
                                      style="background: <?= $item['light'] ?>; color: <?= $item['bg'] ?>; border: 1px solid <?= $item['bg'] ?>20;">
                                    <span class="w-1 h-1 rounded-full" style="background: <?= $item['bg'] ?>;"></span>
                                    <?= $item['badge'] ?>
                                </span>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <?php if(!empty($item['pdf'])): ?>
                                <a href="upload/<?= $item['pdf'] ?>" target="_blank"
                                   class="text-slate-800 text-sm font-semibold hover:text-amber-600 transition-colors truncate block leading-snug">
                                    <?= $item['title'] ?>
                                </a>
                                <?php else: ?>
                                <div class="text-slate-800 text-sm font-semibold truncate leading-snug">
                                    <?= $item['title'] ?>
                                </div>
                                <?php endif; ?>
                                
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1 block">
                                    <?= $item['date'] ?>
                                </span>
                            </div>

                            <!-- New indicator -->
                            <?php if($item['isNew']): ?>
                            <span class="shrink-0 mt-1 px-1.5 py-0.5 rounded text-[8px] font-black text-white bg-gradient-to-r from-amber-500 to-red-500 animate-pulse shadow-sm">
                                NEW
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- View All Button -->
                <div class="mt-6 pt-5 border-t border-slate-100 relative z-10">
                    <a href="notices.php"
                       class="group/btn flex items-center justify-center gap-2 w-full px-5 py-3.5 bg-gradient-to-r from-slate-900 to-slate-800 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.15em] hover:from-amber-500 hover:to-orange-500 transition-all duration-300 shadow-lg hover:shadow-amber-500/20 hover:scale-[1.02]">
                        <i class="fas fa-list-ul text-[10px] opacity-70"></i>
                        <span>View All</span>
                        <i class="fas fa-arrow-right text-[9px] opacity-0 -translate-x-2 group-hover/btn:opacity-100 group-hover/btn:translate-x-0 transition-all duration-300"></i>
                    </a>
                </div>
            </div>

            <div class="home-card-desk group bg-white rounded-[32px] sm:rounded-[48px] p-6 sm:p-8 flex flex-col border border-slate-200 transition-all duration-500 hover:shadow-xl hover:shadow-blue-500/5 hover:border-blue-100">

                <!-- Decorative accent -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-24 h-1 rounded-b-full bg-gradient-to-r from-blue-400 to-indigo-500 opacity-0 group-hover:opacity-100 transition-all duration-500"></div>

                <div class="relative mb-6 flex flex-col items-center">
                    <!-- Image with ring -->
                    <div class="relative">
                        <div class="absolute -inset-1 rounded-2xl bg-gradient-to-br from-blue-400 to-indigo-500 opacity-20 blur-sm group-hover:opacity-40 transition-all duration-500"></div>
                        <?php $president_image = "uploads/president.jpg"; ?>
                        <img src="<?= file_exists($president_image) ? $president_image : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=600&auto=format&fit=crop' ?>"
                             alt="President"
                             class="relative w-24 h-24 object-cover object-top rounded-2xl ring-2 ring-white shadow-lg" />
                    </div>

                    <!-- Name badge -->
                    <div class="mt-4 text-center">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            <span class="text-[10px] font-black uppercase tracking-[0.15em] text-blue-700">President</span>
                        </div>
                    </div>
                </div>

                <h2 class="text-lg font-bold text-slate-800 mb-3 text-center">President's Desk</h2>
                <p class="text-sm text-slate-500 font-medium leading-relaxed italic mb-6 flex-1 text-center">
                    "Our vision is to shape tomorrow's leaders with knowledge, character, and an unwavering commitment to society."
                </p>

                <a href="president-statement.php"
                   class="group/btn flex items-center justify-center gap-2 w-full py-3.5 bg-white border-2 border-slate-200 rounded-2xl text-[11px] font-black uppercase tracking-[0.15em] text-slate-800 hover:bg-gradient-to-r hover:from-blue-500 hover:to-indigo-500 hover:text-white hover:border-transparent hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-300">
                    <i class="fas fa-book-open text-[10px] opacity-60"></i>
                    <span>Read Statement</span>
                    <i class="fas fa-arrow-right text-[9px] opacity-0 -translate-x-2 group-hover/btn:opacity-100 group-hover/btn:translate-x-0 transition-all duration-300"></i>
                </a>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════
             ROW 2: Quick Links / Resources (Full Width)
             ═══════════════════════════════════════════════ -->
        <div class="mt-8  ">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-amber-100 to-orange-50 border border-amber-200/50">
                        <i class="fas fa-compass text-amber-500 text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-slate-900 font-black uppercase text-lg tracking-[0.2em]">Quick Links</h3>
                        <p class="text-[13px] text-slate-400 font-semibold tracking-wider mt-0.5">Resources & Services</p>
                    </div>
                </div>
                <div class="hidden sm:flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider"><?= count([1,2,3,4,5,6,7,8]) ?> links</span>
                </div>
            </div>

            <!-- Links Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-8 gap-3 sm:gap-4">
                <?php
                $links = [
                    ['title' => 'Calendar',    'icon' => 'fas fa-calendar-alt',   'link' => 'calendar.php',    'color' => '#f59e0b', 'light' => '#fffbeb'],
                    ['title' => 'Scholarship', 'icon' => 'fas fa-graduation-cap', 'link' => 'scholarship.php', 'color' => '#8b5cf6', 'light' => '#f5f3ff'],
                    ['title' => 'Research',    'icon' => 'fas fa-microscope',     'link' => 'research.php',    'color' => '#10b981', 'light' => '#ecfdf5'],
                    ['title' => 'E-Library',   'icon' => 'fas fa-book-open',      'link' => 'library.php',     'color' => '#06b6d4', 'light' => '#ecfeff'],
                    ['title' => 'Placement',   'icon' => 'fas fa-briefcase',      'link' => 'placement.php',   'color' => '#3b82f6', 'light' => '#eff6ff'],
                    ['title' => 'Alumni',      'icon' => 'fas fa-user-friends',   'link' => 'alumni.php',      'color' => '#ec4899', 'light' => '#fdf2f8'],
                    ['title' => 'Committees',  'icon' => 'fas fa-users-cog',      'link' => 'committees.php',  'color' => '#14b8a6', 'light' => '#f0fdfa'],
                    ['title' => 'Admission',   'icon' => 'fas fa-user-graduate',  'link' => 'admission.php',   'color' => '#f43f5e', 'light' => '#fff1f2'],
                ];
                
                foreach($links as $link): ?>
                <a href="<?= $link['link'] ?>"
                   class="group/link flex  items-center gap-2.5 px-4 py-2 rounded-2xl border border-slate-100 bg-white hover:scale-[1.03] transition-all duration-300 hover:shadow-md"
                   style="--link-color: <?= $link['color'] ?>; --link-light: <?= $link['light'] ?>;"
                   onmouseenter="this.style.borderColor=this.style.getPropertyValue('--link-color')+'30'; this.style.background=this.style.getPropertyValue('--link-light')"
                   onmouseleave="this.style.borderColor=''; this.style.background=''">
                    
                    <!-- Icon circle -->
                    <div class="flex items-center justify-center w-6 h-6 sm:w-7 sm:h-7 rounded-xl transition-all duration-300"
                         style="background: <?= $link['light'] ?>; color: <?= $link['color'] ?>;">
                        <i class="<?= $link['icon'] ?> text-sm sm:text-base"></i>
                    </div>
                    
                    <!-- Label -->
                    <span class="text-[11px] font-bold text-slate-600 group-hover/link:text-slate-800 transition-colors tracking-wider text-center leading-tight">
                        <?= $link['title'] ?>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>

<!-- ═══════════════════════════════════════════════
     NAAC & INSTITUTIONAL QUALITY SECTION
     ═══════════════════════════════════════════════ -->
<section class="py-16 sm:py-28 bg-white relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-amber-500/5 blur-[120px] rounded-full translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-blue-500/5 blur-[100px] rounded-full -translate-x-1/2 translate-y-1/2"></div>
    
    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="flex flex-col lg:flex-row items-start lg:items-end justify-between mb-16 sm:mb-24 gap-8">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-100 text-amber-600 text-[10px] font-black uppercase tracking-[0.2em] mb-4">
                    <i class="fas fa-award"></i> Quality Benchmarks
                </div>
                <h2 class="text-4xl sm:text-7xl font-black text-slate-900 tracking-tighter leading-[0.85] uppercase">
                    Institutional <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600">Excellence Portal</span>
                </h2>
            </div>
            <div class="lg:max-w-sm">
                <p class="text-slate-500 font-medium leading-relaxed border-l-4 border-amber-500/20 pl-6 py-2">
                    Committed to maintaining the highest standards of higher education through continuous quality monitoring and NAAC accreditation protocols.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- NAAC Portal -->
            <div class="group bg-slate-50/50 rounded-[3rem] p-8 border border-slate-100 hover:bg-white hover:shadow-2xl hover:shadow-amber-500/10 hover:border-amber-200 transition-all duration-500">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100 mb-10 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                    <i class="fas fa-medal text-2xl text-amber-500"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">NAAC Portal</h3>
                <p class="text-sm text-slate-500 leading-relaxed mb-10">Access AQAR, SSR, and DVV documents for institutional accreditation cycles.</p>
                <a href="naac.php" class="inline-flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-amber-600 hover:gap-4 transition-all duration-300">
                    Quality Hub <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Committees -->
            <div class="group bg-slate-50/50 rounded-[3rem] p-8 border border-slate-100 hover:bg-white hover:shadow-2xl hover:shadow-blue-500/10 hover:border-blue-200 transition-all duration-500">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100 mb-10 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                    <i class="fas fa-users-cog text-2xl text-blue-500"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Statutory Cells</h3>
                <p class="text-sm text-slate-500 leading-relaxed mb-10">Transparency via Anti-Ragging, Grievance, and Internal Complaint committees.</p>
                <a href="committees.php" class="inline-flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-blue-600 hover:gap-4 transition-all duration-300">
                    View Structure <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Disclosures -->
            <div class="group bg-slate-50/50 rounded-[3rem] p-8 border border-slate-100 hover:bg-white hover:shadow-2xl hover:shadow-purple-500/10 hover:border-purple-200 transition-all duration-500">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100 mb-10 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                    <i class="fas fa-file-contract text-2xl text-purple-500"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Mandatory Docs</h3>
                <p class="text-sm text-slate-500 leading-relaxed mb-10">Regulatory approvals, audit statements, and institutional policies for public review.</p>
                <a href="disclosures.php" class="inline-flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-purple-600 hover:gap-4 transition-all duration-300">
                    Public Files <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Feedback -->
            <div class="group bg-slate-50/50 rounded-[3rem] p-8 border border-slate-100 hover:bg-white hover:shadow-2xl hover:shadow-emerald-500/10 hover:border-emerald-200 transition-all duration-500">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100 mb-10 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                    <i class="fas fa-comment-dots text-2xl text-emerald-500"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Stakeholder Info</h3>
                <p class="text-sm text-slate-500 leading-relaxed mb-10">Feedback system for students, parents, and alumni to ensure continuous growth.</p>
                <a href="feedback.php" class="inline-flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-emerald-600 hover:gap-4 transition-all duration-300">
                    Share Insights <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<?php
if (isset($announcements_result)) $announcements_result->free();
?>


<section class="relative py-10 px-6 overflow-hidden bg-white">
 

    <div class=" flex flex-col items-center text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-slate-100 premium-shadow text-slate-500 text-xs font-bold mb-8 animate-in fade-in slide-in-from-bottom-2">
            <ShieldCheck size={16} class="text-amber-500" />
            <span class="uppercase tracking-widest text-[10px]">Campus Integrity Protocol 2024</span>
        </div>

        <h1
            class="text-3xl font-black text-slate-900 leading-[0.95] tracking-tighter mb-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
            CAMPUS IS YOUR <br />
            <span class="text-amber-500 relative inline-block">
                SAFE SPACE.
                <svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 358 8" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 5.5C118.5 2 237.5 1.5 357 7" stroke="#EF4444" strokeWidth="2" strokeLinecap="round" />
                </svg>
            </span>
        </h1>

        <p class="text-lg  text-slate-500 max-w-2xl mx-auto mb-12 leading-relaxed font-medium animate-in fade-in slide-in-from-bottom-6 duration-1000">
            A zero-tolerance ecosystem designed to empower students. Confidential reporting, instant tracking, and
            unwavering support.
        </p>

        <div
            class="flex flex-col sm:flex-row gap-5 justify-center items-center w-full max-w-lg mb-20 animate-in fade-in slide-in-from-bottom-8 duration-1000">
            <button class="w-full sm:w-auto px-10 py-3 bg-gradient-to-br from-amber-400 to-amber-600  text-white font-semibold rounded-xl btn-hover-effect shadow-2xl  flex items-center justify-center gap-3 text-[13px]">
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

<!-- Home Section Specific Styles -->
<style>
.home-card-desk {
    position: relative;
}

/* Vertical marquee for announcements */
.marquee-container {
    mask-image: linear-gradient(to bottom, transparent 0%, black 8%, black 92%, transparent 100%);
    -webkit-mask-image: linear-gradient(to bottom, transparent 0%, black 8%, black 92%, transparent 100%);
}

/* Quick link hover lift */
.group\/link:hover .fa-icon {
    transform: translateY(-2px);
}
</style>