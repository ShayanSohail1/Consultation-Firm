<?php
// c:\xampps\htdocs\Consulation\consultants.php
require_once 'config/db.php';
include 'includes/header.php';

// Search & Filter
$search = $_GET['search'] ?? '';
$filter_spec = $_GET['specialization'] ?? '';

// Build Query
$query = "
    SELECT c.*, u.full_name, u.profile_picture 
    FROM consultants c 
    JOIN users u ON c.user_id = u.id 
    WHERE u.status = 'active'
";
$params = [];

if ($search) {
    $query .= " AND (u.full_name LIKE ? OR c.bio LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($filter_spec) {
    $query .= " AND c.specialization = ?";
    $params[] = $filter_spec;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$consultants = $stmt->fetchAll();

// Get unique specializations for filter dropdown
$specs = $pdo->query("SELECT DISTINCT specialization FROM consultants")->fetchAll(PDO::FETCH_COLUMN);
?>

<div style="background: var(--bg-body); padding: 60px 0; text-align: center;">
    <div class="container">
        <h1 style="color: var(--primary-color); margin-bottom: 15px;">Meet Our Experts</h1>
        <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto 30px;">Our team of experienced consultants
            is dedicated to helping you achieve your academic goals.</p>

        <!-- Search Form -->
        <form method="GET" style="max-width: 600px; margin: 0 auto; display: flex; gap: 10px;">
            <input type="text" name="search" class="form-control" placeholder="Search by name or keyword..."
                value="<?php echo htmlspecialchars($search); ?>">
            <select name="specialization" class="form-control" style="width: 200px;">
                <option value="">All Specializations</option>
                <?php foreach ($specs as $s): ?>
                    <option value="<?php echo htmlspecialchars($s); ?>" <?php echo $filter_spec == $s ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($s); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if ($search || $filter_spec): ?>
                <a href="consultants.php" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="container" style="margin-top: 50px;">
    <?php if (count($consultants) == 0): ?>
        <p class="text-center" style="color: var(--text-muted);">No consultants found matching your criteria.</p>
    <?php endif; ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 40px;">
        <?php foreach ($consultants as $c): ?>
            <div class="card" style="text-align: center; padding: 40px 30px;">
                <div
                    style="width: 100px; height: 100px; background: #e1e9f0; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--primary-color);">
                    <?php echo strtoupper(substr($c['full_name'], 0, 1)); ?>
                </div>

                <h3 style="margin-bottom: 5px;"><?php echo htmlspecialchars($c['full_name']); ?></h3>
                <p style="color: var(--secondary-color); font-weight: 500; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($c['specialization']); ?></p>

                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($c['bio']); ?>
                </p>

                <div
                    style="display: flex; justify-content: center; gap: 15px; margin-bottom: 20px; font-size: 0.9rem; color: var(--text-light);">
                    <span><i class="fas fa-briefcase"></i> <?php echo $c['experience_years']; ?>+ Years</span>
                    <span><i class="fas fa-globe"></i> EN, FR</span>
                </div>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['role_name'] == 'student'): ?>
                    <a href="student/book_appointment.php" class="btn btn-secondary btn-block">Book Appointment</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-secondary btn-block">Register to Consult</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>