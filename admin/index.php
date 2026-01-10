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
  <title>MIT College Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-gray-100 flex">

<!-- Sidebar -->
<aside class="fixed top-0 w-64 bg-gradient-to-b from-slate-800 to-slate-900 text-white min-h-screen p-5 relative">
  <div class="flex items-center gap-3 mb-8 border-b border-white/20 pb-5">
    <div class="w-12 h-12 rounded-full border-2 border-white/20 bg-white/10 flex items-center justify-center text-2xl">
      <i class="fa-slab-press fa-regular fa-user"></i>
    </div>
    <div>
      <h3 class="font-bold text-lg"><?= htmlspecialchars($admin_name) ?></h3>
      <p class="text-sm text-gray-200">Administrator</p>
    </div>
  </div>

  <nav class="space-y-2">
    <a href="?page=dashboard" data-page="dashboard" class="menu-item flex items-center gap-3 p-3  transition ease-in duration-200">
      <i class="fas fa-chart-line"></i> Dashboard
    </a>

    <a href="?page=courses" data-page="courses" class="menu-item flex items-center gap-3 p-3  transition ease-in duration-200">
      <i class="fas fa-book"></i> Courses
    </a>

    <a href="?page=faculty" data-page="faculty" class="menu-item flex items-center gap-3 p-3  transition ease-in duration-200">
      <i class="fas fa-chalkboard-teacher"></i> Faculty
    </a>

    <a href="?page=notices" data-page="notices" class="menu-item flex items-center gap-3 p-3  transition ease-in duration-200">
       <i class="fa-solid fa-clipboard-list"></i> Notices
    </a>
 <a href="?page=gallery" data-page="gallery" class="menu-item flex items-center gap-3 p-3  transition ease-in duration-200">
      <i class="fas fa-images"></i> Gallery
    </a>
     <a href="?page=announcements" data-page="announcements" class="menu-item flex items-center gap-3 p-3  transition ease-in duration-200">
      <i class="fas fa-bullhorn"></i> Announcements
    </a>
    <a href="?page=news" data-page="news" class="menu-item flex items-center gap-3 p-3  transition ease-in duration-200">
      <i class="fa-slab-press fa-regular fa-newspaper"></i> News
    </a>
  <div class="w-full p-5 absolute left-0 bottom-0 flex items-center gap-3 mb-8 border-t border-white/20 pb-5">
      <a href="logout.php" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-rose-500 hover:text-white text-rose-500 transition ease-in duration-200 justify-center border border-rose-500">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>
  
  </nav>
</aside>

<!-- Main -->
<div class="flex-1">



<div id="loader" class="hidden fixed inset-0 bg-black/30 flex items-center justify-center z-50">
  <div class="bg-white p-5 rounded-lg shadow flex items-center gap-3">
    <i class="fas fa-spinner fa-spin text-blue-600"></i> Loading...
  </div>
</div>

<main id="content" class="p-6"></main>

</div>

<script>
const content = document.getElementById("content");
const loader = document.getElementById("loader");

function loadPage(page, push = true) {
  loader.classList.remove("hidden");

  fetch(`load.php?page=${page}`)
    .then(res => res.text())
    .then(data => {
      content.innerHTML = data;
      loader.classList.add("hidden");

      document.querySelectorAll('.menu-item').forEach(item => {
        item.classList.remove('text-amber-500');
        if(item.dataset.page === page){
          item.classList.add('text-amber-500');
        }
      });

      if(push) history.pushState({page}, "", `?page=${page}`);
    });
}

document.querySelectorAll('.menu-item').forEach(item => {
  item.addEventListener("click", function(e){
    e.preventDefault();
    loadPage(this.dataset.page);
  });
});

window.onpopstate = function(e){
  if(e.state?.page){
    loadPage(e.state.page, false);
  }
};

const urlParams = new URLSearchParams(window.location.search);
const startPage = urlParams.get('page') || 'dashboard';
loadPage(startPage, false);
</script>

</body>
</html>
