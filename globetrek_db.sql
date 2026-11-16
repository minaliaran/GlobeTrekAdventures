

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'customer') DEFAULT 'customer',
    profile_photo VARCHAR(255) DEFAULT 'images/default-avatar.png',
    phone VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Admin Password: 'admin 123'
INSERT INTO users (name, email, password, role) 
VALUES ('System Admin', 'admin@globetrek.com', '$2y$10$8dZSbz4ZvPlXu/pLroVHG.87YCnaIE7oDR6qu.Y3mWLfJcIwafasO', 'admin')
ON DUPLICATE KEY UPDATE password=VALUES(password);

-- Staff Password: 'staff 123'
INSERT INTO users (name, email, password, role) 
VALUES ('GlobeTrek Staff', 'staff@globetrek.com', '$2y$10$qm2WcvD7SLZn17GRSjYt1eePMNDu5Ayx08N3/3MPpl20PlQvmgK/u', 'staff')
ON DUPLICATE KEY UPDATE password=VALUES(password);


CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_password_resets_token_hash ON password_resets (token_hash(191));


CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO packages (title, category, price, description, image_url, featured) VALUES 
('Cultural Triangle Explorer', 'cultural', 799.00, 'Sigiriya, Dambulla, Polonnaruwa, Anuradhapura — the ancient heart of Sri Lanka.', 'images/culturalpckg.png', 1),
('Southern Shores Escape', 'beach', 699.00, 'Galle Fort, Unawatuna Beach, and Mirissa whale watching paradise.', 'images/southernpckg.png', 1),
('Wild Sri Lanka Safari', 'wildlife', 899.00, 'Leopards in Yala, elephants in Udawalawe, and blue whales off the coast.', 'images/wildpckg.png', 1),
('Hill Country Tea Trail', 'cultural', 749.00, 'Nuwara Eliya, Ella, scenic rail journeys, and guided tea estate experiences.', 'images/hillcountry.jpg', 0),
('East Coast Lagoon Escape', 'beach', 629.00, 'Pasikuda, Trincomalee beaches, snorkeling, and calm lagoon adventures.', 'images/pasikuda.jpg', 0),
('Rainforest & River Adventure', 'wildlife', 679.00, 'Sinharaja rainforest trails, river kayaking, waterfalls, and birdwatching.', 'images/rainforestpckg.jpg', 0);


CREATE TABLE IF NOT EXISTS hotels (
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
);


