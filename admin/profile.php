<?php
/**
 * Admin Profile Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireAdmin();

$db = getDB();
$user_id = $_SESSION['user_id'];

// Get current admin user data
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$admin = $stmt->fetch();

if (!$admin) {
    setFlashMessage('error', 'User not found');
    redirect(SITE_URL . '/admin/dashboard.php');
}

$pageTitle = 'My Profile';
$csrf_token = generateCSRFToken();
$flash = getFlashMessage();
include __DIR__ . '/../includes/admin_header.php';
?>

<h1 class="admin-page-title">My Profile</h1>

<?php if ($flash): ?>
    <div class="admin-card admin-mb-6"
        style="border-left: 4px solid <?php echo $flash['type'] === 'success' ? 'var(--admin-success)' : 'var(--admin-danger)'; ?>;">
        <div class="admin-card-body">
            <?php echo escapeHTML($flash['message']); ?>
        </div>
    </div>
<?php endif; ?>

<div class="admin-grid admin-grid-cols-2 admin-mb-6">
    <!-- Profile Information -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Profile Information</h3>
        </div>
        <div class="admin-card-body">
            <form method="POST" action="profile_actions.php">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="update_profile">

                <div class="admin-form-group">
                    <label for="first_name" class="admin-label">First Name *</label>
                    <input type="text" id="first_name" name="first_name" class="admin-input"
                        value="<?php echo escapeHTML($admin['first_name']); ?>" required>
                </div>

                <div class="admin-form-group">
                    <label for="last_name" class="admin-label">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" class="admin-input"
                        value="<?php echo escapeHTML($admin['last_name']); ?>" required>
                </div>

                <div class="admin-form-group">
                    <label for="email" class="admin-label">Email *</label>
                    <input type="email" id="email" name="email" class="admin-input"
                        value="<?php echo escapeHTML($admin['email']); ?>" required>
                    <small class="admin-text-secondary"
                        style="font-size: var(--admin-font-size-xs); display: block; margin-top: 0.5rem;">
                        Used for login and notifications
                    </small>
                </div>

                <div class="admin-form-group">
                    <label for="phone" class="admin-label">Phone</label>
                    <input type="tel" id="phone" name="phone" class="admin-input"
                        value="<?php echo escapeHTML($admin['phone'] ?? ''); ?>">
                </div>

                <button type="submit" class="admin-btn admin-btn-primary">
                    <iconify-icon icon="solar:check-circle-linear" width="18"></iconify-icon>
                    Update Profile
                </button>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Change Password</h3>
        </div>
        <div class="admin-card-body">
            <form method="POST" action="profile_actions.php">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="change_password">

                <div class="admin-form-group">
                    <label for="current_password" class="admin-label">Current Password *</label>
                    <input type="password" id="current_password" name="current_password" class="admin-input" required>
                </div>

                <div class="admin-form-group">
                    <label for="new_password" class="admin-label">New Password *</label>
                    <input type="password" id="new_password" name="new_password" class="admin-input" required
                        minlength="8">
                    <small class="admin-text-secondary"
                        style="font-size: var(--admin-font-size-xs); display: block; margin-top: 0.5rem;">
                        Minimum 8 characters
                    </small>
                </div>

                <div class="admin-form-group">
                    <label for="confirm_password" class="admin-label">Confirm New Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="admin-input" required
                        minlength="8">
                </div>

                <button type="submit" class="admin-btn admin-btn-primary">
                    <iconify-icon icon="solar:lock-password-linear" width="18"></iconify-icon>
                    Change Password
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Account Details -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Account Details</h3>
    </div>
    <div class="admin-card-body">
        <div class="admin-grid admin-grid-cols-3">
            <div>
                <div class="admin-label" style="margin-bottom: 0.5rem;">Role</div>
                <span class="admin-badge admin-badge-info">Administrator</span>
            </div>
            <div>
                <div class="admin-label" style="margin-bottom: 0.5rem;">Account Status</div>
                <span class="admin-badge admin-badge-success">Active</span>
            </div>
            <div>
                <div class="admin-label" style="margin-bottom: 0.5rem;">Member Since</div>
                <div>
                    <?php echo date('F j, Y', strtotime($admin['created_at'])); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>