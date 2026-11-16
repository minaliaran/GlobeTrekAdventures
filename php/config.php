<?php
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}

if (!defined('DB_PORT')) {
    define('DB_PORT', '3306');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'globetrek_db');
}

if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}

if (!function_exists('getPDO')) {
    function getPDO(): PDO
    {
        static $pdo = null;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            if ($e->getCode() == 1049) {
                $tempPdo = new PDO(
                    'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8',
                    DB_USER,
                    DB_PASS,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                
                $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                $pdo = new PDO(
                    'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8',
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } else {
                throw $e;
            }
        }

        return $pdo;
    }
}

if (!function_exists('ensureBookingSchema')) {
    function ensureBookingSchema(PDO $pdo): void
    {
        static $checked = false;

        if ($checked) {
            return;
        }

        $checked = true;

        $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            booking_type ENUM('package','transport','hotel') NOT NULL DEFAULT 'package',
            service_name VARCHAR(200) DEFAULT NULL,
            package_id INT NULL,
            hotel_id VARCHAR(50) DEFAULT NULL,
            travel_date DATE DEFAULT NULL,
            check_in DATE DEFAULT NULL,
            check_out DATE DEFAULT NULL,
            travelers INT DEFAULT 1,
            guests INT DEFAULT 1,
            pickup_location VARCHAR(200) DEFAULT NULL,
            dropoff_location VARCHAR(200) DEFAULT NULL,
            pickup_time TIME DEFAULT NULL,
            special_requests TEXT DEFAULT NULL,
            contact_phone VARCHAR(20) DEFAULT NULL,
            guest_name VARCHAR(100) DEFAULT NULL,
            guest_email VARCHAR(100) DEFAULT NULL,
            guest_phone VARCHAR(20) DEFAULT NULL,
            room_type VARCHAR(50) DEFAULT NULL,
            total_price DECIMAL(10, 2) NOT NULL DEFAULT 0,
            status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
            payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
            payment_method VARCHAR(50) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");

        $columnChecks = [
            'booking_type' => "ALTER TABLE bookings MODIFY COLUMN booking_type ENUM('package','transport','hotel') NOT NULL DEFAULT 'package' AFTER user_id",
            'service_name' => "ALTER TABLE bookings ADD COLUMN service_name VARCHAR(200) DEFAULT NULL AFTER booking_type",
            'check_in' => "ALTER TABLE bookings ADD COLUMN check_in DATE DEFAULT NULL AFTER travel_date",
            'check_out' => "ALTER TABLE bookings ADD COLUMN check_out DATE DEFAULT NULL AFTER check_in",
            'guests' => "ALTER TABLE bookings ADD COLUMN guests INT DEFAULT 1 AFTER travelers",
            'hotel_id' => "ALTER TABLE bookings ADD COLUMN hotel_id VARCHAR(50) DEFAULT NULL AFTER package_id",
            'pickup_location' => "ALTER TABLE bookings ADD COLUMN pickup_location VARCHAR(200) DEFAULT NULL AFTER guests",
            'dropoff_location' => "ALTER TABLE bookings ADD COLUMN dropoff_location VARCHAR(200) DEFAULT NULL AFTER pickup_location",
            'pickup_time' => "ALTER TABLE bookings ADD COLUMN pickup_time TIME DEFAULT NULL AFTER dropoff_location",
            'special_requests' => "ALTER TABLE bookings ADD COLUMN special_requests TEXT DEFAULT NULL AFTER pickup_time",
            'contact_phone' => "ALTER TABLE bookings ADD COLUMN contact_phone VARCHAR(20) DEFAULT NULL AFTER special_requests",
            'guest_name' => "ALTER TABLE bookings ADD COLUMN guest_name VARCHAR(100) DEFAULT NULL AFTER contact_phone",
            'guest_email' => "ALTER TABLE bookings ADD COLUMN guest_email VARCHAR(100) DEFAULT NULL AFTER guest_name",
            'guest_phone' => "ALTER TABLE bookings ADD COLUMN guest_phone VARCHAR(20) DEFAULT NULL AFTER guest_email",
            'room_type' => "ALTER TABLE bookings ADD COLUMN room_type VARCHAR(50) DEFAULT NULL AFTER guest_phone",
            'payment_status' => "ALTER TABLE bookings ADD COLUMN payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid' AFTER status",
            'payment_method' => "ALTER TABLE bookings ADD COLUMN payment_method VARCHAR(50) DEFAULT NULL AFTER payment_status",
        ];

        foreach ($columnChecks as $columnName => $alterSql) {
            $stmt = $pdo->prepare("SHOW COLUMNS FROM bookings LIKE ?");
            $stmt->execute([$columnName]);

            if (!$stmt->fetch()) {
                $pdo->exec($alterSql);
            }
        }

        $packageColumn = $pdo->query("SHOW COLUMNS FROM bookings LIKE 'package_id'")->fetch();
        if ($packageColumn && strtoupper((string)($packageColumn['Null'] ?? 'NO')) !== 'YES') {
            $pdo->exec('ALTER TABLE bookings MODIFY package_id INT(11) NULL');
        }
    }
}

if (!function_exists('ensureUserSchema')) {
    function ensureUserSchema(PDO $pdo): void
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

      
        $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'profile_photo'");
        $stmt->execute();
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE users ADD COLUMN profile_photo VARCHAR(255) DEFAULT 'images/default-avatar.png' AFTER role");
        }

        $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'phone'");
        $stmt->execute();
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER profile_photo");
        }

       
        $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($count == 0) {
            $defaultUsers = [
                ['System Admin', 'admin@globetrek.com', '$2y$10$7R5vL7p.P8W9m1M1M1M1Me1M1M1M1M1M1M1M1M1M1M1M1M1M1M1M.', 'admin'],
                ['GlobeTrek Staff', 'staff@globetrek.com', '$2y$10$0zP8W9m1M1M1M1M1M1M1Me1M1M1M1M1M1M1M1M1M1M1M1M1M1M1M.', 'staff']
            ];
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            foreach ($defaultUsers as $u) {
                $stmt->execute($u);
            }
        }
    }
}

