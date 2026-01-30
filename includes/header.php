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
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo SITE_URL; ?>/user/catalog.php">
                        📚 <?php echo SITE_NAME; ?>
                    </a>
                </div>
                
                <nav class="main-nav">
                    <a href="<?php echo SITE_URL; ?>/user/catalog.php">Browse Books</a>
                    <a href="<?php echo SITE_URL; ?>/user/orders.php">My Orders</a>
                </nav>
                
                <div class="header-actions">
                    <a href="<?php echo SITE_URL; ?>/user/cart.php" class="cart-icon">
                        🛒 Cart
                        <?php if ($cartCount > 0): ?>
                            <span class="cart-badge" id="cart-count"><?php echo $cartCount; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <div class="user-menu">
                        <button class="user-menu-toggle">
                            👤 <?php echo escapeHTML($_SESSION['first_name'] ?? 'User'); ?>
                        </button>
                        <div class="user-menu-dropdown">
                            <a href="<?php echo SITE_URL; ?>/auth/logout.php">Logout</a>
                        </div>
                    </div>
                </div>
                
                <button class="mobile-menu-toggle">☰</button>
            </div>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container">
