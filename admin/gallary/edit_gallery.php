<?php
include 'includes/header.php';
include 'includes/db.php';

$id = $_GET['id'];
$gallery = $conn->query("SELECT * FROM gallery WHERE id=$id")->fetch_assoc();

if(isset($_POST['submit'])){
    $caption = $_POST['caption'];

    if($_FILES['image']['name']){
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $image_name = time()."_gallery.".$ext;
        move_uploaded_file($tmp, "../assets/uploads/".$image_name);
        $stmt = $conn->prepare("UPDATE gallery SET image=?, caption=? WHERE id=?");
        $stmt->bind_param("ssi", $image_name, $caption, $id);
    } else {
        $stmt = $conn->prepare("UPDATE gallery SET caption=? WHERE id=?");
        $stmt->bind_param("si", $caption, $id);
    }

    if($stmt->execute()){
        echo "<p class='text-green-500'>Gallery updated successfully!</p>";
    } else {
        echo "<p class='text-red-500'>Error updating gallery!</p>";
    }
}
?>

<h2 class="text-2xl font-bold mb-4">Edit Gallery Image</h2>

<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow max-w-lg">
    <input type="text" name="caption" value="<?php echo $gallery['caption']; ?>" class="w-full p-2 mb-4 border rounded" required>
    <p>Current Image:</p>
    <img src="../assets/uploads/<?php echo $gallery['image']; ?>" class="w-48 h-48 mb-4 rounded object-cover">
    <input type="file" name="image" class="w-full p-2 mb-4 border rounded">
    <button type="submit" name="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Image</button>
</form>

<?php include 'includes/footer.php'; ?>
