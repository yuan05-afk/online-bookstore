<?php
/**
 * Modern Authentication Header Component
 */

if (!defined('SITE_NAME')) {
    die('Direct access not permitted');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($pageTitle) ? escapeHTML($pageTitle) . ' - ' : ''; ?>
        <?php echo SITE_NAME; ?>
    </title>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/auth.css">
</head>

<body class="auth-page">

    <!-- Modern Navbar -->
    <header class="auth-header">
        <div class="auth-header-container">
            <a href="<?php echo SITE_URL; ?>" class="auth-logo">
                <div class="auth-logo-icon">
                    <iconify-icon icon="solar:book-2-linear" width="18" stroke-width="1.5"></iconify-icon>
                </div>
                <span class="auth-logo-text">
                    <?php echo SITE_NAME; ?>
                </span>
            </a>
            <div class="auth-center-logo">
                <img src="<?php echo SITE_URL; ?>/assets/images/logo-long.png" alt="Night Owl Books"
                    class="auth-brand-logo">
            </div>
            <div class="auth-actions">
                <?php if (basename($_SERVER['PHP_SELF']) !== 'login.php'): ?>
                    <a href="<?php echo SITE_URL; ?>/auth/login.php" class="auth-btn-signin">Sign In</a>
                <?php endif; ?>
                <?php if (basename($_SERVER['PHP_SELF']) !== 'register.php'): ?>
                    <a href="<?php echo SITE_URL; ?>/auth/register.php" class="auth-btn-primary">
                        Get Started
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="auth-main">