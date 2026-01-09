<?php
include 'protect.php';
include '../db.php';
session_start();

if(isset($_POST['save'])){
    $title = $_POST['title'];
    $badge = $_POST['badge'];

    $pdf = time().$_FILES['pdf']['name'];
    move_uploaded_file($_FILES['pdf']['tmp_name'], "../uploads/".$pdf);

    $conn->query("INSERT INTO announcements (title,badge,pdf) VALUES ('$title','$badge','$pdf')");

    $_SESSION['success'] = "Announcement Added Successfully!";
    header("Location: announcements.php");
}
?>

<!DOCTYPE html>
<html>
<head>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 p-6">

<h1 class="text-2xl font-bold mb-4">Add Announcement</h1>

<form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow-lg w-96">
  <input type="text" name="title" placeholder="Title" required class="w-full p-2 border rounded mb-3">

  <select name="badge" class="w-full p-2 border rounded mb-3">
    <option>NEW</option>
    <option>EVENT</option>
    <option>NOTICE</option>
    <option>INFO</option>
  </select>

  <input type="file" name="pdf" required class="mb-3">

  <button name="save" class="bg-amber-500 text-white px-4 py-2 rounded hover:scale-105 transition">
    Save
  </button>
</form>

</body>
</html>
