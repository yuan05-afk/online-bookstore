<?php
/**
 * Admin Book Form (Add/Edit)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireAdmin();

$db = getDB();
$book_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$isEdit = $book_id > 0;

$book = null;
if ($isEdit) {
    $stmt = $db->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();

    if (!$book) {
        setFlashMessage('error', 'Book not found');
        redirect(SITE_URL . '/admin/books.php');
    }
}

// Get categories
$categoriesStmt = $db->query("SELECT * FROM categories ORDER BY name");
$categories = $categoriesStmt->fetchAll();

$pageTitle = $isEdit ? 'Edit Book' : 'Add New Book';
$csrf_token = generateCSRFToken();
include __DIR__ . '/../includes/admin_header.php';
?>

<a href="books.php" class="admin-back-link">
    <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
    Back to Books
</a>

<h1 class="admin-page-title"><?php echo $pageTitle; ?></h1>

<div class="admin-card">
    <div class="admin-card-body">
        <form method="POST" action="book_actions.php">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'create'; ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?php echo $book_id; ?>">
            <?php endif; ?>

            <div class="admin-grid admin-grid-cols-2">
                <div class="admin-form-group">
                    <label for="isbn" class="admin-label">ISBN *</label>
                    <input type="text" id="isbn" name="isbn" class="admin-input"
                        value="<?php echo $book ? escapeHTML($book['isbn']) : ''; ?>" required <?php echo $isEdit ? 'readonly' : ''; ?>>
                </div>

                <div class="admin-form-group">
                    <label for="category_id" class="admin-label">Category *</label>
                    <select id="category_id" name="category_id" class="admin-select" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($book && $book['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo escapeHTML($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="admin-form-group">
                <label for="title" class="admin-label">Title *</label>
                <input type="text" id="title" name="title" class="admin-input"
                    value="<?php echo $book ? escapeHTML($book['title']) : ''; ?>" required>
            </div>

            <div class="admin-form-group">
                <label for="author" class="admin-label">Author *</label>
                <input type="text" id="author" name="author" class="admin-input"
                    value="<?php echo $book ? escapeHTML($book['author']) : ''; ?>" required>
            </div>

            <div class="admin-grid admin-grid-cols-2">
                <div class="admin-form-group">
                    <label for="price" class="admin-label">Price *</label>
                    <input type="number" id="price" name="price" class="admin-input" step="0.01" min="0"
                        value="<?php echo $book ? $book['price'] : ''; ?>" required>
                </div>

                <div class="admin-form-group">
                    <label for="stock_quantity" class="admin-label">Stock Quantity *</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" class="admin-input" min="0"
                        value="<?php echo $book ? $book['stock_quantity'] : '0'; ?>" required>
                </div>
            </div>

            <div class="admin-form-group">
                <label for="description" class="admin-label">Description</label>
                <textarea id="description" name="description" class="admin-textarea"
                    rows="5"><?php echo $book ? escapeHTML($book['description']) : ''; ?></textarea>
            </div>

            <div class="admin-form-group">
                <label for="cover_image" class="admin-label">Cover Image URL</label>
                <?php if ($book && $book['cover_image']): ?>
                    <div
                        style="margin-bottom: 1rem; padding: 1rem; background: var(--admin-bg-tertiary); border-radius: var(--admin-radius-md);">
                        <img src="<?php echo escapeHTML($book['cover_image']); ?>" alt="Current cover"
                            style="max-width: 200px; border-radius: var(--admin-radius-md); border: 1px solid var(--admin-border-light);">
                        <p
                            style="margin-top: 0.5rem; font-size: var(--admin-font-size-sm); color: var(--admin-text-secondary);">
                            Current image</p>
                    </div>
                <?php endif; ?>
                <input type="url" id="cover_image" name="cover_image" class="admin-input"
                    value="<?php echo $book ? escapeHTML($book['cover_image']) : ''; ?>"
                    placeholder="https://example.com/book-cover.jpg">
                <small class="admin-text-secondary"
                    style="font-size: var(--admin-font-size-xs); display: block; margin-top: 0.5rem;">
                    Enter the direct URL to the book cover image
                </small>
            </div>

            <div class="admin-flex admin-flex-gap-4" style="margin-top: 2rem;">
                <button type="submit" class="admin-btn admin-btn-primary">
                    <?php echo $isEdit ? 'Update Book' : 'Add Book'; ?>
                </button>
                <a href="books.php" class="admin-btn admin-btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>