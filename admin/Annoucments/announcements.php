<?php
include 'protect.php';
include '../db.php';
session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 p-6">

<h1 class="text-2xl font-bold mb-4">Announcements</h1>

<a href="add-announcement.php" class="bg-amber-500 text-white px-4 py-2 rounded-lg shadow hover:scale-105 transition">
  + Add Announcement
</a>

<table class="w-full mt-6 bg-white rounded-xl overflow-hidden shadow-lg">
<tr class="bg-slate-200">
  <th class="p-3">Title</th>
  <th>Badge</th>
  <th>PDF</th>
  <th>Action</th>
</tr>

<?php
$res = $conn->query("SELECT * FROM announcements ORDER BY id DESC");
while($row = $res->fetch_assoc()):
?>
<tr class="border-t hover:bg-slate-50 transition">
  <td class="p-3"><?= $row['title'] ?></td>
  <td><?= $row['badge'] ?></td>
  <td>
    <a href="../uploads/<?= $row['pdf'] ?>" target="_blank" class="text-blue-600 underline">View</a>
  </td>
  <td>
    <a href="edit-announcement.php?id=<?= $row['id'] ?>" class="text-green-600 mr-3">Edit</a>
    <a href="delete-announcement.php?id=<?= $row['id'] ?>" 
       onclick="return confirm('Delete this?')" 
       class="text-red-600">Delete</a>
  </td>
</tr>
<?php endwhile; ?>
</table>

<?php if(isset($_SESSION['success'])): ?>
<script>
alert("<?= $_SESSION['success'] ?>");
</script>
<?php unset($_SESSION['success']); endif; ?>

</body>
</html>
