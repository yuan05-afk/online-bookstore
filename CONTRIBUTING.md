# Contributing to Online Bookstore

Thank you for considering contributing to the Online Bookstore project! This document provides guidelines for contributing.

## 🚀 Getting Started

1. Fork the repository
2. Clone your fork: `git clone https://github.com/YOUR_USERNAME/online-bookstore.git`
3. Create a branch: `git checkout -b feature/your-feature-name`
4. Make your changes
5. Test thoroughly
6. Commit: `git commit -m "Add: your feature description"`
7. Push: `git push origin feature/your-feature-name`
8. Create a Pull Request

## 📝 Coding Standards

### PHP Code Style
- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Add PHPDoc comments for functions
- Keep functions small and focused (max 50 lines)
- Use type hints where possible

### JavaScript Code Style
- Use ES6+ features
- Use `const` and `let` instead of `var`
- Use arrow functions where appropriate
- Add JSDoc comments for complex functions

### CSS Code Style
- Use CSS custom properties (variables)
- Follow BEM naming convention where applicable
- Keep selectors specific but not overly complex
- Group related styles together

## 🔒 Security Guidelines

1. **Never commit sensitive data**
   - API keys, passwords, tokens
   - `.env` file is in `.gitignore`

2. **Always use prepared statements**
   ```php
   // Good
   $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
   $stmt->execute([$userId]);
   
   // Bad
   $query = "SELECT * FROM users WHERE id = $userId";
   ```

3. **Validate and sanitize inputs**
   ```php
   $email = sanitizeInput($_POST['email']);
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       // Handle error
   }
   ```

4. **Use CSRF tokens for forms**
   ```php
   <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
   ```

## 🧪 Testing

### Before Submitting PR

1. Test all affected features
2. Test on different browsers (Chrome, Firefox, Safari)
3. Test responsive design on mobile
4. Check for console errors
5. Verify database queries work correctly

### Test Accounts

Use these accounts for testing:
- Admin: `admin@bookstore.com` / `admin123`
- User: `user@bookstore.com` / `user123`

## 📋 Pull Request Guidelines

### PR Title Format
```
[Type] Brief description

Types:
- Add: New feature
- Fix: Bug fix
- Update: Improve existing feature
- Refactor: Code refactoring
- Docs: Documentation changes
- Style: Code style changes (formatting, etc.)
```

### PR Description Template
```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Tested locally
- [ ] Tested on different browsers
- [ ] Tested responsive design

## Screenshots (if applicable)
Add screenshots here

## Checklist
- [ ] Code follows project style guidelines
- [ ] Self-reviewed code
- [ ] Commented complex code
- [ ] Updated documentation
- [ ] No new warnings
```

## 🐛 Bug Reports

### Before Reporting

1. Check if bug already reported
2. Try to reproduce on latest version
3. Gather relevant information

### Bug Report Template

```markdown
**Describe the bug**
Clear description of the bug

**To Reproduce**
Steps to reproduce:
1. Go to '...'
2. Click on '...'
3. See error

**Expected behavior**
What should happen

**Screenshots**
If applicable

**Environment**
- Browser: [e.g. Chrome 120]
- OS: [e.g. Windows 11]
- PHP Version: [e.g. 8.1]
```

## 💡 Feature Requests

### Feature Request Template

```markdown
**Is your feature request related to a problem?**
Description of the problem

**Describe the solution you'd like**
Clear description of desired feature

**Describe alternatives you've considered**
Alternative solutions

**Additional context**
Any other context
```

## 📁 Project Structure

When adding new features, follow the existing structure:

```
admin/          - Admin panel pages
api/            - AJAX API endpoints
assets/         - Static files (CSS, JS, images)
auth/           - Authentication pages
config/         - Configuration files
database/       - Database schema and seeds
includes/       - Reusable components
middleware/     - Middleware (auth, etc.)
user/           - User-facing pages
```

## 🔄 Development Workflow

### 1. Create Feature Branch
```bash
git checkout -b feature/add-wishlist
```

### 2. Make Changes
- Write code
- Add comments
- Update documentation

### 3. Test Locally
```bash
# Start local server
php -S localhost:8000

# Test in browser
# Check console for errors
# Test all affected features
```

### 4. Commit Changes
```bash
git add .
git commit -m "Add: wishlist feature for users"
```

### 5. Push and Create PR
```bash
git push origin feature/add-wishlist
# Create PR on GitHub
```

## 📚 Resources

- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [MDN Web Docs](https://developer.mozilla.org/)
- [PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)

## ❓ Questions?

If you have questions:
1. Check existing documentation
2. Search closed issues
3. Create a new issue with `question` label

## 🙏 Thank You!

Your contributions make this project better for everyone!

---

**Happy Coding! 🚀**
