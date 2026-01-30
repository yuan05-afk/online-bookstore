<?php
/**
 * User Login Page - Modern Design
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect(SITE_URL . '/admin/dashboard.php');
    } else {
        redirect(SITE_URL . '/user/catalog.php');
    }
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate inputs
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!validateEmail($email)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (empty($password)) {
            $errors[] = 'Password is required.';
        }

        // If no validation errors, attempt login
        if (empty($errors)) {
            try {
                $stmt = getDB()->prepare("SELECT id, email, password_hash, first_name, last_name, role FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && verifyPassword($password, $user['password_hash'])) {
                    // Regenerate session ID for security
                    session_regenerate_id(true);

                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['last_activity'] = time();

                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        redirect(SITE_URL . '/admin/dashboard.php');
                    } else {
                        redirect(SITE_URL . '/user/catalog.php');
                    }
                } else {
                    $errors[] = 'Invalid email or password.';
                }
            } catch (PDOException $e) {
                error_log('Login error: ' . $e->getMessage());
                $errors[] = 'An error occurred. Please try again later.';
            }
        }
    }
}

$pageTitle = 'Sign In';
require_once __DIR__ . '/../includes/auth_header.php';
?>

<?php
$flashMessage = getFlashMessage();
?>

<section class="auth-section">
    <div class="auth-card fade-in">
        <div class="auth-card-body">
            <div class="auth-card-header">
                <div class="auth-card-icon">
                    <iconify-icon icon="solar:user-circle-linear" width="24" stroke-width="1.5"></iconify-icon>
                </div>
                <h2 class="auth-card-title">Welcome back</h2>
                <p class="auth-card-subtitle">Please enter your details to sign in.</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="auth-alert auth-alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo escapeHTML($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($flashMessage && $flashMessage['type'] === 'success'): ?>
                <div class="auth-alert auth-alert-success">
                    <p><?php echo escapeHTML($flashMessage['message']); ?></p>
                </div>
            <?php endif; ?>

            <!-- Demo Credentials Info -->
            <div class="auth-alert auth-alert-info">
                <p>Demo Credentials:</p>
                <div>
                    <p><strong>Admin:</strong> admin@bookstore.com / admin123</p>
                    <p><strong>User:</strong> user@bookstore.com / user123</p>
                </div>
            </div>

            <form method="POST" action="" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <div class="auth-form-group">
                    <label class="auth-form-label">Email address</label>
                    <div class="auth-input-wrapper">
                        <iconify-icon icon="solar:letter-linear" class="auth-input-icon" stroke-width="1.5"
                            width="18"></iconify-icon>
                        <input type="email" name="email" value="<?php echo escapeHTML($email); ?>" required
                            class="auth-input" placeholder="Enter your email">
                    </div>
                </div>

                <div class="auth-form-group">
                    <label class="auth-form-label">Password</label>
                    <div class="auth-input-wrapper">
                        <iconify-icon icon="solar:lock-password-linear" class="auth-input-icon" stroke-width="1.5"
                            width="18"></iconify-icon>
                        <input type="password" name="password" required class="auth-input" placeholder="••••••••">
                    </div>
                </div>

                <div style="text-align: right; padding-top: 0.25rem;">
                    <a href="#" class="auth-forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="auth-submit">
                    Sign in
                </button>
            </form>
        </div>
        <div class="auth-card-footer">
            Don't have an account?
            <a href="<?php echo SITE_URL; ?>/auth/register.php">Create an account</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/auth_footer.php'; ?>