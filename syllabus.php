<?php
include 'admin/includes/db.php';
include 'admin/includes/functions.php';

// Pagination settings
$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$start_from = ($page - 1) * $results_per_page;

// Filtering and Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$year_filter = isset($_GET['year']) ? trim($_GET['year']) : '';

// Build query with filters using UNION to include faculty uploads
$sql_syllabus = "SELECT id, subject_name, academic_year, syllabus_file, uploaded_by, status, created_at, 'legacy' as source 
                  FROM syllabus WHERE status = 'approved'";

$sql_faculty_syllabus = "SELECT fc.id, fc.title as subject_name, 'Current' as academic_year, fc.file_path as syllabus_file, f.name as uploaded_by, fc.status, fc.created_at, 'faculty' as source 
                        FROM faculty_content fc 
                        JOIN faculty f ON fc.faculty_id = f.id 
                        WHERE fc.type = 'syllabus' AND fc.status = 'approved'";

$final_sql = "SELECT * FROM (($sql_syllabus) UNION ($sql_faculty_syllabus)) as combined WHERE 1=1";

if (!empty($search)) {
    $final_sql .= " AND (subject_name LIKE ? OR academic_year LIKE ? OR uploaded_by LIKE ?) ";
    $search_param = "%$search%";
    $params[] = $search_param; $params[] = $search_param; $params[] = $search_param;
    $types .= "sss";
}

// Count total records for pagination (using a subquery for accuracy)
$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM ($final_sql) as t");
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_rows = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $results_per_page);

// Fetch filtered syllabus records with pagination
$query = "$final_sql ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt_types = $types . "ii";
$stmt_params = array_merge($params, [$start_from, $results_per_page]);
$stmt->bind_param($stmt_types, ...$stmt_params);
$stmt->execute();
$syllabi = $stmt->get_result();

// Get unique academic years for the filter
$years_query = "SELECT DISTINCT academic_year FROM syllabus ORDER BY academic_year DESC";
$years_result = $conn->query($years_query);

include 'includes/header.php';
?>

