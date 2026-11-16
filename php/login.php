<?php
require_once __DIR__ . '/config.php';

$message = '';
$message_type = '';
$redirect = trim($_GET['redirect'] ?? $_POST['redirect'] ?? '');

if ($redirect !== '') {
    $redirect = ltrim($redirect, '/\\');
    $isUnsafe = function_exists('str_contains') ? str_contains($redirect, '..') : strpos($redirect, '..') !== false;
    if (preg_match('/^(?:https?:)?\/\//i', $redirect) || $isUnsafe) {
        $redirect = '';
    }
}

if (isset($_SESSION['role'])) {
    if ($redirect !== '') {
        header('Location: ' . $redirect);
        exit;
    }

    if ($_SESSION['role'] === 'admin') {
        header('Location: ' . appUrl('admin/dashboard.php'));
        exit;
    }

    if ($_SESSION['role'] === 'staff') {
        header('Location: ' . appUrl('admin/staff_dashboard.php'));
        exit;
    }

    header('Location: ' . appUrl('php/dashboard.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $message = 'Please enter your email and password.';
        $message_type = 'error';
    } else {
        try {
            $pdo = getPDO();

            // Hardcoded credentials for admin and staff (fallback)
            $hardcodedUsers = [
                'admin@globetrek.com' => ['password' => 'admin 123', 'role' => 'admin', 'name' => 'System Admin', 'id' => 1],
                'staff@globetrek.com' => ['password' => 'staff 123', 'role' => 'staff', 'name' => 'GlobeTrek Staff', 'id' => 2],
            ];

            $isHardcodedUser = isset($hardcodedUsers[$email]) && $hardcodedUsers[$email]['password'] === $password;

            if ($isHardcodedUser) {
                $user = $hardcodedUsers[$email];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $user['role'];
                $_SESSION['profile_photo'] = 'images/default-avatar.png';

                if ($redirect !== '') {
                    header('Location: ' . $redirect);
                    exit;
                }

                if ($user['role'] === 'admin') {
                    header('Location: ' . appUrl('admin/dashboard.php'));
                    exit;
                }

                if ($user['role'] === 'staff') {
                    header('Location: ' . appUrl('admin/staff_dashboard.php'));
                    exit;
                }

                header('Location: ' . appUrl('php/dashboard.php'));
                exit;
            }

            $stmt = $pdo->prepare('SELECT id, name, email, password, role, profile_photo FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['profile_photo'] = $user['profile_photo'] ?? 'images/default-avatar.png';

                if ($redirect !== '') {
                    header('Location: ' . $redirect);
                    exit;
                }

                if ($user['role'] === 'admin') {
                    header('Location: ' . appUrl('admin/dashboard.php'));
                    exit;
                }

                if ($user['role'] === 'staff') {
                    header('Location: ' . appUrl('admin/staff_dashboard.php'));
                    exit;
                }

                header('Location: ' . appUrl('php/dashboard.php'));
                exit;
            }

            $message = 'Invalid email or password.';
            $message_type = 'error';
        } catch (PDOException $e) {
            $message = 'Database connection failed. Please import database.sql into phpMyAdmin.';
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GlobeTrek Adventures</title>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/components.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="../css/navigation.css">
    <link rel="stylesheet" href="../css/auth.css?v=1.2">
</head>
<body class="auth-layout dashboard-body login-page">
    <header class="navbar" id="navbar"></header>

    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-box">
                <div class="auth-brand-logo" style="text-align: center; margin-bottom: 25px;">
                   
                </div>

                <div class="auth-header">
                    <h2>Welcome Back</h2>
                    <p>Log in to manage your GlobeTrek adventures.</p>
                </div>

                <?php if ($message !== ''): ?>
                    <div class="auth-message <?php echo htmlspecialchars($message_type); ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST" class="auth-form" autocomplete="on" novalidate>
                    <?php if ($redirect !== ''): ?>
                        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="you@example.com"
                                autocomplete="email"
                                inputmode="email"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-with-icon has-toggle">
                            <i class="fas fa-lock"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required>
                            <button class="password-toggle" type="button" data-toggle-password="password" aria-label="Show password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="auth-options">
                        <label class="checkbox-container">
                            <input type="checkbox" name="remember" value="1">
                            <span class="checkmark"></span>
                            Remember Me
                        </label>
                        <a href="forgot-password.php" class="forgot-pass">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn-primary btn-full">Login <i class="fas fa-sign-in-alt"></i></button>

                    <div class="auth-footer">
                        <p>Don't have an account? <a href="register.php">Register Now</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/newsletter.php'; ?>

    <footer class="footer"></footer>
    <script src="../js/script.js"></script>
    <script src="../js/auth.js"></script>
</body>
</html>
