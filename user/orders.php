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

<h1 class="user-page-title">Order History</h1>

<?php if (empty($orders)): ?>
    <div class="user-orders-empty">
        <iconify-icon icon="solar:box-linear" width="64" style="color: var(--user-zinc-300);"></iconify-icon>
        <p>You haven't placed any orders yet.</p>
        <a href="catalog.php" class="user-btn user-btn-primary">Start Shopping</a>
    </div>
<?php else: ?>
    <div class="user-orders-list">
        <?php foreach ($orders as $order): ?>
            <div class="user-order-card" onclick="window.location.href='order_detail.php?id=<?php echo $order['id']; ?>'">
                <div class="user-order-header">
                    <div class="user-order-icon">
                        <iconify-icon icon="solar:box-linear" width="24"></iconify-icon>
                    </div>
                    <div class="user-order-info">
                        <h3>Order #<?php echo escapeHTML($order['order_number']); ?></h3>
                        <p><?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                    </div>
                </div>

                <div class="user-order-body">
                    <div class="user-order-meta">
                        <span class="user-order-status <?php echo strtolower($order['status']); ?>">
                            <?php echo escapeHTML($order['status']); ?>
                        </span>
                        <span class="user-order-total">
                            <?php echo formatPrice($order['grand_total']); ?>
                        </span>
                    </div>
                    <iconify-icon icon="solar:alt-arrow-right-linear" class="user-order-arrow" width="20"></iconify-icon>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>