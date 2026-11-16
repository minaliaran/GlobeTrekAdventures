<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    $redirectTarget = 'booking.php';
    if (!empty($_SERVER['QUERY_STRING'])) {
        $redirectTarget .= '?' . $_SERVER['QUERY_STRING'];
    }
    header('Location: ' . appUrl('php/login.php') . '?redirect=' . urlencode($redirectTarget));
    exit;
}

$message = '';
$message_type = 'error';
$packages = [];
$packageMap = [];
$transportOptions = [
    'Scenic Train Ride'   => 15.00,
    'Private Transfer'    => 45.00,
    'Tuk-Tuk Adventure'   => 20.00,
    'Boat & Ferry'        => 30.00,
    'Helicopter Transfer' => 350.00,
    'Intercity Bus'       => 2.00,
];

$selectedType        = $_GET['type'] ?? 'package';
$validTypes          = ['package', 'transport', 'hotel'];
if (!in_array($selectedType, $validTypes, true)) {
    $selectedType = 'package';
}
$selectedPackageId      = (int)($_GET['package_id'] ?? 0);
$selectedService        = trim($_GET['item'] ?? '');
$selectedHotelId        = trim($_GET['hotel_id'] ?? '');
$selectedHotelCategory  = trim($_GET['category'] ?? '');

