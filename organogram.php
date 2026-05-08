<?php include "includes/header.php"; ?>
<?php include "admin/includes/db.php"; ?>

<div class="bg-white min-h-screen pb-20">
    <!-- Hero -->
    <section class="relative pt-24 pb-32 overflow-hidden bg-slate-900">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                <i class="fas fa-sitemap"></i> Institutional Governance
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter leading-[0.9] uppercase mb-8">
                Institutional <br/>
                <span class="text-emerald-500">Organogram</span>
            </h1>
            <p class="text-slate-400 max-w-2xl mx-auto font-medium text-lg leading-relaxed mb-12">
                Transparency in leadership and administrative structure. Our hierarchical framework ensures efficient academic and administrative functioning.
            </p>
        </div>
    </section>

    <!-- Organogram Diagram Placeholder -->
    <section class="py-24 max-w-7xl mx-auto px-6">
        <div class="bg-slate-50 rounded-[4rem] p-12 md:p-20 border border-slate-100 text-center">
            <h2 class="text-3xl font-black text-slate-900 mb-16 tracking-tight uppercase">Administrative Structure</h2>
            
            <!-- Simplified Visual Organogram -->
            <div class="flex flex-col items-center gap-12">
                <div class="px-10 py-6 bg-slate-900 text-white rounded-2xl shadow-xl font-black uppercase tracking-widest">Management Council</div>
                <div class="w-1 h-12 bg-slate-200"></div>
                <div class="px-10 py-6 bg-emerald-600 text-white rounded-2xl shadow-xl font-black uppercase tracking-widest">Principal</div>
                <div class="w-1 h-12 bg-slate-200"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-20 w-full max-w-4xl">
                    <div class="flex flex-col items-center">
                        <div class="px-8 py-5 bg-white border-2 border-emerald-100 text-slate-900 rounded-2xl shadow-lg font-black uppercase tracking-widest mb-8">IQAC Coordinator</div>
                        <div class="space-y-4 text-left w-full bg-white p-8 rounded-3xl border border-slate-100">
                            <h4 class="text-[10px] font-black text-emerald-600 uppercase tracking-widest border-b border-slate-50 pb-2 mb-4">Academic Wing</h4>
                            <ul class="text-sm font-bold text-slate-600 space-y-3">
                                <li><i class="fas fa-chevron-right text-[10px] mr-2 text-emerald-400"></i> HODs of Departments</li>
                                <li><i class="fas fa-chevron-right text-[10px] mr-2 text-emerald-400"></i> Teaching Staff</li>
                                <li><i class="fas fa-chevron-right text-[10px] mr-2 text-emerald-400"></i> Librarian</li>
                                <li><i class="fas fa-chevron-right text-[10px] mr-2 text-emerald-400"></i> Physical Director</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-center">
                        <div class="px-8 py-5 bg-white border-2 border-blue-100 text-slate-900 rounded-2xl shadow-lg font-black uppercase tracking-widest mb-8">Office Registrar</div>
                        <div class="space-y-4 text-left w-full bg-white p-8 rounded-3xl border border-slate-100">
                            <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-widest border-b border-slate-50 pb-2 mb-4">Administrative Wing</h4>
                            <ul class="text-sm font-bold text-slate-600 space-y-3">
                                <li><i class="fas fa-chevron-right text-[10px] mr-2 text-blue-400"></i> Superintendent</li>
                                <li><i class="fas fa-chevron-right text-[10px] mr-2 text-blue-400"></i> Senior Clerks</li>
                                <li><i class="fas fa-chevron-right text-[10px] mr-2 text-blue-400"></i> Junior Clerks</li>
                                <li><i class="fas fa-chevron-right text-[10px] mr-2 text-blue-400"></i> Support Staff</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>
