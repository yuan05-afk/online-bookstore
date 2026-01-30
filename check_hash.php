<?php
require_once __DIR__ . '/config/config.php';

$db = getDB();
$stmt = $db->query("SELECT email, password_hash, LENGTH(password_hash) as hash_length FROM users");
$users = $stmt->fetchAll();

foreach ($users as $user) {
    echo "Email: {$user['email']}\n";
    echo "Hash: {$user['password_hash']}\n";
    echo "Length: {$user['hash_length']} characters\n";
    echo "Expected: 60 characters for bcrypt\n\n";
}
