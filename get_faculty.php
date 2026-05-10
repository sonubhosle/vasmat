<?php
include __DIR__ . '/admin/includes/db.php';

/* ================= FETCH API ================= */
if (isset($_GET['fetch'])) {
    $type = $_GET['type'] ?? 'teaching';
    
    // Validate type
    $allowedTypes = ['teaching', 'non-teaching'];
    if (!in_array($type, $allowedTypes)) {
        $type = 'teaching';
    }

    $stmt = $conn->prepare("SELECT * FROM faculty WHERE faculty_type=? AND is_active=1 ORDER BY id DESC");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $res = $stmt->get_result();

    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Directory | MIT College</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .tab-active {
            background: #0f172a;
            color: white;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
        }
        .tab-inactive {
            background: white;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

        /* Smooth centered Modal Animation */
        .modal-active {
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        .modal-active .modal-container {
            opacity: 1 !important;
            transform: translate(0, 0) scale(1) !important;
            transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen font-sans">
    <div class="max-w-7xl mx-auto px-6 py-12">
        
        <!-- Header Section -->
        <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="max-w-2xl">
                <span class="text-[10px] font-black uppercase tracking-[0.4em] text-primary-600 mb-2 block">Institutional Directory</span>
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-none">
                    Academic <span class="text-primary-500 italic font-serif">&</span> Professional <span class="text-primary-500">Staff</span>
                </h2>
                <p class="text-slate-500 text-sm font-medium mt-4">Discover the experts leading our departments and driving academic excellence through innovation and mentorship.</p>
            </div>
            
            <!-- Category Tabs -->
            <div class="flex gap-3 bg-white p-2 rounded-[1.8rem] border border-slate-100 shadow-sm self-start md:self-end">
                <button onclick="switchTab('teaching')" id="tab-teaching" class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all tab-active">
                    <i class="fas fa-graduation-cap mr-2"></i> Teaching
                </button>
                <button onclick="switchTab('non-teaching')" id="tab-non-teaching" class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all tab-inactive">
                    <i class="fas fa-user-shield mr-2"></i> Non-Teaching
                </button>
            </div>
        </div>

        <!-- Faculty Table Content -->
        <div class="bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Faculty Profile</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Designation</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Academic Credentials</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-right">Operations</th>
                        </tr>
                    </thead>
                    <tbody id="facultyData" class="divide-y divide-slate-50">
                        <!-- Populated via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[200] flex items-center justify-center p-6 opacity-0 pointer-events-none transition-all duration-500 ease-out">
        <div class="modal-container bg-white w-full max-w-2xl rounded-[3rem] overflow-hidden shadow-2xl max-h-[90vh] flex flex-col transform -translate-y-24 -translate-x-24 scale-90 opacity-0 transition-all duration-700 ease-out">
            <!-- Modal Header -->
            <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-2xl flex items-center justify-center shadow-inner">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Full Profile Details</h3>
                        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Faculty Management System</p>
                    </div>
                </div>
                <button onclick="closeModal()" class="w-10 h-10 rounded-2xl bg-slate-50 text-slate-400 hover:bg-slate-100 transition-all flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-10 overflow-y-auto custom-scrollbar flex-1 bg-white">
                <div id="modalContent" class="space-y-12">
                    <!-- Dynamic content -->
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 bg-slate-50 flex flex-col md:flex-row items-center justify-between gap-4">
                <div id="resumeContainer">
                    <!-- Resume button will appear here -->
                </div>
                <button onclick="closeModal()" class="px-8 py-3 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl">Close Profile</button>
            </div>
        </div>
    </div>

    <script>
    let currentType = 'teaching';

    function switchTab(type) {
        currentType = type;
        // Update UI
        document.querySelectorAll('button[id^="tab-"]').forEach(btn => {
            btn.classList.remove('tab-active');
            btn.classList.add('tab-inactive');
        });
        document.getElementById('tab-' + type).classList.remove('tab-inactive');
        document.getElementById('tab-' + type).classList.add('tab-active');
        
        loadFaculty();
    }

    function loadFaculty() {
        const targetDiv = "#facultyData";
        $(targetDiv).html(`
            <tr><td colspan="4" class="px-8 py-20 text-center"><i class="fas fa-circle-notch fa-spin text-primary-500 text-3xl"></i></td></tr>
        `);

        $.ajax({
            url: "get_faculty.php?fetch=1&type=" + currentType,
            type: "GET",
            dataType: "json",
            success: function(res) {
                let html = "";
                if (res.length == 0) {
                    html = `
                        <tr>
                            <td colspan="4" class="px-8 py-32 text-center">
                                <div class="max-w-xs mx-auto opacity-40">
                                    <i class="fas fa-user-slash text-5xl mb-4"></i>
                                    <h4 class="font-black text-sm uppercase tracking-widest">No Staff Registered</h4>
                                </div>
                            </td>
                        </tr>
                    `;
                } else {
                    res.forEach(f => {
                        html += `
                            <tr class="hover:bg-slate-50/50 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-5">
                                        <div class="w-16 h-16 rounded-[1.5rem] overflow-hidden border-4 border-white shadow-lg shrink-0 group-hover:scale-110 transition-transform">
                                            <img src="upload/faculty/${f.photo}" 
                                                 onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(f.name)}&background=f59e0b&color=fff&size=200'"
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-black text-slate-800 tracking-tight mb-1">${f.name}</h4>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">${currentType} Staff Member</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-4 py-1.5 bg-slate-100 text-slate-600 text-[9px] font-black rounded-full uppercase tracking-widest border border-slate-200">
                                        ${f.designation}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-amber-50 text-amber-500 rounded-lg flex items-center justify-center text-[10px]">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <span class="text-xs font-bold text-slate-600 truncate max-w-[200px]">${f.education || 'Credentials Not Set'}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-3 scale-95 group-hover:scale-100 transition-all">
                                        <button onclick='viewFaculty(${JSON.stringify(f)})' class="px-5 py-2.5 bg-slate-900 text-white text-[9px] font-black rounded-xl uppercase tracking-widest hover:bg-primary-500 transition-all shadow-lg shadow-slate-900/10">
                                            View Profile
                                        </button>
                                        ${f.email ? `
                                            <a href="mailto:${f.email}" class="w-10 h-10 bg-slate-100 text-slate-500 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-all">
                                                <i class="fas fa-envelope text-xs"></i>
                                            </a>
                                        ` : ''}
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
                $(targetDiv).html(html);
            }
        });
    }

    function viewFaculty(f) {
        const modal = document.getElementById('detailsModal');
        const content = document.getElementById('modalContent');
        const resumeContainer = document.getElementById('resumeContainer');
        
        content.innerHTML = `
            <div class="flex flex-col md:flex-row items-center gap-10">
                <div class="w-44 h-44 rounded-[2.5rem] overflow-hidden border-8 border-slate-50 shadow-2xl shrink-0">
                    <img src="upload/faculty/${f.photo}" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(f.name)}&background=f59e0b&color=fff&size=200'" class="w-full h-full object-cover">
                </div>
                <div class="text-center md:text-left">
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight leading-none mb-3">${f.name}</h2>
                    <p class="text-primary-600 font-bold text-lg mb-4">${f.designation}</p>
                    <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                        <span class="px-3 py-1.5 bg-slate-100 text-[9px] font-black text-slate-500 uppercase tracking-widest rounded-lg border border-slate-200">ID: #FAC-${f.id}</span>
                        <span class="px-3 py-1.5 bg-emerald-50 text-[9px] font-black text-emerald-600 uppercase tracking-widest rounded-lg border border-emerald-100">Verified Staff</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-12 gap-x-10">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-4 bg-primary-500 rounded-full"></div>
                        <h5 class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Academic Background</h5>
                    </div>
                    <p class="text-sm font-semibold text-slate-600 leading-relaxed pl-4.5">${f.education || 'Information not provided.'}</p>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-4 bg-blue-500 rounded-full"></div>
                        <h5 class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Professional Experience</h5>
                    </div>
                    <p class="text-sm font-semibold text-slate-600 leading-relaxed pl-4.5">${f.experience || 'Information not provided.'}</p>
                </div>
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-4 bg-slate-900 rounded-full"></div>
                        <h5 class="text-[10px] font-black text-slate-900 uppercase tracking-widest">About / Professional Bio</h5>
                    </div>
                    <p class="text-sm font-semibold text-slate-600 leading-relaxed pl-4.5 italic whitespace-pre-line">${f.about || 'This faculty member has not provided a professional bio yet.'}</p>
                </div>
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-4 bg-amber-500 rounded-full"></div>
                        <h5 class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Achievements & Recognition</h5>
                    </div>
                    <p class="text-sm font-semibold text-slate-600 leading-relaxed pl-4.5">${f.achievements || 'No notable achievements listed.'}</p>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="mailto:${f.email}" class="flex items-center gap-4 p-4 hover:bg-slate-50 rounded-2xl transition-all group border border-transparent hover:border-slate-100">
                    <div class="w-10 h-10 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center transition-all group-hover:bg-primary-500 group-hover:text-white">
                        <i class="fas fa-envelope text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Email Address</p>
                        <p class="text-xs font-black text-slate-900">${f.email || 'N/A'}</p>
                    </div>
                </a>
                <a href="tel:${f.phone}" class="flex items-center gap-4 p-4 hover:bg-slate-50 rounded-2xl transition-all group border border-transparent hover:border-slate-100">
                    <div class="w-10 h-10 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center transition-all group-hover:bg-blue-600 group-hover:text-white">
                        <i class="fas fa-phone text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Phone Number</p>
                        <p class="text-xs font-black text-slate-900">${f.phone || 'N/A'}</p>
                    </div>
                </a>
            </div>
        `;

        // Handle Resume Button
        if (f.resume) {
            resumeContainer.innerHTML = `
                <a href="upload/resumes/${f.resume}" target="_blank" class="flex items-center justify-center gap-3 px-6 py-3 bg-amber-500 text-white rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/20">
                    <i class="fas fa-file-pdf"></i> View Professional Resume
                </a>
            `;
        } else {
            resumeContainer.innerHTML = '';
        }

        modal.classList.add('modal-active');
    }

    function closeModal() {
        const modal = document.getElementById('detailsModal');
        modal.classList.remove('modal-active');
    }

    $(document).ready(function() {
        loadFaculty();
    });

    // Close on outside click
    window.onclick = function(event) {
        if (event.target.id === 'detailsModal') {
            closeModal();
        }
    }

    // Close modal on escape
    document.addEventListener('keydown', (e) => {
        if(e.key === 'Escape') closeModal();
    });
    </script>
</body>
</html>
<?php include __DIR__ . '/includes/footer.php'; ?>
