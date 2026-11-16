<?php

require_once __DIR__ . '/../php/auth.php';
requireAdmin();

$pdo = getPDO();
$pageTitle = "Admin Dashboard | GlobeTrek";
$message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['booking_id'])) {
    $bookingId = (int)$_POST['booking_id'];
    $newStatus = ($_POST['action'] === 'confirm') ? 'confirmed' : 'cancelled';
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    if ($stmt->execute([$newStatus, $bookingId])) {
        $message = "Booking #$bookingId has been " . $newStatus . ".";
    }
}

$userCount    = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$bookingCount = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$hotelCount   = $pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();
$packageCount = $pdo->query("SELECT COUNT(*) FROM packages")->fetchColumn();


$allBookings = $pdo->query("
    SELECT b.*, u.name as user_name, u.email as user_email
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    ORDER BY b.created_at DESC
    LIMIT 30
")->fetchAll();


$confirmedPayments = $pdo->query("
    SELECT b.*, u.name as user_name, u.email as user_email
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    WHERE b.status = 'confirmed'
    ORDER BY b.created_at DESC
    LIMIT 20
")->fetchAll();


$pendingPayments = $pdo->query("
    SELECT b.*, u.name as user_name, u.email as user_email
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    WHERE b.status = 'pending'
    ORDER BY b.created_at DESC
    LIMIT 20
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navigation.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/admin-forms.css">
</head>
<body class="dashboard-body admin-theme">
    <header class="navbar" id="navbar"></header>

    <div class="dash-container">
        <section class="dash-hero">
            <div class="dash-hero-badge"><i class="fas fa-user-shield"></i> Administration</div>
            <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px;">
                <div>
                    <h2>Admin Control Panel</h2>
                    <p>Full control over GlobeTrek's content, bookings, and users.</p>
                </div>
                <a href="../php/logout.php" class="btn-view dash-action-button dash-action-button-reject btn-admin-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </section>

        
        <div class="dash-stat-grid">
            <div class="dash-stat-card border-primary">
                <h4>Total Customers</h4>
                <div class="value"><?php echo $userCount; ?></div>
                <span class="dash-stat-note"><i class="fas fa-users"></i> Registered customers</span>
            </div>
            <div class="dash-stat-card border-blue">
                <h4>Total Bookings</h4>
                <div class="value"><?php echo $bookingCount; ?></div>
                <span class="dash-stat-note"><i class="fas fa-calendar-check"></i> All reservations</span>
            </div>
            <div class="dash-stat-card border-accent">
                <h4>Hotels Listed</h4>
                <div class="value"><?php echo $hotelCount; ?></div>
                <span class="dash-stat-note"><i class="fas fa-hotel"></i> Accommodations</span>
            </div>
            <div class="dash-stat-card border-purple">
                <h4>Tour Packages</h4>
                <div class="value"><?php echo $packageCount; ?></div>
                <span class="dash-stat-note"><i class="fas fa-suitcase"></i> Active packages</span>
            </div>
        </div>

        
        <section class="dash-table-box dash-section-margin">
            <div class="dash-section-head">
                <div>
                    <h3 class="dash-section-title">Content Management</h3>
                    <p class="dash-section-copy">Add new listings to the GlobeTrek platform.</p>
                </div>
            </div>
            <div class="dash-stat-grid dash-stat-grid-auto">
                <a href="../php/admin_add_hotel.php" class="dash-stat-card border-accent dash-card-link">
                    <i class="fas fa-hotel icon-2rem icon-success"></i>
                    <h4 class="card-h4">Add New Hotel</h4>
                </a>
                <a href="../php/admin_add_package.php" class="dash-stat-card border-blue dash-card-link">
                    <i class="fas fa-suitcase-rolling icon-2rem icon-blue"></i>
                    <h4 class="card-h4">Add New Package</h4>
                </a>
                <a href="../php/admin_add_destination.php" class="dash-stat-card border-primary dash-card-link">
                    <i class="fas fa-map-marker-alt icon-2rem icon-green"></i>
                    <h4 class="card-h4">Add Destination</h4>
                </a>
                <a href="../php/admin_add_activity.php" class="dash-stat-card border-purple dash-card-link">
                    <i class="fas fa-hiking icon-2rem icon-purple"></i>
                    <h4 class="card-h4">Add Things To Do</h4>
                </a>
            </div>
        </section>

        
        <section class="dash-table-box">
            <div class="dash-section-head">
                <div>
                    <h3 class="dash-section-title">All Bookings</h3>
                    <p class="dash-section-copy">Confirm or cancel any customer booking across the platform.</p>
                </div>
            </div>

            <?php if ($message !== ''): ?>
                <div class="dash-empty-state dash-flash-message">
                    <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="dash-table-container">
                <?php if (empty($allBookings)): ?>
                    <div class="dash-empty-state">
                        <i class="fas fa-inbox icon-2rem icon-success"></i>
                        <p>No bookings found yet.</p>
                    </div>
                <?php else: ?>
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Service</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allBookings as $b): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($b['user_name']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($b['user_email']); ?></small>
                                    </td>
                                    <td><span class="dash-status-chip dash-status-neutral"><?php echo ucfirst($b['booking_type']); ?></span></td>
                                    <td><?php echo htmlspecialchars($b['service_name'] ?? 'Trip'); ?></td>
                                    <td>$<?php echo number_format($b['total_price'], 2); ?></td>
                                    <td><span class="dash-status-chip dash-status-<?php echo $b['status']; ?>"><?php echo ucfirst($b['status']); ?></span></td>
                                    <td><?php echo date('Y-m-d', strtotime($b['created_at'])); ?></td>
                                    <td>
                                        <?php if ($b['status'] === 'pending'): ?>
                                        <div class="dash-action-group">
                                            <form action="dashboard.php" method="POST" class="dash-inline-form">
                                                <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                                <input type="hidden" name="action" value="confirm">
                                                <button type="submit" class="btn-book dash-action-button">Confirm</button>
                                            </form>
                                            <form action="dashboard.php" method="POST" class="dash-inline-form">
                                                <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn-view dash-action-button dash-action-button-reject">Cancel</button>
                                            </form>
                                        </div>
                                        <?php else: ?>
                                            <span class="dash-action-placeholder">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>

        
        <section class="dash-table-box">
            <div class="dash-section-head">
                <div>
                    <h3 class="dash-section-title">✓ Confirmed Payments (Done)</h3>
                    <p class="dash-section-copy">Successfully paid bookings and confirmed reservations.</p>
                </div>
            </div>

            <div class="dash-table-container">
                <?php if (empty($confirmedPayments)): ?>
                    <div class="dash-empty-state">
                        <i class="fas fa-hourglass-end dash-neutral-icon"></i>
                        <p>No confirmed payments yet.</p>
                    </div>
                <?php else: ?>
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($confirmedPayments as $b): ?>
                                <tr class="row-confirmed">
                                    <td><strong>#<?php echo $b['id']; ?></strong></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($b['user_name']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($b['user_email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($b['service_name'] ?? 'Trip'); ?></td>
                                    <td><strong>$ <?php echo number_format($b['total_price'], 2); ?></strong></td>
                                    <td><?php echo date('M d, Y', strtotime($b['created_at'])); ?></td>
                                    <td><span class="status-badge-confirmed">Confirmed</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>

        
        <section class="dash-table-box">
            <div class="dash-section-head">
                <div>
                    <h3 class="dash-section-title"> Pending Payments (Not Done)</h3>
                    <p class="dash-section-copy">Bookings awaiting payment confirmation.</p>
                </div>
            </div>

            <div class="dash-table-container">
                <?php if (empty($pendingPayments)): ?>
                    <div class="dash-empty-state">
                        <i class="fas fa-check-circle dash-success-icon"></i>
                        <p>No pending payments!</p>
                    </div>
                <?php else: ?>
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingPayments as $b): ?>
                                <tr class="row-pending">
                                    <td><strong>#<?php echo $b['id']; ?></strong></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($b['user_name']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($b['user_email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($b['service_name'] ?? 'Trip'); ?></td>
                                    <td><strong>$ <?php echo number_format($b['total_price'], 2); ?></strong></td>
                                    <td><?php echo date('M d, Y', strtotime($b['created_at'])); ?></td>
                                    <td><span class="status-badge-pending">Pending</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <footer class="footer"></footer>
    <script src="../js/script.js"></script>
</body>
</html>
