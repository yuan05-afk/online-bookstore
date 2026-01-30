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
    SELECT oi.*, b.title, b.author, b.isbn
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

<div class="admin-content">
    <div class="content-header">
        <h1>Order Details</h1>
        <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?>">
            <?php echo escapeHTML($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="order-detail-grid">
        <div class="info-card">
            <h3>Order Information</h3>
            <p><strong>Order Number:</strong>
                <?php echo escapeHTML($order['order_number']); ?>
            </p>
            <p><strong>Order Date:</strong>
                <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?>
            </p>
            <p><strong>Status:</strong> <span class="status status-<?php echo strtolower($order['status']); ?>">
                    <?php echo escapeHTML($order['status']); ?>
                </span></p>
            <p><strong>Transaction ID:</strong>
                <?php echo escapeHTML($order['transaction_id']); ?>
            </p>
        </div>

        <div class="info-card">
            <h3>Customer Information</h3>
            <p><strong>Name:</strong>
                <?php echo escapeHTML($order['first_name'] . ' ' . $order['last_name']); ?>
            </p>
            <p><strong>Email:</strong>
                <?php echo escapeHTML($order['email']); ?>
            </p>
            <p><strong>Phone:</strong>
                <?php echo escapeHTML($order['phone']); ?>
            </p>
        </div>

        <div class="info-card">
            <h3>Shipping Address</h3>
            <p>
                <?php echo nl2br(escapeHTML($order['shipping_address'])); ?>
            </p>
        </div>

        <div class="info-card">
            <h3>Payment Information</h3>
            <p><strong>Method:</strong>
                <?php echo escapeHTML($order['payment_method']); ?>
            </p>
            <p><strong>Subtotal:</strong>
                <?php echo formatPrice($order['total_amount']); ?>
            </p>
            <p><strong>Tax:</strong>
                <?php echo formatPrice($order['tax_amount']); ?>
            </p>
            <p><strong>Total:</strong>
                <?php echo formatPrice($order['grand_total']); ?>
            </p>
        </div>
    </div>

    <div class="order-items-section">
        <h2>Order Items</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ISBN</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td>
                            <?php echo escapeHTML($item['isbn']); ?>
                        </td>
                        <td>
                            <?php echo escapeHTML($item['title']); ?>
                        </td>
                        <td>
                            <?php echo escapeHTML($item['author']); ?>
                        </td>
                        <td>
                            <?php echo $item['quantity']; ?>
                        </td>
                        <td>
                            <?php echo formatPrice($item['price_at_purchase']); ?>
                        </td>
                        <td>
                            <?php echo formatPrice($item['price_at_purchase'] * $item['quantity']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="update-status-section">
        <h2>Update Order Status</h2>
        <form method="POST" action="" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="Processing" <?php echo $order['status'] === 'Processing' ? 'selected' : ''; ?>
                        >Processing</option>
                    <option value="Shipped" <?php echo $order['status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped
                    </option>
                    <option value="Delivered" <?php echo $order['status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered
                    </option>
                    <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="notes">Internal Notes</label>
                <textarea id="notes" name="notes" rows="3"><?php echo escapeHTML($order['notes'] ?? ''); ?></textarea>
            </div>

            <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>