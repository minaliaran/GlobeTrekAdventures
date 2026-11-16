<?php

require_once __DIR__ . '/config.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($query)) {
    header("Location: " . appUrl('index.php'));
    exit;
}

$normalized = strtolower($query);
$collapsed = preg_replace('/\s+/', '', $normalized);


$destinations = [
    'anuradhapura' => 'destinations/anuradhapura/anuradhapura.html',
    'arugambay' => 'destinations/arugambay/arugambay.html',
    'badulla' => 'destinations/badulla/badulla.html',
    'bentota' => 'destinations/bentota/bentota.html',
    'colombo' => 'destinations/colombo/colombo.html',
    'dambulla' => 'destinations/dambulla/dambulla.html',
    'ella' => 'destinations/ella/ella.html',
    'galle' => 'destinations/galle/galle.html',
    'haputale' => 'destinations/haputale/haputale.html',
    'hikkaduwa' => 'destinations/hikkaduwa/hikkaduwa.html',
    'kalpitiya' => 'destinations/kalpitiya/kalpitiya.html',
    'kandy' => 'destinations/kandy/kandy.html',
    'mirissa' => 'destinations/mirissa/mirissa.html',
    'negambo' => 'destinations/negambo/negombo.html',
    'negombo' => 'destinations/negambo/negombo.html',
    'nuwaraeliya' => 'destinations/nuwaraeliya/nuwaraeliya.html',
    'pasikuda' => 'destinations/pasikuda/pasikuda.html',
    'polonnaruwa' => 'destinations/polonnaruwa/polonnaruwa.html',
    'ratnapura' => 'destinations/ratnapura/ratnapura.html',
    'sigiriya' => 'destinations/sigiriya/sigiriya.html',
    'thrincomalee' => 'destinations/thrincomalee/trincomalee.html',
    'trincomalee' => 'destinations/thrincomalee/trincomalee.html',
    'udawalawe' => 'destinations/udawalawe/udawalawe.html',
    'yala' => 'destinations/yala/yala.html'
];


foreach ($destinations as $key => $path) {
    if ($normalized === $key || $collapsed === $key) {
        header("Location: " . appUrl($path));
        exit;
    }
}


$routes = [
    ['keys' => ['package', 'tour', 'deal', 'itinerary'], 'href' => 'php/packages.php'],
    ['keys' => ['hotel', 'accommodation', 'stay', 'resort', 'hostel'], 'href' => 'php/accommodation.php'],
    ['keys' => ['transport', 'train', 'bus', 'taxi', 'transfer', 'driver'], 'href' => 'transport.html'],
    ['keys' => ['gallery', 'photo', 'image', 'picture'], 'href' => 'gallery.html'],
    ['keys' => ['guide', 'travel guide', 'visa', 'budget', 'tips'], 'href' => 'guide.html'],
    ['keys' => ['destination', 'place', 'city', 'map'], 'href' => 'destinations/destinations.html'],
    ['keys' => ['thing', 'activity', 'adventure', 'experience'], 'href' => 'thingstodo/things-to-do.html'],
    ['keys' => ['contact', 'support', 'help', 'message'], 'href' => 'contact.html'],
    ['keys' => ['booking', 'book', 'reserve'], 'href' => 'php/booking.php'],
    ['keys' => ['about', 'company', 'team'], 'href' => 'php/about.php']
];

foreach ($routes as $route) {
    foreach ($route['keys'] as $key) {
        if (strpos($normalized, $key) !== false) {
            header("Location: " . appUrl($route['href']));
            exit;
        }
    }
}


foreach ($destinations as $key => $path) {
    if (strpos($normalized, $key) !== false) {
        header("Location: " . appUrl($path));
        exit;
    }
}


header("Location: " . appUrl('index.php?search_error=true&query=' . urlencode($query)));
exit;
