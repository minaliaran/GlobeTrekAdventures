<?php
require_once __DIR__ . '/config.php';

try {
    $pdo = getPDO();
    $newPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@globetrek.com'");
    $stmt->execute([$newPassword]);
    
    if ($stmt->rowCount() > 0) {
        echo "Admin password has been reset to: admin123\n";
    } else {
        echo "Admin user not found or password already set to this hash.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
