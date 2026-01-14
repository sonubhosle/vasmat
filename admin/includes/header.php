<?php
session_start();
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ADMIN MIT COLLEGE</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/styles.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
 <style>
  /* Custom scrollbar for webkit */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: #fbbf24;
  border-radius: 20px;

}

::-webkit-scrollbar-thumb:hover {
  background: #f8b91b;
}
 </style>
<body class="bg-gray-50  ">

<!-- Sidebar -->

 <div class="flex ">
    
<aside class="sticky top-0 w-64 z-100 bg-gradient-to-b from-slate-800 to-slate-900 text-white min-h-screen  relative">
  <div class="flex items-center gap-3 mb-2 border-b border-white/20 p-5">
    <div class="w-12 h-12 rounded-full border-2 border-white/20 bg-white/10 flex items-center justify-center text-2xl">
      <i class="fa-slab-press fa-regular fa-user"></i>
    </div>
    <div>
      <h3 class="font-bold text-lg"><?= htmlspecialchars($admin_name) ?></h3>
      <p class="text-sm text-gray-200">Administrator</p>
    </div>
  </div>

  <nav class="space-y-2 overflow-y-auto h-[calc(90vh-150px)]  ">
    <a href="/mit-college/admin/index.php" data-page="dashboard" class="menu-item flex items-center gap-3 px-6 py-2 hover:text-amber-400  transition ease-in duration-200">
      <i class="fas fa-chart-line"></i> Dashboard
    </a>
    <a href="/mit-college/admin/pages/courses.php" data-page="courses" class="menu-item flex items-center gap-3 px-6 py-2 hover:text-amber-400 transition ease-in duration-200">
      <i class="fas fa-book"></i> Courses
    </a>
    <a href="/mit-college/admin/pages/faculty.php" data-page="faculty" class="menu-item flex items-center gap-3 px-6 py-2 hover:text-amber-400 transition ease-in duration-200">
      <i class="fas fa-chalkboard-teacher"></i> Faculty
    </a>
    <a href="/mit-college/admin/pages/notices.php" data-page="notices" class="menu-item flex items-center gap-3 px-6 py-2 hover:text-amber-400 transition ease-in duration-200">
       <i class="fa-solid fa-clipboard-list"></i> Notices
    </a>
    <a href="/mit-college/admin/pages/gallery.php" data-page="gallery" class="menu-item flex items-center gap-3 px-6 py-2 hover:text-amber-400 transition ease-in duration-200">
      <i class="fas fa-images"></i> Gallery
    </a>
     <a href="/mit-college/admin/pages/announcements.php" data-page="announcements" class="menu-item flex items-center gap-3 px-6 py-2 hover:text-amber-400 transition ease-in duration-200">
      <i class="fas fa-bullhorn"></i> Announcements
    </a>
    <a href="/mit-college/admin/pages/admin_events.php" data-page="events" class="menu-item flex items-center gap-3 px-6 py-2 hover:text-amber-400 transition ease-in duration-200">
      <i class="fa-slab-press fa-regular fa-newspaper"></i> Events
    </a>
  
  
  </nav>
    <div class="w-full p-5 absolute left-0 bottom-0 flex items-center gap-3  border-t border-white/20 pb-5">
      <a href="logout.php" class="w-full flex items-center gap-3 p-3 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600  text-white  justify-center hover:from-amber-600 hover:to-amber-400 transition ease-in duration-200">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
     </div>
</aside>

<div class="main px-7 py-5 overflow-y-auto w-full">



