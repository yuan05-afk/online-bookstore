<?php
/**
 * Admin Book Management
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireAdmin();

$db = getDB();
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category_id = isset($_GET['category']) ? (int) $_GET['category'] : null;

// Build query
$where = [];
$params = [];

if ($search) {
    $where[] = "(title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if ($category_id) {
    $where[] = "category_id = ?";
    $params[] = $category_id;
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get books
$sql = "
    SELECT b.*, c.name as category_name
    FROM books b
    LEFT JOIN categories c ON b.category_id = c.id
    $whereClause
    ORDER BY b.title ASC
";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

// Get categories
$categoriesStmt = $db->query("SELECT * FROM categories ORDER BY name");
$categories = $categoriesStmt->fetchAll();

$pageTitle = 'Manage Books';
$flash = getFlashMessage();
include __DIR__ . '/../includes/admin_header.php';
?>

<div class="admin-flex-between admin-mb-6">
    <h1 class="admin-page-title" style="margin: 0;">Book Management</h1>
    <a href="book_form.php" class="admin-btn admin-btn-primary">
        <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
        Add New Book
    </a>
</div>

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
        <input type="text" name="search" class="admin-input" placeholder="Search books..."
            value="<?php echo escapeHTML($search); ?>">
    </div>

    <select name="category" class="admin-select">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                <?php echo escapeHTML($cat['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
    <?php if ($search || $category_id): ?>
        <a href="books.php" class="admin-btn admin-btn-secondary">Clear</a>
    <?php endif; ?>
</form>

<!-- Books Table -->
<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Cover</th>
                    <th>Book Info</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($books)): ?>
                    <tr>
                        <td colspan="6" class="admin-text-center admin-text-muted" style="padding: 3rem;">
                            No books found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td>
                                <?php if ($book['cover_image']): ?>
                                    <img src="<?php echo escapeHTML($book['cover_image']); ?>"
                                        alt="<?php echo escapeHTML($book['title']); ?>"
                                        style="width: 2.5rem; height: 3.5rem; object-fit: cover; border-radius: var(--admin-radius-sm); border: 1px solid var(--admin-border-light);">
                                <?php else: ?>
                                    <div
                                        style="width: 2.5rem; height: 3.5rem; background: var(--admin-bg-tertiary); border-radius: var(--admin-radius-sm); display: flex; align-items: center; justify-content: center;">
                                        <iconify-icon icon="solar:book-2-linear" width="20"
                                            style="color: var(--admin-text-muted);"></iconify-icon>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div
                                    style="font-weight: var(--admin-font-weight-medium); color: var(--admin-text-primary); margin-bottom: 0.25rem;">
                                    <?php echo escapeHTML($book['title']); ?>
                                </div>
                                <div style="font-size: var(--admin-font-size-xs); color: var(--admin-text-secondary);">
                                    <?php echo escapeHTML($book['author']); ?>
                                </div>
                            </td>
                            <td class="admin-text-secondary">
                                <?php echo escapeHTML($book['category_name']); ?>
                            </td>
                            <td>
                                <strong><?php echo formatPrice($book['price']); ?></strong>
                            </td>
                            <td>
                                <?php if ($book['stock_quantity'] < 10): ?>
                                    <span
                                        class="admin-badge admin-badge-<?php echo $book['stock_quantity'] == 0 ? 'danger' : 'warning'; ?>">
                                        <?php echo $book['stock_quantity']; ?> In Stock
                                    </span>
                                <?php else: ?>
                                    <span class="admin-badge admin-badge-success">
                                        <?php echo $book['stock_quantity']; ?> In Stock
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="book_form.php?id=<?php echo $book['id']; ?>" class="admin-btn-icon" title="Edit">
                                        <iconify-icon icon="solar:pen-linear" width="18"></iconify-icon>
                                    </a>
                                    <a href="book_actions.php?action=delete&id=<?php echo $book['id']; ?>"
                                        class="admin-btn-icon" style="color: var(--admin-danger);" title="Delete"
                                        onclick="return confirm('Are you sure you want to delete this book?')">
                                        <iconify-icon icon="solar:trash-bin-trash-linear" width="18"></iconify-icon>
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