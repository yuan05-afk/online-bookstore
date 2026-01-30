<?php
/**
 * Admin Order Management
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireAdmin();

$db = getDB();
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Build query
$where = [];
$params = [];

if ($status_filter) {
    $where[] = "o.status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $where[] = "(o.order_number LIKE ? OR u.email LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get orders
$sql = "
    SELECT o.*, u.email, u.first_name, u.last_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    $whereClause
    ORDER BY o.created_at DESC
";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$pageTitle = 'Manage Orders';
$flash = getFlashMessage();
include __DIR__ . '/../includes/admin_header.php';
?>

<div class="admin-content">
    <h1>Manage Orders</h1>

    <?php if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?>">
            <?php echo escapeHTML($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="filters">
        <form method="GET" action="" class="filter-form">
            <input type="text" name="search" placeholder="Search by order #, customer..."
                value="<?php echo escapeHTML($search); ?>">

            <select name="status">
                <option value="">All Statuses</option>
                <option value="Processing" <?php echo $status_filter === 'Processing' ? 'selected' : ''; ?>>Processing
                </option>
                <option value="Shipped" <?php echo $status_filter === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                <option value="Delivered" <?php echo $status_filter === 'Delivered' ? 'selected' : ''; ?>>Delivered
                </option>
                <option value="Cancelled" <?php echo $status_filter === 'Cancelled' ? 'selected' : ''; ?>>Cancelled
                </option>
            </select>

            <button type="submit" class="btn btn-secondary">Filter</button>
            <?php if ($search || $status_filter): ?>
                <a href="orders.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>

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
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="6" class="text-center">No orders found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <?php echo escapeHTML($order['order_number']); ?>
                        </td>
                        <td>
                            <?php echo escapeHTML($order['first_name'] . ' ' . $order['last_name']); ?><br>
                            <small>
                                <?php echo escapeHTML($order['email']); ?>
                            </small>
                        </td>
                        <td>
                            <?php echo formatPrice($order['grand_total']); ?>
                        </td>
                        <td>
                            <span class="status status-<?php echo strtolower($order['status']); ?>">
                                <?php echo escapeHTML($order['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                        </td>
                        <td class="action-buttons">
                            <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-secondary">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>