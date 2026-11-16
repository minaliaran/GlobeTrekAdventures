<?php
require_once 'auth.php';

if (currentRole() === 'admin') {
    header('Location: ' . appUrl('admin/dashboard.php'));
    exit;
}

if (currentRole() === 'staff') {
    header('Location: ' . appUrl('admin/staff_dashboard.php'));
    exit;
}

requireCustomer();

$bookings = [];
$upcomingTrips = [];
$bookingMessage = '';
$today = date('Y-m-d');
$summary = [
    'upcoming' => 0,
    'journeys' => 0,
    'spend' => 0,
    'pending' => 0
];

try {
    $pdo = getPDO();
    ensureBookingSchema($pdo);
    ensurePackageSchema($pdo);

    $bookingStmt = $pdo->prepare("
        SELECT
            b.id,
            b.booking_type,
            COALESCE(b.service_name, p.title) AS booking_name,
            b.travel_date,
            b.check_in,
            b.check_out,
            b.travelers,
            b.guests,
            b.pickup_location,
            b.dropoff_location,
            b.guest_name,
            b.total_price,
            b.status,
            b.created_at
        FROM bookings b
        LEFT JOIN packages p ON b.package_id = p.id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC
        LIMIT 8
    ");
    $bookingStmt->execute([$_SESSION['user_id']]);
    $bookings = $bookingStmt->fetchAll();

    $upcomingStmt = $pdo->prepare("
        SELECT
            b.booking_type,
            COALESCE(b.service_name, p.title) AS booking_name,
            b.travel_date,
            b.check_in,
            b.check_out,
            b.guests,
            b.travelers,
            b.total_price,
            b.status
        FROM bookings b
        LEFT JOIN packages p ON b.package_id = p.id
        WHERE b.user_id = ?
          AND b.status <> 'cancelled'
          AND COALESCE(b.check_in, b.travel_date) >= ?
        ORDER BY COALESCE(b.check_in, b.travel_date) ASC
        LIMIT 3
    ");
    $upcomingStmt->execute([$_SESSION['user_id'], $today]);
    $upcomingTrips = $upcomingStmt->fetchAll();

    $summaryStmt = $pdo->prepare("
        SELECT
            COUNT(*) AS journeys,
            COALESCE(SUM(total_price), 0) AS spend,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
            SUM(
                CASE
                    WHEN status <> 'cancelled' AND COALESCE(check_in, travel_date) >= ?
                    THEN 1
                    ELSE 0
                END
            ) AS upcoming
        FROM bookings
        WHERE user_id = ?
    ");
    $summaryStmt->execute([$today, $_SESSION['user_id']]);
    $summaryRow = $summaryStmt->fetch();

    if ($summaryRow) {
        $summary = $summaryRow;
    }
} catch (PDOException $e) {
    $bookingMessage = 'We could not load your dashboard details right now.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - GlobeTrek Adventures</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navigation.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body class="dashboard-body">
    <header class="navbar" id="navbar"></header>

    <div class="dash-container">
        <section class="dash-hero">
            <div class="dash-hero-badge"><i class="fas fa-leaf"></i> Customer Space</div>
            <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px;">
                <div>
                    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? 'Traveller'); ?>!</h2>
                    <p>Use this simple space to check your trips, open booking history, and make a new reservation without extra clutter.</p>
                </div>
                <a href="logout.php" class="btn-view dash-action-button dash-action-button-reject btn-admin-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </section>

        <div class="dash-stat-grid">
            <div class="dash-stat-card border-primary">
                <h4>Upcoming Trips</h4>
                <div class="value"><?php echo number_format((int) ($summary['upcoming'] ?? 0)); ?></div>
                <span class="dash-stat-note"><i class="fas fa-suitcase-rolling"></i> Confirmed and pending journeys</span>
            </div>

            <div class="dash-stat-card border-blue">
                <h4>Total Bookings</h4>
                <div class="value"><?php echo number_format((int) ($summary['journeys'] ?? 0)); ?></div>
                <span class="dash-stat-note"><i class="fas fa-map-marked-alt"></i> Your full booking record</span>
            </div>

            <div class="dash-stat-card border-accent">
                <h4>Total Spend</h4>
                <div class="value">$<?php echo number_format((float) ($summary['spend'] ?? 0), 2); ?></div>
                <span class="dash-stat-note"><i class="fas fa-wallet"></i> Total booking value</span>
            </div>

            <div class="dash-stat-card border-purple">
                <h4>Pending Actions</h4>
                <div class="value"><?php echo number_format((int) ($summary['pending'] ?? 0)); ?></div>
                <span class="dash-stat-note"><i class="fas fa-clock"></i> Bookings waiting to confirm</span>
            </div>
        </div>

        <div class="dash-action-grid">
            <a href="booking.php" class="dash-action-card">
                <div class="dash-action-icon"><i class="fas fa-plus-circle"></i></div>
                <h3>New Booking</h3>
                <p>Create a new tour, hotel, or transport booking.</p>
            </a>

            <a href="my_bookings.php" class="dash-action-card">
                <div class="dash-action-icon"><i class="fas fa-history"></i></div>
                <h3>Booking History</h3>
                <p>Review your past and upcoming bookings in one place.</p>
            </a>

            <a href="<?php echo appUrl('php/packages.php'); ?>" class="dash-action-card">
                <div class="dash-action-icon"><i class="fas fa-compass"></i></div>
                <h3>Browse Packages</h3>
                <p>See travel packages and plan your next journey.</p>
            </a>
        </div>

        <section class="dash-visual-card dash-simple-section">
            <h3>Upcoming Bookings</h3>

            <?php if ($bookingMessage !== ''): ?>
                <div class="dash-empty-state">
                    <i class="fas fa-circle-exclamation"></i>
                    <p><?php echo htmlspecialchars($bookingMessage); ?></p>
                </div>
            <?php elseif (empty($upcomingTrips)): ?>
                <div class="dash-empty-state">
                    <i class="fas fa-calendar-check"></i>
                    <h4>No upcoming trips yet</h4>
                    <p>Once you make a new booking, your next journeys will appear here.</p>
                    <a href="booking.php" class="dash-card-link">Plan a Trip</a>
                </div>
            <?php else: ?>
                <div class="dash-trip-grid">
                    <?php foreach ($upcomingTrips as $index => $trip): ?>
                        <?php
                        $visualClass = 'dash-visual-' . (($index % 6) + 1);
                        $status = strtolower((string) ($trip['status'] ?? 'pending'));
                        $people = ($trip['booking_type'] ?? '') === 'hotel'
                            ? ((int) ($trip['guests'] ?? 1)) . ' guests'
                            : ((int) ($trip['travelers'] ?? 1)) . ' travelers';
                        $dateLabel = ($trip['booking_type'] ?? '') === 'hotel'
                            ? date('M d', strtotime((string) $trip['check_in'])) . ' to ' . date('M d, Y', strtotime((string) $trip['check_out']))
                            : (!empty($trip['travel_date']) ? date('M d, Y', strtotime((string) $trip['travel_date'])) : 'Date pending');
                        ?>
                        <article class="dash-trip-card">
                            <div class="dash-trip-visual <?php echo $visualClass; ?>">
                                <span class="dash-chip <?php echo htmlspecialchars($status); ?>">
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo htmlspecialchars(ucfirst($status)); ?>
                                </span>
                            </div>
                            <div class="dash-trip-body">
                                <h4><?php echo htmlspecialchars((string) ($trip['booking_name'] ?? 'Booking')); ?></h4>
                                <div class="dash-trip-meta">
                                    <span><i class="fas fa-calendar"></i> <?php echo htmlspecialchars($dateLabel); ?></span>
                                    <span><i class="fas fa-users"></i> <?php echo htmlspecialchars($people); ?></span>
                                </div>
                                <div class="dash-trip-footer">
                                    <span class="dash-price">$<?php echo number_format((float) $trip['total_price'], 2); ?></span>
                                    <a href="my_bookings.php" class="dash-card-link">View Details</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="dash-table-box">
            <div class="dash-section-head">
                <div>
                    <div class="dash-welcome-badge"><i class="fas fa-clock-rotate-left"></i> Recent Activity</div>
                    <h3 class="dash-section-title">Booking History</h3>
                    <p class="dash-section-copy">See your latest bookings, travel dates, and current status in one place.</p>
                </div>
                <a href="my_bookings.php" class="dash-card-link">Open Full History</a>
            </div>

            <?php if ($bookingMessage !== ''): ?>
                <div class="dash-empty-state">
                    <i class="fas fa-circle-exclamation"></i>
                    <p><?php echo htmlspecialchars($bookingMessage); ?></p>
                </div>
            <?php elseif (empty($bookings)): ?>
                <div class="dash-empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h4>No bookings yet</h4>
                    <p>Your booking history will appear here after you make your first reservation.</p>
                    <a href="booking.php" class="dash-card-link">Make a Booking</a>
                </div>
            <?php else: ?>
                <div class="dash-table-container">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Booking</th>
                                <th>Travel Date</th>
                                <th>Details</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Booked On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>
                                        <span class="dash-status-chip dash-status-neutral">
                                            <?php echo htmlspecialchars(ucfirst((string) $booking['booking_type'])); ?>
                                        </span>
                                    </td>
                                    <td class="dash-table-strong"><?php echo htmlspecialchars((string) ($booking['booking_name'] ?? '')); ?></td>
                                    <td>
                                        <?php if (($booking['booking_type'] ?? '') === 'hotel'): ?>
                                            <?php echo date('M d, Y', strtotime((string) $booking['check_in'])); ?>
                                            <span class="dash-table-muted">to</span>
                                            <?php echo date('M d, Y', strtotime((string) $booking['check_out'])); ?>
                                        <?php else: ?>
                                            <?php echo !empty($booking['travel_date']) ? date('M d, Y', strtotime((string) $booking['travel_date'])) : '-'; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (($booking['booking_type'] ?? '') === 'hotel'): ?>
                                            <?php echo !empty($booking['guest_name']) ? htmlspecialchars((string) $booking['guest_name']) : 'Hotel stay'; ?>
                                        <?php else: ?>
                                            <?php
                                            $pickup = trim((string) ($booking['pickup_location'] ?? ''));
                                            $dropoff = trim((string) ($booking['dropoff_location'] ?? ''));
                                            echo ($pickup !== '' || $dropoff !== '') ? htmlspecialchars(trim($pickup . ' -> ' . $dropoff, ' ->')) : 'Trip booking';
                                            ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="dash-table-strong">$<?php echo number_format((float) $booking['total_price'], 2); ?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'dash-status-neutral';
                                        if (($booking['status'] ?? '') === 'confirmed') {
                                            $statusClass = 'dash-status-confirmed';
                                        } elseif (($booking['status'] ?? '') === 'pending') {
                                            $statusClass = 'dash-status-pending';
                                        } elseif (($booking['status'] ?? '') === 'cancelled') {
                                            $statusClass = 'dash-status-cancelled';
                                        }
                                        ?>
                                        <span class="dash-status-chip <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars(ucfirst((string) $booking['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="dash-table-muted"><?php echo date('Y-m-d', strtotime((string) $booking['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../js/script.js"></script>

