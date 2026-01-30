<?php
/**
 * Shopping Cart Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireUser();

$user_id = getCurrentUserId();
$db = getDB();

// Get cart items
$stmt = $db->prepare("
    SELECT ci.*, b.title, b.author, b.price, b.cover_image, b.stock_quantity
    FROM cart_items ci
    JOIN books b ON ci.book_id = b.id
    WHERE ci.user_id = ?
    ORDER BY ci.created_at DESC
");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll();

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = calculateTax($subtotal);
$total = $subtotal + $tax;

$pageTitle = 'Shopping Cart';
include __DIR__ . '/../includes/header.php';
?>

<h1 class="user-page-title">Shopping Cart</h1>

<?php if (empty($cartItems)): ?>
    <div class="user-cart-empty">
        <iconify-icon icon="solar:cart-large-linear" width="64" style="color: var(--user-zinc-300);"></iconify-icon>
        <p>Your cart is empty</p>
        <a href="catalog.php" class="user-btn user-btn-primary">Continue Shopping</a>
    </div>
<?php else: ?>
    <div class="user-cart-grid">
        <div class="user-cart-items">
            <?php foreach ($cartItems as $item): ?>
                <div class="user-cart-item" data-cart-item-id="<?php echo $item['id']; ?>">
                    <div class="user-cart-thumbnail">
                        <?php if ($item['cover_image']): ?>
                            <img src="<?php echo escapeHTML($item['cover_image'] ?: SITE_URL . '/assets/images/placeholder.jpg'); ?>"
                                alt="<?php echo escapeHTML($item['title']); ?>">
                        <?php else: ?>
                            <div class="user-book-placeholder">No Image</div>
                        <?php endif; ?>
                    </div>

                    <div class="user-cart-details">
                        <div class="user-cart-item-header">
                            <div>
                                <h3 class="user-cart-item-title">
                                    <a href="book_detail.php?id=<?php echo $item['book_id']; ?>">
                                        <?php echo escapeHTML($item['title']); ?>
                                    </a>
                                </h3>
                                <p class="user-cart-item-author">
                                    <?php echo escapeHTML($item['author']); ?>
                                </p>
                            </div>
                            <button class="user-cart-remove-btn" data-cart-item-id="<?php echo $item['id']; ?>" title="Remove">
                                <iconify-icon icon="solar:trash-bin-trash-linear" width="20"></iconify-icon>
                            </button>
                        </div>

                        <div class="user-cart-item-footer">
                            <div class="user-cart-quantity">
                                <button onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">
                                    <iconify-icon icon="solar:minus-circle-linear" width="18"></iconify-icon>
                                </button>
                                <span><?php echo $item['quantity']; ?></span>
                                <button onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">
                                    <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                                </button>
                            </div>
                            <span class="user-cart-item-price">
                                <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="user-cart-summary">
            <h2 class="user-cart-summary-title">Order Summary</h2>

            <div class="user-summary-rows">
                <div class="user-summary-row">
                    <span>Subtotal:</span>
                    <span><?php echo formatPrice($subtotal); ?></span>
                </div>

                <div class="user-summary-row">
                    <span>Tax (<?php echo (TAX_RATE * 100); ?>%):</span>
                    <span><?php echo formatPrice($tax); ?></span>
                </div>

                <div class="user-summary-row user-summary-total">
                    <span>Total:</span>
                    <span><?php echo formatPrice($total); ?></span>
                </div>
            </div>

            <a href="checkout.php" class="user-btn user-btn-primary user-btn-block">
                <iconify-icon icon="solar:card-linear" width="20"></iconify-icon>
                Proceed to Checkout
            </a>
            <a href="catalog.php" class="user-btn user-btn-secondary user-btn-block">
                Continue Shopping
            </a>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>