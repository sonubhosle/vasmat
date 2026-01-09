<?php
session_start();
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-600 p-4 text-white flex justify-between items-center">
    <h1 class="font-bold text-xl">MIT College Admin</h1>
    <div>
        <a href="dashboard.php" class="px-3 hover:underline">Dashboard</a>
        <a href="courses.php" class="px-3 hover:underline">Courses</a>
        <a href="faculty.php" class="px-3 hover:underline">Faculty</a>
        <a href="notices.php" class="px-3 hover:underline">Notices</a>
        <a href="gallery.php" class="px-3 hover:underline">Gallery</a>
        <a href="messages.php" class="px-3 hover:underline">Messages</a>
        <a href="logout.php" class="px-3 hover:underline">Logout</a>
    </div>
</nav>
<div class="p-6">
