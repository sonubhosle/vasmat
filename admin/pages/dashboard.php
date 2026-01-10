<?php include 'includes/db.php'; ?>

<h2 class="text-2xl font-bold mb-6">Dashboard</h2>

<div class="grid grid-cols-4 gap-4">
    <?php
    $tables = ['courses','faculty','notices','gallery','messages'];
    foreach($tables as $table){
        $count = $conn->query("SELECT COUNT(*) as total FROM $table")->fetch_assoc()['total'];
        echo "<div class='bg-white p-4 rounded shadow text-center'>
                <h3 class='text-xl font-bold mb-2'>$table</h3>
                <p class='text-2xl'>$count</p>
              </div>";
    }
    ?>
</div>

