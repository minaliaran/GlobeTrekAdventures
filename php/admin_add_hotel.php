<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$pdo = getPDO();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name'] ?? '');
    $slug      = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
    $category  = trim($_POST['category'] ?? 'boutique');
    $location  = trim($_POST['location'] ?? '');
    $price     = (float)($_POST['price_per_night'] ?? 0);
    $desc      = trim($_POST['description'] ?? '');

    if ($name === '' || $location === '' || $price <= 0) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO hotels (slug, name, category, location, price_per_night, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$slug, $name, $category, $location, $price, $desc]);
            $message = "Hotel '{$name}' added successfully!";
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Hotel | GlobeTrek Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            <div class="dash-hero-badge"><i class="fas fa-hotel"></i> Admin</div>
            <h2>Add New Hotel</h2>
            <p>Add a new accommodation listing to the GlobeTrek platform.</p>
        </section>

        <section class="dash-table-box">
            <?php if ($message): ?>
                <div class="dash-empty-state flash-message <?php echo $messageType; ?>">
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form method="POST" class="admin-form-container">
                <div class="dash-section-head">
                    <a href="../admin/dashboard.php" class="admin-form-back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
                <div class="admin-form-group">
                    <label>Hotel Name *</label>
                    <input type="text" name="name" required>
                </div>
                <div class="admin-form-group">
                    <label>Category *</label>
                    <select name="category">
                        <option value="luxury">Luxury</option>
                        <option value="boutique" selected>Boutique</option>
                        <option value="eco">Eco Lodge</option>
                        <option value="budget">Budget</option>
                    </select>
                </div>
                <div class="admin-form-group">
                    <label>Location *</label>
                    <input type="text" name="location" required placeholder="e.g. Colombo">
                </div>
                <div class="admin-form-group">
                    <label>Price Per Night (USD) *</label>
                    <input type="number" name="price_per_night" required min="1" step="0.01">
                </div>
                <div class="admin-form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"></textarea>
                </div>
                <button type="submit" class="btn-book admin-form-button"><i class="fas fa-plus"></i> Add Hotel</button>
            </form>
        </section>
    </div>
    <footer class="footer"></footer>
    <script src="../js/script.js"></script>
</body>
</html>
