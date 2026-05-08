<?php include "includes/header.php"; ?>
<?php include "admin/includes/db.php"; ?>

<div class="bg-white min-h-screen pb-20">
    <!-- Hero -->
    <section class="relative pt-24 pb-32 overflow-hidden bg-slate-50">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/gplay.png');"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10 flex flex-col lg:flex-row items-center gap-16">
            <div class="lg:w-1/2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                    <i class="fas fa-briefcase"></i> Global Careers
                </div>
                <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tighter leading-[0.9] uppercase mb-8">
                    Placement <br/>
                    <span class="text-blue-600">Cell</span>
                </h1>
                <p class="text-slate-500 font-medium text-lg leading-relaxed mb-10">
                    Empowering students with industry-ready skills and connecting them with top-tier global organizations.
                </p>
                <div class="flex flex-wrap gap-4">
                    <div class="px-8 py-6 bg-white rounded-3xl border border-slate-100 shadow-xl group hover:border-blue-500 transition-all">
                        <div class="text-3xl font-black text-slate-900">92%</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Placements</div>
                    </div>
                    <div class="px-8 py-6 bg-white rounded-3xl border border-slate-100 shadow-xl group hover:border-blue-500 transition-all">
                        <div class="text-3xl font-black text-slate-900">50+</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Recruiters</div>
                    </div>
                    <div class="px-8 py-6 bg-white rounded-3xl border border-slate-100 shadow-xl group hover:border-blue-500 transition-all">
                        <div class="text-3xl font-black text-slate-900">12 LPA</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Highest Pkg</div>
                    </div>
                </div>
            </div>
            <div class="lg:w-1/2 relative">
                <div class="absolute -inset-10 bg-blue-500/10 blur-[120px] rounded-full animate-pulse"></div>
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1000&auto=format&fit=crop" 
                     alt="Placement" class="relative rounded-[4rem] shadow-2xl rotate-2 hover:rotate-0 transition-all duration-700" />
            </div>
        </div>
    </section>

    <!-- Placement Records -->
    <section class="py-24 max-w-7xl mx-auto px-6">
        <h2 class="text-4xl font-black text-slate-900 mb-16 tracking-tight uppercase">Recent Placements</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php
            $p_query = "SELECT * FROM placements ORDER BY year DESC LIMIT 8";
            $p_res = $conn->query($p_query);
            if ($p_res && $p_res->num_rows > 0):
                while($p = $p_res->fetch_assoc()):
            ?>
            <div class="bg-white p-6 rounded-[3rem] border border-slate-100 shadow-xl group hover:border-blue-400 transition-all text-center">
                <div class="w-24 h-24 bg-slate-50 rounded-full mx-auto mb-6 overflow-hidden border-4 border-white shadow-lg">
                    <img src="<?= $p['image_path'] ?: 'https://i.pravatar.cc/150?u='.$p['id'] ?>" alt="Student" class="w-full h-full object-cover" />
                </div>
                <h4 class="font-bold text-slate-900 text-lg"><?= $p['student_name'] ?></h4>
                <p class="text-xs font-black text-blue-500 uppercase tracking-widest mb-4"><?= $p['company'] ?></p>
                <div class="inline-flex px-4 py-2 bg-slate-50 rounded-xl text-[10px] font-black text-slate-500 uppercase tracking-widest">
                    Pkg: <?= $p['package'] ?> LPA
                </div>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <!-- Sample Data if table is empty -->
            <?php for($i=1; $i<=4; $i++): ?>
            <div class="bg-white p-6 rounded-[3rem] border border-slate-100 shadow-xl opacity-50 grayscale">
                <div class="w-24 h-24 bg-slate-100 rounded-full mx-auto mb-6"></div>
                <h4 class="font-bold text-slate-400">Student Name</h4>
                <p class="text-xs font-bold text-slate-300 uppercase">Company Name</p>
            </div>
            <?php endfor; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>

<?php include "includes/footer.php"; ?>
