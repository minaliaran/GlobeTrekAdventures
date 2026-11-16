<?php
require_once __DIR__ . '/php/config.php';

$pageTitle = "Welcome to Paradise | GlobeTrek";
$bodyClass = "home-page";
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $pageTitle; ?></title>
        <link rel="icon" type="image/png" href="<?php echo appUrl('images/favicon.png'); ?>">
        <link
            href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap"
            rel="stylesheet">
        
        <link rel="stylesheet" href="<?php echo appUrl('css/style.css'); ?>">
        <link rel="stylesheet" href="<?php echo appUrl('css/layout.css'); ?>">
        <link rel="stylesheet" href="<?php echo appUrl('css/components.css'); ?>">
        <link rel="stylesheet" href="<?php echo appUrl('css/pages.css'); ?>">
        <link rel="stylesheet" href="<?php echo appUrl('css/navigation.css'); ?>">
    </head>

    <body class="<?php echo $bodyClass; ?>">
        <div id="scroll-progress"></div>
        <?php include __DIR__ . '/includes/navbar.php'; ?>


        <section class="hero hero--video-only">
            <div class="hero-video-container">
                <video autoplay muted loop playsinline preload="auto" class="hero-video">
                    <source src="<?php echo appUrl('video/homesrilanka.mp4'); ?>" type="video/mp4">
                </video>
            </div>
            <div class="hero-gradient"></div>
            <div class="hero-content">
                <h1 class="hero-title reveal-fade">Escape to Paradise</h1>

                <p class="hero-desc reveal-fade">
                    Discover breathtaking destinations, luxury stays, and unforgettable adventures across Sri Lanka.
                </p>

                <div class="hero-cta-group">
                    <a href="<?php echo appUrl('php/booking.php'); ?>" class="btn-primary btn-hero-cta">
                        Book Now
                    </a>
                    <a href="<?php echo appUrl('destinations/destinations.html'); ?>" class="btn-secondary btn-hero-cta">
                        Explore Destinations
                    </a>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="intro-grid">
                    <div class="intro-title-col">
                        <h2 class="intro-title">
                            DISCOVER <br>
                            THE PEARL <br>
                            OF INDIAN
                            OCEAN
                        </h2>
                    </div>
                    <div class="intro-text-col">
                        <p class="intro-text">
                            An island just 65,610 square kilometres, yet Sri Lanka feels like the world within your
                            reach. Drive from sun-drenched beaches to misty tea hills, wander lush rainforests, and
                            explore
                            ancient cities steeped in history and wonder. Savour vibrant cuisine - from spicy curries
                            to
                            fresh
                            seafood, while soaking in the rhythm of local life and colourful traditions.
                        </p>
                        <p class="intro-text">
                            Meet warm, welcoming people whose smiles and stories make every journey unforgettable.
                            Encounter elephants roaming
                            freely, leopards in the wild, and birds hidden in emerald forests. Every day here is a new
                            adventure, a feast for the senses, and a trip of a lifetime.
                        </p>
                    <a href="<?php echo appUrl('php/about.php'); ?>" class="btn-about-link">
                            About Us
                            <span class="circle-icon">
                                <i class="fas fa-arrow-right"></i>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="things-to-do common-home clearfix interactive-map-section">
            <div class="interactive-map-bg-clouds" style="background-image: url('<?php echo appUrl('images/clouds.png'); ?>');"></div>

            <div class="wrapper container interactive-map-wrapper">

                <div class="text-holder reveal interactive-map-content-left">
                    <h2 class="interactive-map-title">
                        THINGS<br>TO DO<br>
                        <span class="interactive-map-subtitle">IN SRI LANKA</span>
                    </h2>
                    <p class="interactive-map-description">
                        Sri Lanka is a land of endless discovery, with something to captivate every traveller. From
                        sun-kissed beaches to misty hills, bustling markets to serene temples, every corner tells a
                        story... We're here to help you explore it all, local insights, expert tips, the best food,
                        hidden gems, and experiences that turn every day into your next unforgettable memory.
                    </p>
                </div>

                <div class="img-holder map-holder reveal interactive-map-middle">
                    <div class="interactive-map-container">
                        <img src="<?php echo appUrl('images/slmap.jpg'); ?>" alt="Sri Lanka Map" class="interactive-map-img">

                        <div class="map-pin-custom pin-loc-anuradhapura" data-loc="anuradhapura"
                            data-title="Anuradhapura" data-img="<?php echo appUrl('images/auradhapuraabout.jpg'); ?>"
                            data-desc="The sacred ancient capital, home to immense stupas and the Bodhi Tree.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Anuradhapura</span>
                        </div>
                        <div class="map-pin-custom pin-loc-sigiriya" data-loc="sigiriya" data-title="Sigiriya"
                            data-img="<?php echo appUrl('images/sigiriya.jpg'); ?>"
                            data-desc="Ancient sky palace and fortress complex significant for its archaeological importance.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Sigiriya</span>
                        </div>
                        <div class="map-pin-custom pin-loc-dambulla" data-loc="dambulla" data-title="Dambulla"
                            data-img="<?php echo appUrl('images/dambulla.jpg'); ?>"
                            data-desc="A majestic ancient cave temple complex filled with legendary statues.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Dambulla</span>
                        </div>
                        <div class="map-pin-custom pin-loc-polonnaruwa" data-loc="polonnaruwa" data-title="Polonnaruwa"
                            data-img="<?php echo appUrl('images/polonnaruwa.jpg'); ?>"
                            data-desc="The second most ancient kingdom featuring spectacularly preserved stone carvings.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Polonnaruwa</span>
                        </div>
                        <div class="map-pin-custom pin-loc-kandy" data-loc="kandy" data-title="Kandy"
                            data-img="<?php echo appUrl('images/kandy.jpg'); ?>"
                            data-desc="Imperial city of the last kings, spiritual heart of the island.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Kandy</span>
                        </div>
                        <div class="map-pin-custom pin-loc-nuwaraeliya" data-loc="nuwaraeliya" data-title="Nuwara Eliya"
                            data-img="<?php echo appUrl('images/nuwaraeliya.jpg'); ?>"
                            data-desc="Little England of Sri Lanka, known for its rolling tea estates and cool climate.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Nuwara Eliya</span>
                        </div>
                        <div class="map-pin-custom pin-loc-ella" data-loc="ella" data-title="Ella"
                            data-img="<?php echo appUrl('images/ella.jpg'); ?>"
                            data-desc="Misty mountain range and cascading waterfalls in the central highlands.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Ella</span>
                        </div>
                        <div class="map-pin-custom pin-loc-haputale" data-loc="haputale" data-title="Haputale"
                            data-img="<?php echo appUrl('images/haputale.jpg'); ?>"
                            data-desc="Dramatic cloud forests and panoramic vistas perfect for peaceful hikes.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Haputale</span>
                        </div>
                        <div class="map-pin-custom pin-loc-badulla" data-loc="badulla" data-title="Badulla"
                            data-img="<?php echo appUrl('images/badulla.jpg'); ?>"
                            data-desc="Scenic valley town encircled by the Namunukula mountain range and tea plains.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Badulla</span>
                        </div>
                        <div class="map-pin-custom pin-loc-ratnapura" data-loc="ratnapura" data-title="Ratnapura"
                            data-img="<?php echo appUrl('images/rathnapura.jpg'); ?>"
                            data-desc="The City of Gems, offering insight into Sri Lanka's historic gemstone trade.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Ratnapura</span>
                        </div>
                        <div class="map-pin-custom pin-loc-kalpitiya" data-loc="kalpitiya" data-title="Kalpitiya"
                            data-img="<?php echo appUrl('images/kalpitiya.jpg'); ?>"
                            data-desc="Asia's capital for kitesurfing and incredible dolphin watching expeditions.">
                            <div class="pin-label-custom pin-label-left">Kalpitiya</div>
                            <div class="pin-marker-custom"></div>
                        </div>
                        <div class="map-pin-custom pin-loc-negambo" data-loc="negambo" data-title="Negombo"
                            data-img="<?php echo appUrl('images/negombo.jpg'); ?>"
                            data-desc="Historic charm, Dutch canals, and an iconic lively fish market.">
                            <div class="pin-label-custom pin-label-left">Negombo</div>
                            <div class="pin-marker-custom"></div>
                        </div>
                        <div class="map-pin-custom pin-loc-colombo" data-loc="colombo" data-title="Colombo"
                            data-img="<?php echo appUrl('images/colombobackground.jpg'); ?>"
                            data-desc="The vibrant commercial capital blending colonial architecture with modern luxury.">
                            <div class="pin-label-custom pin-label-left">Colombo</div>
                            <div class="pin-marker-custom"></div>
                        </div>
                        <div class="map-pin-custom pin-loc-bentota" data-loc="bentota" data-title="Bentota"
                            data-img="<?php echo appUrl('images/bentota.jpg'); ?>"
                            data-desc="Premium beach resort destination with tranquil watersports on the Madu ganga.">
                            <div class="pin-label-custom pin-label-left">Bentota</div>
                            <div class="pin-marker-custom"></div>
                        </div>
                        <div class="map-pin-custom pin-loc-hikkaduwa" data-loc="hikkaduwa" data-title="Hikkaduwa"
                            data-img="<?php echo appUrl('images/hikkaduwa.jpg'); ?>"
                            data-desc="Vibrant coastal town famed for colorful coral reefs and great surfing.">
                            <div class="pin-label-custom pin-label-left">Hikkaduwa</div>
                            <div class="pin-marker-custom"></div>
                        </div>
                        <div class="map-pin-custom pin-loc-galle" data-loc="galle" data-title="Galle"
                            data-img="<?php echo appUrl('images/galle.jpg'); ?>"
                            data-desc="Living colonial heritage city surrounded by ocean ramparts.">
                            <div class="pin-label-custom pin-label-left">Galle</div>
                            <div class="pin-marker-custom"></div>
                        </div>
                        <div class="map-pin-custom pin-loc-mirissa" data-loc="mirissa" data-title="Mirissa"
                            data-img="<?php echo appUrl('images/mirissa.jpg'); ?>"
                            data-desc="Pristine beaches, secret snorkeling spots, and whale watching tours.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Mirissa</span>
                        </div>
                        <div class="map-pin-custom pin-loc-yala" data-loc="yala" data-title="Yala"
                            data-img="<?php echo appUrl('images/yala.jpg'); ?>"
                            data-desc="World-famous park with the highest density of leopards on Earth.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Yala</span>
                        </div>
                        <div class="map-pin-custom pin-loc-udawalawe" data-loc="udawalawe" data-title="Udawalawe"
                            data-img="<?php echo appUrl('images/udawalve.jpg'); ?>"
                            data-desc="Open grasslands guaranteeing wild elephant sightings year-round.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Udawalawe</span>
                        </div>
                        <div class="map-pin-custom pin-loc-arugambay" data-loc="arugambay" data-title="Arugam Bay"
                            data-img="<?php echo appUrl('images/arugambay.jpg'); ?>"
                            data-desc="World-renowned surfing destination with a laid-back bohemian vibe.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Arugam Bay</span>
                        </div>
                        <div class="map-pin-custom pin-loc-thrincomalee" data-loc="thrincomalee"
                            data-title="Trincomalee" data-img="<?php echo appUrl('images/thrincomalee.jpg'); ?>"
                            data-desc="Deepwater port city with pristine white sand beaches and historic temples.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Trincomalee</span>
                        </div>
                        <div class="map-pin-custom pin-loc-pasikuda active" data-loc="pasikuda" data-title="Pasikuda"
                            data-img="<?php echo appUrl('images/passikuda.jpg'); ?>"
                            data-desc="One of the world's longest stretches of shallow coastline reef.">
                            <div class="pin-marker-custom"></div> <span class="pin-label-custom">Pasikuda</span>
                        </div>
                    </div>
                </div>

                <div class="card-holder reveal interactive-map-card-holder">
                    <div class="interactive-map-card">
                        <div class="interactive-map-card-img-wrap">
                            <img id="dynamic-card-img" class="interactive-map-card-img" src="<?php echo appUrl('images/pasikuda.jpg'); ?>"
                                alt="Pasikuda">
                        </div>
                        <div class="interactive-map-card-body">
                            <h3 id="dynamic-card-title" class="interactive-map-card-title">PASIKUDA</h3>
                            <span id="dynamic-card-subtitle" class="interactive-map-card-subtitle">PASIKUDA
                                DESTINATION</span>
                            <p id="dynamic-card-desc" class="interactive-map-card-desc">One of the world's longest
                                stretches of shallow coastline reef.</p>

                            <a href="<?php echo appUrl('destinations/pasikuda/pasikuda.html'); ?>" id="dynamic-card-link" class="interactive-map-card-link">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </section>


        <section class="section section-alt">
            <div class="container">
                <div class="section-header">
                    <div class="section-tag">Gallery</div>
                    <h2 class="section-title">Sri Lanka in Pictures</h2>
                    <p class="section-desc">Explore moments from beaches, heritage cities, hill country, and wildlife adventures.</p>
                </div>
                <div class="blog-grid">
                    <article class="blog-card">
                        <div class="blog-img">
                            <img src="<?php echo appUrl('images/sigiriya.jpg'); ?>"
                                alt="Sigiriya" loading="lazy">
                            <span class="blog-category">Heritage</span>
                        </div>
                        <div class="blog-content">
                            <h3>Ancient Wonders</h3>
                            <p>From Sigiriya to sacred temples, discover timeless landmarks and cultural treasures.</p>
                            <div class="blog-meta">
                                <span><i class="fas fa-image"></i> Curated Photos</span>
                                <span><i class="fas fa-map-marker-alt"></i> Cultural Triangle</span>
                            </div>
                            <a href="<?php echo appUrl('gallery.html'); ?>" ></a>
                        </div>
                    </article>
                    <article class="blog-card">
                        <div class="blog-img">
                            <img src="<?php echo appUrl('images/mirissa.jpg'); ?>"
                                alt="Coastal Sri Lanka" loading="lazy">
                            <span class="blog-category">Coast</span>
                        </div>
                        <div class="blog-content">
                            <h3>Beach Escapes</h3>
                            <p>See stunning coastlines, golden sunsets, and turquoise waters across Sri Lanka.</p>
                            <div class="blog-meta">
                                <span><i class="fas fa-image"></i> Curated Photos</span>
                                <span><i class="fas fa-map-marker-alt"></i> South Coast</span>
                            </div>
                            <a href="<?php echo appUrl('gallery.html'); ?>" ></a>
                        </div>
                    </article>
                    <article class="blog-card">
                        <div class="blog-img">
                            <img src="<?php echo appUrl('images/yala.jpg'); ?>"
                                alt="Wildlife in Sri Lanka" loading="lazy">
                            <span class="blog-category">Wildlife</span>
                        </div>
                        <div class="blog-content">
                            <h3>Wildlife Moments</h3>
                            <p>Experience safari highlights with elephants, leopards, and birds in natural habitats.</p>
                            <div class="blog-meta">
                                <span><i class="fas fa-image"></i> Curated Photos</span>
                                <span><i class="fas fa-map-marker-alt"></i> Yala & Udawalawe</span>
                            </div>
                            <a href="<?php echo appUrl('gallery.html'); ?>" ></a>
                        </div>
                    </article>
                </div>
                <div class="blog-cta" >
                    <a href="<?php echo appUrl('gallery.html'); ?>" class="btn-primary">View Gallery</a>
                </div>
            </div>
        </section>

        <section class="section travel-insight">
            <div class="container">
                <div class="section-header">
                    <div class="section-tag">Travel Planning</div>
                    <h2 class="section-title">When to Travel in Sri Lanka</h2>
                    <p class="section-desc">Understand the climate across regions before planning your journey.</p>
                </div>

                <div class="insight-grid">
                    <div class="insight-card">
                        <h3>Coastal Areas</h3>
                        <p>
                            <strong>Best season: December &ndash; April</strong>
                            Clear skies, calm seas, perfect for beaches in Galle and Mirissa.
                        </p>
                    </div>

                    <div class="insight-card">
                        <h3>Hill Country</h3>
                        <p>
                            <strong>Best season: January &ndash; March</strong>
                            Cool climate ideal for Ella, Nuwara Eliya, and tea plantations.
                        </p>
                    </div>

                    <div class="insight-card">
                        <h3>Wildlife Parks</h3>
                        <p>
                            <strong>Best season: May &ndash; September</strong>
                            Best elephant and leopard sightings in Yala and Udawalawe.
                        </p>
                    </div>

                    <div class="insight-card">
                        <h3>Monsoon Periods</h3>
                        <p>
                            <strong>Southwest: May &ndash; August</strong><br>
                            <strong>Northeast: October &ndash; January</strong><br>
                            Travel smart based on region.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section-alt">
            <div class="container">
                <div class="section-header">
                    <div class="section-tag">Traveler Reviews</div>
                    <h2 class="section-title">What Our Guests Say</h2>
                    <p class="section-desc">Real experiences from real travelers who explored Sri Lanka with us.</p>
                </div>
                <div class="testimonials-grid">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p>"An incredible journey through ancient wonders and pristine beaches. The cultural
                                triangle was mind-blowing, and our guide made every moment special."</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="images/sarahjohnson.jpg"
                                alt="Carmen Mundt" class="author-avatar">
                            <div class="author-info">
                                <h4>Carmen Mundt</h4>
                                <span>USA</span>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p>"From tea plantations to whale watching, Sri Lanka exceeded all expectations. The food, 
                                the people, the wildlife - everything was perfect!"</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="images/carlossainz.jpg"
                                alt="Carlos Sainz" class="author-avatar">
                            <div class="author-info">
                                <h4>Carlos Sainz</h4>
                                <span>Singapore</span>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p>"The hill country train ride was magical. Waking up to misty mountains and fresh tea - 
                                this trip changed my perspective on travel."</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="images/charlesleclerc.jpg"
                                alt="charles leclerc" class="author-avatar">
                            <div class="author-info">
                                <h4>charles leclerc</h4>
                                <span>UK</span>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="packages">
            <div class="container">
                <div class="section-header">
                    <div class="section-tag">Tour Packages</div>
                    <h2 class="section-title">Curated Sri Lanka Tours</h2>
                    <p class="section-desc">Handcrafted itineraries for every kind of traveler.</p>
                </div>
                <div class="tours-grid">
                    <div class="tour-card" data-category="cultural">
                        <div class="tour-img">
                           <img src="<?php echo appUrl('images/culturalpckg.png'); ?>" alt="Cultural Triangle">
                            <span class="tour-fav" data-fav><i class="far fa-heart"></i></span>
                        </div>
                        <div class="tour-body">
                            <h3>Cultural Triangle Explorer</h3>
                            <p>Sigiriya, Dambulla, Polonnaruwa, Anuradhapura &mdash; the ancient heart of Sri Lanka.</p>
                            <div class="tour-footer">
                                <span class="tour-price">$799 <small>/person</small></span>
                            <a href="<?php echo appUrl('php/packages.php'); ?>" ></a>
                            </div>
                        </div>
                    </div>
                    <div class="tour-card" data-category="beach">
                        <div class="tour-img">
                            <img src="<?php echo appUrl('images/southernpckg.png'); ?>" alt="Southern Shores"
                                loading="lazy">
                            <span class="tour-fav" data-fav><i class="far fa-heart"></i></span>
                        </div>
                        <div class="tour-body">
                            <h3>Southern Shores Escape</h3>
                            <p>Galle Fort, Unawatuna Beach, and Mirissa whale watching paradise.</p>
                            <div class="tour-footer">
                                <span class="tour-price">$699 <small>/person</small></span>
                            <a href="<?php echo appUrl('php/packages.php'); ?>" ></a>
                            </div>
                        </div>
                    </div>
                    <div class="tour-card" data-category="wildlife">
                        <div class="tour-img">
                            <img src="<?php echo appUrl('images/wildpckg.png'); ?>" alt="Wildlife Safari"
                                loading="lazy">
                            <span class="tour-fav" data-fav><i class="far fa-heart"></i></span>
                        </div>
                        <div class="tour-body">
                            <h3>Wild Sri Lanka Safari</h3>
                            <p>Leopards in Yala, elephants in Udawalawe, and blue whales off the coast.</p>
                            <div class="tour-footer">
                                <span class="tour-price">$899 <small>/person</small></span>
                            <a href="<?php echo appUrl('php/packages.php'); ?>" ></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="blog-cta" >
                    <a href="<?php echo appUrl('php/packages.php'); ?>" class="btn-primary">View All Packages</a>
                </div>
            </div>
        </section>

        <section class="section section-alt">
            <div class="container">
                <div class="section-header">
                    <div class="section-tag">Why Sri Lanka</div>
                    <h2 class="section-title">A World of Wonder</h2>
                    <p class="section-desc">More diversity per square kilometer than almost anywhere on Earth.</p>
                </div>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon-wrap"><i class="fas fa-landmark"></i></div>
                        <h3>Ancient Heritage</h3>
                        <p>8 UNESCO World Heritage Sites including Sigiriya, Dambulla, and Kandy.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon-wrap"><i class="fas fa-umbrella-beach"></i></div>
                        <h3>Pristine Beaches</h3>
                        <p>Over 1,340 km of coastline &mdash; from whale watching to surfing.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon-wrap"><i class="fas fa-paw"></i></div>
                        <h3>Wildlife Safaris</h3>
                        <p>Leopards, elephants, and blue whales in their natural habitats.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon-wrap"><i class="fas fa-mug-hot"></i></div>
                        <h3>Tea Country</h3>
                        <p>Iconic train rides through misty plantations &mdash; a world-class journey.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="newsletter">
            <div class="newsletter-inner">
                <h2>Plan Your Sri Lanka Adventure</h2>
                <p>Subscribe for exclusive deals, seasonal tips, and hidden gems.</p>
                <div class="nl-form">
                    <input type="email" placeholder="Enter email address" id="nl-email" />
                    <button id="nl-btn">Subscribe <i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </section>
        <?php include 'includes/footer.php'; ?>

    </body>



</html>
