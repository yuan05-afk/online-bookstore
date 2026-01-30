<?php
/**
 * Shopping Cart API
 * AJAX endpoints for cart operations
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../middleware/auth_middleware.php';

requireAuth();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$user_id = getCurrentUserId();

$db = getDB();

try {
    switch ($action) {
        case 'add':
            $book_id = (int) ($_POST['book_id'] ?? 0);
            $quantity = (int) ($_POST['quantity'] ?? 1);

            if ($book_id <= 0 || $quantity <= 0) {
                throw new Exception('Invalid parameters');
            }

            // Check if book exists and has stock
            $stmt = $db->prepare("SELECT stock_quantity FROM books WHERE id = ?");
            $stmt->execute([$book_id]);
            $book = $stmt->fetch();

            if (!$book) {
                throw new Exception('Book not found');
            }

            if ($book['stock_quantity'] < $quantity) {
                throw new Exception('Insufficient stock');
            }

            // Check if item already in cart
            $stmt = $db->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND book_id = ?");
            $stmt->execute([$user_id, $book_id]);
            $existing = $stmt->fetch();

            if ($existing) {
                // Update quantity
                $newQuantity = $existing['quantity'] + $quantity;
                $stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
                $stmt->execute([$newQuantity, $existing['id']]);
            } else {
                // Insert new item
                $stmt = $db->prepare("INSERT INTO cart_items (user_id, book_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $book_id, $quantity]);
            }

            echo json_encode(['success' => true, 'message' => 'Added to cart']);
            break;

        case 'update':
            $cart_item_id = (int) ($_POST['cart_item_id'] ?? 0);
            $quantity = (int) ($_POST['quantity'] ?? 0);

            if ($quantity <= 0) {
                // Remove item
                $stmt = $db->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
                $stmt->execute([$cart_item_id, $user_id]);
            } else {
                // Update quantity
                $stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$quantity, $cart_item_id, $user_id]);
            }

            echo json_encode(['success' => true, 'message' => 'Cart updated']);
            break;

        case 'remove':
            $cart_item_id = (int) ($_POST['cart_item_id'] ?? 0);

            $stmt = $db->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
            $stmt->execute([$cart_item_id, $user_id]);

            echo json_encode(['success' => true, 'message' => 'Item removed']);
            break;

        case 'clear':
            $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt->execute([$user_id]);

            echo json_encode(['success' => true, 'message' => 'Cart cleared']);
            break;

        case 'get_count':
            $stmt = $db->prepare("SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch();
            $count = $result['count'] ?? 0;

            echo json_encode(['success' => true, 'count' => (int) $count]);
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
