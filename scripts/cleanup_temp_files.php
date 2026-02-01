#!/usr/bin/env php
<?php
/**
 * Cleanup Temporary Development Files
 * 
 * This script removes temporary debug and test files that are not needed
 * for the application to run in production.
 * 
 * Usage: php scripts/cleanup_temp_files.php
 */

echo "=== Online Bookstore - Cleanup Temporary Files ===\n\n";

// List of temporary files to remove
$tempFiles = [
    // Password debug files
    'admin_hash.txt',
    'user_hash.txt',
    'password_hashes.txt',
    'production_hashes.txt',
    'check_hash.php',
    'debug_passwords.php',
    'fix_passwords.php',
    'fix_passwords_production.sql',
    'fix_production_passwords.php',
    'generate_hashes.php',
    'get_production_hashes.php',
    'get_seed_hashes.php',
    'verify_seed_hashes.php',
    'FINAL_PASSWORD_FIX.sql',

    // Login debug files
    'debug_login.php',

    // Email debug files
    'email_preview.php',
    'test_sendgrid.php',
    'test_sendgrid_debug.php',
    'sendgrid_test_result.txt',

    // SendGrid backup
    'includes/email_smtp_backup.php',
    'includes/email_sendgrid.php',
];

$baseDir = dirname(__DIR__);
$deletedCount = 0;
$notFoundCount = 0;

echo "Scanning for temporary files...\n\n";

foreach ($tempFiles as $file) {
    $filePath = $baseDir . '/' . $file;

    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            echo "✓ Deleted: $file\n";
            $deletedCount++;
        } else {
            echo "✗ Failed to delete: $file\n";
        }
    } else {
        $notFoundCount++;
    }
}

echo "\n=== Summary ===\n";
echo "Files deleted: $deletedCount\n";
echo "Files not found: $notFoundCount\n";
echo "\n✅ Cleanup complete!\n";
echo "\nYour project is now cleaner and more developer-friendly.\n";
echo "See TEMP_FILES.md for more information.\n";
?>