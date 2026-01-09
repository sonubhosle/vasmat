<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<h2 class="text-2xl font-bold mb-4">Courses</h2>
<a href="add_course.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mb-4 inline-block">Add Course</a>

<table class="min-w-full bg-white">
    <thead class="bg-gray-200">
        <tr>
            <th class="py-2 px-4 border">ID</th>
            <th class="py-2 px-4 border">Name</th>
            <th class="py-2 px-4 border">Description</th>
            <th class="py-2 px-4 border">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $res = $conn->query("SELECT * FROM courses");
        while($row = $res->fetch_assoc()){
            echo "<tr>
                    <td class='border px-4 py-2'>{$row['id']}</td>
                    <td class='border px-4 py-2'>{$row['course_name']}</td>
                    <td class='border px-4 py-2'>{$row['description']}</td>
                    <td class='border px-4 py-2'>
                        <a href='edit_course.php?id={$row['id']}' class='text-blue-600 mr-2'>Edit</a>
                        <a href='delete_course.php?id={$row['id']}' class='text-red-600'>Delete</a>
                    </td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>
