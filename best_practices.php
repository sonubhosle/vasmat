<?php include "includes/header.php"; ?>
<?php include "admin/includes/db.php"; ?>

<div class="bg-white min-h-screen pb-20">
    <!-- Hero -->
    <section class="relative pt-24 pb-32 overflow-hidden bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                <i class="fas fa-award"></i> Institutional Excellence
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tighter leading-[0.9] uppercase mb-8">
                Best <span class="text-blue-600">Practices</span>
            </h1>
            <p class="text-slate-500 font-medium text-lg max-w-2xl mx-auto leading-relaxed mb-12">
                Documenting our unique initiatives and institutional distinctiveness that define our commitment to quality education and social responsibility.
            </p>
        </div>
    </section>

    <!-- Best Practice 1 -->
    <section class="py-24 max-w-7xl mx-auto px-6 border-b border-slate-50">
        <div class="flex flex-col lg:flex-row items-center gap-20">
            <div class="lg:w-1/2">
                <div class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-4">Practice 01</div>
                <h2 class="text-4xl font-black text-slate-900 uppercase tracking-tight mb-8">Empowerment Through ICT</h2>
                <div class="space-y-6 text-slate-500 font-medium leading-relaxed">
                    <p><strong>The Context:</strong> Bridging the digital divide in rural education by integrating advanced ICT tools in everyday learning.</p>
                    <p><strong>The Objectives:</strong> To make students industry-ready and proficient in digital communication and problem-solving.</p>
                    <p><strong>The Practice:</strong> Smart classrooms, free Wi-Fi zones, and mandatory digital literacy modules for all first-year students.</p>
                </div>
            </div>
            <div class="lg:w-1/2">
                <div class="aspect-video bg-slate-100 rounded-[3rem] overflow-hidden shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=1000" alt="ICT Practice" class="w-full h-full object-cover" />
                </div>
            </div>
        </div>
    </section>

    <!-- Institutional Distinctiveness -->
    <section class="py-32 bg-slate-900 mx-6 rounded-[5rem] mt-24">
        <div class="max-w-4xl mx-auto px-10 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-[0.2em] mb-8">
                <i class="fas fa-star"></i> Criterion 7.3
            </div>
            <h2 class="text-5xl font-black text-white uppercase tracking-tighter mb-10 leading-none">Institutional <br/> <span class="text-blue-500">Distinctiveness</span></h2>
            <div class="bg-white/5 border border-white/10 p-12 rounded-[3.5rem] backdrop-blur-xl">
                <h4 class="text-xl font-bold text-white mb-6">"Rural Excellence, Global Vision"</h4>
                <p class="text-slate-400 font-medium leading-relaxed text-lg">
                    Our institution stands out for its unique blend of traditional values and modern technological infrastructure. Situated in a semi-urban area, we serve as the primary gateway for rural students to access global-standard education and career opportunities, particularly in the fields of Computer Science and Information Technology.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12 pt-12 border-t border-white/10">
                    <div>
                        <div class="text-3xl font-black text-white mb-2">80%</div>
                        <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Rural Student Base</div>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-white mb-2">90%</div>
                        <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Digital Literacy</div>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-white mb-2">15+</div>
                        <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Skill Courses</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>
