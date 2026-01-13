<?php
session_start();
require ('../includes/db.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if(isset($_POST['send_link'])){
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $email = trim($_POST['email']);
    
    // Always show success message even if email doesn't exist (security best practice)
    $user_exists = false;
    
    $stmt = $conn->prepare("SELECT id FROM admins WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

   // ... other code ...

if($result->num_rows > 0){
    $user_exists = true;
    $user = $result->fetch_assoc();
    
    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", strtotime("+15 minutes"));
    
    // Clear any existing reset tokens first
    $clear = $conn->prepare("UPDATE admins SET reset_token=NULL, reset_expires=NULL WHERE email=?");
    $clear->bind_param("s", $email);
    $clear->execute();
    $clear->close();
    
    $update = $conn->prepare("UPDATE admins SET reset_token=?, reset_expires=? WHERE email=?");
    $update->bind_param("sss", $token, $expires, $email);
    $update->execute();
    $update->close();
    
    // FIXED: Generate correct reset link for pages directory
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    
    // Since your files are in admin/pages/, the correct path is:
    $resetLink = "$protocol://$host/mit-college/admin/pages/reset-password.php?token=$token";
    
    // ... rest of email sending code ...
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'sbhosle1011@gmail.com';
            $mail->Password   = 'gvfcsniugbcriqpg';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('sbhosle1011@gmail.com', 'Admin Panel');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "
                <h2>Password Reset Request</h2>
                <p>You requested a password reset. Click the link below to reset your password:</p>
                <p><a href='$resetLink' style='background-color: #4F46E5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a></p>
                <p>Or copy this link: <br>$resetLink</p>
                <br>
                <p><small>This link will expire in 15 minutes.</small></p>
                <p><small>If you didn't request this, please ignore this email.</small></p>
            ";
            
            $mail->AltBody = "Password Reset Link: $resetLink\n\nThis link expires in 15 minutes.";

            $mail->send();
        } catch (Exception $e) {
            error_log("Email sending failed: " . $mail->ErrorInfo);
            // Don't show email error to user for security
        }
    }
    
    $stmt->close();
    
    // Always show success message for security
    $_SESSION['success'] = "If an account exists with that email, you will receive a password reset link shortly.";
    
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
  
  <p class="text-sm text-gray-600 mb-4 text-center">
    Enter your email address and we'll send you a link to reset your password.
  </p>

  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

    <div class="mb-4">
      <input type="email" name="email" placeholder="Enter your email" required 
             class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
    </div>

    <button type="submit" name="send_link" 
            class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition duration-300">
      Send Reset Link
    </button>
  </form>

  <div class="mt-4 text-center">
    <a href="login.php" class="text-blue-600 hover:underline">← Back to login</a>
  </div>
</div>

<script>
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 p-4 rounded shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    alertDiv.textContent = message;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>

<?php if(isset($_SESSION['error'])): ?>
<script>showAlert("error", "<?php echo htmlspecialchars($_SESSION['error']); ?>");</script>
<?php unset($_SESSION['error']); endif; ?>

<?php if(isset($_SESSION['success'])): ?>
<script>showAlert("success", "<?php echo htmlspecialchars($_SESSION['success']); ?>");</script>
<?php unset($_SESSION['success']); endif; ?>

</body>
</html>