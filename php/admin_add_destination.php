<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$message = '';
$messageType = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $region = trim($_POST['region'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if ($name === '' || $region === '') {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } else {
        
        $message = "Destination '{$name}' request recorded! Create the HTML page at: destinations/" . strtolower(str_replace(' ', '', $name)) . "/" . strtolower(str_replace(' ', '', $name)) . ".html";
        $messageType = 'success';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Destination | GlobeTrek Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navigation.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body class="dashboard-body admin-theme">
    <header class="navbar" id="navbar"></header>
    <div class="dash-container">
        <section class="dash-hero">
            <div class="dash-hero-badge"><i class="fas fa-map-marker-alt"></i> Admin</div>
            <h2>Add New Destination</h2>
            <p>Register a new destination for the GlobeTrek platform.</p>
        </section>

        <section class="dash-table-box">
            <?php if ($message): ?>
                <div class="dash-empty-state dash-flash-message" style="color: <?php echo $messageType === 'success' ? '#10b981' : '#ef4444'; ?>;">
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form method="POST" style="padding: 30px; display: grid; gap: 20px; max-width: 700px;">
                <div class="dash-section-head">
                    <a href="../admin/dashboard.php" style="color: #10b981; font-size: 14px;"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Destination Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Arugam Bay" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Region *</label>
                    <select name="region" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                        <option value="North">North</option>
                        <option value="South" selected>South</option>
                        <option value="East">East</option>
                        <option value="West">West</option>
                        <option value="Central">Central</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Description</label>
                    <textarea name="description" rows="4" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; resize: vertical;" placeholder="Brief description of the destination..."></textarea>
                </div>
                <button type="submit" class="btn-book" style="padding: 14px 30px; width: fit-content;"><i class="fas fa-plus"></i> Register Destination</button>
            </form>
        </section>
    </div>
    <footer class="admin-footer" style="padding: 40px 0; text-align: center; background: #f8fafc; border-top: 1px solid #e2e8f0; margin-top: 50px;">
        <div class="container">
            <p style="color: #64748b; font-size: 14px;">&copy; <?php echo date('Y'); ?> GlobeTrek Adventures. Admin Management Portal.</p>
        </div>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>
