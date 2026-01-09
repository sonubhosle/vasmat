<?php
include 'includes/header.php';
include 'includes/db.php';

$id = $_GET['id'];
$faculty = $conn->query("SELECT * FROM faculty WHERE id=$id")->fetch_assoc();

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $designation = $_POST['designation'];
    $department = $_POST['department'];

    if($_FILES['photo']['name']){
        $photo = $_FILES['photo']['name'];
        $tmp = $_FILES['photo']['tmp_name'];
        $ext = pathinfo($photo, PATHINFO_EXTENSION);
        $photo_name = time()."_faculty.".$ext;
        move_uploaded_file($tmp, "../assets/uploads/".$photo_name);
        $stmt = $conn->prepare("UPDATE faculty SET name=?, designation=?, department=?, photo=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $designation, $department, $photo_name, $id);
    } else {
        $stmt = $conn->prepare("UPDATE faculty SET name=?, designation=?, department=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $designation, $department, $id);
    }

    if($stmt->execute()){
        echo "<p class='text-green-500'>Faculty updated successfully!</p>";
    } else {
        echo "<p class='text-red-500'>Error updating faculty!</p>";
    }
}
?>

<h2 class="text-2xl font-bold mb-4">Edit Faculty</h2>

<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
    <input type="text" name="name" value="<?php echo $faculty['name']; ?>" class="w-full p-2 mb-4 border rounded" required>
    <input type="text" name="designation" value="<?php echo $faculty['designation']; ?>" class="w-full p-2 mb-4 border rounded" required>
    <input type="text" name="department" value="<?php echo $faculty['department']; ?>" class="w-full p-2 mb-4 border rounded" required>
    <p>Current Photo:</p>
    <img src="../assets/uploads/<?php echo $faculty['photo']; ?>" class="w-24 h-24 mb-4 rounded">
    <input type="file" name="photo" class="w-full p-2 mb-4 border rounded">
    <button type="submit" name="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Faculty</button>
</form>

<?php include 'includes/footer.php'; ?>