if (!function_exists('ensureContactSchema')) {
    function ensureContactSchema(PDO $pdo): void
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL,
            destination VARCHAR(100) DEFAULT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }
}

if (!function_exists('ensureNewsletterSchema')) {
    function ensureNewsletterSchema(PDO $pdo): void
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        $pdo->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(150) UNIQUE NOT NULL,
            subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }
}

if (!function_exists('ensurePasswordResetSchema')) {
    function ensurePasswordResetSchema(PDO $pdo): void
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token_hash VARCHAR(255) NOT NULL,
            expires_at DATETIME NOT NULL,
            used_at DATETIME DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");

        $stmt = $pdo->prepare("SHOW INDEX FROM password_resets WHERE Key_name = 'idx_password_resets_token_hash'");
        $stmt->execute();
        if (!$stmt->fetch()) {
            $pdo->exec("CREATE INDEX idx_password_resets_token_hash ON password_resets (token_hash(191))");
        }
    }
}

if (!function_exists('ensurePackageSchema')) {
    function ensurePackageSchema(PDO $pdo): void
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        $pdo->exec("CREATE TABLE IF NOT EXISTS packages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            category VARCHAR(50) NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            description TEXT,
            image_url VARCHAR(255),
            featured TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $count = $pdo->query("SELECT COUNT(*) FROM packages")->fetchColumn();
        if ($count == 0) {
            $packages = [
                ['Cultural Triangle Explorer', 'cultural', 799.00, 'Sigiriya, Dambulla, Polonnaruwa, Anuradhapura — the ancient heart of Sri Lanka.', 'images/culturalpckg.png', 1],
                ['Southern Shores Escape', 'beach', 699.00, 'Galle Fort, Unawatuna Beach, and Mirissa whale watching paradise.', 'images/southernpckg.png', 1],
                ['Wild Sri Lanka Safari', 'wildlife', 899.00, 'Leopards in Yala, elephants in Udawalawe, and blue whales off the coast.', 'images/wildpckg.png', 1],
                ['Hill Country Tea Trail', 'cultural', 749.00, 'Nuwara Eliya, Ella, scenic rail journeys, and guided tea estate experiences.', 'images/hillcountry.jpg', 0],
                ['East Coast Lagoon Escape', 'beach', 629.00, 'Pasikuda, Trincomalee beaches, snorkeling, and calm lagoon adventures.', 'images/passikudabeach.jpg', 0],
                ['Rainforest & River Adventure', 'wildlife', 679.00, 'Sinharaja rainforest trails, river kayaking, waterfalls, and birdwatching.', 'images/rainforestpckg.jpg', 0]
            ];
            $stmt = $pdo->prepare("INSERT INTO packages (title, category, price, description, image_url, featured) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($packages as $p) $stmt->execute($p);
        }

        $extraPackages = [
            ['Hill Country Tea Trail', 'cultural', 749.00, 'Nuwara Eliya, Ella, scenic rail journeys, and guided tea estate experiences.', 'images/hillcountry.jpg', 0],
            ['East Coast Lagoon Escape', 'beach', 629.00, 'Pasikuda, Trincomalee beaches, snorkeling, and calm lagoon adventures.', 'images/passikudabeach.jpg', 0],
            ['Rainforest & River Adventure', 'wildlife', 679.00, 'Sinharaja rainforest trails, river kayaking, waterfalls, and birdwatching.', 'images/rainforestpckg.jpg', 0],
        ];
        $existsStmt = $pdo->prepare("SELECT id FROM packages WHERE title = ? LIMIT 1");
        $insertStmt = $pdo->prepare("INSERT INTO packages (title, category, price, description, image_url, featured) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($extraPackages as $pkg) {
            $existsStmt->execute([$pkg[0]]);
            if (!$existsStmt->fetch()) {
                $insertStmt->execute($pkg);
            }
        }
    }
}

if (!function_exists('ensureHotelSchema')) {
    function ensureHotelSchema(PDO $pdo): void
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        $pdo->exec("CREATE TABLE IF NOT EXISTS hotels (
            id INT AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(50) UNIQUE NOT NULL,
            name VARCHAR(100) NOT NULL,
            category ENUM('luxury', 'boutique', 'eco', 'hostel') NOT NULL,
            location VARCHAR(100) NOT NULL,
            description TEXT,
            price_per_night DECIMAL(10, 2) NOT NULL,
            image_url VARCHAR(255),
            rating INT DEFAULT 5,
            amenities TEXT,
            gallery TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $stmt = $pdo->prepare("SHOW COLUMNS FROM hotels LIKE 'category'");
        $stmt->execute();
        $col = $stmt->fetch();
        if ($col && strpos($col['Type'], 'hostel') === false) {
            $pdo->exec("ALTER TABLE hotels MODIFY COLUMN category ENUM('luxury', 'boutique', 'eco', 'hostel') NOT NULL");
        }

        $stmt = $pdo->prepare("SHOW COLUMNS FROM hotels LIKE 'gallery'");
        $stmt->execute();
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE hotels ADD COLUMN gallery TEXT DEFAULT NULL AFTER amenities");
        }

       
        $allAccommodations = [
            ['ocean-breeze', 'Ocean Breeze Luxury Resort', 'luxury', 'Bentota, South Coast', 'Experience ultimate luxury by the sea with world-class spa facilities and private beach access.', 180.00, 'images/oceanbreezehotel6.jpg', 5, 'Infinity Pool, Luxury Spa, Free WiFi, Ocean View', 'images/oceanbreezehotel1.jpg,images/oceanbreezehotel2.jpg,images/oceanbreezehotel3.jpg,images/oceanbreezehotel4.jpg,images/oceanbreezehotel5.jpg,images/oceanbreezehotel6.jpg,images/oceanbreezehotel7.jpg,images/oceanbreezehotel8.jpg,images/oceanbreezehotel9.jpg'],
            ['grand-kandyan', 'The Grand Kandyan Estate', 'boutique', 'Kandy, Central Highlands', 'A colonial masterpiece offering breathtaking views of the misty Kandy mountains and tea estates.', 145.00, 'images/thegrandkandyanestate7.jpg', 5, 'Tea Garden, Fitness Center, Fine Dining', 'images/thegrandkandyanestate1.jpg,images/thegrandkandyanestate2.jpg,images/thegrandkandyanestate3.jpg,images/thegrandkandyanestate4.jpg,images/thegrandkandyanestate5.jpg,images/thegrandkandyanestate6.jpg,images/thegrandkandyanestate7.jpg'],
            ['wild-trails', 'Wild Trails Jungle Lodge', 'eco', 'Ella, Hill Country', 'Immerse yourself in nature at this eco-friendly lodge nestled in the heart of Ella\'s lush rainforest.', 95.00, 'images/wildtrailsjunglelodge12.jpg', 4, 'Eco Friendly, Safari Desk, Mountain View', 'images/wildtrailsjunglelodge1.jpg,images/wildtrailsjunglelodge2.jpg,images/wildtrailsjunglelodge3.jpg,images/wildtrailsjunglelodge4.jpg,images/wildtrailsjunglelodge5.jpg,images/wildtrailsjunglelodge6.jpg,images/wildtrailsjunglelodge7.jpg,images/wildtrailsjunglelodge8.jpg,images/wildtrailsjunglelodge9.jpg'],
            ['sigiriya-rock-hideout', 'Sigiriya Rock Hideout', 'boutique', 'Sigiriya, Cultural Triangle', 'Boutique stay with a direct view of the majestic Sigiriya Lion Rock.', 120.00, 'images/sigiriyarockhideout1.jpg', 5, 'Garden View, Near Landmark, Pool', 'images/sigiriyarockhideout1.jpg,images/sigiriyarockhideout2.jpg,images/sigiriyarockhideout3.jpg,images/sigiriyarockhideout4.jpg,images/sigiriyarockhideout5.jpg,images/sigiriyarockhideout6.jpg,images/sigiriyarockhideout7.jpg'],
            ['mirissa-blue', 'Mirissa Blue Boutique', 'boutique', 'Mirissa, South Coast', 'Stylish modern boutique hotel just steps away from the famous coconut tree hill.', 110.00, 'images/mirissablueboutique1.jpg', 4, 'Roof top Bar, Surfing Kits, Sea Food', 'images/mirissablueboutique1.jpg,images/mirissablueboutique2.jpg,images/mirissablueboutique3.jpg,images/mirissablueboutique4.jpg,images/mirissablueboutique5.jpg,images/mirissablueboutique6.jpg,images/mirissablueboutique7.jpg,images/mirissablueboutique8.jpg,images/mirissablueboutique9.jpg,images/mirissablueboutique10.jpg,images/mirissablueboutique11.jpg,images/mirissablueboutique12.jpg'],
            ['trinco-blu', 'Trinco Blu Shores', 'luxury', 'Trincomalee, East Coast', 'Vibrant retro-chic resort offering whale watching expeditions and pristine beach views.', 155.00, 'images/trincoblushores7.jpg', 5, 'Diving Center, Pool Side Bar, Whale Watching', 'images/trincoblushores1.jpg,images/trincoblushores2.jpg,images/trincoblushores3.jpg,images/trincoblushores4.jpg,images/trincoblushores5.jpg,images/trincoblushores6.jpg,images/trincoblushores7.jpg'],
            ['galle-fort-heritage', 'The Heritage Hotel Galle Fort', 'boutique', 'Galle Fort', 'A historic Dutch villa restored into a premium boutique stay within the walls of the Galle Fort.', 210.00, 'images/theheritagehotelgallefort2.jpg', 5, 'Butler service, Colonial Library, Courtyard', 'images/theheritagehotelgallefort1.jpg,images/theheritagehotelgallefort2.jpg,images/theheritagehotelgallefort3.jpg,images/theheritagehotelgallefort4.jpg,images/theheritagehotelgallefort5.jpg,images/theheritagehotelgallefort6.jpg,images/theheritagehotelgallefort7.jpg'],
            ['arugam-surf-eco', 'Arugam Bay Surf Eco-Lodge', 'eco', 'Arugam Bay', 'Sustainable living at its best, designed for surfers and digital nomads looking for tranquility.', 65.00, 'images/arugambaysurf2.jpg', 4, 'Surf School, Organic Cafe, Yoga Deck', 'images/arugambaysurf1.jpg,images/arugambaysurf2.jpg,images/arugambaysurf3.jpg,images/arugambaysurf4.jpg,images/arugambaysurf5.jpg'],
            ['anuradhapura-heritage', 'Ancient Heritage Stay', 'boutique', 'Anuradhapura', 'Experience Zen-like calmness near the sacred ancient city of Anuradhapura.', 85.00, 'images/hertiagehotel1.jpg', 4, 'Bicycle Rental, Traditional Spa, Quiet Zone', 'images/hertiagehotel1.jpg,images/hertiagehotel2.jpg,images/hertiagehotel3.jpg,images/hertiagehotel4.jpg,images/hertiagehotel5.jpg,images/hertiagehotel6.jpg,images/hertiagehotel7.jpg,images/hertiagehotel8.jpg,images/hertiagehotel9.jpg,images/hertiagehotel10.jpg,images/hertiagehotel11.jpg'],
            ['yala-leopard-camp', 'Yala Leopard Tented Camp', 'eco', 'Yala National Park', 'Luxury glamping in the heart of the leopard capital, offering twice-daily safaris.', 250.00, 'images/yalaleopardcamp1.jpg', 5, 'All-inclusive Safari, Campfire Dinner, Nature Guides', 'images/yalaleopardcamp1.jpg,images/yalaleopardcamp2.jpg,images/yalaleopardcamp3.jpg,images/yalaleopardcamp4.jpg,images/yalaleopardcamp5.jpg,images/yalaleopardcamp6.jpg,images/yalaleopardcamp7.jpg'],
            ['forest-shade-eco', 'Forest Shade Eco Resort', 'eco', 'Kegalle, Sabaragamuwa Province', 'Nestled in the lush greenery of Kegalle, this eco-resort offers a serene escape with nature trails and traditional hospitality.', 85.00, 'images/forestshade1.jpg', 4, 'Nature Trails, Organic Dining, Scenic Views, Free WiFi', 'images/forestshade1.jpg,images/forestshade2.jpg,images/forestshade3.jpg,images/forestshade4.jpg,images/forestshade5.jpg,images/forestshade6.jpg'],
            ['sarah-cottage', 'Sarah Cottage - Nuwara Eliya', 'boutique', 'Nuwara Eliya, Hill Country', 'A charming and cozy cottage nestled in the misty hills of Nuwara Eliya, offering a peaceful retreat with colonial charm.', 95.00, 'images/sarahcottage1.jpg', 4, 'Garden, Fireplace, Free WiFi, Mountain View', 'images/sarahcottage1.jpg,images/sarahcottage2.jpg,images/sarahcottage3.jpg,images/sarahcottage4.jpg,images/sarahcottage5.jpg'],
            ['little-england', 'Little England by Regus', 'luxury', 'Nuwara Eliya, Hill Country', 'Experience the charm of "Little England" in these luxury cottages surrounded by mist-covered tea plantations.', 150.00, 'images/littleengland7.jpg', 5, 'Fireplace, Mountain View, Private Garden, Butler Service', 'images/littleengland1.jpg,images/littleengland2.jpg,images/littleengland3.jpg,images/littleengland4.jpg,images/littleengland5.jpg,images/littleengland6.jpg,images/littleengland7.jpg,images/littleengland8.jpg'],
            ['hikka-sandy-pearl', 'Hikka Sandy Pearl Beach Resort', 'boutique', 'Hikkaduwa, South Coast', 'A relaxing beachfront resort in Hikkaduwa, perfect for those looking to enjoy the sun, sand, and surf of the southern coast.', 90.00, 'images/hikkasandypearl4.jpg', 4, 'Beachfront, Outdoor Pool, Surf Gear, Restaurant', 'images/hikkasandypearl1.jpg,images/hikkasandypearl2.jpg,images/hikkasandypearl3.jpg,images/hikkasandypearl4.jpg,images/hikkasandypearl5.jpg,images/hikkasandypearl6.jpg,images/hikkasandypearl7.jpg,images/hikkasandypearl8.jpg,'],
            ['queens-wood-cottage', 'Queens Wood Cottage', 'boutique', 'Nuwara Eliya, Hill Country', 'A charming boutique cottage that captures the vintage essence of Nuwara Eliya, surrounded by lush gardens and misty hills.', 85.00, 'images/queenswood4.jpg', 4, 'Garden, Tea Terrace, Fireplace, Free WiFi', 'images/queenswood1.jpg,images/queenswood2.jpg,images/queenswood3.jpg,images/queenswood4.jpg,images/queenswood5.jpg'],
            ['dover-cottage', 'Dover Cottage', 'boutique', 'Nuwara Eliya, Hill Country', 'A cozy and budget-friendly boutique stay in the highlands, offering a warm and homely atmosphere for travelers.', 70.00, 'images/dovercottage5.jpg', 4, 'Homestay Feel, Scenic Views, Garden, Shared Lounge', 'images/dovercottage1.jpg,images/dovercottage2.jpg,images/dovercottage3.jpg,images/dovercottage4.jpg,images/dovercottage5.jpg,images/dovercottage6.jpg'],
            ['ella-mount-hostel', 'Ella Mount View Hostel', 'hostel', 'Ella, Hill Country', 'Wake up to panoramic views of Ella Gap from your dorm bed in this cozy, mountain-side hostel.', 18.00, 'images/ellamountviewhostel9.jpg', 5, 'Mountain View, Hammocks, Free Breakfast, Common Area', 'images/ellaview.jpg,images/ellamountviewhostel2.jpg,images/ellamountviewhostel3.jpg,images/ellamountviewhostel4.jpg,images/ellamountviewhostel5.jpg,images/ellamountviewhostel6.jpg,images/ellamountviewhostel7.jpg,images/ellamountviewhostel8.jpg,images/ellamountviewhostel9.jpg']
        ];

     

        
        $stmt = $pdo->prepare("
            INSERT INTO hotels (slug, name, category, location, description, price_per_night, image_url, rating, amenities, gallery)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                gallery = VALUES(gallery),
                image_url = VALUES(image_url),
                rating = VALUES(rating),
                description = VALUES(description),
                price_per_night = VALUES(price_per_night)
        ");

        foreach ($allAccommodations as $acc) {
            $stmt->execute($acc);
        }
    }
}

if (!function_exists('ensureDestinationSchema')) {
    function ensureDestinationSchema(PDO $pdo): void
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        $pdo->exec("CREATE TABLE IF NOT EXISTS destinations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(100) UNIQUE NOT NULL,
            name VARCHAR(150) NOT NULL,
            category VARCHAR(50) NOT NULL,
            location VARCHAR(150) NOT NULL,
            description TEXT,
            price DECIMAL(10, 2) NOT NULL,
            image_url VARCHAR(255),
            rating DECIMAL(3, 1) DEFAULT 5.0,
            featured TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $count = $pdo->query("SELECT COUNT(*) FROM destinations")->fetchColumn();
        if ($count == 0) {
            $destinations = [
                ['sigiriya', 'Sigiriya Rock Fortress', 'Featured', 'Matale District', 'Ancient sky palace and fortress complex significant for its archaeological importance.', 45.00, 'images/sigiriya.jpg', 5.0, 1],
                ['kandy', 'Kandy Hub', 'Cultural', 'Central Province', 'Imperial city of the last kings, spiritual heart of the island.', 35.00, 'images/kandy.jpg', 4.8, 0],
                ['ella', 'Ella Village', 'Nature', 'Badulla District', 'Misty mountain range and cascading waterfalls in the central highlands.', 30.00, 'images/ella.jpg', 4.9, 0],
                ['galle', 'Galle Old Fort', 'Coastal', 'Southern Province', 'Living colonial heritage city surrounded by ocean ramparts.', 40.00, 'images/galle.jpg', 4.7, 0],
                ['pasikuda', 'Pasikuda', 'Coastal', 'Eastern Province', 'One of the world\'s longest stretches of shallow coastline reef.', 40.00, 'images/pasikuda.jpg', 4.8, 0]
            ];
            $stmt = $pdo->prepare("INSERT INTO destinations (slug, name, category, location, description, price, image_url, rating, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($destinations as $d) $stmt->execute($d);
        }
    }
}

try {
    $pdo = getPDO();
    ensureBookingSchema($pdo);
    ensureUserSchema($pdo);
    ensureContactSchema($pdo);
    ensurePasswordResetSchema($pdo);
    ensurePackageSchema($pdo);
    ensureHotelSchema($pdo);
    ensureDestinationSchema($pdo);
    ensureNewsletterSchema($pdo);
} catch (Exception $e) {
    
    die("Database Connection/Schema Error: " . $e->getMessage());
}





if (!function_exists('appBasePath')) {
    function appBasePath(): string
    {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = str_replace('\\', '/', dirname($scriptName));

        if ($scriptDir === '/' || $scriptDir === '.') {
            $scriptDir = '';
        }

      
        $base = preg_replace('#/(admin|api|php)$#', '', $scriptDir) ?? $scriptDir;
        if ($base === '/' || $base === '.') {
            $base = '';
        }

        return rtrim($base, '/');
    }
}

if (!function_exists('appUrl')) {
    function appUrl(string $path = ''): string
    {
        $base = appBasePath();
        $path = ltrim($path, '/');

        if ($path === '') {
            return $base === '' ? '/' : $base . '/';
        }

        return ($base === '' ? '' : $base . '/') . $path;
    }
}
