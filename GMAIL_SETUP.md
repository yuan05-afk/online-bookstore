# Gmail SMTP Setup Guide

This guide will help you configure Gmail SMTP for sending order confirmation emails.

## Prerequisites

- A Gmail account
- Two-Factor Authentication (2FA) enabled on your Gmail account

---

## Step 1: Enable 2-Factor Authentication

1. Go to your Google Account: https://myaccount.google.com/
2. Click on **Security** in the left sidebar
3. Under "Signing in to Google", click **2-Step Verification**
4. Follow the prompts to enable 2FA if not already enabled

---

## Step 2: Generate App Password

1. Go to your Google Account: https://myaccount.google.com/
2. Click on **Security** in the left sidebar
3. Under "Signing in to Google", click **2-Step Verification**
4. Scroll down to the bottom and click **App passwords**
5. You may need to sign in again
6. In the "Select app" dropdown, choose **Mail**
7. In the "Select device" dropdown, choose **Other (Custom name)**
8. Enter a name like "Online Bookstore" or "Night Owl Books"
9. Click **Generate**
10. **IMPORTANT**: Copy the 16-character password that appears (it will look like: `xxxx xxxx xxxx xxxx`)
11. Save this password - you won't be able to see it again!

---

## Step 3: Configure .env File

1. Open the `.env` file in the root directory of your project
2. Update the following values:

```env
# SMTP Email Configuration (Gmail)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-gmail@gmail.com
SMTP_PASSWORD=xxxx xxxx xxxx xxxx
SMTP_FROM_EMAIL=your-gmail@gmail.com
SMTP_FROM_NAME=Night Owl Books
SMTP_ENCRYPTION=tls
```

**Replace:**
- `your-gmail@gmail.com` with your actual Gmail address
- `xxxx xxxx xxxx xxxx` with the 16-character App Password you generated

**Example:**
```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=nightowlbooks@gmail.com
SMTP_PASSWORD=abcd efgh ijkl mnop
SMTP_FROM_EMAIL=nightowlbooks@gmail.com
SMTP_FROM_NAME=Night Owl Books
SMTP_ENCRYPTION=tls
```

---

## Step 4: Test Email Sending

1. Make sure your XAMPP Apache and MySQL are running
2. Log in to your bookstore website
3. Add a book to your cart
4. Proceed to checkout
5. Complete the order
6. Check your email inbox for the order confirmation

---

## Troubleshooting

### "Authentication failed" Error

**Possible causes:**
- App Password is incorrect (check for typos or extra spaces)
- 2FA is not enabled on your Gmail account
- You're using your regular Gmail password instead of the App Password

**Solution:**
- Regenerate a new App Password
- Make sure to copy it exactly (including spaces or remove all spaces)
- Verify 2FA is enabled

### "Could not connect to SMTP host" Error

**Possible causes:**
- Firewall blocking port 587
- Internet connection issues
- Gmail SMTP is temporarily unavailable

**Solution:**
- Check your internet connection
- Try using port 465 with SSL encryption instead:
  ```env
  SMTP_PORT=465
  SMTP_ENCRYPTION=ssl
  ```

### Email Not Received

**Check:**
1. Spam/Junk folder
2. Gmail "Promotions" or "Updates" tab
3. Error logs in `xampp/apache/logs/error.log`
4. Check if email was sent successfully in error logs

### Gmail Daily Sending Limit

Gmail free accounts have a limit of **500 emails per day**. If you exceed this:
- Wait 24 hours for the limit to reset
- Consider using a professional email service like SendGrid for production

---

## Security Best Practices

1. **Never commit .env file to Git** - It's already in .gitignore
2. **Use App Passwords, not your main password**
3. **Rotate App Passwords periodically**
4. **Revoke unused App Passwords** from your Google Account settings
5. **Use environment variables** for all sensitive data

---

## Alternative: Using a Different Email

If you want to use a different "From" email address:

```env
SMTP_USERNAME=your-gmail@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_FROM_EMAIL=noreply@nightowlbooks.com
SMTP_FROM_NAME=Night Owl Books
```

**Note:** Gmail will still show "via gmail.com" in the recipient's inbox, but the display name and reply-to address will be your custom email.

---

## Production Recommendations

For production use, consider:

1. **SendGrid** - Professional email service with better deliverability
2. **Amazon SES** - Cost-effective for high volume
3. **Mailgun** - Developer-friendly with good documentation
4. **Postmark** - Excellent for transactional emails

These services offer:
- Higher sending limits
- Better deliverability rates
- Detailed analytics
- Professional support
- No "via gmail.com" notices

---

## Need Help?

If you encounter issues:

1. Check the error logs: `xampp/apache/logs/error.log`
2. Verify your .env configuration
3. Test with a simple Gmail account first
4. Make sure PHPMailer is installed: `composer install`

---

## Email Template Customization

To customize the email template, edit:
- `includes/email.php` - Look for `generateOrderConfirmationHTML()` function
- Modify colors, layout, or content as needed
- Test changes by placing a new order
