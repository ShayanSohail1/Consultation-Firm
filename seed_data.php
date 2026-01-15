<?php
// c:\xampps\htdocs\Consulation\seed_data.php
require_once 'config/db.php';

echo "Warning: This will clear existing data in appointments, documents, services, consultants, students, and non-admin users. Proceed? (Run manually if unsure)\n";

try {
    $pdo->beginTransaction();

    // 1. Clear Tables (Reverse order of dependencies)
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE notifications");
    $pdo->exec("TRUNCATE TABLE consultation_notes");
    $pdo->exec("TRUNCATE TABLE documents");
    $pdo->exec("TRUNCATE TABLE appointments");
    $pdo->exec("TRUNCATE TABLE availability");
    $pdo->exec("TRUNCATE TABLE services");
    $pdo->exec("TRUNCATE TABLE students");
    $pdo->exec("TRUNCATE TABLE consultants");
    $pdo->exec("DELETE FROM users WHERE role_id != 1"); // Keep Admin
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "Tables cleared.\n";

    // 2. Services
    $services = [
        ['Visa Consultation', 'Visa', 60, 50.00, 'Expert guidance on visa application process.'],
        ['University Application', 'Application', 60, 100.00, 'End-to-end support for university admission.'],
        ['Mock Interview', 'Preparation', 45, 40.00, 'Practice interview with feedback.'],
        ['Document Review', 'Documentation', 30, 30.00, 'Review of SOP, LORs and transcripts.'],
        ['Career Counseling', 'Consultation', 60, 75.00, 'Path finding for high school graduates.']
    ];
    $svc_stmt = $pdo->prepare("INSERT INTO services (service_name, category, duration, fee, description) VALUES (?, ?, ?, ?, ?)");
    foreach ($services as $s)
        $svc_stmt->execute($s);
    echo "Services seeded.\n";

    // 3. Users & Consultants
    $consultants_data = [
        ['Dr. Alan Grant', 'alan', 'alan@test.com', 'Paleontology', 15, 'I specialize in US universities.'],
        ['Sarah Connor', 'sarah', 'sarah@test.com', 'Security', 8, 'Expert in UK and European visas.'],
        ['Tony Stark', 'tony', 'tony@test.com', 'Engineering', 12, 'Top tier engineering school admissions.']
    ];

    foreach ($consultants_data as $c) {
        // User
        $stmt = $pdo->prepare("INSERT INTO users (role_id, username, email, password, full_name, status) VALUES (2, ?, ?, ?, ?, 'active')");
        $pass = password_hash('password123', PASSWORD_DEFAULT);
        $stmt->execute([$c[1], $c[2], $pass, $c[0]]);
        $uid = $pdo->lastInsertId();

        // Consultant Profile
        $stmt = $pdo->prepare("INSERT INTO consultants (user_id, specialization, experience_years, bio, hourly_rate) VALUES (?, ?, ?, ?, 100)");
        $stmt->execute([$uid, $c[3], $c[4], $c[5]]);
        $cid = $pdo->lastInsertId();

        // Availability (Mon-Fri 9-5)
        for ($i = 1; $i <= 5; $i++) {
            $stmt = $pdo->prepare("INSERT INTO availability (consultant_id, day_of_week, start_time, end_time, is_available) VALUES (?, ?, '09:00:00', '17:00:00', 1)");
            $stmt->execute([$cid, $i]);
        }
    }
    echo "Consultants seeded.\n";

    // 4. Users & Students
    $students_data = [
        ['Marty McFly', 'marty', 'marty@test.com'],
        ['Peter Parker', 'peter', 'peter@test.com'],
        ['Hermione Granger', 'hermione', 'hermione@test.com'],
        ['Bruce Wayne', 'bruce', 'bruce@test.com'],
        ['Clark Kent', 'clark', 'clark@test.com']
    ];

    foreach ($students_data as $s) {
        $stmt = $pdo->prepare("INSERT INTO users (role_id, username, email, password, full_name, status) VALUES (3, ?, ?, ?, ?, 'active')");
        $pass = password_hash('password123', PASSWORD_DEFAULT);
        $stmt->execute([$s[1], $s[2], $pass, $s[0]]);
        $uid = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO students (user_id, target_country) VALUES (?, 'USA')");
        $stmt->execute([$uid]);
    }
    echo "Students seeded.\n";

    // 5. Appointments
    // Get IDs
    $c_ids = $pdo->query("SELECT id FROM consultants")->fetchAll(PDO::FETCH_COLUMN);
    $s_ids = $pdo->query("SELECT id FROM students")->fetchAll(PDO::FETCH_COLUMN);
    $svc_ids = $pdo->query("SELECT id FROM services")->fetchAll(PDO::FETCH_COLUMN);

    $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];

    for ($i = 0; $i < 10; $i++) {
        $cid = $c_ids[array_rand($c_ids)];
        $sid = $s_ids[array_rand($s_ids)];
        $svid = $svc_ids[array_rand($svc_ids)];
        $stat = $statuses[array_rand($statuses)];

        $date = date('Y-m-d', strtotime("+$i days"));

        $stmt = $pdo->prepare("INSERT INTO appointments (student_id, consultant_id, service_id, appointment_date, start_time, end_time, status) VALUES (?, ?, ?, ?, '10:00:00', '11:00:00', ?)");
        $stmt->execute([$sid, $cid, $svid, $date, $stat]);

        // Notification
        $u_stmt = $pdo->prepare("SELECT user_id FROM students WHERE id = ?");
        $u_stmt->execute([$sid]);
        $suid = $u_stmt->fetchColumn();

        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, 'Booking Update', 'Your appointment status is $stat')");
        $stmt->execute([$suid]);
    }
    echo "Appointments seeded.\n";

    $pdo->commit();
    echo "Seeding completed successfully!\n";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>