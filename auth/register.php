<?php
/**
 * User Registration Page - Modern Design
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
$formData = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'state' => '',
    'zip_code' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        // Sanitize and collect form data
        $formData['first_name'] = sanitizeInput($_POST['first_name'] ?? '');
        $formData['last_name'] = sanitizeInput($_POST['last_name'] ?? '');
        $formData['email'] = sanitizeInput($_POST['email'] ?? '');
        $formData['phone'] = sanitizeInput($_POST['phone'] ?? '');
        $formData['address'] = sanitizeInput($_POST['address'] ?? '');
        $formData['city'] = sanitizeInput($_POST['city'] ?? '');
        $formData['state'] = sanitizeInput($_POST['state'] ?? '');
        $formData['zip_code'] = sanitizeInput($_POST['zip_code'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate required fields
        if (empty($formData['first_name'])) {
            $errors[] = 'First name is required.';
        }

        if (empty($formData['last_name'])) {
            $errors[] = 'Last name is required.';
        }

        if (empty($formData['email'])) {
            $errors[] = 'Email is required.';
        } elseif (!validateEmail($formData['email'])) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (!validatePassword($password)) {
            $errors[] = 'Password must be at least ' . MIN_PASSWORD_LENGTH . ' characters long.';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        // Check if email already exists
        if (empty($errors)) {
            try {
                $stmt = getDB()->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$formData['email']]);
                if ($stmt->fetch()) {
                    $errors[] = 'An account with this email already exists.';
                }
            } catch (PDOException $e) {
                error_log('Registration check error: ' . $e->getMessage());
                $errors[] = 'An error occurred. Please try again later.';
            }
        }

        // If no errors, create the account
        if (empty($errors)) {
            try {
                $passwordHash = hashPassword($password);

                $stmt = getDB()->prepare("
                    INSERT INTO users (email, password_hash, first_name, last_name, phone, address, city, state, zip_code, role) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'user')
                ");

                $stmt->execute([
                    $formData['email'],
                    $passwordHash,
                    $formData['first_name'],
                    $formData['last_name'],
                    $formData['phone'],
                    $formData['address'],
                    $formData['city'],
                    $formData['state'],
                    $formData['zip_code']
                ]);

                setFlashMessage('success', 'Account created successfully! Please sign in.');
                redirect(SITE_URL . '/auth/login.php');
            } catch (PDOException $e) {
                error_log('Registration error: ' . $e->getMessage());
                $errors[] = 'An error occurred during registration. Please try again later.';
            }
        }
    }
}

$pageTitle = 'Create Account';
require_once __DIR__ . '/../includes/auth_header.php';
?>

<section class="auth-section">
    <div class="auth-card auth-card-wide fade-in">
        <div class="auth-card-body">
            <div class="auth-card-header">
                <div class="auth-card-icon">
                    <iconify-icon icon="solar:users-group-rounded-linear" width="24" stroke-width="1.5"></iconify-icon>
                </div>
                <h2 class="auth-card-title">Create an account</h2>
                <p class="auth-card-subtitle">Start your reading journey today.</p>
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

            <form method="POST" action="" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <div class="auth-grid-2">
                    <div class="auth-form-group">
                        <label class="auth-form-label">First Name</label>
                        <div class="auth-input-wrapper">
                            <iconify-icon icon="solar:user-linear" class="auth-input-icon" stroke-width="1.5" width="18"></iconify-icon>
                            <input type="text" name="first_name" value="<?php echo escapeHTML($formData['first_name']); ?>" required class="auth-input" placeholder="John">
                        </div>
                    </div>

                    <div class="auth-form-group">
                        <label class="auth-form-label">Last Name</label>
                        <div class="auth-input-wrapper">
                            <iconify-icon icon="solar:user-linear" class="auth-input-icon" stroke-width="1.5" width="18"></iconify-icon>
                            <input type="text" name="last_name" value="<?php echo escapeHTML($formData['last_name']); ?>" required class="auth-input" placeholder="Doe">
                        </div>
                    </div>
                </div>

                <div class="auth-form-group">
                    <label class="auth-form-label">Email address</label>
                    <div class="auth-input-wrapper">
                        <iconify-icon icon="solar:letter-linear" class="auth-input-icon" stroke-width="1.5" width="18"></iconify-icon>
                        <input type="email" name="email" value="<?php echo escapeHTML($formData['email']); ?>" required class="auth-input" placeholder="john@example.com">
                    </div>
                </div>

                <div class="auth-form-group">
                    <label class="auth-form-label">Phone (Optional)</label>
                    <div class="auth-input-wrapper">
                        <iconify-icon icon="solar:phone-linear" class="auth-input-icon" stroke-width="1.5" width="18"></iconify-icon>
                        <input type="tel" name="phone" value="<?php echo escapeHTML($formData['phone']); ?>" class="auth-input" placeholder="(555) 123-4567">
                    </div>
                </div>

                <div class="auth-grid-2">
                    <div class="auth-form-group">
                        <label class="auth-form-label">Password</label>
                        <div class="auth-input-wrapper">
                            <iconify-icon icon="solar:lock-password-linear" class="auth-input-icon" stroke-width="1.5" width="18"></iconify-icon>
                            <input type="password" name="password" required class="auth-input" placeholder="Create password">
                        </div>
                    </div>
                    <div class="auth-form-group">
                        <label class="auth-form-label">Confirm Password</label>
                        <div class="auth-input-wrapper">
                            <iconify-icon icon="solar:lock-password-linear" class="auth-input-icon" stroke-width="1.5" width="18"></iconify-icon>
                            <input type="password" name="confirm_password" required class="auth-input" placeholder="Confirm password">
                        </div>
                    </div>
                </div>

                <div class="auth-pt-2">
                    <button type="submit" class="auth-submit">
                        Create Account
                    </button>
                    <p class="auth-text-xs auth-text-center auth-mt-3" style="color: var(--auth-zinc-400);">
                        By registering, you agree to our <a href="#" class="auth-link">Terms</a> and <a href="#" class="auth-link">Privacy Policy</a>.
                    </p>
                </div>
            </form>
        </div>
        <div class="auth-card-footer">
            Already have an account? 
            <a href="<?php echo SITE_URL; ?>/auth/login.php">Sign in</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/auth_footer.php'; ?>