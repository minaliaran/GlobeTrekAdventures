<?php
require_once __DIR__ . '/config.php';

$message = '';
$message_type = '';

if (isset($_SESSION['role'])) {
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
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = 'customer'; 

    if ($name === '' || $email === '' || $password === '') {
        $message = 'Please fill in all fields.';
        $message_type = 'error';
    } else {
        try {
            $pdo = getPDO();

            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $message = 'Email already exists.';
                $message_type = 'error';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
                if ($stmt->execute([$name, $email, $hashedPassword, $role])) {
                    $message = 'Registration successful! You can now log in.';
                    $message_type = 'success';
                    header('Refresh: 2; URL=login.php');
                } else {
                    $message = 'Registration failed. Please try again.';
                    $message_type = 'error';
                }
            }
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
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
    <title>Register - GlobeTrek Adventures</title>
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
<body class="auth-layout dashboard-body register-page">
    <header class="navbar" id="navbar"></header>

    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-box">
                <div class="auth-header">
                    <h2>Create Account</h2>
                    <p>Join GlobeTrek Adventures and start planning your Sri Lanka journey.</p>
                </div>

                <?php if ($message !== ''): ?>
                    <div class="auth-message <?php echo htmlspecialchars($message_type); ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST" class="auth-form" autocomplete="on" novalidate>
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                placeholder="Enter your full name"
                                autocomplete="name"
                                required>
                        </div>
                    </div>

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
                                placeholder="Create a password"
                                autocomplete="new-password"
                                required>
                            <button class="password-toggle" type="button" data-toggle-password="password" aria-label="Show password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>



                    <div class="auth-options">
                        <label class="checkbox-container">
                            <input type="checkbox" name="terms" required>
                            <span class="checkmark"></span>
                            I agree to the <a href="terms.php">Terms & Conditions</a>
                        </label>
                    </div>

                    <button type="submit" class="btn-primary btn-full">Register <i class="fas fa-user-plus"></i></button>

                    <div class="auth-footer">
                        <p>Already have an account? <a href="login.php">Login Now</a></p>
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
