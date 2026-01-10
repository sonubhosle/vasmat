<?php
session_start();
include 'includes/db.php';

if(isset($_POST['register'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(strlen($password) < 6){
        $error = "Password must be at least 6 characters!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM admins WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();

        if($check->get_result()->num_rows > 0){
            $error = "Email already exists!";
        } else {
            $stmt = $conn->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed);
            $stmt->execute();

            header("Location: login.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="bg-white p-8 rounded shadow w-96">
<h1 class="text-2xl font-bold mb-6 text-center">Register</h1>

<?php if(isset($error)): ?>
<p class="text-red-600 mb-3"><?= $error ?></p>
<?php endif; ?>

<form method="POST">
<input type="text" name="name" placeholder="Name" required class="w-full p-2 mb-3 border rounded">
<input type="email" name="email" placeholder="Email" required class="w-full p-2 mb-3 border rounded">
<input type="password" name="password" placeholder="Password" required class="w-full p-2 mb-3 border rounded">

<button name="register" class="w-full bg-blue-600 text-white py-2 rounded">Register</button>
</form>

<p class="mt-4 text-center">
Already have account? <a href="login.php" class="text-blue-600">Login</a>
</p>
</div>

</body>
</html>
