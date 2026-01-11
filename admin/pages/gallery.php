<?php
include 'includes/header.php';
include 'includes/db.php';
?>

<h2 class="text-2xl font-bold mb-4">Gallery</h2>
<a href="add_gallery.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mb-4 inline-block">Add Image</a>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<?php
$res = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
while($row = $res->fetch_assoc()){
    echo "<div class='bg-white p-2 rounded shadow text-center'>
            <img src='../assets/uploads/{$row['image']}' class='w-full h-48 object-cover mb-2 rounded'>
            <p class='text-gray-700'>{$row['caption']}</p>
            <a href='edit_gallery.php?id={$row['id']}' class='text-blue-600 mr-2'>Edit</a>
            <a href='delete_gallery.php?id={$row['id']}' class='text-amber-600'>Delete</a>
          </div>";
}
?>
</div>

<?php include 'includes/footer.php'; ?>
