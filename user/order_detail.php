<?php
/**
 * Order Detail Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireUser();

$order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$user_id = getCurrentUserId();
$db = getDB();

// Get order
$stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    setFlashMessage('error', 'Order not found');
    redirect(SITE_URL . '/user/orders.php');
}

// Get order items
$stmt = $db->prepare("
    SELECT oi.*, b.title, b.author, b.cover_image
    FROM order_items oi
    JOIN books b ON oi.book_id = b.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$orderItems = $stmt->fetchAll();

$pageTitle = 'Order Details';
include __DIR__ . '/../includes/header.php';
?>

<a href="orders.php" class="user-back-link">
    <iconify-icon icon="solar:alt-arrow-left-linear" width="16"></iconify-icon>
    Back to Orders
</a>

<div class="user-detail-header">
    <h1>Order #<?php echo escapeHTML($order['order_number']); ?></h1>
    <span class="user-order-status <?php echo strtolower($order['status']); ?>">
        <?php echo escapeHTML($order['status']); ?>
    </span>
</div>

<!-- Order Progress -->
<div class="user-order-progress">
    <div
        class="user-progress-step <?php echo in_array($order['status'], ['Processing', 'Shipped', 'Delivered']) ? 'completed' : 'active'; ?>">
        <div class="user-progress-icon">
            <iconify-icon icon="solar:box-linear" width="20"></iconify-icon>
        </div>
        <span>Processing</span>
    </div>
    <div
        class="user-progress-line <?php echo in_array($order['status'], ['Shipped', 'Delivered']) ? 'completed' : ''; ?>">
    </div>
    <div
        class="user-progress-step <?php echo in_array($order['status'], ['Shipped', 'Delivered']) ? 'completed' : ($order['status'] === 'Processing' ? 'active' : ''); ?>">
        <div class="user-progress-icon">
            <iconify-icon icon="solar:delivery-linear" width="20"></iconify-icon>
        </div>
        <span>Shipped</span>
    </div>
    <div class="user-progress-line <?php echo $order['status'] === 'Delivered' ? 'completed' : ''; ?>"></div>
    <div class="user-progress-step <?php echo $order['status'] === 'Delivered' ? 'completed' : ''; ?>">
        <div class="user-progress-icon">
            <iconify-icon icon="solar:check-circle-linear" width="20"></iconify-icon>
        </div>
        <span>Delivered</span>
    </div>
</div>

<!-- Order Items -->
<section class="user-order-items-section">
    <h2>Order Items</h2>
    <div class="user-order-items-list">
        <?php foreach ($orderItems as $item): ?>
            <div class="user-order-item">
                <div class="user-order-item-image">
                    <?php if ($item['cover_image']): ?>
                        <img src="<?php echo escapeHTML($item['cover_image'] ?: SITE_URL . '/assets/images/placeholder.jpg'); ?>"
                            alt="<?php echo escapeHTML($item['title']); ?>">
                    <?php else: ?>
                        <div class="user-book-placeholder">No Image</div>
                    <?php endif; ?>
                </div>
                <div class="user-order-item-details">
                    <h4><?php echo escapeHTML($item['title']); ?></h4>
                    <p><?php echo escapeHTML($item['author']); ?></p>
                    <p style="font-size: 0.875rem; color: var(--user-zinc-500);">Quantity: <?php echo $item['quantity']; ?>
                    </p>
                </div>
                <div class="user-order-item-price">
                    <?php echo formatPrice($item['price_at_purchase'] * $item['quantity']); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Order Information Grid -->
<div class="user-order-info-grid">
    <div class="user-info-card">
        <h3>Order Information</h3>
        <p><strong>Order Number:</strong> <?php echo escapeHTML($order['order_number']); ?></p>
        <p><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
        <p><strong>Transaction ID:</strong> <?php echo escapeHTML($order['transaction_id']); ?></p>
    </div>

    <div class="user-info-card">
        <h3>Shipping Address</h3>
        <p><?php echo nl2br(escapeHTML($order['shipping_address'])); ?></p>
    </div>

    <div class="user-info-card">
        <h3>Payment Summary</h3>
        <p><strong>Payment Method:</strong> <?php echo escapeHTML($order['payment_method']); ?></p>
        <p><strong>Subtotal:</strong> <?php echo formatPrice($order['total_amount']); ?></p>
        <p><strong>Tax:</strong> <?php echo formatPrice($order['tax_amount']); ?></p>
        <p><strong>Total:</strong> <span
                style="font-size: 1.125rem; font-weight: 600; color: var(--user-zinc-900);"><?php echo formatPrice($order['grand_total']); ?></span>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>