INSERT INTO hotels (slug, name, category, location, description, price_per_night, image_url, rating, amenities, gallery) VALUES 
('ocean-breeze', 'Ocean Breeze Luxury Resort', 'luxury', 'Bentota, South Coast', 'Experience ultimate luxury by the sea with world-class spa facilities and private beach access.', 180.00, 'images/oceanbreezehotel1.jpg', 5, 'Infinity Pool, Luxury Spa, Free WiFi, Ocean View', 'images/oceanbreezehotel1.jpg, images/oceanbreezehotel2.jpg, images/oceanbreezehotel3.jpg, images/oceanbreezehotel4.jpg'),
('grand-kandyan', 'The Grand Kandyan Estate', 'boutique', 'Kandy, Central Highlands', 'A colonial masterpiece offering breathtaking views of the misty Kandy mountains and tea estates.', 145.00, 'images/thegrandkandyanestate1.png', 5, 'Tea Garden, Fitness Center, Fine Dining', 'images/thegrandkandyanestate1.png, images/thegrandkandyanestate2.png, images/thegrandkandyanestate3.png, images/thegrandkandyanestate4.png'),
('wild-trails', 'Wild Trails Jungle Lodge', 'eco', 'Ella, Hill Country', 'Immerse yourself in nature at this eco-friendly lodge nestled in the heart of Ella''s lush rainforest.', 95.00, 'images/wildtrailsjunglelodge1.jpg', 4, 'Eco Friendly, Safari Desk, Mountain View', 'images/wildtrailsjunglelodge1.jpg, images/wildtrailsjunglelodge2.jpg, images/wildtrailsjunglelodge3.jpg, images/wildtrailsjunglelodge4.jpg'),
('sigiriya-rock-hideout', 'Sigiriya Rock Hideout', 'boutique', 'Sigiriya, Cultural Triangle', 'Boutique stay with a direct view of the majestic Sigiriya Lion Rock.', 120.00, 'images/sigiriyarockhideout1.jpg', 5, 'Garden View, Near Landmark, Pool', 'images/sigiriyarockhideout1.jpg, images/sigiriyarockhideout2.jpg, images/sigiriyarockhideout3.jpg, images/sigiriyarockhideout4.jpg'),
('mirissa-blue', 'Mirissa Blue Boutique', 'boutique', 'Mirissa, South Coast', 'Stylish modern boutique hotel just steps away from the famous coconut tree hill.', 110.00, 'images/mirissa.jpg', 4, 'Roof top Bar, Surfing Kits, Sea Food', 'images/mirissa1.jpg, images/mirissa2.jpg, images/mirissa3.jpg, images/mirissa4.jpg'),
('trinco-blu', 'Trinco Blu Shores', 'luxury', 'Trincomalee, East Coast', 'Vibrant retro-chic resort offering whale watching expeditions and pristine beach views.', 155.00, 'images/thrincomalee.jpg', 5, 'Diving Center, Pool Side Bar, Whale Watching', 'images/thrincomalee1.jpg, images/thrincomalee2.jpg, images/thrincomalee3.jpg, images/thrincomalee4.jpg'),
('galle-fort-heritage', 'Galle Fort Heritage Villa', 'boutique', 'Galle Fort', 'A historic Dutch villa restored into a premium boutique stay within the walls of the Galle Fort.', 210.00, 'images/theheritagehotelgallefort7.jpg', 5, 'Butler service, Colonial Library, Courtyard', ''),
('arugam-surf-eco', 'Arugam Bay Surf Eco-Lodge', 'eco', 'Arugam Bay', 'Sustainable living at its best, designed for surfers and digital nomads looking for tranquility.', 65.00, 'images/arugambaysurf2.jpg', 4, 'Surf School, Organic Cafe, Yoga Deck', 'images/arugambaysurf1.jpg, images/arugambaysurf2.jpg, images/arugambaysurf3.jpg, images/arugambaysurf4.jpg'),
('anuradhapura-heritage', 'Ancient Heritage Stay', 'boutique', 'Anuradhapura', 'Experience Zen-like calmness near the sacred ancient city of Anuradhapura.', 85.00, 'images/hertiagehotel1.jpg', 4, 'Bicycle Rental, Traditional Spa, Quiet Zone', 'images/hertiagehotel1.jpg, images/hertiagehotel2.jpg, images/hertiagehotel3.jpg, images/hertiagehotel4.jpg,images/hertiagehotel5.jpg, images/hertiagehotel6.jpg, images/hertiagehotel7.jpg'),
('yala-leopard-camp', 'Yala Leopard Tented Camp', 'eco', 'Yala National Park', 'Luxury glamping in the heart of the leopard capital, offering twice-daily safaris.', 250.00, 'images/yalaleopardcamp1.jpg', 5, 'All-inclusive Safari, Campfire Dinner, Nature Guides', 'images/yalaleopardcamp1.jpg, images/yalaleopardcamp2.jpg, images/yalaleopardcamp3.jpg, images/yalaleopardcamp4.jpg,images/yalaleopardcamp5.jpg,images/yalaleopardcamp6.jpg,images/yalaleopardcamp7.jpg'),
('forest-shade-eco', 'Forest Shade Eco Resort', 'eco', 'Kegalle, Sabaragamuwa Province', 'Nestled in the lush greenery of Kegalle, this eco-resort offers a serene escape with nature trails and traditional hospitality.', 85.00, 'images/forestshade1.jpg', 4, 'Nature Trails, Organic Dining, Scenic Views, Free WiFi', 'images/forestshade1.jpg,images/forestshade2.jpg,images/forestshade3.jpg,images/forestshade4.jpg,images/forestshade5.jpg,images/forestshade6.jpg'),
('ella-mount-hostel', 'Ella Mount View Hostel', 'hostel', 'Ella, Hill Country', 'Wake up to panoramic views of Ella Gap from your dorm bed in this cozy, mountain-side hostel.', 18.00, 'images/ella.jpg', 5, 'Mountain View, Hammocks, Free Breakfast, Common Area', 'images/ellaviwehotel1.jpg'),
('sarah-cottage', 'Sarah Cottage - Nuwara Eliya', 'boutique', 'Nuwara Eliya, Hill Country', 'A charming and cozy cottage nestled in the misty hills of Nuwara Eliya, offering a peaceful retreat with colonial charm.', 95.00, 'images/sarahcottage1.jpg', 4, 'Garden, Fireplace, Free WiFi, Mountain View', 'images/sarahcottage1.jpg,images/sarahcottage2.jpg,imgages/sarahcottage3.jpg,images/sarahcottage4.jpg'),
('little-england', 'Little England by Regus', 'luxury', 'Nuwara Eliya, Hill Country', 'Experience the charm of "Little England" in these luxury cottages surrounded by mist-covered tea plantations.', 150.00, 'images/littleengland1.jpg', 5, 'Fireplace, Mountain View, Private Garden, Butler Service', 'images/littleengland1.jpg,images/littleengland2.jpg,images/littleengland3.jpg,images/littleengland4.jpg'),
('hikka-sandy-pearl', 'Hikka Sandy Pearl Beach Resort', 'boutique', 'Hikkaduwa, South Coast', 'A relaxing beachfront resort in Hikkaduwa, perfect for those looking to enjoy the sun, sand, and surf of the southern coast.', 90.00, 'images/hikkasandypearl1.jpg', 4, 'Beachfront, Outdoor Pool, Surf Gear, Restaurant', 'images/hikkasandypearl1.jpg,images/hikkasandypearl2.jpg,images/hikkasandypearl3.jpg,images/hikkasandypearl4.jpg'),
('queens-wood-cottage', 'Queens Wood Cottage', 'boutique', 'Nuwara Eliya, Hill Country', 'A charming boutique cottage that captures the vintage essence of Nuwara Eliya, surrounded by lush gardens and misty hills.', 85.00, 'images/queenswood1.jpg', 4, 'Garden, Tea Terrace, Fireplace, Free WiFi', 'images/queenswood1.jpg,images/queenswood2.jpg,images/queenswood3.jpg,images/queenswood4.jpg'),
('dover-cottage', 'Dover Cottage', 'boutique', 'Nuwara Eliya, Hill Country', 'A cozy and budget-friendly boutique stay in the highlands, offering a warm and homely atmosphere for travelers.', 70.00, 'images/dovercottage1.jpg', 4, 'Homestay Feel, Scenic Views, Garden, Shared Lounge', 'images/dovercottage1.jpg,images/dovercottage2.jpg,images/dovercottage3.jpg,images/dovercottage4.jpg');



