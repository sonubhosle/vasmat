<?php
include 'protect.php';
include '../db.php';
session_start();

$id = $_GET['id'];
$data = $conn->query("SELECT * FROM news WHERE id=$id")->fetch_assoc();

if(isset($_POST['update'])){
    $title = $_POST['title'];
    $date = $_POST['event_date'];

    if($_FILES['pdf']['name']){
        $pdf = time().$_FILES['pdf']['name'];
        move_uploaded_file($_FILES['pdf']['tmp_name'], "../uploads/".$pdf);
        $conn->query("UPDATE news SET title='$title', event_date='$date', pdf='$pdf' WHERE id=$id");
    } else {
        $conn->query("UPDATE news SET title='$title', event_date='$date' WHERE id=$id");
    }

    $_SESSION['success'] = "News Updated!";
    header("Location: news.php");
}
?>

<!DOCTYPE html>
<html>
<head>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 p-6">

<h1 class="text-2xl font-bold mb-4">Edit News</h1>

<form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow-lg w-96">
  <input type="text" name="title" value="<?= $data['title'] ?>" required class="w-full p-2 border rounded mb-3">

  <input type="date" name="event_date" value="<?= $data['event_date'] ?>" required class="w-full p-2 border rounded mb-3">

  <input type="file" name="pdf" class="mb-3">

  <?php if($data['pdf']): ?>
    <p class="text-sm text-slate-500 mb-2">Current PDF: <?= $data['pdf'] ?></p>
  <?php endif; ?>

  <button name="update" class="bg-green-500 text-white px-4 py-2 rounded hover:scale-105 transition">
    Update
  </button>
</form>

</body>
</html>
