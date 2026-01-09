<?php
include 'protect.php';
include '../db.php';
session_start();

$id = $_GET['id'];
$data = $conn->query("SELECT * FROM announcements WHERE id=$id")->fetch_assoc();

if(isset($_POST['update'])){
    $title = $_POST['title'];
    $badge = $_POST['badge'];

    if($_FILES['pdf']['name']){
        $pdf = time().$_FILES['pdf']['name'];
        move_uploaded_file($_FILES['pdf']['tmp_name'], "../uploads/".$pdf);
        $conn->query("UPDATE announcements SET title='$title', badge='$badge', pdf='$pdf' WHERE id=$id");
    } else {
        $conn->query("UPDATE announcements SET title='$title', badge='$badge' WHERE id=$id");
    }

    $_SESSION['success'] = "Announcement Updated!";
    header("Location: announcements.php");
}
?>

<!DOCTYPE html>
<html>
<head>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 p-6">

<h1 class="text-2xl font-bold mb-4">Edit Announcement</h1>

<form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow-lg w-96">
  <input type="text" name="title" value="<?= $data['title'] ?>" required class="w-full p-2 border rounded mb-3">

  <select name="badge" class="w-full p-2 border rounded mb-3">
    <option <?= $data['badge']=="NEW"?'selected':'' ?>>NEW</option>
    <option <?= $data['badge']=="EVENT"?'selected':'' ?>>EVENT</option>
    <option <?= $data['badge']=="NOTICE"?'selected':'' ?>>NOTICE</option>
    <option <?= $data['badge']=="INFO"?'selected':'' ?>>INFO</option>
  </select>

  <input type="file" name="pdf" class="mb-3">

  <button name="update" class="bg-green-500 text-white px-4 py-2 rounded hover:scale-105 transition">
    Update
  </button>
</form>

</body>
</html>
