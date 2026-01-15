<?php
// c:\xampps\htdocs\Consulation\config_smtp.php
require_once 'config/db.php';

$settings = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => '587',
    'smtp_user' => '',
    'smtp_pass' => '', // App Password
    'company_name' => 'EduConsult',
    'currency_symbol' => '$'
];

try {
    foreach ($settings as $key => $val) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $val, $val]);
    }
    echo "SMTP Configuration updated successfully for " . $settings['smtp_user'];
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>