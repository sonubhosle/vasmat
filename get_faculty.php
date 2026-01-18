<?php
include __DIR__ . '/admin/includes/db.php';

/* ================= FETCH API ================= */
if (isset($_GET['fetch'])) {
    $type = $_GET['type'] ?? 'teaching';

    $sql = "SELECT * FROM faculty WHERE faculty_type='$type' ORDER BY id DESC";
    $res = $conn->query($sql);

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
    <title>Faculty Directory</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
   

        <!-- Teaching Staff Section -->
        <section class="mb-16">
            <div class="flex items-center gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-700">Teaching Staff</h2>
                    <p class="text-slate-600">Academic Faculty & Instructors</p>
                </div>
                <span class="ml-auto bg-gradient-to-r from-blue-100 to-blue-50 border border-blue-200 text-blue-700 text-sm font-semibold px-4 py-2 rounded-full shadow-sm">
                    <span id="teachingCount">0</span> Members
                </span>
            </div>
            
            <div id="teachingData" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Loading skeleton -->
                <?php for($i=0; $i<4; $i++): ?>
                <div class="animate-pulse">
                    <div class="bg-white rounded-xl shadow h-40"></div>
                </div>
                <?php endfor; ?>
            </div>
        </section>

        <!-- Non-Teaching Staff Section -->
        <section class="mb-16">
            <div class="flex items-center gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-700">Non-Teaching Staff</h2>
                    <p class="text-slate-600">Administrative & Support Staff</p>
                </div>
                <span class="ml-auto bg-gradient-to-r from-emerald-100 to-green-50 border border-emerald-200 text-emerald-700 text-sm font-semibold px-4 py-2 rounded-full shadow-sm">
                    <span id="nonTeachingCount">0</span> Members
                </span>
            </div>
            
            <div id="nonTeachingData" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Loading skeleton -->
                <?php for($i=0; $i<4; $i++): ?>
                <div class="animate-pulse">
                    <div class="bg-white rounded-xl shadow h-40"></div>
                </div>
                <?php endfor; ?>
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
                console.log(type, res);
                
                // Update count
                $(countId).text(res.length);
                
                let html = "";
                if (res.length == 0) {
                    html = `
                        <div class="col-span-2 bg-gradient-to-br from-slate-50 to-white rounded-2xl shadow-inner border border-slate-200 p-12 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-slate-200 to-slate-300 rounded-full mb-6">
                                <i class="fas fa-user-slash text-slate-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-700 mb-2">No Members Found</h3>
                            <p class="text-slate-500">There are no ${type} staff members at the moment.</p>
                        </div>
                    `;
                } else {
                    res.forEach(f => {
                        const bgGradient = type === 'teaching' 
                            ? 'bg-gradient-to-br from-blue-50 to-white border-blue-100 hover:border-blue-300' 
                            : 'bg-gradient-to-br from-emerald-50 to-white border-emerald-100 hover:border-emerald-300';
                        
                        const textColor = type === 'teaching' ? 'text-blue-600' : 'text-emerald-600';
                        const iconBg = type === 'teaching' ? 'bg-blue-100 text-blue-600' : 'bg-emerald-100 text-emerald-600';
                        const badgeBg = type === 'teaching' ? 'bg-gradient-to-r from-blue-500 to-blue-600' : 'bg-gradient-to-r from-emerald-500 to-green-600';
                        
                        html += `
                            <div class="group relative">
                                <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl blur opacity-0 group-hover:opacity-20 transition duration-500"></div>
                                <div class="relative ${bgGradient} border-2 rounded-xl shadow-sm p-3 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                                    <div class="flex items-start gap-5">
                                        <!-- Profile Image -->
                                        <div class="relative flex-shrink-0">
                                            <img src="upload/faculty/${f.photo}" 
                                                 onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(f.name)}&background=random&color=fff&bold=true&size=150'"
                                                 class="w-32 h-32 rounded-xl  border-4 border-white">
                                                
                                        </div>
                                        
                                        <!-- Info -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between mb-2">
                                                <div>
                                                    <h3 class="text-lg font-bold text-slate-800 truncate group-hover:text-blue-700 transition-colors">${f.name}</h3>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <span class="${textColor} font-semibold text-sm">${f.designation}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex gap-2 ml-2">
                                                    ${f.email ? `
                                                        <a href="mailto:${f.email}" class="${iconBg} w-8 h-8 rounded-full flex items-center justify-center hover:shadow-md transition-all hover:scale-110">
                                                            <i class="fas fa-envelope text-xs"></i>
                                                        </a>
                                                    ` : ''}
                                                    
                                                    ${f.phone ? `
                                                        <a href="tel:${f.phone}" class="${type === 'teaching' ? 'bg-blue-100 text-blue-600' : 'bg-emerald-100 text-emerald-600'} w-8 h-8 rounded-full flex items-center justify-center hover:shadow-md transition-all hover:scale-110">
                                                            <i class="fas fa-phone text-xs"></i>
                                                        </a>
                                                    ` : ''}
                                                </div>
                                            </div>
                                            
                                            <!-- Details Grid -->
                                            <div class="flex gap-4 items-center mt-2">
                                                ${f.education ? `
                                                    <div class="flex items-center gap-2">
                                                        <div class="${iconBg} w-6 h-6 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                                                            <i class="fas fa-graduation-cap text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-slate-500">Education</p>
                                                            <p class="text-sm font-medium text-slate-700 truncate">${f.education}</p>
                                                        </div>
                                                    </div>
                                                ` : ''}
                                                
                                                ${f.experience ? `
                                                    <div class="flex items-center gap-2">
                                                        <div class="${iconBg} w-6 h-6 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                                                            <i class="fas fa-briefcase text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-slate-500">Experience</p>
                                                            <p class="text-sm font-medium text-slate-700">${f.experience}</p>
                                                        </div>
                                                    </div>
                                                ` : ''}
                                            </div>
                                            
                                            <!-- Contact Info -->
                                            <div class="mt-4 pt-4 border-t border-slate-100">
                                                <div class="flex items-center justify-between text-sm">
                                                    ${f.email ? `
                                                        <span class="text-slate-600 truncate max-w-[60%]">
                                                            <i class="fas fa-envelope text-slate-400 mr-1"></i>
                                                            ${f.email}
                                                        </span>
                                                    ` : ''}
                                                    
                                                    ${f.phone ? `
                                                        <span class="${textColor} font-medium">
                                                            <i class="fas fa-phone mr-1"></i>
                                                            ${f.phone}
                                                        </span>
                                                    ` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                
                $(targetDiv).html(html);
            },
            error: function() {
                $(targetDiv).html(`
                    <div class="col-span-2 bg-gradient-to-br from-red-50 to-white border border-red-200 rounded-2xl p-10 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-red-100 to-red-200 rounded-full mb-4">
                            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Failed to Load Data</h3>
                        <p class="text-slate-600 mb-6">Please check your connection and try again.</p>
                        <button onclick="loadFaculty('${type}', '${targetDiv}', '${countId}')" 
                                class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                            <i class="fas fa-redo mr-2"></i>Try Again
                        </button>
                    </div>
                `);
            }
        });
    }

    // Load both sections
    $(document).ready(function() {
        loadFaculty("teaching", "#teachingData", "#teachingCount");
        loadFaculty("non-teaching", "#nonTeachingData", "#nonTeachingCount");
    });
    </script>
</body>
</html>