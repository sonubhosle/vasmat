<?php include "includes/header.php"; ?>
<?php include "admin/includes/db.php"; ?>

<div class="bg-slate-50 min-h-screen pb-20">
    <!-- Header Hero -->
    <section class="relative pt-20 pb-32 overflow-hidden bg-[#1e1b2e]">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#fbbf24 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-black uppercase tracking-[0.3em] mb-8">
                <i class="fas fa-award"></i> Institutional Excellence
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter leading-none mb-6 uppercase">
                NAAC <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">Accreditation</span>
            </h1>
            <p class="text-slate-400 max-w-2xl mx-auto font-medium text-lg leading-relaxed">
                Centralized repository for NAAC accreditation documents, SSR cycles, and quality assurance framework.
            </p>
        </div>
    </section>

    <!-- Accreditation Status Bar -->
    <div class="max-w-7xl mx-auto px-6 -mt-12 relative z-30">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 shrink-0">
                    <i class="fas fa-certificate text-xl"></i>
                </div>
                <div>
                    <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Current Status</h4>
                    <p class="text-sm font-black text-slate-900 tracking-tight">Accredited - Cycle 1</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500 shrink-0">
                    <i class="fas fa-star text-xl"></i>
                </div>
                <div>
                    <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-widest">NAAC Grade</h4>
                    <p class="text-sm font-black text-slate-900 tracking-tight">B++ (CGPA 2.91)</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 shrink-0">
                    <i class="fas fa-calendar-check text-xl"></i>
                </div>
                <div>
                    <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Validity</h4>
                    <p class="text-sm font-black text-slate-900 tracking-tight">Valid until 2028</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Content Section -->
    <div class="max-w-7xl mx-auto px-6 mt-12 relative z-20">
        <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-900/10 border border-slate-100 overflow-hidden">
            <!-- Navigation -->
            <div class="flex flex-wrap items-center justify-center gap-2 p-4 bg-slate-50/50 border-b border-slate-100">
                <button onclick="switchNaacTab('ssr')" id="tab-ssr" class="naac-tab-btn active px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">SSR Portal</button>
                <button onclick="switchNaacTab('criteria')" id="tab-criteria" class="naac-tab-btn px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">Criteria-wise Docs</button>
                <button onclick="switchNaacTab('dvv')" id="tab-dvv" class="naac-tab-btn px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">DVV / SSS</button>
                <button onclick="switchNaacTab('certificates')" id="tab-certificates" class="naac-tab-btn px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">Certificates</button>
            </div>

            <!-- Content Area -->
            <div class="p-8 md:p-16">
                <!-- SSR Content -->
                <div id="content-ssr" class="naac-content-pane block animate-in fade-in duration-500">
                    <h2 class="text-3xl font-black text-slate-900 mb-8 tracking-tight">Self Study Reports (SSR)</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php 
                        $ssr_query = "SELECT * FROM naac_docs WHERE category = 'SSR' AND status = 'Active'";
                        $ssr_res = $conn->query($ssr_query);
                        if ($ssr_res && $ssr_res->num_rows > 0):
                            while($ssr = $ssr_res->fetch_assoc()):
                        ?>
                        <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 flex flex-col items-center text-center group hover:border-amber-400 transition-all">
                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-rose-500 shadow-sm mb-6 group-hover:scale-110 transition-transform">
                                <i class="fas fa-file-pdf text-2xl"></i>
                            </div>
                            <h4 class="font-bold text-slate-900 mb-2"><?= $ssr['title'] ?></h4>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 italic">Academic Year <?= $ssr['year'] ?></p>
                            <a href="<?= $ssr['file_path'] ?>" target="_blank" class="px-8 py-3 bg-white border border-slate-200 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-900 hover:text-white transition-all shadow-sm">Download Report</a>
                        </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <div class="col-span-full py-12 text-center bg-slate-50 rounded-3xl border border-dashed border-slate-200">
                            <i class="fas fa-folder-open text-4xl text-slate-200 mb-4"></i>
                            <p class="text-slate-400 font-bold tracking-tight">SSR documents are currently being archived.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Criteria-wise Content -->
                <div id="content-criteria" class="naac-content-pane hidden animate-in fade-in duration-500">
                    <h2 class="text-3xl font-black text-slate-900 mb-8 tracking-tight">Criteria-wise Documentation</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php for($i=1; $i<=7; $i++): ?>
                        <a href="#" class="flex items-center justify-between p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:border-blue-400 hover:bg-white transition-all group">
                            <div class="flex items-center gap-5">
                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-blue-500 font-black shadow-sm group-hover:bg-blue-500 group-hover:text-white transition-all"><?= $i ?></div>
                                <span class="text-sm font-bold text-slate-700 tracking-tight">Criterion <?= $i ?>: [Criterion Name]</span>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 group-hover:text-blue-500 translate-x-0 group-hover:translate-x-1 transition-all"></i>
                        </a>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- DVV Content -->
                <div id="content-dvv" class="naac-content-pane hidden animate-in fade-in duration-500 text-center py-10">
                    <div class="max-w-xl mx-auto">
                        <i class="fas fa-magnifying-glass-chart text-6xl text-slate-100 mb-8"></i>
                        <h2 class="text-3xl font-black text-slate-900 mb-4 tracking-tight">DVV & Student Satisfaction Survey</h2>
                        <p class="text-slate-500 font-medium mb-10 leading-relaxed">
                            Transparency in data validation and verification process. Access clarifications and latest SSS analysis reports here.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="#" class="px-8 py-4 bg-slate-900 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl hover:shadow-slate-900/20 transition-all">View Clarifications</a>
                            <a href="#" class="px-8 py-4 bg-white border border-slate-200 text-slate-700 rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-lg hover:border-slate-900 transition-all">Latest SSS Report</a>
                        </div>
                    </div>
                </div>

                <!-- Certificates Content -->
                <div id="content-certificates" class="naac-content-pane hidden animate-in fade-in duration-500">
                    <h2 class="text-3xl font-black text-slate-900 mb-8 tracking-tight">Institutional Certificates</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php 
                        $cert_query = "SELECT * FROM naac_docs WHERE category = 'Certificate'";
                        $cert_res = $conn->query($cert_query);
                        if ($cert_res && $cert_res->num_rows > 0):
                            while($cert = $cert_res->fetch_assoc()):
                        ?>
                        <div class="flex items-center gap-6 p-6 bg-slate-50 rounded-3xl border border-slate-100">
                            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-amber-500 shadow-sm"><i class="fas fa-award text-xl"></i></div>
                            <div class="flex-1">
                                <h4 class="font-bold text-slate-900 text-lg tracking-tight"><?= $cert['title'] ?></h4>
                                <a href="<?= $cert['file_path'] ?>" class="text-[10px] font-black text-amber-600 uppercase tracking-widest hover:underline">Download PDF</a>
                            </div>
                        </div>
                        <?php endwhile; endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.naac-tab-btn {
    color: #64748b;
    border: 1px solid transparent;
}
.naac-tab-btn.active {
    background: #1e1b2e;
    color: #fff;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.naac-tab-btn:not(.active):hover {
    background: rgba(245,158,11,0.05);
    color: #f59e0b;
    border-color: rgba(245,158,11,0.1);
}
</style>

<script>
function switchNaacTab(id) {
    document.querySelectorAll('.naac-tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('tab-' + id).classList.add('active');
    document.querySelectorAll('.naac-content-pane').forEach(pane => pane.classList.add('hidden'));
    document.getElementById('content-' + id).classList.remove('hidden');
}
</script>

<style>
.naac-tab-btn {
    color: #64748b;
    border: 1px solid transparent;
}
.naac-tab-btn.active {
    background: #1e1b2e;
    color: #fff;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.naac-tab-btn:not(.active):hover {
    background: rgba(245,158,11,0.05);
    color: #f59e0b;
    border-color: rgba(245,158,11,0.1);
}
</style>

<script>
function switchNaacTab(id) {
    // Buttons
    document.querySelectorAll('.naac-tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('tab-' + id).classList.add('active');
    
    // Content
    document.querySelectorAll('.naac-content-pane').forEach(pane => pane.classList.add('hidden'));
    document.getElementById('content-' + id).classList.remove('hidden');
}
</script>


<?php include "includes/footer.php"; ?>