<?php
/**
 * Order Confirmation Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireUser();

if (!isset($_SESSION['order_confirmation'])) {
    redirect(SITE_URL . '/user/orders.php');
}

$confirmation = $_SESSION['order_confirmation'];
unset($_SESSION['order_confirmation']);

$pageTitle = 'Order Confirmation';
include __DIR__ . '/../includes/header.php';
?>

<div class="user-confirmation-container">
    <div class="user-confirmation-content">
        <div class="user-confirmation-icon">
            <iconify-icon icon="solar:check-circle-bold" width="64"
                style="color: var(--user-emerald-500);"></iconify-icon>
        </div>

        <h1 class="user-confirmation-title">Order Placed Successfully!</h1>
        <p class="user-confirmation-message">
            Thank you for your order. We've received your payment and will process your order shortly.
        </p>

        <div class="user-confirmation-details">
            <div class="user-confirmation-row">
                <span>Order Number:</span>
                <strong><?php echo escapeHTML($confirmation['order_number']); ?></strong>
            </div>

            <div class="user-confirmation-row">
                <span>Transaction ID:</span>
                <strong><?php echo escapeHTML($confirmation['transaction_id']); ?></strong>
            </div>

            <div class="user-confirmation-row">
                <span>Total Amount:</span>
                <strong style="font-size: 1.125rem; color: var(--user-zinc-900);">
                    <?php echo formatPrice($confirmation['total']); ?>
                </strong>
            </div>

            <div class="user-confirmation-row">
                <span>Estimated Delivery:</span>
                <strong><?php echo date('F j, Y', strtotime('+5 days')); ?></strong>
            </div>
        </div>

        <p class="user-confirmation-note">
            <iconify-icon icon="solar:info-circle-linear" width="16"></iconify-icon>
            A confirmation email has been logged to your account. You can track your order status in your order history.
        </p>

        <div class="user-confirmation-actions">
            <a href="orders.php" class="user-btn user-btn-primary">
                <iconify-icon icon="solar:box-linear" width="20"></iconify-icon>
                View Order History
            </a>
            <a href="catalog.php" class="user-btn user-btn-secondary">
                Continue Shopping
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>