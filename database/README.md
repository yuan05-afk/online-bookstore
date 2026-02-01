# Database Documentation

## Schema Overview

The Online Bookstore uses MySQL database with 6 main tables.

## Tables

### 1. users
Stores user account information for both customers and administrators.

**Columns:**
- `id` (INT, PK, AUTO_INCREMENT) - Unique user ID
- `email` (VARCHAR(255), UNIQUE) - User email address
- `password_hash` (VARCHAR(255)) - Bcrypt hashed password
- `first_name` (VARCHAR(100)) - User's first name
- `last_name` (VARCHAR(100)) - User's last name
- `role` (ENUM: 'user', 'admin') - User role
- `phone` (VARCHAR(20)) - Contact phone number
- `address` (TEXT) - Street address
- `city` (VARCHAR(100)) - City
- `state` (VARCHAR(100)) - State/Province
- `zip_code` (VARCHAR(20)) - Postal code
- `country` (VARCHAR(100)) - Country (default: 'USA')
- `created_at` (TIMESTAMP) - Account creation date
- `updated_at` (DATETIME) - Last update date

**Indexes:**
- `idx_email` on `email`
- `idx_role` on `role`

---

### 2. categories
Book categories for organization and filtering.

**Columns:**
- `id` (INT, PK, AUTO_INCREMENT) - Category ID
- `name` (VARCHAR(100), UNIQUE) - Category name
- `slug` (VARCHAR(100), UNIQUE) - URL-friendly slug
- `description` (TEXT) - Category description
- `created_at` (TIMESTAMP) - Creation date

**Indexes:**
- `idx_slug` on `slug`

**Default Categories:**
- Fiction, Non-Fiction, Science, Technology, History, Business, Self-Help, Children, Mystery, Romance

---

### 3. books
Book inventory and catalog.

**Columns:**
- `id` (INT, PK, AUTO_INCREMENT) - Book ID
- `isbn` (VARCHAR(20), UNIQUE) - ISBN number
- `title` (VARCHAR(255)) - Book title
- `author` (VARCHAR(255)) - Author name
- `price` (DECIMAL(10,2)) - Book price
- `description` (TEXT) - Book description
- `cover_image` (VARCHAR(255)) - Cover image URL
- `category_id` (INT, FK) - Reference to categories table
- `stock_quantity` (INT) - Available stock
- `created_at` (TIMESTAMP) - Added date
- `updated_at` (DATETIME) - Last update date

**Indexes:**
- `idx_isbn` on `isbn`
- `idx_title` on `title`
- `idx_author` on `author`
- `idx_category` on `category_id`

**Foreign Keys:**
- `category_id` → `categories(id)` ON DELETE SET NULL

---

### 4. cart_items
Shopping cart items for logged-in users.

**Columns:**
- `id` (INT, PK, AUTO_INCREMENT) - Cart item ID
- `user_id` (INT, FK) - Reference to users table
- `book_id` (INT, FK) - Reference to books table
- `quantity` (INT) - Quantity in cart
- `session_id` (VARCHAR(100)) - Session ID for guest carts (future use)
- `created_at` (TIMESTAMP) - Added to cart date

**Indexes:**
- `idx_user_id` on `user_id`
- `idx_session_id` on `session_id`

**Unique Constraints:**
- `unique_cart_item` on (`user_id`, `book_id`)

**Foreign Keys:**
- `user_id` → `users(id)` ON DELETE CASCADE
- `book_id` → `books(id)` ON DELETE CASCADE

---

### 5. orders
Customer orders.

**Columns:**
- `id` (INT, PK, AUTO_INCREMENT) - Order ID
- `order_number` (VARCHAR(50), UNIQUE) - Unique order number
- `user_id` (INT, FK) - Reference to users table
- `status` (ENUM) - Order status: 'pending', 'processing', 'shipped', 'delivered', 'cancelled'
- `payment_method` (VARCHAR(50)) - Payment method used
- `payment_status` (VARCHAR(50)) - Payment status
- `total_amount` (DECIMAL(10,2)) - Subtotal before tax
- `tax_amount` (DECIMAL(10,2)) - Tax amount
- `grand_total` (DECIMAL(10,2)) - Total including tax
- `shipping_address` (TEXT) - Full shipping address
- `transaction_id` (VARCHAR(100)) - Payment transaction ID
- `notes` (TEXT) - Order notes
- `created_at` (TIMESTAMP) - Order date
- `updated_at` (DATETIME) - Last update date

**Indexes:**
- `idx_user_id` on `user_id`
- `idx_order_number` on `order_number`
- `idx_status` on `status`
- `idx_created_at` on `created_at`

**Foreign Keys:**
- `user_id` → `users(id)` ON DELETE CASCADE

---

### 6. order_items
Line items for each order.

**Columns:**
- `id` (INT, PK, AUTO_INCREMENT) - Order item ID
- `order_id` (INT, FK) - Reference to orders table
- `book_id` (INT, FK) - Reference to books table
- `quantity` (INT) - Quantity ordered
- `price_at_purchase` (DECIMAL(10,2)) - Price at time of purchase

**Indexes:**
- `idx_order_id` on `order_id`

**Foreign Keys:**
- `order_id` → `orders(id)` ON DELETE CASCADE
- `book_id` → `books(id)` ON DELETE RESTRICT

---

## Relationships

```
users (1) ──── (M) cart_items
users (1) ──── (M) orders
categories (1) ──── (M) books
books (1) ──── (M) cart_items
books (1) ──── (M) order_items
orders (1) ──── (M) order_items
```

## Seed Data

The `seed.sql` file includes:
- 2 test users (1 admin, 1 customer)
- 10 book categories
- 50 books across various categories

## Database Setup

### Local Development
```sql
CREATE DATABASE online_bookstore;
USE online_bookstore;
SOURCE database/schema.sql;
SOURCE database/seed.sql;
```

### Production
1. Create database on your hosting provider
2. Import `schema.sql` via phpMyAdmin or command line
3. Import `seed.sql` for initial data

## Backup

### Export Database
```bash
mysqldump -u root -p online_bookstore > backup.sql
```

### Import Database
```bash
mysql -u root -p online_bookstore < backup.sql
```

## Maintenance

### Clear Old Cart Items
```sql
DELETE FROM cart_items WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

### Archive Old Orders
```sql
-- Create archive table
CREATE TABLE orders_archive LIKE orders;

-- Move old orders
INSERT INTO orders_archive SELECT * FROM orders 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
```

---

**Database Version:** MySQL 5.7+  
**Character Set:** UTF-8  
**Collation:** utf8_unicode_ci
