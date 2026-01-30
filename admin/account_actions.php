<?php
/**
 * Admin Account Actions Handler
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/admin/accounts.php');
}

if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    setFlashMessage('error', 'Invalid security token');
    redirect(SITE_URL . '/admin/accounts.php');
}

$db = getDB();
$current_user_id = $_SESSION['user_id'];
$account_id = isset($_POST['account_id']) ? (int) $_POST['account_id'] : 0;
$action = $_POST['action'] ?? '';

// Prevent self-modification
if ($account_id === $current_user_id) {
    setFlashMessage('error', 'You cannot modify your own account');
    redirect(SITE_URL . '/admin/account_detail.php?id=' . $account_id);
}

// Verify account exists
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$account_id]);
$account = $stmt->fetch();

if (!$account) {
    setFlashMessage('error', 'User account not found');
    redirect(SITE_URL . '/admin/accounts.php');
}

// Promote User to Admin
if ($action === 'promote_to_admin') {
    // Check if user is already an admin
    if ($account['role'] === 'admin') {
        setFlashMessage('error', 'User is already an administrator');
        redirect(SITE_URL . '/admin/account_detail.php?id=' . $account_id);
    }

    // Promote to admin
    $stmt = $db->prepare("UPDATE users SET role = 'admin' WHERE id = ?");

    if ($stmt->execute([$account_id])) {
        setFlashMessage('success', 'User promoted to administrator successfully');
    } else {
        setFlashMessage('error', 'Failed to promote user to administrator');
    }

    redirect(SITE_URL . '/admin/account_detail.php?id=' . $account_id);
}

// Invalid action
setFlashMessage('error', 'Invalid action');
redirect(SITE_URL . '/admin/account_detail.php?id=' . $account_id);
