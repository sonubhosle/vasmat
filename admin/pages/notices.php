<?php
include 'includes/header.php';
include 'includes/db.php';
?>

<h2 class="text-2xl font-bold mb-4">Notices</h2>
<a href="add_notice.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mb-4 inline-block">Add Notice</a>

<table class="min-w-full bg-white">
    <thead class="bg-gray-200">
        <tr>
            <th class="py-2 px-4 border">ID</th>
            <th class="py-2 px-4 border">Title</th>
            <th class="py-2 px-4 border">Description</th>
            <th class="py-2 px-4 border">File</th>
            <th class="py-2 px-4 border">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $res = $conn->query("SELECT * FROM notices ORDER BY id DESC");
        while($row = $res->fetch_assoc()){
            echo "<tr>
                    <td class='border px-4 py-2'>{$row['id']}</td>
                    <td class='border px-4 py-2'>{$row['title']}</td>
                    <td class='border px-4 py-2'>{$row['description']}</td>
                    <td class='border px-4 py-2'>";
            if($row['file']){
                echo "<a href='../assets/uploads/{$row['file']}' class='text-blue-600 hover:underline' download>Download</a>";
            } else {
                echo "No File";
            }
            echo "</td>
                    <td class='border px-4 py-2'>
                        <a href='edit_notice.php?id={$row['id']}' class='text-blue-600 mr-2'>Edit</a>
                        <a href='delete_notice.php?id={$row['id']}' class='text-amber-600'>Delete</a>
                    </td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>
