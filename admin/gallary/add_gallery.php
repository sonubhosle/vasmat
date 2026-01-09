<?php
include 'includes/header.php';
include 'includes/db.php';

if(isset($_POST['submit'])){
    $caption = $_POST['caption'];
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $image_name = time()."_gallery.".$ext;
    move_uploaded_file($tmp, "../assets/uploads/".$image_name);

    $stmt = $conn->prepare("INSERT INTO gallery (image, caption) VALUES (?, ?)");
    $stmt->bind_param("ss", $image_name, $caption);
    if($stmt->execute()){
        echo "<p class='text-green-500'>Image added successfully!</p>";
    } else {
        echo "<p class='text-red-500'>Error adding image!</p>";
    }
}
?>

<h2 class="text-2xl font-bold mb-4">Add Gallery Image</h2>

<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow max-w-lg">
    <input type="text" name="caption" placeholder="Caption" class="w-full p-2 mb-4 border rounded" required>
    <input type="file" name="image" class="w-full p-2 mb-4 border rounded" required>
    <button type="submit" name="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Image</button>
</form>

<?php include 'includes/footer.php'; ?>
