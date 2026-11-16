<?php

$baseUrl = appUrl('');


$current_page = basename($_SERVER['PHP_SELF']);
$is_packages = ($current_page === 'packages.php');
$is_accommodation = ($current_page === 'accommodation.php' || $current_page === 'hotel.php');
?>
<header class="navbar" id="navbar" data-standardized="true">
    <div class="nav-left-group">
        <div class="nav-logo">
            <a href="<?php echo appUrl('index.php'); ?>" class="logo-link">
                <img src="<?php echo appUrl('images/logo.jpg'); ?>" alt="GlobeTrek Adventures" class="logo-img">
            </a>
        </div>
        <nav class="nav-menu" id="nav-menu">
            <div class="nav-links-wrapper">
                <ul class="nav-links">
                    <li><a href="<?php echo appUrl('index.php'); ?>" data-nav-key="home">Home</a></li>
                    <li><a href="<?php echo appUrl('php/about.php'); ?>" data-nav-key="about">About</a></li>
                    <li class="dropdown">
                        <a href="#" data-nav-key="explore">Explore</a>
                        <div class="dropdown-panel">
                            <ul>
                                <li><a href="<?php echo appUrl('destinations/destinations.html'); ?>">Destinations</a></li>
                                <li><a href="<?php echo appUrl('thingstodo/things-to-do.html'); ?>">Things to Do</a></li>
                                <li><a href="<?php echo appUrl('php/packages.php'); ?>">Packages</a></li>
                                <li><a href="<?php echo appUrl('php/accommodation.php'); ?>">Accommodation</a></li>
                            </ul>
                        </div>
                    </li>
                    <li><a href="<?php echo appUrl('gallery.html'); ?>" data-nav-key="gallery">Gallery</a></li>
                    <li class="dropdown">
                        <a href="#" data-nav-key="services">Services</a>
                        <div class="dropdown-panel">
                            <ul>
                                <li><a href="<?php echo appUrl('transport.html'); ?>" data-nav-key="transport">Transportation</a></li>
                                <li><a href="<?php echo appUrl('guide.html'); ?>" data-nav-key="guide">Travel Guide</a></li>
                                <li><a href="<?php echo appUrl('php/booking.php'); ?>" data-nav-key="booking">Booking</a></li>
                                <li><a href="<?php echo appUrl('contact.html'); ?>" data-nav-key="contact">Contact</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="nav-search-central">
        <div class="nav-search-trigger" id="search-btn">
            <i class="fas fa-search"></i>
        </div>
    </div>

    <div class="nav-actions" id="nav-actions">
        <div id="auth-links">
        <?php if (isset($_SESSION['user_id'])): 
            try {
                $pdo = getPDO();
                $stmt = $pdo->prepare("SELECT name, role, profile_photo FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch();
            } catch (Exception $e) {
                $user = null;
            }
            
            if ($user):
                $dashboardPage = 'php/dashboard.php';
                if ($user['role'] === 'admin') $dashboardPage = 'admin/dashboard.php';
                if ($user['role'] === 'staff') $dashboardPage = 'admin/staff_dashboard.php';

                $currentPage = $_SERVER['PHP_SELF'];

        ?>
            <div class="nav-profile-wrap">
                <a href="<?php echo appUrl($dashboardPage); ?>" class="nav-avatar-link">
                    <img src="<?php echo appUrl($user['profile_photo'] ?: 'images/default-avatar.png'); ?>" alt="Profile" class="nav-avatar">
                </a>
            </div>
            <?php else: ?>
                <a href="<?php echo appUrl('php/login.php'); ?>" class="nav-btn-ghost">Login</a>
                <a href="<?php echo appUrl('php/register.php'); ?>" class="nav-btn-ghost">Register</a>
                
            <?php endif; ?>
        <?php else: ?>
            <a href="<?php echo appUrl('php/login.php'); ?>" class="nav-btn-ghost">Login</a>
            <a href="<?php echo appUrl('php/register.php'); ?>" class="nav-btn-ghost">Register</a>
        <?php endif; ?>
        </div>

        <button class="btn-hamburger" id="menu-btn"><span></span><span></span><span></span></button>
    </div>
</header>
