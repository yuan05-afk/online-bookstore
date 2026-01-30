<?php
/**
 * Fix Password Hashes - Generate and Update Database
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/security.php';

echo "=== FIXING PASSWORD HASHES ===\n\n";

// Generate fresh hashes
$adminHash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
$userHash = password_hash('user123', PASSWORD_BCRYPT, ['cost' => 12]);

echo "Generated hashes:\n";
echo "Admin hash: $adminHash\n";
echo "User hash: $userHash\n\n";

// Verify lengths
echo "Admin hash length: " . strlen($adminHash) . " (should be 60)\n";
echo "User hash length: " . strlen($userHash) . " (should be 60)\n\n";

// Test verification before updating
echo "Testing verification:\n";
$adminTest = password_verify('admin123', $adminHash);
$userTest = password_verify('user123', $userHash);
echo "Admin test: " . ($adminTest ? "PASS" : "FAIL") . "\n";
echo "User test: " . ($userTest ? "PASS" : "FAIL") . "\n\n";

if (!$adminTest || !$userTest) {
    echo "ERROR: Hash verification failed! Not updating database.\n";
    exit(1);
}

// Update database
echo "Updating database...\n";
$db = getDB();

try {
    // Update admin
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = 'admin@bookstore.com'");
    $stmt->execute([$adminHash]);
    echo "✓ Updated admin password\n";

    // Update user
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = 'user@bookstore.com'");
    $stmt->execute([$userHash]);
    echo "✓ Updated user password\n";

    echo "\n=== SUCCESS ===\n";
    echo "Passwords have been updated. Try logging in now!\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
