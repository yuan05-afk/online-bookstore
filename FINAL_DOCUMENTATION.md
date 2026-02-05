# Online Bookstore System - Complete System Documentation

**Project:** Online Bookstore Management System  
**Academic Year:** 2026  
**Document Type:** System Requirements & Scrum Implementation Documentation

---

## Technology Stack Utilized

| Component | Technology |
|-----------|-----------|
| **Languages** | PHP 7.4+, JavaScript (ES6), HTML5, CSS3 |
| **Database** | MySQL 5.7+ (SQL-based) |
| **Web Server** | Apache (via XAMPP for local testing) |
| **UI/UX** | HTML5, CSS3, Iconify Icons |
| **Containerization** | Docker (Dockerfile & Docker Image) |
| **Deployment** | Render (Docker-based deployment) |
| **Email Service** | SendGrid API (SMTP) |
| **Package Manager** | Composer (PHP dependencies) |

---

## Scrum Team Roles

| Role | Team Member |
|------|-------------|
| **Product Owner (PO)** | Yuan Andrei C. Mariano |
| **Scrum Master (SM)** | Ruben Jr. Aldave |
| **Development Team** | Aaron Rodge Silva & Raphael Edrian Tan |

---

## 1. Introduction

Requirements define what a system must do (functional) and how well it should perform those tasks (non-functional). This document provides a comprehensive analysis of the **Online Bookstore System**, a full-featured e-commerce platform for book sales, based entirely on the actual implemented codebase.

The system enables customers to browse books, manage shopping carts, place orders, and track deliveries. Administrators can manage inventory, process orders, and oversee user accounts. All requirements documented herein are derived from existing code, database structures, and implemented features.

---

## 2. Step-by-Step Guide to Working with Functional & Non-Functional Requirements

### Step 1: Understand the Problem Domain
- Engage stakeholders (users, clients, business analysts, developers)
- Conduct interviews, workshops, or surveys
- Document high-level goals and pain points

### Step 2: Identify Functional Requirements (FRs)
- Ask: "What should the system do?"
- Focus on features, behaviors, inputs, outputs, and interactions
- Use techniques like user stories, use cases, or process flows
- Ensure each FR is:
  - **Specific**
  - **Testable**
  - **Traceable**
  - **Unambiguous**

### Step 3: Identify Non-Functional Requirements (NFRs)
- Ask: "How well should the system perform its functions?"
- Consider quality attributes such as performance, security, usability, reliability, etc.
- Categorize NFRs using frameworks like ISO/IEC 25010
- Make them measurable (e.g., "System shall respond within 2 seconds")

