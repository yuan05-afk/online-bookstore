<?php
/**
 * Generate Password Hashes for Production Database
 * Run this script to get the correct password hashes
 */

// Admin password: admin123
$adminPassword = 'admin123';
$adminHash = password_hash($adminPassword, PASSWORD_BCRYPT, ['cost' => 12]);

// User password: user123
$userPassword = 'user123';
$userHash = password_hash($userPassword, PASSWORD_BCRYPT, ['cost' => 12]);

echo "=== PASSWORD HASHES FOR SEED.SQL ===\n\n";
echo "Admin (admin123):\n";
echo $adminHash . "\n\n";
echo "User (user123):\n";
echo $userHash . "\n\n";

echo "=== SQL UPDATE STATEMENTS ===\n\n";
echo "-- Update admin password\n";
echo "UPDATE users SET password_hash = '$adminHash' WHERE email = 'admin@bookstore.com';\n\n";
echo "-- Update user password\n";
echo "UPDATE users SET password_hash = '$userHash' WHERE email = 'user@bookstore.com';\n\n";

echo "=== FOR SEED.SQL FILE ===\n\n";
echo "-- Insert admin user (password: admin123)\n";
echo "INSERT INTO users (email, password_hash, first_name, last_name, role, phone, address, city, state, zip_code) VALUES\n";
echo "('admin@bookstore.com', '$adminHash', 'Admin', 'User', 'admin', '555-0100', '123 Admin St', 'New York', 'NY', '10001');\n\n";

echo "-- Insert test user (password: user123)\n";
echo "INSERT INTO users (email, password_hash, first_name, last_name, role, phone, address, city, state, zip_code) VALUES\n";
echo "('user@bookstore.com', '$userHash', 'Test', 'User', 'user', '555-0200', '456 User Ave', 'Los Angeles', 'CA', '90001');\n";
?>