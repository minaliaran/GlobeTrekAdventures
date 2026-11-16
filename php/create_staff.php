<?php
require_once __DIR__ . '/config.php';

$pdo = getPDO();

$name = 'Staff Member';
$email = 'staff@globetrek.com';
$password = password_hash('staff123', PASSWORD_DEFAULT);
$role = 'staff';

$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE password = ?, name = ?");
$stmt->execute([$name, $email, $password, $role, $password, $name]);

echo "Staff account created/reset successfully.\n";
echo "Email: staff@globetrek.com\n";
echo "Password: staff123\n";
