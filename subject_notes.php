<?php include './includes/header.php'; ?>
<?php
include __DIR__ . "/admin/includes/db.php";

// Handle search and filters
$search = '';
$class_filter = '';
$subject_filter = '';
$semester_filter = '';

if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
}

if (isset($_GET['class']) && !empty($_GET['class'])) {
    $class_filter = " AND class = '" . $conn->real_escape_string($_GET['class']) . "'";
}

if (isset($_GET['subject']) && !empty($_GET['subject'])) {
    $subject_filter = " AND subject_name = '" . $conn->real_escape_string($_GET['subject']) . "'";
}

if (isset($_GET['semester']) && !empty($_GET['semester'])) {
    $semester_filter = " AND semester = '" . $conn->real_escape_string($_GET['semester']) . "'";
}

// Build query with filters
$query = "SELECT * FROM notes WHERE 1=1";
if (!empty($search)) {
    $query .= " AND (subject_name LIKE '%$search%' OR description LIKE '%$search%' OR class LIKE '%$search%')";
}
$query .= $class_filter . $subject_filter . $semester_filter . " ORDER BY created_at DESC";

$notes = $conn->query($query);
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

    
    <div class="px-6  py-4 sm:py-6 lg:py-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8 lg:mb-10 animate-fade-in-up">

              <div class="mb-12">
                        <div class="flex items-center gap-4 mb-4">
                            <span
                                class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-400">Content</span>
                        </div>
                        <h2 class="text-4xl  font-black text-slate-900 mb-6 ">
                            Study <span class="italic font-serif">Notes</span>
                        </h2>

                    </div>

            <!-- Search and Filters -->
            <div class="bg-white rounded-xl p-4 sm:p-6 border border-slate-100 mb-6">
                <form method="GET" action="" class="space-y-4 sm:space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 ">
                         <!-- Search Bar -->
                    <div class="relative">
                        <div class="absolute left-3 sm:left-4 top-1/2 transform -translate-y-1/2 text-slate-400">
                            <i class="fas fa-search text-sm sm:text-base"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="<?= htmlspecialchars($search) ?>"
                               placeholder="Search notes by subject, class, or description..."
                               class="w-full py-2.5 pl-11 rounded-xl   border border-slate-200 focus:outline-none focus:ring-2 sm:focus:ring-4 focus:ring-amber-400/20 focus:border-amber-400 ">
                    </div>

                    <!-- Filters Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        <!-- Class Filter -->
                        <div class="custom-dropdown">
                            <div class="dropdown-btn" onclick="toggleDropdown('classDropdown')">
                                <span class="flex items-center gap-2 truncate">
                                    <i class="fas fa-graduation-cap text-slate-400 text-sm"></i>
                                    <span id="classLabel" class="truncate">
                                        <?= isset($_GET['class']) && $_GET['class'] ? htmlspecialchars($_GET['class']) : 'All Classes' ?>
                                    </span>
                                </span>
                                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                            </div>
                            <input type="hidden" name="class" id="classInput" value="<?= isset($_GET['class']) ? htmlspecialchars($_GET['class']) : '' ?>">
                            <div id="classDropdown" class="dropdown-menu">
                                <div class="dropdown-search">
                                    <input type="text" placeholder="Search class..." class="dropdown-search-input" data-target="classOptions">
                                </div>
                                <div class="dropdown-options" id="classOptions">
                                    <div class="dropdown-option" onclick="selectOption('class', '', 'All Classes')">
                                        All Classes
                                    </div>
                                    <?php while($class = $classes->fetch_assoc()): ?>
                                    <div class="dropdown-option <?= isset($_GET['class']) && $_GET['class'] == $class['class'] ? 'selected' : '' ?>" 
                                         onclick="selectOption('class', '<?= htmlspecialchars($class['class']) ?>', '<?= htmlspecialchars($class['class']) ?>')">
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
                                    <span id="subjectLabel" class="truncate">
                                        <?= isset($_GET['subject']) && $_GET['subject'] ? htmlspecialchars($_GET['subject']) : 'All Subjects' ?>
                                    </span>
                                </span>
                                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                            </div>
                            <input type="hidden" name="subject" id="subjectInput" value="<?= isset($_GET['subject']) ? htmlspecialchars($_GET['subject']) : '' ?>">
                            <div id="subjectDropdown" class="dropdown-menu">
                                <div class="dropdown-search">
                                    <input type="text" placeholder="Search subject..." class="dropdown-search-input" data-target="subjectOptions">
                                </div>
                                <div class="dropdown-options" id="subjectOptions">
                                    <div class="dropdown-option" onclick="selectOption('subject', '', 'All Subjects')">
                                        All Subjects
                                    </div>
                                    <?php 
                                    $subjects->data_seek(0);
                                    while($subject = $subjects->fetch_assoc()): ?>
                                    <div class="dropdown-option <?= isset($_GET['subject']) && $_GET['subject'] == $subject['subject_name'] ? 'selected' : '' ?>" 
                                         onclick="selectOption('subject', '<?= htmlspecialchars($subject['subject_name']) ?>', '<?= htmlspecialchars($subject['subject_name']) ?>')">
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
                                    <span id="semesterLabel" class="truncate">
                                        <?= isset($_GET['semester']) && $_GET['semester'] ? htmlspecialchars($_GET['semester']) : 'All Semesters' ?>
                                    </span>
                                </span>
                                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                            </div>
                            <input type="hidden" name="semester" id="semesterInput" value="<?= isset($_GET['semester']) ? htmlspecialchars($_GET['semester']) : '' ?>">
                            <div id="semesterDropdown" class="dropdown-menu">
                                <div class="dropdown-search">
                                    <input type="text" placeholder="Search semester..." class="dropdown-search-input" data-target="semesterOptions">
                                </div>
                                <div class="dropdown-options" id="semesterOptions">
                                    <div class="dropdown-option" onclick="selectOption('semester', '', 'All Semesters')">
                                        All Semesters
                                    </div>
                                    <?php 
                                    $semesters->data_seek(0);
                                    while($semester = $semesters->fetch_assoc()): ?>
                                    <div class="dropdown-option <?= isset($_GET['semester']) && $_GET['semester'] == $semester['semester'] ? 'selected' : '' ?>" 
                                         onclick="selectOption('semester', '<?= htmlspecialchars($semester['semester']) ?>', '<?= htmlspecialchars($semester['semester']) ?>')">
                                        <?= htmlspecialchars($semester['semester']) ?>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="flex justify-end flex-col sm:flex-row gap-3 pt-2">
                        <button type="submit" 
                                class=" px-4 sm:px-6 py-3 bg-gradient-to-r from-amber-400 to-amber-600 text-white font-semibold rounded-lg sm:rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2 group">
                            <i class="fas fa-filter text-sm"></i>
                            <span class="text-sm sm:text-base">Apply Filters</span>
                            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform duration-200"></i>
                        </button>
                        <a href="subject_notes.php" 
                           class="px-4 sm:px-6 py-3 border-2 border-slate-200 text-slate-700 font-medium rounded-lg sm:rounded-xl hover:bg-slate-50 transition-all duration-200 flex items-center justify-center gap-2 text-sm sm:text-base">
                            <i class="fas fa-redo text-sm"></i>
                            Reset Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Count -->
        <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <?php if (!empty($search) || !empty($class_filter) || !empty($subject_filter) || !empty($semester_filter)): ?>
                <div class="text-xs sm:text-sm text-slate-600 bg-amber-50 px-3 py-1.5 rounded-full">
                    <i class="fas fa-filter mr-1"></i>
                    Filters applied
                </div>
            <?php endif; ?>
        </div>

        <!-- Notes Grid -->
        <div class="animate-fade-in-up" style="animation-delay: 0.2s;">
            <?php if ($totalNotes == 0): ?>
                <!-- Empty State -->
                <div class="text-center py-12 sm:py-16 bg-white/50 backdrop-blur-sm rounded-xl sm:rounded-2xl border-2 border-dashed border-slate-200">
                    <div class="max-w-md mx-auto px-4">
                        <div class="w-24 h-24 sm:w-32 sm:h-32 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                            <i class="fas fa-search text-slate-400 text-3xl sm:text-4xl lg:text-5xl"></i>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-700 mb-2 sm:mb-3">No Notes Found</h3>
                        <p class="text-slate-500 text-sm sm:text-base mb-6 sm:mb-8">Try adjusting your search filters or check back later</p>
                        <a href="get_all_notes.php" 
                           class="px-6 sm:px-8 py-3 bg-gradient-to-r from-amber-500 to-indigo-600 text-white font-semibold rounded-lg sm:rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5 inline-flex items-center gap-2 text-sm sm:text-base">
                            <i class="fas fa-redo"></i>
                            Clear Filters
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Notes Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 ">
                    <?php while($row = $notes->fetch_assoc()): 
                        $fileExt = strtolower(pathinfo($row['file_path'], PATHINFO_EXTENSION));
                        $fileIcon = $fileExt == 'pdf' ? 'fa-file-pdf' : 
                                   ($fileExt == 'doc' || $fileExt == 'docx' ? 'fa-file-word' : 
                                   ($fileExt == 'ppt' || $fileExt == 'pptx' ? 'fa-file-powerpoint' : 
                                   ($fileExt == 'xls' || $fileExt == 'xlsx' ? 'fa-file-excel' : 'fa-file')));
                        $fileColor = $fileExt == 'pdf' ? 'bg-red-100 text-red-600' : 
                                   ($fileExt == 'doc' || $fileExt == 'docx' ? 'bg-amber-100 text-amber-600' : 
                                   ($fileExt == 'ppt' || $fileExt == 'pptx' ? 'bg-orange-100 text-orange-600' : 
                                   ($fileExt == 'xls' || $fileExt == 'xlsx' ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-600')));
                        
                        // Fix file path - get correct absolute path
                        $basePath = __DIR__ . 'upload/notes/';
                        $filePath = $basePath . $row['file_path'];
                        
                        // Get file size properly
                        $fileSize = 0;
                        if (file_exists($filePath)) {
                            $fileSize = filesize($filePath);
                        }
                        $fileSizeFormatted = $fileSize ? formatFileSize($fileSize) : 'Unknown size';
                    ?>
                    <div class="group bg-white relative rounded-xl overflow-hidden border border-slate-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl animate-fade-in">
                        <!-- Card Header -->
                        <div class="p-3 border-b border-slate-100">
                            <div class="flex justify-between items-start gap-3">
                                   <div class="<?= $fileColor ?> file-icon flex-shrink-0 py-3 px-4 rounded-xl">
                                    <i class="fas <?= $fileIcon ?> text-lg sm:text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-base  text-slate-800 truncate"><?= htmlspecialchars($row['subject_name']) ?></h3>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-xl text-xs font-medium bg-amber-100 text-amber-700">
                                            <i class="fas fa-graduation-cap mr-1 text-xs"></i>
                                            <?= htmlspecialchars($row['class']) ?>
                                        </span>
                                        <?php if (!empty($row['semester'])): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-xl text-xs font-medium bg-purple-100 text-purple-700">
                                           Sem - <?= htmlspecialchars($row['semester']) ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                             
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="px-3 py-1 ">
                            <!-- File Info -->
                                <div class="flex items-center gap-3 justify-between">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs text-slate-500">
                                            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-user text-xs"></i>
                                                    <?= !empty($row['created_by']) ? htmlspecialchars($row['created_by']) : 'Unknown' ?>
                                                </span>
                                                <span class="hidden sm:inline">•</span>
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-calendar text-xs"></i>
                                                    <?= date('M d, Y', strtotime($row['created_at'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col sm:flex-row gap-3 ">
                                <!-- Preview Button -->
                                <a href="upload/notes/<?= rawurlencode($row['file_path']) ?>" 
                                   target="_blank"
                                   class="flex-1 px-3 py-2  bg-gradient-to-r from-amber-50 to-indigo-50 text-amber-700 rounded-xl sm:rounded-xl font-medium hover:from-amber-100 hover:to-indigo-100 transition-all duration-200 border border-amber-100 hover:border-amber-200 flex items-center justify-center gap-2 group text-sm">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>

                                <!-- Download Button -->
                                <a href="upload/notes/<?= rawurlencode($row['file_path']) ?>" 
                                   download="<?= htmlspecialchars($row['subject_name']) . '.' . $fileExt ?>"
                                   class="px-3.5 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl sm:rounded-xl font-semibold hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 group text-sm download-btn">
                                    <i class="fas fa-download text-sm group-hover:animate-bounce-slow"></i>
                                    
                                </a>
                            </div>
                                </div>

                            <!-- Action Buttons -->
                            
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <!-- Results Info -->
                <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-slate-200 text-center text-slate-600 text-xs sm:text-sm">
                    <p class="flex flex-wrap items-center justify-center gap-1 sm:gap-2">
                        <i class="fas fa-info-circle"></i>
                        Showing <?= $totalNotes ?> note<?= $totalNotes != 1 ? 's' : '' ?> • 
                        You can preview notes in your browser or download them for offline study
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

<style>
    .custom-dropdown {
        position: relative;
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

    .dropdown-btn:hover {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .dropdown-btn:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .dropdown-btn.active {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        z-index: 60;
    }

    .dropdown-btn i {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .dropdown-btn.active i {
        transform: rotate(180deg);
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e7e7e7;
        border-top: none;
        border-radius: 10px;
        box-shadow: 
            0 4px 6px -1px rgba(0, 0, 0, 0.1),
            0 10px 15px -3px rgba(0, 0, 0, 0.1),
            0 20px 25px -5px rgba(0, 0, 0, 0.1);
        z-index: 50;
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
        animation: slideDown 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .dropdown-menu.hiding {
        animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .dropdown-search {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
        animation: fadeIn 0.2s ease-out 0.1s both;
    }

    .dropdown-search input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        outline: none;
        transition: all 0.2s ease;
    }

    .dropdown-search input:focus {
        border-color: #fbbf24;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }

    .dropdown-options {
        max-height: 13rem;
        overflow-y: auto;
        animation: fadeIn 0.2s ease-out 0.15s both;
    }

    .dropdown-option {
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #334155;
        position: relative;
        overflow: hidden;
    }

    .dropdown-option:hover {
        background-color: #f8fafc;
        padding-left: 1.25rem;
    }

    .dropdown-option.selected {
        background-color: #eff6ff;
        color:  #fbbf24;
        font-weight: 500;
        padding-left: 1.25rem;
    }

    .dropdown-option.selected::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 4px;
        background-color:  #fbbf24;
        border-radius: 50%;
    }

    .dropdown-option:last-child {
        border-bottom: none;
    }

    /* Backdrop for dropdown focus */
    .dropdown-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: transparent;
        z-index: 40;
        display: none;
    }

    .dropdown-backdrop.active {
        display: block;
        animation: fadeIn 0.2s ease-out;
    }
</style>

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