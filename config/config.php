<?php
/**
 * Application Configuration
 * Global settings and constants
 */

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('America/New_York');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
ini_set('session.cookie_samesite', 'Strict');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Application settings
define('SITE_NAME', 'Online Bookstore');
define('SITE_URL', 'http://localhost/online-bookstore');
define('BASE_PATH', dirname(__DIR__));

// Tax rate (8.5%)
define('TAX_RATE', 0.085);

// Upload settings
define('UPLOAD_DIR', BASE_PATH . '/assets/images/books/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('ITEMS_PER_PAGE', 20);

// Security
define('CSRF_TOKEN_EXPIRE', 3600); // 1 hour
define('SESSION_TIMEOUT', 7200); // 2 hours

// Password requirements
define('MIN_PASSWORD_LENGTH', 6);

// Include database configuration
require_once BASE_PATH . '/config/database.php';

// Get database connection
function getDB()
{
    return Database::getInstance()->getConnection();
}
