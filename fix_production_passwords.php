<?php
/**
 * Fix Password Hashes for PRODUCTION Database (FreeSQLDatabase)
 */

echo "=== FIXING PASSWORD HASHES FOR PRODUCTION ===\n\n";

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
echo "Admin test: " . ($adminTest ? "PASS ✓" : "FAIL ✗") . "\n";
echo "User test: " . ($userTest ? "PASS ✓" : "FAIL ✗") . "\n\n";

if (!$adminTest || !$userTest) {
    echo "ERROR: Hash verification failed! Not updating database.\n";
    exit(1);
}

// Connect to PRODUCTION database (FreeSQLDatabase)
echo "Connecting to production database...\n";
try {
    $db = new PDO(
        'mysql:host=sql12.freesqldatabase.com;dbname=sql12815923;charset=utf8',
        'sql12815923',
        'gJUmV8zau4'
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected to production database\n\n";
} catch (PDOException $e) {
    echo "ERROR: Could not connect to database: " . $e->getMessage() . "\n";
    exit(1);
}

// Update database
echo "Updating production database...\n";

try {
    // Update admin
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = 'admin@bookstore.com'");
    $stmt->execute([$adminHash]);
    echo "✓ Updated admin password\n";

    // Update user
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = 'user@bookstore.com'");
    $stmt->execute([$userHash]);
    echo "✓ Updated user password\n";

    // Verify updates
    echo "\nVerifying updates...\n";
    $stmt = $db->query("SELECT email, role, LENGTH(password_hash) as hash_length FROM users WHERE email IN ('admin@bookstore.com', 'user@bookstore.com')");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        echo "- {$user['email']} ({$user['role']}): hash length = {$user['hash_length']}\n";
    }

    echo "\n=== SUCCESS ===\n";
    echo "Production passwords have been updated!\n";
    echo "Try logging in at: https://online-bookstore-6ldw.onrender.com/auth/login.php\n";
    echo "Admin: admin@bookstore.com / admin123\n";
    echo "User: user@bookstore.com / user123\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>