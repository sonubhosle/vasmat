<?php
include 'includes/header.php';
include 'includes/db.php';

$id = $_GET['id'];
$notice = $conn->query("SELECT * FROM notices WHERE id=$id")->fetch_assoc();

if(isset($_POST['submit'])){
    $title = $_POST['title'];
    $description = $_POST['description'];

    if($_FILES['file']['name']){
        $file = $_FILES['file']['name'];
        $tmp = $_FILES['file']['tmp_name'];
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $file_name = time()."_notice.".$ext;
        move_uploaded_file($tmp, "../assets/uploads/".$file_name);
        $stmt = $conn->prepare("UPDATE notices SET title=?, description=?, file=? WHERE id=?");
        $stmt->bind_param("sssi", $title, $description, $file_name, $id);
    } else {
        $stmt = $conn->prepare("UPDATE notices SET title=?, description=? WHERE id=?");
        $stmt->bind_param("ssi", $title, $description, $id);
    }

    if($stmt->execute()){
        echo "<p class='text-green-500'>Notice updated successfully!</p>";
    } else {
        echo "<p class='text-red-500'>Error updating notice!</p>";
    }
}
?>

<h2 class="text-2xl font-bold mb-4">Edit Notice</h2>

<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow max-w-lg">
    <input type="text" name="title" value="<?php echo $notice['title']; ?>" class="w-full p-2 mb-4 border rounded" required>
    <textarea name="description" class="w-full p-2 mb-4 border rounded" required><?php echo $notice['description']; ?></textarea>
    <?php if($notice['file']){ ?>
        <p>Current File: <a href="../assets/uploads/<?php echo $notice['file']; ?>" class="text-blue-600 hover:underline" download>Download</a></p>
    <?php } ?>
    <input type="file" name="file" class="w-full p-2 mb-4 border rounded">
    <button type="submit" name="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Notice</button>
</form>

<?php include 'includes/footer.php'; ?>
