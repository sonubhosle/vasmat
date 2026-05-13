<?php include './includes/header.php'; ?>
<?php
include __DIR__ . "/admin/includes/db.php";

// Handle search and filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$class_val = isset($_GET['class']) ? $_GET['class'] : '';
$subject_val = isset($_GET['subject']) ? $_GET['subject'] : '';
$semester_val = isset($_GET['semester']) ? $_GET['semester'] : '';
$dept_val = isset($_GET['dept']) ? $_GET['dept'] : '';

// Build query with filters using UNION to include faculty uploads
$sql_notes = "SELECT id, subject_name, description, file_path, class, semester, created_by, created_at, 'legacy' as source, '' as department 
              FROM notes WHERE status = 'approved'";

$sql_faculty = "SELECT fc.id, fc.title as subject_name, fc.description, fc.file_path, 'General' as class, 'N/A' as semester, f.name as created_by, fc.created_at, 'faculty' as source, fc.department 
                FROM faculty_content fc 
                JOIN faculty f ON fc.faculty_id = f.id 
                WHERE fc.type = 'notes' AND fc.status = 'approved'";

$final_sql = "SELECT * FROM (($sql_notes) UNION ($sql_faculty)) as combined WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $final_sql .= " AND (subject_name LIKE ? OR description LIKE ? OR class LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm; $params[] = $searchTerm; $params[] = $searchTerm;
    $types .= "sss";
}

if (!empty($class_val)) {
    $final_sql .= " AND class = ?";
    $params[] = $class_val;
    $types .= "s";
}

if (!empty($dept_val)) {
    $final_sql .= " AND department = ?";
    $params[] = $dept_val;
    $types .= "s";
}

