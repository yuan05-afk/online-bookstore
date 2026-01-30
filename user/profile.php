<?php
/**
 * User Profile Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireUser();

$user_id = getCurrentUserId();
$db = getDB();

// Handle Personal Information Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_personal'])) {
    validateCSRFToken($_POST['csrf_token'] ?? '');

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    $errors = [];

    if (empty($first_name))
        $errors[] = 'First name is required';
    if (empty($last_name))
        $errors[] = 'Last name is required';
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    // Check if email is taken
    if (empty($errors)) {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            $errors[] = 'Email is already taken';
        }
    }

    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->execute([$first_name, $last_name, $email, $phone, $user_id]);

        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['email'] = $email;

        setFlashMessage('success', 'Personal information updated successfully!');
        redirect(SITE_URL . '/user/profile.php');
    } else {
        foreach ($errors as $error) {
            setFlashMessage('error', $error);
        }
    }
}

// Handle Address Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_address'])) {
    validateCSRFToken($_POST['csrf_token'] ?? '');

    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $zip_code = trim($_POST['zip_code'] ?? '');
    $country = trim($_POST['country'] ?? '');

    $stmt = $db->prepare("UPDATE users SET address = ?, city = ?, state = ?, zip_code = ?, country = ? WHERE id = ?");
    $stmt->execute([$address, $city, $state, $zip_code, $country, $user_id]);

    setFlashMessage('success', 'Address updated successfully!');
    redirect(SITE_URL . '/user/profile.php');
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    validateCSRFToken($_POST['csrf_token'] ?? '');

    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    if (empty($current_password)) {
        $errors[] = 'Current password is required';
    }
    if (empty($new_password)) {
        $errors[] = 'New password is required';
    } elseif (strlen($new_password) < 6) {
        $errors[] = 'New password must be at least 6 characters';
    }
    if ($new_password !== $confirm_password) {
        $errors[] = 'New passwords do not match';
    }

    if (empty($errors)) {
        $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Current password is incorrect';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $user_id]);

            setFlashMessage('success', 'Password changed successfully!');
            redirect(SITE_URL . '/user/profile.php');
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            setFlashMessage('error', $error);
        }
    }
}

// Get user data
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    setFlashMessage('error', 'User not found');
    redirect(SITE_URL . '/auth/logout.php');
}

$pageTitle = 'My Profile';
$csrf_token = generateCSRFToken();
include __DIR__ . '/../includes/header.php';
?>

<h1 class="user-page-title">My Profile</h1>

<div class="user-profile-container">
    <!-- Personal Information Form -->
    <form method="POST" class="user-profile-section" id="personalInfoForm">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="update_personal" value="1">

        <section class="user-checkout-section">
            <h2>Personal Information</h2>

            <div class="user-form-row">
                <div class="user-form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" class="user-input"
                        value="<?php echo escapeHTML($user['first_name']); ?>" required>
                </div>

                <div class="user-form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" class="user-input"
                        value="<?php echo escapeHTML($user['last_name']); ?>" required>
                </div>
            </div>

            <div class="user-form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="user-input"
                    value="<?php echo escapeHTML($user['email']); ?>"
                    pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" title="Please enter a valid email address"
                    required>
            </div>

            <div class="user-form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="user-input"
                    value="<?php echo escapeHTML($user['phone'] ?? ''); ?>">
            </div>

            <button type="submit" class="user-btn user-btn-primary">
                <iconify-icon icon="solar:diskette-linear" width="18"></iconify-icon>
                Save Personal Information
            </button>
        </section>
    </form>

    <!-- Address Information Form -->
    <form method="POST" class="user-profile-section" id="addressForm">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="update_address" value="1">

        <section class="user-checkout-section">
            <h2>Address Information</h2>

            <div class="user-form-group">
                <label for="address">Street Address</label>
                <input type="text" id="address" name="address" class="user-input"
                    value="<?php echo escapeHTML($user['address'] ?? ''); ?>">
            </div>

            <div class="user-form-row">
                <div class="user-form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" class="user-input"
                        value="<?php echo escapeHTML($user['city'] ?? ''); ?>">
                </div>

                <div class="user-form-group">
                    <label for="state">State</label>
                    <input type="text" id="state" name="state" class="user-input"
                        value="<?php echo escapeHTML($user['state'] ?? ''); ?>">
                </div>
            </div>

            <div class="user-form-row">
                <div class="user-form-group">
                    <label for="zip_code">ZIP Code</label>
                    <input type="text" id="zip_code" name="zip_code" class="user-input"
                        value="<?php echo escapeHTML($user['zip_code'] ?? ''); ?>">
                </div>

                <div class="user-form-group">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" class="user-input"
                        value="<?php echo escapeHTML($user['country'] ?? 'USA'); ?>">
                </div>
            </div>

            <button type="submit" class="user-btn user-btn-primary">
                <iconify-icon icon="solar:diskette-linear" width="18"></iconify-icon>
                Save Address
            </button>
        </section>
    </form>

    <!-- Change Password Form -->
    <form method="POST" class="user-profile-section" id="passwordForm">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="update_password" value="1">

        <section class="user-checkout-section">
            <h2>Change Password</h2>

            <div class="user-form-group">
                <label for="current_password">Current Password *</label>
                <input type="password" id="current_password" name="current_password" class="user-input" required>
            </div>

            <div class="user-form-row">
                <div class="user-form-group">
                    <label for="new_password">New Password *</label>
                    <input type="password" id="new_password" name="new_password" class="user-input" required>
                    <small>At least 6 characters</small>
                </div>

                <div class="user-form-group">
                    <label for="confirm_password">Confirm New Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="user-input" required>
                </div>
            </div>

            <button type="submit" class="user-btn user-btn-primary">
                <iconify-icon icon="solar:lock-password-linear" width="18"></iconify-icon>
                Change Password
            </button>
        </section>
    </form>
</div>

<script>
    // Confirmation dialogs for profile forms
    document.addEventListener('DOMContentLoaded', function () {
        // Personal Information Form
        const personalInfoForm = document.getElementById('personalInfoForm');
        if (personalInfoForm) {
            personalInfoForm.addEventListener('submit', function (e) {
                if (!confirm('Are you sure you want to update your personal information?')) {
                    e.preventDefault();
                }
            });
        }

        // Address Form
        const addressForm = document.getElementById('addressForm');
        if (addressForm) {
            addressForm.addEventListener('submit', function (e) {
                if (!confirm('Are you sure you want to update your address?')) {
                    e.preventDefault();
                }
            });
        }

        // Password Form
        const passwordForm = document.getElementById('passwordForm');
        if (passwordForm) {
            passwordForm.addEventListener('submit', function (e) {
                if (!confirm('Are you sure you want to change your password?')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>

<style>
    .user-profile-container {
        max-width: 48rem;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .user-profile-section {
        width: 100%;
    }
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>