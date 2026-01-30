<?php
/**
 * Admin Book Actions (Create, Update, Delete)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireAdmin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$db = getDB();

try {
    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid security token');
            }

            // Sanitize inputs
            $isbn = sanitizeInput($_POST['isbn']);
            $title = sanitizeInput($_POST['title']);
            $author = sanitizeInput($_POST['author']);
            $price = (float) $_POST['price'];
            $stock_quantity = (int) $_POST['stock_quantity'];
            $description = sanitizeInput($_POST['description'] ?? '');
            $category_id = (int) $_POST['category_id'];

            // Validate ISBN
            if (!validateISBN($isbn)) {
                throw new Exception('Invalid ISBN format');
            }

            // Check if ISBN already exists
            $stmt = $db->prepare("SELECT id FROM books WHERE isbn = ?");
            $stmt->execute([$isbn]);
            if ($stmt->fetch()) {
                throw new Exception('ISBN already exists');
            }

            // Handle image upload
            $cover_image = null;
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $errors = validateFileUpload($_FILES['cover_image']);
                if (!empty($errors)) {
                    throw new Exception(implode(', ', $errors));
                }
                $cover_image = uploadFile($_FILES['cover_image'], 'book_');
            }

            // Insert book
            $stmt = $db->prepare("
                INSERT INTO books (isbn, title, author, price, description, cover_image, category_id, stock_quantity)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$isbn, $title, $author, $price, $description, $cover_image, $category_id, $stock_quantity]);

            setFlashMessage('success', 'Book added successfully');
            redirect(SITE_URL . '/admin/books.php');
            break;

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid security token');
            }

            $id = (int) $_POST['id'];
            $title = sanitizeInput($_POST['title']);
            $author = sanitizeInput($_POST['author']);
            $price = (float) $_POST['price'];
            $stock_quantity = (int) $_POST['stock_quantity'];
            $description = sanitizeInput($_POST['description'] ?? '');
            $category_id = (int) $_POST['category_id'];

            // Get current book
            $stmt = $db->prepare("SELECT * FROM books WHERE id = ?");
            $stmt->execute([$id]);
            $book = $stmt->fetch();

            if (!$book) {
                throw new Exception('Book not found');
            }

            $cover_image = $book['cover_image'];

            // Handle image upload
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $errors = validateFileUpload($_FILES['cover_image']);
                if (!empty($errors)) {
                    throw new Exception(implode(', ', $errors));
                }

                // Delete old image
                if ($cover_image) {
                    deleteFile($cover_image);
                }

                $cover_image = uploadFile($_FILES['cover_image'], 'book_');
            }

            // Update book
            $stmt = $db->prepare("
                UPDATE books 
                SET title = ?, author = ?, price = ?, description = ?, 
                    cover_image = ?, category_id = ?, stock_quantity = ?
                WHERE id = ?
            ");
            $stmt->execute([$title, $author, $price, $description, $cover_image, $category_id, $stock_quantity, $id]);

            setFlashMessage('success', 'Book updated successfully');
            redirect(SITE_URL . '/admin/books.php');
            break;

        case 'delete':
            $id = (int) ($_GET['id'] ?? 0);

            // Check if book is in any orders
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM order_items WHERE book_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch();

            if ($result['count'] > 0) {
                setFlashMessage('error', 'Cannot delete book that has been ordered. Consider setting stock to 0 instead.');
                redirect(SITE_URL . '/admin/books.php');
            }

            // Get book to delete image
            $stmt = $db->prepare("SELECT cover_image FROM books WHERE id = ?");
            $stmt->execute([$id]);
            $book = $stmt->fetch();

            if ($book) {
                // Delete image
                if ($book['cover_image']) {
                    deleteFile($book['cover_image']);
                }

                // Delete book
                $stmt = $db->prepare("DELETE FROM books WHERE id = ?");
                $stmt->execute([$id]);

                setFlashMessage('success', 'Book deleted successfully');
            } else {
                setFlashMessage('error', 'Book not found');
            }

            redirect(SITE_URL . '/admin/books.php');
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    setFlashMessage('error', $e->getMessage());
    redirect(SITE_URL . '/admin/books.php');
}
