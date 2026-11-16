<?php
require_once __DIR__ . '/../php/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$destination = trim($_POST['destination'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $destination === '' || $message === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please fill in all contact form fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

try {
    $pdo = getPDO();
    ensureContactSchema($pdo);

    $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, destination, message) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $email, $destination, $message]);

    echo json_encode([
        'success' => true,
        'message' => 'Your message has been sent successfully. Our team will contact you soon.'
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'We could not send your message right now. Please try again.'
    ]);
}
