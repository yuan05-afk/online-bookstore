<?php
// Verify the hashes in seed.sql will work
$adminHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
$userHash = '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm';

echo "=== VERIFYING SEED.SQL HASHES ===\n\n";
echo "Admin hash length: " . strlen($adminHash) . " (should be 60)\n";
echo "User hash length: " . strlen($userHash) . " (should be 60)\n\n";

echo "Testing passwords:\n";
$adminTest = password_verify('admin123', $adminHash);
$userTest = password_verify('user123', $userHash);

echo "Admin (admin123): " . ($adminTest ? "✓ PASS" : "✗ FAIL") . "\n";
echo "User (user123): " . ($userTest ? "✓ PASS" : "✗ FAIL") . "\n\n";

if ($adminTest && $userTest) {
    echo "✅ SUCCESS! The hashes in seed.sql are now correct!\n";
    echo "Future deployments will work without manual password fixes.\n";
} else {
    echo "❌ ERROR! Hashes still don't work.\n";
}
?>