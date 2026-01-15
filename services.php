<?php
// c:\xampps\htdocs\Consulation\services.php
require_once 'config/db.php';
include 'includes/header.php';

// Filter
$cat = $_GET['category'] ?? '';

// Fetch Categories
$categories = $pdo->query("SELECT DISTINCT category FROM services WHERE is_active = 1")->fetchAll(PDO::FETCH_COLUMN);

// Fetch Services (flat list, not grouped)
$sql = "SELECT id, service_name, category, duration, fee, description, is_active FROM services WHERE is_active = 1";
$params = [];
if ($cat) {
    $sql .= " AND category = ?";
    $params[] = $cat;
}
$sql .= " ORDER BY category, service_name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="background: var(--primary-color); padding: 80px 0; margin-bottom: 60px; color: white; text-align: center;">
    <div class="container">
        <h1 style="font-size: 3rem; margin-bottom: 15px;">Our Services</h1>
        <p style="opacity: 0.9; font-size: 1.1rem; margin-bottom: 30px;">Comprehensive solutions for every step of your
            education journey.</p>

        <!-- Filter Pills -->
        <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
            <a href="services.php" class="btn <?php echo $cat == '' ? 'btn-secondary' : 'btn-outline-white'; ?>"
                style="<?php echo $cat == '' ? 'background:white; color:var(--primary-color);' : 'border:1px solid white; color:white;'; ?>">All</a>
            <?php foreach ($categories as $c): ?>
                <a href="services.php?category=<?php echo urlencode($c); ?>" class="btn"
                    style="<?php echo $cat == $c ? 'background:white; color:var(--primary-color);' : 'border:1px solid white; color:white;'; ?>">
                    <?php echo htmlspecialchars($c); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="container">
    <?php if (empty($services)): ?>
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-inbox" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 20px;"></i>
            <h2 style="color: var(--text-muted);">No services available</h2>
            <p>Please check back later or contact us for more information.</p>
        </div>
    <?php else: ?>
        <!-- All services in a single horizontal grid -->
        <div class="grid-3" style="margin-bottom: 60px;">
            <?php foreach ($services as $svc): ?>
                <div class="card" style="display: flex; flex-direction: column;">
                    <div style="margin-bottom: auto;">
                        <span
                            style="display: inline-block; padding: 4px 12px; background: var(--bg-body); border-radius: 20px; font-size: 0.75rem; color: var(--primary-color); font-weight: 600; margin-bottom: 15px;">
                            <?php echo htmlspecialchars($svc['category']); ?>
                        </span>
                        <h3 style="font-size: 1.3rem; margin-bottom: 10px;">
                            <?php echo htmlspecialchars($svc['service_name']); ?>
                        </h3>
                        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.7;">
                            <?php echo htmlspecialchars($svc['description']); ?>
                        </p>
                    </div>

                    <div
                        style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="display: block; font-size: 0.85rem; color: var(--text-muted);">Duration:
                                <?php echo $svc['duration']; ?> mins</span>
                            <span
                                style="display: block; font-weight: 700; color: var(--primary-color); font-size: 1.2rem;">$<?php echo number_format($svc['fee'], 2); ?></span>
                        </div>

                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role_name'] == 'student'): ?>
                            <a href="student/book_appointment.php" class="btn btn-primary">Book Now</a>
                        <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role_name'] != 'student'): ?>
                            <span class="btn btn-secondary disabled" style="opacity: 0.6;">Student Only</span>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-secondary">Login to Book</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>