<?php include "includes/header.php"; ?>
<?php include "admin/includes/db.php"; ?>

<div class="bg-white min-h-screen pb-20">
    <!-- Hero -->
    <section class="relative pt-24 pb-32 overflow-hidden bg-slate-900">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                <i class="fas fa-users"></i> Alumni Network
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter leading-[0.9] uppercase mb-8">
                Stay <span class="text-amber-500">Connected</span>
            </h1>
            <p class="text-slate-400 max-w-2xl mx-auto font-medium text-lg leading-relaxed mb-12">
                Join our global community of graduates. Celebrate success, mentor juniors, and stay updated with your alma mater.
            </p>
            <div class="flex flex-wrap justify-center gap-6">
                <a href="#register" class="px-8 py-4 bg-amber-500 text-slate-900 rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl hover:bg-white transition-all">Join Alumni Network</a>
                <a href="#stories" class="px-8 py-4 bg-white/5 border border-white/10 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-lg hover:bg-white/10 transition-all">Success Stories</a>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <div class="max-w-7xl mx-auto px-6 -mt-12 relative z-20">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <?php 
            $alumni_stats = [
                ['label' => 'Total Alumni', 'value' => '2500+', 'icon' => 'fa-users'],
                ['label' => 'Countries', 'value' => '12+', 'icon' => 'fa-globe'],
                ['label' => 'Top MNCs', 'value' => '40+', 'icon' => 'fa-building'],
                ['label' => 'Success Stories', 'value' => '150+', 'icon' => 'fa-quote-left']
            ];
            foreach($alumni_stats as $stat):
            ?>
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl flex items-center gap-4">
                <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-900 shrink-0">
                    <i class="fas <?= $stat['icon'] ?> text-xl"></i>
                </div>
                <div>
                    <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-widest"><?= $stat['label'] ?></h4>
                    <p class="text-sm font-black text-slate-900 tracking-tight"><?= $stat['value'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Alumni Stories -->
    <section id="stories" class="py-24 max-w-7xl mx-auto px-6">
        <h2 class="text-4xl font-black text-slate-900 mb-16 tracking-tight uppercase">Success Stories</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            <?php for($i=1; $i<=3; $i++): ?>
            <div class="group">
                <div class="relative mb-8">
                    <div class="aspect-square bg-slate-100 rounded-[3rem] overflow-hidden grayscale hover:grayscale-0 transition-all duration-700">
                        <img src="https://i.pravatar.cc/400?img=<?= $i+10 ?>" alt="Alumni" class="w-full h-full object-cover" />
                    </div>
                    <div class="absolute -bottom-6 -right-6 w-20 h-20 bg-amber-500 rounded-3xl flex items-center justify-center text-slate-900 shadow-xl group-hover:rotate-12 transition-transform">
                        <i class="fas fa-quote-right text-2xl"></i>
                    </div>
                </div>
                <h4 class="text-2xl font-black text-slate-900 uppercase mb-2">Alumni Name</h4>
                <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-4">Software Engineer at Google</p>
                <p class="text-slate-500 font-medium leading-relaxed italic">
                    "The foundation I received at MIT College helped me navigate the complex world of tech. Grateful for the faculty support."
                </p>
            </div>
            <?php endfor; ?>
        </div>
    </section>

    <!-- Registration Form -->
    <section id="register" class="py-24 bg-slate-50 mx-6 rounded-[5rem]">
        <div class="max-w-3xl mx-auto px-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black text-slate-900 mb-4 tracking-tight uppercase">Registration</h2>
                <p class="text-slate-500 font-medium">Add yourself to our directory to receive event updates and networking opportunities.</p>
            </div>
            <form action="api/alumni_reg.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-12 rounded-[3.5rem] shadow-2xl border border-slate-100">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-2">Full Name</label>
                    <input type="text" name="name" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:border-amber-400 outline-none text-sm font-bold" />
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-2">Email Address</label>
                    <input type="email" name="email" required class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:border-amber-400 outline-none text-sm font-bold" />
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-2">Passing Year</label>
                    <select name="year" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:border-amber-400 outline-none text-sm font-bold">
                        <?php for($y=date('Y'); $y>=2000; $y--) echo "<option>$y</option>"; ?>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-2">Current Company</label>
                    <input type="text" name="company" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:border-amber-400 outline-none text-sm font-bold" />
                </div>
                <div class="md:col-span-2 pt-4">
                    <button type="submit" class="w-full py-5 bg-slate-900 text-white rounded-3xl text-xs font-black uppercase tracking-widest shadow-xl hover:bg-amber-500 transition-all">Submit Registration</button>
                </div>
            </form>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>
