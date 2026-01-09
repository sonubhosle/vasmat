<?php
include 'includes/db.php';
session_start();

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if($admin && password_verify($password, $admin['password'])){
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        header("Location: dashboard.php");
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded shadow-md w-96">
        <h1 class="text-2xl font-bold mb-6 text-center">Admin Login</h1>
        <?php if(isset($error)) { echo "<p class='text-red-500 mb-4'>$error</p>"; } ?>
        <form method="post">
            <input type="email" name="email" placeholder="Email" required class="w-full p-2 mb-4 border rounded">
            <input type="password" name="password" placeholder="Password" required class="w-full p-2 mb-4 border rounded">
            <button type="submit" name="login" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Login</button>
        </form>
        <p class="mt-4 text-center text-gray-600">Don't have account? <a href="register.php" class="text-blue-600">Register</a></p>
    </div>
</body>
</html>
