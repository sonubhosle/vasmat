<?php
include 'admin/includes/db.php';
include 'admin/includes/functions.php';

$dept = 'BCA Dept';

include 'includes/header.php';
?>

<div class="px-6 py-12 ">
    <!-- Hero Header -->
    <div class="mb-16 animate-fade-in-up">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full uppercase bg-amber-50 border border-amber-100 text-amber-600 text-[10px] font-black  mb-6">
            <i class="fas fa-university"></i> Academic Department
        </div>
        <h1 class="text-3xl  font-black text-slate-900 mb-6 tracking-tight">
            Department of <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">BCA</span>
        </h1>
        <p class="text-slate-500 text-lg max-w-2xl font-medium">Access all academic materials, schedules, and official communications for Bachelor of Computer Applications.</p>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex gap-4 mb-12 flex-wrap animate-fade-in-up" style="animation-delay: 0.1s;">
        <button onclick="switchTab('notes')" id="tab-notes" 
                class="dept-tab flex items-center gap-3 px-8 py-3 rounded-[1.5rem] text-sm font-black uppercase tracking-widest transition-all shadow-xl shadow-slate-900/10 active-tab">
            <i class="fas fa-book"></i> Study Notes
        </button>
        <button onclick="switchTab('syllabus')" id="tab-syllabus" 
                class="dept-tab flex items-center gap-3 px-8 py-3 rounded-[1.5rem] text-sm font-black uppercase tracking-widest transition-all inactive-tab">
            <i class="fas fa-graduation-cap"></i> Syllabus
        </button>
        <button onclick="switchTab('timetable')" id="tab-timetable" 
                class="dept-tab flex items-center gap-3 px-8 py-3 rounded-[1.5rem] text-sm font-black uppercase tracking-widest transition-all inactive-tab">
            <i class="fas fa-calendar-alt"></i> Timetable
        </button>
        <button onclick="switchTab('circulars')" id="tab-circulars" 
                class="dept-tab flex items-center gap-3 px-8 py-3 rounded-[1.5rem] text-sm font-black uppercase tracking-widest transition-all inactive-tab">
            <i class="fas fa-bullhorn"></i> Circulars
        </button>
    </div>

    <!-- Resources Sections -->
    <div class="relative min-h-[400px]">
        
        <!-- Notes Section -->
        <section id="section-notes" class="dept-section opacity-100 visible transition-all duration-500">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                    <div class="w-2 h-8 bg-emerald-500 rounded-full"></div>
                    Study <span class="italic font-serif">Notes</span>
                </h2>
                <a href="subject_notes.php?dept=<?= urlencode($dept) ?>" class="text-xs font-black uppercase tracking-widest text-emerald-600 hover:text-emerald-700">View All <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="overflow-x-auto rounded-[2rem] border border-slate-100 bg-white shadow-xl shadow-slate-200/40">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Title</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Description</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Added By</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php
                        $stmt = $conn->prepare("SELECT fc.title, fc.description, fc.file_path, fc.created_at, f.name as faculty_name 
                                              FROM faculty_content fc 
                                              JOIN faculty f ON fc.faculty_id = f.id 
                                              WHERE fc.department = ? AND fc.type = 'notes' AND fc.status = 'approved' 
                                              ORDER BY fc.created_at DESC LIMIT 10");
                        $stmt->bind_param("s", $dept);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        if ($res->num_rows > 0):
                            while($row = $res->fetch_assoc()):
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                                        <i class="fas fa-book text-sm"></i>
                                    </div>
                                    <span class="text-sm font-bold text-slate-700"><?= htmlspecialchars($row['title']) ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm text-slate-500 max-w-xs truncate"><?= htmlspecialchars($row['description']) ?></td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-700 uppercase tracking-tight"><?= htmlspecialchars($row['faculty_name']) ?></span>
                                    <span class="text-[10px] text-slate-400 font-bold"><?= date('M d, Y', strtotime($row['created_at'])) ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all" title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" download class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-emerald-600 transition-all" title="Download">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center text-slate-400 font-bold uppercase tracking-widest text-xs">No notes uploaded yet.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Syllabus Section -->
        <section id="section-syllabus" class="dept-section hidden opacity-0 transition-all duration-500">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                    <div class="w-2 h-8 bg-amber-500 rounded-full"></div>
                    Academic <span class="italic font-serif">Syllabus</span>
                </h2>
                <a href="syllabus.php?dept=<?= urlencode($dept) ?>" class="text-xs font-black uppercase tracking-widest text-amber-600 hover:text-amber-700">View All <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="overflow-x-auto rounded-[2rem] border border-slate-100 bg-white shadow-xl shadow-slate-200/40">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Resource Title</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Added By</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php
                        $stmt = $conn->prepare("SELECT fc.title, fc.file_path, fc.created_at, f.name as faculty_name 
                                              FROM faculty_content fc 
                                              JOIN faculty f ON fc.faculty_id = f.id 
                                              WHERE fc.department = ? AND fc.type = 'syllabus' AND fc.status = 'approved' 
                                              ORDER BY fc.created_at DESC LIMIT 10");
                        $stmt->bind_param("s", $dept);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        if ($res->num_rows > 0):
                            while($row = $res->fetch_assoc()):
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-amber-500 group-hover:text-white transition-all">
                                        <i class="fas fa-scroll text-sm"></i>
                                    </div>
                                    <span class="text-sm font-bold text-slate-700"><?= htmlspecialchars($row['title']) ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-700 uppercase tracking-tight"><?= htmlspecialchars($row['faculty_name']) ?></span>
                                    <span class="text-[10px] text-slate-400 font-bold"><?= date('M d, Y', strtotime($row['created_at'])) ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all" title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" download class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-amber-600 transition-all" title="Download">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="3" class="px-8 py-12 text-center text-slate-400 font-bold uppercase tracking-widest text-xs">No syllabus uploaded yet.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Timetable Section -->
        <section id="section-timetable" class="dept-section hidden opacity-0 transition-all duration-500">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                    <div class="w-2 h-8 bg-blue-500 rounded-full"></div>
                    Class <span class="italic font-serif">Timetables</span>
                </h2>
                <a href="timetable.php?dept=<?= urlencode($dept) ?>" class="text-xs font-black uppercase tracking-widest text-blue-600 hover:text-blue-700">View All <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="overflow-x-auto rounded-[2rem] border border-slate-100 bg-white shadow-xl shadow-slate-200/40">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Timetable Title</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Added By</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php
                        $stmt = $conn->prepare("SELECT fc.title, fc.file_path, fc.created_at, f.name as faculty_name 
                                              FROM faculty_content fc 
                                              JOIN faculty f ON fc.faculty_id = f.id 
                                              WHERE fc.department = ? AND fc.type = 'timetable' AND fc.status = 'approved' 
                                              ORDER BY fc.created_at DESC LIMIT 10");
                        $stmt->bind_param("s", $dept);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        if ($res->num_rows > 0):
                            while($row = $res->fetch_assoc()):
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-blue-500 group-hover:text-white transition-all">
                                        <i class="fas fa-calendar-alt text-sm"></i>
                                    </div>
                                    <span class="text-sm font-bold text-slate-700"><?= htmlspecialchars($row['title']) ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-700 uppercase tracking-tight"><?= htmlspecialchars($row['faculty_name']) ?></span>
                                    <span class="text-[10px] text-slate-400 font-bold"><?= date('M d, Y', strtotime($row['created_at'])) ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all" title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" download class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-blue-600 transition-all" title="Download">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="3" class="px-8 py-12 text-center text-slate-400 font-bold uppercase tracking-widest text-xs">No timetable uploaded yet.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Circulars Section -->
        <section id="section-circulars" class="dept-section hidden opacity-0 transition-all duration-500">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                    <div class="w-2 h-8 bg-rose-500 rounded-full"></div>
                    Departmental <span class="italic font-serif">Circulars</span>
                </h2>
                <a href="circulars.php?dept=<?= urlencode($dept) ?>" class="text-xs font-black uppercase tracking-widest text-rose-600 hover:text-rose-700">View All <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="overflow-x-auto rounded-[2rem] border border-slate-100 bg-white shadow-xl shadow-slate-200/40">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Circular Title</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Added By</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php
                        $stmt = $conn->prepare("SELECT fc.title, fc.file_path, fc.created_at, f.name as faculty_name 
                                              FROM faculty_content fc 
                                              JOIN faculty f ON fc.faculty_id = f.id 
                                              WHERE fc.department = ? AND fc.type = 'circulars' AND fc.status = 'approved' 
                                              ORDER BY fc.created_at DESC LIMIT 10");
                        $stmt->bind_param("s", $dept);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        if ($res->num_rows > 0):
                            while($row = $res->fetch_assoc()):
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-rose-500 group-hover:text-white transition-all">
                                        <i class="fas fa-bullhorn text-sm"></i>
                                    </div>
                                    <span class="text-sm font-bold text-slate-700"><?= htmlspecialchars($row['title']) ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-700 uppercase tracking-tight"><?= htmlspecialchars($row['faculty_name']) ?></span>
                                    <span class="text-[10px] text-slate-400 font-bold"><?= date('M d, Y', strtotime($row['created_at'])) ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all" title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" download class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-rose-600 transition-all" title="Download">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="3" class="px-8 py-12 text-center text-slate-400 font-bold uppercase tracking-widest text-xs">No circulars uploaded yet.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</div>

<style>
    .animate-fade-in-up {
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .active-tab {
        background: #0f172a;
        color: white;
        border: 1px solid #0f172a;
    }
    .inactive-tab {
        background: white;
        color: #64748b;
        border: 1px solid #f1f5f9;
    }
    .inactive-tab:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #e2e8f0;
    }
</style>

<script>
    function switchTab(type) {
        // Update Tab Buttons
        document.querySelectorAll('.dept-tab').forEach(tab => {
            tab.classList.remove('active-tab', 'shadow-xl', 'shadow-slate-900/10');
            tab.classList.add('inactive-tab');
        });
        const activeTab = document.getElementById('tab-' + type);
        activeTab.classList.remove('inactive-tab');
        activeTab.classList.add('active-tab', 'shadow-xl', 'shadow-slate-900/10');

        // Update Sections
        document.querySelectorAll('.dept-section').forEach(section => {
            section.classList.add('hidden', 'opacity-0');
            section.classList.remove('visible');
        });
        const activeSection = document.getElementById('section-' + type);
        activeSection.classList.remove('hidden');
        setTimeout(() => {
            activeSection.classList.remove('opacity-0');
            activeSection.classList.add('visible');
        }, 10);
    }
</script>

<?php include 'includes/footer.php'; ?>
