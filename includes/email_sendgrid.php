<?php
/**
 * Email Helper Functions - SendGrid Implementation
 * Handles email sending using SendGrid API
 */

require_once BASE_PATH . '/vendor/autoload.php';

/**
 * Send email via SendGrid API
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $htmlBody HTML email body
 * @param string $textBody Plain text email body (fallback)
 * @return array ['success' => bool, 'message' => string]
 */
function sendEmail($to, $subject, $htmlBody, $textBody = '')
{
    try {
        // Get SendGrid API key from environment
        $apiKey = getenv('SENDGRID_API_KEY');
        $fromEmail = getenv('SENDGRID_FROM_EMAIL') ?: 'noreply@nightowlbooks.com';
        $fromName = getenv('SENDGRID_FROM_NAME') ?: 'Night Owl Books';

        if (!$apiKey) {
            throw new Exception('SendGrid API key not configured');
        }

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($fromEmail, $fromName);
        $email->setSubject($subject);
        $email->addTo($to);
        $email->addContent("text/plain", $textBody ?: strip_tags($htmlBody));
        $email->addContent("text/html", $htmlBody);

        $sendgrid = new \SendGrid($apiKey);
        $response = $sendgrid->send($email);

        if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
            return [
                'success' => true,
                'message' => 'Email sent successfully',
                'message_id' => $response->headers()['X-Message-Id'] ?? null
            ];
        } else {
            throw new Exception('SendGrid returned status ' . $response->statusCode());
        }
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return [
            'success' => false,
            'message' => "Email could not be sent. Error: " . $e->getMessage()
        ];
    }
}

/**
 * Send order confirmation email
 * 
 * @param int $orderId Order ID
 * @return array ['success' => bool, 'message' => string]
 */
