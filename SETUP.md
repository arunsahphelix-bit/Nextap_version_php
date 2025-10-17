# NextTap Builder - Setup Guide

## Quick Start

### 1. Database Setup

This platform requires MySQL. For Replit or local development, you have two options:

#### Option A: Using MySQL (Recommended for Production)

1. Ensure MySQL is installed and running
2. Run the database setup:
   ```bash
   php setup-database.php
   ```

#### Option B: Using SQLite (Quick Demo)

For quick testing without MySQL, you can use SQLite by modifying `config.php`:

```php
// Comment out MySQL config
// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'nexttap_builder');

// Use SQLite instead
define('USE_SQLITE', true);
define('SQLITE_DB', __DIR__ . '/nexttap.db');
```

### 2. Email Configuration

Configure SMTP settings in `config.php` for OTP verification:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
```

**Note:** For Gmail, you need to create an App Password:
1. Go to Google Account Settings
2. Security → 2-Step Verification
3. App passwords → Generate new password
4. Use this password in SMTP_PASS

### 3. File Permissions

Set secure upload directory permissions:

```bash
chmod 755 uploads uploads/designs uploads/proofs uploads/profile_images uploads/website_assets
```

The application handles file security internally with:
- MIME type validation
- File size limits (5MB default)
- Random filename generation
- Extension whitelist

### 4. Start the Server

The server should already be running on port 5000. If not:

```bash
php -S 0.0.0.0:5000
```

### 5. Create Admin User

**⚠️ CRITICAL SECURITY STEP:**

1. Open `create-admin.php` in a text editor
2. Find and copy the SETUP_TOKEN value (line 4)
3. Visit `http://localhost:5000/create-admin.php?token=YOUR_TOKEN_HERE`
4. Enter your admin details with a strong password (min 8 chars)
5. Click "Create Admin"
6. The file will self-delete or show a critical error if it fails
7. **If deletion fails, manually remove create-admin.php IMMEDIATELY**

**Security Features:**
- Token-protected (returns 404 without valid token)
- Works only once (blocks if admin exists)
- Hard-fails if unable to self-destruct
- Never accessible without the secret token

### 6. Access the Platform

- **Homepage:** http://localhost:5000
- **Admin Login:** Use the credentials you just created

## Features to Test

### 1. User Registration
1. Go to `/pages/register.php`
2. Register with a company email
3. Verify OTP (check email or database)

### 2. Create Profile
1. Login → Dashboard
2. Click "Create Profile"
3. Fill profile details
4. View public profile at `/profile/[your-slug]`

### 3. Order NFC Card
1. Dashboard → "Order NFC Card"
2. Choose: Existing Profile or Custom Design
3. Upload business proof
4. Admin can approve from `/admin/orders.php`

### 4. Build Website
1. Dashboard → "Build Website"
2. Choose from 7 templates
3. Fill content
4. Publish and view at `/sites/[your-slug]`

## Troubleshooting

### Database Connection Error
- Check MySQL is running
- Verify credentials in `config.php`
- Run `php setup-database.php` again

### Email OTP Not Sending
- Verify SMTP credentials
- Check spam folder
- Enable "Less secure app access" for Gmail
- Or use App Password

### File Upload Errors
- Check directory permissions (777)
- Verify max file size in `config.php`
- Check PHP upload settings in `php.ini`

### .htaccess Not Working
If URL rewriting doesn't work:
1. Enable mod_rewrite in Apache
2. Check AllowOverride is set to All
3. For PHP built-in server, URLs work with query params

## Deployment to Shared Hosting

### 1. Upload Files
- Upload all files via FTP/cPanel File Manager
- Exclude: vendor/, .git/, node_modules/

### 2. Install Composer Dependencies
```bash
composer install --no-dev
```

### 3. Create Database
- Create MySQL database in cPanel
- Import `database.sql`
- Update `config.php` with database credentials

### 4. Set Permissions
```bash
chmod 755 uploads uploads/designs uploads/proofs uploads/profile_images
```

### 5. Configure Domain
- Point domain to public root
- Ensure .htaccess is uploaded and active

## Security Checklist

- [ ] Create admin user with strong password (min 8 chars)
- [ ] Delete create-admin.php after setup
- [ ] Update SMTP credentials (use App Password for Gmail)
- [ ] Set strong database password
- [ ] Verify upload permissions are 755 (not 777)
- [ ] Enable HTTPS on production
- [ ] Set proper file permissions (755 for folders, 644 for files)
- [ ] Remove setup-database.php after database initialization
- [ ] Review and rotate all secrets regularly
- [ ] Add CSRF protection (production)
- [ ] Enable rate limiting (production)

## Security Features Implemented

✅ **IDOR Protection:** All API endpoints verify resource ownership
✅ **Secure File Uploads:** MIME type validation, size limits, random filenames
✅ **Password Security:** bcrypt hashing with cost factor 10
✅ **SQL Injection Prevention:** Prepared statements throughout
✅ **Session Security:** HTTP-only session cookies
✅ **No Default Credentials:** Admin must be created securely

## Support

For issues:
1. Check logs in PHP error_log
2. Verify database connection
3. Check file permissions
4. Review SMTP configuration

## Next Steps

After setup:
1. Create your first profile
2. Test NFC ordering workflow
3. Build a website with templates
4. Customize themes and templates
5. Configure admin notifications
