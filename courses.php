<?php
include 'includes/header.php';
include '../mit-admin/includes/db.php'; // For DB connection
?>
<h2 class="text-3xl font-bold mb-4">Our Courses</h2>
<div class="grid grid-cols-2 md:grid-cols-3 gap-6">
    <?php
    $res = $conn->query("SELECT * FROM courses ORDER BY id DESC");
    while($row = $res->fetch_assoc()){
        echo "<div class='bg-white p-4 rounded shadow'>
                <h3 class='text-xl font-bold mb-2'>{$row['course_name']}</h3>
                <p class='text-gray-700'>{$row['description']}</p>
              </div>";
    }
    ?>
</div>
<?php include 'includes/footer.php'; ?>
