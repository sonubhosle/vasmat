<?php 

include 'includes/header.php'; 

include __DIR__ . '/includes/db.php';

$totalNotes = $conn->query("SELECT COUNT(*) as count FROM notes")->fetch_assoc()['count'];
$totalEvents = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$totalAnnouncements = $conn->query("SELECT COUNT(*) as count FROM announcements")->fetch_assoc()['count'];

?>




<div  class="grid grid-cols-1 relative z-10 sm:grid-cols-2 lg:grid-cols-3 gap-4 animate-in fade-in slide-in-from-bottom-4 duration-500">

    <div class="group relative overflow-hidden rounded-2xl bg-white p-6  border border-slate-200 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer">
        <div
            class="absolute top-0 right-0 w-24 h-24 -mr-8 -mt-8 rounded-full opacity-10 transition-transform duration-500 group-hover:scale-110 bg-blue-600">
        </div>
        <div class="flex flex-col h-full ">

            <div class="flex gap-4">
                <div
                    class="w-12 h-12 rounded-xl flex items-center justify-center text-white bg-blue-600 shadow-lg shadow-blue-500/20">
                    <i class='bx bx-book-open text-[25px]'></i>
                </div>
                <h3 class="text-[18px] font-bold text-slate-800  gap-2">
                    My Courses
                    <p class="text-xs mt-1 font-medium  text-slate-500">Total</p>
                </h3>
            </div>
            <div class="mt-auto pt-7 flex items-center text-sm font-semibold text-blue-600 group-hover:text-blue-700">
                View All

            </div>
        </div>

    </div>

    <div class="group relative overflow-hidden rounded-2xl bg-white p-6 border border-slate-200 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer">
            <div class="absolute top-0 right-0 w-24 h-24 -mr-8 -mt-8 rounded-full opacity-10 transition-transform duration-500 group-hover:scale-110 bg-violet-500"></div>
            
            <div class="flex flex-col h-full">
                <div class="flex gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-500/20">
                        <i class='bx bx-notepad text-[25px]'></i>
                    </div>
                    
                    <div>
                        <h3 class="text-[18px] font-bold text-slate-800">Notes</h3>
                        <p class="text-xs mt-1 font-medium text-slate-500">Total <?= $totalNotes ?></p>
                    </div>
                </div>
                
                <div class="mt-auto pt-7 flex items-center text-sm font-semibold text-violet-600 group-hover:text-violet-700">
                    <span>View Details</span>
                    <i class='bx bx-chevron-right ml-1 text-lg transition-transform duration-300 group-hover:translate-x-1'></i>
                </div>
            </div>
        </div>
    <div
        class="group relative overflow-hidden rounded-2xl bg-white p-6  border border-slate-200 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer">
        <div
            class="absolute top-0 right-0 w-24 h-24 -mr-8 -mt-8 rounded-full opacity-10 transition-transform duration-500 group-hover:scale-110 bg-purple-600">
        </div>
        <div class="flex flex-col h-full ">
            <div class="flex gap-4">
                <div
                    class="w-12 h-12 rounded-xl flex items-center justify-center text-white bg-purple-600 shadow-lg shadow-purple-500/20">
                    <i class='bx bx-image text-[25px]'></i>
                </div>
                <h3 class="text-[18px] font-bold text-slate-800 ">Events
                    <p class="text-xs mt-1 font-medium  text-slate-500">Total <?= $totalEvents ?> </p>

                </h3>

            </div>
            <div class="mt-auto pt-7 flex items-center text-sm font-semibold text-blue-600 group-hover:text-blue-700">
                View All
            </div>
        </div>
    </div>


    <div
        class="group relative overflow-hidden rounded-2xl bg-white p-6  border border-slate-200 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer">
        <div
            class="absolute top-0 right-0 w-24 h-24 -mr-8 -mt-8 rounded-full opacity-10 transition-transform duration-500 group-hover:scale-110 bg-rose-500">
        </div>
        <div class="flex flex-col h-full ">
            <div class="flex gap-4">
                <div
                    class="w-12 h-12 rounded-xl flex items-center justify-center text-white bg-rose-500 shadow-lg shadow-rose-500/20">
                    <i class='bx bx-bell text-[25px]'></i>
                </div>
                <h3 class="text-[18px] font-bold text-slate-800 ">
                    Announcements
                    <p class="text-xs mt-1 font-medium  text-slate-500">Live <?= $totalAnnouncements ?></p>
                </h3>
            </div>

            <div class="mt-auto pt-7 flex items-center text-sm font-semibold text-blue-600 group-hover:text-blue-700">
                View All

            </div>
        </div>
    </div>


    <div
        class="group relative overflow-hidden rounded-2xl bg-white p-6  border border-slate-200 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer">
        <div
            class="absolute top-0 right-0 w-24 h-24 -mr-8 -mt-8 rounded-full opacity-10 transition-transform duration-500 group-hover:scale-110 bg-slate-700">
        </div>
        <div class="flex flex-col h-full ">
            <div class="flex gap-4">
                <div
                    class="w-12 h-12 rounded-xl flex items-center justify-center text-white bg-slate-700 shadow-lg shadow-slate-500/20">
                    <i class='bx bx-error-alt text-[25px]'></i>
                </div>
                <h3 class="text-[18px] font-bold text-slate-800 ">Complaints
                    <p class="text-xs mt-1 font-medium  text-slate-500">Total 6</p>
                </h3>

            </div>

            <div class="mt-auto pt-7 flex items-center text-sm font-semibold text-blue-600 group-hover:text-blue-700">
                View All

            </div>
        </div>
    </div>

    <div
        class="group relative overflow-hidden rounded-2xl bg-white p-6  border border-slate-200 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer">
        <div
            class="absolute top-0 right-0 w-24 h-24 -mr-8 -mt-8 rounded-full opacity-10 transition-transform duration-500 group-hover:scale-110 bg-emerald-600">
        </div>
        <div class="flex flex-col h-full ">
            <div class="flex gap-4">
                <div
                    class="w-12 h-12 rounded-xl flex items-center justify-center text-white bg-emerald-600 shadow-lg shadow-emerald-500/20">
                    <i class='bx bx-list-plus text-[25px]'></i>
                </div>
                <h3 class="text-[18px] font-bold text-slate-800 ">
                    Syllabuses
                    <p class="text-xs mt-1 font-medium  text-slate-500">Uploaded 6</p>
                </h3>

            </div>

            <div class="mt-auto pt-7 flex items-center text-sm font-semibold text-blue-600 group-hover:text-blue-700">
                View All

            </div>
        </div>
    </div>