CREATE TABLE IF NOT EXISTS destinations (
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
);


INSERT INTO destinations (slug, name, category, location, description, price, image_url, rating, featured) VALUES
('sigiriya', 'Sigiriya Rock Fortress', 'Featured', 'Matale District', 'Ancient sky palace and fortress complex significant for its archaeological importance.', 45.00, 'images/sigiriya.jpg', 5.0, 1),
('kandy', 'Kandy Hub', 'Cultural', 'Central Province', 'Imperial city of the last kings, spiritual heart of the island.', 35.00, 'images/kandy.jpg', 4.8, 0),
('ella', 'Ella Village', 'Nature', 'Badulla District', 'Misty mountain range and cascading waterfalls in the central highlands.', 30.00, 'images/ella.jpg', 4.9, 0),
('galle', 'Galle Old Fort', 'Coastal', 'Southern Province', 'Living colonial heritage city surrounded by ocean ramparts.', 40.00, 'images/galle.jpg', 4.7, 0),
('mirissa', 'Mirissa Bay', 'Coastal', 'Matara District', 'Pristine beaches, secret snorkeling spots, and whale watching tours.', 25.00, 'images/mirissa.jpg', 4.6, 0),
('yala', 'Yala Safari', 'Wildlife', 'Hambantota District', 'World-famous park with the highest density of leopards on Earth.', 65.00, 'images/yala.jpg', 4.9, 0),
('nuwara-eliya', 'Nuwara Eliya', 'Hill Country', 'Central Province', 'Little England of Sri Lanka, known for its rolling tea estates and cool climate.', 40.00, 'images/nuwaraeliya.jpg', 4.8, 0),
('trincomalee', 'Trincomalee', 'Coastal', 'Eastern Province', 'Deepwater port city with pristine white sand beaches and historic temples.', 35.00, 'images/thrincomalee.jpg', 4.7, 0),
('colombo', 'Colombo Hub', 'Heritage', 'Western Province', 'The vibrant commercial capital blending colonial architecture with modern luxury.', 25.00, 'images/colombobackground.jpg', 4.6, 0),
('arugam-bay', 'Arugam Bay', 'Adventure', 'Eastern Province', 'World-renowned surfing destination with a laid-back bohemian vibe.', 30.00, 'images/arugambay.jpg', 4.9, 0),
('anuradhapura', 'Anuradhapura', 'Heritage', 'North Central', 'The sacred ancient capital, home to immense stupas and the Bodhi Tree.', 20.00, 'images/auradhpura.jpg', 4.8, 0),
('polonnaruwa', 'Polonnaruwa', 'Heritage', 'North Central', 'The second most ancient kingdom featuring spectacularly preserved stone carvings.', 25.00, 'images/polonnaruwa.jpg', 4.7, 0),
('udawalawe', 'Udawalawe Safari', 'Wildlife', 'Sabaragamuwa', 'Open grasslands guaranteeing wild elephant sightings year-round.', 55.00, 'images/udawalve.jpg', 4.9, 0),
('hikkaduwa', 'Hikkaduwa', 'Coastal', 'Southern Province', 'Vibrant coastal town famed for colorful coral reefs and great surfing.', 20.00, 'images/hikkaduwa.jpg', 4.6, 0),
('bentota', 'Bentota', 'Coastal', 'Southern Province', 'Premium beach resort destination with tranquil watersports on the Madu ganga.', 45.00, 'images/bentota.jpg', 4.8, 0),
('haputale', 'Haputale', 'Hill Country', 'Uva Province', 'Dramatic cloud forests and panoramic vistas perfect for peaceful hikes.', 30.00, 'images/haputale.jpg', 4.7, 0),
('kalpitiya', 'Kalpitiya', 'Adventure', 'North Western', 'Asia''s capital for kitesurfing and incredible dolphin watching expeditions.', 50.00, 'images/kalpitiya.jpg', 4.9, 0),
('negombo', 'Negombo', 'Coastal', 'Western Province', 'Historic charm, Dutch canals, and an iconic lively fish market.', 25.00, 'images/negombo.jpg', 4.5, 0),
('pasikuda', 'Pasikuda', 'Coastal', 'Eastern Province', 'One of the world''s longest stretches of shallow coastline reef.', 40.00, 'images/pasikuda.jpg', 4.8, 0),
('dambulla', 'Dambulla Cave Temple', 'Heritage', 'Central Province', 'A majestic ancient cave temple complex filled with legendary statues.', 20.00, 'images/dambulla.jpg', 4.9, 0),
('ratnapura', 'Ratnapura', 'Heritage', 'Sabaragamuwa', 'The City of Gems, offering insight into Sri Lanka''s historic gemstone trade.', 15.00, 'images/rathnapura.jpg', 4.4, 0),
('badulla', 'Badulla', 'Hill Country', 'Uva Province', 'Scenic valley town encircled by the Namunukula mountain range and tea plains.', 25.00, 'images/badulla.jpg', 4.6, 0);



CREATE TABLE IF NOT EXISTS bookings (
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    destination VARCHAR(100) DEFAULT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
