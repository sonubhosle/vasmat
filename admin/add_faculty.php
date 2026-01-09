<?php
include 'includes/header.php';
include 'includes/db.php';

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $designation = $_POST['designation'];
    $department = $_POST['department'];

    // Photo upload
    $photo = $_FILES['photo']['name'];
    $tmp = $_FILES['photo']['tmp_name'];
    $ext = pathinfo($photo, PATHINFO_EXTENSION);
    $photo_name = time()."_faculty.".$ext;
    move_uploaded_file($tmp, "../assets/uploads/".$photo_name);

    $stmt = $conn->prepare("INSERT INTO faculty (name, designation, department, photo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $designation, $department, $photo_name);
    if($stmt->execute()){
        echo "<p class='text-green-500'>Faculty added successfully!</p>";
    } else {
        echo "<p class='text-red-500'>Error adding faculty!</p>";
    }
}
?>

<h2 class="text-2xl font-bold mb-4">Add Faculty</h2>

<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
    <input type="text" name="name" placeholder="Name" class="w-full p-2 mb-4 border rounded" required>
    <input type="text" name="designation" placeholder="Designation" class="w-full p-2 mb-4 border rounded" required>
    <input type="text" name="department" placeholder="Department" class="w-full p-2 mb-4 border rounded" required>
    <input type="file" name="photo" class="w-full p-2 mb-4 border rounded" required>
    <button type="submit" name="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Faculty</button>
</form>

<?php include 'includes/footer.php'; ?>
