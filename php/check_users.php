<?php
require_once __DIR__ . '/config.php';

try {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT name, email, role FROM users");
    $users = $stmt->fetchAll();
    
    echo "Current Users in Database:\n";
    foreach ($users as $u) {
        echo "- {$u['name']} ({$u['email']}) Role: {$u['role']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
