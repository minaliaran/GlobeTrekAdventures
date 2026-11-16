<?php
require_once __DIR__ . '/config.php';

$slug = $_GET['slug'] ?? '';

function hotelAssetUrl(string $path): string
{
    $path = trim($path);

    if ($path === '') {
        return '';
    }
    if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'data:')) {
        return $path;
    }
    return appUrl($path);
}

if (!$slug) {
    header('Location: ../accommodation.html');
    exit;
}

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE slug = ?");
$stmt->execute([$slug]);
$hotel = $stmt->fetch();

if (!$hotel) {
    echo "<div style='padding: 20px; font-family: sans-serif;'>";
    echo "<h1>Hotel Not Found</h1>";
    echo "<p>We couldn't find a hotel with the slug: <strong>" . htmlspecialchars($slug) . "</strong></p>";
    echo "<p>Please check if your database has data in the <code>hotels</code> table.</p>";
    echo "<a href='../accommodation.html'>Back to Accommodations</a>";
    echo "</div>";
    exit;
}

$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT name, email, phone FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}

$mainImage = hotelAssetUrl((string) ($hotel['image_url'] ?? ''));
$images = array_values(array_unique(array_filter(array_map(
    static fn($img) => hotelAssetUrl((string) $img),
    explode(',', (string) ($hotel['gallery'] ?? ''))
))));

if ($mainImage && !in_array($mainImage, $images, true)) {
    array_unshift($images, $mainImage);
}

if (empty($images)) {
    $fallbackImage = appUrl('assets/gallery/default-hotel.jpg');
    $images[] = $fallbackImage;
    if (!$mainImage) {
        $mainImage = $fallbackImage;
    }
}

if (!$mainImage) {
    $mainImage = $images[0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($hotel['name']); ?> | GlobeTrek Adventures</title>
    <link rel="icon" type="image/png" href="<?php echo appUrl('images/favicon.png'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo appUrl('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('css/layout.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('css/components.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('css/pages.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('css/navigation.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('css/responsive.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('css/hotels.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('css/booking.css'); ?>">
</head>
<body class="hotel-detail-page">
    <header class="navbar" id="navbar"></header>

    <section class="hotel-details-hero hotel-details-hero--dynamic" style="background-image: url('<?php echo htmlspecialchars($mainImage); ?>');" data-hero-image="<?php echo htmlspecialchars($mainImage); ?>">
        <div class="hero-overlay"></div>
        <div class="container reveal visible">
            <div class="hero-path">
             
            </div>
            <h1><?php echo htmlspecialchars($hotel['name']); ?></h1>
            <div class="hero-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hotel['location']); ?></div>
        </div>
    </section>

    <section class="hotel-details-body section">
        <div class="container">
            <div class="details-grid">
                <div class="details-main reveal visible">
                    
                    <div class="content-block">
                        <div class="hotel-gallery-head hotel-gallery-count">
                            <h2>Room & Hotel Photos</h2>
                            <div class="hotel-gallery-count">
                                <i class="fas fa-camera"></i>
                                <?php echo count($images); ?> photo<?php echo count($images) === 1 ? '' : 's'; ?>
                            </div>
                        </div>
                        <div class="hotel-gallery-container">
                            <button class="gallery-nav-btn prev-btn" id="gallery-prev" aria-label="Previous image">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            
                            <div class="hotel-feature-slider" id="hotel-gallery-slider">
                                <?php foreach ($images as $img): ?>
                                    <?php if(trim($img) !== ''): ?>
                                        <div class="gallery-item-wrap">
                                            <img
                                                class="hotel-gallery-image"
                                                src="<?php echo htmlspecialchars(trim($img)); ?>"
                                                alt="Hotel Gallery Image"
                                                data-lightbox-src="<?php echo htmlspecialchars(trim($img)); ?>"
                                                data-fallback-src="../images/default-hotel.jpg">
                                            <div class="gallery-hover"><i class="fas fa-expand-alt"></i></div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <button class="gallery-nav-btn next-btn" id="gallery-next" aria-label="Next image">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                    <div class="content-block">
                        <h2>About this experience</h2>
                        <p><?php echo nl2br(htmlspecialchars($hotel['description'])); ?></p>
                    </div>

                    <div class="content-block">
                        <h2>Premium Amenities</h2>
                        <div class="amenities-grid">
                            <?php 
                            $amenities = explode(',', $hotel['amenities']);
                            foreach($amenities as $a): 
                                if(trim($a) !== ''):
                            ?>
                                <div class="amenity-item">
                                    <i class="fas fa-check"></i>
                                    <span><?php echo htmlspecialchars(trim($a)); ?></span>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                    
                    <div class="content-block">
                        <h2>Location Map</h2>
                        <div class="mini-map-placeholder">
                            <iframe 
                                class="hotel-map-frame"
                                width="100%" 
                                height="400" 
                                frameborder="0" 
                                src="https://maps.google.com/maps?q=<?php echo urlencode($hotel['location']); ?>&output=embed" 
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>

                </div> 

                
                <aside class="details-sidebar reveal visible">
                    <div class="booking-sticky-card">
                        <h3 class="hotel-sidebar-title">Stay Summary</h3>
                        <div class="price-header">
                            <span class="price-val">$<?php echo htmlspecialchars($hotel['price_per_night']); ?></span>
                            <span class="price-unit">/ night</span>
                        </div>

                        <div class="booking-msg">
                            View hotel details here, then continue to booking page to confirm your stay.
                        </div>

                        <div class="hotel-detail-info">
                            <strong>Category:</strong>
                            <?php echo htmlspecialchars(ucfirst((string) ($hotel['category'] ?? 'Hotel'))); ?>
                        </div>
                        <div class="hotel-detail-info">
                            <strong>Location:</strong>
                            <?php echo htmlspecialchars((string) $hotel['location']); ?>
                        </div>

                        <a
                            href="booking.php?type=hotel&hotel_id=<?php echo urlencode((string) $hotel['slug']); ?>&category=<?php echo urlencode((string) ($hotel['category'] ?? '')); ?>" 
                            class="btn-primary btn-book-full"
                        >
                            Book This Hotel
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <footer class="footer"></footer>

    
    <div id="hotel-lightbox" class="lightbox-overlay">
        <span class="lightbox-close">&times;</span>
        <img src="" alt="Lightbox">
    </div>

    <script src="<?php echo appUrl('js/script.js'); ?>"></script>
    <script src="<?php echo appUrl('js/hotel-details.js'); ?>"></script>

</body>
</html>
