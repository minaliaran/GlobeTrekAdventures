<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$bookings = [];
$message = '';
$flashMessage = '';
$flashType = 'success';

if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $flashMessage = trim((string) ($_GET['msg'] ?? 'Booking successful.'));
}

try {
    $pdo = getPDO();
    ensureBookingSchema($pdo);
    ensurePackageSchema($pdo);
    $stmt = $pdo->prepare('SELECT b.id, b.booking_type, COALESCE(b.service_name, p.title) AS booking_name, b.travel_date, b.check_in, b.check_out, b.travelers, b.guests, b.pickup_location, b.dropoff_location, b.guest_name, b.guest_email, b.total_price, b.status, b.created_at FROM bookings b LEFT JOIN packages p ON b.package_id = p.id WHERE b.user_id = ? ORDER BY b.created_at DESC');
    $stmt->execute([$_SESSION['user_id']]);
    $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = 'Database connection failed.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - GlobeTrek Adventures</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/navigation.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/booking.css">
</head>
<body class="dashboard-body">
    <header class="navbar" id="navbar"></header>

    <div class="booking-page">
        <div class="booking-card">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
                <div>
                    <h1 class="booking-section-title">My Booking History</h1>
                    <p class="booking-section-subtitle">Manage your past and upcoming adventures in Sri Lanka.</p>
                </div>
                <a href="logout.php" class="btn-view dash-action-button dash-action-button-reject btn-admin-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>

            <?php if ($flashMessage !== ''): ?>
                <div class="booking-msg <?php echo htmlspecialchars($flashType); ?>">
                    <?php echo htmlspecialchars($flashMessage); ?>
                </div>
            <?php endif; ?>

            <?php if ($message !== ''): ?>
                <div class="booking-msg error"><?php echo htmlspecialchars($message); ?></div>
            <?php elseif (empty($bookings)): ?>
                <div class="booking-msg booking-empty-state">
                    You do not have any bookings yet. <a href="booking.php" class="booking-empty-state-link">Make your first booking here.</a>
                </div>
            <?php else: ?>
                <div class="booking-table-wrap">
                    <table class="booking-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Service / Package</th>
                                <th>Travel Date</th>
                                <th>Travelers</th>
                                <th>Route / Details</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Date Booked</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>
                                        <span class="booking-status booking-type-pill">
                                            <?php echo htmlspecialchars(ucfirst((string) $booking['booking_type'])); ?>
                                        </span>
                                    </td>
                                    <td class="booking-name-cell">
                                        <?php echo htmlspecialchars((string) ($booking['booking_name'] ?? '')); ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (($booking['booking_type'] ?? '') === 'hotel') {
                                            echo date('M d, Y', strtotime((string) $booking['check_in'])) . ' - ' . date('M d, Y', strtotime((string) $booking['check_out']));
                                        } else {
                                            echo !empty($booking['travel_date']) ? date('M d, Y', strtotime((string) $booking['travel_date'])) : '-';
                                        }
                                        ?>
                                    </td>
                                    <td class="booking-center-cell">
                                        <?php
                                        if (($booking['booking_type'] ?? '') === 'hotel') {
                                            echo (int) ($booking['guests'] ?? 0);
                                        } else {
                                            echo (int) ($booking['travelers'] ?? 0);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (($booking['booking_type'] ?? '') === 'hotel'): ?>
                                            <?php if (!empty($booking['guest_name'])): ?>
                                                <i class="fas fa-user booking-guest-icon"></i>
                                                <?php echo htmlspecialchars((string) $booking['guest_name']); ?>
                                                <?php if (!empty($booking['guest_email'])): ?>
                                                    <br><small class="booking-guest-email"><?php echo htmlspecialchars((string) $booking['guest_email']); ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="booking-empty-dash">-</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php
                                            $route = trim((string) ($booking['pickup_location'] ?? ''));
                                            $dropoff = trim((string) ($booking['dropoff_location'] ?? ''));
                                            if ($route !== '' || $dropoff !== '') {
                                                echo '<i class="fas fa-map-marker-alt booking-route-icon"></i> ';
                                                echo htmlspecialchars($route . ' -> ' . $dropoff);
                                            } else {
                                                echo '<span class="booking-empty-dash">-</span>';
                                            }
                                            ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="booking-price-cell">$<?php echo number_format((float) $booking['total_price'], 2); ?></td>
                                    <td>
                                        <span class="booking-status <?php echo htmlspecialchars(strtolower((string) $booking['status'])); ?>">
                                            <?php echo htmlspecialchars((string) $booking['status']); ?>
                                        </span>
                                    </td>
                                    <td class="booking-date-cell"><?php echo date('Y-m-d', strtotime((string) $booking['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="booking-actions booking-actions-spaced">
                <a class="book-btn book-btn-primary" href="<?php echo appUrl('php/booking.php'); ?>">Make New Booking</a>
                <a class="book-btn book-btn-secondary" href="<?php echo appUrl('php/dashboard.php'); ?>">Back to Dashboard</a>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
<script src="../js/script.js"></script>
