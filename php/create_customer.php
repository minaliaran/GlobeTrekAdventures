<?php
require_once __DIR__ . '/config.php';

$pdo = getPDO();


$name = 'Test Customer';
$email = 'customer@globetrek.com';
$password = password_hash('customer123', PASSWORD_DEFAULT);
$role = 'customer';

$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE password = ?, name = ?");
$stmt->execute([$name, $email, $password, $role, $password, $name]);
echo "Customer account created/reset.\nEmail: customer@globetrek.com\nPassword: customer123\n";
