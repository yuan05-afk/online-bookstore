<?php
/**
 * Book Catalog Page
 * Browse and search books
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireUser();

// Get filter parameters
$category_id = isset($_GET['category']) ? (int) $_GET['category'] : null;
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * ITEMS_PER_PAGE;

// Build query
$db = getDB();
$where = [];
$params = [];

if ($category_id) {
    $where[] = "b.category_id = ?";
    $params[] = $category_id;
}

if ($search) {
    $where[] = "(b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$countSql = "SELECT COUNT(*) FROM books b $whereClause";
$stmt = $db->prepare($countSql);
$stmt->execute($params);
$totalBooks = $stmt->fetchColumn();
$totalPages = ceil($totalBooks / ITEMS_PER_PAGE);

// Get books
$sql = "
    SELECT b.*, c.name as category_name
    FROM books b
    LEFT JOIN categories c ON b.category_id = c.id
    $whereClause
    ORDER BY b.title ASC
    LIMIT ? OFFSET ?
";
$params[] = ITEMS_PER_PAGE;
$params[] = $offset;
$stmt = $db->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

// Get all categories for filter
$categoriesStmt = $db->query("SELECT * FROM categories ORDER BY name");
$categories = $categoriesStmt->fetchAll();

$pageTitle = 'Book Catalog';
include __DIR__ . '/../includes/header.php';
?>

<div class="catalog-container">
    <aside class="catalog-sidebar">
        <h2>Categories</h2>
        <ul class="category-list">
            <li>
                <a href="catalog.php" class="<?php echo !$category_id ? 'active' : ''; ?>">
                    All Books
                </a>
            </li>
            <?php foreach ($categories as $cat): ?>
                <li>
                    <a href="catalog.php?category=<?php echo $cat['id']; ?>"
                        class="<?php echo $category_id == $cat['id'] ? 'active' : ''; ?>">
                        <?php echo escapeHTML($cat['name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <main class="catalog-main">
        <div class="catalog-header">
            <h1>
                <?php echo $pageTitle; ?>
            </h1>
            <form method="GET" action="" class="search-form">
                <?php if ($category_id): ?>
                    <input type="hidden" name="category" value="<?php echo $category_id; ?>">
                <?php endif; ?>
                <input type="text" name="search" placeholder="Search by title, author, or ISBN..."
                    value="<?php echo escapeHTML($search); ?>">
                <button type="submit" class="btn btn-secondary">Search</button>
            </form>
        </div>

        <?php if ($search): ?>
            <p class="search-results">
                Found
                <?php echo $totalBooks; ?> result(s) for "
                <?php echo escapeHTML($search); ?>"
                <a href="catalog.php<?php echo $category_id ? '?category=' . $category_id : ''; ?>"
                    class="clear-search">Clear</a>
            </p>
        <?php endif; ?>

        <?php if (empty($books)): ?>
            <div class="no-results">
                <p>No books found.</p>
            </div>
        <?php else: ?>
            <div class="books-grid">
                <?php foreach ($books as $book): ?>
                    <div class="book-card">
                        <div class="book-image">
                            <?php if ($book['cover_image']): ?>
                                <img src="<?php echo SITE_URL; ?>/assets/images/books/<?php echo escapeHTML($book['cover_image']); ?>"
                                    alt="<?php echo escapeHTML($book['title']); ?>">
                            <?php else: ?>
                                <div class="book-placeholder">No Image</div>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <h3 class="book-title">
                                <a href="book_detail.php?id=<?php echo $book['id']; ?>">
                                    <?php echo escapeHTML($book['title']); ?>
                                </a>
                            </h3>
                            <p class="book-author">
                                <?php echo escapeHTML($book['author']); ?>
                            </p>
                            <p class="book-price">
                                <?php echo formatPrice($book['price']); ?>
                            </p>
                            <div class="book-actions">
                                <?php if ($book['stock_quantity'] > 0): ?>
                                    <button class="btn btn-primary btn-sm add-to-cart" data-book-id="<?php echo $book['id']; ?>">
                                        Add to Cart
                                    </button>
                                <?php else: ?>
                                    <span class="out-of-stock">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
                            class="btn btn-secondary">Previous</a>
                    <?php endif; ?>

                    <span class="page-info">Page
                        <?php echo $page; ?> of
                        <?php echo $totalPages; ?>
                    </span>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
                            class="btn btn-secondary">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>