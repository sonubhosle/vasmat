<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>MIT COLLEGE OF COMPUTER SCI. & I.T. Socity Market, Basmath Tq. Basmath Dist. Hingoli PIN : 431512</title>
    <link rel="icon" href="https://mitbasmath.com/wp-content/uploads/2025/12/cropped-cropped-Mit-Logo.png"
        type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/vasmat/assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.259.0/dist/lucide.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="/vasmat/assets/js/navScript.js?v=2"></script>

</head>
<body class="bg-slate-100 text-slate-800 leading-normal tracking-normal">

    <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
    <header class="sticky top-0 left-0 w-full z-[200]">

        <?php if ($currentPage === 'index.php'): ?>
        <!-- BIG HEADER — index.php only -->
        <div id="bigHeader"
            class="bg-white transition-all duration-700 ease-in-out origin-top overflow-hidden  max-h-96 pt-6 pb-3 opacity-100">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <div class="inline-block text-[16px] tracking-widest   text-slate-600 font-semibold  uppercase  ">
                    Yuvak Pratishthan's
                </div>

                <h1
                    class="text-4xl bg-gradient-to-r from-amber-400 to-orange-500 bg-clip-text text-transparent font-black  tracking-tighter mb-1 uppercase leading-[0.85]">
                    MIT COLLEGE OF <br class="hidden md:block lg:hidden" />
                    <span>COMPUTER SCI. & I.T.</span>
                </h1>

                <p class="text-[13px] md:text-[14px] font-bold text-slate-600  tracking-widest  mb-1">
                    Socity Market, Basmath Tq. Basmath Dist. Hingoli PIN : 431512
                </p>

                <div
                    class="flex flex-wrap justify-center items-center gap-x-6 gap-y-1 mb-1 text-[10px]  font-black text-slate-600 uppercase tracking-widest">
                    <span class="flex items-center gap-1.5">
                        Affiliated to SRTMU, Nanded & Approved by Govt. of Maharashtra & AICTE
                    </span>
                </div>
                <div
                    class="flex flex-wrap justify-center items-center gap-x-6  text-[10px]  font-black text-slate-600 uppercase tracking-widest">
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
        <nav id="navBar" class="transition ease-in duration-300 bg-white shadow-sm border-t border-b border-slate-100 py-2 ">
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
                            class="text-[13px] font-semibold text-slate-600 hover:text-amber-400  tracking-widest transition ease-in duration-500 cursor-pointer">Home</a>

                            <!-- Departments  -->
                                <div class="relative group">
                            <a href="courses.php"
                                class="flex items-center gap-1.5 text-[13px] font-semibold text-slate-600 group-hover:text-amber-400  tracking-widest transition ease-in duration-300 cursor-pointer">
                                Departments
                            </a>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 pt-5 pointer-events-none group-hover:pointer-events-auto z-50">
                                <div class="bg-white border border-slate-100 shadow-2xl rounded-2xl overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 group-hover:max-h-[400px] group-hover:opacity-100">
                                    <div class="p-2 min-w-[210px] flex flex-col">
                                        <a href="offered_courses.php"
                                            class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500   tracking-widest rounded-xl transition ease-in duration-300">
                                            BCA Dept
                                        </a>
                                        <a href="fees_structure.php"
                                            class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500  tracking-widest rounded-xl transition ease-in duration-300">
                                            B.Sc (CS) Dept
                                        </a>
                                    
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- COURSES -->
                        <div class="relative group">
                            <a href="courses.php"
                                class="flex items-center gap-1.5 text-[13px] font-semibold text-slate-600 group-hover:text-amber-400  tracking-widest transition ease-in duration-500 cursor-pointer">
                                Courses
                            </a>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 pt-5 pointer-events-none group-hover:pointer-events-auto z-50">
                                <div class="bg-white border border-slate-100 shadow-2xl rounded-2xl overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 group-hover:max-h-[400px] group-hover:opacity-100">
                                    <div class="p-2 min-w-[210px] flex flex-col">
                                        <a href="offered_courses.php"
                                            class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500   tracking-widest rounded-xl transition ease-in duration-300">
                                            Offered Courses
                                        </a>
                                        <a href="fees_structure.php"
                                            class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500   tracking-widest rounded-xl transition ease-in duration-300">
                                            Fees Structure
                                        </a>
                                        <a href="courses.php"
                                            class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500   tracking-widest rounded-xl transition ease-in duration-300">
                                            Our Courses
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FACILITIES -->
                        <div class="relative group">
                            <a href="faculty.php"
                                class="flex items-center gap-1.5 text-[13px] font-semibold text-slate-600 group-hover:text-amber-400  tracking-widest transition ease-in duration-500 cursor-pointer">
                                Facilities
                            </a>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 pt-5 pointer-events-none group-hover:pointer-events-auto z-50">
                                <div class="bg-white border border-slate-100 shadow-2xl rounded-2xl overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 group-hover:max-h-[400px] group-hover:opacity-100">
                                    <div class="p-2 min-w-[210px] flex flex-col">
                                        <a href="about.php#library"
                                            class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500   tracking-widest rounded-xl transition ease-in duration-300">
                                            Library
                                        </a>
                                        <a href="about.php#seminar-hall"
                                            class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500  tracking-widest rounded-xl transition ease-in duration-300">
                                            Seminar Hall
                                        </a>
                                        <a href="about.php#labs"
                                            class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500  tracking-widest rounded-xl transition ease-in duration-300">
                                            Computer Labs
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- STUDENT CORNER -->
                        <div class="relative group">
                            <button
                                class="flex items-center gap-1.5 text-[13px] font-semibold text-slate-600 group-hover:text-amber-400  tracking-widest transition ease-in duration-500 cursor-pointer">
                                Student Corner
                            </button>
                            <div class="absolute top-full left-1/2 -translate-x-1/2 pt-5 pointer-events-none group-hover:pointer-events-auto z-50">
                                <div class="bg-white border border-slate-100 shadow-2xl rounded-2xl overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 group-hover:max-h-[400px] group-hover:opacity-100">
                                    <div class="p-2 min-w-[210px] flex flex-col">
                                        <a href="subject_notes.php"
                                            class="flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500  tracking-widest rounded-xl transition ease-in duration-300">
                                            Subject Notes
                                        </a>
                                        <a href="syllabus.php"
                                            class="flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500  tracking-widest rounded-xl transition ease-in duration-300">
                                            Syllabus
                                        </a>
                                        <a href="gallery.php"
                                            class="flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500  tracking-widest rounded-xl transition ease-in duration-300">
                                            Photo Gallery
                                        </a>
                                        <a href="help.php"
                                            class="flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[13px] font-semibold text-slate-500   tracking-widest rounded-xl transition ease-in duration-300">
                                            Help
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <a href="get_events.php" class="text-[13px] font-semibold text-slate-600 hover:text-amber-400  tracking-widest">Events</a>
                        <a href="naac.php" class="text-[13px] font-semibold text-slate-600 hover:text-amber-400 uppercase tracking-widest transition ease-in duration-500 cursor-pointer">NAAC</a>

                        <a href="get_faculty.php" class="text-[13px] font-semibold text-slate-600 hover:text-amber-400  tracking-widest transition ease-in duration-500 cursor-pointer">Faculty</a>
 
                        <a href="about.php" class="text-[13px] font-semibold text-slate-600 hover:text-amber-400 tracking-widest transition ease-in duration-500 cursor-pointer">About</a>
 
                            <a href="contact.php" class="text-[13px] font-semibold text-white tracking-widest bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-400 transition-all px-4 py-2.5 rounded-3xl  ">
                            Contact Us
                        </a>

                    </div>
                </div>
                <!-- RIGHT: Another Button (Example) -->
                <a href="" id="mobileRightButton"
                    class="flex lg:hidden text-[13px] font-semibold text-white   tracking-widest bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-400 transition-all px-4 py-2 rounded-2xl shadow-lg shadow-slate-900/20">
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

                    <!-- NAAC -->
                    <a href="naac.php"
                        class="w-full text-left text-[13px] py-2 font-semibold text-slate-700   block uppercase">NAAC</a>

                    <!-- Faculty -->
                    <a href="get_faculty.php"
                        class="w-full text-left text-[13px] py-2 font-semibold text-slate-700   block">Faculty</a>

                    <!-- About -->
                    <a href="about.php"
                        class="w-full text-left text-[13px] py-2 font-semibold text-slate-700   block">About</a>

                </div>

                <a href="contact.php"
                    class="mt-10 w-full py-5 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-black rounded-xl tracking-[0.2em] text-[13px] shadow-2xl active:scale-95 transition-all text-center block">
                    Contact Us
                </a>

            </div>
        </div>
    </div>


    


    <main class="">