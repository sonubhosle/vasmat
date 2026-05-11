<?php include "includes/header.php"; ?>
<?php require_once __DIR__ . '/includes/auth_helper.php'; ?>

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
        <?php
        // Safe query: table may not exist on some installations. Catch errors and fall back.
        $latest_cal = null;
        try {
            $res = $conn->query("SELECT * FROM academic_calendars WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1");
            if ($res && $res !== false) {
                $latest_cal = $res->fetch_assoc();
            }
        } catch (Throwable $e) {
            $latest_cal = null;
        }

        if ($latest_cal):
        ?>
        <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-900/5 border border-slate-100 overflow-hidden mb-12">
            <div class="p-12 border-b border-slate-50 flex flex-col md:flex-row justify-between items-center bg-slate-50/50 gap-6">
                <div>
                    <span class="text-[9px] font-black text-primary-600 uppercase tracking-[0.3em] mb-1 block">Current Schedule</span>
                    <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight"><?= e($latest_cal['title']) ?> (<?= e($latest_cal['academic_year']) ?>)</h3>
                </div>
                <a href="<?= e($latest_cal['file_path']) ?>" target="_blank" class="px-8 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-900/10 flex items-center gap-3">
                    <i class="fas fa-download"></i> Download PDF
                </a>
            </div>
            

        </div>
        <?php else: ?>
        <div class="bg-white rounded-[3.5rem] p-24 text-center border border-slate-100 shadow-xl">
            <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-200">
                <i class="fas fa-calendar-xmark text-4xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-2">No Calendar Available</h3>
            <p class="text-slate-400 text-sm font-medium">The academic calendar for the current year has not been uploaded yet.</p>
        </div>
        <?php endif; ?>

        <!-- All Calendars (raw data preview) -->
        <?php
        $allCalendars = [];
        try {
            $allRes = $conn->query("SELECT * FROM academic_calendars ORDER BY created_at DESC");
            if ($allRes && $allRes !== false) {
                $allCalendars = $allRes->fetch_all(MYSQLI_ASSOC);
            }
        } catch (Throwable $e) {
            $allCalendars = [];
        }

        if (!empty($allCalendars)):
        ?>
        <div class="mt-12 max-w-7xl mx-auto px-6">
            <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-4">All Calendars (Database)</h4>
            <div class="overflow-x-auto bg-white rounded-2xl border border-slate-100 shadow-sm">
                <table class="w-full text-sm table-auto">
                    <thead>
                        <tr class="text-left text-xs text-slate-500 uppercase bg-slate-50">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Academic Year</th>
                            <th class="px-4 py-3">File</th>
                            <th class="px-4 py-3">Active</th>
                            <th class="px-4 py-3">Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allCalendars as $row): ?>
                        <tr class="border-t border-slate-50 hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-600"><?= e($row['id']) ?></td>
                            <td class="px-4 py-3 font-bold text-slate-800"><?= e($row['title']) ?></td>
                            <td class="px-4 py-3 text-slate-600"><?= e($row['academic_year']) ?></td>
                            <td class="px-4 py-3 text-slate-600">
                                <?php if (!empty($row['file_path'])): ?>
                                    <a href="<?= e($row['file_path']) ?>" target="_blank" class="text-amber-600 hover:underline">Download</a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if ($row['is_active']): ?>
                                    <span class="px-2 py-1 text-xs font-black bg-emerald-50 text-emerald-600 rounded-lg">Yes</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-black bg-slate-50 text-slate-500 rounded-lg">No</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-500"><?= e($row['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Archives / Previous Years -->
        <?php
        $archives = [];
        try {
            $archivesRes = $conn->query("SELECT * FROM academic_calendars WHERE is_active = 1 AND id != '" . ($latest_cal['id'] ?? 0) . "' ORDER BY academic_year DESC");
            if ($archivesRes && $archivesRes !== false) {
                $archives = $archivesRes->fetch_all(MYSQLI_ASSOC);
            }
        } catch (Throwable $e) {
            $archives = [];
        }

        if (!empty($archives)):
        ?>
        <div class="mt-20">
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.4em] mb-8 text-center">Previous Academic Years</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($archives as $arch): ?>
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center text-lg group-hover:bg-rose-50 group-hover:text-rose-500 transition-colors">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900"><?= e($arch['academic_year']) ?></p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Archive</p>
                        </div>
                    </div>
                    <a href="<?= e($arch['file_path']) ?>" target="_blank" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all">
                        <i class="fas fa-download text-xs"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </section>
</div>

<?php include "includes/footer.php"; ?>
