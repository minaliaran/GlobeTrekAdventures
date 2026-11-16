<?php
require_once __DIR__ . '/../php/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$token = trim($_POST['token'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($token === '' || $password === '' || $confirmPassword === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please complete all password reset fields.']);
    exit;
}

if ($password !== $confirmPassword) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
    exit;
}

try {
    $pdo = getPDO();
    ensurePasswordResetSchema($pdo);

    $stmt = $pdo->query('SELECT id, user_id, token_hash, expires_at, used_at FROM password_resets WHERE used_at IS NULL ORDER BY id DESC');
    $reset = null;

    foreach ($stmt->fetchAll() as $row) {
        if (password_verify($token, $row['token_hash'])) {
            $reset = $row;
            break;
        }
    }

    if (!$reset) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'This password reset link is invalid.']);
        exit;
    }

    if (strtotime((string) $reset['expires_at']) < time()) {
        http_response_code(410);
        echo json_encode(['success' => false, 'message' => 'This password reset link has expired.']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([$hashedPassword, $reset['user_id']]);
    $pdo->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = ?')->execute([$reset['id']]);

    echo json_encode(['success' => true, 'message' => 'Your password has been updated successfully.']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not reset your password right now.']);
}
