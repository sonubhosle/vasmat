<?php include "includes/header.php"; ?>
<?php include "admin/includes/db.php"; ?>

<div class="bg-slate-50 min-h-screen pb-20">
    <!-- Header -->
    <section class="relative pt-24 pb-28 overflow-hidden bg-slate-900">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#fbbf24 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
            <h1 class="text-5xl md:text-6xl font-black text-white tracking-tighter uppercase mb-4">
                Institutional <span class="text-amber-500">Committees</span>
            </h1>
            <p class="text-slate-400 max-w-2xl mx-auto font-medium">
                Mandatory statutory cells and committees ensuring transparency, safety, and inclusive growth for all institutional stakeholders.
            </p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-6 -mt-10 relative z-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php
            $query = "SELECT * FROM committees";
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0):
                while($row = $result->fetch_assoc()):
                    $c_id = $row['id'];
                    $colorClass = "border-".$row['color']."-400";
                    $iconBg = "bg-".$row['color']."-50";
                    $iconColor = "text-".$row['color']."-500";
                    $accentColor = "text-".$row['color']."-600";
            ?>
            <!-- Dynamic Committee Card -->
            <div class="bg-white p-8 rounded-[3.5rem] shadow-2xl shadow-slate-900/5 border border-slate-100 group hover:<?= $colorClass ?> transition-all duration-500">
                <div class="w-16 h-16 <?= $iconBg ?> rounded-3xl flex items-center justify-center <?= $iconColor ?> mb-8 group-hover:scale-110 transition-transform">
                    <i class="fas <?= $row['icon'] ?> text-2xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-4 uppercase tracking-tight"><?= $row['name'] ?></h3>
                <p class="text-sm text-slate-500 leading-relaxed mb-8 font-medium"><?= $row['description'] ?></p>
                
                <div class="space-y-4 mb-10">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Committee Composition</h4>
                    <?php
                    $m_query = "SELECT * FROM committee_members WHERE committee_id = $c_id ORDER BY id ASC";
                    $m_result = $conn->query($m_query);
                    if ($m_result && $m_result->num_rows > 0):
                        while($m_row = $m_result->fetch_assoc()):
                    ?>
                    <div class="flex items-center justify-between py-2 border-b border-slate-50">
                        <span class="text-sm font-bold text-slate-700"><?= $m_row['name'] ?></span>
                        <span class="text-xs font-black <?= $accentColor ?> uppercase tracking-widest"><?= $m_row['role'] ?></span>
                    </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <p class="text-xs italic text-slate-400">Composition details updating soon...</p>
                    <?php endif; ?>
                </div>
                
                <div class="flex items-center gap-4">
                    <a href="#" class="px-6 py-3 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-amber-500 transition-all">Lodge Complaint</a>
                    <a href="#" class="px-6 py-3 bg-slate-50 text-slate-500 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-slate-100 transition-all">View Minutes</a>
                </div>
            </div>
            <?php 
                endwhile;
            endif; 
            ?>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
