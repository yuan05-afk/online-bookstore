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

<div class="order-detail-container">
    <h1>Order Details</h1>

    <div class="order-detail-header">
        <div class="order-info-grid">
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
    </div>

    <div class="order-items-section">
        <h2>Order Items</h2>
        <div class="order-items-list">
            <?php foreach ($orderItems as $item): ?>
                <div class="order-item">
                    <div class="item-image">
                        <?php if ($item['cover_image']): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/images/books/<?php echo escapeHTML($item['cover_image']); ?>"
                                alt="<?php echo escapeHTML($item['title']); ?>">
                        <?php else: ?>
                            <div class="book-placeholder-small">No Image</div>
                        <?php endif; ?>
                    </div>

                    <div class="item-details">
                        <h4>
                            <?php echo escapeHTML($item['title']); ?>
                        </h4>
                        <p class="item-author">
                            <?php echo escapeHTML($item['author']); ?>
                        </p>
                        <p class="item-quantity">Quantity:
                            <?php echo $item['quantity']; ?>
                        </p>
                    </div>

                    <div class="item-price">
                        <?php echo formatPrice($item['price_at_purchase'] * $item['quantity']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="order-tracking">
        <h2>Order Status Timeline</h2>
        <div class="timeline">
            <div
                class="timeline-item <?php echo in_array($order['status'], ['Processing', 'Shipped', 'Delivered']) ? 'completed' : ''; ?>">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <h4>Processing</h4>
                    <p>Your order is being prepared</p>
                </div>
            </div>

            <div
                class="timeline-item <?php echo in_array($order['status'], ['Shipped', 'Delivered']) ? 'completed' : ''; ?>">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <h4>Shipped</h4>
                    <p>Your order has been shipped</p>
                </div>
            </div>

            <div class="timeline-item <?php echo $order['status'] === 'Delivered' ? 'completed' : ''; ?>">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <h4>Delivered</h4>
                    <p>Your order has been delivered</p>
                </div>
            </div>
        </div>
    </div>

    <div class="order-actions">
        <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>