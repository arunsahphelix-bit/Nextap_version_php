# NextTap Builder - Complete PHP + MySQL Platform

A comprehensive web platform combining digital NFC profile builder, custom NFC card ordering, and website builder with 7 pre-designed templates.

## Features

- **User Authentication & OTP Verification**
  - Email-based OTP verification
  - Secure password hashing
  - Session-based authentication

- **Digital Profile Builder**
  - Create NFC-enabled digital business profiles
  - 3 customizable themes
  - Public profile URLs (nexttap.in/profile/username)
  - Analytics tracking (views, taps)
  - QR code generation

- **NFC Card Ordering**
  - Use existing profile or upload custom design
  - Business proof verification
  - Order status tracking
  - Admin approval workflow

- **Website Builder**
  - 7 pre-designed responsive templates
  - Dynamic content management
  - Subdomain support (nexttap.in/sites/username)
  - Publish/unpublish functionality

- **Admin Dashboard**
  - User management
  - Order approval/rejection
  - System analytics

## Tech Stack

- **Backend:** PHP 8.2
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, Bootstrap 5, Vanilla JavaScript
- **Email:** PHPMailer (SMTP)

## Installation

1. **Database Setup:**
   ```bash
   php setup-database.php
   ```

2. **Configuration:**
   Edit `config.php` to set up:
   - Database credentials
   - SMTP email settings
   - Base URL

3. **File Permissions:**
   Set secure upload directory permissions:
   ```bash
   chmod 755 uploads uploads/designs uploads/proofs uploads/profile_images uploads/website_assets
   ```

## Admin User Setup

For security, there are no default admin credentials. You must create an admin user securely:

1. Run the database setup: `php setup-database.php`
2. Open `create-admin.php` in a text editor and copy the SETUP_TOKEN value
3. Visit `/create-admin.php?token=YOUR_TOKEN_HERE` (paste your token)
4. Create your admin account with a strong password (min 8 characters)
5. The file will hard-fail if it cannot self-delete (delete manually if needed)

**Security:** The setup token prevents unauthorized access. Without the token, the page returns 404. The file self-destructs after use and hard-fails if deletion fails.

## Project Structure

```
/
├── assets/            # CSS, JS, images
├── includes/          # Core PHP files (db, mailer, header, footer)
├── pages/             # User interface pages
├── templates/         # Website templates
├── uploads/           # User uploaded files
├── api/               # AJAX endpoints
├── admin/             # Admin dashboard
├── config.php         # Configuration
└── database.sql       # Database schema
```

## Key URLs

- Home: `/`
- Login: `/pages/login.php`
- Register: `/pages/register.php`
- Dashboard: `/pages/dashboard.php`
- Admin: `/admin/dashboard.php`
- Public Profile: `/profile/[slug]`
- Public Website: `/sites/[slug]`

## SMTP Configuration

For email OTP verification to work, configure SMTP in `config.php`:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
```

## Deployment

This project is designed for shared hosting (cPanel/MilesWeb):
- No Node.js required
- No build process
- Pure PHP and vanilla JavaScript
- All dependencies via Composer

## Features Checklist

✅ User registration & login
✅ Email OTP verification
✅ Digital profile builder
✅ Profile analytics (views/taps)
✅ NFC card ordering system
✅ Admin dashboard
✅ Website builder with templates
✅ QR code generation
✅ Light/dark mode toggle
✅ Responsive design
✅ File upload validation
✅ Order management

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## License

Proprietary - All rights reserved

## Support

For issues or questions, contact the development team.
