<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=' . urlencode('hotel.php?slug=' . ($_POST['hotel_id'] ?? '')));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . appUrl('php/accommodation.php'));
    exit;
}

try {
    $pdo = getPDO();
    ensureBookingSchema($pdo);
    ensureHotelSchema($pdo);

    $hotelSlug = trim($_POST['hotel_id'] ?? '');
    $checkIn = trim($_POST['check_in'] ?? '');
    $checkOut = trim($_POST['check_out'] ?? '');
    $guests = (int)($_POST['guests'] ?? 1);
    $guestName = trim($_POST['guest_name'] ?? '');
    $guestEmail = trim($_POST['guest_email'] ?? '');
    $guestPhone = trim($_POST['guest_phone'] ?? '');
    $roomType = trim($_POST['room_type'] ?? '');

   
    if (empty($hotelSlug) || empty($checkIn) || empty($checkOut) || empty($guestName) || empty($guestEmail)) {
        $error = 'All fields are required.';
        header("Location: hotel.php?slug=$hotelSlug&status=error&msg=" . urlencode($error));
        exit;
    }

    
    $checkInDate = new DateTime($checkIn);
    $checkOutDate = new DateTime($checkOut);
    $today = new DateTime();

    if ($checkInDate < $today) {
        $error = 'Check-in date cannot be in the past.';
        header("Location: hotel.php?slug=$hotelSlug&status=error&msg=" . urlencode($error));
        exit;
    }

    if ($checkOutDate <= $checkInDate) {
        $error = 'Check-out date must be after check-in date.';
        header("Location: hotel.php?slug=$hotelSlug&status=error&msg=" . urlencode($error));
        exit;
    }

    
    $stmt = $pdo->prepare("SELECT name, category, price_per_night FROM hotels WHERE slug = ?");
    $stmt->execute([$hotelSlug]);
    $hotel = $stmt->fetch();

    if (!$hotel) {
        $error = 'Hotel not found.';
        header("Location: hotel.php?slug=$hotelSlug&status=error&msg=" . urlencode($error));
        exit;
    }


    $nights = $checkInDate->diff($checkOutDate)->days;
    if ($nights < 1) $nights = 1;

    $subtotal = (float)$hotel['price_per_night'] * $nights;
    $serviceFee = 20.00;
    $totalPrice = $subtotal + $serviceFee;

   
    $stmt = $pdo->prepare(
        "INSERT INTO bookings (user_id, booking_type, hotel_id, service_name, check_in, check_out, guests, total_price, status, guest_name, guest_email, guest_phone, room_type)
         VALUES (?, 'hotel', ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?)"
    );

    $stmt->execute([
        $_SESSION['user_id'],
        $hotelSlug,
        $hotel['name'],
        $checkIn,
        $checkOut,
        $guests,
        $totalPrice,
        $guestName,
        $guestEmail,
        $guestPhone,
        $roomType ?: null
    ]);

    
    $success = 'Accommodation booking successful. You can see your booking below.';
    header('Location: my_bookings.php?status=success&msg=' . urlencode($success));
    exit;

} catch (Exception $e) {
    error_log("Hotel booking error: " . $e->getMessage());
    $error = 'An error occurred while processing your booking. Please try again.';
    $hotelSlug = $_POST['hotel_id'] ?? '';
    header("Location: hotel.php?slug=$hotelSlug&status=error&msg=" . urlencode($error));
    exit;
}
?>