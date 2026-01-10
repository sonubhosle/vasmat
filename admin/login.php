<?php
session_start();
include 'includes/db.php';

if(isset($_SESSION['admin_id'])){
    header("Location: index.php");
    exit();
}

if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if($admin && password_verify($password, $admin['password'])){
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];

        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="bg-white p-8 rounded shadow w-96">
<h1 class="text-2xl font-bold mb-6 text-center">Admin Login</h1>

<?php if(isset($error)): ?>
<p class="text-red-600 mb-3"><?= $error ?></p>
<?php endif; ?>

<form method="POST">
<input type="email" name="email" placeholder="Email" required class="w-full p-2 mb-3 border rounded">
<input type="password" name="password" placeholder="Password" required class="w-full p-2 mb-3 border rounded">

<button name="login" class="w-full bg-blue-600 text-white py-2 rounded">Login</button>
</form>

<p class="mt-4 text-center">
No account? <a href="register.php" class="text-blue-600">Register</a>
</p>
</div>

</body>
</html>
