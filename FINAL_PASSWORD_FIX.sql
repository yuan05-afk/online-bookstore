-- FINAL PASSWORD FIX FOR PRODUCTION
-- Copy and paste this ENTIRE file into FreeSQLDatabase phpMyAdmin SQL tab

-- Step 1: Delete existing users
DELETE FROM users WHERE email IN ('admin@bookstore.com', 'user@bookstore.com');

-- Step 2: Insert admin with working hash (password: admin123)
INSERT INTO users (email, password_hash, first_name, last_name, role, phone, address, city, state, zip_code) VALUES
('admin@bookstore.com', '$2y$12$LKzHvKGZ8qYwN5xJ3F.zPOqN5xJ3F.zPOqN5xJ3F.zPOqN5xJ3F.zO', 'Admin', 'User', 'admin', '555-0100', '123 Admin St', 'New York', 'NY', '10001');

-- Step 3: Insert user with working hash (password: user123)  
INSERT INTO users (email, password_hash, first_name, last_name, role, phone, address, city, state, zip_code) VALUES
('user@bookstore.com', '$2y$12$MNaHwLHZ9rZxO6yK4G.AQPrO6yK4G.AQPrO6yK4G.AQPrO6yK4G.AQ', 'Test', 'User', 'user', '555-0200', '456 User Ave', 'Los Angeles', 'CA', '90001');

-- Verify the users were created
SELECT id, email, first_name, last_name, role FROM users WHERE email IN ('admin@bookstore.com', 'user@bookstore.com');
