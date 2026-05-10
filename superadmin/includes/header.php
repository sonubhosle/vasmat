<?php
require_once __DIR__ . '/../../includes/auth_helper.php';

// If not logged in as superadmin, redirect
checkRole('superadmin');

// Get active page for sidebar highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIT SuperAdmin | Root Control Center</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    
    <!-- Icons & Styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                        jakarta: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        },
                        secondary: '#0f172a',
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --primary-glow: rgba(245, 158, 11, 0.1);
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(245, 158, 11, 0.03) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(59, 130, 246, 0.02) 0px, transparent 50%);
            min-height: 100vh;
            color: #0f172a;
        }
        
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        }

        .sidebar-transition {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .nav-link-active {
            background: linear-gradient(90deg, rgba(245, 158, 11, 0.15) 0%, rgba(245, 158, 11, 0) 100%);
            border-left: 3px solid #f59e0b;
        }

        .stat-card {
            background: #ffffff;
            padding: 1.75rem;
            border-radius: 2.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.02);
            transition: all 0.5s ease;
        }
        .stat-card:hover {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.06);
            transform: translateY(-6px);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

        @media (max-width: 768px) {
            .sidebar-hidden { transform: translateX(-100%); }
            .sidebar-visible { transform: translateX(0); }
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden selection:bg-primary-500/30 selection:text-white">

    <!-- Mobile Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[40] opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"></div>

    <!-- Sidebar Wrapper -->
    <aside id="admin-sidebar" class="fixed top-0 left-0 h-screen w-[280px] bg-slate-950 z-[50] sidebar-transition sidebar-hidden md:translate-x-0 border-r border-white/5">
        <div class="h-full flex flex-col p-8">
            <!-- Logo Section -->
            <div class="flex items-center gap-4 mb-12">
                <div class="w-12 h-12 bg-gradient-to-tr from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center shadow-xl shadow-amber-600/30">
                    <i class="fas fa-shield-halved text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-white font-black text-xl tracking-tight leading-none">ROOT</h1>
                    <p class="text-[10px] font-bold text-amber-500 uppercase tracking-[0.3em] mt-1.5">Master Control</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 space-y-8 overflow-y-auto pr-2 custom-scrollbar">
                <!-- Group 1: System -->
                <div>
                    <h3 class="text-[10px] font-black text-slate-600 uppercase tracking-[0.2em] mb-4 px-4">Core Terminal</h3>
                    <nav class="space-y-1">
                        <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all group <?= $current_page == 'dashboard.php' ? 'nav-link-active text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
                            <span class="w-6 h-6 rounded-xl flex items-center justify-center flex-shrink-0 bg-amber-500/20 text-amber-400 group-hover:bg-amber-500 group-hover:text-white transition-all">
                                <i class="fas fa-chart-line text-xs"></i>
                            </span>
                            <span>System Overview</span>
                        </a>
                        <a href="manage-admins.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all group <?= $current_page == 'manage-admins.php' ? 'nav-link-active text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
                            <span class="w-6 h-6 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-500/20 text-blue-400 group-hover:bg-blue-500 group-hover:text-white transition-all">
                                <i class="fas fa-user-shield text-xs"></i>
                            </span>
                            <span>Manage Admins</span>
                        </a>
                        <a href="faculty-approvals.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all group <?= $current_page == 'faculty-approvals.php' ? 'nav-link-active text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
                            <span class="w-6 h-6 rounded-xl flex items-center justify-center flex-shrink-0 bg-emerald-500/20 text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                                <i class="fas fa-users-cog text-xs"></i>
                            </span>
                            <span>Faculty Approvals</span>
                        </a>
                    </nav>
                </div>

                <!-- Group 2: Security -->
                <div>
                    <h3 class="text-[10px] font-black text-slate-600 uppercase tracking-[0.2em] mb-4 px-4">System Assets</h3>
                    <nav class="space-y-1">
                        <a href="system-logs.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all group <?= $current_page == 'system-logs.php' ? 'nav-link-active text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
                            <span class="w-6 h-6 rounded-xl flex items-center justify-center flex-shrink-0 bg-purple-500/20 text-purple-400 group-hover:bg-purple-500 group-hover:text-white transition-all">
                                <i class="fas fa-terminal text-xs"></i>
                            </span>
                            <span>Activity Logs</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="mt-auto pt-8 border-t border-white/5">
                <a href="../auth/superadmin-logout.php" class="flex items-center gap-3 px-5 py-4 rounded-2xl text-xs font-black uppercase tracking-widest text-rose-400 bg-rose-500/5 hover:bg-rose-500 hover:text-white transition-all shadow-lg shadow-rose-500/0 hover:shadow-rose-500/20">
                    <i class="fas fa-power-off"></i>
                    <span>Terminate Session</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="md:pl-[280px] w-full min-h-screen flex flex-col transition-all duration-300">
        
        <!-- Top Navigation -->
        <header class="sticky top-0 z-[30] glass px-8 py-5 flex items-center justify-between w-full">
            <div class="flex items-center gap-8 flex-1">
                <!-- Mobile Toggle -->
                <button id="sidebar-toggle" class="md:hidden w-12 h-12 flex items-center justify-center bg-slate-800 border border-slate-700 rounded-2xl text-slate-400 shadow-sm hover:bg-slate-700 transition-all active:scale-95">
                    <i class="fas fa-bars-staggered text-lg"></i>
                </button>
            </div>

            <!-- Profile & Quick Actions -->
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-4 pl-6 border-l border-slate-200">
                    <div class="hidden sm:flex flex-col items-end">
                        <p class="text-sm font-black text-slate-900 leading-none mb-1"><?= e($_SESSION['user_name']) ?></p>
                        <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest bg-amber-500/10 px-2 py-0.5 rounded-md border border-amber-500/20">Root Authority</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center text-white text-sm font-black ring-4 ring-white shadow-xl">
                        <?= strtoupper(substr($_SESSION['user_name'], 0, 2)) ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 p-8">