$final_sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($final_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$notes = $stmt->get_result();
$totalNotes = $notes->num_rows;

// Get distinct values for filters
$classes = $conn->query("SELECT DISTINCT class FROM notes WHERE class IS NOT NULL AND class != '' ORDER BY class");
$subjects = $conn->query("SELECT DISTINCT subject_name FROM notes WHERE subject_name IS NOT NULL AND subject_name != '' ORDER BY subject_name");
$semesters = $conn->query("SELECT DISTINCT semester FROM notes WHERE semester IS NOT NULL AND semester != '' ORDER BY semester");
?>



   <script>
    tailwind.config = {
        theme: {
            extend: {
                animation: {
                    'fade-in-up': 'fadeInUp 0.5s ease-out',
                    'fade-in': 'fadeIn 0.3s ease-out',
                    'slide-down': 'slideDown 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
                    'slide-up': 'slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
                    'bounce-slow': 'bounce 1s infinite',
                    'scale-in': 'scaleIn 0.2s cubic-bezier(0.4, 0, 0.2, 1)',
                    'scale-out': 'scaleOut 0.2s cubic-bezier(0.4, 0, 0.2, 1)',
                },
                keyframes: {
                    fadeInUp: {
                        'from': {
                            opacity: '0',
                            transform: 'translateY(20px)'
                        },
                        'to': {
                            opacity: '1',
                            transform: 'translateY(0)'
                        }
                    },
                    fadeIn: {
                        'from': { opacity: '0' },
                        'to': { opacity: '1' }
                    },
                    slideDown: {
                        'from': {
                            opacity: '0',
                            transform: 'translateY(-10px) scale(0.95)'
                        },
                        'to': {
                            opacity: '1',
                            transform: 'translateY(0) scale(1)'
                        }
                    },
                    slideUp: {
                        'from': {
                            opacity: '1',
                            transform: 'translateY(0) scale(1)'
                        },
                        'to': {
                            opacity: '0',
                            transform: 'translateY(-10px) scale(0.95)'
                        }
                    },
                    scaleIn: {
                        'from': {
                            opacity: '0',
                            transform: 'scale(0.95)'
                        },
                        'to': {
                            opacity: '1',
                            transform: 'scale(1)'
                        }
                    },
                    scaleOut: {
                        'from': {
                            opacity: '1',
                            transform: 'scale(1)'
                        },
                        'to': {
                            opacity: '0',
                            transform: 'scale(0.95)'
                        }
                    }
                },
                transitionProperty: {
                    'height': 'height',
                    'spacing': 'margin, padding',
                    'transform-opacity': 'transform, opacity',
                }
            }
        }
    }
</script>

    
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
        }
        .table-row-hover:hover {
            background-color: #f8fafc;
        }
        .file-icon-box {
            transition: transform 0.3s ease;
        }
        .table-row-hover:hover .file-icon-box {
            transform: scale(1.1);
        }
        .custom-dropdown {
            position: relative;
            z-index: 50;
        }
        .dropdown-btn {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            background: white;
            color: #334155;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }
        .dropdown-btn:hover, .dropdown-btn:focus, .dropdown-btn.active {
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
            border-color: #fbbf24;
        }
        .dropdown-btn.active {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e7e7e7;
            border-top: none;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            z-index: 9999;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transform: translateY(-10px) scale(0.95);
            transform-origin: top center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform, opacity, max-height;
        }
        .dropdown-menu.show {
            max-height: 20rem;
            opacity: 1;
            transform: translateY(0) scale(1);
            overflow-y: auto;
        }
        .dropdown-search {
            padding: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
        }
        .dropdown-search input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            outline: none;
        }
        .dropdown-options {
            max-height: 13rem;
            overflow-y: auto;
        }
        .dropdown-option {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
            color: #334155;
        }
        .dropdown-option:hover {
            background-color: #f8fafc;
            color: #fbbf24;
        }
        .dropdown-option.selected {
            background-color: #fffbeb;
            color: #fbbf24;
            font-weight: 700;
        }
    </style>
    
    <div class="px-6 py-4 ">
        <!-- Header -->
        <div class="mb-12 animate-fade-in-up">
            <div class="flex items-center gap-4 mb-4">
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-500">Academic Resources</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-slate-900 mb-6">
                Subject <span class="italic font-serif">Notes</span>
            </h2>
            <p class="text-slate-500 text-lg max-w-2xl">Access curated study materials and lecture notes shared by our expert faculty.</p>
        </div>

        <div class="glass-card rounded-[2rem] p-6 mb-12 shadow-xl shadow-slate-200/50 animate-fade-in-up" 
             style="position: relative; z-index: 100; overflow: visible !important;">
            <form method="GET" action="" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Search Bar -->
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="<?= htmlspecialchars($search) ?>"
                               placeholder="Search notes by subject, class, or keywords..."
                               class="w-full py-3.5 pl-12 pr-4 rounded-2xl border border-slate-100 bg-slate-50/50 focus:outline-none focus:ring-4 focus:ring-amber-400/10 focus:border-amber-400 transition-all font-medium">
                    </div>

                    <!-- Filters Row -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Department Filter -->
                        <div class="custom-dropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('deptDropdown')">
                                <span class="flex items-center gap-2 truncate">
                                    <i class="fas fa-building text-slate-400 text-sm"></i>
                                    <span id="deptLabel" class="truncate font-semibold text-slate-700">
                                        <?= isset($_GET['dept']) && $_GET['dept'] ? htmlspecialchars($_GET['dept']) : 'Department' ?>
                                    </span>
                                </span>
                                <i class="fas fa-chevron-down text-slate-400 text-[10px]"></i>
                            </div>
                            <input type="hidden" name="dept" id="deptInput" value="<?= isset($_GET['dept']) ? htmlspecialchars($_GET['dept']) : '' ?>">
                            <div id="deptDropdown" class="dropdown-menu">
                                <div class="dropdown-options" id="deptOptions">
                                    <div class="dropdown-option font-bold text-amber-500" onclick="selectOption('dept', '', 'All Departments')">All Departments</div>
                                    <div class="dropdown-option" onclick="selectOption('dept', 'BCA Dept', 'BCA Dept')">BCA Dept</div>
                                    <div class="dropdown-option" onclick="selectOption('dept', 'B.Sc(CS) Dept', 'B.Sc(CS) Dept')">B.Sc(CS) Dept</div>
                                </div>
                            </div>
                        </div>

                        <!-- Class Filter -->
                        <div class="custom-dropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('classDropdown')">
                                <span class="flex items-center gap-2 truncate">
                                    <i class="fas fa-graduation-cap text-slate-400 text-sm"></i>
                                    <span id="classLabel" class="truncate font-semibold text-slate-700">
                                        <?= isset($_GET['class']) && $_GET['class'] ? htmlspecialchars($_GET['class']) : 'Class' ?>
                                    </span>
                                </span>
                                <i class="fas fa-chevron-down text-slate-400 text-[10px]"></i>
                            </div>
                            <input type="hidden" name="class" id="classInput" value="<?= isset($_GET['class']) ? htmlspecialchars($_GET['class']) : '' ?>">
                            <div id="classDropdown" class="dropdown-menu">
                                <div class="dropdown-search">
                                    <input type="text" placeholder="Search class..." class="dropdown-search-input" data-target="classOptions">
                                </div>
                                <div class="dropdown-options" id="classOptions">
                                    <div class="dropdown-option font-bold text-amber-500" onclick="selectOption('class', '', 'All Classes')">All Classes</div>
                                    <?php while($class = $classes->fetch_assoc()): ?>
                                    <div class="dropdown-option" onclick="selectOption('class', '<?= htmlspecialchars($class['class']) ?>', '<?= htmlspecialchars($class['class']) ?>')">
                                        <?= htmlspecialchars($class['class']) ?>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Subject Filter -->
                        <div class="custom-dropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('subjectDropdown')">
                                <span class="flex items-center gap-2 truncate">
                                    <i class="fas fa-book text-slate-400 text-sm"></i>
                                    <span id="subjectLabel" class="truncate font-semibold text-slate-700">
                                        <?= isset($_GET['subject']) && $_GET['subject'] ? htmlspecialchars($_GET['subject']) : 'Subject' ?>
                                    </span>
                                </span>
                                <i class="fas fa-chevron-down text-slate-400 text-[10px]"></i>
                            </div>
                            <input type="hidden" name="subject" id="subjectInput" value="<?= isset($_GET['subject']) ? htmlspecialchars($_GET['subject']) : '' ?>">
                            <div id="subjectDropdown" class="dropdown-menu">
                                <div class="dropdown-search">
                                    <input type="text" placeholder="Search subject..." class="dropdown-search-input" data-target="subjectOptions">
                                </div>
                                <div class="dropdown-options" id="subjectOptions">
                                    <div class="dropdown-option font-bold text-amber-500" onclick="selectOption('subject', '', 'All Subjects')">All Subjects</div>
                                    <?php $subjects->data_seek(0); while($subject = $subjects->fetch_assoc()): ?>
                                    <div class="dropdown-option" onclick="selectOption('subject', '<?= htmlspecialchars($subject['subject_name']) ?>', '<?= htmlspecialchars($subject['subject_name']) ?>')">
                                        <?= htmlspecialchars($subject['subject_name']) ?>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Semester Filter -->
                        <div class="custom-dropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('semesterDropdown')">
                                <span class="flex items-center gap-2 truncate">
                                    <i class="fas fa-calendar-alt text-slate-400 text-sm"></i>
                                    <span id="semesterLabel" class="truncate font-semibold text-slate-700">
                                        <?= isset($_GET['semester']) && $_GET['semester'] ? htmlspecialchars($_GET['semester']) : 'Semester' ?>
                                    </span>
                                </span>
                                <i class="fas fa-chevron-down text-slate-400 text-[10px]"></i>
                            </div>
                            <input type="hidden" name="semester" id="semesterInput" value="<?= isset($_GET['semester']) ? htmlspecialchars($_GET['semester']) : '' ?>">
                            <div id="semesterDropdown" class="dropdown-menu">
                                <div class="dropdown-options" id="semesterOptions">
                                    <div class="dropdown-option font-bold text-amber-500" onclick="selectOption('semester', '', 'All Semesters')">All Semesters</div>
                                    <?php $semesters->data_seek(0); while($semester = $semesters->fetch_assoc()): ?>
                                    <div class="dropdown-option" onclick="selectOption('semester', '<?= htmlspecialchars($semester['semester']) ?>', '<?= htmlspecialchars($semester['semester']) ?>')">
                                        <?= htmlspecialchars($semester['semester']) ?>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="flex justify-end gap-3 pt-2">
                    <a href="subject_notes.php" class="px-6 py-3 border border-slate-200 text-slate-500 font-bold rounded-2xl hover:bg-slate-50 transition-all flex items-center gap-2 text-sm uppercase tracking-widest">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                    <button type="submit" class="px-8 py-3 bg-slate-900 text-white font-bold rounded-2xl shadow-xl shadow-slate-900/10 hover:shadow-slate-900/20 hover:-translate-y-0.5 transition-all flex items-center gap-2 text-sm uppercase tracking-widest">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                </div>
            </form>
        </div>

        <!-- Notes Table Body -->
        <div class="animate-fade-in-up" style="animation-delay: 0.2s; position: relative; z-index: 1;">
            <?php if ($totalNotes == 0): ?>
                <div class="text-center py-20 glass-card rounded-[2rem] border-2 border-dashed border-slate-200">
                    <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search text-slate-300 text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 mb-2">No Notes Match</h3>
                    <p class="text-slate-500 mb-8">We couldn't find any study materials matching your criteria.</p>
                    <a href="subject_notes.php" class="px-8 py-3 bg-amber-500 text-white font-bold rounded-2xl hover:bg-amber-600 transition-all uppercase tracking-widest text-sm">Clear all filters</a>
                </div>
            <?php else: ?>
                <div class="glass-card rounded-[2rem] overflow-hidden shadow-2xl shadow-slate-200/40">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Details</th>
                                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Categorization</th>
                                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Uploader</th>
                                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Posted on</th>
                                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $notes->fetch_assoc()): 
                                    $fileExt = strtolower(pathinfo($row['file_path'], PATHINFO_EXTENSION));
                                    $fileIcon = $fileExt == 'pdf' ? 'fa-file-pdf' : 
                                               ($fileExt == 'doc' || $fileExt == 'docx' ? 'fa-file-word' : 
                                               ($fileExt == 'ppt' || $fileExt == 'pptx' ? 'fa-file-powerpoint' : 
                                               ($fileExt == 'xls' || $fileExt == 'xlsx' ? 'fa-file-excel' : 'fa-file')));
                                    $fileColor = $fileExt == 'pdf' ? 'text-red-500 bg-red-50' : 
                                               ($fileExt == 'doc' || $fileExt == 'docx' ? 'text-blue-500 bg-blue-50' : 
                                               ($fileExt == 'ppt' || $fileExt == 'pptx' ? 'text-orange-500 bg-orange-50' : 
                                               ($fileExt == 'xls' || $fileExt == 'xlsx' ? 'text-emerald-500 bg-emerald-50' : 'text-slate-500 bg-slate-50')));
                                ?>
                                <tr class="table-row-hover transition-all duration-300 group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-5">
                                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm <?= $fileColor ?> file-icon-box">
                                                <i class="fas <?= $fileIcon ?> text-xl"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-[17px] font-black text-slate-800 tracking-tight group-hover:text-amber-600 transition-colors"><?= htmlspecialchars($row['subject_name']) ?></h4>
                                                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-0.5">.<?= $fileExt ?> document</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col gap-1.5">
                                            <span class="inline-flex px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-amber-100 self-start">
                                                <?= htmlspecialchars($row['class']) ?>
                                            </span>
                                            <?php if (!empty($row['semester'])): ?>
                                            <span class="inline-flex px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-indigo-100 self-start">
                                                SEM: <?= htmlspecialchars($row['semester']) ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 uppercase">
                                                <?= substr($row['created_by'] ?? 'U', 0, 1) ?>
                                            </div>
                                            <span class="text-sm font-bold text-slate-600"><?= htmlspecialchars($row['created_by'] ?: 'Unknown') ?></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="text-sm font-semibold text-slate-400"><?= date('M d, Y', strtotime($row['created_at'])) ?></span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex justify-end gap-3">
                                            <?php 
                                                $filePath = ($row['source'] === 'faculty') ? $row['file_path'] : 'upload/notes/' . $row['file_path'];
                                            ?>
                                            <a href="<?= $filePath ?>" 
                                               target="_blank"
                                               class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-amber-500 hover:text-white hover:scale-110 transition-all shadow-sm"
                                               title="Preview File">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                            <a href="<?= $filePath ?>" 
                                               download="<?= htmlspecialchars($row['subject_name']) . '.' . $fileExt ?>"
                                               class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-emerald-500 hover:scale-110 transition-all shadow-lg shadow-slate-900/10 download-btn"
                                               title="Download File">
                                                <i class="fas fa-download text-xs"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-center">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-4">
                            <span class="w-8 h-[1px] bg-slate-200"></span>
                            Found <?= $totalNotes ?> Resources
                            <span class="w-8 h-[1px] bg-slate-200"></span>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <script>
        // Custom Dropdown Functions
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const btn = dropdown.previousElementSibling;
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu.id !== dropdownId) {
                    menu.classList.remove('show');
                    menu.previousElementSibling.previousElementSibling.classList.remove('active');
                }
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('show');
            btn.classList.toggle('active');
            
            // Focus on search input when opening
            if (dropdown.classList.contains('show')) {
                setTimeout(() => {
                    const searchInput = dropdown.querySelector('.dropdown-search-input');
                    if (searchInput) searchInput.focus();
                }, 10);
            }
        }

        function selectOption(type, value, label) {
            document.getElementById(type + 'Input').value = value;
            document.getElementById(type + 'Label').textContent = label;
            document.getElementById(type + 'Dropdown').classList.remove('show');
            document.querySelector(`[onclick="toggleDropdown('${type}Dropdown')"]`).classList.remove('active');
            
            // Update selected state
            document.querySelectorAll(`#${type}Options .dropdown-option`).forEach(option => {
                option.classList.remove('selected');
            });
            event.target.classList.add('selected');
        }

        // Search in dropdowns
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('dropdown-search-input')) {
                const target = e.target.getAttribute('data-target');
                const searchTerm = e.target.value.toLowerCase();
                const options = document.querySelectorAll(`#${target} .dropdown-option`);
                
                options.forEach(option => {
                    const text = option.textContent.toLowerCase();
                    option.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            }
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
                document.querySelectorAll('.dropdown-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
            }
        });

        // Format file sizes function
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Add animations to cards
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '50px'
        });

        document.querySelectorAll('.animate-fade-in').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease-out';
            observer.observe(card);
        });

        // Show loading state on download
        document.querySelectorAll('.download-btn').forEach(link => {
            link.addEventListener('click', function(e) {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Downloading...';
                this.classList.add('opacity-75', 'cursor-wait');
                
                // Reset after 3 seconds if download doesn't complete
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('opacity-75', 'cursor-wait');
                }, 3000);
            });
        });

        // Auto-apply filters on mobile after dropdown selection
        if (window.innerWidth < 768) {
            document.querySelectorAll('.dropdown-option').forEach(option => {
                option.addEventListener('click', function() {
                    setTimeout(() => {
                        this.closest('form').submit();
                    }, 300);
                });
            });
        }

        // Search input focus effect
        const searchInput = document.querySelector('input[name="search"]');
   

        // Initialize dropdowns with current values
        document.addEventListener('DOMContentLoaded', function() {
            // Set selected state for dropdowns
            <?php if (isset($_GET['dept']) && $_GET['dept']): ?>
            document.querySelectorAll('#deptOptions .dropdown-option').forEach(option => {
                if (option.textContent.trim() === '<?= htmlspecialchars($_GET['dept']) ?>') {
                    option.classList.add('selected');
                }
            });
            <?php endif; ?>
            
            <?php if (isset($_GET['class']) && $_GET['class']): ?>
            document.querySelectorAll('#classOptions .dropdown-option').forEach(option => {
                if (option.textContent.trim() === '<?= htmlspecialchars($_GET['class']) ?>') {
                    option.classList.add('selected');
                }
            });
            <?php endif; ?>
            
            <?php if (isset($_GET['subject']) && $_GET['subject']): ?>
            document.querySelectorAll('#subjectOptions .dropdown-option').forEach(option => {
                if (option.textContent.trim() === '<?= htmlspecialchars($_GET['subject']) ?>') {
                    option.classList.add('selected');
                }
            });
            <?php endif; ?>
            
            <?php if (isset($_GET['semester']) && $_GET['semester']): ?>
            document.querySelectorAll('#semesterOptions .dropdown-option').forEach(option => {
                if (option.textContent.trim() === '<?= htmlspecialchars($_GET['semester']) ?>') {
                    option.classList.add('selected');
                }
            });
            <?php endif; ?>
        });
    </script>


<?php
// Function to format file size
function formatFileSize($bytes) {
    if ($bytes == 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return number_format($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
?>

<?php include './includes/footer.php'; ?>