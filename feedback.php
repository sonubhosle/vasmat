<?php include "includes/header.php"; ?>
<?php include "admin/includes/db.php"; ?>

<div class="bg-slate-50 min-h-screen ">
    <!-- Header -->
    <section class="relative pt-10 pb-10 overflow-hidden ">
        <div class=" px-6 relative z-10 ">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-[0.2em] mb-3">
                <i class="fas fa-comment-dots"></i> Quality Improvement
            </div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Stakeholder <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">Feedback</span></h2>
            <p class="text-slate-500 max-w-2xl mt-2 ">
                Your feedback is crucial for our continuous quality enhancement. All responses are analyzed for institutional growth.
            </p>
        </div>
    </section>

    <div class="w-full-mt-12 relative z-20">
        <div class="bg-white  border border-slate-100 overflow-hidden  w-full ">
            <div class="px-6 py-10">
                <form id="feedbackForm" class="space-y-8 pb-24 relative">
                    <!-- Dropdown + Category Row -->
                    <div class="flex flex-col md:flex-row md:items-start md:gap-6">
                        <!-- Stakeholder Selection (custom dropdown, compact) -->
                        <div class="w-full md:w-1/3">
                            <div class="space-y-2">
                                    <div class="relative">
                                    <button type="button" id="userTypeBtn" aria-haspopup="listbox" aria-expanded="false" class="w-full flex items-center justify-between px-4 h-12 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700">
                                        <span id="userTypeLabel">Student</span>
                                        <i class="fas fa-chevron-down text-slate-400"></i>
                                    </button>

                                    <ul id="userTypeList" role="listbox" tabindex="-1" class="absolute z-30 mt-2 w-full bg-white border border-slate-100 rounded-2xl shadow-lg overflow-hidden transition-all duration-200 transform origin-top opacity-0 scale-95 pointer-events-none invisible">
                                        <li role="option" class="px-4 py-3 hover:bg-blue-50 cursor-pointer text-sm font-semibold" data-value="Student">Student</li>
                                        <li role="option" class="px-4 py-3 hover:bg-blue-50 cursor-pointer text-sm font-semibold" data-value="Parent">Parent</li>
                                        <li role="option" class="px-4 py-3 hover:bg-blue-50 cursor-pointer text-sm font-semibold" data-value="Alumni">Alumni</li>
                                        <li role="option" class="px-4 py-3 hover:bg-blue-50 cursor-pointer text-sm font-semibold" data-value="Employer">Employer</li>
                                    </ul>

                                    <input type="hidden" name="user_type" id="user_type_input" value="Student" required>
                                </div>
                            </div>
                        </div>

                        <!-- Category Selection (compact tabs) -->
                        <div class="w-full md:w-2/3 mt-4 md:mt-0">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="category" value="Teaching" checked class="peer sr-only" />
                                    <div class="flex items-center gap-3 px-4 h-12 bg-slate-50 rounded-2xl border border-slate-200 justify-center md:justify-start text-slate-600 peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white transition-colors duration-200 ease-in-out group-hover:border-slate-300">
                                        <i class="fas fa-chalkboard-teacher text-lg"></i>
                                        <span class="text-[9px] font-black uppercase tracking-widest">Teaching</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="category" value="Curriculum" class="peer sr-only" />
                                    <div class="flex items-center gap-3 px-4 h-12 bg-slate-50 rounded-2xl border border-slate-200 justify-center md:justify-start text-slate-600 peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white transition-colors duration-200 ease-in-out group-hover:border-slate-300">
                                        <i class="fas fa-book-open text-lg"></i>
                                        <span class="text-[9px] font-black uppercase tracking-widest">Curriculum</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="category" value="Infrastructure" class="peer sr-only" />
                                    <div class="flex items-center gap-3 px-4 h-12 bg-slate-50 rounded-2xl border border-slate-200 justify-center md:justify-start text-slate-600 peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white transition-colors duration-200 ease-in-out group-hover:border-slate-300">
                                        <i class="fas fa-building text-lg"></i>
                                        <span class="text-[9px] font-black uppercase tracking-widest">Infrastructure</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Rating -->
                   
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                     <div>
                         <h4 class="text-[10px] mb-2 ml-2 font-black uppercase text-slate-600 tracking-widest">Overall Rating</h4>
                        <div class="space-y-4 px-6 py-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <div class="flex  gap-4">
                            <?php for($i=1; $i<=5; $i++): ?>
                            <button type="button" onclick="setRating(<?= $i ?>)" class="rating-star w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-500 hover:text-amber-400 hover:scale-110 transition-all shadow-sm" id="star-<?= $i ?>">
                                <i class="fas fa-star text-xl"></i>
                            </button>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="0" required />
                    </div>
                   </div>

                    <!-- Comments -->
                    <div class="">
                        <label class="text-[10px]  font-black uppercase text-slate-600 tracking-widest ml-2">Detailed Comments</label>
                        <textarea name="comments" rows="3" placeholder="Share your experience and suggestions..." required class="w-full  px-4 py-2 bg-slate-50 border border-slate-200 rounded-2xl focus:border-amber-500 focus:ring-4 focus:ring-amber-500/20 outline-none text-sm font-bold resize-none transition-all ease-in-out duration-200"></textarea>
                    </div>

                    </div>
                
                    <!-- Options -->
                    <div class="ml-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="anonymous" id="anon" class="sr-only" />
                            <div class="toggle-track w-12 h-6 bg-slate-200 rounded-full relative transition-colors duration-200 ease-in-out">
                                <span class="toggle-knob absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-200 ease-in-out"></span>
                            </div>
                            <span class="ml-3 text-sm font-bold text-slate-500 toggle-label">Submit anonymously</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full md:w-auto md:h-10 md:px-5 md:py-2 py-4 bg-slate-900 text-white rounded-full text-xs font-black uppercase tracking-widest shadow-2xl hover:bg-blue-600 transition-all md:absolute md:bottom-6 md:right-6">
                        <span class="hidden md:inline">Submit Feedback</span>
                        <span class="md:hidden">Submit</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
