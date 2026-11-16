<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password | GlobeTrek Adventures</title>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/components.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="../css/navigation.css">
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body class="auth-layout dashboard-body login-page">
    <header class="navbar" id="navbar"></header>

    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-box">
                <div class="auth-header">
                    <h2>Set New Password</h2>
                    <p>Choose a new password for your account.</p>
                </div>

                <div id="reset-message" class="auth-message is-hidden"></div>

                <form id="reset-password-form" class="auth-form" autocomplete="off" novalidate>
                    <input type="hidden" id="reset-token" name="token">

                    <div class="form-group">
                        <label for="new-password">New Password</label>
                        <div class="input-with-icon has-toggle">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="new-password" name="password" placeholder="Enter new password" required>
                            <button class="password-toggle" type="button" data-toggle-password="new-password" aria-label="Show password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm-new-password">Confirm Password</label>
                        <div class="input-with-icon has-toggle">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm-new-password" name="confirm_password" placeholder="Confirm new password" required>
                            <button class="password-toggle" type="button" data-toggle-password="confirm-new-password" aria-label="Show password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary btn-full">Save New Password <i class="fas fa-key"></i></button>

                    <div class="auth-footer auth-footer--spaced">
                        <p><a href="login.php">Return to Login</a></p>
                    </div>
                </form>
            </div>

            <div class="auth-image" id="register-img">
                <div class="auth-overlay"></div>
                <div class="auth-content">
                    <h3>Secure Your Account</h3>
                    <p>Choose a strong password to continue using GlobeTrek Adventures safely.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer"></footer>
    <script src="../js/script.js"></script>
    <script src="../js/auth.js"></script>
</body>
</html>