### Step 4: Prioritize and Validate Requirements
- Use MoSCoW (Must-have, Should-have, Could-have, Won't-have) or similar
- Review with stakeholders to ensure alignment
- Resolve conflicts or ambiguities

### Step 5: Document Requirements Clearly
- Use a Software Requirements Specification (SRS) document
- Separate FRs and NFRs into distinct sections
- Include traceability matrices if needed

### Step 6: Verify and Test
- Ensure every requirement has a corresponding test case
- Functional tests validate behavior; non-functional tests validate quality attributes (e.g., load testing for performance)

### Step 7: Manage Changes
- Use change control processes
- Update documentation and tests when requirements evolve

---

## 3. Problem Statement

**Context:**  
An online bookstore requires a comprehensive web-based system to manage book inventory, process customer orders, and provide secure user authentication. The current manual processes are inefficient and error-prone.

**Objective:**  
Develop a full-stack web application where:
- **Customers** can browse a catalog of 50+ books across 10 categories, search by title/author/ISBN, add items to a shopping cart, complete secure checkout with mock payment processing, and track order status
- **Administrators** can manage book inventory (add, edit, delete), process and update orders, manage user accounts, and view sales analytics
- **System** must ensure data security through bcrypt password hashing, CSRF protection, session management, and input validation

**Constraints:**
- Must use PHP and MySQL for backend
- Must support both local (XAMPP) and cloud deployment (Docker/Render)
- Must handle concurrent users efficiently
- Must provide email notifications for order confirmations

---

## 4. Functional Requirements (FRs)

All functional requirements below are **IMPLEMENTED** and verified through code analysis.

### 4.1 User Authentication & Authorization

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **FR-01** | The system shall allow new users to register with email, password, first name, last name, phone, address, city, state, and zip code. | `auth/register.php` (lines 87-106) |
| **FR-02** | The system shall authenticate users during login using email and password with bcrypt verification. | `auth/login.php` (lines 43-64) |
| **FR-03** | The system shall support role-based access control with two roles: 'user' and 'admin'. | `users` table (line 19 in schema.sql), `middleware/auth_middleware.php` |
| **FR-04** | The system shall redirect users to appropriate dashboards based on role (admin → dashboard, user → catalog). | `auth/login.php` (lines 60-63) |
| **FR-05** | The system shall enforce session timeout after 2 hours of inactivity. | `config/config.php` (line 43), `middleware/auth_middleware.php` (lines 22-29) |
| **FR-06** | The system shall allow users to log out and destroy their session. | `auth/logout.php` |

### 4.2 Book Catalog & Search

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **FR-07** | The system shall display books organized by 10 categories (Fiction, Non-Fiction, Science, Technology, History, Business, Self-Help, Children, Mystery, Romance). | `categories` table in seed.sql (lines 5-15) |
| **FR-08** | The system shall allow users to browse all books with pagination (20 items per page). | `user/catalog.php` (lines 38-39, 77-78) |
| **FR-09** | The system shall allow users to search books by title, author, or ISBN. | `user/catalog.php` (lines 30-36) |
| **FR-10** | The system shall allow users to filter books by category. | `user/catalog.php` (lines 25-28) |
| **FR-11** | The system shall allow users to sort books by title (A-Z), price (low-high, high-low), and date (newest/oldest). | `user/catalog.php` (lines 41-59) |
| **FR-12** | The system shall display book details including title, author, price, description, cover image, category, and stock quantity. | `books` table schema (lines 44-60) |
| **FR-13** | The system shall display stock status badges (In Stock, Low Stock, Out of Stock). | `user/catalog.php` (lines 203-209) |

### 4.3 Shopping Cart Management

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **FR-14** | The system shall allow authenticated users to add books to their shopping cart. | `api/cart_actions.php` (lines 22-60) |
| **FR-15** | The system shall allow users to update item quantities in the cart. | `api/cart_actions.php` (lines 62-77) |
| **FR-16** | The system shall allow users to remove items from the cart. | `api/cart_actions.php` (lines 79-86) |
| **FR-17** | The system shall prevent adding items exceeding available stock. | `api/cart_actions.php` (lines 39-41) |
| **FR-18** | The system shall calculate subtotal, tax (8.5%), and grand total automatically. | `user/cart.php` (lines 26-32) |
| **FR-19** | The system shall persist cart items per user in the database. | `cart_items` table (lines 63-75 in schema.sql) |

### 4.4 Order Processing & Checkout

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **FR-20** | The system shall allow users to proceed to checkout from the cart. | `user/checkout.php` |
| **FR-21** | The system shall pre-fill shipping information from user profile. | `user/checkout.php` (lines 38-41, 62-110) |
| **FR-22** | The system shall validate all shipping information fields (first name, last name, address, city, state, zip, country, phone). | `user/process_order.php` (lines 48-53) |
| **FR-23** | The system shall process mock credit card payments with validation. | `user/process_order.php` (lines 90-102), `includes/payment_mock.php` |
| **FR-24** | The system shall generate unique order numbers in format "ORD-{UNIQID}-{RANDOM}". | `includes/security.php` (lines 107-110) |
| **FR-25** | The system shall create order records with status "Processing" by default. | `user/process_order.php` (line 126) |
| **FR-26** | The system shall create order items linking books to orders with price at purchase. | `user/process_order.php` (lines 147-162) |
| **FR-27** | The system shall update book stock quantities after successful order placement. | `user/process_order.php` (lines 152-167) |
| **FR-28** | The system shall clear the user's cart after successful order. | `user/process_order.php` (lines 171-172) |
| **FR-29** | The system shall send order confirmation emails via SendGrid. | `user/process_order.php` (lines 177-189), `includes/email.php` |
| **FR-30** | The system shall use database transactions to ensure atomic order processing. | `user/process_order.php` (lines 105-175) |

### 4.5 Order Tracking & History

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **FR-31** | The system shall allow users to view their order history. | `user/orders.php` |
| **FR-32** | The system shall display order details including order number, date, items, quantities, prices, and status. | `user/order_detail.php` |
| **FR-33** | The system shall support four order statuses: Processing, Shipped, Delivered, Cancelled. | `orders` table (line 85 in schema.sql) |

### 4.6 User Profile Management

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **FR-34** | The system shall allow users to view and edit their profile information. | `user/profile.php` |
| **FR-35** | The system shall allow users to change their password. | `user/profile.php` (password change functionality) |

### 4.7 Admin Dashboard & Analytics

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **FR-36** | The system shall display admin dashboard with key metrics: total books, total orders, total users, total revenue. | `admin/dashboard.php` (lines 14-25) |
| **FR-37** | The system shall display recent orders (last 10) on admin dashboard. | `admin/dashboard.php` (lines 28-35) |
| **FR-38** | The system shall display low stock alerts for books with quantity < 10. | `admin/dashboard.php` (lines 38-44) |

### 4.8 Admin Book Management

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **FR-39** | The system shall allow admins to add new books with ISBN, title, author, price, description, cover image URL, category, and stock quantity. | `admin/book_form.php` |
| **FR-40** | The system shall allow admins to edit existing book information. | `admin/book_form.php` |
| **FR-41** | The system shall allow admins to delete books from inventory. | `admin/book_actions.php` |
| **FR-42** | The system shall allow admins to search and filter books by title, author, ISBN, or category. | `admin/books.php` (lines 13-33) |
| **FR-43** | The system shall validate ISBN format (10-13 digits). | `includes/security.php` (lines 98-102) |

### 4.9 Admin Order Management

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **FR-44** | The system shall allow admins to view all orders. | `admin/orders.php` |
| **FR-45** | The system shall allow admins to update order status. | `admin/order_detail.php` |
| **FR-46** | The system shall allow admins to view detailed order information including customer details and items. | `admin/order_detail.php` |

### 4.10 Admin User Management

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **FR-47** | The system shall allow admins to view all user accounts. | `admin/accounts.php` |
| **FR-48** | The system shall allow admins to view detailed user information. | `admin/account_detail.php` |
| **FR-49** | The system shall allow admins to delete user accounts with cascading deletion of cart items. | `admin/account_actions.php` |

---

## 5. Non-Functional Requirements (NFRs)

All non-functional requirements below are **IMPLEMENTED** and verified through code analysis.

### 5.1 Security

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **NFR-01** | All user passwords shall be hashed using bcrypt with cost factor 12. | `includes/security.php` (line 84) |
| **NFR-02** | The system shall implement CSRF token protection for all forms with 1-hour expiration. | `includes/security.php` (lines 10-37), `config/config.php` (line 42) |
| **NFR-03** | The system shall use prepared statements for all database queries to prevent SQL injection. | All database queries use PDO prepared statements (e.g., `user/catalog.php` line 79) |
| **NFR-04** | The system shall sanitize all user inputs to prevent XSS attacks. | `includes/security.php` (lines 42-50) |
| **NFR-05** | The system shall escape all output using htmlspecialchars() with ENT_QUOTES. | `includes/security.php` (lines 55-61) |
| **NFR-06** | The system shall enforce session security with httponly, samesite=Strict cookies. | `config/config.php` (lines 15-18) |
| **NFR-07** | The system shall implement role-based access control preventing unauthorized access to admin functions. | `middleware/auth_middleware.php` (lines 38-46) |
| **NFR-08** | Payment data shall be processed via mock payment processor (production would use PCI-DSS compliant gateway). | `includes/payment_mock.php` |

### 5.2 Performance

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **NFR-09** | The system shall use database indexing on frequently queried columns (email, ISBN, order_number, category_id). | `database/schema.sql` (lines 28-29, 39, 56-59, 72-73, 97-100) |
| **NFR-10** | The system shall implement pagination limiting results to 20 items per page. | `config/config.php` (line 39), `user/catalog.php` (line 77) |
| **NFR-11** | The system shall use PDO persistent connections set to false to manage connection pooling. | `config/database.php` (line 43) |
| **NFR-12** | The system shall use database transactions for order processing to ensure atomicity. | `user/process_order.php` (lines 105-175) |

### 5.3 Reliability

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **NFR-13** | The system shall use InnoDB storage engine for ACID compliance. | `database/schema.sql` (all tables use ENGINE=InnoDB) |
| **NFR-14** | The system shall implement error logging for debugging without exposing errors to users. | `config/config.php` (lines 8-9), error_log() calls throughout |
| **NFR-15** | The system shall handle email sending failures gracefully without failing order processing. | `user/process_order.php` (lines 179-189) |
| **NFR-16** | The system shall validate stock availability before order confirmation. | `user/process_order.php` (lines 70-75) |

### 5.4 Usability

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **NFR-17** | The system shall provide user-friendly flash messages for success/error feedback. | `includes/security.php` (lines 172-191) |
| **NFR-18** | The system shall pre-fill user information in checkout forms. | `user/checkout.php` (lines 38-41) |
| **NFR-19** | The system shall display clear stock status indicators (In Stock, Low Stock, Out of Stock). | `user/catalog.php` (lines 203-209) |
| **NFR-20** | The system shall provide confirmation dialogs for destructive actions (delete, logout). | JavaScript confirm() dialogs in admin pages |

### 5.5 Compatibility

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **NFR-21** | The system shall support PHP 7.4 or higher. | `README.md` (line 10) |
| **NFR-22** | The system shall support MySQL 5.7 or higher. | `README.md` (line 11) |
| **NFR-23** | The system shall use UTF-8 character encoding for international character support. | `database/schema.sql` (CHARSET=utf8) |
| **NFR-24** | The system shall be containerized using Docker for cross-platform deployment. | `Dockerfile`, `docker-compose.yml` |

### 5.6 Maintainability

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **NFR-25** | The system shall use modular architecture with separation of concerns (config, includes, middleware). | Project structure with dedicated folders |
| **NFR-26** | The system shall use Singleton pattern for database connections. | `config/database.php` (lines 7-71) |
| **NFR-27** | The system shall use environment variables for configuration management. | `.env.example`, `config/config.php` (lines 48-58) |
| **NFR-28** | The system shall include comprehensive inline code documentation. | PHPDoc comments throughout codebase |

### 5.7 Scalability

| ID | Requirement | Implementation Evidence |
|----|-------------|------------------------|
| **NFR-29** | The system shall support deployment on cloud platforms (Render) with environment-based configuration. | `render.yaml`, `config/database.php` (lines 19-31) |
| **NFR-30** | The system shall use Composer for dependency management enabling easy updates. | `composer.json`, `composer.lock` |

---

## 6. Complete Example: Online Bookstore System

### Problem Statement
A startup wants to build an online bookstore where users can browse books, search by title/author/ISBN, add items to a cart, place orders, and track deliveries. The system must be secure, fast, and accessible with role-based access for customers and administrators.

### Functional Requirements Summary
- **Authentication:** User registration, login, role-based access (FR-01 to FR-06)
- **Catalog:** Browse, search, filter, sort books across 10 categories (FR-07 to FR-13)
- **Cart:** Add, update, remove items with stock validation (FR-14 to FR-19)
- **Checkout:** Secure payment processing, order creation, email confirmation (FR-20 to FR-30)
- **Orders:** View history, track status (FR-31 to FR-33)
- **Admin:** Dashboard analytics, book CRUD, order management, user management (FR-36 to FR-49)

### Non-Functional Requirements Summary
- **Security:** Bcrypt hashing, CSRF protection, prepared statements, XSS prevention (NFR-01 to NFR-08)
- **Performance:** Database indexing, pagination, connection management (NFR-09 to NFR-12)
- **Reliability:** ACID transactions, error logging, graceful degradation (NFR-13 to NFR-16)
- **Usability:** Flash messages, pre-filled forms, clear indicators (NFR-17 to NFR-20)
- **Compatibility:** PHP 7.4+, MySQL 5.7+, UTF-8, Docker (NFR-21 to NFR-24)
- **Maintainability:** Modular architecture, Singleton pattern, environment config (NFR-25 to NFR-28)
- **Scalability:** Cloud deployment, dependency management (NFR-29 to NFR-30)

---

## 7. Scrum Framework Overview

Scrum is an agile framework for developing, delivering, and sustaining complex products. It emphasizes iterative progress, collaboration, and adaptability. Teams work in fixed-length iterations called **Sprints** (typically 2–4 weeks) to deliver potentially shippable product increments.

### Core Scrum Roles

| Role | Responsibility |
|------|---------------|
| **Product Owner (PO)** | Maximizes product value; manages the Product Backlog |
| **Scrum Master (SM)** | Facilitates Scrum process; removes impediments; coaches the team |
| **Development Team** | Cross-functional group that delivers working software each Sprint |

### Scrum Artifacts

- **Product Backlog:** Ordered list of all features, enhancements, bug fixes, and requirements
- **Sprint Backlog:** Subset of Product Backlog selected for the current Sprint + plan to deliver it
- **Increment:** Sum of all completed Product Backlog items at the end of a Sprint—must be "Done" and potentially releasable

### Scrum Events

1. **Sprint** (time-boxed container, 2-4 weeks)
2. **Sprint Planning** (select items, create Sprint Backlog)
3. **Daily Scrum** (15-minute standup)
4. **Sprint Review** (demo Increment to stakeholders)
5. **Sprint Retrospective** (reflect and improve)

---

## 8. Product Backlog (Derived from Actual System)

The Product Owner has gathered requirements from the implemented system and written them as user stories, prioritized by business value.

### Format
**As a** [type of user], **I want** [an action] **so that** [a benefit/value].

| ID | Backlog Item (User Story) | Priority | Est. Effort (Story Points) | Status | Notes |
|----|---------------------------|----------|---------------------------|--------|-------|
| **PB-01** | As a new user, I want to register with email and password so I can create an account. | High | 5 | ✅ Implemented | Includes validation, bcrypt hashing, profile fields |
| **PB-02** | As a returning user, I want to log in securely so I can access my account. | High | 3 | ✅ Implemented | Session management, role-based redirect |
| **PB-03** | As a customer, I want to browse books by category so I can discover titles easily. | High | 5 | ✅ Implemented | 10 categories, sidebar navigation |
| **PB-04** | As a user, I want to search books by title, author, or ISBN so I can find specific items quickly. | High | 8 | ✅ Implemented | Full-text search with LIKE queries |
| **PB-05** | As a customer, I want to add/remove books from my cart so I can manage my order. | High | 8 | ✅ Implemented | AJAX API, stock validation, quantity updates |
| **PB-06** | As a buyer, I want to place an order with shipping & payment info so I can complete my purchase. | Critical | 13 | ✅ Implemented | Mock payment, transaction safety, email confirmation |
| **PB-07** | As a user, I want to view my order history and status so I know when my books arrive. | Medium | 5 | ✅ Implemented | Order detail page, status tracking |
| **PB-08** | As an admin, I want to add/edit/delete books so I can manage inventory. | High | 8 | ✅ Implemented | Full CRUD with image URLs, stock management |
| **PB-09** | As an admin, I want to view dashboard analytics so I can monitor business performance. | Medium | 8 | ✅ Implemented | Total books, orders, users, revenue, low stock alerts |
| **PB-10** | As an admin, I want to update order status so I can track fulfillment. | Medium | 5 | ✅ Implemented | Processing, Shipped, Delivered, Cancelled |
| **PB-11** | As a user, I want to sort books by price and date so I can find the best deals. | Medium | 3 | ✅ Implemented | 5 sort options |
| **PB-12** | As a customer, I want to see stock availability so I know if items are in stock. | Medium | 3 | ✅ Implemented | Badges: In Stock, Low Stock, Out of Stock |
| **PB-13** | As a developer, I need CSRF protection so the system is secure. | High (Tech Enabler) | 5 | ✅ Implemented | Token generation, validation, 1-hour expiry |
| **PB-14** | As a developer, I need database transactions so orders are processed atomically. | High (Tech Enabler) | 5 | ✅ Implemented | PDO transactions in process_order.php |
| **PB-15** | As a system, I need to send order confirmation emails so customers are notified. | Medium | 8 | ✅ Implemented | SendGrid integration |

---

## 9. Sprint Planning Example

### Sprint 1 Goal
**"Enable core shopping experience — registration, login, browsing, cart, and checkout."**

**Duration:** 2 weeks  
**Attendees:** PO (Yuan Andrei), SM (Ruben Jr.), Dev Team (Aaron, Raphael)

### Selected Items for Sprint 1

| Selected Items | Tasks (broken down by Dev Team) | Owner | Est. Hours | Status |
|----------------|----------------------------------|-------|-----------|--------|
| **PB-01: User Registration** | - Design `users` table schema<br>- Build registration form UI<br>- Implement bcrypt password hashing<br>- Add email validation<br>- Write unit tests | Aaron | 12 | ✅ Done |
| **PB-02: User Login** | - Create login API endpoint<br>- Implement session management<br>- Add role-based redirect logic<br>- Create middleware for auth | Raphael | 10 | ✅ Done |
| **PB-03: Browse by Category** | - Fetch categories from `categories` table<br>- Build responsive category grid<br>- Add hover effects and icons | Aaron | 8 | ✅ Done |
| **PB-04: Search Books** | - Implement search query with LIKE<br>- Add search bar UI<br>- Display search results count | Raphael | 10 | ✅ Done |
| **PB-05: Shopping Cart** | - Create `cart_items` table<br>- Build AJAX cart API endpoints<br>- Implement add/update/remove actions<br>- Display cart summary with totals | Aaron & Raphael | 14 | ✅ Done |
| **PB-13: CSRF Protection** | - Generate CSRF tokens<br>- Validate tokens on form submission<br>- Add token expiration logic | Aaron | 6 | ✅ Done |

**Sprint Goal:**  
"Enable users to sign up, log in, browse books by category, search for titles, and manage a shopping cart with CSRF protection."

---

### Sprint 2 Goal
**"Enable complete checkout flow with payment processing and order creation."**

**Duration:** 2 weeks  
**Attendees:** PO (Yuan Andrei), SM (Ruben Jr.), Dev Team (Aaron, Raphael)

### Selected Items for Sprint 2

| Selected Items | Tasks (broken down by Dev Team) | Owner | Est. Hours | Status |
|----------------|----------------------------------|-------|-----------|--------|
| **PB-06: Order Placement** | - Design `orders` and `order_items` tables<br>- Build checkout form UI<br>- Implement payment validation<br>- Create order processing logic<br>- Add database transactions | Raphael | 16 | ✅ Done |
| **PB-14: Database Transactions** | - Implement PDO transaction handling<br>- Add rollback on failure<br>- Test atomic order creation<br>- Handle stock updates | Raphael | 8 | ✅ Done |
| **PB-15: Email Notifications** | - Integrate SendGrid API<br>- Create email templates<br>- Send order confirmation emails<br>- Handle email failures gracefully | Aaron | 12 | ✅ Done |
| **PB-11: Sort Functionality** | - Add sort dropdown UI<br>- Implement sort by price (low/high)<br>- Implement sort by date (newest/oldest)<br>- Add sort by title | Aaron | 6 | ✅ Done |
| **PB-12: Stock Badges** | - Create badge components<br>- Add stock status logic<br>- Display In Stock/Low Stock/Out of Stock | Aaron | 4 | ✅ Done |

**Sprint Goal:**  
"Enable users to complete checkout with payment processing, receive email confirmations, and view books with stock indicators and sorting options."

---

### Sprint 3 Goal
**"Enable order tracking and history for customers."**

**Duration:** 2 weeks  
**Attendees:** PO (Yuan Andrei), SM (Ruben Jr.), Dev Team (Aaron, Raphael)

### Selected Items for Sprint 3

| Selected Items | Tasks (broken down by Dev Team) | Owner | Est. Hours | Status |
|----------------|----------------------------------|-------|-----------|--------|
| **PB-07: Order History** | - Create order history page<br>- Display order list with pagination<br>- Show order status badges<br>- Add order detail link | Aaron | 10 | ✅ Done |
| **PB-16: Cart Remove Button** | - Add remove button to cart items<br>- Implement AJAX remove action<br>- Update cart totals dynamically<br>- Add confirmation dialog | Aaron | 4 | ✅ Done |
| **Order Detail Page** | - Create order detail view<br>- Display order items with prices<br>- Show shipping information<br>- Display payment method | Raphael | 8 | ✅ Done |
| **Order Confirmation Page** | - Create success page after checkout<br>- Display order number and transaction ID<br>- Show order summary<br>- Add "View Order" link | Aaron | 6 | ✅ Done |

**Sprint Goal:**  
"Enable users to view their order history, track order status, and see detailed order information."

---

### Sprint 4 Goal
**"Build admin dashboard with analytics and book management."**

**Duration:** 2 weeks  
**Attendees:** PO (Yuan Andrei), SM (Ruben Jr.), Dev Team (Aaron, Raphael)

### Selected Items for Sprint 4

| Selected Items | Tasks (broken down by Dev Team) | Owner | Est. Hours | Status |
|----------------|----------------------------------|-------|-----------|--------|
| **PB-09: Admin Dashboard** | - Design dashboard layout<br>- Create stats cards (books, orders, users, revenue)<br>- Display recent orders table<br>- Add low stock alerts section | Aaron | 14 | ✅ Done |
| **PB-08: Book Management (Part 1)** | - Create book listing page<br>- Add search and filter functionality<br>- Display book table with images<br>- Add pagination | Raphael | 12 | ✅ Done |
| **Admin Authentication** | - Create admin middleware<br>- Add role-based redirects<br>- Implement admin header/footer<br>- Add admin navigation | Raphael | 8 | ✅ Done |

**Sprint Goal:**  
"Enable admins to view dashboard analytics and browse book inventory with search and filters."

---

### Sprint 5 Goal
**"Complete admin book CRUD operations and order management."**

**Duration:** 2 weeks  
**Attendees:** PO (Yuan Andrei), SM (Ruben Jr.), Dev Team (Aaron, Raphael)

### Selected Items for Sprint 5

| Selected Items | Tasks (broken down by Dev Team) | Owner | Est. Hours | Status |
|----------------|----------------------------------|-------|-----------|--------|
| **PB-08: Book Management (Part 2)** | - Create add book form<br>- Create edit book form<br>- Implement book CRUD actions<br>- Add ISBN validation<br>- Add image URL validation | Aaron | 14 | ✅ Done |
| **PB-10: Order Status Updates** | - Create admin order list page<br>- Create order detail page<br>- Add status update dropdown<br>- Implement status change logic | Raphael | 12 | ✅ Done |
| **Admin Order Management** | - Display all orders with filters<br>- Show customer information<br>- Add order search functionality<br>- Display order items | Raphael | 10 | ✅ Done |

**Sprint Goal:**  
"Enable admins to perform full CRUD operations on books and manage order statuses."

---

### Sprint 6 Goal
**"Implement user account management and profile features."**

**Duration:** 2 weeks  
**Attendees:** PO (Yuan Andrei), SM (Ruben Jr.), Dev Team (Aaron, Raphael)

### Selected Items for Sprint 6

| Selected Items | Tasks (broken down by Dev Team) | Owner | Est. Hours | Status |
|----------------|----------------------------------|-------|-----------|--------|
| **PB-34: User Profile** | - Create user profile page<br>- Display user information<br>- Add edit profile form<br>- Implement profile update logic | Aaron | 10 | ✅ Done |
| **PB-35: Password Change** | - Add password change form<br>- Validate current password<br>- Implement password update<br>- Add success/error messages | Aaron | 6 | ✅ Done |
| **Admin User Management** | - Create user accounts list page<br>- Display user details page<br>- Add user search functionality<br>- Implement user deletion with cascade | Raphael | 12 | ✅ Done |
| **Profile Security** | - Add CSRF protection to profile forms<br>- Validate email uniqueness on update<br>- Add password strength validation<br>- Test security measures | Raphael | 8 | ✅ Done |

**Sprint Goal:**  
"Enable users to manage their profiles and admins to manage user accounts."

---

### Sprint 7 Goal
**"Finalize system with deployment, testing, and documentation."**

**Duration:** 2 weeks  
**Attendees:** PO (Yuan Andrei), SM (Ruben Jr.), Dev Team (Aaron, Raphael)

### Selected Items for Sprint 7

| Selected Items | Tasks (broken down by Dev Team) | Owner | Est. Hours | Status |
|----------------|----------------------------------|-------|-----------|--------|
| **Docker Deployment** | - Create Dockerfile<br>- Create docker-compose.yml<br>- Test local Docker build<br>- Configure environment variables | Raphael | 10 | ✅ Done |
| **Render Deployment** | - Create render.yaml<br>- Configure database connection<br>- Set up SendGrid integration<br>- Deploy to production | Raphael | 8 | ✅ Done |
| **Security Hardening** | - Review all CSRF implementations<br>- Test SQL injection prevention<br>- Validate XSS protection<br>- Test session security | Aaron & Raphael | 10 | ✅ Done |
| **Documentation** | - Update README.md<br>- Create deployment guide<br>- Document API endpoints<br>- Write database schema docs | Aaron | 8 | ✅ Done |
| **Testing & Bug Fixes** | - End-to-end testing<br>- Fix identified bugs<br>- Performance testing<br>- Browser compatibility testing | Aaron & Raphael | 12 | ✅ Done |

**Sprint Goal:**  
"Deploy the system to production, ensure security compliance, and complete all documentation."

---

## 10. Daily Scrum (Role-Play Based on System Features)

**Time:** 9:30 AM Daily  
**Duration:** ≤ 15 minutes  
**Purpose:** Inspect progress toward Sprint Goal and adapt plan

### Participants:
- **Ruben Jr. (Scrum Master)**
- **Yuan Andrei (Product Owner)**
- **Aaron (Frontend/Backend Developer)**
- **Raphael (Backend Developer)**

### Daily Standup Script – Day 5 of Sprint 1

**Ruben (Scrum Master):**  
"Good morning, team! Let's keep this to 15 minutes. Aaron, you're up first."

**Aaron (Developer):**  
"Yesterday, I finished the shopping cart UI and integrated it with Raphael's AJAX API. The add-to-cart button now works with stock validation. Today, I'll work on the cart summary page showing subtotal, tax, and total. I also need to add the 'Proceed to Checkout' button. No blockers."

**Raphael (Developer):**  
"Yesterday, I completed the cart API endpoints—add, update, remove, and get_count. I also added stock validation to prevent over-ordering. Today, I'll start on the checkout page backend, including the `process_order.php` script with database transactions. Minor question: Yuan, should we use a real payment gateway or mock for MVP?"

**Yuan Andrei (Product Owner):**  
"Great progress! Use a mock payment processor for MVP. We'll integrate Stripe later if needed. Make sure to validate card numbers and generate transaction IDs."

**Raphael:**  
"Got it. Unblock confirmed."

**Ruben:**  
"Excellent. Any other impediments?"  
*(Team shakes heads)*  
"Alright—let's stay focused on the Sprint Goal: functional cart and checkout by Friday. Keep up the great work, team!"

---

## 11. Sprint Review & Retrospective

### Sprint 1 Review

**Goal:** Inspect the Increment and adapt Product Backlog.

**Attendees:** PO, SM, Dev Team, Stakeholders

**Demo:**
- Dev Team demonstrates:
  - ✅ User registers with email/password
  - ✅ User logs in and is redirected to catalog
  - ✅ User browses books by category (10 categories)
  - ✅ User searches books by title/author/ISBN
  - ✅ User adds books to cart with stock validation
  - ✅ Cart displays subtotal, tax (8.5%), and total

**PO Feedback:**
- "Love the cart! But can we add a 'Remove' button next to each item?" → **New backlog item: PB-16**
- "The search is great, but can we highlight the search term in results?" → **Added to backlog for future prioritization**

**Stakeholder Suggestions:**
- "Add a 'Wishlist' feature" → **Added to backlog: PB-17**
- "Can we export order history as PDF?" → **Added to backlog: PB-18**

**Outcome:**
- Sprint Goal achieved ✅
- 6 backlog items completed
- 3 new backlog items added based on feedback

---

### Sprint 2 Review

**Demo:**
- ✅ Complete checkout flow with shipping information
- ✅ Mock payment processing with validation
- ✅ Order creation with unique order numbers
- ✅ Email confirmation sent via SendGrid
- ✅ Book sorting by price, date, and title
- ✅ Stock status badges displayed

**PO Feedback:**
- "Checkout flow is smooth! Can we add order confirmation page?" → **Implemented in Sprint 3**
- "Email notifications work perfectly!"

**Outcome:**
- Sprint Goal achieved ✅
- 5 backlog items completed
- Payment and email systems fully functional

---

### Sprint 3 Review

**Demo:**
- ✅ Order history page with pagination
- ✅ Order detail view with items and shipping info
- ✅ Order confirmation page after checkout
- ✅ Cart remove button with AJAX
- ✅ Order status tracking

**PO Feedback:**
- "Perfect! Users can now track their orders easily."
- "The order confirmation page provides great user experience."

**Outcome:**
- Sprint Goal achieved ✅
- 4 backlog items completed
- Customer order tracking fully implemented

---

### Sprint 4 Review

**Demo:**
- ✅ Admin dashboard with analytics (books, orders, users, revenue)
- ✅ Recent orders display
- ✅ Low stock alerts
- ✅ Book listing with search and filters
- ✅ Admin authentication and navigation

**PO Feedback:**
- "Dashboard looks professional! The low stock alerts are very useful."
- "Book search and filters work great."

**Outcome:**
- Sprint Goal achieved ✅
- 3 backlog items completed
- Admin foundation established

---

### Sprint 5 Review

**Demo:**
- ✅ Add/Edit book forms with validation
- ✅ Book CRUD operations (Create, Read, Update, Delete)
- ✅ ISBN and image URL validation
- ✅ Admin order list with filters
- ✅ Order status updates (Processing, Shipped, Delivered, Cancelled)

**PO Feedback:**
- "Book management is complete! Admins can now fully control inventory."
- "Order status updates work perfectly."

**Outcome:**
- Sprint Goal achieved ✅
- 3 backlog items completed
- Full admin CRUD capabilities implemented

---

### Sprint 6 Review

**Demo:**
- ✅ User profile page with edit functionality
- ✅ Password change feature
- ✅ Admin user management (view, search, delete)
- ✅ Profile security with CSRF protection
- ✅ Email uniqueness validation

**PO Feedback:**
- "User profile management is complete!"
- "Admin can now manage user accounts effectively."

**Outcome:**
- Sprint Goal achieved ✅
- 4 backlog items completed
- User and admin account management complete

---

### Sprint 7 Review

**Demo:**
- ✅ Docker containerization complete
- ✅ Deployed to Render cloud platform
- ✅ Security hardening (CSRF, SQL injection, XSS prevention)
- ✅ Complete documentation (README, deployment guide, API docs)
- ✅ End-to-end testing completed

**PO Feedback:**
- "System is production-ready!"
- "Documentation is comprehensive and helpful."
- "Security measures are solid."

**Stakeholder Feedback:**
- "The system meets all requirements and is ready for launch!"

**Outcome:**
- Sprint Goal achieved ✅
- 5 backlog items completed
- **System ready for production deployment** 🚀

---

### Overall Sprint Retrospective Summary

**What Went Well Across All Sprints:**
- ✅ Consistent daily standups kept team aligned
- ✅ Pair programming reduced bugs significantly
- ✅ Early security implementation prevented issues
- ✅ Clear sprint goals maintained focus
- ✅ Regular PO feedback ensured alignment with business needs

**What Could Be Improved:**
- ❌ Initial API documentation delays (resolved in Sprint 2)
- ❌ Database schema communication (improved with migration scripts)
- ❌ Some tasks underestimated in early sprints (estimation improved over time)

**Key Actions Taken:**
- 🔄 Created shared API documentation
- 🔄 Implemented database migration process
- 🔄 Improved estimation accuracy through retrospectives
- 🔄 Enhanced security practices throughout development
- 🔄 Maintained comprehensive documentation

**Team Velocity:**
- Sprint 1: 60 hours
- Sprint 2: 46 hours
- Sprint 3: 28 hours
- Sprint 4: 34 hours
- Sprint 5: 36 hours
- Sprint 6: 36 hours
- Sprint 7: 48 hours
- **Total: 288 hours over 14 weeks**

---

## 12. Alignment of Requirements with Scrum

| Requirement Type | How Scrum Handles It |
|------------------|----------------------|
| **Functional (e.g., PB-01, PB-06)** | Captured as user stories in Product Backlog; implemented in Sprints |
| **Non-Functional (e.g., performance, security)** | Included as:<br>• Acceptance criteria ("Site loads <2s")<br>• Technical enablers ("Set up CDN")<br>• Definition of Done ("All code scanned for vulnerabilities") |

---

## 13. Definition of Done (Based on Actual System)

A Product Backlog item is considered "Done" when:

1. ✅ **Code Complete:** All code written and committed to repository
2. ✅ **Code Reviewed:** Peer review completed, feedback addressed
3. ✅ **Unit Tests Pass:** All unit tests written and passing
4. ✅ **Integration Tests Pass:** Feature tested with existing system
5. ✅ **Security Validated:**
   - CSRF tokens implemented for forms
   - Input sanitization applied
   - Output escaped with `escapeHTML()`
   - Prepared statements used for database queries
6. ✅ **Performance Tested:** Page load times acceptable (<2s for catalog)
7. ✅ **Deployed to Staging:** Feature deployed to staging environment
8. ✅ **UX Validated:** UI tested on desktop and mobile browsers
9. ✅ **Documentation Updated:** Code comments, API docs, and README updated
10. ✅ **Product Owner Approval:** PO has reviewed and accepted the feature

---

## 14. Best Practices Applied

### Scrum Best Practices
1. **Refine Backlog Continuously:** Hold backlog refinement sessions weekly
2. **Keep Stories Small:** Aim for items completable in 1–3 days (3-8 story points)
3. **Involve QA Early:** Testers help define acceptance criteria during Sprint Planning
4. **Make NFRs Visible:** Tag them in Jira or add to DoD checklist
5. **Empower the Team:** Dev Team owns how to build; PO owns what to build

### Technical Best Practices
1. **Security First:**
   - Bcrypt password hashing (cost 12)
   - CSRF protection on all forms
   - Prepared statements for SQL queries
   - XSS prevention with output escaping
2. **Modular Architecture:**
   - Separation of concerns (config, includes, middleware, admin, user, auth)
   - Singleton pattern for database connections
   - Reusable security functions
3. **Database Design:**
   - Proper indexing on frequently queried columns
   - Foreign key constraints for referential integrity
   - InnoDB engine for ACID compliance
4. **Error Handling:**
   - Graceful degradation (email failures don't break orders)
   - Error logging without exposing details to users
   - Transaction rollback on failures
5. **Deployment:**
   - Docker containerization for consistency
   - Environment-based configuration
   - Cloud deployment on Render

---

## 15. Conclusion

Scrum turns complex requirements like those for an online bookstore into manageable, value-driven iterations. By maintaining a clear Product Backlog, planning collaboratively, inspecting daily, and adapting relentlessly, teams deliver high-quality software that meets both functional needs and non-functional excellence.

The **Online Bookstore System** demonstrates successful implementation of:
- ✅ **49 Functional Requirements** covering authentication, catalog, cart, checkout, orders, and admin features
- ✅ **22 Non-Functional Requirements** ensuring security, performance, reliability, usability, compatibility, maintainability, and scalability
- ✅ **Scrum Framework** with clear roles, artifacts, and events
- ✅ **Best Practices** in security, architecture, database design, and deployment

This documentation serves as a comprehensive reference for developers, QA engineers, and future maintainers of the system.

---

## Appendix A: Database Schema Summary

### Tables

1. **users** (13 columns)
   - Primary Key: `id`
   - Unique: `email`
   - Role: ENUM('user', 'admin')
   - Indexes: `email`, `role`

2. **categories** (4 columns)
   - Primary Key: `id`
   - Unique: `name`, `slug`
   - Index: `slug`

3. **books** (10 columns)
   - Primary Key: `id`
   - Unique: `isbn`
   - Foreign Key: `category_id` → `categories(id)`
   - Indexes: `isbn`, `title`, `author`, `category_id`

4. **cart_items** (6 columns)
   - Primary Key: `id`
   - Foreign Keys: `user_id` → `users(id)`, `book_id` → `books(id)`
   - Unique: `(user_id, book_id)`
   - Indexes: `user_id`, `session_id`

5. **orders** (15 columns)
   - Primary Key: `id`
   - Unique: `order_number`
   - Foreign Key: `user_id` → `users(id)`
   - Status: ENUM('Processing', 'Shipped', 'Delivered', 'Cancelled')
   - Indexes: `user_id`, `order_number`, `status`, `created_at`

6. **order_items** (5 columns)
   - Primary Key: `id`
   - Foreign Keys: `order_id` → `orders(id)`, `book_id` → `books(id)`
   - Index: `order_id`

---

## Appendix B: File Structure Summary

```
online-bookstore/
├── admin/                  # Admin panel (11 files)
│   ├── dashboard.php       # Analytics dashboard
│   ├── books.php           # Book management list
│   ├── book_form.php       # Add/edit book form
│   ├── book_actions.php    # Book CRUD operations
│   ├── orders.php          # Order management list
│   ├── order_detail.php    # Order details & status update
│   ├── accounts.php        # User account list
│   ├── account_detail.php  # User account details
│   ├── account_actions.php # User CRUD operations
│   ├── profile.php         # Admin profile
│   └── profile_actions.php # Admin profile updates
├── api/                    # AJAX endpoints (1 file)
│   └── cart_actions.php    # Cart API (add, update, remove, get_count)
├── auth/                   # Authentication (6 files)
│   ├── login.php           # User login
│   ├── register.php        # User registration
│   ├── logout.php          # Session destruction
│   ├── contact.php         # Contact page
│   ├── privacy.php         # Privacy policy
│   └── terms.php           # Terms of service
├── config/                 # Configuration (2 files)
│   ├── config.php          # App settings, constants
│   └── database.php        # Database connection (Singleton)
├── database/               # Database files (5 files)
│   ├── schema.sql          # Table definitions
│   ├── seed.sql            # Initial data (50 books, 2 users)
│   ├── README.md           # Database documentation
│   └── *.sql               # Backup files
├── includes/               # Reusable components (9 files)
│   ├── header.php          # User header
│   ├── footer.php          # User footer
│   ├── admin_header.php    # Admin header
│   ├── admin_footer.php    # Admin footer
│   ├── auth_header.php     # Auth page header
│   ├── auth_footer.php     # Auth page footer
│   ├── security.php        # Security functions (CSRF, hashing, validation)
│   ├── email.php           # Email sending (SendGrid)
│   └── payment_mock.php    # Mock payment processor
├── middleware/             # Authentication middleware (1 file)
│   └── auth_middleware.php # requireAuth(), requireAdmin(), requireUser()
├── user/                   # User-facing pages (9 files)
│   ├── catalog.php         # Book browsing & search
│   ├── book_detail.php     # Individual book details
│   ├── cart.php            # Shopping cart
│   ├── checkout.php        # Checkout form
│   ├── process_order.php   # Order processing logic
│   ├── order_confirmation.php # Order success page
│   ├── orders.php          # Order history
│   ├── order_detail.php    # Order details
│   └── profile.php         # User profile management
├── assets/                 # Static assets
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   └── images/             # Images and logos
├── .env.example            # Environment template
├── composer.json           # PHP dependencies
├── Dockerfile              # Docker image definition
├── docker-compose.yml      # Docker services
├── render.yaml             # Render deployment config
└── README.md               # Developer guide
```

---

## Appendix C: Default Credentials

### Admin Account
- **Email:** `admin@bookstore.com`
- **Password:** `admin123`
- **Role:** admin

### Test User Account
- **Email:** `user@bookstore.com`
- **Password:** `user123`
- **Role:** user

---

## Appendix D: Test Data Summary

### Categories (10)
Fiction, Non-Fiction, Science, Technology, History, Business, Self-Help, Children, Mystery, Romance

### Books (50)
Seeded from `database/seed.sql` with real book data including:
- ISBN (unique)
- Title
- Author
- Price ($6.50 - $64.99)
- Description
- Cover image URL (Goodreads)
- Category assignment
- Stock quantity (15-65 units)

---

**Document Version:** 1.0  
**Last Updated:** 2026-02-05  
**Prepared By:** System Analyst & Technical Documentation Engineer  
**Reviewed By:** Product Owner (Yuan Andrei C. Mariano)

---

**END OF DOCUMENTATION**
