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

<div class="admin-content">
    <div class="content-header">
        <h1>
            <?php echo $pageTitle; ?>
        </h1>
        <a href="books.php" class="btn btn-secondary">Back to Books</a>
    </div>

    <form method="POST" action="book_actions.php" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'create'; ?>">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?php echo $book_id; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="isbn">ISBN *</label>
                <input type="text" id="isbn" name="isbn" value="<?php echo $book ? escapeHTML($book['isbn']) : ''; ?>"
                    required <?php echo $isEdit ? 'readonly' : ''; ?>>
            </div>

            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($book && $book['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo escapeHTML($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="title">Title *</label>
            <input type="text" id="title" name="title" value="<?php echo $book ? escapeHTML($book['title']) : ''; ?>"
                required>
        </div>

        <div class="form-group">
            <label for="author">Author *</label>
            <input type="text" id="author" name="author" value="<?php echo $book ? escapeHTML($book['author']) : ''; ?>"
                required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="price">Price *</label>
                <input type="number" id="price" name="price" step="0.01" min="0"
                    value="<?php echo $book ? $book['price'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="stock_quantity">Stock Quantity *</label>
                <input type="number" id="stock_quantity" name="stock_quantity" min="0"
                    value="<?php echo $book ? $book['stock_quantity'] : '0'; ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description"
                rows="5"><?php echo $book ? escapeHTML($book['description']) : ''; ?></textarea>
        </div>

        <div class="form-group">
            <label for="cover_image">Cover Image URL</label>
            <?php if ($book && $book['cover_image']): ?>
                <div class="current-image">
                    <img src="<?php echo escapeHTML($book['cover_image']); ?>" alt="Current cover"
                        style="max-width: 200px; border-radius: 0.5rem; border: 1px solid #ddd;">
                    <p style="margin-top: 0.5rem; font-size: 0.875rem; color: #666;">Current image URL</p>
                </div>
            <?php endif; ?>
            <input type="url" id="cover_image" name="cover_image"
                value="<?php echo $book ? escapeHTML($book['cover_image']) : ''; ?>"
                placeholder="https://example.com/book-cover.jpg">
            <small>Enter the direct URL to the book cover image (right-click image → Copy Image Address)</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?php echo $isEdit ? 'Update Book' : 'Add Book'; ?>
            </button>
            <a href="books.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>