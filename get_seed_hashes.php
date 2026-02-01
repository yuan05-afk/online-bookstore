<?php
// Generate proper password hashes for seed.sql
$adminHash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
$userHash = password_hash('user123', PASSWORD_BCRYPT, ['cost' => 12]);

echo "Copy these into seed.sql:\n\n";
echo "Admin hash (length " . strlen($adminHash) . "):\n";
echo $adminHash . "\n\n";
echo "User hash (length " . strlen($userHash) . "):\n";
echo $userHash . "\n\n";

// Verify they work
echo "Verification:\n";
echo "Admin: " . (password_verify('admin123', $adminHash) ? "✓ PASS" : "✗ FAIL") . "\n";
echo "User: " . (password_verify('user123', $userHash) ? "✓ PASS" : "✗ FAIL") . "\n";
?>