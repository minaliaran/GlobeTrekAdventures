<?php
require_once __DIR__ . '/config.php';

$pageTitle = "Tour Packages | GlobeTrek Adventures";
$bodyClass = "packages-page";

try {
    $pdo = getPDO();
   
    $stmt = $pdo->query("SELECT * FROM packages ORDER BY id DESC");
    $dbPackages = $stmt->fetchAll();
} catch (Exception $e) {
    $dbPackages = [];
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

 
   
    <section class="pkg-hero">
        <div class="pkg-hero-img-wrap">
            <img src="<?php echo appUrl('images/pckback.png'); ?>" alt="Tour Packages">
        </div>
        <div class="hero-gradient"></div>
        <div class="container hero-content">
            <div class="reveal">
                <span class="hero-badge"><i class="fas fa-boxes-packing"></i> GlobeTrek Experiences</span>
                <h1 class="hero-title">Tour <em>Packages</em></h1>
                <p class="hero-desc">Choose your adventure. From luxury escapes to wild expeditions, we have it all.</p>
            </div>
        </div>

        <div class="hero-scroll-indicator">
            <span>Discover Tours</span>
            <div class="scroll-mouse">
                <div class="scroll-wheel"></div>
            </div>
        </div>
    </section>


<main class="pkg-root">
    <section class="packages-list">
        <div class="container">
            <div class="section-header reveal">
                <p class="section-label">Our Tours</p>
                <h2 class="section-title">Experience Sri Lanka</h2>
            </div>

            <div class="pkg-main-grid">
                <?php if (!empty($dbPackages)): ?>
                    <?php foreach ($dbPackages as $pkg): 
                        
                        $viewPages = [
                            'Cultural Triangle Explorer' => 'packages/cultural-triangle.html',
                            'Southern Shores Escape'     => 'packages/southern-shores.html',
                            'Wild Sri Lanka Safari'      => 'packages/wildlife-safari.html',
                            'Hill Country Tea Trail'     => 'packages/hill-country.html',
                            'East Coast Lagoon Escape'   => 'packages/east-coast.html',
                            'Rainforest & River Adventure' => 'packages/rainforest.html'
                        ];
                        $targetPage = $viewPages[$pkg['title']] ?? 'packages/cultural-triangle.php'; 
                    ?>
                        <div class="pkg-card reveal" data-category="<?php echo htmlspecialchars($pkg['category']); ?>">
                            <div class="pkg-img-wrap">
                                <?php 
                                    $img = $pkg['image_url'];
                                    $displayImg = (strpos($img, 'http') === 0) ? $img : appUrl($img);
                                ?>
                                <img src="<?php echo $displayImg; ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>">
                                <?php if ($pkg['featured']): ?>
                                    <span class="pkg-ribbon">Featured</span>
                                <?php endif; ?>
                            </div>
                            <div class="pkg-content">
                                <div class="pkg-meta">
                                    <span><i class="fas fa-tags"></i> <?php echo ucfirst(htmlspecialchars($pkg['category'])); ?></span>
                                </div>
                                <h3 class="pkg-title"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                                <p class="pkg-desc"><?php echo htmlspecialchars($pkg['description']); ?></p>
                                <div class="pkg-footer">
                                    <div class="pkg-price-wrap">
                                        <span class="pkg-price-label">From</span>
                                        <span class="pkg-price"><span class="currency">$</span><?php echo number_format($pkg['price']); ?></span>
                                    </div>
                                    <a href="<?php echo appUrl($targetPage); ?>" class="btn-pkg">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                   
                    <div id="pkg-empty" class="pkg-empty-state active">
                        <i class="fas fa-compass"></i>
                        <p>No packages found in the database.</p>
                        <span>Try adding some through the Admin Panel</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
