<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$pdo = getPDO();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $price    = (float)($_POST['price'] ?? 0);
    $duration = trim($_POST['duration'] ?? '');
    $desc     = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? 'adventure');
    $imageUrl = trim($_POST['image_url'] ?? '');

    if ($title === '' || $price <= 0 || $duration === '') {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO packages (title, price, duration, description, category, image_url) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $price, $duration, $desc, $category, $imageUrl]);
            $message = "Package '{$title}' added successfully!";
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
    <title>Add Package | GlobeTrek Admin</title>
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
            <div class="dash-hero-badge"><i class="fas fa-suitcase-rolling"></i> Admin</div>
            <h2>Add New Package</h2>
            <p>Create a new tour package for customers to explore and book.</p>
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
                    <a href="../admin/dashboard.php" class="admin-back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
                <div>
                    <label class="form-label-admin">Package Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Cultural Triangle Explorer" class="form-input-admin">
                </div>
                <div>
                    <label class="form-label-admin">Category *</label>
                    <select name="category" class="form-input-admin">
                        <option value="adventure">Adventure</option>
                        <option value="cultural">Cultural</option>
                        <option value="wildlife">Wildlife</option>
                        <option value="beach">Beach</option>
                        <option value="nature">Nature</option>
                    </select>
                </div>
                <div>
                    <label class="form-label-admin">Duration *</label>
                    <input type="text" name="duration" required placeholder="e.g. 8 Days / 7 Nights" class="form-input-admin">
                </div>
                <div>
                    <label class="form-label-admin">Price (USD) *</label>
                    <input type="number" name="price" required min="1" step="0.01" class="form-input-admin">
                </div>
                <div>
                    <label class="form-label-admin">Description</label>
                    <textarea name="description" rows="4" class="form-input-admin admin-textarea" placeholder="Describe the package highlights and itinerary..."></textarea>
                </div>
                <div>
                    <label class="form-label-admin">Image URL</label>
                    <input type="text" name="image_url" placeholder="e.g. images/my-package-photo.jpg or a full URL" class="form-input-admin">
                </div>
                <button type="submit" class="btn-book btn-admin-submit"><i class="fas fa-plus"></i> Add Package</button>
            </form>
        </section>
    </div>
    <footer class="footer"></footer>
    <script src="../js/script.js"></script>
</body>
</html>
