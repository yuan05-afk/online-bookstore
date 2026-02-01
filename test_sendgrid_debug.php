<?php
/**
 * Detailed SendGrid Debug Test
 */

require_once __DIR__ . '/config/config.php';

echo "=== SENDGRID DEBUG TEST ===\n\n";

// Check configuration
$apiKey = $_ENV['SENDGRID_API_KEY'] ?? null;
$fromEmail = $_ENV['SENDGRID_FROM_EMAIL'] ?? null;
$fromName = $_ENV['SENDGRID_FROM_NAME'] ?? null;

echo "1. Configuration:\n";
echo "   API Key: " . ($apiKey ? "✓ " . substr($apiKey, 0, 15) . "..." : "✗ NOT SET") . "\n";
echo "   From Email: " . ($fromEmail ? "✓ $fromEmail" : "✗ NOT SET") . "\n";
echo "   From Name: " . ($fromName ? "✓ $fromName" : "✗ NOT SET") . "\n\n";

if (!$apiKey || !$fromEmail) {
    echo "ERROR: Missing configuration!\n";
    exit(1);
}

// Check if SendGrid library is installed
echo "2. Checking SendGrid library...\n";
if (!file_exists(__DIR__ . '/vendor/sendgrid/sendgrid/lib/SendGrid.php')) {
    echo "   ✗ SendGrid library NOT installed!\n";
    echo "   Run: composer require sendgrid/sendgrid\n";
    exit(1);
}
echo "   ✓ SendGrid library installed\n\n";

// Try to send email
echo "3. Attempting to send test email...\n";
echo "   To: $fromEmail\n";
echo "   From: $fromName <$fromEmail>\n\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';

    $email = new \SendGrid\Mail\Mail();
    $email->setFrom($fromEmail, $fromName);
    $email->setSubject("SendGrid Test - " . date('Y-m-d H:i:s'));
    $email->addTo($fromEmail);
    $email->addContent("text/plain", "This is a test email from SendGrid.");
    $email->addContent("text/html", "<h1>SendGrid Test</h1><p>If you see this, it works!</p>");

    $sendgrid = new \SendGrid($apiKey);

    echo "   Sending...\n";
    $response = $sendgrid->send($email);

    echo "\n4. Response:\n";
    echo "   Status Code: " . $response->statusCode() . "\n";
    echo "   Body: " . $response->body() . "\n";
    echo "   Headers: " . print_r($response->headers(), true) . "\n";

    if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
        echo "\n✅ SUCCESS! Email sent!\n";
        echo "Check your inbox at: $fromEmail\n";
        echo "(Also check spam folder)\n";
    } else {
        echo "\n❌ FAILED! Status code indicates error.\n";
        echo "Check the response above for details.\n";
    }

} catch (Exception $e) {
    echo "\n❌ EXCEPTION OCCURRED!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
?>