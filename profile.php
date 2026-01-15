<?php
// c:\xampps\htdocs\Consulation\profile.php
require_once 'config/db.php';
require_once 'includes/auth.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];

    // Simple validation
    if (empty($full_name) || empty($email)) {
        $error = "Name and Email are required.";
    } else {
        try {
            $pdo->beginTransaction();

            // Update Info
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $user_id]);
            $_SESSION['full_name'] = $full_name; // update session

            // Update Password if set
            if (!empty($new_password)) {
                $hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hash, $user_id]);
            }

            $pdo->commit();
            $success = "Profile updated successfully.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error updating profile.";
        }
    }
}

// Fetch User
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <h2 style="margin-bottom: 20px; color: var(--primary-color);">My Profile</h2>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="profile.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control"
                        value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control"
                        value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">

                <div class="form-group">
                    <label>New Password (Leave blank to keep current)</label>
                    <input type="password" name="new_password" class="form-control" placeholder="********">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>