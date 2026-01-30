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

<h1 class="user-page-title">Book Catalog</h1>

<div class="user-catalog-layout">
    <aside class="user-sidebar">
        <div class="user-sidebar-section">
            <h2 class="user-sidebar-title">Categories</h2>
            <ul class="user-category-list">
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
        </div>
    </aside>

    <main class="user-catalog-main">
        <div class="user-catalog-header">
            <form method="GET" action="" class="user-search-bar">
                <?php if ($category_id): ?>
                    <input type="hidden" name="category" value="<?php echo $category_id; ?>">
                <?php endif; ?>
                <iconify-icon icon="solar:magnifer-linear" class="user-search-icon" width="18"
                    stroke-width="1.5"></iconify-icon>
                <input type="text" name="search" class="user-search-input"
                    placeholder="Search by title, author, or ISBN..." value="<?php echo escapeHTML($search); ?>">
            </form>
        </div>

        <?php if ($search): ?>
            <p style="font-size: 0.875rem; color: var(--user-zinc-500); margin-bottom: 1.5rem;">
                Found <?php echo $totalBooks; ?> result(s) for "<?php echo escapeHTML($search); ?>"
                <a href="catalog.php<?php echo $category_id ? '?category=' . $category_id : ''; ?>"
                    style="color: var(--user-zinc-900); text-decoration: underline; margin-left: 0.5rem;">Clear</a>
            </p>
        <?php endif; ?>

        <?php if (empty($books)): ?>
            <div class="user-cart-empty">
                <p>No books found.</p>
                <a href="catalog.php" class="user-btn user-btn-primary">View All Books</a>
            </div>
        <?php else: ?>
            <div class="user-books-grid user-fade-in">
                <?php foreach ($books as $book): ?>
                    <div class="user-book-card">
                        <div class="user-book-image-wrapper">
                            <?php if ($book['cover_image']): ?>
                                <img src="<?php echo SITE_URL; ?>/assets/images/books/<?php echo escapeHTML($book['cover_image']); ?>"
                                    alt="<?php echo escapeHTML($book['title']); ?>">
                            <?php else: ?>
                                <div class="user-book-placeholder">No Image</div>
                            <?php endif; ?>
                            <div class="user-book-overlay">
                                <a href="book_detail.php?id=<?php echo $book['id']; ?>" class="user-overlay-btn"
                                    title="View Details">
                                    <iconify-icon icon="solar:eye-linear" width="20" stroke-width="1.5"></iconify-icon>
                                </a>
                                <?php if ($book['stock_quantity'] > 0): ?>
                                    <button class="user-overlay-btn add-to-cart" data-book-id="<?php echo $book['id']; ?>"
                                        title="Add to Cart">
                                        <iconify-icon icon="solar:cart-plus-linear" width="20" stroke-width="1.5"></iconify-icon>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <h3 class="user-book-title">
                            <a href="book_detail.php?id=<?php echo $book['id']; ?>">
                                <?php echo escapeHTML($book['title']); ?>
                            </a>
                        </h3>
                        <p class="user-book-author">
                            <?php echo escapeHTML($book['author']); ?>
                        </p>
                        <div class="user-book-footer">
                            <span class="user-book-price">
                                <?php echo formatPrice($book['price']); ?>
                            </span>
                            <?php if ($book['stock_quantity'] > 10): ?>
                                <span class="user-stock-badge in-stock">In Stock</span>
                            <?php elseif ($book['stock_quantity'] > 0): ?>
                                <span class="user-stock-badge low-stock">Low Stock</span>
                            <?php else: ?>
                                <span class="user-stock-badge out-of-stock">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="user-pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
                            class="user-pagination-btn">
                            <iconify-icon icon="solar:alt-arrow-left-linear" width="16"></iconify-icon>
                        </a>
                    <?php endif; ?>

                    <span class="user-pagination-info">
                        Page <?php echo $page; ?> of <?php echo $totalPages; ?>
                    </span>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
                            class="user-pagination-btn">
                            <iconify-icon icon="solar:alt-arrow-right-linear" width="16"></iconify-icon>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>