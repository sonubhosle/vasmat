<?php include 'includes/header.php'; ?>
<?php include '../mit-admin/includes/db.php'; ?>

<h2 class="text-3xl font-bold mb-4">Contact Us</h2>

<?php
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO messages (name,email,subject,message) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $name,$email,$subject,$message);
    if($stmt->execute()){
        echo "<p class='text-green-500 mb-4'>Message sent successfully!</p>";
    } else {
        echo "<p class='text-red-500 mb-4'>Error sending message.</p>";
    }
}
?>

<form method="post" class="bg-white p-4 rounded shadow max-w-lg">
    <input type="text" name="name" placeholder="Name" class="w-full p-2 mb-4 border rounded" required>
    <input type="email" name="email" placeholder="Email" class="w-full p-2 mb-4 border rounded" required>
    <input type="text" name="subject" placeholder="Subject" class="w-full p-2 mb-4 border rounded" required>
    <textarea name="message" placeholder="Message" class="w-full p-2 mb-4 border rounded" required></textarea>
    <button type="submit" name="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Send Message</button>
</form>

<?php include 'includes/footer.php'; ?>
