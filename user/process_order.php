<?php
/**
 * Process Order
 * Handle order submission and payment processing
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/payment_mock.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/user/checkout.php');
}

$errors = [];
$user_id = getCurrentUserId();
$db = getDB();

// Validate CSRF token
if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    setFlashMessage('error', 'Invalid security token');
    redirect(SITE_URL . '/user/checkout.php');
}

// Sanitize inputs
$shipping = [
    'first_name' => sanitizeInput($_POST['first_name'] ?? ''),
    'last_name' => sanitizeInput($_POST['last_name'] ?? ''),
    'address' => sanitizeInput($_POST['address'] ?? ''),
    'city' => sanitizeInput($_POST['city'] ?? ''),
    'state' => sanitizeInput($_POST['state'] ?? ''),
    'zip_code' => sanitizeInput($_POST['zip_code'] ?? ''),
    'country' => sanitizeInput($_POST['country'] ?? ''),
    'phone' => sanitizeInput($_POST['phone'] ?? '')
];

$payment = [
    'card_number' => preg_replace('/\s+/', '', $_POST['card_number'] ?? ''),
    'expiry_month' => sanitizeInput($_POST['expiry_month'] ?? ''),
    'expiry_year' => sanitizeInput($_POST['expiry_year'] ?? ''),
    'cvv' => sanitizeInput($_POST['cvv'] ?? '')
];

// Validate shipping information
foreach ($shipping as $key => $value) {
    if (empty($value)) {
        $errors[] = ucfirst(str_replace('_', ' ', $key)) . ' is required';
    }
}

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

// Validate stock availability
foreach ($cartItems as $item) {
    if ($item['stock_quantity'] < $item['quantity']) {
        $errors[] = "Insufficient stock for: " . $item['title'];
    }
}

if (!empty($errors)) {
    $_SESSION['checkout_errors'] = $errors;
    redirect(SITE_URL . '/user/checkout.php');
}

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = calculateTax($subtotal);
$total = $subtotal + $tax;

// Process payment
$paymentResult = PaymentProcessor::processPayment(
    $payment['card_number'],
    $payment['expiry_month'],
    $payment['expiry_year'],
    $payment['cvv'],
    $total
);

if (!$paymentResult['success']) {
    setFlashMessage('error', $paymentResult['message']);
    redirect(SITE_URL . '/user/checkout.php');
}

// Begin transaction
try {
    $db->beginTransaction();

    // Create order
    $orderNumber = generateOrderNumber();
    $shippingAddress = implode(', ', [
        $shipping['address'],
        $shipping['city'],
        $shipping['state'],
        $shipping['zip_code'],
        $shipping['country']
    ]);

    $cardType = PaymentProcessor::getCardType($payment['card_number']);
    $paymentMethod = $cardType . ' ending in ' . substr($payment['card_number'], -4);

    $stmt = $db->prepare("
        INSERT INTO orders (
            user_id, order_number, total_amount, tax_amount, grand_total,
            status, shipping_address, shipping_city, shipping_state, 
            shipping_zip, shipping_country, payment_method, transaction_id
        ) VALUES (?, ?, ?, ?, ?, 'Processing', ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $user_id,
        $orderNumber,
        $subtotal,
        $tax,
        $total,
        $shippingAddress,
        $shipping['city'],
        $shipping['state'],
        $shipping['zip_code'],
        $shipping['country'],
        $paymentMethod,
        $paymentResult['transaction_id']
    ]);

    $orderId = $db->lastInsertId();

    // Create order items and update stock
    $stmt = $db->prepare("
        INSERT INTO order_items (order_id, book_id, quantity, price_at_purchase)
        VALUES (?, ?, ?, ?)
    ");

    $updateStockStmt = $db->prepare("
        UPDATE books SET stock_quantity = stock_quantity - ? WHERE id = ?
    ");

    foreach ($cartItems as $item) {
        $stmt->execute([
            $orderId,
            $item['book_id'],
            $item['quantity'],
            $item['price']
        ]);

        $updateStockStmt->execute([
            $item['quantity'],
            $item['book_id']
        ]);
    }

    // Clear cart
    $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Log order confirmation (simulated email)
    $logDir = __DIR__ . '/../logs';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $logEntry = sprintf(
        "[%s] Order Confirmation - Order #%s | User: %s | Total: $%.2f | Transaction: %s\n",
        date('Y-m-d H:i:s'),
        $orderNumber,
        $_SESSION['email'],
        $total,
        $paymentResult['transaction_id']
    );

    file_put_contents($logDir . '/order_confirmations.log', $logEntry, FILE_APPEND);

    $db->commit();

    // Redirect to confirmation page
    $_SESSION['order_confirmation'] = [
        'order_number' => $orderNumber,
        'transaction_id' => $paymentResult['transaction_id'],
        'total' => $total
    ];

    redirect(SITE_URL . '/user/order_confirmation.php');

} catch (Exception $e) {
    $db->rollBack();
    error_log("Order processing error: " . $e->getMessage());
    setFlashMessage('error', 'An error occurred while processing your order. Please try again.');
    redirect(SITE_URL . '/user/checkout.php');
}
