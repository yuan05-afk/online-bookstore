<?php
/**
 * Checkout Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireUser();

$user_id = getCurrentUserId();
$db = getDB();

// Get cart items
$stmt = $db->prepare("
    SELECT ci.*, b.title, b.price, b.stock_quantity
    FROM cart_items ci
    JOIN books b ON ci.book_id = b.id
    WHERE ci.user_id = ?
");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll();

if (empty($cartItems)) {
    setFlashMessage('error', 'Your cart is empty');
    redirect(SITE_URL . '/user/cart.php');
}

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = calculateTax($subtotal);
$total = $subtotal + $tax;

// Get user information
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$pageTitle = 'Checkout';
$csrf_token = generateCSRFToken();
include __DIR__ . '/../includes/header.php';
?>

<div class="checkout-container">
    <h1>Checkout</h1>

    <form method="POST" action="process_order.php" class="checkout-form">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <div class="checkout-sections">
            <div class="checkout-main">
                <!-- Shipping Information -->
                <section class="checkout-section">
                    <h2>Shipping Information</h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name"
                                value="<?php echo escapeHTML($user['first_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name"
                                value="<?php echo escapeHTML($user['last_name']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Street Address *</label>
                        <input type="text" id="address" name="address"
                            value="<?php echo escapeHTML($user['address'] ?? ''); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City *</label>
                            <input type="text" id="city" name="city"
                                value="<?php echo escapeHTML($user['city'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="state">State *</label>
                            <input type="text" id="state" name="state"
                                value="<?php echo escapeHTML($user['state'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="zip_code">ZIP Code *</label>
                            <input type="text" id="zip_code" name="zip_code"
                                value="<?php echo escapeHTML($user['zip_code'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="country">Country *</label>
                            <input type="text" id="country" name="country"
                                value="<?php echo escapeHTML($user['country'] ?? 'USA'); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone"
                            value="<?php echo escapeHTML($user['phone'] ?? ''); ?>" required>
                    </div>
                </section>

                <!-- Payment Information -->
                <section class="checkout-section">
                    <h2>Payment Information</h2>
                    <p class="payment-note">This is a mock payment system. No actual charges will be made.</p>

                    <div class="form-group">
                        <label for="card_number">Card Number *</label>
                        <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456"
                            maxlength="19" required>
                        <small>Test card: 4532015112830366 (Visa)</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiry_month">Expiry Month *</label>
                            <select id="expiry_month" name="expiry_month" required>
                                <option value="">Month</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo sprintf('%02d', $i); ?>">
                                        <?php echo sprintf('%02d', $i); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="expiry_year">Expiry Year *</label>
                            <select id="expiry_year" name="expiry_year" required>
                                <option value="">Year</option>
                                <?php for ($i = 0; $i < 10; $i++): ?>
                                    <?php $year = date('Y') + $i; ?>
                                    <option value="<?php echo $year; ?>">
                                        <?php echo $year; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cvv">CVV *</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4" required>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Order Summary Sidebar -->
            <aside class="checkout-sidebar">
                <div class="order-summary">
                    <h2>Order Summary</h2>

                    <div class="summary-items">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="summary-item">
                                <span>
                                    <?php echo escapeHTML($item['title']); ?> ×
                                    <?php echo $item['quantity']; ?>
                                </span>
                                <span>
                                    <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-totals">
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
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        Place Order
                    </button>
                </div>
            </aside>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>