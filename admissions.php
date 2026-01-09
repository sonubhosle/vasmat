<?php include 'includes/header.php'; ?>

<h2 class="text-3xl font-bold mb-4">Admissions</h2>
<p class="text-gray-700 mb-4">
    To apply for admissions, please fill out the form below and we will contact you with further details.
</p>

<form class="bg-white p-4 rounded shadow max-w-lg">
    <input type="text" name="name" placeholder="Full Name" class="w-full p-2 mb-4 border rounded" required>
    <input type="email" name="email" placeholder="Email" class="w-full p-2 mb-4 border rounded" required>
    <input type="text" name="phone" placeholder="Phone Number" class="w-full p-2 mb-4 border rounded" required>
    <select name="course" class="w-full p-2 mb-4 border rounded" required>
        <option value="">Select Course</option>
        <?php
        include '../mit-admin/includes/db.php';
        $res = $conn->query("SELECT * FROM courses");
        while($row = $res->fetch_assoc()){
            echo "<option value='{$row['course_name']}'>{$row['course_name']}</option>";
        }
        ?>
    </select>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
</form>

<?php include 'includes/footer.php'; ?>
