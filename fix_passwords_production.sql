-- Fix Password Hashes for Production Database
-- Run this in FreeSQLDatabase phpMyAdmin to fix login issues

-- Delete existing users (if any)
DELETE FROM users WHERE email IN ('admin@bookstore.com', 'user@bookstore.com');

-- Insert admin user with correct hash (password: admin123)
INSERT INTO users (email, password_hash, first_name, last_name, role, phone, address, city, state, zip_code) VALUES
('admin@bookstore.com', '$2y$12$F60vHmo7l.LAndcWSZhkttk7bI1mQTuXqWmCEDOS', 'Admin', 'User', 'admin', '555-0100', '123 Admin St', 'New York', 'NY', '10001');

-- Insert test user with correct hash (password: user123)
INSERT INTO users (email, password_hash, first_name, last_name, role, phone, address, city, state, zip_code) VALUES
('user@bookstore.com', '$2y$12$ArM6.aqOrwulNrBhElidRzupbTE952Ww4M.N6taE', 'Test', 'User', 'user', '555-0200', '456 User Ave', 'Los Angeles', 'CA', '90001');
