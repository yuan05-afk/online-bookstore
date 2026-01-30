<?php
/**
 * Debug script to check login credentials and database state
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/security.php';

echo "=== LOGIN DEBUG SCRIPT ===\n\n";

// Test database connection
try {
    $db = getDB();
    echo "✓ Database connection successful\n\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Check if users exist
echo "--- Checking Users in Database ---\n";
$stmt = $db->query("SELECT id, email, role, LEFT(password_hash, 40) as hash_preview FROM users");
$users = $stmt->fetchAll();

if (empty($users)) {
    echo "✗ NO USERS FOUND IN DATABASE!\n";
    echo "You need to run seed.sql to insert demo users.\n\n";
} else {
    echo "Found " . count($users) . " user(s):\n";
    foreach ($users as $user) {
        echo "  - ID: {$user['id']}, Email: {$user['email']}, Role: {$user['role']}\n";
        echo "    Hash preview: {$user['hash_preview']}...\n";
    }
    echo "\n";
}

// Test password verification for demo accounts
echo "--- Testing Password Verification ---\n";

$testCredentials = [
    ['email' => 'admin@bookstore.com', 'password' => 'admin123'],
    ['email' => 'user@bookstore.com', 'password' => 'user123']
];

foreach ($testCredentials as $cred) {
    echo "\nTesting: {$cred['email']} / {$cred['password']}\n";

    $stmt = $db->prepare("SELECT id, email, password_hash, role FROM users WHERE email = ?");
    $stmt->execute([$cred['email']]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "  ✗ User NOT FOUND in database\n";
        continue;
    }

    echo "  ✓ User found: {$user['email']} (Role: {$user['role']})\n";
    echo "  Password hash: {$user['password_hash']}\n";

    // Test password verification
    $verified = verifyPassword($cred['password'], $user['password_hash']);

    if ($verified) {
        echo "  ✓ PASSWORD VERIFICATION: SUCCESS\n";
    } else {
        echo "  ✗ PASSWORD VERIFICATION: FAILED\n";

        // Generate a new hash for comparison
        $newHash = hashPassword($cred['password']);
        echo "  New hash would be: $newHash\n";

        // Test if new hash works
        $testVerify = verifyPassword($cred['password'], $newHash);
        echo "  New hash verification: " . ($testVerify ? "SUCCESS" : "FAILED") . "\n";
    }
}

echo "\n=== END DEBUG ===\n";
