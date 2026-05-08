<?php include "includes/header.php"; ?>
<?php include "admin/includes/db.php"; ?>

<div class="bg-white min-h-screen pb-20">
    <!-- Hero -->
    <section class="pt-24 pb-32 bg-slate-50 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-emerald-500/5 blur-[120px] rounded-full translate-x-1/2 -translate-y-1/2"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-600 text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                <i class="fas fa-microscope"></i> Innovation & Discovery
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tighter leading-[0.9] uppercase mb-8">
                Research & <br/>
                <span class="text-emerald-500">Publications</span>
            </h1>
            <p class="text-slate-500 font-medium text-lg leading-relaxed max-w-2xl mb-12">
                Advancing the frontiers of knowledge through interdisciplinary research, faculty publications, and innovative student projects.
            </p>
            
            <!-- Quick Stats -->
            <div class="flex flex-wrap gap-8">
                <div class="flex items-center gap-4">
                    <span class="text-4xl font-black text-slate-900">120+</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-tight">Journal<br/>Papers</span>
                </div>
                <div class="h-10 w-px bg-slate-200"></div>
                <div class="flex items-center gap-4">
                    <span class="text-4xl font-black text-slate-900">15+</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-tight">Ongoing<br/>Projects</span>
                </div>
                <div class="h-10 w-px bg-slate-200"></div>
                <div class="flex items-center gap-4">
                    <span class="text-4xl font-black text-slate-900">08</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-tight">Patents<br/>Filed</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Research Verticals -->
    <section class="py-24 max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-12 bg-white rounded-[4rem] border border-slate-100 shadow-2xl shadow-slate-900/5 group hover:border-emerald-400 transition-all">
                <div class="w-16 h-16 bg-blue-50 rounded-3xl flex items-center justify-center text-blue-500 mb-8 group-hover:scale-110 transition-transform">
                    <i class="fas fa-robot text-2xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-4 tracking-tight uppercase">AI & Data Science</h3>
                <p class="text-sm text-slate-500 font-medium leading-relaxed">Pioneering work in neural networks, predictive analytics, and automated decision systems.</p>
            </div>
            <div class="p-12 bg-white rounded-[4rem] border border-slate-100 shadow-2xl shadow-slate-900/5 group hover:border-emerald-400 transition-all">
                <div class="w-16 h-16 bg-emerald-50 rounded-3xl flex items-center justify-center text-emerald-500 mb-8 group-hover:scale-110 transition-transform">
                    <i class="fas fa-shield-alt text-2xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-4 tracking-tight uppercase">Cyber Security</h3>
                <p class="text-sm text-slate-500 font-medium leading-relaxed">Advanced research in network forensics, ethical hacking, and blockchain security protocols.</p>
            </div>
            <div class="p-12 bg-white rounded-[4rem] border border-slate-100 shadow-2xl shadow-slate-900/5 group hover:border-emerald-400 transition-all">
                <div class="w-16 h-16 bg-amber-50 rounded-3xl flex items-center justify-center text-amber-500 mb-8 group-hover:scale-110 transition-transform">
                    <i class="fas fa-atom text-2xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-4 tracking-tight uppercase">Modern IT</h3>
                <p class="text-sm text-slate-500 font-medium leading-relaxed">Exploring cloud computing, edge networks, and full-stack software architectures.</p>
            </div>
        </div>
    </section>

    <!-- Publications List -->
    <section class="py-24 bg-slate-900 mx-6 rounded-[5rem] overflow-hidden relative">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 50px 50px;"></div>
        <div class="max-w-7xl mx-auto px-10 relative z-10">
            <h2 class="text-4xl font-black text-white mb-16 tracking-tight uppercase">Recent Faculty Publications</h2>
            <div class="space-y-4">
                <?php 
                $pub_query = "SELECT * FROM naac_docs WHERE category = 'Policy' ORDER BY created_at DESC"; // Temporary mapping or create research table
                // For now showing a clean static list with high-end UI
                $sample_pubs = [
                    ['title' => 'Deep Learning in Precision Agriculture', 'author' => 'Dr. A. B. Joshi', 'journal' => 'International Journal of IT', 'year' => '2024'],
                    ['title' => 'Cloud Security Protocols for Small Businesses', 'author' => 'Prof. S. R. Patil', 'journal' => 'Journal of Cyber Security', 'year' => '2023'],
                    ['title' => 'Edge Computing in Smart Cities', 'author' => 'Dr. V. K. Sharma', 'journal' => 'IT Professional India', 'year' => '2023']
                ];
                foreach($sample_pubs as $pub):
                ?>
                <div class="p-8 bg-white/5 border border-white/10 rounded-[3rem] flex flex-col md:flex-row md:items-center justify-between gap-8 hover:bg-white/10 transition-all group">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase rounded-lg"><?= $pub['journal'] ?></span>
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest"><?= $pub['year'] ?></span>
                        </div>
                        <h4 class="text-xl font-bold text-white tracking-tight"><?= $pub['title'] ?></h4>
                        <p class="text-sm text-slate-400 mt-1 font-medium">By <?= $pub['author'] ?></p>
                    </div>
                    <button class="w-14 h-14 bg-white/5 text-white rounded-2xl flex items-center justify-center hover:bg-emerald-500 transition-all">
                        <i class="fas fa-external-link-alt"></i>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>

<?php include "includes/footer.php"; ?>
