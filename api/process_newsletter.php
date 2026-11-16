<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../php/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

try {
    $pdo = getPDO();
    ensureNewsletterSchema($pdo);

    $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
    $stmt->execute([$email]);

    echo json_encode(['success' => true, 'message' => 'Thank you for subscribing! Check your inbox soon.']);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) { 
        echo json_encode(['success' => true, 'message' => 'You are already subscribed! Thanks for staying connected.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again later.']);
    }
}
