<?php
session_start();

require ('../includes/db.php');

if (!isset($_GET['token'])) {
    die("Invalid request");
}

$token = $_GET['token'];

$stmt = $conn->prepare("SELECT id, reset_expires FROM admins WHERE reset_token=?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    die("Invalid or expired token");
}

$user = $result->fetch_assoc();

if(strtotime($user['reset_expires']) < time()){
    die("Token expired");
}

if(isset($_POST['reset'])){
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if($password !== $confirm){
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: reset-password.php?token=$token");
        exit();
    }

    if(strlen($password) < 6){
        $_SESSION['error'] = "Password must be at least 6 characters!";
        header("Location: reset-password.php?token=$token");
        exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $update = $conn->prepare("UPDATE admins SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
    $update->bind_param("si", $hashed, $user['id']);
    
    if($update->execute()){
        $_SESSION['success'] = "Password reset successfully!";
       header("Location: ../login.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to reset password. Please try again.";
    }
    
    $update->close();
}

$stmt->close();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Reset Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="bg-white p-8 rounded shadow w-96">
  <h1 class="text-2xl font-bold mb-6 text-center">Reset Password</h1>

  <form method="POST" onsubmit="return validateMatch()">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

    <div class="relative mb-3">
      <input id="password" type="password" name="password" placeholder="New password" required class="w-full p-2 border rounded">
      <span onclick="togglePassword('password')" class="absolute right-3 top-2 cursor-pointer">👁️</span>
    </div>
    <p id="strength" class="text-sm mb-3"></p>

    <div class="relative mb-3">
      <input id="confirm_password" type="password" name="confirm_password" placeholder="Confirm password" required class="w-full p-2 border rounded">
      <span onclick="togglePassword('confirm_password')" class="absolute right-3 top-2 cursor-pointer">👁️</span>
    </div>
    <p id="match" class="text-sm mb-3"></p>

    <button name="reset" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
      Reset Password
    </button>
  </form>
</div>

<script>
// Simple toggle password function
function togglePassword(id){
  const input = document.getElementById(id);
  input.type = input.type === "password" ? "text" : "password";
}

// Password strength function
function initPasswordStrength(inputId, strengthId){
  const input = document.getElementById(inputId);
  const strength = document.getElementById(strengthId);

  input.addEventListener("input", function(){
    const val = input.value;
    let score = 0;
    if(val.length >= 6) score++;
    if(/[A-Z]/.test(val)) score++;
    if(/[0-9]/.test(val)) score++;
    if(/[\W]/.test(val)) score++;

    let text="", color="";
    switch(score){
      case 0: text=""; break;
      case 1: text="Weak"; color="red"; break;
      case 2: text="Fair"; color="orange"; break;
      case 3: text="Good"; color="blue"; break;
      case 4: text="Strong"; color="green"; break;
    }
    strength.innerText = text;
    strength.style.color = color;
  });
}

function validateMatch(){
  const p = document.getElementById("password").value;
  const c = document.getElementById("confirm_password").value;
  const m = document.getElementById("match");

  if(p !== c){
    m.innerText = "Passwords do not match!";
    m.className = "text-red-500";
    return false;
  }

  return true;
}

// Initialize
initPasswordStrength("password", "strength");
</script>

<?php if(isset($_SESSION['error'])): ?>
<script>
alert("<?php echo htmlspecialchars($_SESSION['error']); ?>");
</script>
<?php unset($_SESSION['error']); endif; ?>

<?php if(isset($_SESSION['success'])): ?>
<script>
alert("<?php echo htmlspecialchars($_SESSION['success']); ?>");
</script>
<?php unset($_SESSION['success']); endif; ?>

</body>
</html>