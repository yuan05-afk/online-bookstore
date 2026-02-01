<?php
/**
 * Debug and Generate Working Password Hashes
 */

echo "=== GENERATING NEW PASSWORD HASHES ===\n\n";

// Generate admin hash
$adminHash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
echo "Admin Hash (admin123):\n";
echo $adminHash . "\n";
echo "Length: " . strlen($adminHash) . " characters\n\n";

// Generate user hash
$userHash = password_hash('user123', PASSWORD_BCRYPT, ['cost' => 12]);
echo "User Hash (user123):\n";
echo $userHash . "\n";
echo "Length: " . strlen($userHash) . " characters\n\n";

// Test verification
echo "=== TESTING VERIFICATION ===\n\n";
$testAdmin = password_verify('admin123', $adminHash);
$testUser = password_verify('user123', $userHash);

echo "Admin verification: " . ($testAdmin ? "✓ PASS" : "✗ FAIL") . "\n";
echo "User verification: " . ($testUser ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "=== SQL FOR PHPMYADMIN ===\n\n";
echo "-- Delete old users\n";
echo "DELETE FROM users WHERE email IN ('admin@bookstore.com', 'user@bookstore.com');\n\n";

echo "-- Insert admin (password: admin123)\n";
echo "INSERT INTO users (email, password_hash, first_name, last_name, role, phone, address, city, state, zip_code) VALUES\n";
echo "('admin@bookstore.com', '" . $adminHash . "', 'Admin', 'User', 'admin', '555-0100', '123 Admin St', 'New York', 'NY', '10001');\n\n";

echo "-- Insert user (password: user123)\n";
echo "INSERT INTO users (email, password_hash, first_name, last_name, role, phone, address, city, state, zip_code) VALUES\n";
echo "('user@bookstore.com', '" . $userHash . "', 'Test', 'User', 'user', '555-0200', '456 User Ave', 'Los Angeles', 'CA', '90001');\n\n";

echo "=== COPY THE SQL ABOVE AND RUN IN PHPMYADMIN ===\n";
?>