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
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
</head>

<body class="admin-body">
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h2>📚 Admin Panel</h2>
            </div>

            <nav class="admin-nav">
                <a href="<?php echo SITE_URL; ?>/admin/dashboard.php"
                    class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                    📊 Dashboard
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/books.php"
                    class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['books.php', 'book_form.php', 'book_actions.php']) ? 'active' : ''; ?>">
                    📚 Manage Books
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/orders.php"
                    class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['orders.php', 'order_detail.php']) ? 'active' : ''; ?>">
                    📦 Manage Orders
                </a>
                <a href="<?php echo SITE_URL; ?>/auth/logout.php">
                    🚪 Logout
                </a>
            </nav>

            <div class="admin-user-info">
                <p>Logged in as:</p>
                <p><strong>
                        <?php echo escapeHTML($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>
                    </strong></p>
            </div>
        </aside>

        <main class="admin-main">