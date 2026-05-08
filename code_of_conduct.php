<?php include "includes/header.php"; ?>
<?php include "admin/includes/db.php"; ?>

<div class="bg-slate-50 min-h-screen pb-20">
    <!-- Hero -->
    <section class="relative pt-24 pb-32 overflow-hidden bg-white">
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                <i class="fas fa-gavel"></i> Ethics & values
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tighter leading-[0.9] uppercase mb-8">
                Code of <br/>
                <span class="text-slate-400">Conduct</span>
            </h1>
            <p class="text-slate-500 font-medium text-lg leading-relaxed max-w-2xl">
                The institutional code of conduct for students, teachers, administrators, and other staff members as prescribed by the governing body.
            </p>
        </div>
    </section>

    <!-- Conduct Sections -->
    <section class="py-24 max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Student Conduct -->
            <div class="bg-white p-12 rounded-[4rem] shadow-2xl shadow-slate-900/5 border border-slate-100">
                <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-3xl flex items-center justify-center mb-8">
                    <i class="fas fa-user-graduate text-2xl"></i>
                </div>
                <h3 class="text-3xl font-black text-slate-900 uppercase mb-6 tracking-tight">For Students</h3>
                <ul class="space-y-6">
                    <li class="flex gap-4">
                        <span class="w-6 h-6 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center shrink-0 text-[10px] font-black mt-1">01</span>
                        <p class="text-sm font-bold text-slate-600">Students must maintain 75% attendance in each semester to appear for university examinations.</p>
                    </li>
                    <li class="flex gap-4">
                        <span class="w-6 h-6 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center shrink-0 text-[10px] font-black mt-1">02</span>
                        <p class="text-sm font-bold text-slate-600">Wearing the institutional identity card is mandatory within the campus premises.</p>
                    </li>
                    <li class="flex gap-4">
                        <span class="w-6 h-6 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center shrink-0 text-[10px] font-black mt-1">03</span>
                        <p class="text-sm font-bold text-slate-600">Ragging is strictly prohibited and is a punishable offense under the law.</p>
                    </li>
                </ul>
            </div>

            <!-- Staff Conduct -->
            <div class="bg-white p-12 rounded-[4rem] shadow-2xl shadow-slate-900/5 border border-slate-100">
                <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-3xl flex items-center justify-center mb-8">
                    <i class="fas fa-chalkboard-teacher text-2xl"></i>
                </div>
                <h3 class="text-3xl font-black text-slate-900 uppercase mb-6 tracking-tight">For Teachers</h3>
                <ul class="space-y-6">
                    <li class="flex gap-4">
                        <span class="w-6 h-6 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center shrink-0 text-[10px] font-black mt-1">01</span>
                        <p class="text-sm font-bold text-slate-600">Faculty members must adhere strictly to the academic calendar and teaching plans.</p>
                    </li>
                    <li class="flex gap-4">
                        <span class="w-6 h-6 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center shrink-0 text-[10px] font-black mt-1">02</span>
                        <p class="text-sm font-bold text-slate-600">Professional development through research and publications is highly encouraged.</p>
                    </li>
                    <li class="flex gap-4">
                        <span class="w-6 h-6 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center shrink-0 text-[10px] font-black mt-1">03</span>
                        <p class="text-sm font-bold text-slate-600">Teachers must maintain the highest standards of professional ethics and confidentiality.</p>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="mt-12 text-center">
            <a href="uploads/code_of_conduct.pdf" target="_blank" class="inline-flex items-center gap-3 px-10 py-5 bg-slate-900 text-white rounded-3xl text-[11px] font-black uppercase tracking-widest hover:bg-amber-500 transition-all shadow-xl">
                Download Full Handbook <i class="fas fa-file-pdf"></i>
            </a>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>
