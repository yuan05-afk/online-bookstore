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

<div class="book-detail-container">
    <div class="book-detail-main">
        <div class="book-detail-image">
            <?php if ($book['cover_image']): ?>
                <img src="<?php echo SITE_URL; ?>/assets/images/books/<?php echo escapeHTML($book['cover_image']); ?>"
                    alt="<?php echo escapeHTML($book['title']); ?>">
            <?php else: ?>
                <div class="book-placeholder-large">No Image Available</div>
            <?php endif; ?>
        </div>

        <div class="book-detail-info">
            <h1>
                <?php echo escapeHTML($book['title']); ?>
            </h1>
            <p class="book-author-large">by
                <?php echo escapeHTML($book['author']); ?>
            </p>

            <div class="book-meta">
                <p><strong>ISBN:</strong>
                    <?php echo escapeHTML($book['isbn']); ?>
                </p>
                <p><strong>Category:</strong>
                    <a href="catalog.php?category=<?php echo $book['category_id']; ?>">
                        <?php echo escapeHTML($book['category_name']); ?>
                    </a>
                </p>
                <p><strong>Price:</strong> <span class="price-large">
                        <?php echo formatPrice($book['price']); ?>
                    </span></p>
                <p><strong>Availability:</strong>
                    <?php if ($book['stock_quantity'] > 0): ?>
                        <span class="in-stock">In Stock (
                            <?php echo $book['stock_quantity']; ?> available)
                        </span>
                    <?php else: ?>
                        <span class="out-of-stock">Out of Stock</span>
                    <?php endif; ?>
                </p>
            </div>

            <div class="book-description">
                <h2>Description</h2>
                <p>
                    <?php echo nl2br(escapeHTML($book['description'])); ?>
                </p>
            </div>

            <?php if ($book['stock_quantity'] > 0): ?>
                <div class="book-actions-detail">
                    <div class="quantity-selector">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1"
                            max="<?php echo $book['stock_quantity']; ?>">
                    </div>
                    <button class="btn btn-primary btn-lg add-to-cart" data-book-id="<?php echo $book['id']; ?>">
                        Add to Cart
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($relatedBooks)): ?>
        <div class="related-books">
            <h2>Related Books</h2>
            <div class="books-grid">
                <?php foreach ($relatedBooks as $relatedBook): ?>
                    <div class="book-card">
                        <div class="book-image">
                            <?php if ($relatedBook['cover_image']): ?>
                                <img src="<?php echo SITE_URL; ?>/assets/images/books/<?php echo escapeHTML($relatedBook['cover_image']); ?>"
                                    alt="<?php echo escapeHTML($relatedBook['title']); ?>">
                            <?php else: ?>
                                <div class="book-placeholder">No Image</div>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <h3 class="book-title">
                                <a href="book_detail.php?id=<?php echo $relatedBook['id']; ?>">
                                    <?php echo escapeHTML($relatedBook['title']); ?>
                                </a>
                            </h3>
                            <p class="book-author">
                                <?php echo escapeHTML($relatedBook['author']); ?>
                            </p>
                            <p class="book-price">
                                <?php echo formatPrice($relatedBook['price']); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>