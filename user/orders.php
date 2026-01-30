<?php
/**
 * Order History Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireUser();

$user_id = getCurrentUserId();
$db = getDB();

// Get all orders for user
$stmt = $db->prepare("
    SELECT * FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

$pageTitle = 'Order History';
include __DIR__ . '/../includes/header.php';
?>

<div class="orders-container">
    <h1>Order History</h1>

    <?php if (empty($orders)): ?>
        <div class="no-orders">
            <p>You haven't placed any orders yet.</p>
            <a href="catalog.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-number">
                            <strong>Order #
                                <?php echo escapeHTML($order['order_number']); ?>
                            </strong>
                        </div>
                        <div class="order-date">
                            <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                        </div>
                    </div>

                    <div class="order-body">
                        <div class="order-info">
                            <div class="info-item">
                                <span class="label">Status:</span>
                                <span class="status status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo escapeHTML($order['status']); ?>
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="label">Total:</span>
                                <span class="value">
                                    <?php echo formatPrice($order['grand_total']); ?>
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="label">Payment:</span>
                                <span class="value">
                                    <?php echo escapeHTML($order['payment_method']); ?>
                                </span>
                            </div>
                        </div>

                        <div class="order-actions">
                            <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn btn-secondary btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>