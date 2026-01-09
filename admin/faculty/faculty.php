<?php
include 'includes/header.php';
include 'includes/db.php';
?>

<h2 class="text-2xl font-bold mb-4">Faculty</h2>
<a href="add_faculty.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mb-4 inline-block">Add Faculty</a>

<table class="min-w-full bg-white">
    <thead class="bg-gray-200">
        <tr>
            <th class="py-2 px-4 border">ID</th>
            <th class="py-2 px-4 border">Photo</th>
            <th class="py-2 px-4 border">Name</th>
            <th class="py-2 px-4 border">Designation</th>
            <th class="py-2 px-4 border">Department</th>
            <th class="py-2 px-4 border">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $res = $conn->query("SELECT * FROM faculty");
        while($row = $res->fetch_assoc()){
            echo "<tr>
                    <td class='border px-4 py-2'>{$row['id']}</td>
                    <td class='border px-4 py-2'><img src='../assets/uploads/{$row['photo']}' class='w-16 h-16 object-cover rounded'></td>
                    <td class='border px-4 py-2'>{$row['name']}</td>
                    <td class='border px-4 py-2'>{$row['designation']}</td>
                    <td class='border px-4 py-2'>{$row['department']}</td>
                    <td class='border px-4 py-2'>
                        <a href='edit_faculty.php?id={$row['id']}' class='text-blue-600 mr-2'>Edit</a>
                        <a href='delete_faculty.php?id={$row['id']}' class='text-red-600'>Delete</a>
                    </td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>
