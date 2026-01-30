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

<div class="admin-content">
    <div class="content-header">
        <h1>Manage Books</h1>
        <a href="book_form.php" class="btn btn-primary">Add New Book</a>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?>">
            <?php echo escapeHTML($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="filters">
        <form method="GET" action="" class="filter-form">
            <input type="text" name="search" placeholder="Search books..." value="<?php echo escapeHTML($search); ?>">

            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo escapeHTML($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-secondary">Filter</button>
            <?php if ($search || $category_id): ?>
                <a href="books.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ISBN</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($books)): ?>
                <tr>
                    <td colspan="7" class="text-center">No books found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td>
                            <?php echo escapeHTML($book['isbn']); ?>
                        </td>
                        <td>
                            <?php echo escapeHTML($book['title']); ?>
                        </td>
                        <td>
                            <?php echo escapeHTML($book['author']); ?>
                        </td>
                        <td>
                            <?php echo escapeHTML($book['category_name']); ?>
                        </td>
                        <td>
                            <?php echo formatPrice($book['price']); ?>
                        </td>
                        <td class="<?php echo $book['stock_quantity'] < 10 ? 'text-warning' : ''; ?>">
                            <?php echo $book['stock_quantity']; ?>
                        </td>
                        <td class="action-buttons">
                            <a href="book_form.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                            <a href="book_actions.php?action=delete&id=<?php echo $book['id']; ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>