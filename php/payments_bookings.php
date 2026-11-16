<?php
/**
 * GlobeTrek Adventures - Payments & Bookings Report
 * Path: /admin/payments_bookings.php
 */
require_once __DIR__ . '/../php/auth.php';
requireStaff(); // Allow both admin and staff

$pdo = getPDO();
$pageTitle = "Payments & Bookings | GlobeTrek";

// Get filter parameter
$month = isset($_GET['month']) ? trim($_GET['month']) : date('Y-m');

// Payment Statistics
$stats = $pdo->query("
    SELECT 
        COUNT(*) as total_bookings,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
        SUM(CASE WHEN status = 'confirmed' THEN total_price ELSE 0 END) as revenue_confirmed,
        SUM(CASE WHEN status = 'pending' THEN total_price ELSE 0 END) as revenue_pending
    FROM bookings
    WHERE DATE_FORMAT(created_at, '%Y-%m') = ?
", [$month])->fetch();

// Monthly Bookings Data (last 12 months)
$monthlyData = $pdo->query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        DATE_FORMAT(created_at, '%b %Y') as month_label,
        COUNT(*) as bookings,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'confirmed' THEN total_price ELSE 0 END) as revenue
    FROM bookings
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC
")->fetchAll();

// Payment Details (current month)
$payments = $pdo->prepare("
    SELECT 
        b.id,
        b.booking_type,
        b.service_name,
        b.total_price,
        b.status,
        b.created_at,
        u.name as user_name,
        u.email as user_email,
        b.travelers,
        b.guests
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    WHERE DATE_FORMAT(b.created_at, '%Y-%m') = ?
    ORDER BY b.created_at DESC
");
$payments->execute([$month]);
$paymentDetails = $payments->fetchAll();

// Payment Status Summary
$statusSummary = $pdo->query("
    SELECT 
        status,
        COUNT(*) as count,
        SUM(total_price) as total
    FROM bookings
    GROUP BY status
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
    <link rel="stylesheet" href="../css/payments.css">
</head>
<body class="dashboard-body admin-theme" data-user-role="<?php echo htmlspecialchars($_SESSION['role']); ?>">
    <header class="navbar" id="navbar"></header>

    <div class="dash-container">
        <section class="dash-hero">
            <div class="dash-hero-badge"><i class="fas fa-credit-card"></i> Payments & Bookings</div>
            <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px;">
                <div>
                    <h2>Payment & Booking Reports</h2>
                    <p>Track payments, revenue, and monthly booking statistics.</p>
                </div>
                <a href="../php/logout.php" class="btn-view dash-action-button dash-action-button-reject" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </section>

        
        <div class="payment-header">
            <form method="GET">
                <label>Select Month:</label>
                <input type="month" name="month" value="<?php echo htmlspecialchars($month); ?>">
                <button type="submit"><i class="fas fa-filter"></i> Filter</button>
            </form>
            <a href="dashboard.php">← Back to Dashboard</a>
        </div>

        
        <div class="payment-grid">
            <div class="payment-card confirmed">
                <h4>✓ Confirmed Bookings</h4>
                <div class="value"><?php echo (int)$stats['confirmed'] ?? 0; ?></div>
                <small>Status: Confirmed</small>
            </div>
            <div class="payment-card pending">
                <h4>⏳ Pending Bookings</h4>
                <div class="value"><?php echo (int)$stats['pending'] ?? 0; ?></div>
                <small>Awaiting payment</small>
            </div>
            <div class="payment-card cancelled">
                <h4>✗ Cancelled Bookings</h4>
                <div class="value"><?php echo (int)$stats['cancelled'] ?? 0; ?></div>
                <small>Cancelled</small>
            </div>
            <div class="payment-card revenue">
                <h4>💰 Confirmed Revenue</h4>
                <div class="value">$ <?php echo number_format((float)$stats['revenue_confirmed'] ?? 0, 2); ?></div>
                <small>Paid bookings</small>
            </div>
            <div class="payment-card revenue pending-revenue">
                <h4>⏸ Pending Revenue</h4>
                <div class="value">$ <?php echo number_format((float)$stats['revenue_pending'] ?? 0, 2); ?></div>
                <small>Not yet paid</small>
            </div>
        </div>

        
        <div class="monthly-section">
            <h3><i class="fas fa-chart-pie"></i> Overall Payment Status</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Total Bookings</th>
                            <th>Total Amount</th>
                            <th>Average Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($statusSummary as $row): ?>
                        <tr>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $row['count']; ?></td>
                            <td>$ <?php echo number_format((float)$row['total'], 2); ?></td>
                            <td>$ <?php echo number_format((float)$row['total'] / max(1, $row['count']), 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="monthly-section">
            <h3><i class="fas fa-calendar-alt"></i> Last 12 Months Breakdown</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Total Bookings</th>
                            <th>Confirmed</th>
                            <th>Pending</th>
                            <th>Revenue (Confirmed)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monthlyData as $row): ?>
                        <tr <?php echo $row['month'] === $month ? 'class="current-month"' : ''; ?>>
                            <td><?php echo $row['month_label']; ?></td>
                            <td><?php echo $row['bookings']; ?></td>
                            <td><span class="status-badge status-confirmed"><?php echo $row['confirmed']; ?></span></td>
                            <td><span class="status-badge status-pending"><?php echo $row['pending']; ?></span></td>
                            <td>$ <?php echo number_format((float)$row['revenue'] ?? 0, 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="monthly-section">
            <h3><i class="fas fa-receipt"></i> Payments - <?php echo date('F Y', strtotime($month)); ?></h3>
            <?php if (empty($paymentDetails)): ?>
                <p class="no-bookings">No bookings for this month</p>
            <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Service</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paymentDetails as $row): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_name'] ?? 'N/A'); ?></td>
                            <td><small><?php echo ucfirst($row['booking_type']); ?></small></td>
                            <td><strong>$ <?php echo number_format((float)$row['total_price'], 2); ?></strong></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <script src="../js/payments.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>
