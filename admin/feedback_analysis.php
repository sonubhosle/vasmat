<?php 
include "includes/header.php"; 

// Fetch stats
$total_query = $conn->query("SELECT COUNT(*) as total, AVG(rating) as avg_rating FROM feedback");
$stats = $total_query->fetch_assoc();

$cat_query = $conn->query("SELECT category, COUNT(*) as count, AVG(rating) as avg FROM feedback GROUP BY category");
?>

<main class="p-8">
    <header class="mb-12">
        <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">Feedback <span class="text-blue-500">Analysis</span></h2>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Real-time stakeholder satisfaction monitoring</p>
    </header>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-100">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Responses</div>
            <div class="text-4xl font-black text-slate-900"><?= $stats['total'] ?></div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-100">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Overall Rating</div>
            <div class="text-4xl font-black text-blue-500"><?= number_format($stats['avg_rating'], 1) ?> <span class="text-lg text-slate-300">/ 5.0</span></div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-100">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Analysis Status</div>
            <div class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase rounded-lg inline-block">Active</div>
        </div>
    </div>

    <!-- Feedback Table -->
    <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-900/5 border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
            <h3 class="font-black text-slate-900 uppercase text-xs tracking-widest">Recent Submissions</h3>
            <button onclick="window.print()" class="text-[10px] font-black text-blue-500 uppercase tracking-widest hover:text-blue-700 transition-all">Export Report</button>
        </div>
        <table class="w-full text-left">
            <thead class="bg-slate-50/30">
                <tr>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Stakeholder</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Category</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Rating</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Comments</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php 
                $feedback_res = $conn->query("SELECT * FROM feedback ORDER BY id DESC LIMIT 50");
                if ($feedback_res && $feedback_res->num_rows > 0):
                    while($f = $feedback_res->fetch_assoc()):
                ?>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-6 font-bold text-slate-700 text-sm"><?= $f['user_type'] ?></td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[9px] font-black uppercase rounded-lg"><?= $f['category'] ?></span>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex text-amber-400 text-xs gap-0.5">
                            <?php for($i=1; $i<=5; $i++) echo ($i<=$f['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star text-slate-200"></i>'); ?>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-xs font-bold text-slate-500 max-w-xs truncate" title="<?= $f['comments'] ?>"><?= $f['comments'] ?></td>
                    <td class="px-8 py-6 text-[10px] font-black text-slate-400"><?= date('d M, Y', strtotime($f['created_at'])) ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center text-slate-300">
                        <i class="fas fa-comment-slash text-4xl mb-4 opacity-20"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest">No feedback received yet</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include "includes/footer.php"; ?>
