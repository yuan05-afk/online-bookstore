<?php
/**
 * Admin User Accounts Management
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireAdmin();

$db = getDB();
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? sanitizeInput($_GET['role']) : '';

// Build query
$where = [];
$params = [];

if ($search) {
    $where[] = "(email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if ($role_filter) {
    $where[] = "role = ?";
    $params[] = $role_filter;
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get users
$sql = "SELECT * FROM users $whereClause ORDER BY created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

$pageTitle = 'User Accounts';
$flash = getFlashMessage();
include __DIR__ . '/../includes/admin_header.php';
?>

<h1 class="admin-page-title">User Accounts</h1>

<?php if ($flash): ?>
    <div class="admin-card admin-mb-6"
        style="border-left: 4px solid <?php echo $flash['type'] === 'success' ? 'var(--admin-success)' : 'var(--admin-danger)'; ?>;">
        <div class="admin-card-body">
            <?php echo escapeHTML($flash['message']); ?>
        </div>
    </div>
<?php endif; ?>

<!-- Filter Bar -->
<form method="GET" action="" class="admin-filter-bar">
    <div class="admin-input-icon">
        <iconify-icon icon="solar:magnifer-linear" width="18"></iconify-icon>
        <input type="text" name="search" class="admin-input" placeholder="Search by name or email..."
            value="<?php echo escapeHTML($search); ?>">
    </div>

    <select name="role" class="admin-select">
        <option value="">All Roles</option>
        <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
        <option value="user" <?php echo $role_filter === 'user' ? 'selected' : ''; ?>>User</option>
    </select>

    <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
    <?php if ($search || $role_filter): ?>
        <a href="accounts.php" class="admin-btn admin-btn-secondary">Clear</a>
    <?php endif; ?>
</form>

<!-- Accounts Table -->
<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="admin-text-center admin-text-muted" style="padding: 3rem;">
                            No users found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="admin-text-secondary">#
                                <?php echo $user['id']; ?>
                            </td>
                            <td>
                                <div style="font-weight: var(--admin-font-weight-medium); color: var(--admin-text-primary);">
                                    <?php echo escapeHTML($user['first_name'] . ' ' . $user['last_name']); ?>
                                </div>
                            </td>
                            <td class="admin-text-secondary">
                                <?php echo escapeHTML($user['email']); ?>
                            </td>
                            <td class="admin-text-secondary">
                                <?php echo escapeHTML($user['phone'] ?? '-'); ?>
                            </td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="admin-badge admin-badge-info">Admin</span>
                                <?php else: ?>
                                    <span class="admin-badge admin-badge-neutral">User</span>
                                <?php endif; ?>
                            </td>
                            <td class="admin-text-secondary">
                                <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="account_detail.php?id=<?php echo $user['id']; ?>"
                                        class="admin-btn admin-btn-sm admin-btn-secondary">
                                        View Details
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>