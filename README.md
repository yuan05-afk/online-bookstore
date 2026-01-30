# Online Bookstore System

A complete, production-ready online bookstore system built with PHP, MySQL, and Docker for academic capstone/thesis projects.

## 🚀 Features

### User Features
- **Authentication**: Secure registration and login with bcrypt password hashing
- **Book Browsing**: Browse books by category with search functionality (title, author, ISBN)
- **Shopping Cart**: Add/remove books, view cart summary with tax calculation
- **Order Management**: Place orders with mock payment processing, track order status
- **Order History**: View complete order history with detailed tracking

### Admin Features
- **Dashboard**: Statistics overview with recent orders and low stock alerts
- **Book Management**: Full CRUD operations for book catalog with image uploads
- **Order Management**: View all orders, update order statuses, manage customer orders
- **Inventory Control**: Track stock levels and manage book availability

### Security Features
- Bcrypt password hashing (cost factor 12)
- CSRF token protection on all forms
- SQL injection prevention (prepared statements)
- XSS protection (output escaping)
- Secure session management
- Input validation and sanitization

## 🛠️ Technology Stack

- **Backend**: PHP 8.1
- **Database**: MySQL 8.0
- **Web Server**: Apache
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Containerization**: Docker
- **Deployment**: Render (Docker-based)

## 📦 Installation

### Local Development (XAMPP)

1. **Prerequisites**
   - XAMPP installed with Apache and MySQL
   - PHP 8.1 or higher

2. **Setup**
   ```bash
   # Clone/copy project to XAMPP htdocs
   cd C:\xampp\htdocs
   
   # Import database
   # Open phpMyAdmin (http://localhost/phpmyadmin)
   # Create database 'online_bookstore'
   # Import database/schema.sql
   # Import database/seed.sql
   
   # Start Apache and MySQL from XAMPP Control Panel
   ```

3. **Access Application**
   - URL: `http://localhost/online-bookstore`
   - Admin: `admin@bookstore.com` / `admin123`
   - User: `user@bookstore.com` / `user123`

### Docker Development

1. **Prerequisites**
   - Docker Desktop installed

2. **Setup**
   ```bash
   # Navigate to project directory
   cd online-bookstore
   
   # Start containers
   docker-compose up -d
   
   # Database will be automatically initialized
   ```

3. **Access Application**
   - Application: `http://localhost:8080`
   - phpMyAdmin: `http://localhost:8081`
   - Admin: `admin@bookstore.com` / `admin123`
   - User: `user@bookstore.com` / `user123`

## 🚢 Deployment to Render

1. **Prerequisites**
   - Render account
   - GitHub repository with project code

2. **Database Setup**
   - Create a PostgreSQL or MySQL database on Render
   - Note the connection details

3. **Web Service Setup**
   - Create new Web Service on Render
   - Connect your GitHub repository
   - Select "Docker" as environment
   - Set environment variables:
     ```
     DB_HOST=your_database_host
     DB_NAME=online_bookstore
     DB_USER=your_database_user
     DB_PASSWORD=your_database_password
     ```

4. **Deploy**
   - Render will automatically build and deploy using Dockerfile
   - Import database schema and seed data manually via database console

## 📁 Project Structure

```
online-bookstore/
├── admin/                  # Admin panel pages
│   ├── dashboard.php
│   ├── books.php
│   ├── book_form.php
│   ├── book_actions.php
│   ├── orders.php
│   └── order_detail.php
├── api/                    # API endpoints
│   └── cart_actions.php
├── assets/                 # Static assets
│   ├── css/
│   │   ├── style.css
│   │   └── admin.css
│   ├── js/
│   │   └── main.js
│   └── images/
│       └── books/
├── auth/                   # Authentication
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── config/                 # Configuration
│   ├── config.php
│   └── database.php
├── database/               # Database files
│   ├── schema.sql
│   └── seed.sql
├── includes/               # Shared components
│   ├── header.php
│   ├── admin_header.php
│   ├── footer.php
│   ├── security.php
│   └── payment_mock.php
├── middleware/             # Middleware
│   └── auth_middleware.php
├── user/                   # User pages
│   ├── catalog.php
│   ├── book_detail.php
│   ├── cart.php
│   ├── checkout.php
│   ├── process_order.php
│   ├── order_confirmation.php
│   ├── orders.php
│   └── order_detail.php
├── Dockerfile
├── docker-compose.yml
├── .dockerignore
├── .env.example
└── index.php
```

## 🔐 Default Credentials

### Admin Account
- Email: `admin@bookstore.com`
- Password: `admin123`

### Test User Account
- Email: `user@bookstore.com`
- Password: `user123`

## 💳 Mock Payment Testing

The system uses a mock payment processor. Use these test cards:

- **Visa**: `4532015112830366`
- **Mastercard**: `5425233430109903`
- **Expiry**: Any future date
- **CVV**: Any 3 digits

Payment has a 90% success rate to simulate real-world scenarios.

## 📊 Database Schema

### Main Tables
- `users` - User accounts with role-based access
- `books` - Book catalog (50 books from Kaggle dataset)
- `categories` - Book categories
- `cart_items` - Shopping cart items
- `orders` - Customer orders
- `order_items` - Individual order items

## 🎨 Design Features

- Modern, responsive UI with gradient backgrounds
- Mobile-first design approach
- Smooth animations and transitions
- Professional color scheme
- Google Fonts (Inter)
- Accessible and user-friendly

## 📝 Notes

- Email notifications are logged but not sent (can integrate SMTP later)
- Payment processing is fully simulated (no real transactions)
- HTTPS enforced on production (Render provides SSL/TLS)
- Session timeout: 2 hours
- CSRF token expiry: 1 hour

## 🐛 Troubleshooting

### Database Connection Issues
- Verify database credentials in `config/database.php`
- Ensure MySQL service is running
- Check database exists and is accessible

### File Upload Issues
- Verify `assets/images/books/` directory exists
- Check directory permissions (755)
- Ensure PHP upload settings allow 5MB files

### Docker Issues
- Run `docker-compose down` and `docker-compose up -d` to restart
- Check logs: `docker-compose logs -f`
- Verify ports 8080, 3306, 8081 are not in use

## 📄 License

This project is created for academic purposes.

## 👨‍💻 Author

Created for academic capstone/thesis project requirements.