</div>



   <div class="grid grid-cols-1 md:grid-cols-2 gap-10 pt-8">
        
       
        <div class="space-y-4">
          <h2 class="text-lg font-bold text-slate-800 px-2 flex items-center gap-2">
            <div class="w-2 h-8 bg-blue-600 rounded-full"></div>
            Featured Courses
          </h2>
          <div class="space-y-3">
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex justify-between items-center"><div class="flex flex-col"><span class="font-semibold text-slate-700">CS101: Computer Science</span><span class="text-xs text-slate-500">Duration: 4 Months</span></div> <span class="text-blue-600 font-bold">$299.00</span></div>
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex justify-between items-center"><div class="flex flex-col"><span class="font-semibold text-slate-700">MAT202: Advanced Calculus</span><span class="text-xs text-slate-500">Duration: 3 Months</span></div> <span class="text-blue-600 font-bold">$150.00</span></div>
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex justify-between items-center"><div class="flex flex-col"><span class="font-semibold text-slate-700">PHY105: Classical Mechanics</span><span class="text-xs text-slate-500">Duration: 5 Months</span></div> <span class="text-blue-600 font-bold">$210.00</span></div>
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex justify-between items-center"><div class="flex flex-col"><span class="font-semibold text-slate-700">ENG300: Technical Writing</span><span class="text-xs text-slate-500">Duration: 2 Months</span></div> <span class="text-blue-600 font-bold">$95.00</span></div>
          </div>
        </div>

        
        <div class="space-y-4">
          <h2 class="text-lg font-bold text-slate-800 px-2 flex items-center gap-2">
            <div class="w-2 h-8 bg-rose-500 rounded-full"></div>
            Recent Announcements
          </h2>
          <div class="space-y-3">
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex justify-between items-start"><span class="text-slate-700 font-medium max-w-[70%]">Library opening 24/7 for final exams week.</span> <span class="text-xs bg-rose-50 text-rose-600 px-2 py-1 rounded font-bold uppercase">Feb 24, 2025</span></div>
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex justify-between items-start"><span class="text-slate-700 font-medium max-w-[70%]">Scholarship applications portal is now live.</span> <span class="text-xs bg-rose-50 text-rose-600 px-2 py-1 rounded font-bold uppercase">Feb 22, 2025</span></div>
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex justify-between items-start"><span class="text-slate-700 font-medium max-w-[70%]">Scheduled maintenance in Academic Block B.</span> <span class="text-xs bg-rose-50 text-rose-600 px-2 py-1 rounded font-bold uppercase">Feb 21, 2025</span></div>
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex justify-between items-start"><span class="text-slate-700 font-medium max-w-[70%]">Guest lecture on AI ethics by Dr. Sarah J.</span> <span class="text-xs bg-rose-50 text-rose-600 px-2 py-1 rounded font-bold uppercase">Feb 20, 2025</span></div>
          </div>
        </div>

        
        <div class="space-y-4">
          <h2 class="text-lg font-bold text-slate-800 px-2 flex items-center gap-2">
            <div class="w-2 h-8 bg-slate-700 rounded-full"></div>
            Complaints Status
          </h2>
          <div class="space-y-3">
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex flex-col"><div class="flex justify-between mb-1"><span class="text-xs font-bold text-slate-400 italic">Feb 24, 2025</span> <span class="text-[10px] bg-amber-100 text-amber-700 px-1.5 rounded uppercase font-bold">In Review</span></div><p class="text-slate-700 text-sm">Slow Wi-Fi connectivity in North Dorm Block.</p></div>
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex flex-col"><div class="flex justify-between mb-1"><span class="text-xs font-bold text-slate-400 italic">Feb 22, 2025</span> <span class="text-[10px] bg-green-100 text-green-700 px-1.5 rounded uppercase font-bold">Resolved</span></div><p class="text-slate-700 text-sm">Projector bulb replaced in Classroom 402.</p></div>
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex flex-col"><div class="flex justify-between mb-1"><span class="text-xs font-bold text-slate-400 italic">Feb 20, 2025</span> <span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 rounded uppercase font-bold">Pending</span></div><p class="text-slate-700 text-sm">Dispute regarding library late fine charges.</p></div>
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex flex-col"><div class="flex justify-between mb-1"><span class="text-xs font-bold text-slate-400 italic">Feb 19, 2025</span> <span class="text-[10px] bg-rose-100 text-rose-700 px-1.5 rounded uppercase font-bold text-rose-600">Urgent</span></div><p class="text-slate-700 text-sm">Leakage reported in Chemistry Lab storage room.</p></div>
          </div>
        </div>

        
        <div class="space-y-4">
          <h2 class="text-lg font-bold text-slate-800 px-2 flex items-center gap-2">
            <div class="w-2 h-8 bg-emerald-600 rounded-full"></div>
            Syllabus Repository
          </h2>
          <div class="space-y-3">
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex justify-between items-center"><div class="flex flex-col"><span class="font-bold text-slate-700">Quantum Mechanics II</span><span class="text-xs text-emerald-600">Class: Grad Year 1</span></div> <span class="text-[10px] font-bold text-slate-400">Added: Jan 15</span></div>
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex justify-between items-center"><div class="flex flex-col"><span class="font-bold text-slate-700">Data Structures v3</span><span class="text-xs text-emerald-600">Class: CS Sophomore</span></div> <span class="text-[10px] font-bold text-slate-400">Added: Jan 20</span></div>
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex justify-between items-center"><div class="flex flex-col"><span class="font-bold text-slate-700">Modern World History</span><span class="text-xs text-emerald-600">Class: Arts & Hum</span></div> <span class="text-[10px] font-bold text-slate-400">Added: Jan 22</span></div>
            <div class="p-4 bg-white border border-slate-200 rounded-xl hover:shadow-lg transition-all flex justify-between items-center"><div class="flex flex-col"><span class="font-bold text-slate-700">Linear Algebra ML</span><span class="text-xs text-emerald-600">Class: Data Science</span></div> <span class="text-[10px] font-bold text-slate-400">Added: Jan 25</span></div>
                    </div>
        </div>

      </div>
<?php include 'includes/footer.php'; ?>