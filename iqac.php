<?php include "includes/header.php"; ?>
<?php include "admin/includes/db.php"; ?>

<div class="bg-white min-h-screen pb-20">
    <!-- Hero Section -->
    <section class="relative pt-24 pb-32 overflow-hidden bg-slate-50">
        <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                <i class="fas fa-microchip"></i> Quality Assurance
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tighter leading-[0.9] uppercase mb-8">
                Internal Quality <br/>
                <span class="text-blue-600">Assurance Cell</span>
            </h1>
            <p class="text-slate-500 font-medium text-lg leading-relaxed max-w-2xl mb-12">
                Driving excellence through systematic evaluation, quality enhancement, and sustainable academic growth.
            </p>
            
            <div class="flex flex-wrap gap-6">
                <a href="#minutes" class="px-8 py-4 bg-slate-900 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl hover:bg-blue-600 transition-all">Meeting Minutes</a>
                <a href="#members" class="px-8 py-4 bg-white border border-slate-200 text-slate-700 rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-lg hover:border-blue-500 transition-all">IQAC Members</a>
            </div>
        </div>
    </section>

    <!-- Vision & Mission -->
    <section class="py-24 max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <div class="p-12 bg-white rounded-[4rem] border border-slate-100 shadow-2xl shadow-slate-900/5 relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                <h3 class="text-2xl font-black text-slate-900 mb-6 uppercase tracking-tight">Our Vision</h3>
                <p class="text-slate-500 font-medium leading-relaxed">
                    To make quality the defining element of higher education of the institution through combination of self and external quality evaluation, promotion and sustenance initiatives.
                </p>
            </div>
            <div class="p-12 bg-blue-600 rounded-[4rem] text-white shadow-2xl shadow-blue-500/20 relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                <h3 class="text-2xl font-black mb-6 uppercase tracking-tight">Our Mission</h3>
                <p class="text-blue-50 font-medium leading-relaxed">
                    To stimulate the academic environment for promotion of quality of teaching-learning and research in higher education.
                </p>
            </div>
        </div>
    </section>

    <!-- Members Table -->
    <section id="members" class="py-24 bg-slate-50 mx-6 rounded-[5rem]">
        <div class="max-w-7xl mx-auto px-10">
            <h2 class="text-4xl font-black text-slate-900 mb-16 tracking-tight uppercase">IQAC Committee Members</h2>
            <div class="bg-white rounded-[3rem] overflow-hidden border border-slate-100 shadow-xl">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-900 text-white">
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest">Name</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest">Designation</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest">Role in IQAC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php 
                        // Assuming committee_id 1 is IQAC based on seed
                        $iqac_members = $conn->query("SELECT * FROM committee_members WHERE committee_id = 6"); // Let's check or use a dynamic filter
                        if ($iqac_members && $iqac_members->num_rows > 0):
                            while($m = $iqac_members->fetch_assoc()):
                        ?>
                        <tr>
                            <td class="px-8 py-6 font-bold text-slate-700"><?= $m['name'] ?></td>
                            <td class="px-8 py-6 text-sm text-slate-500"><?= $m['designation'] ?></td>
                            <td class="px-8 py-6"><span class="px-4 py-1.5 bg-blue-50 text-blue-600 text-[10px] font-black uppercase rounded-lg"><?= $m['role'] ?></span></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="3" class="px-8 py-10 text-center text-slate-400 font-bold">Member list is being updated.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Meeting Minutes & Documents -->
    <section id="minutes" class="py-24 max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2">
                <h2 class="text-3xl font-black text-slate-900 mb-12 tracking-tight uppercase">Recent Meeting Minutes</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php 
                    $docs = $conn->query("SELECT * FROM naac_docs WHERE category = 'Minutes' ORDER BY year DESC");
                    if ($docs && $docs->num_rows > 0):
                        while($d = $docs->fetch_assoc()):
                    ?>
                    <a href="<?= $d['file_path'] ?>" class="flex items-center justify-between p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:border-blue-400 transition-all group">
                        <div class="flex items-center gap-4">
                            <i class="far fa-file-pdf text-rose-500 text-xl"></i>
                            <div>
                                <h4 class="font-bold text-slate-700"><?= $d['title'] ?></h4>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Year <?= $d['year'] ?></p>
                            </div>
                        </div>
                        <i class="fas fa-download text-slate-300 group-hover:text-blue-500"></i>
                    </a>
                    <?php endwhile; else: ?>
                    <p class="col-span-full py-10 bg-slate-50 rounded-3xl text-center text-slate-400 font-bold italic">Minutes will be uploaded shortly.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="lg:col-span-1 space-y-8">
                <div class="p-8 bg-amber-500 rounded-[3rem] text-white">
                    <h3 class="text-xl font-black mb-6 uppercase tracking-tight">Best Practices</h3>
                    <ul class="space-y-4">
                        <li class="flex gap-3">
                            <div class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center shrink-0"><i class="fas fa-check text-xs"></i></div>
                            <p class="text-sm font-bold">ICT Enabled Teaching-Learning</p>
                        </li>
                        <li class="flex gap-3">
                            <div class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center shrink-0"><i class="fas fa-check text-xs"></i></div>
                            <p class="text-sm font-bold">Community Outreach Programs</p>
                        </li>
                    </ul>
                </div>
                
                <div class="p-8 bg-white rounded-[3rem] border border-slate-100 shadow-xl">
                    <h3 class="text-xl font-black text-slate-900 mb-6 uppercase tracking-tight">Institutional Values</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center"><i class="fas fa-leaf"></i></div>
                            <span class="text-sm font-bold text-slate-700">Eco-Friendly Campus</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center"><i class="fas fa-venus-mars"></i></div>
                            <span class="text-sm font-bold text-slate-700">Gender Equity</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>
