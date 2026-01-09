<?php
include 'includes/db.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if(isset($_POST['send_link'])){
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM admins WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){

        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        $update = $conn->prepare("UPDATE admins SET reset_token=?, reset_expires=? WHERE email=?");
        $update->bind_param("sss", $token, $expires, $email);
        $update->execute();

        $resetLink = "http://localhost/vasmat/reset-password.php?token=$token";

        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 0; // keep 0 in production
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'sbhosle1011@gmail.com';
            $mail->Password   = 'gvfcsniugbcriqpg'; // working app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('sbhosle1011@gmail.com', 'Admin Panel');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "
                <h2>Password Reset</h2>
                <p>Click the link below to reset your password:</p>
                <a href='$resetLink'>$resetLink</a>
                <br><br>
                <small>This link expires in 15 minutes.</small>
            ";

            $mail->send();
            $_SESSION['success'] = "Reset link sent to your email!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Email sending failed! " . $mail->ErrorInfo;
        }

    } else {
        $_SESSION['error'] = "No account found!";
    }

    header("Location: forgot-password.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Forgot Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="bg-white p-8 rounded shadow w-96">
  <h1 class="text-2xl font-bold mb-6 text-center">Forgot Password</h1>

  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <input type="email" name="email" placeholder="Enter your email" required class="w-full p-2 mb-4 border rounded">

    <button name="send_link" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
      Send Reset Link
    </button>
  </form>

  <p class="mt-4 text-center">
    <a href="login.php" class="text-blue-600">Back to login</a>
  </p>
</div>

<script src="assets/js/alert.js"></script>

<?php if(isset($_SESSION['error'])): ?>
<script>showAlert("error", "<?php echo $_SESSION['error']; ?>");</script>
<?php unset($_SESSION['error']); endif; ?>

<?php if(isset($_SESSION['success'])): ?>
<script>showAlert("success", "<?php echo $_SESSION['success']; ?>");</script>
<?php unset($_SESSION['success']); endif; ?>

</body>
</html>
