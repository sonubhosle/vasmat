<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title>MIT College of Computer Science & IT | NAAC Accredited | Basmath, Hingoli</title>
    <meta name="description" content="MIT College Basmath offers premium Computer Science & IT education. NAAC Accredited institution affiliated with SRTMU Nanded. Best BCA, B.Sc CS, and IT courses in Hingoli district.">
    <meta name="keywords" content="MIT College Basmath, Computer Science Hingoli, BCA Basmath, NAAC Accredited College, SRTMU Nanded affiliated, Best IT College Maharashtra">
    <meta name="author" content="MIT College Basmath">
    <link rel="canonical" href="http://localhost/vasmat/<?= basename($_SERVER['PHP_SELF']) ?>">

    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="MIT College of Computer Science & IT | Basmath">
    <meta property="og:description" content="Empowering students with excellence in IT and Computer Science education.">
    <meta property="og:image" content="https://mitbasmath.com/wp-content/uploads/2025/12/cropped-cropped-Mit-Logo.png">

    <link rel="icon" href="https://mitbasmath.com/wp-content/uploads/2025/12/cropped-cropped-Mit-Logo.png" type="image/png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/vasmat/assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.259.0/dist/lucide.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="/vasmat/assets/js/navScript.js?v=2"></script>

</head>
<body class="bg-slate-100 text-slate-800 leading-normal tracking-normal">

    <?php 
    require_once __DIR__ . '/../config/config.php';
    $currentPage = basename($_SERVER['PHP_SELF']); 
    ?>
    <header class="sticky top-0 left-0 w-full z-[200]">

        <?php if ($currentPage === 'index.php'): ?>
        <!-- BIG HEADER — index.php only -->
        <div id="bigHeader"
            class="bg-white transition-all duration-700 ease-in-out origin-top overflow-hidden  max-h-96 pt-3 pb-5 opacity-100">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <div class="inline-block text-[16px]    text-slate-600 font-semibold  uppercase  ">
                    Yuvak Pratishthan's
                </div>

                <h1
                    class="text-4xl bg-gradient-to-r from-amber-400 to-orange-500 bg-clip-text text-transparent font-black  tracking-tighter mb-1 uppercase leading-[0.85]">
                    MIT COLLEGE OF <br class="hidden md:block lg:hidden" />
                    <span>COMPUTER SCI. & I.T.</span>
                </h1>

                <p class="text-[13px] md:text-[14px] font-bold text-slate-600   mb-1">
                    Socity Market, Basmath Tq. Basmath Dist. Hingoli PIN : 431512
                </p>

                <div
                    class="flex flex-wrap justify-center items-center gap-x-6 gap-y-1 mb-1 text-[10px]  font-black text-slate-600 uppercase ">
                    <span class="flex items-center gap-1.5">
                        Affiliated to SRTMU, Nanded & Approved by Govt. of Maharashtra & AICTE
                    </span>
                </div>
                <div
                    class="flex flex-wrap justify-center items-center gap-x-6  text-[10px]  font-black text-slate-600 uppercase ">
                    <div class=" items-center gap-4 ">
                        <span class=" px-2 py-0.5 rounded">DTE Code: 2748</span>
                        <span class=" px-2 py-0.5 rounded">College Code: 264</span>
                        <span class=" px-2 py-0.5 rounded">AISHE Code : C-7542</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- NAV BAR -->
        <nav id="navBar" class="transition ease-in duration-300 bg-white shadow-sm border-t border-b border-slate-100 py-4 ">
            <div class="max-w-7xl mx-auto px-5 flex justify-between items-center">

                <div class="flex">
                    <!-- LEFT: Mobile Hamburger Button (shown on mobile) -->
                    <button id="openMobile"
                        class="lg:hidden px-4 py-2 mr-5 border border-slate-100 bg-slate-50 rounded-xl text-slate-600">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <!-- Mini Brand (mobile) -->
                    <a href="index.php" id="brandMiniMobile"
                        class="lg:hidden flex cursor-pointer items-center gap-2 transition-all duration-500 <?= $currentPage === 'index.php' ? 'opacity-0 -translate-x-10 pointer-events-none' : '' ?>">
                        <div class="w-9 h-9 bg-gradient-to-br from-amber-400 to-amber-500 rounded-3xl flex items-center justify-center text-white font-black text-base shadow-xl shrink-0">M</div>
                        <div class="flex flex-col leading-none">
                            <span class="font-black text-slate-900 text-sm uppercase tracking-tight">MIT College</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Comp. Sci. &amp; I.T.</span>
                        </div>
        </a>

                </div>
                <!-- Mini Brand (desktop) -->
                <a href="index.php"  id="brandMiniDesktop"
                    class="hidden lg:flex cursor-pointer items-center absolute left-6 gap-3 transition-all duration-500 <?= $currentPage === 'index.php' ? 'opacity-0 -translate-x-10 pointer-events-none' : '' ?>">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-amber-500 rounded-3xl flex items-center justify-center text-white font-black text-lg shadow-xl shrink-0">M</div>
                    <div class="flex flex-col leading-none">
                        <span class="font-black text-slate-900 text-sm uppercase tracking-tight">MIT College</span>
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Comp. Sci. &amp; I.T.</span>
                    </div>
        </a>

                <!-- CENTER/RIGHT: Desktop Menu -->
                <div id="navInner"
                    class="hidden lg:flex items-center transition-all duration-500 w-full py-1 <?= $currentPage === 'index.php' ? 'justify-center' : 'justify-end' ?>">
                    <div id="desktopMenu" class="flex items-center gap-5">

                        <a href="index.php"
                            class="text-[14px] font-semibold text-slate-600 hover:text-amber-400   transition ease-in duration-500 cursor-pointer">Home</a>

                            <!-- Departments  -->
                                <div class="relative group">
                                <button
                                    class="flex items-center gap-1.5 text-[14px] font-semibold text-slate-600 group-hover:text-amber-400 transition ease-in duration-300 cursor-pointer">
                                    Departments <i class="fa-solid fa-chevron-down text-[10px] opacity-50"></i>
                                </button>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 pt-5 pointer-events-none group-hover:pointer-events-auto z-50">
                                <div class="bg-white border border-slate-100 shadow-2xl rounded-2xl overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 group-hover:max-h-[400px] group-hover:opacity-100">
                                    <div class="p-2 min-w-[210px] flex flex-col">
                                        <a href="offered_courses.php"
                                            class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500   rounded-xl transition ease-in duration-300">
                                            BCA Dept
                                        </a>
                                        <a href="fees_structure.php"
                                            class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500  rounded-xl transition ease-in duration-300">
                                            B.Sc (CS) Dept
                                        </a>
                                    
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- COURSES -->
                        <div class="relative group">
                            <button
                                class="flex items-center gap-1.5 text-[14px] font-semibold text-slate-600 group-hover:text-amber-400 transition ease-in duration-500 cursor-pointer">
                                Courses <i class="fa-solid fa-chevron-down text-[10px] opacity-50"></i>
                            </button>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 pt-5 pointer-events-none group-hover:pointer-events-auto z-50">
                                <div class="bg-white border border-slate-100 shadow-2xl rounded-2xl overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 group-hover:max-h-[400px] group-hover:opacity-100">
                                    <div class="p-2 min-w-[210px] flex flex-col">
                                        <a href="offered_courses.php"
                                            class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500    rounded-xl transition ease-in duration-300">
                                            Offered Courses
                                        </a>
                                        <a href="fees_structure.php"
                                            class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500    rounded-xl transition ease-in duration-300">
                                            Fees Structure
                                        </a>
                                        <a href="courses.php"
                                            class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500    rounded-xl transition ease-in duration-300">
                                            Our Courses
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- INSTITUTIONAL -->
                        <div class="relative group">
                            <button class="flex items-center gap-1.5 text-[14px] font-semibold text-slate-600 group-hover:text-amber-400 transition ease-in duration-300">
                                Institutional <i class="fa-solid fa-chevron-down text-[10px] opacity-50"></i>
                            </button>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 pt-5 pointer-events-none group-hover:pointer-events-auto z-50">
                                <div class="bg-white border border-slate-100 shadow-2xl rounded-2xl overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 group-hover:max-h-[500px] group-hover:opacity-100">
                                    <div class="p-2 min-w-[220px] flex flex-col">
                                        <a href="about.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">About College</a>
                                        <a href="organogram.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">Institutional Organogram</a>
                                        <a href="research.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">Research & Publications</a>
                                        <a href="disclosures.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">Mandatory Disclosures</a>
                                        <a href="committees.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">Statutory Committees</a>
                                        <a href="code_of_conduct.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">Code of Conduct</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- QUALITY & NAAC -->
                        <div class="relative group">
                            <button class="flex items-center gap-1.5 text-[14px] font-semibold text-slate-600 group-hover:text-amber-400 transition ease-in duration-300" aria-label="Quality and NAAC Menu">
                                Quality (NAAC) <i class="fa-solid fa-chevron-down text-[10px] opacity-50"></i>
                            </button>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 pt-5 pointer-events-none group-hover:pointer-events-auto z-50">
                                <div class="bg-white border border-slate-100 shadow-2xl rounded-2xl overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 group-hover:max-h-[500px] group-hover:opacity-100">
                                    <div class="p-2 min-w-[220px] flex flex-col">
                                        <a href="iqac.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">IQAC Portal</a>
                                        <a href="naac.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">SSR & AQAR Reports</a>
                                        <a href="academic_calendar.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">Academic Calendar</a>
                                        <a href="best_practices.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">Best Practices</a>
                                        <a href="feedback.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">Stakeholder Feedback</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- STUDENT LIFE -->
                        <div class="relative group">
                            <button class="flex items-center gap-1.5 text-[14px] font-semibold text-slate-600 group-hover:text-amber-400 transition ease-in duration-300">
                                Student Life <i class="fa-solid fa-chevron-down text-[10px] opacity-50"></i>
                            </button>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 pt-5 pointer-events-none group-hover:pointer-events-auto z-50">
                                <div class="bg-white border border-slate-100 shadow-2xl rounded-2xl overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 group-hover:max-h-[500px] group-hover:opacity-100">
                                    <div class="p-2 min-w-[210px] flex flex-col">
                                        <a href="subject_notes.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">Subject Notes</a>
                                        <a href="nss_ncc.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">NSS & NCC</a>
                                        <a href="scholarship.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">Scholarships</a>
                                        <a href="gallery.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">Photo Gallery</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CAREER -->
                        <div class="relative group">
                            <button class="flex items-center gap-1.5 text-[14px] font-semibold text-slate-600 group-hover:text-amber-400 transition ease-in duration-300">
                                Career <i class="fa-solid fa-chevron-down text-[10px] opacity-50"></i>
                            </button>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 pt-5 pointer-events-none group-hover:pointer-events-auto z-50">
                                <div class="bg-white border border-slate-100 shadow-2xl rounded-2xl overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 group-hover:max-h-[400px] group-hover:opacity-100">
                                    <div class="p-2 min-w-[200px] flex flex-col">
                                        <a href="placement.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">Placement Cell</a>
                                        <a href="alumni.php" class="flex items-center px-4 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-500 rounded-xl transition duration-300">Alumni Network</a>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <a href="get_faculty.php" class="text-[14px] font-semibold text-slate-600 hover:text-amber-400 transition ease-in duration-500 cursor-pointer">Faculty</a>
 

                        <!-- LOGIN / REGISTER -->
                        <div class="relative group">
                            <button
                                class="flex items-center gap-1.5 text-[14px] font-semibold text-slate-600 group-hover:text-amber-400 transition ease-in duration-500 cursor-pointer">
                                Access<i class="fa-solid fa-chevron-down text-[10px] opacity-50"></i>
                            </button>
                            <div class="absolute top-full right-0 pt-5 pointer-events-none group-hover:pointer-events-auto z-50">
                                <div class="bg-white border border-slate-100 shadow-2xl rounded-2xl overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 group-hover:max-h-[500px] group-hover:opacity-100">
                                    <div class="p-2 min-w-[220px] flex flex-col">
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <div class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Welcome, <?= explode(' ', $_SESSION['user_name'])[0] ?></div>
                                            <a href="<?= BASE_URL . ($_SESSION['role'] === 'faculty' ? 'faculty/dashboard.php' : 'admin/index.php') ?>"
                                                class="flex items-center px-3 py-2 hover:bg-amber-50 hover:text-amber-600 text-[13px] font-semibold text-slate-600 rounded-xl transition ease-in duration-300">
                                                <i class="fa-solid fa-gauge-high mr-2 opacity-50"></i> Dashboard
                                            </a>
                                            <a href="<?= BASE_URL ?>auth/<?= $_SESSION['role'] ?>-logout.php"
                                                class="flex items-center px-3 py-2 hover:bg-rose-50 hover:text-rose-600 text-[13px] font-semibold text-slate-600 rounded-xl transition ease-in duration-300">
                                                <i class="fa-solid fa-power-off mr-2 opacity-50"></i> Logout
                                            </a>
                                        <?php else: ?>
                                            <!-- Admin Section -->
                                            <div class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Admin Portal</div>
                                            <a href="/vasmat/auth/admin-login.php"
                                                class="items-center px-3 py-2 w-full flex justify-center text-white bg-gradient-to-r from-amber-400 to-amber-500 text-center text-[13px] font-semibold rounded-3xl transition ease-in duration-300">
                                                Admin Login / Signup
                                            </a>
                                         
                                            <div class="h-px bg-slate-50 my-1 mx-2"></div>
                                            
                                            <!-- Faculty Section -->
                                            <div class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Faculty Portal</div>
                                            <a href="/vasmat/auth/faculty-login.php"
                                                class="flex items-center w-full justify-center px-3 py-2 text-white bg-gradient-to-r from-violet-500 to-fuchsia-500 text-center text-[13px] font-semibold rounded-3xl transition ease-in duration-300">
                                                Faculty Login / Signup
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
 
                            <a href="contact.php" class="text-[14px] font-semibold text-white bg-gradient-to-br from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-400 transition-all px-4 py-2 rounded-2xl shadow-lg shadow-slate-900/20">
                            Contact Us
                        </a>

                    </div>
                </div>
                <!-- RIGHT: Another Button (Example) -->
                <a href="" id="mobileRightButton"
                    class="flex lg:hidden text-[13px] font-semibold text-white    bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-400 transition-all px-4 py-2 rounded-2xl shadow-lg shadow-slate-900/20">
                    For Enquiry
                </a>
            </div>
        </nav>
    </header>

    <!-- MOBILE SIDEBAR -->
    <div id="mobileOverlay" class="fixed inset-0 z-[999] transition-all duration-500 opacity-0 pointer-events-none">
        <div id="mobileBg" class="absolute inset-0 bg-slate-950/40 backdrop-blur-md"></div>

        <div id="mobileMenu"
            class="absolute top-0 left-0 h-full w-[320px] bg-white shadow-2xl transition-transform duration-500 -translate-x-full">
            <div class="p-4 h-full flex flex-col">

                <!-- Header -->
                <div class="flex items-center justify-between mb-10">
                  <a href="index.php" 
                        class=" flex cursor-pointer items-center gap-2 transition-all duration-500 ">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-amber-500 rounded-3xl flex items-center justify-center text-white font-black text-base shadow-xl shrink-0">M</div>
                        <div class="flex flex-col leading-none">
                            <span class="font-black text-slate-900 text-sm uppercase tracking-tight">MIT College</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Comp. Sci. &amp; I.T.</span>
                        </div>
                  </a>

                    <button id="closeMobile" class="absolute top-4 right-4 w-10 h-10 bg-slate-50 rounded-3xl text-slate-700">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Menu -->
                <div class="flex-1 overflow-y-auto pr-2 -mr-2 ">

                    <!-- Home -->
                    <a href="index.php"
                        class="w-full text-left text-[13px] font-semibold text-slate-700  py-2 flex justify-between items-center group hover:text-amber-600 transition ease-in duration-300">
                        Home
                    </a>

                    <!-- Institutional -->
                    <div class="">
                        <button onclick="toggleAccordion('mobInst')"
                            class="w-full flex items-center justify-between py-2 text-[13px] font-semibold text-slate-700 hover:text-amber-600 transition ease-in duration-300">
                                Institutional
                            <i id="icon-mobInst" class="fa-solid fa-chevron-down text-slate-300 transition-transform"></i>
                        </button>
                        <div id="mobInst" data-open="false" class="overflow-hidden transition-all duration-500" style="max-height: 0px; opacity: 0;">
                            <div class="grid gap-2 pt-2">
                                <a href="about.php" class="flex items-center gap-2 px-3 text-[12px] font-semibold text-slate-600 hover:text-amber-600 transition duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> About College
                                </a>
                                <a href="organogram.php" class="flex items-center gap-2 px-3 text-[12px] font-semibold text-slate-600 hover:text-amber-600 transition duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Organogram
                                </a>
                                <a href="disclosures.php" class="flex items-center gap-2 px-3 text-[12px] font-semibold text-slate-600 hover:text-amber-600 transition duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Mandatory Disclosures
                                </a>
                                <a href="committees.php" class="flex items-center gap-2 px-3 text-[12px] font-semibold text-slate-600 hover:text-amber-600 transition duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Statutory Committees
                                </a>
                                <a href="code_of_conduct.php" class="flex items-center gap-2 px-3 text-[12px] font-semibold text-slate-600 hover:text-amber-600 transition duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Code of Conduct
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Departments -->
                    <div class="">
                        <button onclick="toggleAccordion('mobDepts')"
                            class="w-full flex items-center justify-between py-2 text-[13px] font-semibold text-slate-700   hover:text-amber-600 ease-in duration-300">
                               Departments
                            <i id="icon-mobDepts"
                                class="fa-solid fa-chevron-down text-slate-300 transition-transform"></i>
                        </button>

                        <div id="mobDepts" data-open="false" class="overflow-hidden transition-all duration-500" style="max-height: 0px; opacity: 0;">
                            <div class="grid gap-2 pt-2">
                                <a href="offered_courses.php"
                                    class="flex items-center gap-2 px-3  text-[12px] font-semibold text-slate-600    hover:text-amber-600 transition ease-in duration-300">
                                    <span id="dot-mobCourses" class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> BCA Dept
                                </a>
                                <a href="fees_structure.php"
                                    class="flex items-center gap-2 px-3  text-[12px] font-semibold text-slate-600   hover:text-amber-600 transition ease-in duration-300">
                                  <span id="dot-mobCourses" class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> B.Sc (CS) Dept
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Courses -->
                    <div class=" ">
                        <button onclick="toggleAccordion('mobCourses')"
                            class="w-full flex items-center justify-between py-2 text-[13px] font-semibold text-slate-700  tracking-tight hover:text-amber-600">
                                Courses
                            <i id="icon-mobCourses"
                                class="fa-solid fa-chevron-down text-slate-300 transition-transform"></i>
                        </button>

                        <div id="mobCourses" data-open="false" class="overflow-hidden transition-all duration-500" style="max-height: 0px; opacity: 0;">
                            <div class="grid gap-2 pt-2">
                                <a href="offered_courses.php"
                                    class="flex items-center gap-2 px-3  text-[12px] font-semibold text-slate-600    hover:text-amber-600 transition ease-in duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Offered Courses
                                </a>
                                <a href="fees_structure.php"
                                    class="flex items-center gap-2 px-3  text-[12px] font-semibold text-slate-600   hover:text-amber-600 transition ease-in duration-300">
                                  <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Fees Structure
                                </a>
                                <a href="courses.php"
                                    class="flex items-center gap-2 px-3  text-[12px] font-semibold text-slate-600   hover:text-amber-600 transition ease-in duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Our Courses
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Facilities -->
                    <div class="">
                        <button onclick="toggleAccordion('mobFacilities')"
                            class="w-full flex items-center justify-between py-2 text-[13px] font-semibold text-slate-700  tracking-tight hover:text-amber-600">
                                Facilities
                            
                            <i id="icon-mobFacilities"
                                class="fa-solid fa-chevron-down text-slate-300 transition-transform"></i>
                        </button>

                        <div id="mobFacilities" data-open="false" class="overflow-hidden transition-all duration-500" style="max-height: 0px; opacity: 0;">
                            <div class="grid gap-2 pt-2">
                                <a href="about.php#library"
                                    class="flex items-center gap-2 px-3  text-[12px] font-semibold text-slate-600    hover:text-amber-600 transition ease-in duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Library
                                </a>
                                <a href="about.php#seminar-hall"
                                    class="flex items-center gap-2 px-3  text-[12px] font-semibold text-slate-600   hover:text-amber-600 transition ease-in duration-300">
                                  <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Seminar Hall
                                </a>
                                <a href="about.php#labs"
                                    class="flex items-center gap-2 px-3  text-[12px] font-semibold text-slate-600   hover:text-amber-600 transition ease-in duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Computer Labs
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Student -->
                    <div class="">
                        <button onclick="toggleAccordion('mobStudent')"
                            class="w-full flex items-center justify-between py-2 text-[13px] font-semibold text-slate-700  tracking-tight hover:text-amber-600">
                                Student Corner
                            <i id="icon-mobStudent"
                                class="fa-solid fa-chevron-down text-slate-300 transition-transform"></i>
                        </button>

                        <div id="mobStudent" data-open="false" class="overflow-hidden transition-all duration-500" style="max-height: 0px; opacity: 0;">
                            <div class="grid gap-2 pt-2">
                                <a href="subject_notes.php"
                                    class="flex items-center gap-2 px-3  text-[12px] font-semibold text-slate-600    hover:text-amber-600 transition ease-in duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Subject Notes
                                </a>
                                <a href="syllabus.php"
                                    class="flex items-center gap-2 px-3  text-[12px] font-semibold text-slate-600   hover:text-amber-600 transition ease-in duration-300">
                                  <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Syllabus
                                </a>
                                <a href="gallery.php"
                                    class="flex items-center gap-2 px-3  text-[12px] font-semibold text-slate-600   hover:text-amber-600 transition ease-in duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Photo Gallery
                                </a>
                                <a href="help.php"
                                    class="flex items-center gap-2 px-3  text-[12px] font-semibold text-slate-600   hover:text-amber-600 transition ease-in duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Help
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Events -->
                    <a href="get_events.php"
                        class="w-full text-left text-[13px] py-2 font-semibold text-slate-700   block">Events</a>

                    <!-- Research -->
                    <a href="research.php" class="w-full text-left text-[13px] py-2 font-semibold text-slate-700 block">Research</a>

                    <!-- Career -->
                    <div class="">
                        <button onclick="toggleAccordion('mobCareer')"
                            class="w-full flex items-center justify-between py-2 text-[13px] font-semibold text-slate-700 tracking-tight hover:text-amber-600">
                                Career & Placement
                            <i id="icon-mobCareer" class="fa-solid fa-chevron-down text-slate-300 transition-transform"></i>
                        </button>
                        <div id="mobCareer" data-open="false" class="overflow-hidden transition-all duration-500" style="max-height: 0px; opacity: 0;">
                            <div class="grid gap-2 pt-2">
                                <a href="placement.php" class="flex items-center gap-2 px-3 text-[12px] font-semibold text-slate-600 hover:text-amber-600 transition duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Placement Cell
                                </a>
                                <a href="alumni.php" class="flex items-center gap-2 px-3 text-[12px] font-semibold text-slate-600 hover:text-amber-600 transition duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Alumni Network
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- NAAC & Quality -->
                    <div class="">
                        <button onclick="toggleAccordion('mobNaac')"
                            class="w-full flex items-center justify-between py-2 text-[13px] font-semibold text-slate-700 tracking-tight hover:text-amber-600">
                                Quality (NAAC)
                            <i id="icon-mobNaac" class="fa-solid fa-chevron-down text-slate-300 transition-transform"></i>
                        </button>
                        <div id="mobNaac" data-open="false" class="overflow-hidden transition-all duration-500" style="max-height: 0px; opacity: 0;">
                            <div class="grid gap-2 pt-2">
                                <a href="iqac.php" class="flex items-center gap-2 px-3 text-[12px] font-semibold text-slate-600 hover:text-amber-600 transition duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> IQAC Portal
                                </a>
                                <a href="naac.php" class="flex items-center gap-2 px-3 text-[12px] font-semibold text-slate-600 hover:text-amber-600 transition duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> SSR & AQAR Reports
                                </a>
                                <a href="academic_calendar.php" class="flex items-center gap-2 px-3 text-[12px] font-semibold text-slate-600 hover:text-amber-600 transition duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Academic Calendar
                                </a>
                                <a href="best_practices.php" class="flex items-center gap-2 px-3 text-[12px] font-semibold text-slate-600 hover:text-amber-600 transition duration-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Best Practices
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Faculty -->
                    <a href="get_faculty.php"
                        class="w-full text-left text-[13px] py-2 font-semibold text-slate-700   block">Faculty</a>


                    <!-- Auth Section -->
                    <div class="">
                        <button onclick="toggleAccordion('mobAuth')"
                            class="w-full flex items-center justify-between py-2 text-[13px] font-semibold text-slate-700 tracking-tight hover:text-amber-600">
                                Login / Register
                            <i id="icon-mobAuth" class="fa-solid fa-chevron-down text-slate-300 transition-transform"></i>
                        </button>

                        <div id="mobAuth" data-open="false" class="overflow-hidden transition-all duration-500" style="max-height: 0px; opacity: 0;">
                            <div class="grid gap-2 pt-2 pb-4">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Signed in as <?= e($_SESSION['user_name']) ?></div>
                                    <a href="<?= BASE_URL . ($_SESSION['role'] === 'faculty' ? 'faculty/dashboard.php' : 'admin/index.php') ?>"
                                        class="flex items-center gap-2 px-5 text-[12px] font-semibold text-slate-600 hover:text-amber-600 transition ease-in duration-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Dashboard
                                    </a>
                                    <a href="<?= BASE_URL ?>auth/<?= $_SESSION['role'] ?>-logout.php"
                                        class="flex items-center gap-2 px-5 text-[12px] font-semibold text-rose-600 hover:text-rose-700 transition ease-in duration-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Logout
                                    </a>
                                <?php else: ?>
                                    <!-- Admin -->
                                    <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Admin</div>
                                    <a href="/vasmat/auth/admin-login.php"
                                        class="flex items-center justify-center w-full gap-2 px-5 text-[12px] bg-gradient-to-r from-amber-400 to-amber-500 text-white font-semibold rounded-3xl transition ease-in duration-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Admin Login / Signup
                                    </a>

                                    <!-- Faculty -->
                                    <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Faculty</div>
                                    <a href="/vasmat/auth/faculty-login.php"
                                        class="flex items-center justify-center w-full gap-2 px-5 text-[12px] bg-gradient-to-r from-violet-500 to-fuchsia-500 text-white font-semibold rounded-3xl transition ease-in duration-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Faculty Login / Signup
                                    </a>
                                    
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>

                <a href="contact.php"
                    class="mt-10 w-full py-5 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-black rounded-xl tracking-[0.2em] text-[13px] shadow-2xl active:scale-95 transition-all text-center block">
                    Contact Us
                </a>

            </div>
        </div>
    </div>


    


    <main class="">