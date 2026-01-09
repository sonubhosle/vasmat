<?php
include 'includes/db.php';
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if(isset($_POST['register'])){
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(strlen($password) < 6){
        $_SESSION['error'] = "Password must be at least 6 characters!";
        header("Location: register.php");
        exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM admins WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows > 0){
        $_SESSION['error'] = "Email already exists!";
        header("Location: register.php");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed);

    if($stmt->execute()){
        $_SESSION['success'] = "Account created successfully!";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed!";
        header("Location: register.php");
        exit();
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

  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <input type="text" name="name" placeholder="Name" required class="w-full p-2 mb-3 border rounded">

    <input type="email" name="email" placeholder="Email" required class="w-full p-2 mb-3 border rounded">

    <div class="relative mb-1">
      <input id="password" type="password" name="password" placeholder="Password" required class="w-full p-2 border rounded">
      <span onclick="togglePassword('password')" class="absolute right-3 top-2 cursor-pointer">👁️</span>
    </div>

    <p id="strength" class="text-sm mb-3"></p>

    <button name="register" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
      Register
    </button>
  </form>

  <p class="mt-4 text-center">
    Already have an account? <a href="login.php" class="text-blue-600">Login</a>
  </p>
</div>

<script src="assets/js/password.js"></script>
<script src="assets/js/toggle.js"></script>
<script src="assets/js/alert.js"></script>

<script>
initPasswordStrength("password", "strength");
</script>

<?php if(isset($_SESSION['error'])): ?>
<script>showAlert("error", "<?php echo $_SESSION['error']; ?>");</script>
<?php unset($_SESSION['error']); endif; ?>

<?php if(isset($_SESSION['success'])): ?>
<script>showAlert("success", "<?php echo $_SESSION['success']; ?>");</script>
<?php unset($_SESSION['success']); endif; ?>

</body>
</html>
