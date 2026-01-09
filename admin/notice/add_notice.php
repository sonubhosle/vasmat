<?php
include 'includes/header.php';
include 'includes/db.php';

if(isset($_POST['submit'])){
    $title = $_POST['title'];
    $description = $_POST['description'];

    // File upload
    $file_name = '';
    if($_FILES['file']['name']){
        $file = $_FILES['file']['name'];
        $tmp = $_FILES['file']['tmp_name'];
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $file_name = time()."_notice.".$ext;
        move_uploaded_file($tmp, "../assets/uploads/".$file_name);
    }

    $stmt = $conn->prepare("INSERT INTO notices (title, description, file) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $description, $file_name);
    if($stmt->execute()){
        echo "<p class='text-green-500'>Notice added successfully!</p>";
    } else {
        echo "<p class='text-red-500'>Error adding notice!</p>";
    }
}
?>

<h2 class="text-2xl font-bold mb-4">Add Notice</h2>

<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow max-w-lg">
    <input type="text" name="title" placeholder="Title" class="w-full p-2 mb-4 border rounded" required>
    <textarea name="description" placeholder="Description" class="w-full p-2 mb-4 border rounded" required></textarea>
    <input type="file" name="file" class="w-full p-2 mb-4 border rounded">
    <button type="submit" name="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Notice</button>
</form>

<?php include 'includes/footer.php'; ?>
