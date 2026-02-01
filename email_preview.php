<?php
/**
 * Email Template Preview
 * View how the order confirmation email will look
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/email.php';

// Sample order data for preview
$sampleOrder = [
    'id' => 1,
    'order_number' => 'ORD-20260201-001',
    'email' => 'customer@example.com',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'total_amount' => 45.98,
    'tax_amount' => 3.91,
    'grand_total' => 49.89,
    'payment_method' => 'Visa ending in 1234',
    'transaction_id' => 'TXN-ABC123XYZ',
    'shipping_address' => "123 Main Street\nApt 4B\nNew York, NY 10001",
    'created_at' => date('Y-m-d H:i:s')
];

$sampleItems = [
    [
        'title' => 'The Great Gatsby',
        'author' => 'F. Scott Fitzgerald',
        'quantity' => 2,
        'price_at_purchase' => 12.99
    ],
    [
        'title' => 'To Kill a Mockingbird',
        'author' => 'Harper Lee',
        'quantity' => 1,
        'price_at_purchase' => 20.00
    ]
];

// Generate HTML
$html = generateOrderConfirmationHTML($sampleOrder, $sampleItems);

// Output the HTML
echo $html;