/* Toggle switch CSS handled here to ensure smooth translate and color transitions */
(function(){
    const style = document.createElement('style');
    style.innerHTML = `
        #anon:checked + .toggle-track { background-color: #0f172a; }
        #anon + .toggle-track { background-color: #e2e8f0; }
        #anon + .toggle-track .toggle-knob { transform: translateX(0); }
        #anon:checked + .toggle-track .toggle-knob { transform: translateX(1.5rem); }
        #anon:checked ~ .toggle-label { color: #0f172a; }
    `;
    document.head.appendChild(style);
})();
// Custom dropdown behavior for Stakeholder selection
(function(){
    const btn = document.getElementById('userTypeBtn');
    const list = document.getElementById('userTypeList');
    const label = document.getElementById('userTypeLabel');
    const input = document.getElementById('user_type_input');

    if(!btn || !list || !label || !input) return;

    function closeList(){
        list.classList.remove('opacity-100','scale-100','pointer-events-auto','visible');
        list.classList.add('opacity-0','scale-95','pointer-events-none','invisible');
        btn.setAttribute('aria-expanded','false');
    }

    function openList(){
        list.classList.remove('opacity-0','scale-95','pointer-events-none','invisible');
        list.classList.add('opacity-100','scale-100','pointer-events-auto','visible');
        btn.setAttribute('aria-expanded','true');
    }

    btn.addEventListener('click', function(e){
        e.preventDefault();
        if(list.classList.contains('invisible')) openList(); else closeList();
    });

    list.querySelectorAll('li').forEach(function(li){
        li.addEventListener('click', function(){
            const v = this.getAttribute('data-value');
            label.textContent = v;
            input.value = v;
            closeList();
        });
        li.addEventListener('keydown', function(ev){
            if(ev.key === 'Enter' || ev.key === ' '){ ev.preventDefault(); this.click(); }
        });
    });

    document.addEventListener('click', function(e){
        if(!e.target.closest('#userTypeBtn') && !e.target.closest('#userTypeList')) closeList();
    });

    document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeList(); });

})();

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
        showToast('Please select a rating before submitting.', 'error');
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
            showToast(data.message, 'success');
            this.reset();
            setRating(0);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Submit Feedback';
    });
});
</script>

<?php include "includes/footer.php"; ?>
