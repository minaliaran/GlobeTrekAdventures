<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | GlobeTrek Adventures</title>
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
                    <h2>Reset Password</h2>
                    <p>Enter your email address and we will prepare a reset link for your account.</p>
                </div>

                <div id="forgot-message" class="auth-message is-hidden"></div>

                <form id="forgot-password-form" class="auth-form" autocomplete="on" novalidate>
                    <div class="form-group">
                        <label for="forgot-email">Email Address</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input
                                type="email"
                                id="forgot-email"
                                name="email"
                                placeholder="you@example.com"
                                autocomplete="email"
                                inputmode="email"
                                required>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary btn-full">Request Reset Link <i class="fas fa-paper-plane"></i></button>

                    <div id="forgot-reset-link" class="auth-helper is-hidden auth-footer--spaced">
                        <p class="auth-helper-title">Local test reset link:</p>
                        <a id="forgot-reset-anchor" class="btn btn-full" href="#">Open Reset Page</a>
                    </div>

                    <div class="auth-footer auth-footer--spaced">
                        <p>Remembered your password? <a href="login.php">Back to Login</a></p>
                    </div>
                </form>
            </div>

            <div class="auth-image" id="login-img">
                <div class="auth-overlay"></div>
                <div class="auth-content">
                    <h3>Get Back to Your Trip</h3>
                    <p>Request a secure password reset and continue planning your Sri Lanka adventure.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer"></footer>
    <script src="../js/script.js"></script>
    <script src="../js/auth.js"></script>
</body>
</html>
