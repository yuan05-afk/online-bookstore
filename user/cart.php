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

<div class="cart-container">
    <h1>Shopping Cart</h1>

    <?php if (empty($cartItems)): ?>
        <div class="empty-cart">
            <p>Your cart is empty</p>
            <a href="catalog.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-content">
            <div class="cart-items">
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item" data-cart-item-id="<?php echo $item['id']; ?>">
                        <div class="cart-item-image">
                            <?php if ($item['cover_image']): ?>
                                <img src="<?php echo SITE_URL; ?>/assets/images/books/<?php echo escapeHTML($item['cover_image']); ?>"
                                    alt="<?php echo escapeHTML($item['title']); ?>">
                            <?php else: ?>
                                <div class="book-placeholder-small">No Image</div>
                            <?php endif; ?>
                        </div>

                        <div class="cart-item-details">
                            <h3>
                                <a href="book_detail.php?id=<?php echo $item['book_id']; ?>">
                                    <?php echo escapeHTML($item['title']); ?>
                                </a>
                            </h3>
                            <p class="cart-item-author">
                                <?php echo escapeHTML($item['author']); ?>
                            </p>
                            <p class="cart-item-price">
                                <?php echo formatPrice($item['price']); ?>
                            </p>
                        </div>

                        <div class="cart-item-quantity">
                            <label>Quantity:</label>
                            <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1"
                                max="<?php echo $item['stock_quantity']; ?>" data-cart-item-id="<?php echo $item['id']; ?>">
                        </div>

                        <div class="cart-item-subtotal">
                            <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                        </div>

                        <div class="cart-item-remove">
                            <button class="btn-remove" data-cart-item-id="<?php echo $item['id']; ?>">
                                Remove
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h2>Order Summary</h2>

                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>
                        <?php echo formatPrice($subtotal); ?>
                    </span>
                </div>

                <div class="summary-row">
                    <span>Tax (
                        <?php echo (TAX_RATE * 100); ?>%):
                    </span>
                    <span>
                        <?php echo formatPrice($tax); ?>
                    </span>
                </div>

                <div class="summary-row summary-total">
                    <span>Total:</span>
                    <span>
                        <?php echo formatPrice($total); ?>
                    </span>
                </div>

                <a href="checkout.php" class="btn btn-primary btn-block">Proceed to Checkout</a>
                <a href="catalog.php" class="btn btn-secondary btn-block">Continue Shopping</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>