<?php
/**
 * Authentication Middleware
 * Protect routes and enforce role-based access control
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';

/**
 * Require authentication
 * Redirect to login if not authenticated
 */
function requireAuth()
{
    if (!isLoggedIn()) {
        setFlashMessage('error', 'Please log in to access this page');
        redirect(SITE_URL . '/auth/login.php');
    }

    // Check session timeout
    if (
        isset($_SESSION['last_activity']) &&
        (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT
    ) {
        session_destroy();
        setFlashMessage('error', 'Your session has expired. Please log in again');
        redirect(SITE_URL . '/auth/login.php');
    }

    $_SESSION['last_activity'] = time();
}

/**
 * Require admin role
 * Redirect to user dashboard if not admin
 */
function requireAdmin()
{
    requireAuth();

    if (!isAdmin()) {
        setFlashMessage('error', 'Access denied. Admin privileges required');
        redirect(SITE_URL . '/user/catalog.php');
    }
}

/**
 * Require user role
 * Redirect to admin dashboard if admin
 */
function requireUser()
{
    requireAuth();

    if (isAdmin()) {
        redirect(SITE_URL . '/admin/dashboard.php');
    }
}

/**
 * Redirect if already logged in
 */
function redirectIfAuthenticated()
{
    if (isLoggedIn()) {
        if (isAdmin()) {
            redirect(SITE_URL . '/admin/dashboard.php');
        } else {
            redirect(SITE_URL . '/user/catalog.php');
        }
    }
}
