<?php
/**
 * Homepage
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/security.php';

// Redirect to appropriate page based on login status
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect(SITE_URL . '/admin/dashboard.php');
    } else {
        redirect(SITE_URL . '/user/catalog.php');
    }
} else {
    redirect(SITE_URL . '/auth/login.php');
}
