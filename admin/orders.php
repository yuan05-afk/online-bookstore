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

<h1 class="admin-page-title">Order Management</h1>

<?php if ($flash): ?>
    <div class="admin-card admin-mb-6"
        style="border-left: 4px solid <?php echo $flash['type'] === 'success' ? 'var(--admin-success)' : 'var(--admin-danger)'; ?>;">
        <div class="admin-card-body">
            <?php echo escapeHTML($flash['message']); ?>
        </div>
    </div>
<?php endif; ?>

<!-- Filter Bar -->
<form method="GET" action="" class="admin-filter-bar">
    <div class="admin-input-icon">
        <iconify-icon icon="solar:magnifer-linear" width="18"></iconify-icon>
        <input type="text" name="search" class="admin-input" placeholder="Search by order #, customer..."
            value="<?php echo escapeHTML($search); ?>">
    </div>

    <select name="status" class="admin-select">
        <option value="">All Statuses</option>
        <option value="Processing" <?php echo $status_filter === 'Processing' ? 'selected' : ''; ?>>Processing</option>
        <option value="Shipped" <?php echo $status_filter === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
        <option value="Delivered" <?php echo $status_filter === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
        <option value="Cancelled" <?php echo $status_filter === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
    </select>

    <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
    <?php if ($search || $status_filter): ?>
        <a href="orders.php" class="admin-btn admin-btn-secondary">Clear</a>
    <?php endif; ?>
</form>

<!-- Orders Table -->
<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="6" class="admin-text-center admin-text-muted" style="padding: 3rem;">
                            No orders found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <strong><?php echo escapeHTML($order['order_number']); ?></strong>
                            </td>
                            <td>
                                <div style="font-weight: var(--admin-font-weight-medium); color: var(--admin-text-primary);">
                                    <?php echo escapeHTML($order['first_name'] . ' ' . $order['last_name']); ?>
                                </div>
                                <div
                                    style="font-size: var(--admin-font-size-xs); color: var(--admin-text-secondary); margin-top: 0.25rem;">
                                    <?php echo escapeHTML($order['email']); ?>
                                </div>
                            </td>
                            <td>
                                <?php
                                $statusClass = 'neutral';
                                $status = strtolower($order['status']);
                                if ($status === 'delivered')
                                    $statusClass = 'success';
                                elseif ($status === 'processing' || $status === 'shipped')
                                    $statusClass = 'warning';
                                elseif ($status === 'cancelled')
                                    $statusClass = 'danger';
                                ?>
                                <span class="admin-badge admin-badge-<?php echo $statusClass; ?>">
                                    <?php echo escapeHTML($order['status']); ?>
                                </span>
                            </td>
                            <td class="admin-text-secondary">
                                <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                <div style="font-size: var(--admin-font-size-xs); margin-top: 0.25rem;">
                                    <?php echo date('g:i A', strtotime($order['created_at'])); ?>
                                </div>
                            </td>
                            <td class="text-right">
                                <strong><?php echo formatPrice($order['grand_total']); ?></strong>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="order_detail.php?id=<?php echo $order['id']; ?>"
                                        class="admin-btn admin-btn-sm admin-btn-secondary">
                                        View Details
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>