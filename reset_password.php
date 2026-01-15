<?php
// c:\xampps\htdocs\Consulation\reset_password.php
require_once 'config/db.php';
include 'includes/header.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (!$token) {
    $error = "Invalid link.";
} else {
    // 1. Check if token exists at all (ignoring expiry)
    $stmt = $pdo->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    $check = $stmt->fetch();

    if (!$check) {
        $error = "Invalid password reset token.";
    } elseif (strtotime($check['reset_expires']) < time()) {
        $error = "This password reset link has expired.";
    } else {
        // Valid
        $user = $check;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$error) {
    $pass1 = $_POST['password'];
    $pass2 = $_POST['confirm_password'];

    if (strlen($pass1) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($pass1 !== $pass2) {
        $error = "Passwords do not match.";
    } else {
        // Update Password
        $hash = password_hash($pass1, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        if ($stmt->execute([$hash, $user['id']])) {
            $success = "Password updated successfully. You can now login.";
        } else {
            $error = "Failed to update password.";
        }
    }
}
?>

<div class="container">
    <div class="auth-container">
        <div class="card">
            <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 20px;">Reset Password</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="login.php" class="btn btn-primary">Login Now</a>
                </div>
            <?php else: ?>

                <?php if ($token && !$error): ?>
                    <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" style="margin-top: 20px;">Reset
                            Password</button>
                    </form>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>