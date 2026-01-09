<?php
include 'protect.php';
include '../db.php';
session_start();

if(isset($_POST['save'])){
    $title = $_POST['title'];
    $date = $_POST['event_date'];

    $pdf = "";
    if($_FILES['pdf']['name']){
        $pdf = time().$_FILES['pdf']['name'];
        move_uploaded_file($_FILES['pdf']['tmp_name'], "../uploads/".$pdf);
    }

    $conn->query("INSERT INTO news (title, event_date, pdf) VALUES ('$title','$date','$pdf')");

    $_SESSION['success'] = "News Added Successfully!";
    header("Location: news.php");
}
?>

<!DOCTYPE html>
<html>
<head>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 p-6">

<h1 class="text-2xl font-bold mb-4">Add News</h1>

<form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow-lg w-96">
  <input type="text" name="title" placeholder="News Title" required class="w-full p-2 border rounded mb-3">

  <input type="date" name="event_date" required class="w-full p-2 border rounded mb-3">

  <input type="file" name="pdf" class="mb-3">

  <button name="save" class="bg-amber-500 text-white px-4 py-2 rounded hover:scale-105 transition">
    Save
  </button>
</form>

</body>
</html>
