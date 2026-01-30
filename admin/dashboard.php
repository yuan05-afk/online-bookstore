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

<div class="admin-dashboard">
    <h1>Dashboard</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-info">
                <h3>Total Books</h3>
                <p class="stat-value">
                    <?php echo number_format($totalBooks); ?>
                </p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-info">
                <h3>Total Orders</h3>
                <p class="stat-value">
                    <?php echo number_format($totalOrders); ?>
                </p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3>Total Users</h3>
                <p class="stat-value">
                    <?php echo number_format($totalUsers); ?>
                </p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <h3>Total Revenue</h3>
                <p class="stat-value">
                    <?php echo formatPrice($totalRevenue); ?>
                </p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-section">
            <h2>Recent Orders</h2>
            <?php if (empty($recentOrders)): ?>
                <p>No orders yet.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td>
                                    <?php echo escapeHTML($order['order_number']); ?>
                                </td>
                                <td>
                                    <?php echo escapeHTML($order['first_name'] . ' ' . $order['last_name']); ?>
                                </td>
                                <td>
                                    <?php echo formatPrice($order['grand_total']); ?>
                                </td>
                                <td><span class="status status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo escapeHTML($order['status']); ?>
                                    </span></td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                </td>
                                <td>
                                    <a href="order_detail.php?id=<?php echo $order['id']; ?>"
                                        class="btn btn-sm btn-secondary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="dashboard-section">
            <h2>Low Stock Alert</h2>
            <?php if (empty($lowStockBooks)): ?>
                <p>All books are well stocked.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowStockBooks as $book): ?>
                            <tr>
                                <td>
                                    <?php echo escapeHTML($book['title']); ?>
                                </td>
                                <td class="<?php echo $book['stock_quantity'] == 0 ? 'text-danger' : 'text-warning'; ?>">
                                    <?php echo $book['stock_quantity']; ?>
                                </td>
                                <td>
                                    <a href="book_form.php?id=<?php echo $book['id']; ?>"
                                        class="btn btn-sm btn-secondary">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>