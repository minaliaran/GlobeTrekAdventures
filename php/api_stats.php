<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'stats' => [],
    'message' => ''
];


if (!isLoggedIn() || (!in_array(currentRole(), ['admin', 'staff'], true))) {
    $response['message'] = 'Unauthorized access.';
    echo json_encode($response);
    exit;
}

try {
    $pdo = getPDO();

    
    $stmt = $pdo->query("SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE status != 'cancelled'");
    $revenue = $stmt->fetchColumn();

    
    $stmt = $pdo->query("SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE payment_status = 'paid'");
    $collectedRevenue = $stmt->fetchColumn();

  
    $stmt = $pdo->query("SELECT COUNT(*) FROM bookings");
    $totalBookings = $stmt->fetchColumn();

   
    $stmt = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'");
    $pendingBookings = $stmt->fetchColumn();

  
    $stmt = $pdo->query("SELECT COUNT(*) FROM bookings WHERE payment_status = 'unpaid' AND status != 'cancelled'");
    $unpaidBookings = $stmt->fetchColumn();


    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages");
    $totalQueries = $stmt->fetchColumn();

   
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'");
    $totalUsers = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM packages");
    $totalPackages = $stmt->fetchColumn();

  
    $stmt = $pdo->query("SELECT COUNT(*) FROM hotels");
    $totalHotels = $stmt->fetchColumn();

    $response['success'] = true;
    $response['stats'] = [
        'revenue' => number_format($revenue, 2),
        'collected' => number_format($collectedRevenue, 2),
        'bookings' => $totalBookings,
        'pending' => $pendingBookings,
        'unpaid' => $unpaidBookings,
        'queries' => $totalQueries,
        'users' => $totalUsers,
        'packages' => $totalPackages,
        'hotels' => $totalHotels
    ];

} catch (Exception $e) {
    error_log("Admin API Stats Error: " . $e->getMessage());
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);