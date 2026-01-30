<?php
/**
 * Admin Header Component
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
        <?php echo isset($pageTitle) ? escapeHTML($pageTitle) . ' - ' : ''; ?>Admin -
        <?php echo SITE_NAME; ?>
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
</head>

<body class="admin-panel">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-sidebar-header">
            <h2 class="admin-sidebar-title">Admin Panel</h2>
        </div>

        <nav class="admin-sidebar-nav">
            <a href="<?php echo SITE_URL; ?>/admin/dashboard.php"
                class="admin-nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                <iconify-icon icon="solar:widget-linear" width="18"></iconify-icon>
                Dashboard
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/books.php"
                class="admin-nav-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['books.php', 'book_form.php', 'book_actions.php']) ? 'active' : ''; ?>">
                <iconify-icon icon="solar:book-2-linear" width="18"></iconify-icon>
                Books
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/orders.php"
                class="admin-nav-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['orders.php', 'order_detail.php']) ? 'active' : ''; ?>">
                <iconify-icon icon="solar:bag-3-linear" width="18"></iconify-icon>
                Orders
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/accounts.php"
                class="admin-nav-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['accounts.php', 'account_detail.php', 'account_actions.php']) ? 'active' : ''; ?>">
                <iconify-icon icon="solar:users-group-rounded-linear" width="18"></iconify-icon>
                Accounts
            </a>

            <div class="admin-nav-divider"></div>

            <a href="<?php echo SITE_URL; ?>/admin/profile.php"
                class="admin-nav-item <?php echo in_array(basename($_SERVER['PHP_SELF']), ['profile.php', 'profile_actions.php']) ? 'active' : ''; ?>">
                <iconify-icon icon="solar:user-circle-linear" width="18"></iconify-icon>
                Profile
            </a>

            <div class="admin-nav-divider"></div>

            <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="admin-nav-item danger">
                <iconify-icon icon="solar:logout-2-linear" width="18"></iconify-icon>
                Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="admin-content">
        <div class="admin-content-wrapper">