function sendOrderConfirmationEmail($orderId)
{
    $db = getDB();

    // Get order details
    $stmt = $db->prepare("
        SELECT o.*, u.email, u.first_name, u.last_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if (!$order) {
        return [
            'success' => false,
            'message' => 'Order not found'
        ];
    }

    // Get order items
    $stmt = $db->prepare("
        SELECT oi.*, b.title, b.author
        FROM order_items oi
        JOIN books b ON oi.book_id = b.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll();

    // Generate email content
    $htmlBody = generateOrderConfirmationHTML($order, $items);
    $textBody = generateOrderConfirmationText($order, $items);

    $subject = "Order Confirmation - Order #{$order['order_number']}";

    return sendEmail($order['email'], $subject, $htmlBody, $textBody);
}

/**
 * Generate HTML email template for order confirmation
 */
function generateOrderConfirmationHTML($order, $items)
{
    $logoUrl = SITE_URL . '/assets/images/logo-long.png';

    $itemsHtml = '';
    foreach ($items as $item) {
        $itemTotal = $item['price_at_purchase'] * $item['quantity'];
        $itemsHtml .= "
            <tr>
                <td style='padding: 12px; border-bottom: 1px solid #e5e7eb;'>{$item['title']}</td>
                <td style='padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;'>{$item['quantity']}</td>
                <td style='padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: right;'>" . formatPrice($item['price_at_purchase']) . "</td>
                <td style='padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: right;'>" . formatPrice($itemTotal) . "</td>
            </tr>
        ";
    }

    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Order Confirmation</title>
    </head>
    <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f9fafb;'>
        <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f9fafb; padding: 40px 20px;'>
            <tr>
                <td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                        <!-- Header -->
                        <tr>
                            <td style='padding: 40px 40px 20px; text-align: center; border-bottom: 2px solid #f3f4f6;'>
                                <img src='{$logoUrl}' alt='Night Owl Books' style='height: 60px; margin-bottom: 20px;'>
                                <h1 style='margin: 0; color: #111827; font-size: 24px; font-weight: 600;'>Order Confirmation</h1>
                            </td>
                        </tr>
                        
                        <!-- Greeting -->
                        <tr>
                            <td style='padding: 30px 40px 20px;'>
                                <p style='margin: 0 0 15px; color: #374151; font-size: 16px; line-height: 1.6;'>
                                    Hi {$order['first_name']},
                                </p>
                                <p style='margin: 0 0 15px; color: #374151; font-size: 16px; line-height: 1.6;'>
                                    Thank you for your order! We're excited to confirm that we've received your purchase from <strong>Night Owl Books</strong>.
                                </p>
                                <p style='margin: 0; color: #374151; font-size: 16px; line-height: 1.6;'>
                                    This email serves as your official order confirmation and receipt.
                                </p>
                            </td>
                        </tr>
                        
                        <!-- Order Info -->
                        <tr>
                            <td style='padding: 20px 40px;'>
                                <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f9fafb; border-radius: 6px; padding: 20px;'>
                                    <tr>
                                        <td style='padding: 5px 0;'>
                                            <strong style='color: #111827;'>Order Number:</strong>
                                            <span style='color: #374151;'>{$order['order_number']}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='padding: 5px 0;'>
                                            <strong style='color: #111827;'>Order Date:</strong>
                                            <span style='color: #374151;'>" . date('F j, Y', strtotime($order['created_at'])) . "</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='padding: 5px 0;'>
                                            <strong style='color: #111827;'>Transaction ID:</strong>
                                            <span style='color: #374151;'>{$order['transaction_id']}</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
                        <!-- Order Items -->
                        <tr>
                            <td style='padding: 20px 40px;'>
                                <h2 style='margin: 0 0 15px; color: #111827; font-size: 18px; font-weight: 600;'>Order Summary</h2>
                                <table width='100%' cellpadding='0' cellspacing='0' style='border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden;'>
                                    <thead>
                                        <tr style='background-color: #f9fafb;'>
                                            <th style='padding: 12px; text-align: left; color: #111827; font-weight: 600;'>Book</th>
                                            <th style='padding: 12px; text-align: center; color: #111827; font-weight: 600;'>Qty</th>
                                            <th style='padding: 12px; text-align: right; color: #111827; font-weight: 600;'>Price</th>
                                            <th style='padding: 12px; text-align: right; color: #111827; font-weight: 600;'>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$itemsHtml}
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan='3' style='padding: 12px; text-align: right; color: #374151;'>Subtotal:</td>
                                            <td style='padding: 12px; text-align: right; color: #374151;'>" . formatPrice($order['total_amount']) . "</td>
                                        </tr>
                                        <tr>
                                            <td colspan='3' style='padding: 12px; text-align: right; color: #374151;'>Tax:</td>
                                            <td style='padding: 12px; text-align: right; color: #374151;'>" . formatPrice($order['tax_amount']) . "</td>
                                        </tr>
                                        <tr style='background-color: #f9fafb;'>
                                            <td colspan='3' style='padding: 12px; text-align: right; color: #111827; font-weight: 600; font-size: 16px;'>Grand Total:</td>
                                            <td style='padding: 12px; text-align: right; color: #111827; font-weight: 600; font-size: 16px;'>" . formatPrice($order['grand_total']) . "</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </td>
                        </tr>
                        
                        <!-- Payment & Shipping Info -->
                        <tr>
                            <td style='padding: 20px 40px;'>
                                <table width='100%' cellpadding='0' cellspacing='0'>
                                    <tr>
                                        <td width='50%' style='padding-right: 10px; vertical-align: top;'>
                                            <h3 style='margin: 0 0 10px; color: #111827; font-size: 16px; font-weight: 600;'>Payment Method</h3>
                                            <p style='margin: 0; color: #374151; font-size: 14px;'>{$order['payment_method']}</p>
                                        </td>
                                        <td width='50%' style='padding-left: 10px; vertical-align: top;'>
                                            <h3 style='margin: 0 0 10px; color: #111827; font-size: 16px; font-weight: 600;'>Shipping Address</h3>
                                            <p style='margin: 0; color: #374151; font-size: 14px; line-height: 1.6;'>
                                                {$order['shipping_address']}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
                        <!-- Footer -->
                        <tr>
                            <td style='padding: 30px 40px; background-color: #f9fafb; border-top: 2px solid #e5e7eb;'>
                                <p style='margin: 0 0 10px; color: #374151; font-size: 14px; text-align: center;'>
                                    Questions about your order? Contact us at <a href='mailto:support@nightowlbooks.com' style='color: #2563eb; text-decoration: none;'>support@nightowlbooks.com</a>
                                </p>
                                <p style='margin: 0; color: #6b7280; font-size: 12px; text-align: center;'>
                                    © " . date('Y') . " Night Owl Books. All rights reserved.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
    ";

    return $html;
}

/**
 * Generate plain text email for order confirmation
 */
function generateOrderConfirmationText($order, $items)
{
    $text = "ORDER CONFIRMATION\n";
    $text .= "==================\n\n";
    $text .= "Hi {$order['first_name']},\n\n";
    $text .= "Thank you for your order from Night Owl Books!\n\n";
    $text .= "ORDER DETAILS\n";
    $text .= "Order Number: {$order['order_number']}\n";
    $text .= "Order Date: " . date('F j, Y', strtotime($order['created_at'])) . "\n";
    $text .= "Transaction ID: {$order['transaction_id']}\n\n";
    $text .= "ORDER SUMMARY\n";
    $text .= "-------------\n";

    foreach ($items as $item) {
        $itemTotal = $item['price_at_purchase'] * $item['quantity'];
        $text .= "{$item['title']} x{$item['quantity']} - " . formatPrice($itemTotal) . "\n";
    }

    $text .= "\nSubtotal: " . formatPrice($order['total_amount']) . "\n";
    $text .= "Tax: " . formatPrice($order['tax_amount']) . "\n";
    $text .= "Grand Total: " . formatPrice($order['grand_total']) . "\n\n";
    $text .= "Payment Method: {$order['payment_method']}\n";
    $text .= "Shipping Address: {$order['shipping_address']}\n\n";
    $text .= "Questions? Contact us at support@nightowlbooks.com\n\n";
    $text .= "© " . date('Y') . " Night Owl Books. All rights reserved.\n";

    return $text;
}