<div class="px-6 py-4 sm:py-6 lg:py-8 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-12 animate-fade-in-up">
        <div class="flex items-center gap-4 mb-4">
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-500">Academic Standards</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-black text-slate-900 mb-6">
            Course <span class="italic font-serif">Syllabus</span>
        </h2>
        <p class="text-slate-500 text-lg max-w-2xl">Download the official curriculum and academic guidelines for your specific course of study.</p>
    </div>

    <!-- Search and Filters -->
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
                           placeholder="Search syllabus by subject or year..."
                           class="w-full py-3.5 pl-12 pr-4 rounded-2xl border border-slate-100 bg-slate-50/50 focus:outline-none focus:ring-4 focus:ring-emerald-400/10 focus:border-emerald-400 transition-all font-medium">
                </div>

                <!-- Academic Year Filter -->
                <div class="custom-dropdown">
                    <div class="dropdown-btn" onclick="toggleDropdown('yearDropdown')">
                        <span class="flex items-center gap-2 truncate">
                            <i class="fas fa-calendar-alt text-slate-400 text-sm"></i>
                            <span id="yearLabel" class="truncate font-black text-slate-700 uppercase tracking-tight">
                                <?= $year_filter ? 'AY ' . htmlspecialchars($year_filter) : 'Academic Year' ?>
                            </span>
                        </span>
                        <i class="fas fa-chevron-down text-slate-400 text-[10px]"></i>
                    </div>
                    <input type="hidden" name="year" id="yearInput" value="<?= htmlspecialchars($year_filter) ?>">
                    <div id="yearDropdown" class="dropdown-menu">
                        <div class="dropdown-search">
                            <input type="text" placeholder="Search academic year..." class="dropdown-search-input" data-target="yearOptions">
                        </div>
                        <div class="dropdown-options" id="yearOptions">
                            <div class="dropdown-option text-amber-500 font-bold" onclick="selectOption('year', '', 'All Academic Years')">All Academic Years</div>
                            <?php $years_result->data_seek(0); while($y = $years_result->fetch_assoc()): ?>
                                <div class="dropdown-option <?= $year_filter == $y['academic_year'] ? 'selected' : '' ?>" 
                                     onclick="selectOption('year', '<?= htmlspecialchars($y['academic_year']) ?>', 'AY <?= htmlspecialchars($y['academic_year']) ?>')">
                                    AY <?= htmlspecialchars($y['academic_year']) ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-2">
                <a href="syllabus.php" class="px-6 py-3 border border-slate-200 text-slate-500 font-bold rounded-2xl hover:bg-slate-50 transition-all flex items-center gap-2 text-sm uppercase tracking-widest">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <button type="submit" class="px-8 py-3 bg-slate-900 text-white font-bold rounded-2xl shadow-xl shadow-slate-900/10 hover:shadow-slate-900/20 hover:-translate-y-0.5 transition-all flex items-center gap-2 text-sm uppercase tracking-widest">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Syllabus Table -->
    <div class="animate-fade-in-up" style="animation-delay: 0.2s; position: relative; z-index: 1;">
        <?php if ($total_rows == 0): ?>
            <div class="text-center py-20 glass-card rounded-[2rem] border-2 border-dashed border-slate-200">
                <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-file-invoice text-slate-300 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-2">No Syllabus Found</h3>
                <p class="text-slate-500 mb-8">We haven't uploaded the syllabus matching your criteria yet.</p>
                <a href="syllabus.php" class="px-8 py-3 bg-emerald-500 text-white font-bold rounded-2xl hover:bg-emerald-600 transition-all uppercase tracking-widest text-sm">Clear Filters</a>
            </div>
        <?php else: ?>
            <div class="glass-card rounded-[2rem] overflow-hidden shadow-2xl shadow-slate-200/40">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Subject</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Academic Year</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Uploaded By</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $syllabi->fetch_assoc()): ?>
                            <tr class="table-row-hover transition-all duration-300 group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-5">
                                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 bg-emerald-50 text-emerald-500 shadow-sm transition-transform group-hover:scale-110">
                                            <i class="fas fa-file-pdf text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-[17px] font-black text-slate-800 tracking-tight group-hover:text-emerald-600 transition-colors"><?= htmlspecialchars($row['subject_name']) ?></h4>
                                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-0.5">PDF Document</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-amber-100">
                                        AY <?= htmlspecialchars($row['academic_year']) ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-emerald-600 uppercase">
                                            <?= substr($row['uploaded_by'] ?: 'U', 0, 1) ?>
                                        </div>
                                        <span class="text-sm font-bold text-slate-600"><?= htmlspecialchars($row['uploaded_by'] ?: 'Admin') ?></span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-3">
                                        <?php $filePath = ($row['source'] === 'faculty') ? $row['syllabus_file'] : 'upload/syllabus/' . rawurlencode($row['syllabus_file']); ?>
                                        <a href="<?= $filePath ?>" 
                                           target="_blank"
                                           class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-emerald-500 hover:text-white hover:scale-110 transition-all shadow-sm"
                                           title="Preview">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="<?= $filePath ?>" 
                                           download
                                           class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-blue-500 hover:scale-110 transition-all shadow-lg shadow-slate-900/10"
                                           title="Download">
                                            <i class="fas fa-download text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Page <?= $page ?> of <?= $total_pages ?></p>
                    <div class="flex gap-2">
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&year=<?= urlencode($year_filter) ?>" 
                               class="w-8 h-8 rounded-lg flex items-center justify-center transition-all <?= $i == $page ? 'bg-emerald-500  shadow-lg' : 'bg-white text-slate-600 hover:bg-slate-100' ?> text-xs font-bold">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .table-row-hover:hover {
        background-color: #f8fafc;
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out forwards;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Custom Dropdown Styles */
    .custom-dropdown {
        position: relative;
        z-index: 50;
    }
    .dropdown-btn {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.85rem 1.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: #f8fafc;
        color: #334155;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
    }
    .dropdown-btn:hover, .dropdown-btn:focus, .dropdown-btn.active {
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        border-color: #fbbf24;
        background: white;
    }
    .dropdown-menu {
        position: absolute;
        top: calc(100% + 0.5rem);
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e7e7e7;
        border-radius: 1rem;
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
        max-height: 25rem;
        opacity: 1;
        transform: translateY(0) scale(1);
        overflow-y: auto;
    }
    .dropdown-search {
        padding: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
    }
    .dropdown-search input {
        width: 100%;
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        outline: none;
        transition: border-color 0.2s;
    }
    .dropdown-search input:focus {
        border-color: #fbbf24;
    }
    .dropdown-options {
        max-height: 15rem;
        overflow-y: auto;
    }
    .dropdown-option {
        padding: 0.75rem 1.25rem;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #475569;
        font-weight: 500;
    }
    .dropdown-option:last-child {
        border-bottom: none;
    }
    .dropdown-option:hover {
        background-color: #fffbeb;
        color: #fbbf24;
    }
    .dropdown-option.selected {
        background-color: #fff7ed;
        color: #ea580c;
        font-weight: 700;
    }
</style>

<script>
    function toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        const btn = dropdown.previousElementSibling.previousElementSibling; // Adjusted for hidden input
        
        // Handle if btn is not direct previous (due to hidden input)
        const actualBtn = event.currentTarget;
        
        // Close all other dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu.id !== dropdownId) {
                menu.classList.remove('show');
                const otherBtn = menu.closest('.custom-dropdown').querySelector('.dropdown-btn');
                if(otherBtn) otherBtn.classList.remove('active');
            }
        });
        
        // Toggle current dropdown
        dropdown.classList.toggle('show');
        actualBtn.classList.toggle('active');
        
        // Focus search
        if (dropdown.classList.contains('show')) {
            setTimeout(() => {
                const searchInput = dropdown.querySelector('.dropdown-search-input');
                if (searchInput) searchInput.focus();
            }, 50);
        }
    }

    function selectOption(type, value, label) {
        document.getElementById(type + 'Input').value = value;
        document.getElementById(type + 'Label').textContent = label;
        
        const dropdown = document.getElementById(type + 'Dropdown');
        dropdown.classList.remove('show');
        
        const btn = dropdown.closest('.custom-dropdown').querySelector('.dropdown-btn');
        if(btn) btn.classList.remove('active');
        
        // Update selected state
        dropdown.querySelectorAll('.dropdown-option').forEach(option => {
            option.classList.remove('selected');
        });
        event.target.classList.add('selected');
    }

    // Dropdown Search logic
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('dropdown-search-input')) {
            const targetId = e.target.getAttribute('data-target');
            const searchTerm = e.target.value.toLowerCase();
            const options = document.querySelectorAll(`#${targetId} .dropdown-option`);
            
            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }
    });

    // Global click listener to close dropdowns
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.remove('show'));
            document.querySelectorAll('.dropdown-btn').forEach(btn => btn.classList.remove('active'));
        }
    });
</script>
<?php include 'includes/footer.php'; ?>
