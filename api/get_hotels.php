<?php

require_once __DIR__ . '/../php/config.php';

header('Content-Type: application/json');

try {
    $pdo = getPDO();

    $category = strtolower(trim($_GET['category'] ?? 'all'));
    $slug = $_GET['slug'] ?? null;

    if ($slug) {
        $stmt = $pdo->prepare("SELECT * FROM hotels WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        $hotel = $stmt->fetch();
        
        if ($hotel) {
            echo json_encode($hotel);
        } else {
            echo json_encode(['error' => 'Hotel not found']);
        }
    } else {
        if ($category === 'all') {
            $stmt = $pdo->query("SELECT * FROM hotels ORDER BY rating DESC, name ASC");
        } else {
            $stmt = $pdo->prepare("SELECT * FROM hotels WHERE LOWER(TRIM(category)) = ? ORDER BY rating DESC, name ASC");
            $stmt->execute([$category]);
        }
        $hotels = $stmt->fetchAll();
        echo json_encode($hotels);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}
