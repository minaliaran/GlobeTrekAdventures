<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$response = [
    'isLoggedIn' => false,
    'user' => null
];

if (isset($_SESSION['user_id'])) {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT id, name, email, role, profile_photo FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if ($user) {
        $response['isLoggedIn'] = true;
        $response['user'] = $user;
    }
}

echo json_encode($response);
