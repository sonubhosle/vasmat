<?php
include 'includes/header.php';
include '../mit-admin/includes/db.php';
?>
<h2 class="text-3xl font-bold mb-4">Our Faculty</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php
    $res = $conn->query("SELECT * FROM faculty ORDER BY id DESC");
    while($row = $res->fetch_assoc()){
        echo "<div class='bg-white p-4 rounded shadow text-center'>
                <img src='../assets/uploads/{$row['photo']}' class='w-32 h-32 object-cover mx-auto rounded-full mb-2'>
                <h3 class='text-xl font-bold'>{$row['name']}</h3>
                <p class='text-gray-700'>{$row['designation']}</p>
                <p class='text-gray-500'>{$row['department']}</p>
              </div>";
    }
    ?>
</div>
<?php include 'includes/footer.php'; ?>
