<?php
// c:\xampps\htdocs\Consulation\login.php
require_once 'config/db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    // Redirect if already logged in based on role
    // For now, just home
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $is_ajax = isset($_POST['ajax']);

    if (empty($email) || empty($password)) {
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'message' => "All fields are required."]);
            exit;
        }
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'inactive') {
                if ($is_ajax) {
                    echo json_encode(['status' => 'error', 'message' => "Your account is deactivated."]);
                    exit;
                }
                $error = "Your account is deactivated.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['full_name'] = $user['full_name'];

                $redirect = 'student/dashboard.php';
                if ($user['role_id'] == 1)
                    $redirect = 'admin/dashboard.php';
                if ($user['role_id'] == 2)
                    $redirect = 'consultant/dashboard.php';

                if ($is_ajax) {
                    echo json_encode(['status' => 'success', 'message' => "Login successful! Redirecting...", 'redirect' => $redirect]);
                    exit;
                }

                header("Location: $redirect");
                exit();
            }
        } else {
            if ($is_ajax) {
                echo json_encode(['status' => 'error', 'message' => "Invalid email or password."]);
                exit;
            }
            $error = "Invalid email or password.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card fade-in">
            <div class="auth-header">
                <h2>Welcome Back</h2>
                <p>Login to your account to continue</p>
            </div>

            <?php
            if (session_status() === PHP_SESSION_NONE)
                session_start();
            if (isset($_SESSION['register_success'])): ?>
                <div class="alert alert-success"
                    style="background: #d4edda; color: #155724; border-left: 5px solid #28a745;">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $_SESSION['register_success'];
                    unset($_SESSION['register_success']); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" id="login-form">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="text" name="username_email" class="form-control" required
                        placeholder="Enter username or email">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="********">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <div class="auth-links">
                <p>Don't have an account? <a href="register.php">Sign Up</a></p>
                <p><a href="forgot_password.php">Forgot Password?</a></p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>