<?php
/**
 * Admin User Account Detail Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireAdmin();

$account_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$db = getDB();
$current_user_id = $_SESSION['user_id'];

// Get user account
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$account_id]);
$account = $stmt->fetch();

if (!$account) {
    setFlashMessage('error', 'User account not found');
    redirect(SITE_URL . '/admin/accounts.php');
}

// Get user's order history
$stmt = $db->prepare("
    SELECT * FROM orders 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 10
");
$stmt->execute([$account_id]);
$orders = $stmt->fetchAll();

$pageTitle = 'Account Details';
$csrf_token = generateCSRFToken();
$flash = getFlashMessage();
include __DIR__ . '/../includes/admin_header.php';
?>

<a href="accounts.php" class="admin-back-link">
    <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
    Back to Accounts
</a>

<div class="admin-flex-between admin-mb-6">
    <h1 class="admin-page-title" style="margin: 0;">
        <?php echo escapeHTML($account['first_name'] . ' ' . $account['last_name']); ?>
    </h1>
    <?php if ($account['role'] === 'admin'): ?>
        <span class="admin-badge admin-badge-info"
            style="font-size: var(--admin-font-size-base); padding: var(--admin-space-2) var(--admin-space-4);">
            Administrator
        </span>
    <?php else: ?>
        <span class="admin-badge admin-badge-neutral"
            style="font-size: var(--admin-font-size-base); padding: var(--admin-space-2) var(--admin-space-4);">
            User
        </span>
    <?php endif; ?>
</div>

<?php if ($flash): ?>
    <div class="admin-card admin-mb-6"
        style="border-left: 4px solid <?php echo $flash['type'] === 'success' ? 'var(--admin-success)' : 'var(--admin-danger)'; ?>;">
        <div class="admin-card-body">
            <?php echo escapeHTML($flash['message']); ?>
        </div>
    </div>
<?php endif; ?>

<!-- Account Information -->
<div class="admin-grid admin-grid-cols-2 admin-mb-6">
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Account Information</h3>
        </div>
        <div class="admin-card-body">
            <div style="margin-bottom: 1rem;">
                <div class="admin-label" style="margin-bottom: 0.25rem;">User ID</div>
                <div>#
                    <?php echo $account['id']; ?>
                </div>
            </div>
            <div style="margin-bottom: 1rem;">
                <div class="admin-label" style="margin-bottom: 0.25rem;">Email</div>
                <div>
                    <?php echo escapeHTML($account['email']); ?>
                </div>
            </div>
            <div style="margin-bottom: 1rem;">
                <div class="admin-label" style="margin-bottom: 0.25rem;">Phone</div>
                <div>
                    <?php echo escapeHTML($account['phone'] ?? 'Not provided'); ?>
                </div>
            </div>
            <div>
                <div class="admin-label" style="margin-bottom: 0.25rem;">Registered</div>
                <div>
                    <?php echo date('F j, Y g:i A', strtotime($account['created_at'])); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Manage User Role</h3>
        </div>
        <div class="admin-card-body">
            <?php if ($account_id === $current_user_id): ?>
                <div class="admin-card" style="background: var(--admin-warning-bg); border: 1px solid var(--admin-warning); padding: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--admin-warning);">
                        <iconify-icon icon="solar:info-circle-linear" width="20"></iconify-icon>
                        <strong>This is your account</strong>
                    </div>
                    <p style="margin: 0.5rem 0 0 0; font-size: var(--admin-font-size-sm);">
                        You cannot modify your own role. Use the Profile page to update your information.
                    </p>
                </div>
            <?php elseif ($account['role'] === 'admin'): ?>
                <div class="admin-card" style="background: var(--admin-info-bg); border: 1px solid var(--admin-primary); padding: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--admin-primary);">
                        <iconify-icon icon="solar:shield-user-linear" width="20"></iconify-icon>
                        <strong>Administrator Account</strong>
                    </div>
                    <p style="margin: 0.5rem 0 0 0; font-size: var(--admin-font-size-sm);">
                        This user is an administrator. Admin accounts cannot be demoted to prevent accidental loss of admin access.
                    </p>
                </div>
            <?php else: ?>
                <form method="POST" action="account_actions.php" onsubmit="return confirm('Are you sure you want to promote this user to Administrator? They will have full access to the admin panel.');">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="account_id" value="<?php echo $account_id; ?>">
                    <input type="hidden" name="action" value="promote_to_admin">
                    
                    <div class="admin-label" style="margin-bottom: 0.5rem;">Current Role</div>
                    <div style="margin-bottom: 1rem;">
                        <span class="admin-badge admin-badge-neutral">User</span>
                    </div>
                    
                    <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">
                        <iconify-icon icon="solar:shield-user-linear" width="18"></iconify-icon>
                        Promote to Administrator
                    </button>
                    <p style="margin: 0.75rem 0 0 0; font-size: var(--admin-font-size-xs); color: var(--admin-text-secondary);">
                        This action requires confirmation and cannot be undone easily.
                    </p>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Order History -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Recent Orders (Last 10)</h3>
    </div>
    <?php if (empty($orders)): ?>
        <div class="admin-card-body">
            <p class="admin-text-muted">No orders found for this user.</p>
        </div>
    <?php else: ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <strong>
                                    <?php echo escapeHTML($order['order_number']); ?>
                                </strong>
                            </td>
                            <td class="admin-text-secondary">
                                <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = 'neutral';
                                $status = strtolower($order['status']);
                                if ($status === 'delivered')
                                    $statusClass = 'success';
                                elseif ($status === 'processing' || $status === 'shipped')
                                    $statusClass = 'warning';
                                elseif ($status === 'cancelled')
                                    $statusClass = 'danger';
                                ?>
                                <span class="admin-badge admin-badge-<?php echo $statusClass; ?>">
                                    <?php echo escapeHTML($order['status']); ?>
                                </span>
                            </td>
                            <td class="text-right">
                                <strong>
                                    <?php echo formatPrice($order['grand_total']); ?>
                                </strong>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="order_detail.php?id=<?php echo $order['id']; ?>"
                                        class="admin-btn admin-btn-sm admin-btn-secondary">
                                        View Order
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>