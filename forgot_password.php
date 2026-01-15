<?php
// c:\xampps\htdocs\Consulation\forgot_password.php
require_once 'config/db.php';
include 'includes/header.php';

$message = '';
$msg_type = '';
$reset_link = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $is_ajax = isset($_POST['ajax']);

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Save Token
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
        $stmt->execute([$token, $expiry, $user['id']]);

        $reset_link = "http://localhost/Consulation/reset_password.php?token=" . $token;

        require_once 'includes/SimpleSMTP.php';
        $subject = "Password Reset Request - EduConsult";
        $email_body = "
        <div style='font-family: sans-serif; padding: 20px; background: #f4f6f9; color: #333;'>
            <div style='background: white; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
                <h2 style='color: #004e64;'>Password Reset</h2>
                <p>We received a request to reset your password. Click the button below to proceed:</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$reset_link' style='background: #004e64; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Reset Password</a>
                </div>
                <p style='color: #666;'>Or copy this link: <a href='$reset_link'>$reset_link</a></p>
                <p style='color: #666; font-size: 0.9em;'>This link expires in 1 hour. If you didn't request this, please ignore this email.</p>
            </div>
        </div>";

        if (sendEmail($email, $subject, $email_body)) {
            if ($is_ajax) {
                echo json_encode(['status' => 'success', 'message' => "A reset link has been sent to your email address."]);
                exit;
            }
            $message = "A reset link has been sent to your email address.";
            $msg_type = "success";
            $reset_link = ""; // Hide link since email was sent
        } else {
            if ($is_ajax) {
                echo json_encode(['status' => 'error', 'message' => "Could not send email. Verify SMTP settings."]);
                exit;
            }
            $message = "Could not send email. Verify SMTP settings.";
            $msg_type = "danger";
            // Keep reset_link visible for dev backup if email fails
        }
    } else {
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'message' => "No account found with that email address."]);
            exit;
        }
        // Security: Don't reveal if user exists, but for dev we'll be honest
        $message = "No account found with that email address.";
        $msg_type = "danger";
    }
}
?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card" style="text-align: center;">
            <div
                style="width: 80px; height: 80px; background: rgba(0, 78, 100, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                <i class="fas fa-key" style="font-size: 2rem; color: var(--primary-color);"></i>
            </div>
            <h2 style="color: var(--primary-color); margin-bottom: 15px;">Forgot Password?</h2>
            <p style="color: var(--text-muted); margin-bottom: 30px;">Enter your email to receive a reset link.</p>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $msg_type; ?>" style="
                    background: <?php echo $msg_type == 'success' ? '#d4edda' : '#f8d7da'; ?>;
                    color: <?php echo $msg_type == 'success' ? '#155724' : '#721c24'; ?>;
                    padding: 15px 20px;
                    border-radius: 8px;
                    margin-bottom: 20px;
                    text-align: left;
                ">
                    <i class="fas fa-<?php echo $msg_type == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>

                <?php if ($reset_link): ?>
                    <div
                        style="background: var(--bg-body); padding: 15px; border-radius: 8px; margin-bottom: 20px; word-break: break-all;">
                        <small style="color: var(--text-muted);">Reset Link (for development):</small><br>
                        <a href="<?php echo $reset_link; ?>"
                            style="color: var(--primary-color); font-weight: 500;"><?php echo $reset_link; ?></a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!$reset_link): ?>
                <form action="" method="POST" id="forgot-password-form">
                    <div class="form-group" style="text-align: left;">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="name@example.com">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px; padding: 15px;">Send
                        Reset Link</button>
                </form>
            <?php endif; ?>

            <div style="margin-top: 20px;">
                <a href="login.php" style="color: var(--text-muted); font-size: 0.9rem;"><i
                        class="fas fa-arrow-left"></i> Back to Login</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>