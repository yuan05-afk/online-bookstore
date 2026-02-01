<?php
/**
 * Get the WORKING password hashes from production database
 */

// Connect to production database
try {
    $db = new PDO(
        'mysql:host=sql12.freesqldatabase.com;dbname=sql12815923;charset=utf8',
        'sql12815923',
        'gJUmV8zau4'
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the current working hashes
    $stmt = $db->query("SELECT email, password_hash FROM users WHERE email IN ('admin@bookstore.com', 'user@bookstore.com') ORDER BY role DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "=== WORKING HASHES FROM PRODUCTION ===\n\n";

    foreach ($users as $user) {
        $role = ($user['email'] == 'admin@bookstore.com') ? 'Admin' : 'User';
        $password = ($user['email'] == 'admin@bookstore.com') ? 'admin123' : 'user123';

        echo "$role ({$user['email']}):\n";
        echo "Hash: {$user['password_hash']}\n";
        echo "Length: " . strlen($user['password_hash']) . "\n";
        echo "Verifies: " . (password_verify($password, $user['password_hash']) ? "✓ YES" : "✗ NO") . "\n\n";
    }

    echo "=== UPDATE seed.sql WITH THESE HASHES ===\n\n";

    $adminHash = '';
    $userHash = '';

    foreach ($users as $user) {
        if ($user['email'] == 'admin@bookstore.com') {
            $adminHash = $user['password_hash'];
        } else {
            $userHash = $user['password_hash'];
        }
    }

    echo "-- Insert admin user with correct hash (password: admin123)\n";
    echo "INSERT INTO users (email, password_hash, first_name, last_name, role, phone, address, city, state, zip_code) VALUES\n";
    echo "('admin@bookstore.com', '$adminHash', 'Admin', 'User', 'admin', '555-0100', '123 Admin St', 'New York', 'NY', '10001');\n\n";

    echo "-- Insert test user with correct hash (password: user123)\n";
    echo "INSERT INTO users (email, password_hash, first_name, last_name, role, phone, address, city, state, zip_code) VALUES\n";
    echo "('user@bookstore.com', '$userHash', 'Test', 'User', 'user', '555-0200', '456 User Ave', 'Los Angeles', 'CA', '90001');\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>