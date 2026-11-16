<?php
require_once __DIR__ . '/config.php';

try {
    $pdo = getPDO();
    echo 'Database connection successful' . PHP_EOL;

    
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM packages');
    $result = $stmt->fetch();
    echo 'Packages count: ' . $result['count'] . PHP_EOL;

    
    $stmt = $pdo->query('SHOW TABLES LIKE "bookings"');
    if ($stmt->fetch()) {
        echo 'Bookings table exists' . PHP_EOL;
    } else {
        echo 'Bookings table does not exist' . PHP_EOL;
    }


    $stmt = $pdo->query('SELECT COUNT(*) as count FROM hotels');
    $result = $stmt->fetch();
    echo 'Hotels count: ' . $result['count'] . PHP_EOL;

} catch (Exception $e) {
    echo 'Database error: ' . $e->getMessage() . PHP_EOL;
}
?>