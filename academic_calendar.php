<?php include "includes/header.php"; ?>
<?php include "admin/includes/db.php"; ?>

<div class="bg-white min-h-screen pb-20">
    <!-- Hero -->
    <section class="relative pt-24 pb-32 overflow-hidden bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                <i class="fas fa-calendar-alt"></i> Academic Planning
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tighter leading-[0.9] uppercase mb-8">
                Academic <br/>
                <span class="text-blue-600">Calendar</span>
            </h1>
            <p class="text-slate-500 font-medium text-lg max-w-2xl mx-auto leading-relaxed mb-12">
                Stay organized with our comprehensive schedule of academic events, examinations, holidays, and institutional celebrations.
            </p>
        </div>
    </section>

    <!-- Calendar View -->
    <section class="py-24 max-w-5xl mx-auto px-6">
        <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-900/5 border border-slate-100 overflow-hidden">
            <div class="p-12 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Academic Year 2023-24</h3>
                <a href="uploads/academic_calendar_23_24.pdf" target="_blank" class="px-6 py-3 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all">Download PDF</a>
            </div>
            
            <div class="divide-y divide-slate-50">
                <?php 
                $events = [
                    ['date' => 'June 15, 2023', 'event' => 'Commencement of Academic Session', 'type' => 'Academic'],
                    ['date' => 'August 15, 2023', 'event' => 'Independence Day Celebration', 'type' => 'Holiday'],
                    ['date' => 'September 5, 2023', 'event' => 'Teachers Day Celebration', 'type' => 'Event'],
                    ['date' => 'October 10-20, 2023', 'event' => 'First Internal Assessment', 'type' => 'Exam'],
                    ['date' => 'November 1-15, 2023', 'event' => 'Diwali Vacation', 'type' => 'Holiday'],
                    ['date' => 'December 20, 2023', 'event' => 'Annual Sports Meet', 'type' => 'Event'],
                    ['date' => 'January 15-30, 2024', 'event' => 'Semester End Examinations', 'type' => 'Exam']
                ];
                foreach($events as $e):
                    $typeColor = match($e['type']) {
                        'Holiday' => 'bg-rose-50 text-rose-500 border-rose-100',
                        'Exam' => 'bg-amber-50 text-amber-600 border-amber-100',
                        'Academic' => 'bg-blue-50 text-blue-600 border-blue-100',
                        default => 'bg-emerald-50 text-emerald-600 border-emerald-100'
                    };
                ?>
                <div class="p-8 flex flex-col md:flex-row md:items-center gap-6 hover:bg-slate-50/50 transition-colors">
                    <div class="md:w-48 shrink-0">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Date</div>
                        <div class="font-bold text-slate-900"><?= $e['date'] ?></div>
                    </div>
                    <div class="flex-1">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Description</div>
                        <div class="font-bold text-slate-700"><?= $e['event'] ?></div>
                    </div>
                    <div class="shrink-0">
                        <span class="px-3 py-1 rounded-lg border text-[9px] font-black uppercase tracking-widest <?= $typeColor ?>">
                            <?= $e['type'] ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>
