<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>MIT COLLEGE OF COMPUTER SCI. & I.T. Socity Market, Basmath Tq. Basmath Dist. Hingoli PIN : 431512</title>
    <link rel="icon" href="https://mitbasmath.com/wp-content/uploads/2025/12/cropped-cropped-Mit-Logo.png"
        type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/mit-college/assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.259.0/dist/lucide.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body class="bg-slate-100 text-slate-800 leading-normal tracking-normal">

    <header class="sticky top-0 left-0 w-full z-50">

        <!-- BIG HEADER -->
        <div id="bigHeader"
            class="bg-white transition-all duration-700 ease-in-out origin-top overflow-hidden  max-h-96 pt-6 pb-3 opacity-100">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <div class="inline-block text-[16px] tracking-widest   text-slate-600 font-semibold  uppercase  ">
                    Yuvak Pratishthan's
                </div>

                <h1
                    class="text-2xl text-yellow-500 md:text-3xl lg:text-4xl font-extrabold  tracking-tighter mb-1 uppercase leading-[0.85]">
                    MIT COLLEGE OF <br class="hidden md:block lg:hidden" />
                    <span>COMPUTER SCI. & I.T.</span>
                </h1>

                <p class="text-[11px] md:text-[14px] font-bold text-slate-600  tracking-widest  mb-1">
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

        <!-- NAV BAR -->
        <nav id="navBar" class="transition ease-in duration-300 bg-white shadow-sm border-t border-b border-slate-100 py-2 ">
            <div class="max-w-7xl mx-auto px-5 flex justify-between items-center">

                <div class="flex">
                    <!-- LEFT: Mobile Hamburger Button (shown on mobile) -->
                    <button id="openMobile"
                        class="lg:hidden px-4 py-1.5 mr-5 border border-slate-100 bg-slate-50 rounded-xl text-slate-600">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <!-- CENTER: Mini Brand (visible on mobile when scrolled) -->
                    <div id="brandMiniMobile"
                        class="lg:hidden  flex items-center gap-2 transition-all duration-500 opacity-0 -translate-x-10 pointer-events-none">
                        <div
                            class="px-3 py-2 bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center text-white font-black text-2xl font-extrabold shadow-lg">
                            M</div>
                        <div class="flex flex-col">
                            <span class="font-black text-slate-900 text-lg uppercase">MIT COLLEGE</span>
                            <span class="text-[12px] font-bold text-slate-600 uppercase">COMPUTER SCI. & I.T.</span>
                        </div>
                    </div>

                </div>
                <!-- LEFT (Desktop): Mini Brand (hidden by default on desktop) -->
                <div id="brandMiniDesktop"
                    class="hidden lg:flex absolute left-6 w-56 gap-2 transition-all duration-500 opacity-0 -translate-x-10 pointer-events-none">
                    <div
                        class="px-3 py-2 bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center text-white font-black text-2xl font-extrabold shadow-lg">
                        M</div>
                    <div class="flex flex-col">
                        <span class="font-black text-slate-900 text-lg uppercase">MIT COLLEGE</span>
                        <span class="text-[12px] font-bold text-slate-600 uppercase">COMPUTER SCI. & I.T.</span>
                    </div>
                </div>
                <!-- CENTER: Desktop Menu -->
                <div id="navInner"
                    class="hidden lg:flex items-center transition-all duration-500 justify-center w-full py-1">
                    <div id="desktopMenu" class="flex items-center gap-8">

                        <a href="index.php"
                            class="text-[11px] font-black text-slate-600 hover:text-amber-400 uppercase tracking-widest">Home</a>

                        <!-- COURSES -->
                        <div class="relative group">
                            <a href="courses.php"
                                class="flex items-center gap-1.5 text-[11px] font-black text-slate-600 group-hover:text-amber-400 uppercase tracking-widest">
                                Courses
                            </a>
                            <div
                                class="absolute top-full left-1/2 -translate-x-1/2 pt-3 opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-top pointer-events-none group-hover:pointer-events-auto">
                                <div class="bg-white border border-slate-100 shadow-2xl rounded-2xl p-2 min-w-[210px]">
                                    <a href="offered_courses.php"
                                        class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[12px] font-black text-slate-500  uppercase tracking-widest rounded-xl transition ease-in duration-300">
                                        Offered Courses
                                    </a>
                                    <a href="fees_structure.php"
                                        class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[12px] font-black text-slate-500  uppercase tracking-widest rounded-xl transition ease-in duration-300">
                                        Fees Structure
                                    </a>
                                    <a href="courses.php"
                                        class=" flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[12px] font-black text-slate-500  uppercase tracking-widest rounded-xl transition ease-in duration-300">
                                        Our Courses
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- FACILITIES -->
                        <div class="relative group">
                            <a href="faculty.php"
                                class="flex items-center gap-1.5 text-[11px] font-black text-slate-600 group-hover:text-amber-400 uppercase tracking-widest">
                                Facilities
                            </a>
                            <div
                                class="absolute top-full left-1/2 -translate-x-1/2 pt-3 opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-top pointer-events-none group-hover:pointer-events-auto">
                                <div class="bg-white border border-slate-100 shadow-2xl rounded-2xl p-2 min-w-[210px]">
                                    <a
                                        class="flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[12px] font-black text-slate-500  uppercase tracking-widest rounded-xl transition ease-in duration-300">
                                        Library
                                    </a>
                                    <a
                                        class="flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[12px] font-black text-slate-500  uppercase tracking-widest rounded-xl transition ease-in duration-300">
                                        Seminar Hall
                                    </a>
                                    <a
                                        class="flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[12px] font-black text-slate-500  uppercase tracking-widest rounded-xl transition ease-in duration-300">
                                        Computer Labs
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- STUDENT CORNER -->
                        <div class="relative group">
                            <button
                                class="flex items-center gap-1.5 text-[11px] font-black text-slate-600 group-hover:text-amber-400 uppercase tracking-widest">
                                Student Corner
                            </button>
                            <div
                                class="absolute top-full left-1/2 -translate-x-1/2 pt-3 opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-top pointer-events-none group-hover:pointer-events-auto">
                                <div class="bg-white border border-slate-100 shadow-2xl rounded-2xl p-2 min-w-[210px]">
                                    <a
                                        class="flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[12px] font-black text-slate-500  uppercase tracking-widest rounded-xl transition ease-in duration-300">
                                        Subject Notes
                                    </a>
                                    <a
                                        class="flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[12px] font-black text-slate-500  uppercase tracking-widest rounded-xl transition ease-in duration-300">
                                        Syllabus
                                    </a>
                                    <a
                                        class="flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[12px] font-black text-slate-500  uppercase tracking-widest rounded-xl transition ease-in duration-300">
                                        Photo Gallery
                                    </a>
                                    <a
                                        class="flex items-center px-2 py-2 hover:bg-amber-50 hover:text-amber-400 text-[12px] font-black text-slate-500  uppercase tracking-widest rounded-xl transition ease-in duration-300">
                                        Help
                                    </a>
                                </div>
                            </div>
                        </div>

                        <a href="admissions.php"
                            class="text-[11px] font-black text-slate-600 hover:text-amber-400 uppercase tracking-widest">Admissions</a>
                              <a href="get_events.php"
                            class="text-[11px] font-black text-slate-600 hover:text-amber-400 uppercase tracking-widest">Events</a>

                        <a href="about.php"
                            class="text-[11px] font-black text-slate-600 hover:text-amber-400 uppercase tracking-widest">About</a>
 
                            <a href="contact.php"
                            class="text-[11px] font-black text-white  uppercase tracking-widest bg-gradient-to-br from-amber-400 to-amber-600 px-4 py-2.5 rounded-2xl shadow-sm shadow-amber-500/40">
                            Contact Us</a>

                    </div>
                </div>
                <!-- RIGHT: Another Button (Example) -->
                <a href="" id="mobileRightButton"
                    class="flex lg:hidden text-[11px] font-black text-white  uppercase tracking-widest bg-gradient-to-br from-amber-400 to-amber-600 px-4 py-2.5 rounded-2xl shadow-2xl shadow-amber-500/40">
                    For Enquiry
                </a>
            </div>
        </nav>
    </header>

    <!-- MOBILE SIDEBAR -->
    <div id="mobileOverlay" class="fixed inset-0 z-[100] transition-all duration-500 opacity-0 pointer-events-none">
        <div id="mobileBg" class="absolute inset-0 bg-slate-950/40 backdrop-blur-md"></div>

        <div id="mobileMenu"
            class="absolute top-0 left-0 h-full w-[290px] bg-white shadow-2xl transition-transform duration-500 -translate-x-full">
            <div class="p-7 h-full flex flex-col">

                <!-- Header -->
                <div class="flex items-center justify-between mb-10">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 bg-amber-600 rounded-lg flex items-center justify-center text-white font-black shadow-lg">
                            M</div>
                        <div class="flex flex-col">
                            <span class="font-black text-slate-900 uppercase text-xs tracking-tighter leading-none">MIT
                                COLLEGE</span>
                            <span class="text-[8px] font-bold text-amber-600 uppercase mt-1">Basmath</span>
                        </div>
                    </div>

                    <button id="closeMobile" class="p-2 hover:bg-slate-50 rounded-lg text-slate-400">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- Menu -->
                <div class="flex-1 overflow-y-auto pr-2 -mr-2 space-y-5">

                    <!-- Home -->
                    <button
                        class="w-full text-left text-lg font-black text-slate-900 uppercase tracking-tighter border-b border-slate-100 pb-4 flex justify-between items-center group">
                        Home
                        <i class="fa-solid fa-chevron-right text-slate-300 group-hover:text-amber-600"></i>
                    </button>

                    <!-- Courses -->
                    <div class="border-b border-slate-100 pb-2">
                        <button onclick="toggleAccordion('mobCourses')"
                            class="w-full flex items-center justify-between py-4 text-sm font-black text-slate-700 uppercase tracking-tight hover:text-amber-600">
                            <span class="flex items-center gap-3">
                                <span id="dot-mobCourses" class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                                Courses
                            </span>
                            <i id="icon-mobCourses"
                                class="fa-solid fa-chevron-down text-slate-300 transition-transform"></i>
                        </button>

                        <div id="mobCourses" class="overflow-hidden transition-all duration-500 max-h-0 opacity-0">
                            <div class="grid gap-2 pt-2">
                                <button
                                    class="flex items-center gap-4 p-4 text-[11px] font-bold text-slate-500 uppercase bg-slate-50/50 hover:bg-amber-50 hover:text-amber-600 rounded-xl">
                                    <i class="fa-solid fa-book text-slate-400"></i> Offered Courses
                                </button>
                                <button
                                    class="flex items-center gap-4 p-4 text-[11px] font-bold text-slate-500 uppercase bg-slate-50/50 hover:bg-amber-50 hover:text-amber-600 rounded-xl">
                                    <i class="fa-solid fa-scale-balanced text-slate-400"></i> Fees Structure
                                </button>
                                <button
                                    class="flex items-center gap-4 p-4 text-[11px] font-bold text-slate-500 uppercase bg-slate-50/50 hover:bg-amber-50 hover:text-amber-600 rounded-xl">
                                    <i class="fa-solid fa-graduation-cap text-slate-400"></i> Our Courses
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Facilities -->
                    <div class="border-b border-slate-100 pb-2">
                        <button onclick="toggleAccordion('mobFacilities')"
                            class="w-full flex items-center justify-between py-4 text-sm font-black text-slate-700 uppercase tracking-tight hover:text-amber-600">
                            <span class="flex items-center gap-3">
                                <span id="dot-mobFacilities" class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                                Facilities
                            </span>
                            <i id="icon-mobFacilities"
                                class="fa-solid fa-chevron-down text-slate-300 transition-transform"></i>
                        </button>

                        <div id="mobFacilities" class="overflow-hidden transition-all duration-500 max-h-0 opacity-0">
                            <div class="grid gap-2 pt-2">
                                <button
                                    class="flex items-center gap-4 p-4 text-[11px] font-bold text-slate-500 uppercase bg-slate-50/50 hover:bg-amber-50 hover:text-amber-600 rounded-xl">
                                    <i class="fa-solid fa-book-open text-slate-400"></i> Library
                                </button>
                                <button
                                    class="flex items-center gap-4 p-4 text-[11px] font-bold text-slate-500 uppercase bg-slate-50/50 hover:bg-amber-50 hover:text-amber-600 rounded-xl">
                                    <i class="fa-solid fa-users text-slate-400"></i> Seminar Hall
                                </button>
                                <button
                                    class="flex items-center gap-4 p-4 text-[11px] font-bold text-slate-500 uppercase bg-slate-50/50 hover:bg-amber-50 hover:text-amber-600 rounded-xl">
                                    <i class="fa-solid fa-bolt text-slate-400"></i> Computer Labs
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Student -->
                    <div class="border-b border-slate-100 pb-2">
                        <button onclick="toggleAccordion('mobStudent')"
                            class="w-full flex items-center justify-between py-4 text-sm font-black text-slate-700 uppercase tracking-tight hover:text-amber-600">
                            <span class="flex items-center gap-3">
                                <span id="dot-mobStudent" class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                                Student Corner
                            </span>
                            <i id="icon-mobStudent"
                                class="fa-solid fa-chevron-down text-slate-300 transition-transform"></i>
                        </button>

                        <div id="mobStudent" class="overflow-hidden transition-all duration-500 max-h-0 opacity-0">
                            <div class="grid gap-2 pt-2">
                                <button
                                    class="flex items-center gap-4 p-4 text-[11px] font-bold text-slate-500 uppercase bg-slate-50/50 hover:bg-amber-50 hover:text-amber-600 rounded-xl">
                                    <i class="fa-solid fa-file-lines text-slate-400"></i> Subject Notes
                                </button>
                                <button
                                    class="flex items-center gap-4 p-4 text-[11px] font-bold text-slate-500 uppercase bg-slate-50/50 hover:bg-amber-50 hover:text-amber-600 rounded-xl">
                                    <i class="fa-solid fa-layer-group text-slate-400"></i> Syllabus
                                </button>
                                <button
                                    class="flex items-center gap-4 p-4 text-[11px] font-bold text-slate-500 uppercase bg-slate-50/50 hover:bg-amber-50 hover:text-amber-600 rounded-xl">
                                    <i class="fa-solid fa-camera text-slate-400"></i> Photo Gallery
                                </button>
                                <button
                                    class="flex items-center gap-4 p-4 text-[11px] font-bold text-slate-500 uppercase bg-slate-50/50 hover:bg-amber-50 hover:text-amber-600 rounded-xl">
                                    <i class="fa-solid fa-circle-question text-slate-400"></i> Help
                                </button>
                            </div>
                        </div>
                    </div>

                    <button
                        class="w-full text-left text-sm font-black text-slate-700 uppercase tracking-tight py-5 border-b border-slate-100">Admissions</button>
                    <button
                        class="w-full text-left text-sm font-black text-slate-700 uppercase tracking-tight py-5 border-b border-slate-100">Photo
                        Gallery</button>

                </div>

                <button
                    class="mt-10 w-full py-5 bg-slate-900 text-white font-black rounded-xl uppercase tracking-[0.2em] text-[10px] shadow-2xl active:scale-95 transition-all">
                    Report Incident
                </button>

            </div>
        </div>
    </div>


    


    <main class="">