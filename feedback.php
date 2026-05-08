<?php include "includes/header.php"; ?>
<?php include "admin/includes/db.php"; ?>

<div class="bg-slate-50 min-h-screen pb-20">
    <!-- Header -->
    <section class="relative pt-24 pb-28 overflow-hidden bg-slate-900">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 50px 50px;"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                <i class="fas fa-comment-dots"></i> Quality Improvement
            </div>
            <h1 class="text-5xl md:text-6xl font-black text-white tracking-tighter uppercase mb-4">
                Stakeholder <span class="text-blue-500">Feedback</span>
            </h1>
            <p class="text-slate-400 max-w-2xl mx-auto font-medium">
                Your feedback is crucial for our continuous quality enhancement. All responses are analyzed for institutional growth.
            </p>
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-6 -mt-12 relative z-20">
        <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-900/10 border border-slate-100 overflow-hidden">
            <div class="p-8 md:p-16">
                <form id="feedbackForm" class="space-y-8">
                    <!-- Stakeholder Selection -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-2">I am a...</label>
                        <select name="user_type" required class="w-full px-8 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:border-blue-500 outline-none text-xs font-bold">
                            <option value="Student">Student</option>
                            <option value="Parent">Parent</option>
                            <option value="Alumni">Alumni</option>
                            <option value="Employer">Employer</option>
                        </select>
                    </div>

                    <!-- Category Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="category" value="Teaching" checked class="peer sr-only" />
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all group-hover:border-blue-300">
                                <i class="fas fa-chalkboard-teacher text-2xl text-slate-400 mb-3 peer-checked:text-blue-500"></i>
                                <span class="block text-[10px] font-black uppercase text-slate-500 tracking-widest">Teaching</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="category" value="Curriculum" class="peer sr-only" />
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 text-center peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all group-hover:border-emerald-300">
                                <i class="fas fa-book-open text-2xl text-slate-400 mb-3 peer-checked:text-emerald-500"></i>
                                <span class="block text-[10px] font-black uppercase text-slate-500 tracking-widest">Curriculum</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="category" value="Infrastructure" class="peer sr-only" />
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 text-center peer-checked:border-amber-500 peer-checked:bg-amber-50 transition-all group-hover:border-amber-300">
                                <i class="fas fa-building text-2xl text-slate-400 mb-3 peer-checked:text-amber-500"></i>
                                <span class="block text-[10px] font-black uppercase text-slate-500 tracking-widest">Infrastructure</span>
                            </div>
                        </label>
                    </div>

                    <!-- Rating -->
                    <div class="space-y-4 text-center py-6 bg-slate-50 rounded-[2.5rem]">
                        <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Overall Rating</h4>
                        <div class="flex justify-center gap-4">
                            <?php for($i=1; $i<=5; $i++): ?>
                            <button type="button" onclick="setRating(<?= $i ?>)" class="rating-star w-12 h-12 rounded-2xl bg-white border border-slate-100 text-slate-300 hover:text-amber-400 hover:scale-110 transition-all shadow-sm" id="star-<?= $i ?>">
                                <i class="fas fa-star text-xl"></i>
                            </button>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="0" required />
                    </div>

                    <!-- Comments -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-2">Detailed Comments</label>
                        <textarea name="comments" rows="5" placeholder="Share your experience and suggestions..." required class="w-full px-8 py-6 bg-slate-50 border border-slate-100 rounded-[2.5rem] focus:border-blue-500 outline-none text-sm font-bold resize-none"></textarea>
                    </div>

                    <!-- Options -->
                    <div class="flex items-center gap-3 ml-4">
                        <input type="checkbox" name="anonymous" id="anon" class="w-5 h-5 rounded-lg border-slate-200 accent-blue-600" />
                        <label for="anon" class="text-xs font-bold text-slate-500">Submit anonymously</label>
                    </div>

                    <button type="submit" class="w-full py-6 bg-slate-900 text-white rounded-[2.5rem] text-xs font-black uppercase tracking-widest shadow-2xl hover:bg-blue-600 transition-all transform hover:-translate-y-1">
                        Submit Feedback
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function setRating(val) {
    document.getElementById('ratingInput').value = val;
    document.querySelectorAll('.rating-star').forEach((star, idx) => {
        if(idx < val) {
            star.classList.add('text-amber-400', 'border-amber-400', 'bg-amber-50');
            star.classList.remove('text-slate-300', 'bg-white');
        } else {
            star.classList.remove('text-amber-400', 'border-amber-400', 'bg-amber-50');
            star.classList.add('text-slate-300', 'bg-white');
        }
    });
}

document.getElementById('feedbackForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const rating = document.getElementById('ratingInput').value;
    if(rating == 0) {
        alert('Please select a rating before submitting.');
        return;
    }

    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...';

    fetch('api/save_feedback.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            alert(data.message);
            this.reset();
            setRating(0);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Submit Feedback';
    });
});
</script>

<?php include "includes/footer.php"; ?>
