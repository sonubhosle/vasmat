<?php
include 'includes/header.php';
include '../mit-admin/includes/db.php';
?>
<h2 class="text-3xl font-bold mb-4">Gallery</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php
    $res = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
    while($row = $res->fetch_assoc()){
        echo "<div class='bg-white p-2 rounded shadow text-center'>
                <img src='../assets/uploads/{$row['image']}' class='w-full h-48 object-cover mb-2 rounded'>
                <p class='text-gray-700'>{$row['caption']}</p>
              </div>";
    }
    ?>
</div>
<?php include 'includes/footer.php'; ?>
