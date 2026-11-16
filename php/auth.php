<?php
require_once __DIR__ . '/config.php';

if (!function_exists('isLoggedIn')) {
    function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('currentRole')) {
    function currentRole(): string
    {
        return strtolower(trim((string) ($_SESSION['role'] ?? 'guest')));
    }
}

if (!function_exists('requireLogin')) {
    function requireLogin(): void
    {
        if (!isLoggedIn()) {
            header('Location: ' . appUrl('php/login.php'));
            exit;
        }
    }
}

if (!function_exists('requireRole')) {
    function requireRole(string $role): void
    {
        requireLogin();

        if (currentRole() !== strtolower(trim($role))) {
            header('Location: ' . appUrl('php/login.php'));
            exit;
        }
    }
}

if (!function_exists('requireStaff')) {
    function requireStaff(): void
    {
        requireLogin();

        if (!in_array(currentRole(), ['staff', 'admin'], true)) {
            header('Location: ' . appUrl('php/login.php'));
            exit;
        }
    }
}

if (!function_exists('requireAdmin')) {
    function requireAdmin(): void
    {
        requireRole('admin');
    }
}

if (!function_exists('requireCustomer')) {
    function requireCustomer(): void
    {
        requireRole('customer');
    }
}
