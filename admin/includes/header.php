<?php
require_once __DIR__ . '/../../includes/auth_helper.php';
require_once __DIR__ . '/functions.php';

// If not logged in as admin, redirect to login
checkRole(['admin', 'superadmin']);

// Get active page for sidebar highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIT Admin | College Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            background-image: radial-gradient(at 0% 0%, rgba(245, 158, 11, 0.05) 0, transparent 50%), 
                              radial-gradient(at 50% 0%, rgba(59, 130, 246, 0.05) 0, transparent 50%);
        }
        .glass-sidebar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        .nav-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-link.active {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 10px 15px -3px rgba(217, 119, 6, 0.3);
        }
        .nav-link:not(.active):hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            transform: translateX(5px);
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }
        .bento-card {
            background: white;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="flex">
        <!-- Modern Sidebar -->
        <aside class="w-72 glass-sidebar min-h-screen text-slate-400 p-8 flex flex-col fixed h-full z-50">
            <div class="flex items-center gap-4 mb-12">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-lg shadow-amber-500/20">M</div>
                <div>
                    <h1 class="text-white font-black text-lg tracking-tight leading-none">MIT Portal</h1>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Administrator</p>
                </div>
            </div>

            <nav class="flex-1 space-y-2">
                <p class="text-[10px] font-black text-slate-600 uppercase tracking-[0.2em] mb-4 px-4">Main Menu</p>
                
                <a href="/mit-college/admin/index.php" class="nav-link flex items-center gap-4 px-5 py-4 rounded-2xl font-bold text-sm <?= $current_page == 'index.php' ? 'active' : '' ?>">
                    <i class="fas fa-grid-2 text-lg"></i> Overview
                </a>
                
                <a href="/mit-college/admin/manage-faculty.php" class="nav-link flex items-center gap-4 px-5 py-4 rounded-2xl font-bold text-sm <?= $current_page == 'manage-faculty.php' ? 'active' : '' ?>">
                    <i class="fas fa-user-graduate text-lg"></i> Faculty Mgmt
                </a>

                <a href="/mit-college/admin/approve-content.php" class="nav-link flex items-center gap-4 px-5 py-4 rounded-2xl font-bold text-sm <?= $current_page == 'approve-content.php' ? 'active' : '' ?>">
                    <i class="fas fa-shield-check text-lg"></i> Approvals
                </a>

                <p class="text-[10px] font-black text-slate-600 uppercase tracking-[0.2em] mt-8 mb-4 px-4">Communication</p>

                <a href="/mit-college/admin/pages/announcements.php" class="nav-link flex items-center gap-4 px-5 py-4 rounded-2xl font-bold text-sm <?= $current_page == 'announcements.php' ? 'active' : '' ?>">
                    <i class="fas fa-megaphone text-lg"></i> Announcements
                </a>

                <a href="/mit-college/admin/pages/admin_events.php" class="nav-link flex items-center gap-4 px-5 py-4 rounded-2xl font-bold text-sm <?= $current_page == 'admin_events.php' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-star text-lg"></i> Events
                </a>

                <a href="/mit-college/admin/pages/gallery.php" class="nav-link flex items-center gap-4 px-5 py-4 rounded-2xl font-bold text-sm <?= $current_page == 'gallery.php' ? 'active' : '' ?>">
                    <i class="fas fa-images text-lg"></i> Gallery
                </a>
            </nav>

            <div class="mt-auto pt-8 border-t border-white/5">
                <a href="/mit-college/auth/logout.php" class="flex items-center gap-4 px-5 py-4 text-rose-400 hover:bg-rose-400/10 rounded-2xl font-bold text-sm transition-all">
                    <i class="fas fa-power-off"></i> Logout System
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 ml-72 p-12">