try {
    $pdo = getPDO();
    ensureBookingSchema($pdo);
    ensureHotelSchema($pdo);

    $packages = $pdo->query('SELECT id, title, price FROM packages ORDER BY title ASC')->fetchAll();

    $hotels = $pdo->query("
        SELECT slug, name, category, price_per_night
        FROM hotels
        ORDER BY
            CASE category
                WHEN 'luxury'   THEN 1
                WHEN 'boutique' THEN 2
                WHEN 'eco'      THEN 3
                ELSE 4
            END,
            name
    ")->fetchAll();

    $hotelsByCategory = [];
    foreach ($hotels as $hotel) {
        $cat = $hotel['category'];
        if (!isset($hotelsByCategory[$cat])) {
            $hotelsByCategory[$cat] = [];
        }
        $hotelsByCategory[$cat][] = $hotel;
    }

    $hotelCategoryLabels = [
        'luxury'   => 'Luxury Resorts',
        'boutique' => 'Boutique Hotels',
        'eco'      => 'Eco Lodges & Nature Stays',
        'hostel'   => 'Hostels & Budget Stays',
    ];

    foreach ($packages as $package) {
        $packageMap[(int)$package['id']] = $package;
    }

    $hotelMap = [];
    foreach ($hotels as $hotel) {
        $hotelMap[$hotel['slug']] = $hotel;
    }

    if ($selectedPackageId > 0 && isset($packageMap[$selectedPackageId])) {
        $selectedType = 'package';
    }

    if ($selectedService !== '' && isset($transportOptions[$selectedService])) {
        $selectedType = 'transport';
    }

    if ($selectedHotelId !== '' && isset($hotelMap[$selectedHotelId])) {
        $selectedType = 'hotel';
    }

} catch (PDOException $e) {
    $message = 'Database connection failed. Please check your database import.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($pdo)) {
    $selectedType      = $_POST['booking_type'] ?? 'package';
    $selectedPackageId = (int)($_POST['package_id'] ?? 0);
    $selectedService   = trim($_POST['service_name'] ?? '');
    $selectedHotelId   = trim($_POST['hotel_id'] ?? '');
    $checkIn           = trim($_POST['check_in'] ?? '');
    $checkOut          = trim($_POST['check_out'] ?? '');
    $guests            = (int)($_POST['guests'] ?? 1);
    $pickupLocation    = trim($_POST['pickup_location'] ?? '');
    $dropoffLocation   = trim($_POST['dropoff_location'] ?? '');
    $pickupTime        = trim($_POST['pickup_time'] ?? '');
    $specialRequests   = trim($_POST['special_requests'] ?? '');
    $paymentMethod     = trim($_POST['payment_method'] ?? 'card');
    $totalPrice        = 0;
    $bookingTitle      = '';

    if ($selectedType === 'package') {
        $travelDate = trim($_POST['travel_date'] ?? '');
        $travelers  = (int)($_POST['travelers'] ?? 1);

        if ($selectedPackageId <= 0 || !isset($packageMap[$selectedPackageId])) {
            $message = 'Please select a valid package.';
        } else {
            $bookingTitle = $packageMap[$selectedPackageId]['title'];
            $totalPrice   = (float)$packageMap[$selectedPackageId]['price'] * max($travelers, 1);

            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO bookings
                        (user_id, booking_type, service_name, package_id, travel_date, travelers, special_requests, total_price, status, payment_status, payment_method)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    $_SESSION['user_id'],
                    'package',
                    $bookingTitle,
                    $selectedPackageId,
                    $travelDate,
                    max($travelers, 1),
                    $specialRequests !== '' ? $specialRequests : null,
                    $totalPrice,
                    'pending',
                    'paid',
                    $paymentMethod,
                ]);

                $success = 'Package booking successful. You can see your booking below.';
                header('Location: my_bookings.php?status=success&msg=' . urlencode($success));
                exit;
            } catch (PDOException $e) {
                error_log("Package Booking Error: " . $e->getMessage());
                $message = 'Database error: Could not save your package booking. Please verify the table structure.';
            }
        }

    } elseif ($selectedType === 'transport') {
        $travelDate = trim($_POST['travel_date_trans'] ?? '');
        $travelers  = (int)($_POST['transport_travelers'] ?? 1);

        if ($selectedService === '' || !array_key_exists($selectedService, $transportOptions)) {
            $message = 'Please select a valid transport service.';
        } elseif ($pickupLocation === '' || $dropoffLocation === '') {
            $message = 'Please enter pickup and drop-off locations for transport bookings.';
        } else {
            $totalPrice = (float)$transportOptions[$selectedService] * max($travelers, 1);

            $stmt = $pdo->prepare(
                'INSERT INTO bookings
                    (user_id, booking_type, service_name, travel_date, travelers, pickup_location, dropoff_location, pickup_time, special_requests, total_price, status, payment_status, payment_method)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $_SESSION['user_id'],
                'transport',
                $selectedService,
                $travelDate,
                max($travelers, 1),
                $pickupLocation,
                $dropoffLocation,
                $pickupTime !== '' ? $pickupTime : null,
                $specialRequests !== '' ? $specialRequests : null,
                $totalPrice,
                'pending',
                'paid',
                $paymentMethod,
            ]);

            $success = 'Transport booking successful. You can see your booking below.';
            header('Location: my_bookings.php?status=success&msg=' . urlencode($success));
            exit;
        }

    } elseif ($selectedType === 'hotel') {
        if ($selectedHotelId === '' || !isset($hotelMap[$selectedHotelId])) {
            $message = 'Please select a valid hotel.';
        } elseif ($checkIn === '' || $checkOut === '') {
            $message = 'Please select check-in and check-out dates.';
        } else {
            $d1     = new DateTime($checkIn);
            $d2     = new DateTime($checkOut);
            $nights = $d1->diff($d2)->days;
            if ($nights < 1) {
                $nights = 1;
            }

            $hotel      = $hotelMap[$selectedHotelId];
            $totalPrice = (float)$hotel['price_per_night'] * $nights + 20;

            $stmt = $pdo->prepare(
                'INSERT INTO bookings
                    (user_id, booking_type, hotel_id, service_name, check_in, check_out, guests, 
                    special_requests, total_price, status, payment_status, payment_method)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $_SESSION['user_id'],
                'hotel',
                $selectedHotelId,
                $hotel['name'],
                $checkIn,
                $checkOut,
                max($guests, 1),
                $specialRequests !== '' ? $specialRequests : null,
                $totalPrice,
                'pending',
                'paid',
                $paymentMethod,
            ]);

            $success = 'Accommodation booking successful. You can see your booking below.';
            header('Location: my_bookings.php?status=success&msg=' . urlencode($success));
            exit;
        }
    }
}

