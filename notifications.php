<?php
// c:\xampps\htdocs\Consulation\notifications.php
require_once 'config/db.php';
require_once 'includes/auth.php';

requireLogin();

$user_id = $_SESSION['user_id'];

// Mark all as read
$stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
$stmt->execute([$user_id]);

// Fetch Notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();

// Determine dashboard link
$dashboard_link = '#';
if ($_SESSION['role_name'] == 'admin')
    $dashboard_link = 'admin/dashboard.php';
elseif ($_SESSION['role_name'] == 'consultant')
    $dashboard_link = 'consultant/dashboard.php';
elseif ($_SESSION['role_name'] == 'student')
    $dashboard_link = 'student/dashboard.php';
?>

<?php include 'includes/header.php'; ?>

<div class="container" style="padding: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <a href="<?php echo $dashboard_link; ?>" class="btn btn-secondary" style="margin-bottom: 15px;"><i
                    class="fas fa-arrow-left"></i> Back to Dashboard</a>
            <h1 style="color: var(--primary-color); margin: 0;">Notifications</h1>
            <p style="color: var(--text-muted); margin-top: 5px;">Stay updated with your latest activity.</p>
        </div>
        <?php if (count($notifications) > 0): ?>
            <span
                style="background: var(--bg-body); padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; color: var(--text-muted);">
                <i class="fas fa-check-circle" style="color: var(--success);"></i> All marked as read
            </span>
        <?php endif; ?>
    </div>

    <?php if (count($notifications) > 0): ?>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($notifications as $notif): ?>
                <div class="card" style="padding: 20px; display: flex; align-items: flex-start; gap: 15px;">
                    <div
                        style="width: 45px; height: 45px; background: var(--bg-body); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-bell" style="color: var(--primary-color);"></i>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 0; color: var(--text-main); line-height: 1.5;">
                            <?php echo htmlspecialchars($notif['message']); ?>
                        </p>
                        <small style="color: var(--text-muted); display: block; margin-top: 8px;">
                            <i class="far fa-clock"></i>
                            <?php echo date('M d, Y \a\t h:i A', strtotime($notif['created_at'])); ?>
                        </small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card" style="text-align: center; padding: 60px 40px;">
            <div
                style="width: 80px; height: 80px; background: var(--bg-body); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                <i class="fas fa-bell-slash" style="font-size: 2.5rem; color: var(--text-muted);"></i>
            </div>
            <h3 style="color: var(--primary-color); margin-bottom: 10px;">No Notifications</h3>
            <p style="color: var(--text-muted); margin-bottom: 25px;">You're all caught up! Check back later for updates.
            </p>
            <a href="<?php echo $dashboard_link; ?>" class="btn btn-primary"><i class="fas fa-home"></i> Go to Dashboard</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>