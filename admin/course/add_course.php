<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<h2 class="text-2xl font-bold mb-4">Add Course</h2>

<?php
if(isset($_POST['submit'])){
    $name = $_POST['course_name'];
    $desc = $_POST['description'];
    $stmt = $conn->prepare("INSERT INTO courses (course_name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $desc);
    if($stmt->execute()){
        echo "<p class='text-green-500'>Course added successfully!</p>";
    } else {
        echo "<p class='text-red-500'>Error adding course!</p>";
    }
}
?>

<form method="post" class="bg-white p-4 rounded shadow">
    <input type="text" name="course_name" placeholder="Course Name" class="w-full p-2 mb-4 border rounded" required>
    <textarea name="description" placeholder="Description" class="w-full p-2 mb-4 border rounded" required></textarea>
    <button type="submit" name="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Course</button>
</form>

<?php include 'includes/footer.php'; ?>
