<?php
$baseUrl = appUrl('');
if (substr($baseUrl, -1) !== '/') $baseUrl .= '/';
?>

<footer class="footer">
    <div class="footer-top">
        <div class="footer-brand">
            <div class="footer-logo-wrap">
                <img src="<?php echo $baseUrl; ?>images/logo.jpg" alt="GlobeTrek" class="footer-logo">
                <span class="footer-brand-name">GlobeTrek</span>
            </div>
            <p>Your trusted travel partner for exploring the beautiful island of Sri Lanka. Discover hidden gems and unforgettable adventures with us.</p>
            <div class="footer-socials">
                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h4>Destinations</h4>
            <ul>
                <li><a href="<?php echo $baseUrl; ?>destinations/sigiriya/sigiriya.html">Sigiriya Rock</a></li>
                <li><a href="<?php echo $baseUrl; ?>destinations/ella/ella.html">Ella Highlands</a></li>
                <li><a href="<?php echo $baseUrl; ?>destinations/kandy/kandy.html">Kandy City</a></li>
                <li><a href="<?php echo $baseUrl; ?>destinations/galle/galle.html">Galle Fort</a></li>
                <li><a href="<?php echo $baseUrl; ?>destinations/mirissa/mirissa.html">Mirissa Beach</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo $baseUrl; ?>index.php">Home</a></li>
                <li><a href="<?php echo $baseUrl; ?>php/about.php">About Us</a></li>
                <li><a href="<?php echo $baseUrl; ?>php/packages.php">Tour Packages</a></li>
                <li><a href="<?php echo $baseUrl; ?>gallery.html">Gallery</a></li>
                <li><a href="<?php echo $baseUrl; ?>contact.html">Contact</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Contact Us</h4>
            <ul>
                <li><i class="fas fa-map-marker-alt"></i> Negombo, Sri Lanka</li>
                <li><i class="fas fa-phone"></i> +94 11 234 5678</li>
                <li><i class="fas fa-envelope"></i> hello@globetrek.lk</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> GlobeTrek Adventures • Designed by Minali Arandara</p>
    </div>
</footer>

<script src="<?php echo $baseUrl; ?>js/ui.js"></script>
<script src="<?php echo $baseUrl; ?>js/script.js"></script>

</body>
</html>
