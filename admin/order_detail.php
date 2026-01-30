<?php
/**
 * Admin Order Detail Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireAdmin();

$order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$db = getDB();

// Get order
$stmt = $db->prepare("
    SELECT o.*, u.email, u.first_name, u.last_name, u.phone
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    setFlashMessage('error', 'Order not found');
    redirect(SITE_URL . '/admin/orders.php');
}

// Get order items
$stmt = $db->prepare("
    SELECT oi.*, b.title, b.author, b.isbn, b.cover_image
    FROM order_items oi
    JOIN books b ON oi.book_id = b.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$orderItems = $stmt->fetchAll();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $new_status = sanitizeInput($_POST['status']);
        $notes = sanitizeInput($_POST['notes'] ?? '');

        $stmt = $db->prepare("UPDATE orders SET status = ?, notes = ? WHERE id = ?");
        $stmt->execute([$new_status, $notes, $order_id]);

        setFlashMessage('success', 'Order status updated successfully');
        redirect(SITE_URL . '/admin/order_detail.php?id=' . $order_id);
    }
}

$pageTitle = 'Order Details';
$csrf_token = generateCSRFToken();
$flash = getFlashMessage();
include __DIR__ . '/../includes/admin_header.php';
?>

<a href="orders.php" class="admin-back-link">
    <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
    Back to Orders
</a>

<div class="admin-flex-between admin-mb-6">
    <h1 class="admin-page-title" style="margin: 0;">Order #<?php echo escapeHTML($order['order_number']); ?></h1>
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
    <span class="admin-badge admin-badge-<?php echo $statusClass; ?>"
        style="font-size: var(--admin-font-size-base); padding: var(--admin-space-2) var(--admin-space-4);">
        <?php echo escapeHTML($order['status']); ?>
    </span>
</div>

<?php if ($flash): ?>
    <div class="admin-card admin-mb-6"
        style="border-left: 4px solid <?php echo $flash['type'] === 'success' ? 'var(--admin-success)' : 'var(--admin-danger)'; ?>;">
        <div class="admin-card-body">
            <?php echo escapeHTML($flash['message']); ?>
        </div>
    </div>
<?php endif; ?>

<!-- Order Info Grid -->
<div class="admin-grid admin-grid-cols-2 admin-mb-6">
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Order Information</h3>
        </div>
        <div class="admin-card-body">
            <div style="margin-bottom: 1rem;">
                <div class="admin-label" style="margin-bottom: 0.25rem;">Order Date</div>
                <div><?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></div>
            </div>
            <div style="margin-bottom: 1rem;">
                <div class="admin-label" style="margin-bottom: 0.25rem;">Transaction ID</div>
                <div style="font-family: monospace; font-size: var(--admin-font-size-sm);">
                    <?php echo escapeHTML($order['transaction_id']); ?>
                </div>
            </div>
            <div>
                <div class="admin-label" style="margin-bottom: 0.25rem;">Payment Method</div>
                <div><?php echo escapeHTML($order['payment_method']); ?></div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Customer Information</h3>
        </div>
        <div class="admin-card-body">
            <div style="margin-bottom: 1rem;">
                <div class="admin-label" style="margin-bottom: 0.25rem;">Name</div>
                <div><?php echo escapeHTML($order['first_name'] . ' ' . $order['last_name']); ?></div>
            </div>
            <div style="margin-bottom: 1rem;">
                <div class="admin-label" style="margin-bottom: 0.25rem;">Email</div>
                <div><?php echo escapeHTML($order['email']); ?></div>
            </div>
            <div>
                <div class="admin-label" style="margin-bottom: 0.25rem;">Phone</div>
                <div><?php echo escapeHTML($order['phone']); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="admin-grid admin-grid-cols-3 admin-mb-6">
    <div class="admin-card" style="grid-column: span 2;">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Shipping Address</h3>
        </div>
        <div class="admin-card-body">
            <?php echo nl2br(escapeHTML($order['shipping_address'])); ?>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Order Total</h3>
        </div>
        <div class="admin-card-body">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span class="admin-text-secondary">Subtotal:</span>
                <span><?php echo formatPrice($order['total_amount']); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span class="admin-text-secondary">Tax:</span>
                <span><?php echo formatPrice($order['tax_amount']); ?></span>
            </div>
            <div
                style="border-top: 2px solid var(--admin-border-light); padding-top: 0.5rem; margin-top: 0.5rem; display: flex; justify-content: space-between;">
                <strong>Total:</strong>
                <strong style="font-size: var(--admin-font-size-xl); color: var(--admin-primary);">
                    <?php echo formatPrice($order['grand_total']); ?>
                </strong>
            </div>
        </div>
    </div>
</div>

<!-- Order Items -->
<div class="admin-card admin-mb-6">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Order Items</h3>
    </div>
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Cover</th>
                    <th>Book Details</th>
                    <th>ISBN</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td>
                            <?php if ($item['cover_image']): ?>
                                <img src="<?php echo escapeHTML($item['cover_image']); ?>"
                                    alt="<?php echo escapeHTML($item['title']); ?>"
                                    style="width: 2.5rem; height: 3.5rem; object-fit: cover; border-radius: var(--admin-radius-sm); border: 1px solid var(--admin-border-light);">
                            <?php else: ?>
                                <div
                                    style="width: 2.5rem; height: 3.5rem; background: var(--admin-bg-tertiary); border-radius: var(--admin-radius-sm); display: flex; align-items: center; justify-content: center;">
                                    <iconify-icon icon="solar:book-2-linear" width="20"
                                        style="color: var(--admin-text-muted);"></iconify-icon>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-weight: var(--admin-font-weight-medium); color: var(--admin-text-primary);">
                                <?php echo escapeHTML($item['title']); ?>
                            </div>
                            <div
                                style="font-size: var(--admin-font-size-xs); color: var(--admin-text-secondary); margin-top: 0.25rem;">
                                by <?php echo escapeHTML($item['author']); ?>
                            </div>
                        </td>
                        <td class="admin-text-secondary"
                            style="font-family: monospace; font-size: var(--admin-font-size-sm);">
                            <?php echo escapeHTML($item['isbn']); ?>
                        </td>
                        <td class="text-right">
                            <?php echo formatPrice($item['price_at_purchase']); ?>
                        </td>
                        <td class="text-right">
                            <span class="admin-badge admin-badge-neutral">
                                ×<?php echo $item['quantity']; ?>
                            </span>
                        </td>
                        <td class="text-right">
                            <strong><?php echo formatPrice($item['price_at_purchase'] * $item['quantity']); ?></strong>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Update Status -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Update Order Status</h3>
    </div>
    <div class="admin-card-body">
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="admin-grid admin-grid-cols-2">
                <div class="admin-form-group">
                    <label for="status" class="admin-label">Status</label>
                    <select id="status" name="status" class="admin-select" required>
                        <option value="Processing" <?php echo $order['status'] === 'Processing' ? 'selected' : ''; ?>>
                            Processing</option>
                        <option value="Shipped" <?php echo $order['status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped
                        </option>
                        <option value="Delivered" <?php echo $order['status'] === 'Delivered' ? 'selected' : ''; ?>>
                            Delivered</option>
                        <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>
                            Cancelled</option>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label for="notes" class="admin-label">Internal Notes</label>
                    <textarea id="notes" name="notes" class="admin-textarea"
                        rows="3"><?php echo escapeHTML($order['notes'] ?? ''); ?></textarea>
                </div>
            </div>

            <button type="submit" name="update_status" class="admin-btn admin-btn-primary">
                <iconify-icon icon="solar:check-circle-linear" width="18"></iconify-icon>
                Update Status
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>