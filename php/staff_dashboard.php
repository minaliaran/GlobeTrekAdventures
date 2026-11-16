<?php

require_once __DIR__ . '/auth.php';
requireStaff();

$pdo = getPDO();
$pageTitle = "Staff Dashboard | GlobeTrek";
$message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['booking_id'])) {
    $bookingId = (int)$_POST['booking_id'];
    $action = $_POST['action'];
    $newStatus = ($action === 'confirm') ? 'confirmed' : 'cancelled';

    try {
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        if ($stmt->execute([$newStatus, $bookingId])) {
            $message = "Booking #$bookingId has been " . $newStatus . ".";
        }
    } catch (PDOException $e) {
        $message = "Error updating booking: " . $e->getMessage();
    }
}

$pendingBookings = $pdo->query("
    SELECT b.*, u.name as user_name, u.email as user_email
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    WHERE b.status = 'pending'
    ORDER BY b.created_at ASC 
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
<body class="dashboard-body staff-theme">
    <header class="navbar" id="navbar"></header>

    <div class="dash-container">
        <section class="dash-hero">
            <div class="dash-hero-badge"><i class="fas fa-user-tie"></i> Staff Operations</div>
            <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px;">
                <div>
                    <h2>Staff Dashboard</h2>
                    <p>Manage pending reservations and assist customers with their travel plans.</p>
                </div>
                <a href="logout.php" class="btn-view dash-action-button dash-action-button-reject btn-admin-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </section>

        <section class="dash-table-box">
            <div class="dash-section-head">
                <div>
                    <h3 class="dash-section-title">Pending Approvals</h3>
                    <p class="dash-section-copy">New bookings that require staff review and confirmation.</p>
                </div>
            </div>

            <?php if ($message !== ''): ?>
                <div class="dash-empty-state dash-flash-message">
                    <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="dash-table-container">
                <?php if (empty($pendingBookings)): ?>
                    <div class="dash-empty-state">
                        <i class="fas fa-check-circle dash-success-icon"></i>
                        <p>All caught up! No pending bookings to review.</p>
                    </div>
                <?php else: ?>
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Booking Type</th>
                                <th>Service</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingBookings as $b): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($b['user_name']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($b['user_email']); ?></small>
                                    </td>
                                    <td><?php echo ucfirst($b['booking_type']); ?></td>
                                    <td><?php echo htmlspecialchars($b['service_name'] ?? 'Trip'); ?></td>
                                    <td>$<?php echo number_format($b['total_price'], 2); ?></td>
                                    <td>
                                        <div class="dash-action-group">
                                            <form action="staff_dashboard.php" method="POST" class="dash-inline-form">
                                                <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                                <input type="hidden" name="action" value="confirm">
                                                <button type="submit" class="btn-book dash-action-button">Confirm</button>
                                            </form>
                                            <form action="staff_dashboard.php" method="POST" class="dash-inline-form">
                                                <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn-view dash-action-button dash-action-button-reject">Reject</button>
                                            </form>
                                        </div>
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
                    <h3 class="dash-section-title">âœ“ Confirmed Payments (Done)</h3>
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
                    <h3 class="dash-section-title">â³ Pending Payments (Not Done)</h3>
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

