<?php
include 'includes/db.php';
session_start();

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->prepare("SELECT * FROM admins WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows > 0){
        $error = "Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        if($stmt->execute()){
            $_SESSION['admin_id'] = $conn->insert_id;
            $_SESSION['admin_name'] = $name;
            header("Location: dashboard.php");
        } else {
            $error = "Registration failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded shadow-md w-96">
        <h1 class="text-2xl font-bold mb-6 text-center">Admin Register</h1>
        <?php if(isset($error)) { echo "<p class='text-red-500 mb-4'>$error</p>"; } ?>
        <form method="post">
            <input type="text" name="name" placeholder="Name" required class="w-full p-2 mb-4 border rounded">
            <input type="email" name="email" placeholder="Email" required class="w-full p-2 mb-4 border rounded">
            <input type="password" name="password" placeholder="Password" required class="w-full p-2 mb-4 border rounded">
            <button type="submit" name="register" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Register</button>
        </form>
        <p class="mt-4 text-center text-gray-600">Already have account? <a href="login.php" class="text-blue-600">Login</a></p>
    </div>
</body>
</html>
