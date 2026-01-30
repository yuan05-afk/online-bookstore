<?php
/**
 * Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireAdmin();

$db = getDB();

// Get statistics
$stmt = $db->query("SELECT COUNT(*) as total FROM books");
$totalBooks = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM orders");
$totalOrders = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$totalUsers = $stmt->fetch()['total'];

$stmt = $db->query("SELECT SUM(grand_total) as revenue FROM orders WHERE status != 'Cancelled'");
$totalRevenue = $stmt->fetch()['revenue'] ?? 0;

// Get recent orders
$stmt = $db->query("
    SELECT o.*, u.email, u.first_name, u.last_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
");
$recentOrders = $stmt->fetchAll();

// Get low stock books
$stmt = $db->query("
    SELECT * FROM books
    WHERE stock_quantity < 10
    ORDER BY stock_quantity ASC
    LIMIT 10
");
$lowStockBooks = $stmt->fetchAll();

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/../includes/admin_header.php';
?>

<h1 class="admin-page-title">Dashboard Overview</h1>

<!-- Stats Grid -->
<div class="admin-grid admin-grid-cols-4 admin-mb-8">
    <div class="admin-stats-card">
        <div class="admin-stats-header">
            <span class="admin-stats-label">Total Books</span>
            <div class="admin-stats-icon">
                <iconify-icon icon="solar:book-2-linear" width="20"></iconify-icon>
            </div>
        </div>
        <div class="admin-stats-value">
            <?php echo number_format($totalBooks); ?>
        </div>
        <div class="admin-stats-trend admin-text-muted">
            Inventory status
        </div>
    </div>

    <div class="admin-stats-card">
        <div class="admin-stats-header">
            <span class="admin-stats-label">Total Orders</span>
            <div class="admin-stats-icon">
                <iconify-icon icon="solar:bag-3-linear" width="20"></iconify-icon>
            </div>
        </div>
        <div class="admin-stats-value">
            <?php echo number_format($totalOrders); ?>
        </div>
        <div class="admin-stats-trend positive">
            <iconify-icon icon="solar:trending-up-linear" width="14"></iconify-icon>
            +4.2%
        </div>
    </div>

    <div class="admin-stats-card">
        <div class="admin-stats-header">
            <span class="admin-stats-label">Total Users</span>
            <div class="admin-stats-icon">
                <iconify-icon icon="solar:users-group-rounded-linear" width="20"></iconify-icon>
            </div>
        </div>
        <div class="admin-stats-value">
            <?php echo number_format($totalUsers); ?>
        </div>
        <div class="admin-stats-trend positive">
            <iconify-icon icon="solar:trending-up-linear" width="14"></iconify-icon>
            +8.1%
        </div>
    </div>

    <div class="admin-stats-card">
        <div class="admin-stats-header">
            <span class="admin-stats-label">Total Revenue</span>
            <div class="admin-stats-icon">
                <iconify-icon icon="solar:dollar-linear" width="20"></iconify-icon>
            </div>
        </div>
        <div class="admin-stats-value">
            <?php echo formatPrice($totalRevenue); ?>
        </div>
        <div class="admin-stats-trend positive">
            <iconify-icon icon="solar:trending-up-linear" width="14"></iconify-icon>
            +12.5%
        </div>
    </div>
</div>

<!-- Recent Orders and Low Stock -->
<div class="admin-grid admin-grid-cols-3">
    <!-- Recent Orders (2/3 width) -->
    <div style="grid-column: span 2;">
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Recent Orders</h3>
                <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="admin-btn admin-btn-sm admin-btn-secondary">
                    View All
                </a>
            </div>
            <div class="admin-table-wrapper">
                <?php if (empty($recentOrders)): ?>
                    <div class="admin-card-body">
                        <p class="admin-text-muted">No orders yet.</p>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo escapeHTML($order['order_number']); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo escapeHTML($order['first_name'] . ' ' . $order['last_name']); ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = 'neutral';
                                        $status = strtolower($order['status']);
                                        if ($status === 'delivered')
                                            $statusClass = 'success';
                                        elseif ($status === 'processing')
                                            $statusClass = 'warning';
                                        elseif ($status === 'cancelled')
                                            $statusClass = 'danger';
                                        ?>
                                        <span class="admin-badge admin-badge-<?php echo $statusClass; ?>">
                                            <?php echo escapeHTML($order['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <strong><?php echo formatPrice($order['grand_total']); ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert (1/3 width) -->
    <div>
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Low Stock Alert</h3>
            </div>
            <?php if (empty($lowStockBooks)): ?>
                <div class="admin-card-body">
                    <p class="admin-text-muted">All books are well stocked.</p>
                </div>
            <?php else: ?>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php foreach ($lowStockBooks as $book): ?>
                        <div
                            style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--admin-border-light); display: flex; align-items: center; gap: 1rem;">
                            <?php if ($book['cover_image']): ?>
                                <img src="<?php echo escapeHTML($book['cover_image']); ?>"
                                    alt="<?php echo escapeHTML($book['title']); ?>"
                                    style="width: 2.5rem; height: 3.5rem; object-fit: cover; border-radius: var(--admin-radius-sm); border: 1px solid var(--admin-border-light); flex-shrink: 0;">
                            <?php else: ?>
                                <div
                                    style="width: 2.5rem; height: 3.5rem; background: var(--admin-bg-tertiary); border-radius: var(--admin-radius-sm); flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                                    <iconify-icon icon="solar:book-2-linear" width="20"
                                        style="color: var(--admin-text-muted);"></iconify-icon>
                                </div>
                            <?php endif; ?>
                            <div style="flex: 1; min-width: 0;">
                                <div
                                    style="font-size: var(--admin-font-size-base); font-weight: var(--admin-font-weight-medium); color: var(--admin-text-primary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?php echo escapeHTML($book['title']); ?>
                                </div>
                                <div
                                    style="font-size: var(--admin-font-size-xs); color: <?php echo $book['stock_quantity'] == 0 ? 'var(--admin-danger)' : 'var(--admin-warning)'; ?>; margin-top: 0.25rem;">
                                    <?php echo $book['stock_quantity']; ?> units left
                                </div>
                            </div>
                            <a href="book_form.php?id=<?php echo $book['id']; ?>" class="admin-btn-icon">
                                <iconify-icon icon="solar:pen-linear" width="18"></iconify-icon>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="admin-card-footer admin-text-center">
                    <a href="<?php echo SITE_URL; ?>/admin/books.php" class="admin-text-secondary"
                        style="font-size: var(--admin-font-size-sm); font-weight: var(--admin-font-weight-medium); text-decoration: none;">
                        Manage Inventory
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>