<?php
/**
 * Admin Profile Actions Handler
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/admin/profile.php');
}

if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    setFlashMessage('error', 'Invalid security token');
    redirect(SITE_URL . '/admin/profile.php');
}

$db = getDB();
$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// Update Profile Information
if ($action === 'update_profile') {
    $first_name = sanitizeInput($_POST['first_name']);
    $last_name = sanitizeInput($_POST['last_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone'] ?? '');

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlashMessage('error', 'Invalid email format');
        redirect(SITE_URL . '/admin/profile.php');
    }

    // Check if email is already taken by another user
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        setFlashMessage('error', 'Email is already in use by another account');
        redirect(SITE_URL . '/admin/profile.php');
    }

    // Update profile
    $stmt = $db->prepare("
        UPDATE users 
        SET first_name = ?, last_name = ?, email = ?, phone = ?
        WHERE id = ?
    ");

    if ($stmt->execute([$first_name, $last_name, $email, $phone, $user_id])) {
        setFlashMessage('success', 'Profile updated successfully');
    } else {
        setFlashMessage('error', 'Failed to update profile');
    }

    redirect(SITE_URL . '/admin/profile.php');
}

// Change Password
if ($action === 'change_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Get current user
    $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Verify current password
    if (!password_verify($current_password, $user['password_hash'])) {
        setFlashMessage('error', 'Current password is incorrect');
        redirect(SITE_URL . '/admin/profile.php');
    }

    // Validate new password
    if (strlen($new_password) < 8) {
        setFlashMessage('error', 'New password must be at least 8 characters');
        redirect(SITE_URL . '/admin/profile.php');
    }

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        setFlashMessage('error', 'New passwords do not match');
        redirect(SITE_URL . '/admin/profile.php');
    }

    // Update password
    $password_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");

    if ($stmt->execute([$password_hash, $user_id])) {
        setFlashMessage('success', 'Password changed successfully');
    } else {
        setFlashMessage('error', 'Failed to change password');
    }

    redirect(SITE_URL . '/admin/profile.php');
}

// Invalid action
setFlashMessage('error', 'Invalid action');
redirect(SITE_URL . '/admin/profile.php');
