<?php
// c:\xampps\htdocs\Consulation\register.php
require_once 'config/db.php';
require_once 'includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $is_ajax = isset($_POST['ajax']);

    // Basic Validation
    if (empty($full_name) || empty($email) || empty($username) || empty($password)) {
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'message' => "All fields are required."]);
            exit;
        }
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'message' => "Passwords do not match."]);
            exit;
        }
        $error = "Passwords do not match.";
    } else {
        // Check if email or username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->rowCount() > 0) {
            if ($is_ajax) {
                echo json_encode(['status' => 'error', 'message' => "Email or Username already exists."]);
                exit;
            }
            $error = "Email or Username already exists.";
        } else {
            // Create User
            // Role ID 3 = Student (Make sure to sync with your DB seed)
            $role_id = 3;
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                $pdo->beginTransaction();

                // Insert into users
                $stmt = $pdo->prepare("INSERT INTO users (role_id, username, email, password, full_name, status) VALUES (?, ?, ?, ?, ?, 'active')");
                $stmt->execute([$role_id, $username, $email, $hashed_password, $full_name]);
                $user_id = $pdo->lastInsertId();

                // Insert into students (Initialize profile)
                $stmt = $pdo->prepare("INSERT INTO students (user_id) VALUES (?)");
                $stmt->execute([$user_id]);

                // Send Welcome Email
                require_once 'includes/SimpleSMTP.php';
                $subject = "Welcome to EduConsult!";
                $message = "
                <div style='font-family: sans-serif; padding: 20px; background: #f4f6f9; color: #333;'>
                    <div style='background: white; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
                        <h2 style='color: #004e64;'>Welcome to EduConsult, $full_name!</h2>
                        <p>Thank you for registering with us. We are excited to help you achieve your study abroad dreams.</p>
                        <p>You can now login to your dashboard and:</p>
                        <ul>
                            <li>Book appointments with expert consultants</li>
                            <li>Track your application status</li>
                            <li>Access exclusive resources</li>
                        </ul>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='http://localhost/Consulation/login.php' style='background: #004e64; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Login Now</a>
                        </div>
                        <p style='color: #666; font-size: 0.9em;'>If you have any questions, feel free to reply to this email.</p>
                        <p style='margin-top: 30px; font-size: 0.8em; color: #999;'>&copy; " . date('Y') . " EduConsult. All rights reserved.</p>
                    </div>
                </div>";

                sendEmail($email, $subject, $message);

                $pdo->commit();

                // Set flash message for login page
                session_start();
                $_SESSION['register_success'] = "Registration successful! Please login to continue.";

                if ($is_ajax) {
                    echo json_encode(['status' => 'success', 'message' => "Registration successful! Redirecting...", 'redirect' => 'login.php']);
                    exit;
                }

                header("Location: login.php");
                exit();
            } catch (PDOException $e) {
                $pdo->rollBack();
                if ($is_ajax) {
                    echo json_encode(['status' => 'error', 'message' => "Registration failed: " . $e->getMessage()]);
                    exit;
                }
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card fade-in">
            <div class="auth-header">
                <h2>Student Registration</h2>
                <p>Start your journey with us today</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" id="register-form">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" required placeholder="John Doe"
                        value="<?php echo isset($full_name) ? $full_name : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="johndoe"
                        value="<?php echo isset($username) ? $username : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="john@example.com"
                        value="<?php echo isset($email) ? $email : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="********">
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required placeholder="********">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
            </form>

            <div class="auth-links">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>