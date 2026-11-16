<?php
require_once __DIR__ . '/config.php';

$pageTitle = "Accommodations | GlobeTrek Adventures";
$bodyClass = "accommodation-page";

try {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT * FROM hotels ORDER BY rating DESC");
    $hotels = $stmt->fetchAll();
} catch (Exception $e) {
    $hotels = [];
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>


<section class="acc-hero">
    <img src="<?php echo appUrl('images/accomadetion.jpg'); ?>" class="hero-bg" alt="Accommodation">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="acc-hero-content reveal visible">
            <span class="hero-badge"><i class="fas fa-hotel"></i> Handpicked Stays</span>
            <h1 class="hero-title">Stay in <span class="hero-word-accent">Style</span></h1>
            <p class="hero-desc">From luxury resorts to eco-lodges, find your perfect home away from home.</p>
        </div>
    </div>
</section>


<main class="container section hotel-list-main">
    <div class="hotel-grid hotel-grid-dynamic">
        <?php if (!empty($hotels)): ?>
            <?php foreach ($hotels as $h): ?>
                <div class="hotel-card hotel-card-style">
                    <div class="hotel-img hotel-card-img-wrap">
                        <img src="<?php echo appUrl($h['image_url']); ?>" alt="<?php echo htmlspecialchars($h['name']); ?>" class="hotel-card-img">
                    </div>
                    <div class="hotel-content hotel-card-content">
                        <div class="hotel-tag hotel-card-tag">
                            <?php echo htmlspecialchars($h['category']); ?>
                        </div>
                        <h3 class="hotel-card-title"><?php echo htmlspecialchars($h['name']); ?></h3>
                        <p class="hotel-card-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($h['location']); ?></p>
                        <div class="hotel-footer hotel-card-footer">
                            <div class="price hotel-card-price-wrap">
                                <span class="hotel-card-price-value">$<?php echo number_format($h['price_per_night']); ?></span>
                                <small class="hotel-card-price-unit">/night</small>
                            </div>
                            <a href="<?php echo appUrl('php/hotel.php?slug=' . htmlspecialchars($h['slug'])); ?>" class="btn-primary btn-hotel-details">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No accommodations found.</p>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>