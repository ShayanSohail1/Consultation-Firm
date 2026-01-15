<?php
// c:\xampps\htdocs\Consulation\contact.php
require_once 'config/db.php';
include 'includes/header.php';

$msg = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    $is_ajax = isset($_POST['ajax']);

    if (empty($name) || empty($email) || empty($message)) {
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'message' => "All fields are required."]);
            exit;
        }
        $msg = "All fields are required.";
        $msg_type = "danger";
    } else {
        try {
            // Create contact_messages table if not exists
            $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                message TEXT NOT NULL,
                is_read TINYINT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            // Save message to database
            // Save message to database
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);

            // Send Email Notification to Admin (using the configured SMTP user as receiver for now)
            require_once 'includes/SimpleSMTP.php';

            // Get Admin Email (or use default)
            $admin_email = "doctoraura73@gmail.com";

            $subject = "New Contact Form Message from $name";
            $email_body = "
            <div style='font-family: sans-serif; padding: 20px; border: 1px solid #ddd; background: #f9f9f9;'>
                <h2 style='color: #004e64;'>New Contact Message</h2>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Message:</strong></p>
                <p style='background: white; padding: 15px; border-left: 4px solid #004e64;'>$message</p>
            </div>";

            sendEmail($admin_email, $subject, $email_body); // Send to Admin

            // Optional: Send auto-reply to user
            $user_subject = "We received your message - EduConsult";
            $user_body = "Hi $name,<br><br>Thanks for contacting us. We have received your message and will get back to you shortly.<br><br>Best regards,<br>EduConsult Team";
            sendEmail($email, $user_subject, $user_body); // Send to User

            if ($is_ajax) {
                echo json_encode(['status' => 'success', 'message' => "Message sent successfully! We will get back to you soon."]);
                exit;
            }
            $msg = "Message sent successfully! We will get back to you soon.";
            $msg_type = "success";
        } catch (Exception $e) {
            if ($is_ajax) {
                echo json_encode(['status' => 'error', 'message' => "Failed to send message: " . $e->getMessage()]);
                exit;
            }
            $msg = "Failed to send message. Please try again later.";
            $msg_type = "danger";
        }
    }
}
?>

<div class="container" style="padding: 80px 40px;">
    <a href="index.php" class="btn btn-secondary" style="margin-bottom: 30px;"><i class="fas fa-arrow-left"></i> Back to
        Home</a>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px;">
        <div>
            <span style="color: var(--secondary-color); font-weight: 600;">GET IN TOUCH</span>
            <h1 style="font-size: 3rem; color: var(--primary-color); margin-bottom: 20px;">We're here to help</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 40px;">
                Have a question about our services or need help choosing the right path? Send us a message!
            </p>

            <div style="margin-bottom: 30px;">
                <h4 style="margin-bottom: 10px;"><i class="fas fa-envelope"
                        style="color: var(--primary-color); margin-right: 10px;"></i> Email Us</h4>
                <p style="color: var(--text-muted);">contact@educonsult.com</p>
            </div>

            <div style="margin-bottom: 30px;">
                <h4 style="margin-bottom: 10px;"><i class="fas fa-phone"
                        style="color: var(--primary-color); margin-right: 10px;"></i> Call Us</h4>
                <p style="color: var(--text-muted);">+1 (555) 123-4567</p>
            </div>

            <div style="margin-bottom: 30px;">
                <h4 style="margin-bottom: 10px;"><i class="fas fa-map-marker-alt"
                        style="color: var(--primary-color); margin-right: 10px;"></i> Visit Us</h4>
                <p style="color: var(--text-muted);">123 Education Street, Suite 100<br>New York, NY 10001</p>
            </div>
        </div>

        <div class="card" style="padding: 30px; border-radius: 15px;">
            <h3 style="margin-bottom: 25px;">Send a Message</h3>

            <?php if ($msg): ?>
                <div class="alert alert-<?php echo $msg_type; ?>" style="
                    background: <?php echo $msg_type == 'success' ? '#d4edda' : '#f8d7da'; ?>;
                    color: <?php echo $msg_type == 'success' ? '#155724' : '#721c24'; ?>;
                    padding: 15px 20px;
                    border-radius: 8px;
                    margin-bottom: 20px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                ">
                    <i class="fas fa-<?php echo $msg_type == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="contact-form">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" class="form-control" rows="5" placeholder="How can we help?"
                        required></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">Send Message</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>