<?php
/**
 * Test SendGrid Email Configuration
 * Run this to verify SendGrid is working
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/email.php';

echo "=== SENDGRID EMAIL TEST ===\n\n";

// Check configuration
$apiKey = $_ENV['SENDGRID_API_KEY'] ?? null;
$fromEmail = $_ENV['SENDGRID_FROM_EMAIL'] ?? null;
$fromName = $_ENV['SENDGRID_FROM_NAME'] ?? null;

echo "Configuration Check:\n";
echo "- API Key: " . ($apiKey ? "✓ Set (" . substr($apiKey, 0, 10) . "...)" : "✗ Not set") . "\n";
echo "- From Email: " . ($fromEmail ? "✓ $fromEmail" : "✗ Not set") . "\n";
echo "- From Name: " . ($fromName ? "✓ $fromName" : "✗ Not set") . "\n\n";

if (!$apiKey) {
    echo "ERROR: SENDGRID_API_KEY not set in environment variables!\n";
    echo "\nTo fix:\n";
    echo "1. Add to .env file: SENDGRID_API_KEY=your_api_key_here\n";
    echo "2. Or set environment variable in your system\n";
    exit(1);
}

if (!$fromEmail) {
    echo "ERROR: SENDGRID_FROM_EMAIL not set!\n";
    echo "Add to .env file: SENDGRID_FROM_EMAIL=your-verified-email@gmail.com\n";
    exit(1);
}

// Send test email
echo "Sending test email...\n";

$testEmail = $fromEmail; // Send to yourself
$subject = "SendGrid Test - Online Bookstore";
$htmlBody = "
    <html>
    <body style='font-family: Arial, sans-serif; padding: 20px;'>
        <h1 style='color: #2563eb;'>SendGrid is Working! 🎉</h1>
        <p>This is a test email from your Online Bookstore application.</p>
        <p><strong>Configuration:</strong></p>
        <ul>
            <li>From: $fromName &lt;$fromEmail&gt;</li>
            <li>API Key: " . substr($apiKey, 0, 10) . "...</li>
        </ul>
        <p>If you received this email, SendGrid is configured correctly!</p>
        <hr>
        <p style='color: #6b7280; font-size: 12px;'>Sent from Online Bookstore - SendGrid Test</p>
    </body>
    </html>
";
$textBody = "SendGrid is Working!\n\nThis is a test email from your Online Bookstore application.\n\nIf you received this email, SendGrid is configured correctly!";

$result = sendEmail($testEmail, $subject, $htmlBody, $textBody);

echo "\nResult:\n";
if ($result['success']) {
    echo "✓ Email sent successfully!\n";
    if (isset($result['message_id'])) {
        echo "  Message ID: {$result['message_id']}\n";
    }
    echo "\n✅ SUCCESS! Check your inbox at: $testEmail\n";
    echo "\nNext steps:\n";
    echo "1. Check your email (including spam folder)\n";
    echo "2. If received, SendGrid is working!\n";
    echo "3. Deploy to production and add environment variables to Render\n";
} else {
    echo "✗ Email failed to send\n";
    echo "  Error: {$result['message']}\n";
    echo "\n❌ FAILED\n";
    echo "\nTroubleshooting:\n";
    echo "1. Verify API key is correct\n";
    echo "2. Check that from email is verified in SendGrid\n";
    echo "3. Check SendGrid dashboard for errors\n";
}
?>