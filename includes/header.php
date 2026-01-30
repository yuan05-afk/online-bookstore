<?php
/**
 * User Header Component
 */

if (!defined('SITE_NAME')) {
    die('Direct access not permitted');
}

$cartCount = 0;
if (isLoggedIn() && !isAdmin()) {
    $stmt = getDB()->prepare("SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?");
    $stmt->execute([getCurrentUserId()]);
    $result = $stmt->fetch();
    $cartCount = $result['count'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? escapeHTML($pageTitle) . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/user.css">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</head>

<body class="user-page">
    <header class="user-header">
        <div class="user-header-container">
            <a href="<?php echo SITE_URL; ?>/user/catalog.php" class="user-logo">
                <div class="user-logo-icon">
                    <iconify-icon icon="solar:book-2-linear" width="18" stroke-width="1.5"></iconify-icon>
                </div>
                <span class="user-logo-text"><?php echo SITE_NAME; ?></span>
            </a>

            <nav class="user-nav">
                <a href="<?php echo SITE_URL; ?>/user/catalog.php">Browse Books</a>
                <a href="<?php echo SITE_URL; ?>/user/orders.php">My Orders</a>
            </nav>

            <div class="user-header-actions">
                <a href="<?php echo SITE_URL; ?>/user/cart.php" class="user-cart-link">
                    <div class="user-cart-icon-wrapper">
                        <iconify-icon icon="solar:cart-large-linear" width="20" stroke-width="1.5"></iconify-icon>
                        <?php if ($cartCount > 0): ?>
                            <span class="user-cart-badge" id="cart-count"><?php echo $cartCount; ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="user-cart-label">Cart</span>
                </a>

                <div class="user-menu">
                    <button class="user-menu-toggle" id="userMenuToggle">
                        <iconify-icon icon="solar:user-circle-linear" width="18" stroke-width="1.5"></iconify-icon>
                        <span><?php echo escapeHTML($_SESSION['first_name'] ?? 'User'); ?></span>
                        <iconify-icon icon="solar:alt-arrow-down-linear" width="14"></iconify-icon>
                    </button>
                    <div class="user-menu-dropdown" id="userMenuDropdown">
                        <a href="<?php echo SITE_URL; ?>/user/profile.php" class="user-menu-item">
                            <iconify-icon icon="solar:user-linear" width="18"></iconify-icon>
                            <span>My Profile</span>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="user-menu-item">
                            <iconify-icon icon="solar:logout-2-linear" width="18"></iconify-icon>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="user-main">
        <div class="user-container">
            <?php
            $flash = getFlashMessage();
            if ($flash):
                ?>
                <div class="notification notification-<?php echo $flash['type']; ?> show">
                    <?php echo escapeHTML($flash['message']); ?>
                </div>
                <script>
                    setTimeout(() => {
                        const notification = document.querySelector('.notification');
                        if (notification) {
                            notification.classList.remove('show');
                            setTimeout(() => notification.remove(), 300);
                        }
                    }, 5000);
                </script>
            <?php endif; ?>