<?php
include __DIR__ . '/admin/includes/db.php';

/* ================= FETCH API ================= */
if (isset($_GET['fetch'])) {
    $type = $_GET['type'] ?? 'teaching';
    
    // Validate type to prevent injection and filter results
    $allowedTypes = ['teaching', 'non-teaching', 'visiting', 'guest'];
    if (!in_array($type, $allowedTypes)) {
        $type = 'teaching';
    }

    $stmt = $conn->prepare("SELECT * FROM faculty WHERE faculty_type=? ORDER BY id DESC");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $res = $stmt->get_result();

    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit; // IMPORTANT
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
        .table-row-hover:hover {
            background-color: #f8fafc;
        }
  
      
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-indigo-50/30 min-h-screen font-sans">
    <div class=" px-6 py-12 ">
        
        <!-- Header Section -->
            <div class="mb-12 animate-fade-in-up ">

                <h2 class="text-2xl md:text-3xl font-black text-slate-900 mb-6">
                     Our <span class="italic font-serif underline decoration-amber-400/30">Faculty</span>
                </h2>
                <p class="text-slate-500 text-lg max-w-2xl">Meet our dedicated team of academic and administrative professionals committed to excellence.</p>            
        </div>
        <!-- Teaching Staff Section -->
        <section class="mb-20">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-800 flex items-center gap-3">
                        <span class="w-2 h-8 bg-blue-600 rounded-full"></span>
                        Teaching Staff
                    </h2>
                    <p class="text-slate-500 ml-5 font-medium">Academic Experts & Mentors</p>
                </div>
                <div class="inline-flex items-center gap-3 bg-blue-50 border border-blue-100 px-6 py-2.5 rounded-2xl">
                    <i class="fas fa-users text-blue-600"></i>
                    <span class="text-blue-700 font-bold"><span id="teachingCount">0</span> Members</span>
                </div>
            </div>
            
            <div class="glass-card rounded-[2rem] overflow-hidden shadow-2xl shadow-blue-900/5">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-8 py-6 text-xs font-black text-slate-400 uppercase tracking-widest">Faculty Member</th>
                                <th class="px-8 py-6 text-xs font-black text-slate-400 uppercase tracking-widest">Designation</th>
                                <th class="px-8 py-6 text-xs font-black text-slate-400 uppercase tracking-widest">Academic Details</th>
                                <th class="px-8 py-6 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Contact</th>
                            </tr>
                        </thead>
                        <tbody id="teachingData">
                            <!-- Loading skeletons -->
                            <?php for($i=0; $i<3; $i++): ?>
                            <tr class="animate-pulse">
                                <td colspan="4" class="px-8 py-6 border-b border-slate-50">
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-16 bg-slate-200 rounded-2xl"></div>
                                        <div class="space-y-2">
                                            <div class="w-48 h-4 bg-slate-200 rounded-full"></div>
                                            <div class="w-32 h-3 bg-slate-100 rounded-full"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Non-Teaching Staff Section -->
        <section class="mb-20">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-800 flex items-center gap-3">
                        <span class="w-2 h-8 bg-emerald-500 rounded-full"></span>
                        Non-Teaching Staff
                    </h2>
                    <p class="text-slate-500 ml-5 font-medium">Administrative & Support Services</p>
                </div>
                <div class="inline-flex items-center gap-3 bg-emerald-50 border border-emerald-100 px-6 py-2.5 rounded-2xl">
                    <i class="fas fa-hand-holding-heart text-emerald-600"></i>
                    <span class="text-emerald-700 font-bold"><span id="nonTeachingCount">0</span> Members</span>
                </div>
            </div>
            
            <div class="glass-card rounded-[2rem] overflow-hidden shadow-2xl shadow-emerald-900/5">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-8 py-6 text-xs font-black text-slate-400 uppercase tracking-widest">Staff Member</th>
                                <th class="px-8 py-6 text-xs font-black text-slate-400 uppercase tracking-widest">Role</th>
                                <th class="px-8 py-6 text-xs font-black text-slate-400 uppercase tracking-widest">Experience</th>
                                <th class="px-8 py-6 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Contact</th>
                            </tr>
                        </thead>
                        <tbody id="nonTeachingData">
                            <!-- Loading skeletons populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <script>
    function loadFaculty(type, targetDiv, countId) {
        $.ajax({
            url: "get_faculty.php?fetch=1&type=" + type,
            type: "GET",
            dataType: "json",
            success: function(res) {
                $(countId).text(res.length);
                
                let html = "";
                if (res.length == 0) {
                    html = `
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="max-w-xs mx-auto">
                                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-user-slash text-slate-300 text-2xl"></i>
                                    </div>
                                    <h4 class="text-slate-700 font-bold mb-1">No Records Found</h4>
                                    <p class="text-slate-400 text-sm">No ${type} members available at this time.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                } else {
                    res.forEach(f => {
                        const themeColor = type === 'teaching' ? 'blue' : 'emerald';
                        const themeClass = type === 'teaching' ? 'text-blue-600 bg-blue-50' : 'text-emerald-600 bg-emerald-50';
                        
                        html += `
                            <tr class="table-row-hover transition-all duration-300 last:border-0 group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-5">
                                        <div class="w-16 h-16 rounded-2xl overflow-hidden border-2 border-white shadow-lg shadow-slate-200 shrink-0">
                                            <img src="upload/faculty/${f.photo}" 
                                                 onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(f.name)}&background=random&color=fff&bold=true&size=150'"
                                                 class="w-full h-full object-cover faculty-photo">
                                        </div>
                                        <div>
                                            <h4 class="text-[17px] font-black text-slate-800 tracking-tight">${f.name}</h4>
                                            <p class="text-sm text-slate-400 font-medium">Emp ID: #FAC-${f.id}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex px-4 py-1.5 rounded-xl text-xs font-black uppercase tracking-widest ${themeClass} border border-${themeColor}-100">
                                        ${f.designation}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="space-y-2">
                                        ${f.education ? `
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg ${themeClass} flex items-center justify-center shrink-0">
                                                    <i class="fas fa-graduation-cap text-xs"></i>
                                                </div>
                                                <span class="text-sm font-semibold text-slate-600 truncate max-w-[200px]">${f.education}</span>
                                            </div>
                                        ` : ''}
                                        ${f.experience ? `
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                                    <i class="fas fa-history text-xs"></i>
                                                </div>
                                                <span class="text-sm font-semibold text-slate-600">${f.experience} Exp.</span>
                                            </div>
                                        ` : ''}
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-3 transition-all duration-300">
                                        ${f.email ? `
                                            <a href="mailto:${f.email}" class="w-10 h-10 rounded-xl bg-slate-800 text-white flex items-center justify-center hover:bg-blue-600 hover:scale-110 transition-all shadow-lg shadow-slate-200" title="Email ${f.name}">
                                                <i class="fas fa-envelope text-sm"></i>
                                            </a>
                                        ` : ''}
                                        ${f.phone ? `
                                            <a href="tel:${f.phone}" class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 hover:scale-110 transition-all shadow-lg shadow-blue-500/20" title="Call ${f.name}">
                                                <i class="fas fa-phone text-sm"></i>
                                            </a>
                                        ` : ''}
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
                $(targetDiv).html(html);
            },
            error: function() {
                $(targetDiv).html(`
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <div class="bg-red-50 text-red-600 p-6 rounded-3xl border border-red-100 inline-block">
                                <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                                <p class="font-bold">Failed to sync data</p>
                                <button onclick="loadFaculty('${type}', '${targetDiv}', '${countId}')" class="mt-4 text-sm font-black uppercase tracking-widest hover:underline">Retry Connection</button>
                            </div>
                        </td>
                    </tr>
                `);
            }
        });
    }

    $(document).ready(function() {
        loadFaculty("teaching", "#teachingData", "#teachingCount");
        loadFaculty("non-teaching", "#nonTeachingData", "#nonTeachingCount");
    });
    </script>
</body>
</html>
