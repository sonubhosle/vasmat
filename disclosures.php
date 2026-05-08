<?php include "includes/header.php"; ?>
<?php include "admin/includes/db.php"; ?>

<div class="bg-white min-h-screen pb-20">
    <!-- Header -->
    <section class="relative pt-24 pb-32 overflow-hidden bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                <i class="fas fa-file-contract"></i> Statutory Compliance
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tighter leading-[0.9] uppercase mb-8">
                Mandatory <br/>
                <span class="text-slate-400">Disclosures</span>
            </h1>
            <p class="text-slate-500 font-medium text-lg leading-relaxed max-w-2xl mb-12">
                Public access to institutional approvals, affiliation certificates, and mandatory regulatory filings.
            </p>
        </div>
    </section>

    <!-- Documents Grid -->
    <section class="py-24 max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php 
            $docs = $conn->query("SELECT * FROM naac_docs WHERE category = 'Disclosure' ORDER BY id ASC");
            if ($docs && $docs->num_rows > 0):
                while($d = $docs->fetch_assoc()):
            ?>
            <div class="p-10 bg-white rounded-[3.5rem] border border-slate-100 shadow-2xl shadow-slate-900/5 group hover:border-slate-900 transition-all">
                <div class="w-16 h-16 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-400 mb-8 group-hover:scale-110 transition-transform">
                    <i class="fas fa-file-shield text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2"><?= $d['title'] ?></h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8">Updated: <?= date('M Y', strtotime($d['created_at'])) ?></p>
                <a href="<?= $d['file_path'] ?>" target="_blank" class="inline-flex items-center gap-3 px-8 py-3 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-500 transition-all">
                    Download <i class="fas fa-arrow-down"></i>
                </a>
            </div>
            <?php endwhile; else: ?>
            <p class="col-span-full py-12 text-center bg-slate-50 rounded-3xl text-slate-400 font-bold">Disclosure documents are being verified.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>
