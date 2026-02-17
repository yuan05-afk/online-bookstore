-- Run after supabase_migration.sql in Supabase SQL Editor

-- Row counts
SELECT 'users' AS table_name, COUNT(*) AS row_count FROM "users"
UNION ALL SELECT 'categories', COUNT(*) FROM "categories"
UNION ALL SELECT 'books', COUNT(*) FROM "books"
UNION ALL SELECT 'orders', COUNT(*) FROM "orders"
UNION ALL SELECT 'order_items', COUNT(*) FROM "order_items"
UNION ALL SELECT 'cart_items', COUNT(*) FROM "cart_items";

-- Referential integrity spot checks
SELECT COUNT(*) AS orphan_books_category
FROM "books" b
LEFT JOIN "categories" c ON c."id" = b."category_id"
WHERE b."category_id" IS NOT NULL AND c."id" IS NULL;

SELECT COUNT(*) AS orphan_orders_user
FROM "orders" o
LEFT JOIN "users" u ON u."id" = o."user_id"
WHERE u."id" IS NULL;

SELECT COUNT(*) AS orphan_order_items_order
FROM "order_items" oi
LEFT JOIN "orders" o ON o."id" = oi."order_id"
WHERE o."id" IS NULL;

SELECT COUNT(*) AS orphan_order_items_book
FROM "order_items" oi
LEFT JOIN "books" b ON b."id" = oi."book_id"
WHERE b."id" IS NULL;

-- Sequence sanity checks
SELECT 'users' AS table_name, MAX("id") AS max_id FROM "users"
UNION ALL SELECT 'categories', MAX("id") FROM "categories"
UNION ALL SELECT 'books', MAX("id") FROM "books"
UNION ALL SELECT 'orders', MAX("id") FROM "orders"
UNION ALL SELECT 'order_items', MAX("id") FROM "order_items"
UNION ALL SELECT 'cart_items', MAX("id") FROM "cart_items";