$today               = date('Y-m-d');
$postTravelDate      = htmlspecialchars($_POST['travel_date'] ?? $today);
$postTravelers       = htmlspecialchars((string)($_POST['travelers'] ?? 1));
$postGuests          = htmlspecialchars((string)($_POST['guests'] ?? 1));
$postCheckIn         = htmlspecialchars($_POST['check_in'] ?? $today);
$postCheckOut        = htmlspecialchars($_POST['check_out'] ?? '');
$postPickupLocation  = htmlspecialchars($_POST['pickup_location'] ?? '');
$postDropoffLocation = htmlspecialchars($_POST['dropoff_location'] ?? '');
$postPickupTime      = htmlspecialchars($_POST['pickup_time'] ?? '');
$postSpecialRequests = htmlspecialchars($_POST['special_requests'] ?? '');

$pageTitle = "Book Your Stay | GlobeTrek";
$bodyClass = "";
include '../includes/header.php';
include '../includes/navbar.php';
?>


<link rel="stylesheet" href="../css/services.css">
<link rel="stylesheet" href="../css/booking.css">

    
    <section class="bok-hero booking-hero-premium" style="background-image: url('<?php echo appUrl('images/booking.jpg'); ?>');">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content-srv reveal visible booking-hero-content">
                <span class="hero-badge"><i class="fas fa-calendar-check"></i> Seamless Trip Planning</span>
                <h1>Book Your <em>Sri Lanka Journey</em></h1>
                <p>Manage tour packages, hotel stays, and transport requests from one booking space with clearer fields and a smoother flow.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="booking-container">
                <div class="booking-form-wrap reveal visible">
                    <div class="glass-form-card">
                        <form action="booking.php" method="POST" id="booking-form">
                            <div class="form-header">
                                <h3>Booking Details</h3>
                                <p>Select your booking type and provide the travel details needed to confirm your reservation.</p>
                            </div>

                            <?php if ($message !== ''): ?>
                                <div class="booking-msg booking-msg-spaced <?php echo htmlspecialchars($message_type); ?>">
                                    <?php echo htmlspecialchars($message); ?>
                                </div>
                            <?php endif; ?>

                            <div class="booking-switcher">
                                <label class="filter-chip <?php echo $selectedType === 'package' ? 'active' : ''; ?>">
                                    <input type="radio" name="booking_type" value="package" <?php echo $selectedType === 'package' ? 'checked' : ''; ?>> Package
                                </label>
                                <label class="filter-chip <?php echo $selectedType === 'hotel' ? 'active' : ''; ?>">
                                    <input type="radio" name="booking_type" value="hotel" <?php echo $selectedType === 'hotel' ? 'checked' : ''; ?>> Hotel
                                </label>
                                <label class="filter-chip <?php echo $selectedType === 'transport' ? 'active' : ''; ?>">
                                    <input type="radio" name="booking_type" value="transport" <?php echo $selectedType === 'transport' ? 'checked' : ''; ?>> Transport
                                </label>
                            </div>

                            
                            <div class="booking-type-panel <?php echo $selectedType === 'package' ? 'active' : ''; ?>" data-panel="package">
                                <div class="form-group-srv booking-panel-field">
                                    <label for="package_id">Select Package</label>
                                    <select name="package_id" id="package_id">
                                        <option value="">Choose a package</option>
                                        <?php foreach ($packages as $pkg): ?>
                                            <option value="<?php echo $pkg['id']; ?>" <?php echo $selectedPackageId == $pkg['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($pkg['title']); ?> &ndash; $<?php echo htmlspecialchars((string)$pkg['price']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="grid-2-srv">
                                    <div class="form-group-srv">
                                        <label for="travel_date">Travel Date</label>
                                        <input type="date" name="travel_date" id="travel_date" min="<?php echo $today; ?>" value="<?php echo $postTravelDate; ?>">
                                    </div>
                                    <div class="form-group-srv">
                                        <label for="travelers">Travelers</label>
                                        <input type="number" name="travelers" id="travelers" min="1" value="<?php echo $postTravelers; ?>">
                                    </div>
                                </div>
                            </div>

                            
                            <div class="booking-type-panel <?php echo $selectedType === 'hotel' ? 'active' : ''; ?>" data-panel="hotel">
                                <div class="form-group-srv booking-panel-field">
                                    <label for="hotel_id">Select Hotel</label>
                                    <select name="hotel_id" id="hotel_id">
                                        <option value="">Choose a hotel</option>
                                        <?php foreach ($hotelsByCategory as $category => $hotelsInCategory): ?>
                                            <optgroup label="<?php echo htmlspecialchars($hotelCategoryLabels[$category] ?? ucfirst($category)); ?>">
                                                <?php foreach ($hotelsInCategory as $htl): ?>
                                                    <option value="<?php echo htmlspecialchars((string)$htl['slug']); ?>" <?php echo $selectedHotelId === $htl['slug'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars((string)$htl['name']); ?> &ndash; $<?php echo htmlspecialchars((string)$htl['price_per_night']); ?>/night
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="grid-2-srv">
                                    <div class="form-group-srv">
                                        <label for="check_in">Check-in</label>
                                        <input type="date" name="check_in" id="check_in" min="<?php echo $today; ?>" value="<?php echo $postCheckIn; ?>">
                                    </div>
                                    <div class="form-group-srv">
                                        <label for="check_out">Check-out</label>
                                        <input type="date" name="check_out" id="check_out" min="<?php echo $today; ?>" value="<?php echo $postCheckOut; ?>">
                                    </div>
                                </div>
                                <div class="form-group-srv">
                                    <label for="guests">Guests</label>
                                    <input type="number" name="guests" id="guests" min="1" value="<?php echo $postGuests; ?>">
                                </div>
                            </div>

                            
                            
                            <div class="booking-type-panel <?php echo $selectedType === 'transport' ? 'active' : ''; ?>" data-panel="transport">
                                <div class="form-group-srv booking-panel-field">
                                    <label for="service_name">Select Transport</label>
                                    <select name="service_name" id="service_name">
                                        <option value="">Choose transport</option>
                                        <?php foreach ($transportOptions as $name => $price): ?>
                                            <option value="<?php echo htmlspecialchars($name); ?>" <?php echo $selectedService === $name ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($name); ?> &ndash; From $<?php echo htmlspecialchars((string)$price); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="grid-2-srv">
                                    <div class="form-group-srv">
                                        <label for="travel_date_trans">Travel Date</label>
                                        <input type="date" name="travel_date_trans" id="travel_date_trans" min="<?php echo $today; ?>" value="<?php echo $postTravelDate; ?>">
                                    </div>
                                    <div class="form-group-srv">
                                        <label for="travelers_trans">Passengers</label>
                                        <input type="number" name="transport_travelers" id="travelers_trans" min="1" value="<?php echo $postTravelers; ?>">
                                    </div>
                                </div>
                                <div class="grid-2-srv">
                                    <div class="form-group-srv">
                                        <label for="pickup_location">Pickup</label>
                                        <input type="text" name="pickup_location" id="pickup_location" value="<?php echo $postPickupLocation; ?>">
                                    </div>
                                    <div class="form-group-srv">
                                        <label for="dropoff_location">Drop-off</label>
                                        <input type="text" name="dropoff_location" id="dropoff_location" value="<?php echo $postDropoffLocation; ?>">
                                    </div>
                                </div>
                                <div class="form-group-srv">
                                    <label for="pickup_time">Pickup Time</label>
                                    <input type="time" name="pickup_time" id="pickup_time" value="<?php echo $postPickupTime; ?>">
                                </div>
                            </div>

                            
                            <div class="form-group-srv booking-form-spaced-top">
                                <label for="special_requests">Special Requests</label>
                                <textarea name="special_requests" id="special_requests" rows="4"><?php echo $postSpecialRequests; ?></textarea>
                            </div>

                            <div class="booking-inline-note">
                                Your request stays linked to your account, so you can review everything later in one place.
                            </div>

                            <div class="step-action" style="margin-top: 30px; text-align: right;">
                                <button type="button" id="btn-next-to-payment" class="btn-primary">
                                    Next: Payment Details <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>

                            
                            <div class="booking-type-panel booking-payment-panel" data-panel="payment" id="payment-panel">
                                <h3><i class="fas fa-credit-card"></i> Secure Payment</h3>

                                <div class="form-group-srv">
                                    <label for="payment_method">Select Payment Method</label>
                                    <select name="payment_method" id="payment_method" required>
                                        <option value="card" selected>Credit / Debit Card</option>
                                        <option value="paypal">PayPal</option>
                                        <option value="transfer">Bank Transfer</option>
                                    </select>
                                </div>

                                
                                <div class="payment-method-info payment-info-card active" data-method="card">
                                    <p><i class="fab fa-cc-visa"></i> <i class="fab fa-cc-mastercard"></i> We accept all major cards.</p>
                                    <input type="text" placeholder="Card Number" class="form-control payment-input-field">
                                    <div class="payment-input-group">
                                        <input type="text" placeholder="MM/YY" class="form-control payment-input-field-half">
                                        <input type="text" placeholder="CVC" class="form-control payment-input-field-half">
                                    </div>
                                </div>

                                
                                <div class="payment-method-info payment-info-transfer" data-method="transfer">
                                    <p><strong>Bank Details:</strong><br>
                                    GlobeTrek Adventures Ltd.<br>
                                    Bank: Bank of Ceylon<br>
                                    Account: 1234567890</p>
                                </div>

                                
                                <div class="payment-method-info payment-info-paypal" data-method="paypal">
                                    <p><i class="fab fa-paypal"></i> You will be redirected to PayPal to complete your payment securely.</p>
                                </div>

                                <button type="submit" class="btn-book-premium">Confirm &amp; Pay Booking</button>
                            </div>

                            <a href="my_bookings.php" class="booking-link-center">View My Bookings</a>
                        </form>
                    </div>
                </div>

                <aside class="booking-summary reveal visible">
                    <div class="info-header">
                        <h2 class="section-title">How It <em>Works</em></h2>
                        <p class="section-desc">A single booking workspace for guided trips, hotel stays, and trusted transport around the island.</p>
                    </div>

                    <div class="contact-methods">
                        <div class="contact-method-card">
                            <div class="method-icon"><i class="fas fa-compass"></i></div>
                            <div class="method-body">
                                <h4>Pick the Right Service</h4>
                                <p>Switch between package, hotel, and transport bookings without leaving the page.</p>
                            </div>
                        </div>
                        <div class="contact-method-card">
                            <div class="method-icon"><i class="fas fa-list-check"></i></div>
                            <div class="method-body">
                                <h4>Complete Only Relevant Fields</h4>
                                <p>The form now shows only the details needed for the booking type you selected.</p>
                            </div>
                        </div>
                        <div class="contact-method-card">
                            <div class="method-icon"><i class="fas fa-receipt"></i></div>
                            <div class="method-body">
                                <h4>Track Everything Later</h4>
                                <p>Use your dashboard and bookings page to review requests, status, and next steps.</p>
                            </div>
                        </div>
                    </div>

                    <div class="booking-feature-list mt-40">
                        <div class="booking-feature-item">
                            <strong>Connected with packages and hotels</strong>
                            <p>Links from package and hotel pages can land here with the right booking type already selected.</p>
                        </div>
                        <div class="booking-feature-item">
                            <strong>Made for customer accounts</strong>
                            <p>Requests are stored against your profile so the team can confirm details faster.</p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <script src="../js/booking.js"></script>
    
    <?php include '../includes/footer.php'; ?>