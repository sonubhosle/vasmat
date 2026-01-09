<?php
include 'includes/header.php';
include '../mit-admin/includes/db.php';
?>
<h2 class="text-3xl font-bold mb-4">Notices & Announcements</h2>
<div class="space-y-4">
    <?php
    $res = $conn->query("SELECT * FROM notices ORDER BY id DESC");
    while($row = $res->fetch_assoc()){
        echo "<div class='bg-white p-4 rounded shadow'>
                <h3 class='text-xl font-bold mb-1'>{$row['title']}</h3>
                <p class='text-gray-700 mb-1'>{$row['description']}</p>";
        if($row['file']){
            echo "<a href='../assets/uploads/{$row['file']}' class='text-blue-600 hover:underline' download>Download File</a>";
        }
        echo "</div>";
    }
    ?>
</div>
<?php include 'includes/footer.php'; ?>
