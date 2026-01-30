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

<div class="confirmation-container">
    <div class="confirmation-card">
        <div class="confirmation-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>

        <h1>Order Placed Successfully!</h1>
        <p class="confirmation-message">Thank you for your order. We've received your payment and will process your
            order shortly.</p>

        <div class="confirmation-details">
            <div class="detail-row">
                <span class="detail-label">Order Number:</span>
                <span class="detail-value">
                    <?php echo escapeHTML($confirmation['order_number']); ?>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Transaction ID:</span>
                <span class="detail-value">
                    <?php echo escapeHTML($confirmation['transaction_id']); ?>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Total Amount:</span>
                <span class="detail-value">
                    <?php echo formatPrice($confirmation['total']); ?>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Estimated Delivery:</span>
                <span class="detail-value">
                    <?php echo date('F j, Y', strtotime('+5 days')); ?>
                </span>
            </div>
        </div>

        <p class="confirmation-note">
            A confirmation email has been logged to your account. You can track your order status in your order history.
        </p>

        <div class="confirmation-actions">
            <a href="orders.php" class="btn btn-primary">View Order History</a>
            <a href="catalog.php" class="btn btn-secondary">Continue Shopping</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>