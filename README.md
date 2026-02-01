# 📚 Online Bookstore - Developer Guide

A full-featured online bookstore management system built with PHP, MySQL, and modern web technologies.

---

## 🚀 Quick Start

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- Node.js (optional, for development)

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yuan05-afk/online-bookstore.git
   cd online-bookstore
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   ```
   Edit `.env` and set your database credentials.

4. **Import database**
   - Create a MySQL database named `online_bookstore`
   - Import `database/schema.sql`
   - Import `database/seed.sql` (includes 50 books + test users)

5. **Start development server**
   ```bash
   # Using PHP built-in server
   php -S localhost:8000
   
   # Or using XAMPP/WAMP
   # Place project in htdocs/www folder
   ```

6. **Access the application**
   - User Frontend: `http://localhost:8000/user/catalog.php`
   - Admin Panel: `http://localhost:8000/admin/dashboard.php`

### Default Credentials

**Admin Account:**
- Email: `admin@bookstore.com`
- Password: `admin123`

**Test User Account:**
- Email: `user@bookstore.com`
- Password: `user123`

---

## 🏗️ Project Structure

```
online-bookstore/
├── admin/              # Admin panel pages
├── api/                # API endpoints (AJAX)
├── assets/             # Static assets
│   ├── css/           # Stylesheets
│   ├── js/            # JavaScript files
│   └── images/        # Images and logos
├── auth/              # Authentication pages
├── config/            # Configuration files
├── database/          # Database schema and seeds
├── includes/          # Reusable PHP components
├── middleware/        # Authentication middleware
├── user/              # User-facing pages
├── vendor/            # Composer dependencies
├── .env.example       # Environment template
├── composer.json      # PHP dependencies
├── docker-compose.yml # Docker setup
├── Dockerfile         # Docker image
└── render.yaml        # Render deployment config
```

---

## 🔧 Configuration

### Environment Variables

Copy `.env.example` to `.env` and configure:

```env
# Database
DB_HOST=localhost
DB_NAME=online_bookstore
DB_USER=root
DB_PASSWORD=

# SendGrid Email (Production)
SENDGRID_API_KEY=your_api_key
SENDGRID_FROM_EMAIL=your-email@example.com
SENDGRID_FROM_NAME=Night Owl Books

# Application
SITE_URL=http://localhost/online-bookstore
TAX_RATE=0.085
```

### Database Configuration

The application uses MySQL with the following tables:
- `users` - User accounts (admin/customer)
- `books` - Book catalog
- `categories` - Book categories
- `cart_items` - Shopping cart
- `orders` - Order records
- `order_items` - Order line items

---

## 🐳 Docker Deployment

### Local Development with Docker

```bash
# Start all services
docker-compose up -d

# Access application
http://localhost:8080

# Access phpMyAdmin
http://localhost:8081
```

### Production Docker Build

```bash
# Build image
docker build -t online-bookstore .

# Run container
docker run -p 80:80 \
  -e DB_HOST=your_db_host \
  -e DB_NAME=your_db_name \
  -e DB_USER=your_db_user \
  -e DB_PASSWORD=your_db_password \
  online-bookstore
```

---

## 🚢 Production Deployment

### Render Deployment

This project is configured for easy deployment on Render using `render.yaml`.

1. **Push to GitHub**
   ```bash
   git push origin main
   ```

2. **Create Render Account**
   - Go to https://render.com
   - Connect your GitHub repository

3. **Deploy via Blueprint**
   - Render will detect `render.yaml`
   - Click "Apply" to deploy

4. **Set Environment Variables**
   Add these in Render Dashboard:
   - `SENDGRID_API_KEY`
   - `SENDGRID_FROM_EMAIL`
   - `SENDGRID_FROM_NAME`
   - `SITE_URL` (your Render URL)

5. **Database Setup**
   - Use external MySQL (FreeSQLDatabase or similar)
   - Import `database/schema.sql` and `database/seed.sql`

See `docs/DEPLOYMENT.md` for detailed deployment guide.

---

## 📧 Email Configuration

### SendGrid Setup (Recommended for Production)

1. Create SendGrid account: https://signup.sendgrid.com/
2. Generate API key (Settings → API Keys)
3. Verify sender email (Settings → Sender Authentication)
4. Add credentials to `.env`:
   ```env
   SENDGRID_API_KEY=SG.your_key_here
   SENDGRID_FROM_EMAIL=your-verified-email@example.com
   SENDGRID_FROM_NAME=Night Owl Books
   ```

See `docs/SENDGRID_SETUP.md` for detailed guide.

---

## 🎨 Features

### User Features
- ✅ Browse book catalog with search and filters
- ✅ Shopping cart management
- ✅ Secure checkout with mock payment
- ✅ Order history and tracking
- ✅ User profile management
- ✅ Email order confirmations

### Admin Features
- ✅ Dashboard with sales analytics
- ✅ Book inventory management (CRUD)
- ✅ Order management and status updates
- ✅ User account management
- ✅ Category management
- ✅ Admin profile settings

### Technical Features
- ✅ Secure authentication (bcrypt password hashing)
- ✅ CSRF protection
- ✅ Session management
- ✅ Input validation and sanitization
- ✅ Responsive design
- ✅ RESTful API endpoints
- ✅ Transaction-safe database operations
- ✅ Email notifications via SendGrid

---

## 🧪 Testing

### Test Accounts

The seed data includes test accounts:

**Admin:**
- Email: `admin@bookstore.com`
- Password: `admin123`

**User:**
- Email: `user@bookstore.com`
- Password: `user123`

### Test Cards (Mock Payment)

Use these test card numbers in checkout:
- Success: `4242424242424242`
- Decline: `4000000000000002`

---

## 📝 Development Guidelines

### Code Style
- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Comment complex logic
- Keep functions small and focused

### Security Best Practices
- Never commit `.env` file
- Always use prepared statements for database queries
- Validate and sanitize all user inputs
- Use CSRF tokens for forms
- Hash passwords with bcrypt

### Git Workflow
```bash
# Create feature branch
git checkout -b feature/your-feature-name

# Make changes and commit
git add .
git commit -m "Add: your feature description"

# Push and create pull request
git push origin feature/your-feature-name
```

---

## 🐛 Troubleshooting

### Common Issues

**Database Connection Failed**
- Check `.env` database credentials
- Ensure MySQL service is running
- Verify database exists

**Login Not Working**
- Clear browser cookies/cache
- Check password hashes in database
- Verify session configuration

**Email Not Sending**
- Check SendGrid API key is valid
- Verify sender email is verified in SendGrid
- Check Render logs for errors

**Cart/Orders Not Working**
- Check JavaScript console for errors
- Verify API endpoints are accessible
- Check database foreign key constraints

---

## 📚 Additional Documentation

- [Deployment Guide](docs/DEPLOYMENT.md) - Detailed deployment instructions
- [SendGrid Setup](docs/SENDGRID_SETUP.md) - Email configuration guide
- [Database Schema](database/README.md) - Database structure documentation
- [API Reference](docs/API.md) - API endpoints documentation

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

---

## 📄 License

This project is open source and available under the MIT License.

---

## 👥 Team

- **Developer**: Yuan05
- **Project**: Online Bookstore Management System
- **Year**: 2026

---

## 🆘 Support

For issues or questions:
- Create an issue on GitHub
- Contact: nightowlonlinebookstoresmtp@gmail.com

---

**Happy Coding! 📚✨**
