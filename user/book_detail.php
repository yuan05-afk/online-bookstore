<?php
/**
 * Book Detail Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireUser();

$book_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($book_id <= 0) {
    redirect(SITE_URL . '/user/catalog.php');
}

$db = getDB();
$stmt = $db->prepare("
    SELECT b.*, c.name as category_name, c.id as category_id
    FROM books b
    LEFT JOIN categories c ON b.category_id = c.id
    WHERE b.id = ?
");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if (!$book) {
    setFlashMessage('error', 'Book not found');
    redirect(SITE_URL . '/user/catalog.php');
}

// Get related books from same category
$relatedStmt = $db->prepare("
    SELECT * FROM books
    WHERE category_id = ? AND id != ?
    ORDER BY RAND()
    LIMIT 4
");
$relatedStmt->execute([$book['category_id'], $book_id]);
$relatedBooks = $relatedStmt->fetchAll();

$pageTitle = $book['title'];
include __DIR__ . '/../includes/header.php';
?>

<a href="catalog.php" class="user-back-link">
    <iconify-icon icon="solar:alt-arrow-left-linear" width="16"></iconify-icon>
    Back to Catalog
</a>

<div class="user-detail-grid">
    <div class="user-detail-image">
        <?php if ($book['cover_image']): ?>
            <img src="<?php echo escapeHTML($book['cover_image'] ?: SITE_URL . '/assets/images/placeholder.jpg'); ?>"
                alt="<?php echo escapeHTML($book['title']); ?>">
        <?php else: ?>
            <div class="user-book-placeholder">No Image Available</div>
        <?php endif; ?>
    </div>

    <div class="user-detail-info">
        <div>
            <h1 class="user-detail-title">
                <?php echo escapeHTML($book['title']); ?>
            </h1>
            <p class="user-detail-author">by <?php echo escapeHTML($book['author']); ?></p>
        </div>

        <div class="user-detail-price">
            <?php echo formatPrice($book['price']); ?>
        </div>

        <div class="user-detail-meta">
            <p><strong>ISBN:</strong> <?php echo escapeHTML($book['isbn']); ?></p>
            <p>
                <strong>Category:</strong>
                <a href="catalog.php?category=<?php echo $book['category_id']; ?>"
                    style="color: var(--user-zinc-900); text-decoration: underline;">
                    <?php echo escapeHTML($book['category_name']); ?>
                </a>
            </p>
            <p>
                <strong>Availability:</strong>
                <?php if ($book['stock_quantity'] > 10): ?>
                    <span class="user-stock-badge in-stock">In Stock (<?php echo $book['stock_quantity']; ?>
                        available)</span>
                <?php elseif ($book['stock_quantity'] > 0): ?>
                    <span class="user-stock-badge low-stock">Low Stock (<?php echo $book['stock_quantity']; ?>
                        available)</span>
                <?php else: ?>
                    <span class="user-stock-badge out-of-stock">Out of Stock</span>
                <?php endif; ?>
            </p>
        </div>

        <div>
            <h2 style="font-size: 0.875rem; font-weight: 600; color: var(--user-zinc-900); margin-bottom: 0.75rem;">
                Description</h2>
            <p class="user-detail-description">
                <?php echo nl2br(escapeHTML($book['description'])); ?>
            </p>
        </div>

        <?php if ($book['stock_quantity'] > 0): ?>
            <div class="user-quantity-selector">
                <div class="user-quantity-controls">
                    <button class="user-quantity-btn" onclick="decrementQuantity()">
                        <iconify-icon icon="solar:minus-linear" width="18"></iconify-icon>
                    </button>
                    <span class="user-quantity-value" id="quantity">1</span>
                    <button class="user-quantity-btn" onclick="incrementQuantity(<?php echo $book['stock_quantity']; ?>)">
                        <iconify-icon icon="solar:add-linear" width="18"></iconify-icon>
                    </button>
                </div>
            </div>

            <div class="user-detail-actions">
                <button class="user-btn user-btn-primary add-to-cart" data-book-id="<?php echo $book['id']; ?>">
                    <iconify-icon icon="solar:cart-plus-linear" width="20"></iconify-icon>
                    Add to Cart
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($relatedBooks)): ?>
    <div class="user-related-section">
        <h2 class="user-related-title">Related Books</h2>
        <div class="user-books-grid">
            <?php foreach ($relatedBooks as $relatedBook): ?>
                <div class="user-book-card">
                    <div class="user-book-image-wrapper">
                        <?php if ($relatedBook['cover_image']): ?>
                            <img src="<?php echo escapeHTML($relatedBook['cover_image'] ?: SITE_URL . '/assets/images/placeholder.jpg'); ?>"
                                alt="<?php echo escapeHTML($relatedBook['title']); ?>">
                        <?php else: ?>
                            <div class="user-book-placeholder">No Image</div>
                        <?php endif; ?>
                    </div>
                    <h3 class="user-book-title">
                        <a href="book_detail.php?id=<?php echo $relatedBook['id']; ?>">
                            <?php echo escapeHTML($relatedBook['title']); ?>
                        </a>
                    </h3>
                    <p class="user-book-author">
                        <?php echo escapeHTML($relatedBook['author']); ?>
                    </p>
                    <div class="user-book-footer">
                        <span class="user-book-price">
                            <?php echo formatPrice($relatedBook['price']); ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<script>
    function incrementQuantity(max) {
        const el = document.getElementById('quantity');
        const current = parseInt(el.textContent);
        if (current < max) el.textContent = current + 1;
    }
    function decrementQuantity() {
        const el = document.getElementById('quantity');
        const current = parseInt(el.textContent);
        if (current > 1) el.textContent = current - 1;
    }
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>